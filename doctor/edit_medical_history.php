<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$doctor_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Invalid history ID.");
}
$history_id = intval($_GET['id']);

// Fetch the medical history record with patient info and appointment ID
$stmt = $conn->prepare("SELECT mh.*, u.name AS patient_name, a.id AS appointment_id FROM medical_history mh
    JOIN appointments a ON mh.appointment_id = a.id
    JOIN users u ON a.user_id = u.id
    WHERE mh.id = ? AND a.doctor_id = ?");
$stmt->bind_param("ii", $history_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Medical history not found or access denied.");
}

$history = $result->fetch_assoc();

// Fetch current medications from prescriptions linked to this appointment
$prescStmt = $conn->prepare("SELECT m.name, p.dosage 
    FROM prescriptions p 
    JOIN medications m ON p.medication_id = m.id 
    WHERE p.appointment_id = ?");
$prescStmt->bind_param("i", $history['appointment_id']);
$prescStmt->execute();
$prescResult = $prescStmt->get_result();

$currentMedications = [];
while ($row = $prescResult->fetch_assoc()) {
    $currentMedications[] = $row['name'] . ($row['dosage'] ? " — " . $row['dosage'] : "");
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $symptoms = trim($_POST['symptoms']);
    $past_illnesses = trim($_POST['past_illnesses']);
    $allergies = trim($_POST['allergies']);
    $family_history = trim($_POST['family_history']);
    $social_history = trim($_POST['social_history']);
    $doctor_notes = trim($_POST['doctor_notes']);

    $update = $conn->prepare("UPDATE medical_history SET symptoms = ?, past_illnesses = ?, allergies = ?, family_history = ?, social_history = ?, doctor_notes = ? WHERE id = ?");
    $update->bind_param("ssssssi", $symptoms, $past_illnesses, $allergies, $family_history, $social_history, $doctor_notes, $history_id);
    if ($update->execute()) {
        $success = "Medical history updated successfully.";

        // Update local variables for re-display
        $history['symptoms'] = $symptoms;
        $history['past_illnesses'] = $past_illnesses;
        $history['allergies'] = $allergies;
        $history['family_history'] = $family_history;
        $history['social_history'] = $social_history;
        $history['doctor_notes'] = $doctor_notes;
    } else {
        $error = "Failed to update history.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Medical History</title>
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

  header, footer {
    flex-shrink: 0;
  }

  .content-wrapper {
    flex-grow: 1;
    overflow-y: auto;
    padding: 20px;
  }

  .container {
    max-width: 900px;
    margin: 0 auto 40px;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  }

  h2 {
    color: #1e4d2b;
    margin-bottom: 24px;
    border-bottom: 2px solid #1e4d2b;
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

  form {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
  }

  .form-group {
    flex: 1 1 calc(33.333% - 20px);
    display: flex;
    flex-direction: column;
  }

  label {
    font-weight: bold;
    margin-bottom: 6px;
    color: #333;
  }

  textarea {
    resize: vertical;
    min-height: 100px;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    font-family: inherit;
  }

  textarea[readonly] {
    background: #f0f0f0;
    color: #555;
    border-color: #aaa;
  }

  .current-medications {
    flex: 1 1 100%;
  }

  .btn {
    margin-top: 25px;
    background: #1e4d2b;
    color: white;
    border: none;
    padding: 12px 20px;
    font-size: 15px;
    border-radius: 6px;
    cursor: pointer;
    align-self: flex-start;
  }
  .btn:hover {
    background: #14532d;
  }
  .message {
    margin-top: 15px;
    font-weight: bold;
  }
  .message.success { color: green; }
  .message.error { color: red; }
  
  /* Responsive fallback */
  @media (max-width: 850px) {
    .form-group {
      flex: 1 1 100%;
    }
  }
</style>
</head>
<body>

<?php include '../includes/sidebarD.php'; ?>
<?php include '../includes/headerD.php'; ?>

<div class="content-wrapper">
  <div class="container">
    <a href="all_medical_histories.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Medical Histories</a>

    <h2><i class="fas fa-edit"></i> Edit Medical History</h2>
    <p><strong>Patient:</strong> <?= htmlspecialchars($history['patient_name']) ?></p>

    <?php if (!empty($success)): ?>
      <p class="message success">✅ <?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <p class="message error">❌ <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label for="symptoms">Symptoms:</label>
        <textarea name="symptoms" id="symptoms" required><?= htmlspecialchars($history['symptoms']) ?></textarea>
      </div>

      <div class="form-group">
        <label for="past_illnesses">Past Illnesses:</label>
        <textarea name="past_illnesses" id="past_illnesses"><?= htmlspecialchars($history['past_illnesses']) ?></textarea>
      </div>

      <div class="form-group">
        <label for="allergies">Allergies:</label>
        <textarea name="allergies" id="allergies"><?= htmlspecialchars($history['allergies']) ?></textarea>
      </div>

      <div class="form-group">
        <label for="family_history">Family History:</label>
        <textarea name="family_history" id="family_history"><?= htmlspecialchars($history['family_history']) ?></textarea>
      </div>

      <div class="form-group">
        <label for="social_history">Social History:</label>
        <textarea name="social_history" id="social_history"><?= htmlspecialchars($history['social_history']) ?></textarea>
      </div>

      <div class="form-group">
        <label for="doctor_notes">Doctor Notes:</label>
        <textarea name="doctor_notes" id="doctor_notes"><?= htmlspecialchars($history['doctor_notes']) ?></textarea>
      </div>

      <div class="form-group current-medications">
        <label for="current_medications">Current Medications (from prescriptions):</label>
        <textarea id="current_medications" readonly><?= htmlspecialchars(implode("\n", $currentMedications) ?: "No medications prescribed.") ?></textarea>
      </div>

      <button type="submit" class="btn"><i class="fas fa-save"></i> Save Changes</button>
    </form>
  </div>
</div>

<?php include '../includes/footerD.php'; ?>

</body>
</html>
