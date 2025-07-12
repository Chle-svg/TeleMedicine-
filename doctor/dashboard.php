<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../unauthorized.php");
    exit();
}

include '../includes/db.php';

$doctor_id = $_SESSION['user_id'];
$doctor_name = "Doctor";

// Fetch full name from users table
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $doctor_name = "Dr. " . htmlspecialchars($row['name']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Doctor Dashboard - TeleMedicine</title>

    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
           font-family: 'Segoe UI', sans-serif;
        }

        main.content-wrapper {
            position: absolute;
            top: 60px;
            left: 300px;
            right: 0;
            bottom: 50px;
            overflow: hidden;
            background-color: #f5f5f5;
        }

        .main-content {
            height: 100%;
            overflow-y: auto;
            padding: 10px 30px 0 30px;
            box-sizing: border-box;
            margin: 0;
        }

        .main-content h1 {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .main-content .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
        }

        .card {
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
            transition: transform var(--transition), box-shadow var(--transition);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.12);
        }

        .card h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card p {
            flex-grow: 1;
            color: #555;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .btn {
            background: var(--accent);
            color: white;
            padding: 12px 22px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: background var(--transition);
            align-self: start;
        }

        .btn:hover {
            background: #009d94;
        }
    </style>
</head>
<body>

    <?php include '../includes/sidebarD.php'; ?>
    <?php include '../includes/headerD.php'; ?>

    <main class="content-wrapper">
        <div class="main-content">
            <h1>Welcome back, <?= $doctor_name ?>!</h1>
            <p class="subtitle">Here's your TeleMedicine doctor dashboard overview.</p>

            <div class="dashboard-grid">
                <div class="card">
                    <h3><i class="fas fa-calendar-check"></i> Appointments</h3>
                    <p>View and manage your upcoming patient appointments.</p>
                    <a href="appointments.php" class="btn">Manage Appointments</a>
                </div>

                

                <div class="card">
                    <h3><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</h3>
                    <p>Create and track patient prescriptions.</p>
                    <a href="doctor_prescriptions.php" class="btn">Manage Prescriptions</a>
                </div>

               

                <div class="card">
                    <h3><i class="fas fa-file-alt"></i> Manage Medical History</h3>
                    <p>Upload and manage your medical reports.</p>
                    <a href="all_medical_histories.php" class="btn">Submit Now</a>
                </div>

                <div class="card">
                    <h3><i class="fas fa-cog"></i> Settings</h3>
                    <p>Update your profile settings and preferences.</p>
                    <a href="edit_profileD.php" class="btn">Settings</a>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footerD.php'; ?>

    <script>
      document.getElementById('toggleSidebar').addEventListener('click', function () {
          const sidebar = document.querySelector('.sidebar');
          const mainWrapper = document.querySelector('main.content-wrapper');
          const mainHeader = document.querySelector('.main-header');
          const mainContent = document.querySelector('.main-content');

          sidebar.classList.toggle('collapsed');
          mainWrapper.classList.toggle('expanded');
          mainHeader.classList.toggle('collapsed');
          mainContent.classList.toggle('collapsed');
      });
    </script>

</body>
</html>
