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

// Fetch admin's profile data
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $admin_id");
$admin = mysqli_fetch_assoc($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $city = htmlspecialchars($_POST['city']);

    $update = mysqli_query($conn, "UPDATE users SET name='$name', email='$email', phone='$phone', city='$city' WHERE id=$admin_id");

    if ($update) {
        $success = "Profile updated successfully!";
        // Refresh admin data
        $adminQuery = mysqli_query($conn, "SELECT name FROM users WHERE id = $admin_id");
        $adminData = mysqli_fetch_assoc($adminQuery);
        $adminName = $adminData ? $adminData['name'] : 'Admin';
    } else {
        $error = "Error updating profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        /* Improved content area and form styling */
        .content-area {
            padding: 40px 20px;
            padding-bottom: 10px;
            padding-top: 10px;
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .content-area h2 {
            margin-bottom: 25px;
            font-weight: 700;
            font-size: 1.9rem;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .content-area p {
            color: #555;
            margin-bottom: 30px;
            font-size: 1rem;
            line-height: 1.5;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 400;
            color: #34495e;
            font-size: 1rem;
            letter-spacing: 0.02em;
        }
        form input[type="text"],
        form input[type="email"] {
            width: 90%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1.8px solid #bdc3c7;
            border-radius: 6px;
            font-size: 1rem;
            color: #2c3e50;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            outline: none;
            font-family: inherit;
        }
        form input[type="text"]:focus,
        form input[type="email"]:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.5);
        }
        form button {
            display: inline-block;
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            letter-spacing: 0.05em;
            transition: background-color 0.3s ease, transform 0.2s ease;
            user-select: none;
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.4);
        }
        form button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(41, 128, 185, 0.6);
        }
        .success, .error {
            font-weight: 600;
            font-size: 1rem;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1.5px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1.5px solid #f5c6cb;
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
        /* Dropdown styles */
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
                <a href="#" class="dropdown-btn active">
                    <i class="fas fa-cog"></i> <span>Settings</span>
                    <i class="fas fa-caret-down"></i>
                </a>
                <div class="dropdown-container" style="display: block;">
                    <a href="edit_profile.php" class="active"><i class="fas fa-user-edit"></i> Edit Profile</a>
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
            <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
            <p>Update your profile information below.</p>

            <?php if (isset($success)): ?>
                <p class="success"><?= $success ?></p>
            <?php elseif (isset($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <form method="post" novalidate>
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($admin['name']) ?>" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($admin['email']) ?>" required>

                <label for="phone">Phone</label>
                <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($admin['phone']) ?>">

                <label for="city">City</label>
                <input type="text" name="city" id="city" value="<?= htmlspecialchars($admin['city']) ?>">

                <button type="submit">Update Profile</button>
            </form>
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
        if (dropdownContent.style.display === 'block') {
            dropdownContent.style.display = 'none';
        } else {
            dropdownContent.style.display = 'block';
        }
    });
});
</script>

</body>
</html>
