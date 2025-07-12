<?php
include '../includes/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = $conn->prepare("SELECT dosage, instruction FROM medications WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();
    echo json_encode($result);
}
?>
