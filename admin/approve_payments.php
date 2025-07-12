<?php
include '../includes/db.php';
session_start();

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Get logged-in admin's name
$admin_id = $_SESSION['user_id'];
$adminQuery = mysqli_query($conn, "SELECT name FROM users WHERE id = $admin_id");
$adminData = mysqli_fetch_assoc($adminQuery);
$adminName = $adminData ? $adminData['name'] : 'Admin';

// Pagination setup
$limit = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get total count of paid appointments
$countQuery = "SELECT COUNT(*) AS total FROM appointments WHERE payment_status = 'paid'";
$countResult = mysqli_query($conn, $countQuery);
$totalAppointments = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalAppointments / $limit);

// Fetch paginated paid appointments with user and doctor names
$sql = "
    SELECT 
        a.id, a.appointment_date, a.appointment_time, a.appointment_type,
        a.payment_status, a.consult_fee,
        u.name AS user_name, d.name AS doctor_name
    FROM appointments a
    INNER JOIN users u ON a.user_id = u.id
    INNER JOIN users d ON a.doctor_id = d.id
    WHERE a.payment_status = 'paid'
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
    LIMIT $limit OFFSET $offset
";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Payments - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .content-area {
            padding: 20px;
        }
        .content-area h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            border-radius: 5px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
        }
        th {
            background-color: #3498db;
            color: white;
            text-transform: uppercase;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status-paid {
            color: green;
            font-weight: bold;
            text-transform: capitalize;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            background-color: #3498db;
            color: white;
            border-radius: 3px;
            text-decoration: none;
        }
        .pagination a.active, .pagination a:hover {
            background-color: #2980b9;
        }
        .sidebar-toggle {
            user-select: none;
            cursor: pointer;
            color: #3498db;
            padding: 10px 20px;
            font-size: 1.5em;
        }
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
        footer {
            text-align: center;
            padding: 15px;
            color: #666;
            font-size: 14px;
            margin-top: 20px;
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
            <li><a href="View_Application.php"><i class="fas fa-user-md"></i> <span> Doctors Applications</span></a></li>
            <li><a href="view_users.php"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
            <li><a href="medications.php"><i class="fas fa-pills"></i> <span>Medications</span></a></li>
            <li><a href="approve_payments.php" class="active"><i class="fas fa-credit-card"></i> <span>Approve Payments</span></a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn"><i class="fas fa-cog"></i> <span>Settings ▼</span></a>
                <ul class="dropdown-content">
                    <li><a href="edit_profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
                    <li><a href="../admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header class="admin-header">
            <div class="admin-profile">
                <i class="fas fa-user-circle"></i> <span><?= htmlspecialchars($adminName) ?></span>
            </div>
        </header>

        <div class="content-area">
            <h2><i class="fas fa-credit-card"></i> Approved Payments (Paid Appointments)</h2>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Consult Fee</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['user_name']) ?></td>
                                <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                                <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                                <td><?= htmlspecialchars($row['appointment_time']) ?></td>
                                <td><?= ucwords(str_replace('_', ' ', $row['appointment_type'])) ?></td>
                                <td><?= htmlspecialchars($row['consult_fee']) ?> ETB</td>
                                <td class="status-paid"><?= ucfirst($row['payment_status']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" <?= $i === $page ? 'class="active"' : '' ?>><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <p>No paid appointments found.</p>
            <?php endif; ?>
        </div>

        <footer>
            &copy; <?= date("Y") ?> TeleMedicine Admin Panel
        </footer>
    </div>
</div>

<script>
document.querySelector('.sidebar-toggle').addEventListener('click', () => {
    document.querySelector('.sidebar').classList.toggle('collapsed');
});

// Optional: override back button to dashboard
history.pushState(null, null, location.href);
window.onpopstate = function () {
    location.href = 'dashboard.php';
};
</script>

</body>
</html>
