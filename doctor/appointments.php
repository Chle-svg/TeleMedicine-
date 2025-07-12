<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$doctor_id = $_SESSION['user_id'];
$statuses = ['pending', 'accepted', 'rejected'];
$appointments = [];
$pages = [];

$per_page = 3; // Changed here to show 3 cards per page

// Fetch paginated appointments by status
foreach ($statuses as $status) {
    $page = isset($_GET["page_$status"]) ? max(1, intval($_GET["page_$status"])) : 1;
    $offset = ($page - 1) * $per_page;

    $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM appointments WHERE doctor_id = ? AND status = ?");
    $countStmt->bind_param("is", $doctor_id, $status);
    $countStmt->execute();
    $countRes = $countStmt->get_result()->fetch_assoc();
    $total = $countRes['total'];
    $pages[$status] = [
        'total' => $total,
        'current' => $page,
        'max' => max(1, ceil($total / $per_page))
    ];

    $stmt = $conn->prepare("SELECT a.id, u.name AS patient_name, a.appointment_date, a.appointment_time, a.appointment_type 
                            FROM appointments a
                            JOIN users u ON a.user_id = u.id
                            WHERE a.doctor_id = ? AND a.status = ?
                            ORDER BY a.appointment_date DESC, a.appointment_time DESC
                            LIMIT ? OFFSET ?");
    $stmt->bind_param("isii", $doctor_id, $status, $per_page, $offset);
    $stmt->execute();
    $appointments[$status] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Appointments - TeleMedicine</title>
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f0f4f8;
        margin: 0;
        padding: 0;
        color: #222;
    }
    .container {
        max-width: 1000px;
        margin: 60px auto;
        margin-left: 330px;
        background: #fff;
        border-radius: 14px;
        padding: 40px;
        box-shadow: 0 10px 15px rgba(0,0,0,0.05);
    }
    h2 {
        font-size: 28px;
        color: #198754;
        margin-bottom: 20px;
    }
    .tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
    }
    .tab-btn {
        flex: 1;
        padding: 12px;
        border: none;
        background-color: #e9f5ee;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s;
        color: #198754;
    }
    .tab-btn.active {
        background-color: #198754;
        color: white;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

    /* Card grid */
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* 3 cards per row */
        gap: 20px;
    }
    .appointment-card {
        background: #f8fafb;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }
    .appointment-card:hover {
        transform: translateY(-4px);
    }
    .appointment-card h3 {
        margin: 0 0 10px;
        color: #198754;
        font-size: 20px;
    }
    .appointment-card p {
        margin: 6px 0;
        color: #333;
        font-weight: 600;
    }
    .view-btn {
        display: inline-block;
        margin-top: 12px;
        background: #198754;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 700;
        transition: background-color 0.3s ease;
    }
    .view-btn:hover {
        background: #14532d;
    }
    /* Pagination */
    .pagination {
        margin-top: 25px;
        text-align: center;
    }
    .pagination a {
        margin: 0 8px;
        padding: 8px 16px;
        background-color: #198754;
        color: white;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        user-select: none;
        transition: background-color 0.3s ease;
    }
    .pagination a.disabled {
        background-color: #ccc;
        pointer-events: none;
    }

    /* Responsive */
    @media(max-width: 900px) {
        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media(max-width: 600px) {
        .cards-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<script>
    function openTab(evt, tabName) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

        document.getElementById(tabName).classList.add('active');
        if (evt) evt.currentTarget.classList.add('active');
        else document.querySelector(`.tab-btn[data-tab="${tabName}"]`).classList.add('active');
    }

    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab =
            urlParams.has('page_pending') ? 'pending' :
            urlParams.has('page_accepted') ? 'accepted' :
            urlParams.has('page_rejected') ? 'rejected' : 'pending';
        openTab(null, activeTab);
    });
</script>
</head>
<body>
<?php include '../includes/sidebarD.php'; ?>
<?php include '../includes/headerD.php'; ?>

<div class="container">
    <h2><i class="fa fa-calendar-check"></i> Manage Appointments</h2>

    <div class="tabs">
        <?php foreach ($statuses as $status): ?>
            <button class="tab-btn" data-tab="<?= $status ?>" onclick="openTab(event, '<?= $status ?>')">
                <?= ucfirst($status) ?> (<?= $pages[$status]['total'] ?>)
            </button>
        <?php endforeach; ?>
    </div>

    <?php foreach ($statuses as $status): ?>
        <div class="tab-content" id="<?= $status ?>">
            <?php if (empty($appointments[$status])): ?>
                <p>No <?= $status ?> appointments.</p>
            <?php else: ?>
                <div class="cards-grid">
                    <?php foreach ($appointments[$status] as $appt): ?>
                        <div class="appointment-card" tabindex="0">
                            <h3><?= htmlspecialchars($appt['patient_name']) ?></h3>
                            <p><strong>Date:</strong> <?= htmlspecialchars($appt['appointment_date']) ?></p>
                            <p><strong>Time:</strong> <?= htmlspecialchars($appt['appointment_time']) ?></p>
                            <p><strong>Type:</strong> <?= htmlspecialchars(ucwords(str_replace('_', ' ', $appt['appointment_type']))) ?></p>
                            <a href="view_appointment.php?id=<?= $appt['id'] ?>" class="view-btn" aria-label="View details for appointment with <?= htmlspecialchars($appt['patient_name']) ?>">View</a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="pagination" role="navigation" aria-label="<?= ucfirst($status) ?> Pagination">
                    <?php 
                    $cur = $pages[$status]['current'];
                    $max = $pages[$status]['max'];
                    ?>
                    <a href="?page_<?= $status ?>=<?= $cur - 1 ?>#<?= $status ?>" class="<?= ($cur <= 1) ? 'disabled' : '' ?>" aria-disabled="<?= ($cur <= 1) ? 'true' : 'false' ?>">&laquo; Prev</a>
                    <a href="?page_<?= $status ?>=<?= $cur + 1 ?>#<?= $status ?>" class="<?= ($cur >= $max) ? 'disabled' : '' ?>" aria-disabled="<?= ($cur >= $max) ? 'true' : 'false' ?>">Next &raquo;</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/footerD.php'; ?>
</body>
</html>
