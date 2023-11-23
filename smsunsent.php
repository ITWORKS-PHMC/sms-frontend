<?php
// //If not loggedIn cannot passthrough
// session_start();
// if (!isset($_SESSION['loggedin'])) {
//     header('Location: login.php');
//     exit;
// }

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
        <div class="container unsent">
            <h1> Unsent Messages </h1>
            <table id="recipientTable" class="recipient-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mobile Number</th>
                        <th>Text Message</th>
                        <th>View</th>
                    </tr>
                </thead>

                <tbody id="recipientTableBody">
                    <?php
                    $tsql = "SELECT * FROM sms_unsent;";
                    $stmt = sqlsrv_query($conn, $tsql);
                    if ($stmt == false) {
                        echo 'ERROR';
                    }

                    $rowNumber = 1;
                    while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        echo "<tr id='msg-{$obj['sms_id']}'>";
                        echo '<td>' . $rowNumber . '</td>';

                        echo "<td>{$obj['mobile_no']}</td>";
                        
                        echo "<td data-full-message='" . htmlspecialchars($obj['sms_message']) . "'>" . htmlspecialchars(mb_substr($obj['sms_message'], 0, 5)) . "...</td>";

                        // echo "<td>{$obj['date_received']->format('Y-m-d H:i:s')}</td>";
                        
                        echo "<td><button onclick='showPopup({$obj['sms_id']})' class='viewButton'>View</button></td>";
                        echo "</tr>";
                        $rowNumber++;
                    }
                    sqlsrv_free_stmt($stmt);
                    ?>
                </tbody>
            </table>

            <div id="popup" class="overlay">
                <div class="popup">
                    <div class="popup-header">
                        <h2 class="title">Unsent Messages</h2>
                        <button onclick="closePopup()" class="closePopup">&times;</button>
                    </div>
                    <div class="popup-body">
                        <div id="sender"></div>
                        <br>
                        <div id="message"></div>
                    </div>
                    <div class="popup-button">
                        <button onclick="addRecipient(this)" class="replyButton" id="resendButton">Resend</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showPopup(id) {
            document.getElementById("resendButton").value = id;
            const row = document.getElementById(`msg-${id}`);
            console.log(row);
            const cells = document.querySelectorAll(`#msg-${id} > td`);
            console.log(cells);

            const sender = cells[1].textContent;
            const fullMessage = cells[2].getAttribute("data-full-message");

            const popup = document.getElementById("popup");

            const contentSender = document.getElementById("sender");
            const contentMessage = document.getElementById("message");
            
            document.getElementById("message").className = "popupMessage";

            contentSender.textContent = "Sender: " + sender;
            contentMessage.textContent = "Message: " + fullMessage;
            popup.style.display = "flex";
        }
       
        function addRecipient(element) {
            const cells = document.querySelectorAll(`#msg-${element.value} > td`);

            const contact_num = cells[1].textContent;
            const message = cells[2].textContent;

            let contact_string = `0~${contact_num}~${contact_num}`
            // window.location.href = `http://localhost/sms-frontend/sms.php?to=${contact_string}`;
            window.location.href = `http://phmc-sms/sms-frontend/sms.php?to=${contact_string}`; //server phmc-sms01
        } 

        function closePopup() {
            const popup = document.getElementById("popup");
            popup.style.display = "none";
        }        
    </script>
</body>

</html>