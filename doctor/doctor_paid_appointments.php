<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$doctor_id = $_SESSION['user_id'];
$perPage = 3;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total count
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = ? AND payment_status = 'paid'");
$countStmt->bind_param("i", $doctor_id);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, ceil($total / $perPage));

// Get paginated results
$stmt = $conn->prepare("
    SELECT a.*, u.name AS patient_name, u.email, u.phone, u.id AS user_id
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE a.doctor_id = ? AND a.payment_status = 'paid'
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $doctor_id, $perPage, $offset);
$stmt->execute();
$appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Paid Appointments - TeleMedicine</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
    }
    .container {
      max-width: 1000px;
      margin: 60px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      color: #198754;
      font-size: 26px;
      margin-bottom: 24px;
      border-bottom: 2px solid #198754;
      padding-bottom: 10px;
    }
    .cards-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }
    .appointment-card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.06);
      padding: 20px;
      transition: transform 0.2s ease;
    }
    .appointment-card:hover {
      transform: translateY(-3px);
    }
    .appointment-card h3 {
      margin: 0 0 10px;
      font-size: 18px;
      color: #34495e;
    }
    .appointment-card p {
      margin: 5px 0;
      color: #555;
    }
    .badge {
      display: inline-block;
      padding: 6px 12px;
      font-size: 13px;
      border-radius: 20px;
      font-weight: 500;
      color: white;
      background-color: #28a745;
    }
    .action-buttons {
      margin-top: 12px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    .btn {
      background-color: #198754;
      color: white;
      padding: 8px 12px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      font-size: 14px;
    }
    .btn:hover {
      background-color: #14532d;
    }
    .btn-secondary {
      background-color: #0d6efd;
    }
    .btn-secondary:hover {
      background-color: #0a58ca;
    }
    .pagination {
      margin-top: 30px;
      text-align: center;
    }
    .pagination a {
      margin: 0 8px;
      padding: 8px 16px;
      background: #198754;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
    }
    .pagination a.disabled {
      background: #ccc;
      pointer-events: none;
    }
  </style>
</head>
<body>

<?php include '../includes/sidebarD.php'; ?>
<?php include '../includes/headerD.php'; ?>

<div class="container">
  <h2><i class="fas fa-money-check-alt"></i> Paid Appointments</h2>

  <?php if (empty($appointments)): ?>
    <p>No paid appointments found.</p>
  <?php else: ?>
    <div class="cards-grid">
      <?php foreach ($appointments as $appt): ?>
        <div class="appointment-card">
          <h3><?= htmlspecialchars($appt['patient_name']) ?></h3>
          <p><strong>Date:</strong> <?= htmlspecialchars($appt['appointment_date']) ?></p>
          <p><strong>Time:</strong> <?= htmlspecialchars($appt['appointment_time']) ?></p>
          <p><strong>Type:</strong> <?= ucfirst($appt['appointment_type']) ?></p>
          <p><strong>Status:</strong> <?= ucfirst($appt['status']) ?></p>
          <span class="badge">Paid</span>
          <div class="action-buttons">
            <a class="btn" href="write_prescription.php?id=<?= $appt['id'] ?>">
              <i class="fas fa-prescription"></i> Write Prescription
            </a>
            <a class="btn btn-secondary" href="write_medical_history.php?id=<?= $appt['id'] ?>">
              <i class="fas fa-notes-medical"></i> Medical History
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="pagination">
      <a href="?page=<?= $page - 1 ?>" class="<?= $page <= 1 ? 'disabled' : '' ?>">Prev</a>
      <a href="?page=<?= $page + 1 ?>" class="<?= $page >= $totalPages ? 'disabled' : '' ?>">Next</a>
    </div>
  <?php endif; ?>
</div>

<?php include '../includes/footerD.php'; ?>

</body>
</html>
