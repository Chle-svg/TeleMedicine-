<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Initialize $user with defaults to avoid undefined keys
$user = [
    'id' => 0,
    'name' => '',
    'email' => '',
    'age' => '',
    'city' => '',
    'phone' => '',
    'photo' => '',
    'password' => ''
];

// Fetch user data
$stmt = $conn->prepare("SELECT id, name, email, age, city, phone, photo, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows === 1) {
    $userData = $result->fetch_assoc();
    // Merge fetched data with defaults to ensure no undefined keys
    $user = array_merge($user, $userData);
} else {
    die("User not found.");
}

$success_profile = '';
$error_profile = '';
$success_password = '';
$error_password = '';
$success_photo = '';
$error_photo = '';

// Handle profile update form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? $user['name']);
    $email = trim($_POST['email'] ?? $user['email']);
    $age = isset($_POST['age']) && $_POST['age'] !== '' ? intval($_POST['age']) : $user['age'];
    $city = trim($_POST['city'] ?? $user['city']);
    $phone = trim($_POST['phone'] ?? $user['phone']);

    if ($phone !== '' && !preg_match('/^\d{10}$/', $phone)) {
        $error_profile = "Phone number must be exactly 10 digits.";
    }

    if (empty($error_profile) && $email && $email !== $user['email']) {
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkEmail->bind_param("si", $email, $user_id);
        $checkEmail->execute();
        $emailExists = $checkEmail->get_result()->num_rows > 0;
        if ($emailExists) {
            $error_profile = "Email already in use by another account.";
        }
    }

    if (empty($error_profile)) {
        $update = $conn->prepare("UPDATE users SET name = ?, email = ?, age = ?, city = ?, phone = ? WHERE id = ?");
        $update->bind_param("ssissi", $name, $email, $age, $city, $phone, $user_id);
        if ($update->execute()) {
            $success_profile = "Profile updated successfully.";
            // Update $user array to reflect changes in form immediately
            $user['name'] = $name;
            $user['email'] = $email;
            $user['age'] = $age;
            $user['city'] = $city;
            $user['phone'] = $phone;
        } else {
            $error_profile = "Failed to update profile.";
        }
    }
}

// Handle password reset form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $hashed_password = $user['password'];

    if (!password_verify($old_password, $hashed_password)) {
        $error_password = "Old password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error_password = "New password and confirmation do not match.";
    } elseif (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password)) {
        $error_password = "New password must be at least 8 characters long and include at least one uppercase letter.";
    } else {
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $updatePass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updatePass->bind_param("si", $new_hashed, $user_id);
        if ($updatePass->execute()) {
            $success_password = "Password changed successfully.";
        } else {
            $error_password = "Failed to update password.";
        }
    }
}

// Handle photo upload form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_photo'])) {
    if (!empty($_FILES['photo']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['photo']['type'];
        $fileTmp = $_FILES['photo']['tmp_name'];
        $fileName = basename($_FILES['photo']['name']);
        $uploadDir = '../uploads/profile_photos/'; // unified path
        
        if (!in_array($fileType, $allowedTypes)) {
            $error_photo = "Only JPG, PNG, and GIF files are allowed.";
        } else {
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = "profile_" . uniqid('', true) . "." . $ext;
            $uploadFilePath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $uploadFilePath)) {
                // Delete old photo if exists
                if (!empty($user['photo']) && file_exists($uploadDir . $user['photo'])) {
                    unlink($uploadDir . $user['photo']);
                }
                $updatePhoto = $conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
                $updatePhoto->bind_param("si", $newFileName, $user_id);
                if ($updatePhoto->execute()) {
                    $success_photo = "Photo updated successfully.";
                    $user['photo'] = $newFileName;
                } else {
                    $error_photo = "Failed to update photo in database.";
                    unlink($uploadFilePath);
                }
            } else {
                $error_photo = "Failed to upload photo file.";
            }
        }
    } else {
        $error_photo = "Please select a photo to upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Profile - TeleMedicine</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" />
<style>
/* CSS same as you have */
html, body {
  height: 100%;
  margin: 0;
  font-family: Arial, sans-serif;
}
body {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
header, footer {
  flex-shrink: 0;
}
.content-wrapper {
  flex-grow: 1;
  padding: 20px;
  padding-left:100px;
  max-width: 1200px;
  margin: 0 auto;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 140px);
  overflow-y: auto;
}
.back-btn {
  display: inline-block;
  margin-bottom: 20px;
  text-decoration: none;
  background: #6c757d;
  color: white;
  max-width: 70px;
  padding: 8px 14px;
  border-radius: 6px;
  font-weight: 600;
}
.back-btn i {
  margin-right: 6px;
}
h2 {
  color: #198754;
  margin-bottom: 24px;
  border-bottom: 2px solid #198754;
  padding-bottom: 10px;
}
.cards-wrapper {
  display: flex;
  gap: 20px;
  flex-wrap: nowrap;
  flex-grow: 1;
  overflow-y: auto;
}
.card {
  background: #fff;
  padding: 25px 30px;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  flex: 1 1 0;
  box-sizing: border-box;
  overflow-y: auto;
  max-height: 600px;
}
form label {
  font-weight: bold;
  margin-top: 15px;
  display: block;
}
form input[type="text"],
form input[type="email"],
form input[type="number"],
form input[type="password"],
form input[type="file"] {
  width: 100%;
  padding: 10px;
  margin-top: 6px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  box-sizing: border-box;
}
.btn {
  margin-top: 20px;
  background-color: #198754;
  color: white;
  padding: 12px 18px;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
}
.btn:hover {
  background-color: #14532d;
}
p.message.success {
  color: green;
  font-weight: bold;
  margin-top: 12px;
}
p.message.error {
  color: red;
  font-weight: bold;
  margin-top: 12px;
}
small {
  display: block;
  margin-top: 5px;
  font-size: 0.9rem;
  color: #666;
}
.current-photo {
  max-width: 150px;
  max-height: 150px;
  border-radius: 8px;
  margin-bottom: 15px;
  display: block;
  object-fit: cover;
}
.card-photo {
  flex: 1 1 0;
}
@media (max-width: 900px) {
  .cards-wrapper {
    flex-wrap: wrap;
    flex-direction: column;
  }
  .card {
    max-height: none;
    flex-basis: auto;
    margin-bottom: 20px;
  }
}
</style>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>
<?php include '../includes/header.php'; ?>

<div class="content-wrapper">

  <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>

  <div class="cards-wrapper">
    <!-- Profile Edit Card -->
    <div class="card">
      <h3>Update Profile</h3>
      <?php if ($success_profile): ?>
        <p class="message success"><?= htmlspecialchars($success_profile) ?></p>
      <?php endif; ?>
      <?php if ($error_profile): ?>
        <p class="message error"><?= htmlspecialchars($error_profile) ?></p>
      <?php endif; ?>
      <form method="post" novalidate id="profileForm">
        <input type="hidden" name="update_profile" value="1" />
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" />

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" min="0" value="<?= htmlspecialchars($user['age'] ?? '') ?>" />

        <label for="city">City:</label>
        <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" />

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" maxlength="10" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" />

        <small>Phone must be exactly 10 digits.</small>

        <button type="submit" class="btn">Save Profile</button>
      </form>
    </div>

    <!-- Password Change Card -->
    <div class="card">
      <h3>Reset Password</h3>
      <?php if ($success_password): ?>
        <p class="message success"><?= htmlspecialchars($success_password) ?></p>
      <?php endif; ?>
      <?php if ($error_password): ?>
        <p class="message error"><?= htmlspecialchars($error_password) ?></p>
      <?php endif; ?>
      <form method="post" novalidate id="passwordForm">
        <input type="hidden" name="change_password" value="1" />
        <label for="old_password">Old Password:</label>
        <input type="password" id="old_password" name="old_password" required />

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required />

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required />

        <small>Password must be at least 8 characters and include 1 uppercase letter.</small>

        <button type="submit" class="btn">Change Password</button>
      </form>
    </div>

    <!-- Photo Change Card -->
    <div class="card card-photo">
      <h3>Change Profile Photo</h3>
      <?php if ($success_photo): ?>
        <p class="message success"><?= htmlspecialchars($success_photo) ?></p>
      <?php endif; ?>
      <?php if ($error_photo): ?>
        <p class="message error"><?= htmlspecialchars($error_photo) ?></p>
      <?php endif; ?>
      <?php if (!empty($user['photo']) && file_exists('../uploads/profile_photos/' . $user['photo'])): ?>
        <img src="../uploads/profile_photos/<?= htmlspecialchars($user['photo']) ?>" alt="Profile Photo" class="current-photo" />
      <?php else: ?>
        <p>No profile photo uploaded.</p>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data" id="photoForm">
        <input type="hidden" name="change_photo" value="1" />
        <label for="photo">Select a new photo:</label>
        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif" required />
        <small>Allowed file types: JPG, PNG, GIF</small>

        <button type="submit" class="btn">Upload Photo</button>
      </form>
    </div>
  </div>
</div>

<?php include '../includes/footerD.php'; ?>

<script>
  // Client-side validation for profile form
  document.getElementById('profileForm').addEventListener('submit', function(e) {
    const phone = document.getElementById('phone').value.trim();
    if (phone !== '' && !/^\d{10}$/.test(phone)) {
      alert('Phone number must be exactly 10 digits.');
      e.preventDefault();
      return false;
    }
  });

  // Client-side validation for password form
  document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;
    const regex = /^(?=.*[A-Z]).{8,}$/;

    if (!regex.test(newPass)) {
      alert('New password must be at least 8 characters long and contain at least one uppercase letter.');
      e.preventDefault();
      return false;
    }
    if (newPass !== confirmPass) {
      alert('New password and confirmation do not match.');
      e.preventDefault();
      return false;
    }
  });
</script>

</body>
</html>
