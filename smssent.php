<?php
//database connection 
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=500, initial-scale=1" />
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
        <div class="container sent">
            <h1> Sent Messages </h1>
            <div class="sms-recipient">
                <table id="recipientTable" class="recipient-table">
                    <form class="" action="" method="post">
                        <tr>
                            <th>#</th>
                            <th>Mobile Number</th>
                            <th>Text Message</th>
                            <th>Date & Time Created</th>
                            <th>Created By</th> 
                            <th>Date & Time Sent</th>
                        </tr>

                        <?php
                        $tsql = "SELECT * FROM sms_sent ORDER BY date_sent DESC";
                        $stmt = sqlsrv_query($conn, $tsql);
                        if ($stmt == false) {
                            echo 'ERROR';
                        }

                        $rowNumber = 1;
                        while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                            echo "<tr>";
                            echo '<td>' . $rowNumber . '</td>';

                            echo "<td>{$obj['mobile_no']}</td>";

                            echo "<td>" . htmlspecialchars(wordwrap($obj['sms_message'], 50, "<br>\n", true)) . "</td>";

                            echo "<td>{$obj['date_created']->format('Y-m-d H:i:s')}</td>";

                            echo "<td>{$obj['created_by']}</td>";

                            echo "<td>{$obj['date_sent']->format('Y-m-d H:i:s')}</td>";
                            echo "</tr>";
                            $rowNumber++;
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