<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// Check user session
$userId = $_SESSION['user_id'] ?? null;
$user = null;
if ($userId) {
    $result = mysqli_query($conn, "SELECT name, photo FROM users WHERE id='$userId'");
    $user = mysqli_fetch_assoc($result);
}
$name = htmlspecialchars($user['name'] ?? "Guest");
$photo = htmlspecialchars($user['photo'] ?? "default.jpg");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TeleMedicine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* Header Styling */
    .topbar {
      background: linear-gradient(90deg, #1d3557, #457b9d);
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .topbar .logo {
      font-size: 1.5rem;
      font-weight: bold;
      letter-spacing: 1px;
    }
    .topbar .nav-links a {
      color: white;
      margin-left: 1.5rem;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    .topbar .nav-links a:hover {
      color: #f1faee;
    }
    .topbar .user-info {
      display: flex;
      align-items: center;
      gap: 0.7rem;
    }
    .topbar .user-info img {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #f1faee;
    }
  </style>
</head>
<body>
  <div class="topbar">
    <div class="logo">TeleMedicine</div>
    <div class="nav-links">
      <a href="/index.php">Home</a>
      <a href="/user/dashboard.php">Dashboard</a>
      <a href="/user/appointments.php">Appointments</a>
      <a href="/user/prescriptions.php">Prescriptions</a>
      <a href="/logout.php">Logout</a>
    </div>
    <div class="user-info">
      <img src="/uploads/<?php echo $photo; ?>" alt="User Photo">
      <span><?php echo $name; ?></span>
    </div>
  </div>
