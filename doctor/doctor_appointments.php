<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';
$doctor_id = $_SESSION['user_id'];

$sql = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.appointment_type, 
               u.name AS patient_name, u.email AS patient_email, u.phone AS patient_phone, u.photo AS patient_photo
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments - Doctor Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            height: 100%;
            background: #f4f6f8;
            overflow: hidden; /* Prevent full page scroll */
        }

        .layout-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .content-area {
            padding: 40px;
            padding-left: 320px;
            background: #f4f6f8;
            overflow-y: auto;
            flex: 1;
        }

        h2 {
            font-size: 26px;
            color: #27ae60;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .appointments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            max-width: 1300px;
            margin: 0 auto;
            gap: 20px;
        }

        .appointment-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .appointment-card:hover {
            transform: translateY(-4px);
        }

        .patient-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .patient-info img {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #27ae60;
        }

        .patient-details {
            display: flex;
            flex-direction: column;
        }

        .patient-details strong {
            font-size: 16px;
            color: #2c3e50;
        }

        .patient-details span {
            font-size: 14px;
            color: #666;
        }

        .details {
            font-size: 14px;
            color: #333;
            margin: 10px 0;
        }

        .details strong {
            color: #555;
        }

        .btn-view {
            display: inline-block;
            background-color: #27ae60;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }

        .btn-view:hover {
            background-color: #219150;
        }

        @media (max-width: 768px) {
            .content-area {
                padding-left: 0;
            }

            .appointments-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="layout-wrapper">
    <?php include '../includes/sidebarD.php'; ?>
    <?php include '../includes/headerD.php'; ?>

    <div class="content-area">
        <h2><i class="fas fa-calendar-check"></i> My Appointments</h2>

        <div class="appointments-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($appt = $result->fetch_assoc()): ?>
                    <div class="appointment-card">
                        <div class="patient-info">
                            <img src="../uploads/profile_photos/<?php echo htmlspecialchars($appt['patient_photo']); ?>" alt="Patient Photo">
                            <div class="patient-details">
                                <strong><?php echo htmlspecialchars($appt['patient_name']); ?></strong>
                                <span><?php echo htmlspecialchars($appt['patient_email']); ?></span>
                            </div>
                        </div>

                        <div class="details">
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($appt['appointment_date']); ?></p>
                            <p><strong>Time:</strong> <?php echo htmlspecialchars($appt['appointment_time']); ?></p>
                            <p><strong>Type:</strong> <?php echo ucwords(str_replace('_', ' ', $appt['appointment_type'])); ?></p>
                        </div>

                        <a href="view_appointment.php?id=<?php echo $appt['id']; ?>" class="btn-view">View Details</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No appointments found.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footerD.php'; ?>
</div>

</body>
</html>
