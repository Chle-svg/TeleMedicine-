<?php
// join_meeting.php
$room = $_GET['room'] ?? '';

if (!$room) {
    die('No room specified.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Join Video Meeting - <?= htmlspecialchars($room) ?></title>
    <style>
      html, body {
          margin: 0; padding: 0; height: 100%;
          overflow: hidden;
      }
      #jitsi-container {
          height: 100vh;
          width: 100vw;
      }
    </style>
</head>
<body>
<div id="jitsi-container"></div>

<script src='https://meet.jit.si/external_api.js'></script>
<script>
    const domain = 'meet.jit.si';
    const options = {
        roomName: '<?= htmlspecialchars($room) ?>',
        parentNode: document.querySelector('#jitsi-container'),
        width: '100%',
        height: '100%',
    };
    const api = new JitsiMeetExternalAPI(domain, options);
</script>
</body>
</html>
