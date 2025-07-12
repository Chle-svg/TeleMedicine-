<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

if ($appointment_id <= 0) {
    die("Invalid appointment ID.");
}

$stmt = $conn->prepare("
    SELECT a.id, a.status, a.payment_status, a.consult_fee, a.payment_tx_ref, u.name, u.email
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE a.id = ? AND a.user_id = ?
");
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("ii", $appointment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();
$stmt->close();

if (!$appointment) {
    die("Appointment not found or you do not have permission to view it.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TeleMedicine | Payment Receipt</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f8fb;
            margin: 0;
            padding: 30px;
        }

        .receipt-container {
            background: #fff;
            max-width: 800px;
            margin: auto;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            padding: 40px;
            position: relative;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #4CAF50;
            margin: 0;
            font-size: 32px;
        }

        .info-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        .info-table th, .info-table td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
        }

        .info-table th {
            background-color: #f5f5f5;
        }

        .status-paid {
            color: #2e7d32;
            font-weight: bold;
        }

        .status-pending {
            color: #ff9800;
            font-weight: bold;
        }

        .footer-note {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #777;
        }

        .download-btn {
            display: block;
            margin: 25px auto 0;
            background-color: #4CAF50;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .download-btn:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>
    <div id="receipt" class="receipt-container">
        <div class="header">
            <h1>Payment Receipt</h1>
            <p><strong>TeleMedicine</strong> - Reliable Healthcare Online</p>
        </div>

        <table class="info-table">
            <tr><th>Appointment ID</th><td><?= htmlspecialchars($appointment['id']) ?></td></tr>
            <tr><th>Name</th><td><?= htmlspecialchars($appointment['name']) ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($appointment['email']) ?></td></tr>
            <tr><th>Consultation Fee</th><td>ETB <?= number_format($appointment['consult_fee'], 2) ?></td></tr>
            <tr><th>Transaction Reference</th><td><?= htmlspecialchars($appointment['payment_tx_ref'] ?? 'N/A') ?></td></tr>
            <tr>
                <th>Payment Status</th>
                <td><?= $appointment['payment_status'] === 'paid'
                    ? '<span class="status-paid">Paid</span>'
                    : '<span class="status-pending">Pending</span>'; ?>
                </td>
            </tr>
            <tr><th>Appointment Status</th><td><?= ucfirst(htmlspecialchars($appointment['status'])) ?></td></tr>
        </table>

        <p class="footer-note">
            Thank you for trusting TeleMedicine.<br>
            Stay healthy, stay connected.
        </p>
    </div>

    <button class="download-btn" onclick="downloadReceipt()">Download PDF</button>

    <script>
        function downloadReceipt() {
            const element = document.getElementById('receipt');
            html2pdf()
                .from(element)
                .set({
                    margin: 0.5,
                    filename: 'TeleMedicine_Receipt_Appointment<?= $appointment_id ?>.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
                })
                .save();
        }
    </script>
</body>
</html>
