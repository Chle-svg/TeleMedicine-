<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$doctor_id = $_SESSION['user_id'];
$perPage = 1;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total prescriptions count for this doctor
$countStmt = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM prescriptions 
    WHERE doctor_id = ?
");
$countStmt->bind_param("i", $doctor_id);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, ceil($total / $perPage));

// Fetch prescriptions with joins to get patient, medication, appointment info
$stmt = $conn->prepare("
    SELECT p.*, m.name AS medication_name, m.side_effects, m.type AS medication_type,
           u.name AS patient_name, a.appointment_date
    FROM prescriptions p
    JOIN medications m ON p.medication_id = m.id
    JOIN users u ON p.user_id = u.id
    JOIN appointments a ON p.appointment_id = a.id
    WHERE p.doctor_id = ?
    ORDER BY p.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $doctor_id, $perPage, $offset);
$stmt->execute();
$prescriptions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Prescriptions Written - TeleMedicine</title>
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
    height: 470px;
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
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
  }

  .prescriptions-list {
    overflow-y: auto;
    flex-grow: 1;
    padding-right: 12px;
    margin-top: 12px;
  }

  .prescriptions-list::-webkit-scrollbar {
    width: 8px;
  }
  .prescriptions-list::-webkit-scrollbar-thumb {
    background-color: #2a7f62;
    border-radius: 6px;
  }
  .prescriptions-list::-webkit-scrollbar-track {
    background: #f0f3f7;
  }
  @-moz-document url-prefix() {
    .prescriptions-list {
      scrollbar-width: thin;
      scrollbar-color: #2a7f62 #f0f3f7;
    }
  }

  .prescription-card {
    background: #e8f5e9;
    border-radius: 12px;
    box-shadow: 0 5px 14px rgba(0, 0, 0, 0.05);
    padding: 25px 30px;
    margin-bottom: 18px;
    transition: box-shadow 0.3s ease;
  }

  .prescription-card:hover,
  .prescription-card:focus {
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
  }

  .prescription-card h3 {
    font-weight: 700;
    color: #2a7f62;
    margin: 0 0 12px 0;
    font-size: 1.5rem;
  }

  .prescription-info {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    color: #2c3e50;
  }

  .prescription-info > div {
    flex: 1 1 calc(33.333% - 20px);
    background: #f4fff5;
    padding: 12px 15px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
  }

  .prescription-info > div.full-width {
    flex: 1 1 100%;
  }

  .label {
    font-weight: 700;
    color: #4a7a5c;
    margin-bottom: 6px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .info-value {
    font-size: 14px;
    white-space: pre-wrap;
    line-height: 1.4;
  }

  .pagination1 {
    margin-top: 28px;
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-shrink: 0;
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
    .prescription-info > div {
      flex: 1 1 100%;
    }
  }
</style>
</head>
<body>

<?php include '../includes/sidebarD.php'; ?>
<?php include '../includes/headerD.php'; ?>

<main class="container" role="main" aria-label="Prescriptions Written">
  <h2><i class="fas fa-notes-medical"></i> Prescriptions Written</h2>

  <?php if (empty($prescriptions)): ?>
    <p style="font-size:1.2rem; color:#666; text-align: center;">You have not written any prescriptions yet.</p>
  <?php else: ?>
    <div class="prescriptions-list" tabindex="0" aria-label="List of prescriptions you wrote">
      <?php foreach ($prescriptions as $presc): ?>
        <article class="prescription-card" tabindex="0" aria-label="Prescription for <?= htmlspecialchars($presc['medication_name']) ?> prescribed to <?= htmlspecialchars($presc['patient_name']) ?> on <?= htmlspecialchars($presc['appointment_date']) ?>">
          <h3><?= htmlspecialchars($presc['medication_name']) ?> <small>(<?= htmlspecialchars($presc['medication_type']) ?>)</small></h3>
          <div class="prescription-info">
            <div>
              <p class="label">Prescribed to</p>
              <p class="info-value"><?= htmlspecialchars($presc['patient_name']) ?></p>
            </div>
            <div>
              <p class="label">Appointment Date</p>
              <p class="info-value"><?= htmlspecialchars($presc['appointment_date']) ?></p>
            </div>
            <div>
              <p class="label">Dosage</p>
              <p class="info-value"><?= htmlspecialchars($presc['dosage']) ?></p>
            </div>
          </div>
          <div class="prescription-info">
            <div class="full-width">
              <p class="label">Instructions</p>
              <p class="info-value"><?= nl2br(htmlspecialchars($presc['instructions'])) ?></p>
            </div>
          </div>
          <div class="prescription-info">
            <div class="full-width">
              <p class="label">Side Effects</p>
              <p class="info-value"><?= nl2br(htmlspecialchars($presc['side_effects'])) ?></p>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <nav class="pagination1" role="navigation" aria-label="Prescriptions Pagination">
      <a href="?page=<?= $page - 1 ?>" class="<?= $page <= 1 ? 'disabled' : '' ?>" aria-disabled="<?= $page <= 1 ? 'true' : 'false' ?>">Prev</a>
      <a href="?page=<?= $page + 1 ?>" class="<?= $page >= $totalPages ? 'disabled' : '' ?>" aria-disabled="<?= $page >= $totalPages ? 'true' : 'false' ?>">Next</a>
    </nav>
  <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>

</body>
</html>
