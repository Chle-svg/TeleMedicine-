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

// Fetch appointment and patient info
$stmt = $conn->prepare("
    SELECT a.*, u.name AS patient_name, u.email, u.phone, u.id AS user_id
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE a.id = ? AND a.doctor_id = ?
");
$stmt->bind_param("ii", $appointment_id, $doctor_id);
$stmt->execute();
$appointment = $stmt->get_result()->fetch_assoc();

if (!$appointment) {
    die("Appointment not found or unauthorized.");
}

// Check if prescription already exists
$prescriptionStmt = $conn->prepare("
    SELECT p.*, m.name AS medication_name, m.type AS medication_type, m.side_effects,
           m.manufacturer, m.expiration_date
    FROM prescriptions p
    JOIN medications m ON p.medication_id = m.id
    WHERE p.appointment_id = ?
");
$prescriptionStmt->bind_param("i", $appointment_id);
$prescriptionStmt->execute();
$existingPrescription = $prescriptionStmt->get_result()->fetch_assoc();

$medications = $conn->query("SELECT * FROM medications");

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$existingPrescription) {
    $medication_id = intval($_POST['medication_id']);
    $dosage = $_POST['dosage'];
    $instructions = $_POST['instructions'];

    $insert = $conn->prepare("INSERT INTO prescriptions (appointment_id, doctor_id, user_id, medication_id, dosage, instructions) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("iiiiss", $appointment_id, $doctor_id, $appointment['user_id'], $medication_id, $dosage, $instructions);
    if ($insert->execute()) {
        // ✅ Send Notification to user
        $doctor_query = $conn->prepare("SELECT name FROM users WHERE id = ?");
        $doctor_query->bind_param("i", $doctor_id);
        $doctor_query->execute();
        $doctor_result = $doctor_query->get_result();
        $doctor_data = $doctor_result->fetch_assoc();
        $doctor_name = $doctor_data['name'] ?? "your doctor";

        $message = "Dr. $doctor_name has written a new prescription for your appointment.";
        $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
        $notif_stmt->bind_param("is", $appointment['user_id'], $message);
        $notif_stmt->execute();

        $success = "Prescription saved successfully.";
        header("Location: write_prescription.php?id=$appointment_id&success=1");
        exit();
    } else {
        $error = "Failed to save prescription.";
    }
}

if (isset($_GET['success']) && $_GET['success'] == '1' && !$existingPrescription) {
    $prescriptionStmt->execute();
    $existingPrescription = $prescriptionStmt->get_result()->fetch_assoc();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Write Prescription - TeleMedicine</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

    label {
      font-weight: bold;
      margin-top: 15px;
      display: block;
    }

    input, textarea, select {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
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

    .form-section {
      margin-bottom: 25px;
    }

    p.message.success { color: green; font-weight: bold; }
    p.message.error { color: red; font-weight: bold; }

    .readonly-field {
      background-color: #f5f5f5;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-top: 6px;
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
    <h2><i class="fas fa-prescription"></i> Write Prescription</h2>

    <?php if (!empty($success)): ?>
      <p class="message success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="form-section">
      <h3>Patient Details</h3>
      <p><strong>Name:</strong> <?= htmlspecialchars($appointment['patient_name']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($appointment['email']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($appointment['phone']) ?></p>
    </div>

    <?php if ($existingPrescription): ?>
      <div class="form-section">
        <h3>Existing Prescription</h3>
        <p><strong>Medication:</strong> <?= htmlspecialchars($existingPrescription['medication_name']) ?> (<?= htmlspecialchars($existingPrescription['medication_type']) ?>)</p>

        <label>Dosage:</label>
        <div class="readonly-field"><?= nl2br(htmlspecialchars($existingPrescription['dosage'])) ?></div>

        <label>Instructions:</label>
        <div class="readonly-field"><?= nl2br(htmlspecialchars($existingPrescription['instructions'])) ?></div>

        <label>Side Effects:</label>
        <div class="readonly-field"><?= nl2br(htmlspecialchars($existingPrescription['side_effects'])) ?></div>

        <label>Manufacturer:</label>
        <div class="readonly-field"><?= htmlspecialchars($existingPrescription['manufacturer'] ?? 'N/A') ?></div>

        <label>Expiration Date:</label>
        <div class="readonly-field"><?= htmlspecialchars($existingPrescription['expiration_date'] ?? 'N/A') ?></div>

        <p style="color: #198754; font-weight: bold;">Prescription already written for this appointment.</p>
      </div>
    <?php else: ?>
      <form method="post">
        <div class="form-section">
          <label for="medication_id">Select Medication:</label>
          <select name="medication_id" id="medication_id" required onchange="fillMedicationDetails()">
            <option value="">-- Select Medication --</option>
            <?php while ($row = $medications->fetch_assoc()): ?>
              <option value="<?= $row['id'] ?>"
                data-dosage="<?= htmlspecialchars($row['dosage']) ?>"
                data-instruction="<?= htmlspecialchars($row['instructions']) ?>"
                data-side-effect="<?= htmlspecialchars($row['side_effects']) ?>"
                data-manufacturer="<?= htmlspecialchars($row['manufacturer']) ?>"
                data-expiration="<?= htmlspecialchars($row['expiration_date']) ?>">
                <?= htmlspecialchars($row['name']) ?> (<?= htmlspecialchars($row['type']) ?>)
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="form-section">
          <label for="dosage">Dosage:</label>
          <input type="text" name="dosage" id="dosage" required>

          <label for="instructions">Instructions:</label>
          <textarea name="instructions" id="instructions" rows="4" required></textarea>

          <label for="side_effect">Side Effects:</label>
          <textarea id="side_effect" rows="2" readonly></textarea>

          <label for="manufacturer">Manufacturer:</label>
          <input type="text" id="manufacturer" readonly>

          <label for="expiration_date">Expiration Date:</label>
          <input type="text" id="expiration_date" readonly>
        </div>

        <button type="submit" class="btn"><i class="fas fa-save"></i> Write Prescription</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php include '../includes/footerD.php'; ?>

<script>
function fillMedicationDetails() {
  const select = document.getElementById('medication_id');
  const selected = select.options[select.selectedIndex];

  document.getElementById('dosage').value = selected.getAttribute('data-dosage') || '';
  document.getElementById('instructions').value = selected.getAttribute('data-instruction') || '';
  document.getElementById('side_effect').value = selected.getAttribute('data-side-effect') || '';
  document.getElementById('manufacturer').value = selected.getAttribute('data-manufacturer') || '';
  document.getElementById('expiration_date').value = selected.getAttribute('data-expiration') || '';
}
</script>

</body>
</html>
