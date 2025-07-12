<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$history_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($history_id <= 0) {
    die("Invalid medical history ID.");
}

$stmt = $conn->prepare("
    SELECT mh.*, u.name AS patient_name, a.appointment_date, d.name AS doctor_name
    FROM medical_history mh
    JOIN appointments a ON mh.appointment_id = a.id
    JOIN users u ON a.user_id = u.id
    JOIN users d ON a.doctor_id = d.id
    WHERE mh.id = ? AND a.user_id = ?
");
$stmt->bind_param("ii", $history_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$history = $result->fetch_assoc();
$stmt->close();

if (!$history) {
    die("Medical history not found or unauthorized.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Download Medical History - TeleMedicine</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: #f0f4f8;
      margin: 0;
      padding: 20px;
    }
    .pdf-container {
      background: #fff;
      padding: 40px;
      max-width: 750px;
      margin: auto;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }
    .header {
      text-align: center;
      border-bottom: 3px solid #2a7f62;
      padding-bottom: 15px;
    }
    .header h1 {
      color: #2a7f62;
      margin: 0;
    }
    table {
      width: 100%;
      margin-top: 25px;
      border-collapse: collapse;
    }
    th, td {
      text-align: left;
      padding: 10px 12px;
      border-bottom: 1px solid #ddd;
      font-size: 15px;
    }
    th {
      background: #e0f2f1;
      color: #00796b;
    }
    .footer-note {
      text-align: center;
      margin-top: 40px;
      font-size: 13px;
      color: #666;
    }
  </style>
</head>
<body>

<div id="history" class="pdf-container">
  <div class="header">
    <h1>Medical History</h1>
    <p><strong>TeleMedicine</strong> — Patient Record Summary</p>
  </div>

  <table>
    <tr><th>History ID</th><td><?= $history['id'] ?></td></tr>
    <tr><th>Patient Name</th><td><?= htmlspecialchars($history['patient_name']) ?></td></tr>
    <tr><th>Doctor Name</th><td><?= htmlspecialchars($history['doctor_name']) ?></td></tr>
    <tr><th>Appointment Date</th><td><?= htmlspecialchars($history['appointment_date']) ?></td></tr>
    <tr><th>Symptoms</th><td><?= nl2br(htmlspecialchars($history['symptoms'])) ?></td></tr>
    <tr><th>Past Illnesses</th><td><?= nl2br(htmlspecialchars($history['past_illnesses'])) ?></td></tr>
    <tr><th>Allergies</th><td><?= nl2br(htmlspecialchars($history['allergies'])) ?></td></tr>
    <tr><th>Family History</th><td><?= nl2br(htmlspecialchars($history['family_history'])) ?></td></tr>
    <tr><th>Social History</th><td><?= nl2br(htmlspecialchars($history['social_history'])) ?></td></tr>
    <tr><th>Doctor's Notes</th><td><?= nl2br(htmlspecialchars($history['doctor_notes'])) ?></td></tr>
  </table>

  <p class="footer-note">
    This document is confidential and intended for personal health records only.
  </p>
</div>

<script>
window.onload = function () {
  const element = document.getElementById('history');
  html2pdf().from(element)
    .set({
      margin: 0.5,
      filename: 'TeleMedicine_History_<?= $history['id'] ?>.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    })
    .save()
    .then(() => {
      window.location.href = 'my_medical_history.php';
    });
};
</script>

</body>
</html>
