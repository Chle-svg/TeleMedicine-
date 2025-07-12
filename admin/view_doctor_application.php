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

// Validate and get application id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: View_Application.php");
    exit;
}
$application_id = (int)$_GET['id'];

// Fetch application data
$appQuery = mysqli_query($conn, "SELECT * FROM doctor_applications WHERE id = $application_id");
$application = mysqli_fetch_assoc($appQuery);

if (!$application) {
    echo "<p>Application not found.</p>";
    exit;
}

$user_id = $application['user_id'];
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($userQuery);

// Handle form post (accept/reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept'])) {
        $checkExisting = mysqli_query($conn, "SELECT * FROM doctor WHERE user_id = $user_id");
        if (mysqli_num_rows($checkExisting) === 0) {
            mysqli_query($conn, "INSERT INTO doctor (user_id, specialty, experience, consult_fee, bio, cv, created_at)
                VALUES ('$user_id', '{$application['specialty']}', '{$application['experience']}', '{$application['consult_fee']}', '{$application['bio']}', '{$application['cv']}', NOW())");
            mysqli_query($conn, "UPDATE users SET role = 'doctor' WHERE id = $user_id");
        }
        mysqli_query($conn, "UPDATE doctor_applications SET status = 'approve' WHERE id = $application_id");

        // Add notification for acceptance
        $message = "Congratulations! Your doctor application has been accepted so after this you can logout and login back and you get you docotr dashboard.";
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, created_at, is_read) VALUES (?, ?, NOW(), 0)");
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();
        $stmt->close();

        header("Location: View_Application.php");
        exit;
    } elseif (isset($_POST['reject'])) {
        mysqli_query($conn, "UPDATE doctor_applications SET status = 'reject' WHERE id = $application_id");

        // Add notification for rejection
        $message = "We regret to inform you that your doctor application has been rejected.";
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, created_at, is_read) VALUES (?, ?, NOW(), 0)");
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();
        $stmt->close();

        header("Location: View_Application.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Application Details - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
        }

        .admin-container {
            display: flex;
        }

        .sidebar {
            width: 300px;
            background-color: #2c3e50;
            color: white;
            transition: width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar .logo {
            font-size: 22px;
            text-align: center;
            padding: 15px;
            font-weight: bold;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 15px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #34495e;
        }

        .sidebar ul li a i {
            margin-right: 10px;
        }

        .sidebar.collapsed .logo span,
        .sidebar.collapsed ul li a span,
        .sidebar.collapsed .dropdown-container {
            display: none;
        }

         .sidebar-toggle {
            user-select: none;
            cursor: pointer;
            color: #3498db;
            padding: 10px 20px;
            font-size: 1.5em;
        }

        
        .dropdown-container {
            background-color: #34495e;
            display: none;
            padding-left: 20px;
        }

        .dropdown-container a {
            padding-left: 35px;
            display: block;
        }

        .dropdown-btn i.fas.fa-caret-down {
            margin-left: auto;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 15px 25px;
            background-color: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }

        .admin-header .admin-profile {
            text-align: right;
            font-weight: bold;
        }

        .content-area {
            padding: 20px;
            flex-grow: 1;
        }

        .application-details {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: auto;
        }

        .application-details h2 {
            margin-bottom: 25px;
            color: #333;
        }

        .application-details p {
            margin-bottom: 12px;
            font-size: 16px;
        }

        .application-details p strong {
            width: 150px;
            display: inline-block;
            color: #444;
        }

        .btn-action {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            margin-right: 10px;
            cursor: pointer;
        }

        .btn-accept {
            background-color: #2ecc71;
            color: #fff;
        }

        .btn-reject {
            background-color: #e74c3c;
            color: #fff;
        }

        .cv-link {
            color: #2980b9;
            text-decoration: underline;
        }

        .cv-link:hover {
            text-decoration: none;
        }

        a.back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        a.back-link:hover {
            text-decoration: underline;
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

        <div class="sidebar-toggle" title="Shrink/Expand Sidebar">
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
            <div class="application-details">
                <h2><i class="fas fa-user-md"></i> Doctor Application Details</h2>

                <p><strong>Name:</strong> <?= htmlspecialchars($application['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($application['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($application['phone']) ?></p>
                <p><strong>Specialty:</strong> <?= htmlspecialchars($application['specialty']) ?></p>
                <p><strong>Experience:</strong> <?= nl2br(htmlspecialchars($application['experience'])) ?></p>
                <p><strong>Consult Fee:</strong> $<?= htmlspecialchars($application['consult_fee']) ?></p>
                <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($application['bio'])) ?></p>
                <p><strong>CV:</strong> <a class="cv-link" href="../uploads/<?= htmlspecialchars($application['cv']) ?>" target="_blank">View/Download CV</a></p>
                <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($application['status'])) ?></p>

                <?php if ($application['status'] === 'pending'): ?>
                    <form method="post" style="margin-top: 20px;" onsubmit="return confirmAction(event)">
                        <button type="submit" name="accept" class="btn-action btn-accept">Accept</button>
                        <button type="submit" name="reject" class="btn-action btn-reject">Reject</button>
                    </form>
                <?php else: ?>
                    <p><em>This application has been <?= htmlspecialchars($application['status']) ?>.</em></p>
                <?php endif; ?>

                <a href="View_Application.php" class="back-link">← Back to Applications</a>
            </div>
        </div>

        <footer>
            &copy; <?= date("Y") ?> TeleMedicine Admin Panel.
        </footer>
    </div>
</div>

<script>
// Shrink/Expand sidebar
document.querySelector('.sidebar-toggle').addEventListener('click', () => {
    document.querySelector('.sidebar').classList.toggle('collapsed');
});

// Dropdown toggle
document.querySelectorAll('.dropdown-btn').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const dropdown = button.nextElementSibling;
        const isVisible = dropdown.style.display === 'block';

        // Close all others
        document.querySelectorAll('.dropdown-container').forEach(dc => dc.style.display = 'none');
        document.querySelectorAll('.dropdown-btn').forEach(btn => btn.classList.remove('active'));

        if (!isVisible) {
            dropdown.style.display = 'block';
            button.classList.add('active');
        }
    });
});

// Confirm accept/reject
function confirmAction(event) {
    const isAccept = event.submitter.name === 'accept';
    return confirm(`Are you sure you want to ${isAccept ? 'accept' : 'reject'} this application?`);
}
</script>

</body>
</html>
