<?php
$username = $_SESSION['username'];
$selectedCallerCode = $_SESSION['selectedCallerCode'];

// Database connection 
include("./database/connection.php");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Total count of message received
$tsql = "SELECT sms_message, read_status FROM sms_received";
$stmt = sqlsrv_query($conn, $tsql);
if ($stmt == false) {
    echo 'ERROR';
}

$allMsg = 0;
$unreadMsg = 0;
while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $smsMessage = $obj['sms_message'];
    if (strpos($smsMessage, '<' . $selectedCallerCode . '>') !== false) {
        $allMsg++;
        if ($obj['read_status'] == 0) {
            $unreadMsg++;
        }
    }
}
sqlsrv_free_stmt($stmt);
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="refresh" content="300"> <!-- Refresh every 300 seconds (5 minutes) -->
    <style>
/* Style the buttons */
.btn {
  border: none;
  outline: none;
  padding: 10px 16px;
  background-color: #f1f1f1;
  cursor: pointer;
  font-size: 18px;
}

/* Style the active class, and buttons on mouse-over */
.active, .btn:hover {
  background-color: #666;
  color: white;
}
</style>
</head>

<body>
    <aside class="sidebar">
        <ul>
            <img src="img\logo_header.png" alt="logo_header" class="imgMenu">
            <div class="menuTitle">
                <hr>
                <li> MENU BUTTON </li>
                <hr>
            </div>
            <div id="myDIV">
                <li><a href="sms.php" class="btn active">Create New Message</a></li>
                <li><a href="smsqueued.php" class="btn">Queue Messages</a></li>

                <li><a href="smsinbox.php" class="btn">Received
                        <div class="inbox-counter" id="counterInbox">
                            <?php echo $unreadMsg . '/' . $allMsg; ?>
                        </div>
                    </a></li>
                <li><a href="smssent.php" class="btn">Sent Messages</a></li>
                <li><a href="smsunsent.php" class="btn">Unsent Messages</a></li>
                <li><a href="smscancelled.php" class="btn">Cancelled Messages </a></li>

                <?php
                $tsql = "SELECT access_level FROM caller_group WHERE caller_group_code = '$selectedCallerCode'";
                $stmt_caller = sqlsrv_query($conn, $tsql);

                if ($stmt_caller) {
                    $row = sqlsrv_fetch_array($stmt_caller, SQLSRV_FETCH_ASSOC);
                    $accessLevel = $row['access_level'];

                    echo '<ul>';
                    if ($accessLevel != 1) {
                        echo '<li><a href="smsbroadcastsent.php" class="btn">Broadcast Sent Messages</a></li>';
                        echo '<li><a href="smsbroadcastunsent.php" class="btn">Broadcast Unsent Messages</a></li>';
                        if ($accessLevel == 4) {
                            echo '<li><a href="smsjunk.php">Junk Messages</a></li>';
                        }
                    }
                    echo '</ul>';
                }
                ?>
                <div class="menuTitle">
                    <hr>
                    <li> CHANGE GROUP </li>
                    <hr>
                </div>
                <li><a href="smscallergroup.php" class="btn">Caller Group</a></li>
            </div>
        </ul>
    </aside>
    <script>
        // Add active class to the current button (highlight it)
        var header = document.getElementById("myDIV");
        var btns = header.getElementsByClassName("btn");
        for (var i = 0; i < btns.length; i++) {
            btns[i].addEventListener("click", function () {
                var current = document.getElementsByClassName("active");
                current[0].className = current[0].className.replace(" active", "");
                this.className += " active";
            });
        }
    </script>
</body>

</html>