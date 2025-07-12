<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';
$user_id = $_SESSION['user_id'];

$sql = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, u.name AS doctor_name
        FROM appointments a
        JOIN users u ON a.doctor_id = u.id
        WHERE a.user_id = ?
        ORDER BY a.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments - TeleMedicine</title>
    <link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #f1f1f1;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 950px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 25px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #007bff;
            color: white;
        }
        .btn-view {
            padding: 8px 16px;
            background: #17a2b8;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-view:hover {
            background: #138496;
        }
        .status {
            font-weight: bold;
             text-color: black;
            text-transform: capitalize;
        }
        .status.accepted { color: green; }
        .status.rejected { color: red; }
        .status.pending { color: orange; }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="container">
    <h2>My Appointment Requests</h2>

    <table>
        <thead>
            <tr>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                        <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                        <td><?= htmlspecialchars($row['appointment_time']) ?></td>
                        <td class="status <?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
                        <td><a class="btn-view" href="view_user_appointment.php?id=<?= $row['id'] ?>">View</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">No appointments found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>
