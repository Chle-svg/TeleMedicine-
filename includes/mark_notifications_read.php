<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit();
}

$userId = $_SESSION['user_id'];

mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE user_id = $userId");

echo "success";
exit();
?>
