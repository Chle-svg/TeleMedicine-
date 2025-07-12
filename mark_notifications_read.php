<?php
session_start();
include 'includes/db.php'; // adjust path if needed, e.g., '../includes/db.php'

header('Content-Type: text/plain');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "unauthorized";
    exit();
}

$userId = intval($_SESSION['user_id']);

$sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo "error";
    exit();
}

$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    echo "success";
} else {
    http_response_code(500);
    echo "error";
}

$stmt->close();
$conn->close();
