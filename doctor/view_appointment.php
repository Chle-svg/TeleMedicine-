<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$doctor_id = $_SESSION['user_id'];

// Fetch doctor name
$doctor_name = "Doctor";
$doctor_query = mysqli_query($conn, "SELECT name FROM users WHERE id = $doctor_id");
if ($doctor_query && mysqli_num_rows($doctor_query) > 0) {
    $doctor_row = mysqli_fetch_assoc($doctor_query);
    $doctor_name = "Dr. " . htmlspecialchars($doctor_row['name']);
}

// Get appointment ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid appointment ID.");
}
$appointment_id = intval($_GET['id']);

// Fetch appointment and patient info
$sql = "SELECT a.*, u.name AS patient_name, u.email AS patient_email, u.phone AS patient_phone, u.city AS patient_city, u.age AS patient_age, u.gender AS patient_gender, u.id as patient_id
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        WHERE a.id = ? AND a.doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $appointment_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Appointment not found or access denied.");
}
$appointment = $result->fetch_assoc();

// Fetch consult fee
$consult_fee = 100;
$fee_stmt = $conn->prepare("SELECT consult_fee FROM doctor WHERE user_id = ?");
$fee_stmt->bind_param("i", $doctor_id);
$fee_stmt->execute();
$fee_result = $fee_stmt->get_result();
if ($fee_result->num_rows > 0) {
    $fee_row = $fee_result->fetch_assoc();
    $consult_fee = floatval($fee_row['consult_fee']);
}

$success_msg = "";
$error_msg = "";

// Send notification function
function sendNotification($conn, $userId, $message) {
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
    $stmt->bind_param("is", $userId, $message);
    return $stmt->execute();
}

// Handle accept/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'accept') {
        $location = $appointment['appointment_type'] === 'in_person' ? trim($_POST['location'] ?? '') : null;
        $zoom_link = $appointment['appointment_type'] === 'video' ? "https://meet.jit.si/TeleMedicine_appointment_{$appointment_id}_" . bin2hex(random_bytes(4)) : null;

        if ($appointment['appointment_type'] === 'in_person' && empty($location)) {
            $error_msg = "Location is required for in-person appointments.";
        } else {
            // Chapa payment setup
            $tx_ref = "appt{$appointment_id}_" . time();
            $chapa_secret_key = "CHASECK_TEST-ObOKGQ4vLV6nX2ELLeEPMZFCNTxJPS3i";
            $callback_url = "http://localhost/doctor/payment_success.php";
            $post_data = [
                "amount" => $consult_fee,
                "currency" => "ETB",
                "tx_ref" => $tx_ref,
                "callback_url" => $callback_url,
                "return_url" => $callback_url,
                "customer" => [
                    "email" => $appointment['patient_email'],
                    "name" => $appointment['patient_name'],
                    "phone" => $appointment['patient_phone']
                ],
                "customization" => [
                    "title" => "Consultation",
                    "description" => "Appointment ID $appointment_id"
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.chapa.co/v1/transaction/initialize");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer $chapa_secret_key"
            ]);
            $response = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_status === 200) {
                $response_data = json_decode($response, true);
                if (isset($response_data['data']['checkout_url'])) {
                    $payment_link = $response_data['data']['checkout_url'];

                    $stmt = $conn->prepare("UPDATE appointments SET status='accepted', location=?, zoom_link=?, payment_link=?, payment_tx_ref=?, payment_status='pending', consult_fee=? WHERE id=?");
                    $stmt->bind_param("ssssdi", $location, $zoom_link, $payment_link, $tx_ref, $consult_fee, $appointment_id);
                    if ($stmt->execute()) {
                        $success_msg = "Appointment accepted and payment link generated.";
                        $appointment['status'] = 'accepted';
                        $appointment['location'] = $location;
                        $appointment['zoom_link'] = $zoom_link;
                        $appointment['payment_link'] = $payment_link;
                        $appointment['payment_tx_ref'] = $tx_ref;
                        $appointment['consult_fee'] = $consult_fee;
                        $appointment['payment_status'] = 'pending';

                        $notif_msg = "$doctor_name has accepted your appointment. Please proceed with payment.";
                        sendNotification($conn, $appointment['patient_id'], $notif_msg);
                    } else {
                        $error_msg = "Database update failed.";
                    }
                } else {
                    $error_msg = "Payment link generation failed.";
                }
            } else {
                $error_msg = "Chapa API error.";
            }
        }
    } elseif ($action === 'reject') {
        $reason = trim($_POST['rejection_reason'] ?? '');
        if (empty($reason)) {
            $error_msg = "Please provide a reason for rejection.";
        } else {
            $stmt = $conn->prepare("UPDATE appointments SET status='rejected', rejection_reason=? WHERE id=?");
            $stmt->bind_param("si", $reason, $appointment_id);
            if ($stmt->execute()) {
                $success_msg = "Appointment rejected.";
                $appointment['status'] = 'rejected';
                $appointment['rejection_reason'] = $reason;
                $notif_msg = "$doctor_name has rejected your appointment. Reason: <em>" . htmlspecialchars($reason) . "</em>";
                sendNotification($conn, $appointment['patient_id'], $notif_msg);
            } else {
                $error_msg = "Failed to reject appointment.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Appointment Details - TeleMedicine</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
        }
        .container {
            max-width: 920px;
            margin: 30px auto;
            padding: 30px;
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        .card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 25px 30px;
            flex: 1 1 420px;
            box-sizing: border-box;
        }
        h2 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
            border-left: 5px solid #007bff;
            padding-left: 10px;
        }
        .message.success {
            background-color: #e6ffed;
            border-left: 4px solid #28a745;
            padding: 12px;
            margin-bottom: 20px;
        }
        .message.error {
            background-color: #ffe6e6;
            border-left: 4px solid #dc3545;
            padding: 12px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px 30px;
        }
        .info-grid div {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px 20px;
        }
        .info-grid div strong {
            color: #555;
            display: block;
            margin-bottom: 6px;
        }
        .action-form {
            margin-top: 25px;
        }
        input[type="text"] {
            padding: 10px;
            width: 100%;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            margin-right: 15px;
        }
        .btn.accept {
            background-color: #007bff;
            color: white;
        }
        .btn.reject {
            background-color: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        a.meeting-link {
            color: #007bff;
            text-decoration: none;
        }
        a.meeting-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 900px) {
            .container {
                flex-direction: column;
                padding: 20px;
            }
            .card {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
<?php include '../includes/sidebarD.php'; ?>
<?php include '../includes/headerD.php'; ?>

<div class="container">
    <?php if ($success_msg): ?>
        <div class="message success" style="flex-basis: 100%;"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div class="message error" style="flex-basis: 100%;"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2>Patient Information</h2>
       <div class="info-grid">
    <div><strong>Name:</strong> <?= htmlspecialchars($appointment['patient_name']) ?></div>
    <div><strong>Email:</strong> <?= htmlspecialchars($appointment['patient_email']) ?></div>
    <div><strong>Phone:</strong> <?= htmlspecialchars($appointment['patient_phone']) ?></div>
    <div><strong>City:</strong> <?= htmlspecialchars($appointment['patient_city']) ?></div>
    <div><strong>Age:</strong> <?= htmlspecialchars($appointment['patient_age']) ?></div>
    <div><strong>Gender:</strong> <?= htmlspecialchars(ucfirst($appointment['patient_gender'])) ?></div>
</div>

    </div>

    <div class="card">
        <h2>Appointment Information</h2>
        <div class="info-grid">
            <div><strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?></div>
            <div><strong>Time:</strong> <?= htmlspecialchars($appointment['appointment_time']) ?></div>
            <div><strong>Type:</strong> <?= htmlspecialchars(ucwords(str_replace('_', ' ', $appointment['appointment_type']))) ?></div>
            <div><strong>Status:</strong> <?= htmlspecialchars(ucfirst($appointment['status'])) ?></div>

            <?php if ($appointment['status'] === 'accepted'): ?>
                <div><strong>Location:</strong> <?= htmlspecialchars($appointment['location'] ?? '-') ?></div>
                <div><strong>Meeting Link:</strong> <?= $appointment['zoom_link'] ? "<a href='".htmlspecialchars($appointment['zoom_link'])."' class='meeting-link' target='_blank'>Join Meeting</a>" : '-' ?></div>
                <div><strong>Fee:</strong> <?= htmlspecialchars(number_format($appointment['consult_fee'], 2)) ?> ETB</div>
                <div><strong>Payment Status:</strong> <?= htmlspecialchars(ucfirst($appointment['payment_status'])) ?></div>
            <?php endif; ?>
        </div>

     <?php if ($appointment['status'] === 'pending'): ?>
    <form method="POST" class="action-form" onsubmit="return confirm('Are you sure?');">
        <?php if ($appointment['appointment_type'] === 'in_person'): ?>
            <input type="text" name="location" placeholder="Enter location..." required>
        <?php endif; ?>

        <textarea name="rejection_reason" rows="3" placeholder="Reason for rejection (required if rejecting)..." style="width: 100%; margin-top:10px;"></textarea>

        <button type="submit" name="action" value="accept" class="btn accept">Accept</button>
        <button type="submit" name="action" value="reject" class="btn reject">Reject</button>
    </form>
<?php elseif ($appointment['status'] === 'rejected' && !empty($appointment['rejection_reason'])): ?>
    <p><strong>Rejection Reason:</strong> <?= htmlspecialchars($appointment['rejection_reason']) ?></p>
<?php endif; ?>

    </div>
</div>

<?php include '../includes/footerD.php'; ?>
</body>
</html>
