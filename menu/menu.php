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

    sqlsrv_free_stmt($stmt_count);
    // sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html>
<head>    
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav>
        <ul class="menu">
            <li class="menulabel"> MENU BUTTON </li>
            <li class="menulist"><a class="active" href="sms.php">Create New Message</a></li>
            <li class="menulist"><a href="smsqueued.php">Queue Messages</a></li>
            
            <li class="menulist"><a href="smsinbox.php">Inbox 
                 <div class="circle"> <?php echo $unreadCount; ?> </div>  
            </a></li>
          
            <li class="menulist"><a href="smssent.php">Sent Messages</a></li>
            <li class="menulist"><a href="smsunsent.php">Unsent Messages</a></li>
            <li class="menulist"><a href="smscancelled.php">Cancelled Messages </a></li>
            <li class="menulist"><a href="smsbroadcastsent.php">Broadcast Sent Messages</a></li>
            <li class="menulist"><a href="smsbroadcastunsent.php">Broadcast Unsent Messages</a></li>
            <li class="menulist"><a href="smscallergroup.php">Change Caller Group</a></li>
        </ul>
    </nav>
</body>
</html> 