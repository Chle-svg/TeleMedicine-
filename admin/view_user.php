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

// Initialize error variable
$error = "";

// Handle deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = (int)$_POST['delete_user_id'];

    // Prevent deleting self
    if ($delete_user_id === $admin_id) {
        $error = "You cannot delete your own account.";
    } else {
        $deleteQuery = mysqli_query($conn, "DELETE FROM users WHERE id = $delete_user_id");
        if ($deleteQuery) {
            header("Location: view_users.php?msg=User+deleted+successfully");
            exit;
        } else {
            $error = "Failed to delete user.";
        }
    }
} else {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: view_users.php");
        exit;
    }
    $user_id = (int)$_GET['id'];

    $userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
    if (mysqli_num_rows($userQuery) !== 1) {
        header("Location: view_users.php");
        exit;
    }
    $user = mysqli_fetch_assoc($userQuery);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View User - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .content-area {
            padding: 20px;
        }
        .content-area h2 {
            margin-bottom: 20px;
        }
        .user-details {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .user-details p {
            margin: 10px 0;
            font-size: 16px;
        }
        .btn-edit, .btn-back, .btn-delete {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 15px;
            border-radius: 3px;
            color: white;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }
        .btn-back {
            background-color: #3498db;
            margin-right: 10px;
        }
        .btn-edit {
            background-color: #e67e22;
            margin-right: 10px;
        }
        .btn-delete {
            background-color: #e74c3c;
        }
        .btn-delete:hover {
            background-color: #c0392b;
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
        .error-message {
            background: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            max-width: 400px;
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
            <h2><i class="fas fa-user"></i> User Details</h2>

            <?php if (!empty($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                <div class="user-details">
                    <p><strong>ID:</strong> <?= htmlspecialchars($user['id']) ?></p>
                    <p><strong>Username:</strong> <?= htmlspecialchars($user['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Age:</strong> <?= htmlspecialchars($user['age']) ?></p>
                    <p><strong>Gender:</strong> <?= htmlspecialchars(ucfirst($user['gender'])) ?></p>
                    <p><strong>City:</strong> <?= htmlspecialchars($user['city']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                    <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($user['status']) ?></p>
                </div>

                <a href="view_users.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
                <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>

                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                    <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                    <button type="submit" class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
                </form>
            <?php endif; ?>
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

history.pushState(null, null, location.href);
window.onpopstate = function () {
    location.href = 'dashboard.php';
};

<?php if (isset($_GET['msg'])): ?>
    alert("<?= htmlspecialchars($_GET['msg']) ?>");
<?php endif; ?>
</script>

</body>
</html>
