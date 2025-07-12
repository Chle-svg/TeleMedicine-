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

// Fetch rejected doctor applications
$rejectedQuery = "SELECT * FROM doctor_applications WHERE status = 'Reject'";
$rejectedResult = mysqli_query($conn, $rejectedQuery);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rejected Doctors</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        /* Sidebar toggling & dropdowns */
        .sidebar.collapsed { width: 60px; }
        .sidebar.collapsed .logo span,
        .sidebar.collapsed ul li a span { display: none; }
        .sidebar.collapsed ul li a i { margin-right: 0; }
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
        .sidebar ul li .dropdown-btn i { margin-right: 10px; }
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

        /* Table styling */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .content-table th, .content-table td {
            padding: 12px 15px;
            text-align: left;
        }
        .content-table th {
            background-color: #3498db;
            color: #fff;
        }
        .content-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .doctor-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        footer {
            color: rgb(29, 31, 31);
            text-align: center;
            padding: 15px 0;
            position: relative;
            bottom: 0;
            width: 100%;
            font-size: 0.9em;
            margin-top: 30px;
        }
        .cards h2 {
            margin: 0;
            font-size: 1.5em;
            color: #2c3e50;
        }
        .cards p {
            margin: 5px 0 15px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <!-- Sidebar -->
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
                    <a href="rejected_applications.php"><i class="fas fa-user-times"></i> Rejected Applications</a>
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
                    <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <header class="admin-header">
            <div class="admin-profile">
                <i class="fas fa-user-circle"></i> <span><?= htmlspecialchars($username) ?></span>
            </div>
        </header>

        <div class="cards">
            <table class="content-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Specialty</th>
                        <th>Experience</th>
                        <th>Consult Fee</th>
                        <th>City</th>
                        <th>Phone</th>
                        <th>Application Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($rejectedResult) > 0): ?>
                        <?php while ($doctor = mysqli_fetch_assoc($rejectedResult)): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $photo = $doctor['photo'] ?? '';
                                    $photoFilePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profile_photos/' . $photo;
                                    $photoURL = '/uploads/profile_photos/' . $photo;

                                    if (!empty($photo) && file_exists($photoFilePath)) {
                                        echo '<img src="' . htmlspecialchars($photoURL) . '" class="doctor-photo" alt="Doctor Photo">';
                                    } else {
                                        echo '<img src="/uploads/profile_photos/default.jpg" class="doctor-photo" alt="Default Photo">';
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($doctor['name']) ?></td>
                                <td><?= htmlspecialchars($doctor['email']) ?></td>
                                <td><?= htmlspecialchars($doctor['specialty']) ?></td>
                                <td><?= htmlspecialchars($doctor['experience']) ?></td>
                                <td><?= htmlspecialchars($doctor['consult_fee']) ?></td>
                                <td><?= htmlspecialchars($doctor['city']) ?></td>
                                <td><?= htmlspecialchars($doctor['phone']) ?></td>
                                <td><?= htmlspecialchars($doctor['application_date']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center;">No rejected doctor applications found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer>
            &copy; <?= date('Y') ?> TeleMedicine Admin Panel.
        </footer>
    </div>
</div>

<script>
document.querySelector('.sidebar-toggle').addEventListener('click', () => {
    document.querySelector('.sidebar').classList.toggle('collapsed');
});

const dropdownBtns = document.querySelectorAll('.dropdown-btn');
dropdownBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        btn.classList.toggle('active');
        const dropdownContent = btn.nextElementSibling;
        dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
    });
});
</script>

</body>
</html>
