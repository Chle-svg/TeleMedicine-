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
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search setup
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_sql = '';
if ($search !== '') {
    $search_sql = " AND name LIKE ?";
}

// Get total pending applications count with search filter
if ($search !== '') {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM doctor_applications WHERE status = 'pending' $search_sql");
    $like_search = "%$search%";
    $stmt->bind_param("s", $like_search);
    $stmt->execute();
    $totalResult = $stmt->get_result();
} else {
    $totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM doctor_applications WHERE status = 'pending'");
}

$totalRow = $totalResult->fetch_assoc();
$totalApplications = $totalRow['total'];

// Get pending applications for current page with search filter
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM doctor_applications WHERE status = 'pending' $search_sql ORDER BY id DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $like_search, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = mysqli_query($conn, "SELECT * FROM doctor_applications WHERE status = 'pending' ORDER BY id DESC LIMIT $limit OFFSET $offset");
}

// Total pages
$totalPages = ceil($totalApplications / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Doctor Applications - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        /* Additional styling for applications table inside content */
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
            padding: 6px 12px;
            border-radius: 3px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-view:hover {
            background-color: #2980b9;
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
        /* Sidebar toggle button styling */
        .sidebar-toggle {
            user-select: none;
            cursor: pointer;
            color: #3498db;
            padding: 10px 20px;
            font-size: 1.5em;
        }
        /* Sidebar collapse style */
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
        /* Dropdown styles from dashboard */
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
        /* Search form styling */
        .search-form {
            padding-left: 700px;
            margin-bottom: 15px;
            display: flex;
            justify-content: flex-start;
        }
        .search-input {
            padding: 8px 12px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 3px 0 0 3px;
            width: 250px;
            outline: none;
        }
        .search-button {
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 0 3px 3px 0;
            cursor: pointer;
            font-size: 15px;
        }
        .search-button:hover {
            background-color: #2980b9;
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
                <a href="#" class="dropdown-btn active">
                    <i class="fas fa-user-md"></i> <span>Doctors Applications</span>
                    <i class="fas fa-caret-down"></i>
                </a>
                <div class="dropdown-container" style="display: block;">
                    <a href="View_Application.php" class="active"><i class="fas fa-inbox"></i> Pending Applications</a>
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
                    <a href="../admin/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <header class="admin-header">
            <div class="admin-profile">
                <i class="fas fa-user-circle"></i> <span><?= htmlspecialchars($adminName) ?></span>
            </div>
        </header>

        <div class="content-area">
            <h2><i class="fas fa-user-md"></i> Pending Doctor Applications</h2>
            <p>List of all pending doctor applications awaiting review.</p>

            <!-- Search Form -->
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" class="search-input" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>" />
                <button type="submit" class="search-button"><i class="fas fa-search"></i> Search</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th> Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Specialization</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($app = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['id']) ?></td>
                            <td><?= htmlspecialchars($app['name']) ?></td>
                            <td><?= htmlspecialchars($app['email']) ?></td>
                            <td><?= htmlspecialchars($app['phone']) ?></td>
                            <td><?= htmlspecialchars($app['specialty']) ?></td>
                            <td>
                                <a href="view_doctor_application.php?id=<?= $app['id'] ?>" class="btn-view">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No pending applications found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php
                // Preserve search param in pagination links
                $search_param = $search !== '' ? '&search=' . urlencode($search) : '';
                ?>

                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 . $search_param ?>">&laquo; Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i . $search_param ?>" <?= $i === $page ? 'class="active"' : '' ?>><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 . $search_param ?>">Next &raquo;</a>
                <?php endif; ?>
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
</script>

</body>
</html>
