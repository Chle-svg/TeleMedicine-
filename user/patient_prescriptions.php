<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$perPage = 1;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Total prescriptions count
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM prescriptions WHERE user_id = ?");
$countStmt->bind_param("i", $user_id);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, ceil($total / $perPage));
$countStmt->close();

// Fetch prescription info
$stmt = $conn->prepare("
    SELECT p.*, m.name AS medication_name, m.side_effects, m.type AS medication_type,
           d.name AS doctor_name, a.appointment_date
    FROM prescriptions p
    JOIN medications m ON p.medication_id = m.id
    JOIN users d ON p.doctor_id = d.id
    JOIN appointments a ON p.appointment_id = a.id
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $user_id, $perPage, $offset);
$stmt->execute();
$prescriptions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Prescriptions - TeleMedicine</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f8fa;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 960px;
      height: 550px;
      margin: 70px auto 80px;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 12px 35px rgba(0,0,0,0.12);
      padding: 40px 50px;
      display: flex;
      flex-direction: column;
    }

    h2 {
      color: #2a7f62;
      font-weight: 700;
      font-size: 2.4rem;
      letter-spacing: 0.04em;
      margin-bottom: 24px;
      border-bottom: 3px solid #2a7f62;
      padding-bottom: 12px;
    }

    .list {
      overflow-y: auto;
      flex-grow: 1;
      padding-right: 12px;
      margin-top: 12px;
    }

    .card {
      background: #e8f5e9;
      border-radius: 12px;
      box-shadow: 0 5px 14px rgba(0, 0, 0, 0.05);
      padding: 25px 30px;
      margin-bottom: 18px;
      transition: box-shadow 0.3s ease;
    }

    .card:hover {
      box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
    }

    .card h3 {
      font-weight: 700;
      color: #2a7f62;
      margin: 0 0 12px 0;
      font-size: 1.5rem;
    }

    .info-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 12px;
    }

    .info-box {
      flex: 1 1 calc(33.333% - 20px);
      background: #f4fff5;
      padding: 10px 15px;
      border-radius: 8px;
    }

    .info-box p {
      margin: 6px 0;
      color: #333;
    }

    .info-box .label {
      font-weight: bold;
      color: #4a7a5c;
    }

    a.download-btn {
      margin-top: 18px;
      display: inline-block;
      background: #2a7f62;
      color: white;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: 700;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    a.download-btn:hover {
      background: #1f6149;
    }

    .pagination {
      margin-top: 28px;
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    .pagination a {
      padding: 10px 20px;
      background: #2a7f62;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      user-select: none;
      transition: background-color 0.3s ease;
    }

    .pagination a.disabled {
      background: #ccc;
      pointer-events: none;
      cursor: not-allowed;
    }

    .pagination a:hover:not(.disabled) {
      background: #1f6149;
    }

    @media(max-width: 900px) {
      .info-box {
        flex: 1 1 100%;
      }
    }
  </style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>
<?php include '../includes/header.php'; ?>

<main class="container">
  <h2><i class="fas fa-prescription-bottle-alt"></i> My Prescriptions</h2>

  <?php if (empty($prescriptions)): ?>
    <p style="font-size:1.2rem; color:#666;">You have no prescriptions yet.</p>
  <?php else: ?>
    <div class="list">
      <?php foreach ($prescriptions as $presc): ?>
        <div class="card">
          <h3><?= htmlspecialchars($presc['medication_name']) ?> <small>(<?= htmlspecialchars($presc['medication_type']) ?>)</small></h3>
          <div class="info-grid">
            <div class="info-box">
              <p class="label">Doctor</p>
              <p>Dr. <?= htmlspecialchars($presc['doctor_name']) ?></p>
            </div>
            <div class="info-box">
              <p class="label">Appointment Date</p>
              <p><?= htmlspecialchars($presc['appointment_date']) ?></p>
            </div>
            <div class="info-box">
              <p class="label">Dosage</p>
              <p><?= htmlspecialchars($presc['dosage']) ?></p>
            </div>
            <div class="info-box" style="flex: 1 1 100%;">
              <p class="label">Instructions</p>
              <p><?= nl2br(htmlspecialchars($presc['instructions'])) ?></p>
            </div>
            <div class="info-box" style="flex: 1 1 100%;">
              <p class="label">Side Effects</p>
              <p><?= nl2br(htmlspecialchars($presc['side_effects'])) ?></p>
            </div>
          </div>
          <a href="download_prescriptions.php?id=<?= $presc['id'] ?>" target="_self" class="download-btn">
            <i class="fas fa-file-download"></i> Download PDF
          </a>
        </div>
      <?php endforeach; ?>
    </div>

    <nav class="pagination" role="navigation" aria-label="Prescription Pagination">
      <a href="?page=<?= $page - 1 ?>" class="<?= $page <= 1 ? 'disabled' : '' ?>" aria-disabled="<?= $page <= 1 ? 'true' : 'false' ?>">Prev</a>
      <a href="?page=<?= $page + 1 ?>" class="<?= $page >= $totalPages ? 'disabled' : '' ?>" aria-disabled="<?= $page >= $totalPages ? 'true' : 'false' ?>">Next</a>
    </nav>
  <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>
