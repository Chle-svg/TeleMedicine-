<?php
session_start();
include('../includes/db.php');

$message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $name, $hashed_password, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            if ($role === 'admin') {
                // Set admin session and redirect
                $_SESSION['user_id'] = $id;
                $_SESSION['name'] = $name;
                $_SESSION['role'] = $role;

                header("Location: ../admin/dashboard.php");
                exit;
            } else {
                $message = "Access denied. Only admin users can login here.";
            }
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login | TeleMedicine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to bottom right, rgba(255,255,255,0.8), rgba(255,255,255,0.9)), url('../img/sss.jpg') center center/cover no-repeat;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .login-container {
        background: #fff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        width: 400px;
    }
    .login-container h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #0077cc;
    }
    .login-container input[type="email"],
    .login-container input[type="password"] {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
    }
    .login-container button {
        width: 100%;
        padding: 12px;
        background-color: #0077cc;
        color: #fff;
        font-size: 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s;
    }
    .login-container button:hover {
        background-color: #005fa3;
    }
    .message-box {
        background-color: #ffd2d2;
        color: #a10000;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ffaaaa;
        border-radius: 5px;
        text-align: center;
    }
</style>
</head>
<body>

<div class="login-container">
    <h2><i class="fas fa-sign-in-alt"></i> Admin Login</h2>

    <?php if (!empty($message)): ?>
        <div class="message-box"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <input type="email" name="email" placeholder="Admin Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
