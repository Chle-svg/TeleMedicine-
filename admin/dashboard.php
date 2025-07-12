<?php
include '../includes/db.php';
session_start();

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch admin's real name
$adminId = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT name FROM users WHERE id = $adminId");
$username = ($row = mysqli_fetch_assoc($result)) ? $row['name'] : "Admin";

// Fetch total users
$userResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$totalUsers = ($row = mysqli_fetch_assoc($userResult)) ? $row['total'] : 0;

// Fetch total doctors
$doctorResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM   doctor");
$totalDoctors = ($row = mysqli_fetch_assoc($doctorResult)) ? $row['total'] : 0;

// Fetch total appointments
$appointmentResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM appointments");
$totalAppointments = ($row = mysqli_fetch_assoc($appointmentResult)) ? $row['total'] : 0;

// Fetch pending doctor applications
$pendingResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM doctor_applications WHERE status = 'pending'");
$pendingApplications = ($row = mysqli_fetch_assoc($pendingResult)) ? $row['total'] : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar.collapsed {
            width: 60px;
        }
        .sidebar.collapsed .logo span,
        .sidebar.collapsed ul li a span {
            display: none;
        }
        .sidebar.collapsed ul li a i {
            margin-right: 0;
        }
        .sidebar-toggle {
            user-select: none;
            cursor: pointer;
            color: #3498db;
            padding: 10px 20px;
            font-size: 1.5em;
        }
        .sidebar ul li a,
        .sidebar ul li .dropdown-btn {
            display: flex;
            align-items: center;
            color: #ecf0f1;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar ul li a i,
        .sidebar ul li .dropdown-btn i {
            margin-right: 10px;
        }
        .sidebar ul li .dropdown-btn i:last-child {
            margin-left: auto;
            margin-right: 0;
        }
        .sidebar ul li .dropdown-container {
            display: none;
            background-color: #34495e;
        }
        .sidebar ul li .dropdown-container a {
            padding-left: 40px;
            display: block;
            color: #ecf0f1;
            text-decoration: none;
        }
        .sidebar ul li a:hover,
        .sidebar ul li .dropdown-btn:hover,
        .sidebar ul li .dropdown-container a:hover {
            background-color: #3d566e;
            border-radius: 4px;
        }
        footer {
            
            color:rgb(29, 31, 31);
            text-align: center;
            padding: 15px 0;
            position: relative;
            bottom: 0;
            width: 100%;
            font-size: 0.9em;
            margin-top: 30px;
        }
        footer a {
            color: #ecf0f1;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="sidebar">
        <div class="logo">
            <span>💊 TeleMedicine</span>
        </div>

        <div class="sidebar-toggle" title="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </div>

        <ul>
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a></li>
            
            <li>
                <a href="#" class="dropdown-btn">
                    <i class="fas fa-user-md"></i> <span>Doctors Applications</span>
                    <i class="fas fa-caret-down"></i>
                </a>
                <div class="dropdown-container">
                    <a href="View_Application.php"><i class="fas fa-inbox"></i> Pending Applications</a>
                    <a href="approve_doctors.php"><i class="fas fa-user-check"></i> Accepted Doctors</a>
                    <a href="rejected_application.php"><i class="fas fa-user-times"></i> Rejected Applications</a>
                </div>
            </li>
            
            <li><a href="view_users.php"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
            <li><a href="medications.php"><i class="fas fa-pills"></i> <span>Medications</span></a></li>
            <li><a href="approve_payments.php"><i class="fas fa-credit-card"></i> <span>Approve Payments</span></a></li>
            
            <li>
                <a href="#" class="dropdown-btn">
                    <i class="fas fa-cog"></i> <span>Settings</span>
                    <i class="fas fa-caret-down"></i>
                </a>
                <div class="dropdown-container">
                    <a href="edit_profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
                    <a href="../auth/logoutA.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <header class="admin-header">
            <div class="admin-profile">
                <i class="fas fa-user-circle"></i> <span><?= htmlspecialchars($username) ?></span>
            </div>
        </header>

        <div class="cards">
            <div class="card">
                <h3><?= $totalDoctors ?></h3>
                <p><i class="fas fa-user-md"></i> Total Doctors</p>
            </div>
            <div class="card">
                <h3><?= $totalUsers ?></h3>
                <p><i class="fas fa-users"></i> Total Users</p>
            </div>
            <div class="card">
                <h3><?= $totalAppointments ?></h3>
                <p><i class="fas fa-calendar-check"></i> Total Appointments</p>
            </div>
            <div class="card">
                <h3><?= $pendingApplications ?></h3>
                <p><i class="fas fa-hourglass-half"></i> Pending Applications</p>
            </div>
        </div>

        <div class="chart-box">
            <h2><i class="fas fa-chart-bar"></i> Revenue Overview</h2>
            <canvas id="revenueChart"></canvas>
        </div>

        <footer>
            &copy; <?= date('Y') ?>  TeleMedicine Admin Panel.
        </footer>
    </div>
</div>

<script>
document.querySelector('.sidebar-toggle').addEventListener('click', () => {
    document.querySelector('.sidebar').classList.toggle('collapsed');
});

// Dropdown toggles
const dropdownBtns = document.querySelectorAll('.dropdown-btn');
dropdownBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        btn.classList.toggle('active');
        const dropdownContent = btn.nextElementSibling;
        dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
    });
});

// Chart.js
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
            label: 'Revenue (ETB)',
            data: [1200, 1900, 3000, 2500, 2200],
            backgroundColor: '#3498db'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

</body>
</html>
