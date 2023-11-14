<?php
    //database connection 
    include("./database/connection.php");

    $conn = sqlsrv_connect($serverName, $connectionInfo);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $tsql = "SELECT COUNT(*) AS sms_received_id FROM sms_received WHERE read_status = 0";
    $stmt_count = sqlsrv_query($conn, $tsql);

    if ($stmt_count === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_fetch($stmt_count)) {
        $unreadCount = sqlsrv_get_field($stmt_count, 0);
    } else {
        $unreadCount = 0;
    }

    $inbox = "SELECT COUNT(*) AS sms_received_id FROM sms_received";
    $inbox_count = sqlsrv_query($conn, $inbox);

    if ($inbox_count === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_fetch($inbox_count)) {
        $allmsg = sqlsrv_get_field($inbox_count, 0);
    } else {
        $allmsg = 0;
    }

    sqlsrv_free_stmt($stmt_count);
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="300"> <!-- Refresh every 300 seconds (5 minutes) -->
</head>

<body>
<aside class="sidebar">
    <ul>
        <img src="img\logo_header.png" alt="logo_header">
        <hr>
        <li> MENU BUTTON </li>
        <hr>
        <li><a class="active" href="sms.php">Create New Message</a></li>
        <li><a href="smsqueued.php">Queue Messages</a></li>

        <li><a href="smsinbox.php">Inbox
                <div class="inbox-counter" id="counterInbox">
                    <?php echo $unreadCount. '/'. $allmsg; ?>
                </div>
            </a></li> 

        <li><a href="smssent.php">Sent Messages</a></li>
        <li><a href="smsunsent.php">Unsent Messages</a></li>
        <li><a href="smscancelled.php">Cancelled Messages </a></li>
        <li><a href="smsbroadcastsent.php">Broadcast Sent Messages</a></li>
        <li><a href="smsbroadcastunsent.php">Broadcast Unsent Messages</a></li>
        <hr>
        <li>CHANGE GROUP</li>
        <hr>
        <li><a href="smscallergroup.php">Caller Group</a></li>
    </ul>
</aside>
</body>
</html>