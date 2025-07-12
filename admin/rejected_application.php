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
$params = [];
$types = '';

if ($search !== '') {
    $search_sql = " AND users.name LIKE ?";
    $search_param = "%$search%";
}

// Count total rejected applications with search
if ($search !== '') {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM doctor_applications INNER JOIN users ON doctor_applications.user_id = users.id WHERE doctor_applications.status = 'reject' $search_sql");
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $totalResult = $stmt->get_result();
} else {
    $totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM doctor_applications WHERE status = 'reject'");
}

$totalRow = $totalResult->fetch_assoc();
$totalApplications = $totalRow['total'];

// Get rejected applications with user info and search filter
if ($search !== '') {
    $stmt = $conn->prepare("
        SELECT doctor_applications.id AS application_id, doctor_applications.specialty, doctor_applications.experience, 
               doctor_applications.consult_fee, doctor_applications.bio, doctor_applications.cv,
               users.id AS user_id, users.name, users.email
        FROM doctor_applications
        INNER JOIN users ON doctor_applications.user_id = users.id
        WHERE doctor_applications.status = 'reject' $search_sql
        ORDER BY doctor_applications.id DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("sii", $search_param, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "
        SELECT doctor_applications.id AS application_id, doctor_applications.specialty, doctor_applications.experience, 
               doctor_applications.consult_fee, doctor_applications.bio, doctor_applications.cv,
               users.id AS user_id, users.name, users.email
        FROM doctor_applications
        INNER JOIN users ON doctor_applications.user_id = users.id
        WHERE doctor_applications.status = 'reject'
        ORDER BY doctor_applications.id DESC
        LIMIT $limit OFFSET $offset
    ";
    $result = mysqli_query($conn, $query);
}

// Total pages
$totalPages = ceil($totalApplications / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rejected Doctor Applications - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        /* Your existing styles */
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
            background-color: #fceae9;
        }
        .btn-view {
            padding: 6px 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 14px;
            color: white;
            background-color: #3498db;
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

        /* Search form styling */
        .search-form {
            margin-bottom: 15px;
            display: flex;
            justify-content: flex-start;
            padding-left: 700px;
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
            <li><a href="View_Application.php"><i class="fas fa-user-md"></i> <span>Doctors Applications</span></a></li>
            <li><a href="view_users.php"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
            <li><a href="medications.php"><i class="fas fa-pills"></i> <span>Medications</span></a></li>
            <li><a href="approve_payments.php"><i class="fas fa-credit-card"></i> <span>Approve Payments</span></a></li>
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
            <h2><i class="fas fa-user-times"></i> Rejected Doctor Applications</h2>
            <p>View all rejected doctor applications in the system.</p>

            <!-- Search Form -->
            <form method="GET" action="" class="search-form">
                <input
                    type="text"
                    name="search"
                    class="search-input"
                    placeholder="Search by applicant name..."
                    value="<?= htmlspecialchars($search) ?>"
                />
                <button type="submit" class="search-button"><i class="fas fa-search"></i> Search</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Specialty</th>
                        <th>Experience</th>
                        <th>Consult Fee</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($application = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($application['application_id']) ?></td>
                            <td><?= htmlspecialchars($application['name']) ?></td>
                            <td><?= htmlspecialchars($application['email']) ?></td>
                            <td><?= htmlspecialchars($application['specialty']) ?></td>
                            <td><?= nl2br(htmlspecialchars($application['experience'])) ?></td>
                            <td>$<?= htmlspecialchars($application['consult_fee']) ?></td>
                            <td>
                                <a href="view_doctor_application.php?id=<?= $application['application_id'] ?>" class="btn-view">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No rejected applications found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php
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

        <footer style="text-align:center; padding: 15px; color: #666; font-size: 14px;">
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
