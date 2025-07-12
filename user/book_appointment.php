<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$message = '';
$doctor_id = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process form submission
    $doctor_id = intval($_POST['doctor_id']);
    $user_id = $_SESSION['user_id'];
    $date = $_POST['appointment_date'] ?? '';
    $time = $_POST['appointment_time'] ?? '';

    // Basic validation
    if (!$doctor_id || !$date || !$time) {
        $message = "Please fill all required fields.";
    } else {
        // Insert appointment
        $sql = "INSERT INTO appointments (doctor_id, user_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiss", $doctor_id, $user_id, $date, $time);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Appointment booked successfully!";
        } else {
            $message = "Failed to book appointment. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
} elseif (isset($_GET['doctor_id'])) {
    $doctor_id = intval($_GET['doctor_id']);
} else {
    $message = "No doctor selected.";
}

// Fetch doctor info to show on page
$doctor = null;
if ($doctor_id) {
    $query = "
        SELECT u.name, u.email, u.phone, u.city, u.photo, d.specialty, d.experience, d.consult_fee
        FROM users u
        INNER JOIN doctor d ON u.id = d.user_id
        WHERE u.id = $doctor_id AND u.role = 'doctor'
    ";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $doctor = mysqli_fetch_assoc($result);
        $photoPath = !empty($doctor['photo']) ? '../uploads/profile_photos/' . htmlspecialchars($doctor['photo']) : '../uploads/default_avatar.png';
    } else {
        $message = "Doctor not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Appointment - TeleMedicine</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
<style>
  body {
    font-family: 'Poppins', sans-serif;
    padding: 30px;
    background-color: #f5f5f5;
  }
  .container {
    max-width: 600px;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
  }
  .doctor-info {
    display: flex;
    gap: 20px;
    align-items: center;
    margin-bottom: 30px;
  }
  .doctor-photo {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #27ae60;
  }
  h2 {
    color: #27ae60;
    margin: 0;
  }
  p {
    margin: 4px 0;
  }
  form {
    display: flex;
    flex-direction: column;
    gap: 15px;
  }
  label {
    font-weight: 600;
  }
  input[type=date],
  input[type=time] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
  }
  button {
    background-color: #27ae60;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
  }
  button:hover {
    background-color: #219150;
  }
  .message {
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 8px;
  }
  .message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }
  .message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  }
</style>
</head>
<body>

<div class="container">

    <?php if ($message): ?>
        <div class="message <?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($doctor): ?>
        <div class="doctor-info">
            <img src="<?php echo $photoPath; ?>" alt="Doctor Photo" class="doctor-photo">
            <div>
                <h2><i class="fas fa-user-md"></i> <?php echo htmlspecialchars($doctor['name']); ?></h2>
                <p><strong>Specialty:</strong> <?php echo htmlspecialchars($doctor['specialty']); ?></p>
                <p><strong>Experience:</strong> <?php echo htmlspecialchars($doctor['experience']); ?> years</p>
                <p><strong>Consult Fee:</strong> $<?php echo htmlspecialchars($doctor['consult_fee']); ?></p>
            </div>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">

            <label for="appointment_date">Select Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" required>

            <label for="appointment_time">Select Time:</label>
            <input type="time" id="appointment_time" name="appointment_time" required>

            <button type="submit">Confirm Appointment</button>
        </form>
    <?php else: ?>
        <p>No doctor selected to book appointment.</p>
    <?php endif; ?>

</div>

</body>
</html>
