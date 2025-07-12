<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Admin info
$adminId = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT name FROM users WHERE id = $adminId");
$username = ($row = mysqli_fetch_assoc($result)) ? $row['name'] : "Admin";

// Handle form
$success = $error = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim inputs
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $dosage = trim($_POST['dosage'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $manufacturer = trim($_POST['manufacturer'] ?? '');
    $expiration_date = trim($_POST['expiration_date'] ?? '');
    $side_effects = trim($_POST['side_effects'] ?? '');
    $storage = trim($_POST['storage'] ?? '');

    // Validation
    if ($name === '') {
        $errors[] = "Medication name is required.";
    } elseif (strlen($name) > 255) {
        $errors[] = "Medication name cannot exceed 255 characters.";
    }

    if ($type === '') {
        $errors[] = "Medication type is required.";
    } elseif (strlen($type) > 100) {
        $errors[] = "Medication type cannot exceed 100 characters.";
    }

    if ($dosage === '') {
        $errors[] = "Dosage is required.";
    } elseif (strlen($dosage) > 100) {
        $errors[] = "Dosage cannot exceed 100 characters.";
    }

    if ($instructions === '') {
        $errors[] = "Instructions are required.";
    }

    if ($manufacturer === '') {
        $errors[] = "Manufacturer is required.";
    } elseif (strlen($manufacturer) > 255) {
        $errors[] = "Manufacturer cannot exceed 255 characters.";
    }

    if ($expiration_date === '') {
        $errors[] = "Expiration date is required.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiration_date)) {
        $errors[] = "Expiration date must be in YYYY-MM-DD format.";
    } else {
        $expDateTimestamp = strtotime($expiration_date);
        if (!$expDateTimestamp) {
            $errors[] = "Expiration date is invalid.";
        } elseif ($expDateTimestamp < strtotime(date('Y-m-d'))) {
            $errors[] = "Expiration date cannot be in the past.";
        }
    }

    if ($side_effects !== '' && strlen($side_effects) > 1000) {
        $errors[] = "Side effects description is too long.";
    }

    if ($storage !== '' && strlen($storage) > 500) {
        $errors[] = "Storage instructions are too long.";
    }

    if (empty($errors)) {
        // Check if medication with the same name already exists
        $safeName = mysqli_real_escape_string($conn, $name);
        $checkQuery = "SELECT id FROM medications WHERE name = '$safeName' LIMIT 1";
        $checkResult = mysqli_query($conn, $checkQuery);

        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            $error = "This medication is already posted. To edit it, please use the edit page.";
        } else {
            // Escape inputs for safe insertion
            $type = mysqli_real_escape_string($conn, $type);
            $dosage = mysqli_real_escape_string($conn, $dosage);
            $instructions = mysqli_real_escape_string($conn, $instructions);
            $manufacturer = mysqli_real_escape_string($conn, $manufacturer);
            $expiration_date = mysqli_real_escape_string($conn, $expiration_date);
            $side_effects = mysqli_real_escape_string($conn, $side_effects);
            $storage = mysqli_real_escape_string($conn, $storage);

            $insert = mysqli_query($conn, "INSERT INTO medications (name, type, dosage, instructions, manufacturer, expiration_date, side_effects, storage) 
                                        VALUES ('$safeName', '$type', '$dosage', '$instructions', '$manufacturer', '$expiration_date', '$side_effects', '$storage')");

            if ($insert) {
                $success = "Medication posted successfully!";
            } else {
                $error = "Failed to post medication: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Post Medication - Admin Panel</title>
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
            margin-bottom: 20px;
        }
        form {
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            border-radius: 4px;
            max-width: 800px;
            margin: 0 auto;
        }
        /* Grid layout for form */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 40px;
        }
        /* For full-width textareas, spanning both columns */
        .form-grid textarea.full-width {
            grid-column: 1 / -1;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 3px;
            resize: vertical;
        }
        /* Container for buttons side-by-side */
        .form-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .btn-back {
            background: #777;
            color: #fff;
            padding: 10px 15px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background: #555;
        }
        .success { color: green; margin-bottom: 15px; text-align: center; }
        .error { color: red; margin-bottom: 15px; text-align: center; }
        .error ul {
            list-style-type: disc;
            padding-left: 20px;
            text-align: left;
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
            <h2><i class="fas fa-pills"></i> Post New Medication</h2>

            <?php if (!empty($errors)): ?>
                <div class="error">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif ($success): ?>
                <p class="success"><?= htmlspecialchars($success) ?></p>
            <?php elseif ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post">
                <div class="form-grid">
                    <div>
                        <label for="name">Medication Name</label>
                        <input type="text" name="name" id="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="type">Type (e.g., Tablet, Syrup)</label>
                        <input type="text" name="type" id="type" required value="<?= htmlspecialchars($_POST['type'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="dosage">Dosage</label>
                        <input type="text" name="dosage" id="dosage" required value="<?= htmlspecialchars($_POST['dosage'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="manufacturer">Manufacturer</label>
                        <input type="text" name="manufacturer" id="manufacturer" required value="<?= htmlspecialchars($_POST['manufacturer'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="expiration_date">Expiration Date</label>
                        <input type="date" name="expiration_date" id="expiration_date" required value="<?= htmlspecialchars($_POST['expiration_date'] ?? '') ?>">
                    </div>

                    <div></div> <!-- empty cell for alignment -->

                    <div class="full-width">
                        <label for="instructions">Instructions</label>
                        <textarea name="instructions" id="instructions" rows="3" required class="full-width"><?= htmlspecialchars($_POST['instructions'] ?? '') ?></textarea>
                    </div>

                    <div class="full-width">
                        <label for="side_effects">Side Effects</label>
                        <textarea name="side_effects" id="side_effects" rows="3" class="full-width"><?= htmlspecialchars($_POST['side_effects'] ?? '') ?></textarea>
                    </div>

                    <div class="full-width">
                        <label for="storage">Storage Instructions</label>
                        <textarea name="storage" id="storage" rows="2" class="full-width"><?= htmlspecialchars($_POST['storage'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-buttons">
                    <a href="medications.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>

                    <button type="submit">Post Medication</button>
                </div>
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
