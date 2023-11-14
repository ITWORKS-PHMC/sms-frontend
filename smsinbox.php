<?php
// session_start();
// if (!isset($_SESSION['username'])) {
//     header("Location: login.php"); // Redirect to the login page if not login 
//     exit();
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

        <div class="container inbox">
            <h1> Inbox </h1>
            <table id="recipientTable" class="recipient-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Read Status</th>
                        <th>Mobile Number</th>
                        <th>Text Message</th>
                        <th>Date/Time Received</th>
                        <th>View</th>
                    </tr>
                </thead>

                <tbody id="recipientTableBody"></tbody>

                <?php
                $tsql = "SELECT * FROM sms_received ORDER BY date_received DESC;";
                $stmt = sqlsrv_query($conn, $tsql);
                if ($stmt == false) {
                    echo 'ERROR';
                }

                $rowNumber = 1;
                while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $class = "";
                    if ($obj['read_status'] == 0) {
                        $class .= "highlight";
                    }
                    
                    echo "<tr class='$class' id='msg-{$obj['sms_received_id']}'>";
                    echo '<td>' . $rowNumber . '</td>';

                    if ($obj['read_status'] == 0) {
                        echo "<td class='read_status'>Unread</td>";
                    }else{
                        echo "<td class='read_status'>Read</td>";
                    }

                    echo "<td>{$obj['mobile_no']}</td>";
                    //TODO
                    // message should be 10 char length 
                    // to see the content click view to pop up the full content 
                    // echo "<td>";
                    // echo substr($obj['sms_message'], 0, 10);
                    // echo "</td>";

                    echo "<td>" . wordwrap($obj['sms_message'], 35, "<br>\n", true) . "</td>";
                   
                    echo "<td>{$obj['date_received']->format('Y-m-d H:i:s')}</td>";

                    echo "<td><button onclick='showPopup({$obj['sms_received_id']})' class='viewButton'>View</button></td>";
                    echo "</tr>";
                    $rowNumber++;
                }
                sqlsrv_free_stmt($stmt);
                ?>
            </table>
                            
            <div class="popup" id="popup">
                <button onclick="closePopup()" class="closeButton">
                    Close
                </button>

                <div id="sender"></div>
                <div id="message"></div>
                <div id="readStatus"></div>
                <div id="date"></div>

                <button type="button" class="replyButton" id="replyButton" onclick="addRecipient(this)">
                    Reply
                </button>
            </div>
        </div>
    </div>

    <!-- <script src="script.js"></script> -->
    <script>
        function showPopup(id) {
        document.getElementById("replyButton").value = id;
        const row = document.getElementById(`msg-${id}`);
        console.log(row);
        const cells = document.querySelectorAll(`#msg-${id} > td`);

        const status = cells[1].textContent;
        const sender = cells[2].textContent;
        const message = cells[3].textContent;
        const receiveDate = cells[4].textContent;

        const popup = document.getElementById("popup");

        const contentStatus = document.getElementById("readStatus");
        const contentSender = document.getElementById("sender");
        const contentMessage = document.getElementById("message");
        const contentDate = document.getElementById("date");

        contentStatus.textContent = "Read Status: " + status;
        contentSender.textContent = "Sender: " + sender;
        contentMessage.textContent = "Message: " + message;
        contentDate.textContent = "Date: " + receiveDate;

        /* Send the data using post with element id name and name */
        if (status == 0) {
            let update = $.post("smsInboxUpdate.php", {
            id: id,
            });

            /* Alerts the results */
            update.done(function (response) {
            console.log("RESPONSE", response);
            if (response === "Record updated successfully") {
                row.classList.remove("highlight");
                document.querySelector(`#msg-${id} > .read_status`).textContent = 1;
                document.getElementById("counterInbox").textContent =
                Number(document.getElementById("counterInbox").textContent) - 1;
                popup.style.display = "flex";
            }
            });
            
            update.fail(function () {
            console.log("Failed");
            });
        }

        popup.style.display = "flex";
        }

        function closePopup() {
        const popup = document.getElementById("popup");
        popup.style.display = "none";
        }
        
        function addRecipient(element) {
            const cells = document.querySelectorAll(`#msg-${element.value} > td`);
            
            const status = cells[1].textContent;
            const contact_num = cells[2].textContent;
            const message = cells[3].textContent;
            const receiveDate = cells[4].textContent;

            // let contact_string = `0~? ?~${contact_num}`
            let contact_string = `0~${contact_num}~${contact_num}`
            window.location.href = `http://localhost/sms-frontend/sms.php?to=${contact_string}`;
            // window.location.href = `http://uphmc-sms01.uphmc.com.ph/sms-frontend/sms.php?to=${contact_string}`; //server phmc-sms01
        } 
    </script>
</body>

</html>