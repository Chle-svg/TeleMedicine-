<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Get admin info
$admin_id = $_SESSION['user_id'];
$adminQuery = mysqli_query($conn, "SELECT name FROM users WHERE id = $admin_id");
$adminData = mysqli_fetch_assoc($adminQuery);
$adminName = $adminData ? $adminData['name'] : 'Admin';

// Search
$search = $_GET['search'] ?? '';
$searchQuery = $search ? "WHERE name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'" : '';

// Pagination setup
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total users with search
$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users $searchQuery");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalUsers = $totalRow['total'];

// Fetch users
$result = mysqli_query($conn, "SELECT * FROM users $searchQuery ORDER BY id DESC LIMIT $limit OFFSET $offset");
$totalPages = ceil($totalUsers / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .content-area {
            padding: 20px;
        }
        .content-area h2 {
            margin-bottom: 10px;
        }
        .search-bar {
            text-align: right;
            margin-bottom: 20px;
             padding-left: 700px;
        }
        .search-bar input[type="text"] {
            padding: 6px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .search-bar button {
            padding: 6px 12px;
            background: #3498db;
            border: none;
            color: white;
            border-radius: 3px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        table thead {
            background-color: #3498db;
            color: #fff;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-view {
            padding: 6px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 14px;
            color: white;
            background-color: #3498db;
        }
        .pagination {
            margin-top: 15px;
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
            <li><a href="view_users.php" class="active"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
            <li><a href="medications.php"><i class="fas fa-pills"></i> <span>Medications</span></a></li>
            <li><a href="approve_payments.php"><i class="fas fa-credit-card"></i> <span>Approve Payments</span></a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn"><i class="fas fa-cog"></i> <span>Settings               ▼</span></a>
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
            <h2><i class="fas fa-users"></i> Manage Users</h2>
            <p>View all registered users in the system.</p>

            <div class="search-bar">
                <form method="GET">
                    <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <a href="view_user.php?id=<?= $user['id'] ?>" class="btn-view">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">No users found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">&laquo; Prev</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" <?= $i === $page ? 'class="active"' : '' ?>><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>

        <footer style="text-align:center; padding: 15px; color: #666; font-size: 14px;">
            &copy; <?= date("Y") ?> TeleMedicine Admin Panel
        </footer>
    </div>
</div>

<script>
document.querySelector('.sidebar-toggle').addEventListener('click', () => {
    document.querySelector('.sidebar').classList.toggle('collapsed');
});
</script>

</body>
</html>
