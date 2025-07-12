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

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM medical_history mh JOIN appointments a ON mh.appointment_id = a.id WHERE a.doctor_id = ?");
$countStmt->bind_param("i", $doctor_id);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, ceil($total / $perPage));
$countStmt->close();

$stmt = $conn->prepare("SELECT mh.*, u.name AS patient_name, a.appointment_date, a.appointment_time, m.name AS medication_name, p.dosage 
                        FROM medical_history mh 
                        JOIN appointments a ON mh.appointment_id = a.id 
                        JOIN users u ON a.user_id = u.id 
                        LEFT JOIN prescriptions p ON p.appointment_id = a.id 
                        LEFT JOIN medications m ON p.medication_id = m.id 
                        WHERE a.doctor_id = ? 
                        ORDER BY mh.created_at DESC 
                        LIMIT ? OFFSET ?");
$stmt->bind_param("iii", $doctor_id, $perPage, $offset);
$stmt->execute();
$histories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>  Written Medical Histories - TeleMedicine</title>
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
      display: flex;
      align-items: center;
      gap: 10px;
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

    .history-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }

    .history-header h3 {
      font-weight: 700;
      color: #2a7f62;
      margin: 0;
      font-size: 1.5rem;
    }

    a.edit-btn {
      background: #2a7f62;
      color: #fff;
      font-weight: 700;
      padding: 8px 18px;
      border-radius: 8px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      box-shadow: 0 6px 15px rgba(42, 127, 98, 0.45);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    a.edit-btn:hover {
      background: #1f6149;
      box-shadow: 0 9px 18px rgba(31, 97, 73, 0.6);
    }

    .info-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      color: #2c3e50;
    }

    .info-box {
      flex: 1 1 calc(33.333% - 20px);
      background: #f4fff5;
      padding: 15px 18px;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .info-label {
      font-weight: 700;
      color: #2a7f62;
      margin-bottom: 6px;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .info-value {
      font-size: 14px;
      white-space: pre-wrap;
      line-height: 1.4;
      color: #34495e;
    }

    /* Full width box for long text */
    .info-box.full-width {
      flex: 1 1 100%;
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

<?php include '../includes/sidebarD.php'; ?>
<?php include '../includes/headerD.php'; ?>

<main class="container">
  <h2><i class="fas fa-file-medical-alt"></i> My Written Medical Histories</h2>

  <?php if (empty($histories)): ?>
    <p style="font-size: 1.2rem; color: #666; text-align: center;">No medical histories written yet.</p>
  <?php else: ?>
    <div class="history-list">
      <?php foreach ($histories as $history): ?>
        <div class="history-card">
          <div class="history-header">
            <h3>Patient: <?= htmlspecialchars($history['patient_name']) ?></h3>
            <a href="edit_medical_history.php?id=<?= $history['id'] ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
          </div>

          <div class="info-grid">
            <div class="info-box">
              <p class="info-label">Appointment Date</p>
              <p class="info-value"><?= htmlspecialchars($history['appointment_date']) ?></p>
            </div>
            <div class="info-box">
              <p class="info-label">Appointment Time</p>
              <p class="info-value"><?= htmlspecialchars($history['appointment_time']) ?></p>
            </div>
            <div class="info-box">
              <p class="info-label">Symptoms</p>
              <p class="info-value"><?= nl2br(htmlspecialchars($history['symptoms'])) ?></p>
            </div>

            <div class="info-box">
              <p class="info-label">Past Illnesses</p>
              <p class="info-value"><?= nl2br(htmlspecialchars($history['past_illnesses'])) ?></p>
            </div>
            <div class="info-box">
              <p class="info-label">Allergies</p>
              <p class="info-value"><?= nl2br(htmlspecialchars($history['allergies'])) ?></p>
            </div>
            <div class="info-box">
              <p class="info-label">Current Medication</p>
              <p class="info-value">
                <?php if ($history['medication_name']): ?>
                  <?= htmlspecialchars($history['medication_name']) ?> — <?= htmlspecialchars($history['dosage']) ?>
                <?php else: ?>
                  <em>None</em>
                <?php endif; ?>
              </p>
            </div>

            <div class="info-box">
              <p class="info-label">Family History</p>
              <p class="info-value"><?= nl2br(htmlspecialchars($history['family_history'])) ?></p>
            </div>
            <div class="info-box">
              <p class="info-label">Social History</p>
              <p class="info-value"><?= nl2br(htmlspecialchars($history['social_history'])) ?></p>
            </div>
            <div class="info-box full-width">
              <p class="info-label">Doctor Notes</p>
              <p class="info-value"><?= nl2br(htmlspecialchars($history['doctor_notes'])) ?></p>
            </div>
          </div>
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
