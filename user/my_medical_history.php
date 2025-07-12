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

// Count total medical histories
$countStmt = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM medical_history mh
    JOIN appointments a ON mh.appointment_id = a.id
    WHERE a.user_id = ?
");
$countStmt->bind_param("i", $user_id);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, ceil($total / $perPage));
$countStmt->close();

// Fetch paginated medical history
$stmt = $conn->prepare("
    SELECT mh.*, d.name AS doctor_name, a.appointment_date
    FROM medical_history mh
    JOIN appointments a ON mh.appointment_id = a.id
    JOIN users d ON a.doctor_id = d.id
    WHERE a.user_id = ?
    ORDER BY mh.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $user_id, $perPage, $offset);
$stmt->execute();
$histories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>My Medical History - TeleMedicine</title>
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

  .history-list {
    overflow-y: auto;
    flex-grow: 1;
    padding-right: 12px;
    margin-top: 12px;
  }

  .history-card {
    background: #e8f5e9;
    border-radius: 12px;
    box-shadow: 0 5px 14px rgba(0, 0, 0, 0.05);
    padding: 25px 30px;
    margin-bottom: 18px;
    transition: box-shadow 0.3s ease;
  }

  .history-card:hover {
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
  }

  .history-card h3 {
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

  .pagination1 {
    margin-top: 28px;
    display: flex;
    justify-content: center;
    gap: 20px;
  }

  .pagination1 a {
    padding: 10px 20px;
    background: #198754;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: background-color 0.3s ease;
  }

  .pagination1 a.disabled {
    background: #ccc;
    pointer-events: none;
    cursor: not-allowed;
  }

  .pagination1 a:hover:not(.disabled) {
    background: #14532d;
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
  <h2><i class="fas fa-notes-medical"></i> My Medical History</h2>

  <?php if (empty($histories)): ?>
    <p style="font-size:1.2rem; color:#666;">You have no medical history records yet.</p>
  <?php else: ?>
    <div class="history-list">
      <?php foreach ($histories as $h): ?>
        <div class="history-card">
          <h3>Visit on <?= htmlspecialchars($h['appointment_date']) ?></h3>
          <div class="info-grid">
            <div class="info-box">
              <p class="label">Doctor</p>
              <p>Dr. <?= htmlspecialchars($h['doctor_name']) ?></p>
            </div>
            <div class="info-box">
              <p class="label">Symptoms</p>
              <p><?= nl2br(htmlspecialchars($h['symptoms'])) ?></p>
            </div>
            <div class="info-box">
              <p class="label">Past Illnesses</p>
              <p><?= nl2br(htmlspecialchars($h['past_illnesses'])) ?></p>
            </div>
            <div class="info-box">
              <p class="label">Allergies</p>
              <p><?= nl2br(htmlspecialchars($h['allergies'])) ?></p>
            </div>
            <div class="info-box">
              <p class="label">Family History</p>
              <p><?= nl2br(htmlspecialchars($h['family_history'])) ?></p>
            </div>
            <div class="info-box">
              <p class="label">Social History</p>
              <p><?= nl2br(htmlspecialchars($h['social_history'])) ?></p>
            </div>
            <div class="info-box" style="flex: 1 1 100%;">
              <p class="label">Doctor's Notes</p>
              <p><?= nl2br(htmlspecialchars($h['doctor_notes'])) ?></p>
            </div>
          </div>
          <a href="download_medical_history.php?id=<?= $h['id'] ?>" class="download-btn" target="_blank">
            <i class="fas fa-file-download"></i> Download PDF
          </a>
        </div>
      <?php endforeach; ?>
    </div>

    <nav class="pagination1" role="navigation" aria-label="Medical History Pagination">
      <a href="?page=<?= $page - 1 ?>" class="<?= $page <= 1 ? 'disabled' : '' ?>" aria-disabled="<?= $page <= 1 ? 'true' : 'false' ?>">Prev</a>
      <a href="?page=<?= $page + 1 ?>" class="<?= $page >= $totalPages ? 'disabled' : '' ?>" aria-disabled="<?= $page >= $totalPages ? 'true' : 'false' ?>">Next</a>
    </nav>
  <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>
