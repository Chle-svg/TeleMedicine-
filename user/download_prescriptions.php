<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$presc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($presc_id <= 0) {
    die("Invalid prescription ID.");
}

$stmt = $conn->prepare("
    SELECT p.*, m.name AS medication_name, m.side_effects, m.type AS medication_type,
           d.name AS doctor_name, a.appointment_date
    FROM prescriptions p
    JOIN medications m ON p.medication_id = m.id
    JOIN users d ON p.doctor_id = d.id
    JOIN appointments a ON p.appointment_id = a.id
    WHERE p.id = ? AND p.user_id = ?
");
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("ii", $presc_id, $user_id);
$stmt->execute();
$presc = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$presc) {
    die("Prescription not found or you do not have permission to view it.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>TeleMedicine | Prescription PDF</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f8fb;
    margin: 0;
    padding: 20px;
  }
  .prescription-container {
    background: #fff;
    max-width: 700px;
    margin: auto;
    padding: 35px 40px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
  }
  .header {
    text-align: center;
    border-bottom: 3px solid #2a7f62;
    padding-bottom: 15px;
  }
  .header h1 {
    margin: 0;
    color: #2a7f62;
    font-size: 30px;
  }
  table {
    width: 100%;
    margin-top: 30px;
    border-collapse: collapse;
  }
  th, td {
    text-align: left;
    padding: 14px 10px;
    border-bottom: 1px solid #ddd;
    font-size: 16px;
  }
  th {
    background-color: #e0f2f1;
    color: #00796b;
  }
  .footer-note {
    text-align: center;
    margin-top: 40px;
    font-size: 14px;
    color: #555;
  }
</style>
</head>
<body>
<div id="prescription" class="prescription-container" role="document" aria-label="Prescription details">
  <div class="header">
    <h1>Prescription Details</h1>
    <p><strong>TeleMedicine</strong> - Reliable Healthcare Online</p>
  </div>
  <table>
    <tr><th>Prescription ID</th><td><?= htmlspecialchars($presc['id']) ?></td></tr>
    <tr><th>Medication Name</th><td><?= htmlspecialchars($presc['medication_name']) ?> (<?= htmlspecialchars($presc['medication_type']) ?>)</td></tr>
    <tr><th>Prescribed By</th><td>Dr. <?= htmlspecialchars($presc['doctor_name']) ?></td></tr>
    <tr><th>Appointment Date</th><td><?= htmlspecialchars($presc['appointment_date']) ?></td></tr>
    <tr><th>Dosage</th><td><?= htmlspecialchars($presc['dosage']) ?></td></tr>
    <tr><th>Instructions</th><td><?= nl2br(htmlspecialchars($presc['instructions'])) ?></td></tr>
    <tr><th>Side Effects</th><td><?= nl2br(htmlspecialchars($presc['side_effects'])) ?></td></tr>
  </table>

  <p class="footer-note">
    Please follow the doctor's instructions carefully.<br>
    Contact your healthcare provider if you have any questions.
  </p>
</div>

<script>
  window.onload = function() {
    const element = document.getElementById('prescription');
    html2pdf()
      .from(element)
      .set({
        margin: 0.5,
        filename: 'TeleMedicine_Prescription_<?= $presc['id'] ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
      })
      .save()
      .then(() => {
        // Optional: after saving, redirect back to prescriptions list
        window.location.href = 'patient_prescriptions.php';
      });
  };
</script>
</body>
</html>
