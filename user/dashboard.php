<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$username = 'User';

// ✅ Fetch user's name from the database
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = htmlspecialchars($row['name']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard - TeleMedicine</title>

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

<?php include '../includes/sidebar.php'; ?>
<?php include '../includes/header.php'; ?>

<main class="content-wrapper">
    <div class="main-content">
        <h1>Welcome back, <?= $username ?>!</h1>
        <p class="subtitle">Here's a quick overview of your TeleMedicine dashboard.</p>

        <div class="dashboard-grid">
            <div class="card">
                <h3><i class="fas fa-user-md"></i> Doctors</h3>
                <p>View doctor profiles and select a doctor</p>
                <a href="list_doctors.php" class="btn">View Doctors</a>
            </div>

            <div class="card">
                <h3><i class="fas fa-calendar-check"></i> Schedule</h3>
                <p>Check and manage your appointment schedule easily.</p>
                <a href="Uappointments.php" class="btn">View Schedule</a>
            </div>

            <div class="card">
                <h3><i class="fas fa-notes-medical"></i> Medical History</h3>
                <p>Review your past medical records securely.</p>
                <a href="my_medical_history.php" class="btn">View History</a>
            </div>

            <div class="card">
                <h3><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</h3>
                <p>View prescriptions written for you by doctors.</p>
                <a href="patient_prescriptions.php" class="btn">View Prescriptions</a>
            </div>

            <div class="card">
                <h3><i class="fas fa-user-plus"></i> Apply as Doctor</h3>
                <p>Submit your application to become a certified doctor on TeleMedicine.</p>
                <a href="applt_to_be_dr.php" class="btn">Apply Now</a>
            </div>

            <div class="card">
                <h3><i class="fas fa-cog"></i> Settings</h3>
                <p>Update your profile info, password, and profile picture.</p>
                <a href="edit_profileU.php" class="btn">Settings</a>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

<script>
document.getElementById('toggleSidebar').addEventListener('click', function () {
    const sidebar = document.querySelector('.sidebar');
    const header = document.querySelector('.main-header');
    const contentWrapper = document.querySelector('main.content-wrapper');
    const mainContent = document.querySelector('.main-content');

    sidebar.classList.toggle('collapsed');
    header.classList.toggle('collapsed');
    contentWrapper.classList.toggle('expanded');
    mainContent.classList.toggle('collapsed');
});
</script>

</body>
</html>
