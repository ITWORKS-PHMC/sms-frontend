<?php
session_start();
// Redirect to the login page if not login 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$selectedCallerCode = $_SESSION['selectedCallerCode'];

// Database connection 
include("./database/connection.php");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SMS</title>
    <link rel="sms icon" type="x-icon" href="img\logo.png">
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include("./nav/navbar.php"); ?>

    <div class="content">
        <?php include("./menu/menu.php"); ?>

        <div class="container queue">
            <h1> Queue Messages </h1>
            <div class="sms-recipient">
                <table id="recipientTable" class="recipient-table">
                    <form class="" action="" method="post">
                        <tr>
                            <th>#</th>
                            <th>Mobile Number</th>
                            <th>Text Message</th>
                            <th>Date/Time Created</th>
                            <th>Created By</th>
                        </tr>

                        <?php
                        $tsql = "SELECT sms_id, contact_id, mobile_no, sms_message, stat, date_created, created_by, date_resend, resend_by FROM sms_queue";
                        $stmt = sqlsrv_query($conn, $tsql);

                        if ($stmt == false) {
                            echo 'ERROR';
                        }

                        $rowNumber = 1;
                        while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                            $smsMessage = $obj['sms_message'];
                            $pattern = '/(.+?)\s*\[(\d+)\/(\d+)\]\.\.\.<' . $selectedCallerCode . '>/';
                            if (strpos($smsMessage, '<' . $selectedCallerCode . '>') !== false) {
                                if (preg_match($pattern, $smsMessage, $matches)) {
                                    // echo "<tr id='msg-{$obj['sms_id']}'>";
                                    echo "<tr>";
                                    echo '<td>' . $rowNumber . '</td>';

                                    echo "<td>{$obj['mobile_no']}</td>";

                                    // echo "<td>" . htmlspecialchars(wordwrap($obj['sms_message'], 50, "<br>\n", true)) . "</td>";
                                    echo "<td> this message has multiple pages: " . htmlspecialchars($matches[0]) . "</td>";

                                    echo "<td>{$obj['date_created']->format('Y-m-d h:i:s A')}</td>";

                                    echo "<td>{$obj['created_by']}</td>";

                                    // echo "<td><button onclick='showPopup({$obj['sms_id']})' class='viewButton'>View</button></td>";
                                    echo "</tr>";
                                    $rowNumber++;
                                } else {
                                    // echo "<tr id='msg-{$obj['sms_id']}'>";
                                    echo "<tr>";
                                    echo '<td>' . $rowNumber . '</td>';

                                    echo "<td>{$obj['mobile_no']}</td>";

                                    echo "<td>" . htmlspecialchars(wordwrap($obj['sms_message'], 50, "<br>\n", true)) . "</td>";

                                    echo "<td>{$obj['date_created']->format('Y-m-d h:i:s A')}</td>";

                                    echo "<td>{$obj['created_by']}</td>";

                                    // echo "<td><button onclick='showPopup({$obj['sms_id']})' class='viewButton'>View</button></td>";
                                    echo "</tr>";
                                    $rowNumber++;
                                }
                            } else {
                                echo "<tr style='display: none;'>";
                                echo "<td>Caller code doesn't match in this message</td>";
                                echo "</tr>";
                            }
                        }
                        sqlsrv_free_stmt($stmt);
                        ?>
                    </form>
                </table>
            </div>
        </div>
    </div>
</body>

</html>