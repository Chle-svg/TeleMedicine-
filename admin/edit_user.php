<?php
include '../includes/db.php';
session_start();

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

// Get user ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_users.php");
    exit;
}
$user_id = (int)$_GET['id'];

// Fetch user data
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
if (mysqli_num_rows($userQuery) !== 1) {
    header("Location: view_users.php");
    exit;
}
$user = mysqli_fetch_assoc($userQuery);

// Fetch admin name for header
$adminQuery = mysqli_query($conn, "SELECT name FROM users WHERE id = $admin_id");
$adminData = mysqli_fetch_assoc($adminQuery);
$adminName = $adminData ? $adminData['name'] : 'Admin';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $age = isset($_POST['age']) ? (int)$_POST['age'] : null;
    $city = trim($_POST['city'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $status = $_POST['status'] ?? 'active';

    // Basic validation
    if (empty($name) || empty($email)) {
        $error = "Name and Email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email is unique (excluding current user)
        $emailCheck = mysqli_query($conn, "SELECT id FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "' AND id != $user_id");
        if (mysqli_num_rows($emailCheck) > 0) {
            $error = "Email is already in use by another user.";
        } else {
            // Prevent admin from removing their own admin role
            if ($user_id === $admin_id && $role !== 'admin') {
                $error = "You cannot remove your own admin role.";
            } else {
                // Update user
                $stmt = mysqli_prepare($conn, "UPDATE users SET name=?, email=?, age=?, city=?, phone=?, role=?, status=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "ssissssi", $name, $email, $age, $city, $phone, $role, $status, $user_id);
                if (mysqli_stmt_execute($stmt)) {
                    $success = "User updated successfully.";
                    // Refresh user data
                    $userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
                    $user = mysqli_fetch_assoc($userQuery);
                } else {
                    $error = "Failed to update user.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .content-area {
            padding: 20px;
            max-width: 600px;
            margin: auto;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="email"],
        form input[type="number"],
        form select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }
        .btn-submit {
            margin-top: 20px;
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-submit:hover {
            background-color: #2980b9;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            background-color: #7f8c8d;
            color: white;
            padding: 10px 20px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 16px;
            margin-right: 10px;
        }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 3px;
        }
        .error {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }
        .success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        .sidebar-toggle {
            user-select: none;
            cursor: pointer;
            color: #3498db;
            padding: 10px 20px;
            font-size: 1.5em;
            margin-bottom: 15px;
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
            <h2><i class="fas fa-edit"></i> Edit User</h2>

            <?php if ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="message success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label for="age">Age</label>
                <input type="number" id="age" name="age" min="0" value="<?= htmlspecialchars($user['age']) ?>">

                <label for="city">City</label>
                <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city']) ?>">

                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

                
                <?php if ($user_id === $admin_id): ?>
                    <input type="hidden" name="role" value="admin">
                <?php endif; ?>

                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>

                <button type="submit" class="btn-submit">Update User</button>
                <a href="view_users.php" class="btn-back">Cancel</a>
            </form>
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
