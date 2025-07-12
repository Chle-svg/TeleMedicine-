<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

$doctor_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Invalid appointment ID.");
}
$appointment_id = intval($_GET['id']);

// Fetch appointment + patient info
$stmt = $conn->prepare("
    SELECT a.*, u.name AS patient_name, u.email, u.phone, u.id AS user_id
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE a.id = ? AND a.doctor_id = ?
");
$stmt->bind_param("ii", $appointment_id, $doctor_id);
$stmt->execute();
$appointment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$appointment) {
    die("Appointment not found or unauthorized.");
}

// Check for existing medical history
$checkStmt = $conn->prepare("SELECT * FROM medical_history WHERE appointment_id = ?");
$checkStmt->bind_param("i", $appointment_id);
$checkStmt->execute();
$existingHistory = $checkStmt->get_result()->fetch_assoc();

// Get current medications from prescriptions
$prescStmt = $conn->prepare("
    SELECT m.name 
    FROM prescriptions p
    JOIN medications m ON p.medication_id = m.id
    WHERE p.appointment_id = ?
    LIMIT 1
");
$prescStmt->bind_param("i", $appointment_id);
$prescStmt->execute();
$prescRes = $prescStmt->get_result()->fetch_assoc();
$currentMed = $prescRes['name'] ?? '';

// Save new medical history
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$existingHistory) {
    $symptoms = $_POST['symptoms'] ?? '';
    $past_illnesses = $_POST['past_illnesses'] ?? '';
    $current_medications = $currentMed;
    $allergies = $_POST['allergies'] ?? '';
    $family_history = $_POST['family_history'] ?? '';
    $social_history = $_POST['social_history'] ?? '';
    $doctor_notes = $_POST['doctor_notes'] ?? '';

    $insert = $conn->prepare("INSERT INTO medical_history 
        (appointment_id, symptoms, past_illnesses, current_medications, allergies, family_history, social_history, doctor_notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("isssssss", $appointment_id, $symptoms, $past_illnesses, $current_medications, $allergies, $family_history, $social_history, $doctor_notes);

    if ($insert->execute()) {
        // ✅ Send Notification to User
        $user_id = $appointment['user_id'];

        // Fetch doctor name
        $docStmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
        $docStmt->bind_param("i", $doctor_id);
        $docStmt->execute();
        $docResult = $docStmt->get_result()->fetch_assoc();
        $doctor_name = $docResult['name'] ?? "your doctor";

        $notifMsg = "Dr. $doctor_name has written your medical history after your appointment.";
        $notifStmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
        $notifStmt->bind_param("is", $user_id, $notifMsg);
        $notifStmt->execute();

        $success = "Medical history saved successfully.";
        header("Location: write_medical_history.php?id=$appointment_id&success=1");
        exit();
    } else {
        $error = "Failed to save medical history.";
    }
}

// Reload on success
if (isset($_GET['success']) && $_GET['success'] == '1' && !$existingHistory) {
    $checkStmt->execute();
    $existingHistory = $checkStmt->get_result()->fetch_assoc();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Write Medical History - TeleMedicine</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Make header and footer static */
    header, footer {
      flex-shrink: 0;
    }

    .content-wrapper {
      flex-grow: 1;
      overflow-y: auto;
      padding: 20px;
    }

    .container {
      max-width: 800px;
      margin: 0 auto 40px;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      color: #198754;
      margin-bottom: 24px;
      border-bottom: 2px solid #198754;
      padding-bottom: 10px;
    }

    .back-btn {
      display: inline-block;
      margin-bottom: 20px;
      text-decoration: none;
      background: #6c757d;
      color: white;
      padding: 8px 14px;
      border-radius: 6px;
      font-weight: 600;
    }

    .back-btn i {
      margin-right: 6px;
    }

    .patient-details p {
      font-weight: bold;
      margin-bottom: 8px;
      color: #333;
    }

    label {
      font-weight: bold;
      margin-top: 15px;
      display: block;
      color: #222;
    }

    textarea, input {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      font-family: Arial, sans-serif;
      resize: vertical;
    }

    textarea[readonly], input[readonly] {
      background: #eee;
      color: #555;
    }

    .btn {
      margin-top: 20px;
      background-color: #198754;
      color: white;
      padding: 10px 18px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #14532d;
    }

    .message.success {
      color: green;
      font-weight: bold;
    }
    .message.error {
      color: red;
      font-weight: bold;
    }

    .readonly-field {
      background-color: #f5f5f5;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-top: 6px;
      white-space: pre-wrap;
    }

    @media(max-width: 768px) {
      .container {
        margin: 0 15px 40px;
      }
    }
  </style>
</head>
<body>

<?php include '../includes/sidebarD.php'; ?>
<?php include '../includes/headerD.php'; ?>

<div class="content-wrapper">
  <div class="container">
    <a href="doctor_paid_appointments.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back Paid Appointments</a>
    <h2><i class="fas fa-notes-medical"></i> Write Medical History</h2>

    <?php if (!empty($success)): ?>
      <p class="message success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="patient-details">
      <p><strong>Name:</strong> <?= htmlspecialchars($appointment['patient_name']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($appointment['email']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($appointment['phone']) ?></p>
      <p><strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date']) ?></p>
      <p><strong>Time:</strong> <?= htmlspecialchars($appointment['appointment_time']) ?></p>
      <p><strong>Type:</strong> <?= htmlspecialchars(ucwords(str_replace('_', ' ', $appointment['appointment_type']))) ?></p>
    </div>

    <?php if ($existingHistory): ?>
      <?php foreach ([
        'symptoms' => 'Symptoms',
        'past_illnesses' => 'Past Illnesses',
        'current_medications' => 'Current Medications',
        'allergies' => 'Allergies',
        'family_history' => 'Family History',
        'social_history' => 'Social History',
        'doctor_notes' => 'Doctor Notes'
      ] as $field => $label): ?>
        <label><?= $label ?>:</label>
        <div class="readonly-field"><?= nl2br(htmlspecialchars($existingHistory[$field])) ?></div>
      <?php endforeach; ?>
    <?php else: ?>
      <form method="POST">
        <label for="symptoms">Symptoms:</label>
        <textarea id="symptoms" name="symptoms" required></textarea>

        <label for="past_illnesses">Past Illnesses:</label>
        <textarea id="past_illnesses" name="past_illnesses"></textarea>

        <label for="current_medications">Current Medications (from Prescription):</label>
        <input type="text" id="current_medications" name="current_medications" value="<?= htmlspecialchars($currentMed) ?>" readonly>

        <label for="allergies">Allergies:</label>
        <textarea id="allergies" name="allergies"></textarea>

        <label for="family_history">Family History:</label>
        <textarea id="family_history" name="family_history"></textarea>

        <label for="social_history">Social History:</label>
        <textarea id="social_history" name="social_history"></textarea>

        <label for="doctor_notes">Doctor Notes:</label>
        <textarea id="doctor_notes" name="doctor_notes"></textarea>

        <button type="submit" class="btn"><i class="fas fa-save"></i> Write Medical History</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php include '../includes/footerD.php'; ?>

</body>
</html>
