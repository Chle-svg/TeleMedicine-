<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$adminId = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT name FROM users WHERE id = $adminId");
$username = ($row = mysqli_fetch_assoc($result)) ? $row['name'] : "Admin";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: medications.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM medications WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$medication = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$medication) {
    header("Location: medications.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Medication - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
        .content-area {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            margin-top: 30px;
            background: #fff;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
            border-radius: 6px;
        }
        .content-area h2 {
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2c3e50;
        }
        .med-detail {
            margin-bottom: 15px;
        }
        .med-detail strong {
            display: inline-block;
            width: 180px;
            color: #2c3e50;
        }
        .med-detail span {
            color: #34495e;
        }
        .action-buttons {
            margin-top: 30px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .action-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        .action-btn:hover {
            background: #2980b9;
        }
        .action-btn.edit { background: #27ae60; }
        .action-btn.delete { background: #e74c3c; }
        .action-btn.back { background: #7f8c8d; }

        /* Sidebar styles */
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
            color: rgb(29, 31, 31);
            text-align: center;
            padding: 15px 0;
            width: 100%;
            font-size: 0.9em;
            margin-top: 30px;
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
            <li><a href="medications.php" class="active"><i class="fas fa-pills"></i> <span>Medications</span></a></li>
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

    <div class="main-content">
        <header class="admin-header">
            <div class="admin-profile">
                <i class="fas fa-user-circle"></i> <span><?= htmlspecialchars($username) ?></span>
            </div>
        </header>

        <div class="content-area">
            <h2><i class="fas fa-eye"></i> Medication Details</h2>

            <div class="med-detail"><strong>ID:</strong> <span><?= $medication['id'] ?></span></div>
            <div class="med-detail"><strong>Name:</strong> <span><?= htmlspecialchars($medication['name']) ?></span></div>
            <div class="med-detail"><strong>Type:</strong> <span><?= htmlspecialchars($medication['type']) ?></span></div>
            <div class="med-detail"><strong>Dosage:</strong> <span><?= htmlspecialchars($medication['dosage']) ?></span></div>
            <div class="med-detail"><strong>Instructions:</strong> <span><?= nl2br(htmlspecialchars($medication['instructions'])) ?></span></div>
            <div class="med-detail"><strong>Manufacturer:</strong> <span><?= htmlspecialchars($medication['manufacturer']) ?></span></div>
            <div class="med-detail"><strong>Expiration Date:</strong> <span><?= htmlspecialchars($medication['expiration_date']) ?></span></div>
            <div class="med-detail"><strong>Side Effects:</strong> <span><?= nl2br(htmlspecialchars($medication['side_effects'])) ?></span></div>
            <div class="med-detail"><strong>Storage Instructions:</strong> <span><?= nl2br(htmlspecialchars($medication['storage'])) ?></span></div>

            <div class="action-buttons">
                <a href="edit_medication.php?id=<?= $medication['id'] ?>" class="action-btn edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="delete_medication.php?id=<?= $medication['id'] ?>" class="action-btn delete"
                   onclick="return confirm('Are you sure you want to delete this medication?');">
                    <i class="fas fa-trash"></i> Delete
                </a>
                <a href="medications.php" class="action-btn back">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <footer>
            &copy; <?= date("Y") ?> TeleMedicine Admin Panel.
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
