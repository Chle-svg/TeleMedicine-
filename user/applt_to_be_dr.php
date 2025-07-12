<?php
session_start();
include '../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Fetch user info
$userSql = "SELECT name, email, phone, age, city FROM users WHERE id = ?";
$userStmt = mysqli_prepare($conn, $userSql);
mysqli_stmt_bind_param($userStmt, "i", $user_id);
mysqli_stmt_execute($userStmt);
$userResult = mysqli_stmt_get_result($userStmt);
$user = mysqli_fetch_assoc($userResult);
mysqli_stmt_close($userStmt);

if (!$user) {
    die("User not found.");
}

$name = $user['name'];
$email = $user['email'];
$phone = $user['phone'];
$age = (int)$user['age'];
$city = $user['city'];

// Check for existing pending application
$check = mysqli_query($conn, "SELECT * FROM doctor_applications WHERE user_id = $user_id AND status = 'pending'");
$hasPending = mysqli_num_rows($check) > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$hasPending) {
    // Sanitize and validate inputs
    $specialty = $_POST['specialty'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $fee = $_POST['consult_fee'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $available_time = $_POST['available_time'] ?? '';

    // Basic validation
    if (empty($specialty) || !is_numeric($experience) || !is_numeric($fee) || empty($bio) || empty($available_time)) {
        $msg = "Please fill in all fields correctly.";
    } else {
        $experience = (int)$experience;
        $fee = (float)$fee;

        // Handle CV upload
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $cv_name = $_FILES['cv']['name'];
            $cv_tmp = $_FILES['cv']['tmp_name'];
            $cv_ext = strtolower(pathinfo($cv_name, PATHINFO_EXTENSION));
            $cv_new_name = 'CV_' . time() . '_' . rand(1000, 9999) . '.' . $cv_ext;
            $cv_path = "../uploads/" . $cv_new_name;

            if ($cv_ext !== 'pdf') {
                $msg = "Please upload only PDF files for your CV.";
            } elseif (move_uploaded_file($cv_tmp, $cv_path)) {
                // Insert into doctor_applications table
                $sql = "INSERT INTO doctor_applications (user_id, name, email, phone, age, city, specialty, experience, consult_fee, bio, available_time, cv, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
                $stmt = mysqli_prepare($conn, $sql);

                if ($stmt) {
                    mysqli_stmt_bind_param(
                        $stmt,
                        "isssissidsss",
                        $user_id,
                        $name,
                        $email,
                        $phone,
                        $age,
                        $city,
                        $specialty,
                        $experience,
                        $fee,
                        $bio,
                        $available_time,
                        $cv_new_name
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $msg = "Application submitted successfully.";
                    } else {
                        $msg = "Failed to submit your application. Error: " . mysqli_stmt_error($stmt);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $msg = "Failed to prepare the statement. Error: " . mysqli_error($conn);
                }
            } else {
                $msg = "Failed to upload CV. Try again.";
            }
        } else {
            $msg = "Please upload your CV.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Apply for Doctor - TeleMedicine</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="JS/sidebar.js"></script>
    <style>
        html, body { height: 100%; margin: 0; font-family: Arial, sans-serif; }
        body { display: flex; flex-direction: column; min-height: 100vh; }

        .section {
            padding: 10px;
            background: #f4f6f9;
            border-radius: 10px;
            max-width: 800px;
            margin: 40px auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .section h2 {
            text-align: center;
            font-size: 28px;
            color: #333;
            margin-bottom: 0px;
        }

        .form-box {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-box label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #444;
        }

        .form-box input,
        .form-box select,
        .form-box textarea {
            padding: 12px 20px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #fff;
            transition: border 0.3s, box-shadow 0.3s;
            width: 100%;
        }

        .form-box input:focus,
        .form-box select:focus,
        .form-box textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.2);
            outline: none;
        }

        .form-box textarea {
            min-height: 120px;
            resize: vertical;
        }

        .flex-row {
            display: flex;
            gap: 100px;
            flex-wrap: wrap;
        }

        .flex-row > div {
            flex: 1;
            min-width: 180px;
        }

        .form-box button {
            background: #007bff;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-box button:hover {
            background: #0056b3;
        }

        .success, .error {
            padding: 12px;
            border-radius: 6px;
            font-weight: 500;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>
<?php include '../includes/header.php'; ?>

<div class="dashboard-container">
    <section class="section">
        <h2>Apply to be a Doctor</h2>

        <?php if (!empty($msg)): ?>
            <div class="<?= strpos($msg, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <?php if ($hasPending): ?>
            <div class="error">You already have a pending application.</div>
        <?php else: ?>
        <form method="POST" enctype="multipart/form-data" class="form-box" novalidate>
            <label for="specialty">Specialty</label>
            <select name="specialty" id="specialty" required>
                <option value="" disabled selected>Select your specialty</option>
                <option value="Cardiologist">Cardiologist</option>
                <option value="Dermatologist">Dermatologist</option>
                <option value="Neurologist">Neurologist</option>
                <option value="Pediatrician">Pediatrician</option>
                <option value="Psychiatrist">Psychiatrist</option>
                <option value="General Practitioner">General Practitioner</option>
            </select>

            <div class="flex-row">
                <div>
                    <label for="experience">Years of Experience</label>
                    <input type="number" name="experience" id="experience" required min="0">
                </div>
                <div>
                    <label for="consult_fee">Consultation Fee (ETB)</label>
                    <input type="number" name="consult_fee" id="consult_fee" required step="0.01" min="0">
                </div>
            </div>

            <label for="bio">Short Bio</label>
            <textarea name="bio" id="bio" placeholder="Tell us about yourself..." required></textarea>

            <label for="available_time">Available Time</label>
            <input type="text" name="available_time" id="available_time" placeholder="e.g., Mon-Fri, 9AM - 5PM" required>

            <label for="cv">Upload CV (PDF only)</label>
            <input type="file" name="cv" id="cv" accept="application/pdf" required>

            <button type="submit">Submit Application</button>
        </form>
        <?php endif; ?>
    </section>

    <?php include '../includes/footer.php'; ?>
</div>

</body>
</html>
