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

// Handle search
$search = $_GET['search'] ?? '';
$searchSql = $search ? "WHERE name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'" : "";

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Count total
$totalRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM medications $searchSql");
$totalMedications = ($row = mysqli_fetch_assoc($totalRes)) ? $row['total'] : 0;
$totalPages = ceil($totalMedications / $limit);

// Fetch meds
$medications = mysqli_query($conn, "SELECT * FROM medications $searchSql ORDER BY id DESC LIMIT $limit OFFSET $offset");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Medications - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
        .content-area {
            padding: 20px;
        }
        .content-area h2 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .content-area h2 a {
            font-size: 14px;
            background: #3498db;
            color: #fff;
            padding: 8px 14px;
            border-radius: 4px;
            text-decoration: none;
        }
        .content-area h2 a:hover {
            background: #2980b9;
        }

        .search-bar {
            margin: 10px 0 20px;
            text-align: right;
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
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
        }
        table th, table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ccc;
            text-align: left;
            vertical-align: middle;
        }
        table th {
            background-color: #3498db;
            color: #fff;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .pagination a {
            text-decoration: none;
            padding: 6px 12px;
            background: #3498db;
            color: #fff;
            border-radius: 3px;
        }
        .pagination a:hover {
            background: #2980b9;
        }
        .pagination a.active {
            background: #2c3e50;
            font-weight: bold;
        }

        .action-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 6px 10px;
            margin: 0 2px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .action-btn:hover {
            background: #2980b9;
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
            <h2>
                <span><i class="fas fa-pills"></i> All Medications</span>
                <a href="post_medications.php"><i class="fas fa-plus-circle"></i> Post Medication</a>
            </h2>

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
                        <th>Name</th>
                        <th>Type</th>
                        <th>Dosage</th>
                        <th>Instructions</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($medications) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($medications)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['type']) ?></td>
                                <td><?= htmlspecialchars($row['dosage']) ?></td>
                                <td><?= htmlspecialchars($row['instructions']) ?></td>
                                <td>
                                    <a href="view_medication.php?id=<?= $row['id'] ?>" class="action-btn" title="View">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center;">No medications found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">← Prev</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next →</a>
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
