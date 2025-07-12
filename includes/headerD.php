<?php

include '../includes/db.php';  // Adjust path to your db.php file if needed

$userId = $_SESSION['user_id'] ?? 0;
$name = "User";
$photo = "default.jpg";
$notifCount = 0;
$notifications = [];

if ($userId) {
    // Fetch user info
    $query = mysqli_query($conn, "SELECT name, photo FROM users WHERE id='$userId'");
    if ($query && mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        $name = htmlspecialchars($user['name'] ?? "User");
        $photo = htmlspecialchars($user['photo'] ?? "default.jpg");
    }

    // Fetch unread notifications (last 10)
    $notifQuery = mysqli_query($conn, "SELECT id, message, created_at, is_read FROM notifications WHERE user_id = $userId ORDER BY created_at DESC LIMIT 10");
    if ($notifQuery) {
        $notifications = mysqli_fetch_all($notifQuery, MYSQLI_ASSOC);
        // Count unread
        $notifCount = count(array_filter($notifications, fn($n) => $n['is_read'] == 0));
    }
}

function safe_notification_html($html) {
    return strip_tags($html, '<strong>');
}
?>

<header class="main-header" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 30px; color: white; background-color: #007bff; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: relative;">
    <button id="toggleSidebar" style="font-size: 20px; background: none; border: none; color: white; cursor: pointer;">
        <!-- You can add icon here -->
    </button>

    <div class="user-section" style="display: flex; align-items: center; gap: 20px; position: relative;">
        <!-- Notification Icon -->
        <div class="notification-icon" id="notifBell" style="position: relative; cursor: pointer; font-size: 24px; color: #f39c12;">
            <i class="fas fa-bell"></i>
            <?php if ($notifCount > 0): ?>
                <span id="notifBadge" style="
                    position: absolute;
                    top: -6px;
                    right: -8px;
                    background: #e74c3c;
                    color: white;
                    border-radius: 50%;
                    font-size: 13px;
                    padding: 3px 7px;
                    font-weight: bold;
                    box-shadow: 0 0 6px rgba(0,0,0,0.3);
                    min-width: 22px;
                    text-align: center;
                ">
                    <?php echo $notifCount; ?>
                </span>
            <?php endif; ?>

            <!-- Notification Dropdown -->
            <div id="notifDropdown" style="
                display: none;
                position: absolute;
                right: 0;
                top: 36px;
                background: #ffffff;
                color: #2c3e50;
                width: 320px;
                max-height: 360px;
                overflow-y: auto;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(44, 62, 80, 0.15);
                z-index: 1000;
                font-size: 14px;
                font-family: 'Poppins', sans-serif;
            ">
                <?php if (count($notifications) === 0): ?>
                    <div style="padding: 20px; text-align: center; color: #999; font-style: italic;">No notifications</div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): ?>
                        <?php $isUnread = ($notif['is_read'] == 0); ?>
                        <div style="
                            padding: 15px 20px;
                            border-bottom: 1px solid #eee;
                            background-color: <?= $isUnread ? '#fefefe' : '#f5f5f5' ?>;
                            transition: background-color 0.2s ease;
                            cursor: default;
                        "
                        onmouseover="this.style.backgroundColor='#d4edda';"
                        onmouseout="this.style.backgroundColor='<?= $isUnread ? '#fefefe' : '#f5f5f5' ?>';">
                            <div style="line-height: 1.3; color: #34495e;" 
                                 class="notif-message">
                                <?= safe_notification_html($notif['message']); ?>
                            </div>
                            <small style="color: #bbb; display: block; margin-top: 5px; font-size: 12px;">
                                <?= date('M d, Y H:i', strtotime($notif['created_at'])); ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- User photo -->
        <img src="../uploads/profile_photos/<?php echo $photo; ?>" alt="User Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid white;">

        <!-- Username -->
        <span style="font-weight: 600; font-size: 16px; color: white;"><?php echo $name; ?></span>

        <!-- Logout form -->
        <form action="../auth/logout.php" method="POST" style="margin: 0;">
            <button type="submit" style="background: transparent; border: 1px solid white; color: white; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                Logout
            </button>
        </form>
    </div>
</header>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Notification Dropdown JS -->
<script>
    const notifBell = document.getElementById('notifBell');
    const notifDropdown = document.getElementById('notifDropdown');
    let markedAsRead = false;

    notifBell.addEventListener('click', function (event) {
        event.stopPropagation();
        const isVisible = notifDropdown.style.display === 'block';
        notifDropdown.style.display = isVisible ? 'none' : 'block';

        if (!markedAsRead && !isVisible) {
            markedAsRead = true;

            fetch('<?php echo "/online_medication2/mark_notifications_read.php"; ?>', {
                method: 'POST'
            }).then(res => res.text())
              .then(data => {
                if (data.trim() === "success") {
                    const badge = document.getElementById('notifBadge');
                    if (badge) badge.style.display = 'none';
                }
            }).catch(err => console.error('Error marking notifications as read', err));
        }
    });

    document.addEventListener('click', () => {
        notifDropdown.style.display = 'none';
    });
</script>
