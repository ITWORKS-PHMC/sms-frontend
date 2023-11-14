<?php
// //If not loggedIn cannot passthrough
// session_start();
// if (!isset($_SESSION['loggedin'])) {
//     header('Location: login.php');
//     exit;
// }
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
        <div class="container broadcast-sent">
            <h1> Broadcast Sent Messages </h1>
            <table id="recipientTable" class="recipient-table">
                <thead>
                    <tr>
                        <th> ID </th>
                        <th> Contact ID </th>
                        <th> Mobile Number </th>
                        <th> Text Message </th>
                        <th> View </th>
                    </tr>
                </thead>

                <tbody id="recipientTableBody"></tbody>

                <?php
                // $tsql = "SELECT * FROM sms_unsent ORDER BY date_received DESC;";
                $tsql = "SELECT * FROM sms_cancelled;";
                $stmt = sqlsrv_query($conn, $tsql);
                if ($stmt == false) {
                    echo 'ERROR';
                }

                while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    // echo "<tr class='$class' id='msg-{$obj['sms_received_id']}'>";
                    echo "<td>{$obj['sms_id']}</td>";
                    echo "<td>{$obj['contact_id']}</td>";
                    echo "<td>{$obj['mobile_no']}</td>";
                    echo "<td>" . htmlspecialchars(wordwrap($obj['sms_message'], 50, "<br>\n", true)) . "</td>";

                
                    // echo "<td>{$obj['date_received']->format('Y-m-d H:i:s')}</td>";
                    echo "<td><button onclick='showPopup()' class='viewButton'>View</button></td>";

                    echo "</tr>";
                }
                sqlsrv_free_stmt($stmt);
                ?>
            </table>
        </div>
    </div>
</body>
</html>