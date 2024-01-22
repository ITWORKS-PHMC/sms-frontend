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
        <div class="container sent">
            <h1> Sent Messages </h1>
            <table id="recipientTable" class="recipient-table">
                <thread>
                    <tr>
                        <th>#</th>
                        <th>Sent By</th>
                        <th>Mobile Number</th>
                        <th>Recipient</th>
                        <th>Text Message</th>
                        <th>Date & Time Created</th>
                        <th>Date & Time Sent</th>
                        <th>View</th>
                    </tr>
                </thread>

                <tbody id="recipientTableBody">
                    <?php
                    $tsql = "SELECT DISTINCT s.sms_id, s.contact_id, s.mobile_no, s.sms_message, s.date_created, s.created_by, s.date_resend, s.resend_by, s.date_sent, c.contact_lname, c.contact_fname, c.contact_mname
                    FROM sms_sent s
                    LEFT JOIN vw_caller_group_members c ON s.mobile_no = c.mobile_no
                    ORDER BY s.date_sent DESC;";

                    $stmt = sqlsrv_query($conn, $tsql);
                    if ($stmt == false) {
                        echo 'ERROR';
                    }

                    $rowNumber = 1;
                    while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $smsMessage = $obj['sms_message'];
                        $mobileNo = $obj['mobile_no'];
                        if (strpos($smsMessage, '<' . $selectedCallerCode . '>') !== false) {
                            echo "<tr id='msg-{$obj['sms_id']}'>";
                            echo '<td>' . $rowNumber . '</td>';

                            echo "<td>{$obj['created_by']}</td>";

                            if ($obj['contact_lname']) {
                                $maskedMobileNo = substr($mobileNo, 0, 6) . str_repeat('*', strlen($mobileNo) - 8) . substr($mobileNo, -2);
                                echo "<td>{$maskedMobileNo}</td>";
                                echo "<td>{$obj['contact_lname']}, {$obj['contact_fname']} {$obj['contact_mname']} </td>";
                            } else {
                                echo "<td>{$mobileNo}</td>";
                                echo "<td>Unknown User</td>";
                            }

                            echo "<td data-full-message='" . htmlspecialchars($smsMessage) . "'>" . htmlspecialchars(mb_substr($smsMessage, 0, 5)) . "...</td>";

                            echo "<td>{$obj['date_created']->format('Y-m-d h:i:s A')}</td>";

                            echo "<td>{$obj['date_sent']->format('Y-m-d h:i:s A')}</td>";

                            echo "<td><button onclick='showPopup({$obj['sms_id']})' class='viewButton'>View</button></td>";
                            echo "</tr>";
                            $rowNumber++;
                        } else {
                            echo "<tr style='display: none;'>";
                            echo "<td>Caller code doesn't match in this message</td>";
                            echo "</tr>";
                        }
                    }
                    sqlsrv_free_stmt($stmt);
                    ?>
                </tbody>
            </table>

            <div id="popup" class="overlay">
                <div class="popup">
                    <div class="popup-header">
                        <h2 class="title">Sent Messages</h2>
                        <button onclick="closePopup()" class="closePopup">&times;</button>
                    </div>
                    <div class="popup-body">
                        <div id="dateCreated"></div>
                        <br>
                        <div id="sentBy"></div>
                        <br>
                        <div id="dateSent"></div>
                        <br>
                        <div id="mobileNumber"></div>
                        <br>
                        <div id="recipient"></div>
                        <br>
                        <div id="message"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showPopup(id) {
            const row = document.getElementById(`msg-${id}`);
            console.log(row);
            const cells = document.querySelectorAll(`#msg-${id} > td`);
            console.log(cells);

            const sentBy = cells[1].textContent;
            const mobileNumber = cells[2].textContent;
            const recipient = cells[3].textContent;
            const fullMessage = cells[4].getAttribute("data-full-message");
            const dateCreated = cells[5].textContent;
            const dateSent = cells[6].textContent;

            const popup = document.getElementById("popup");

            const contentDateCreated = document.getElementById("dateCreated");
            const contentMobile = document.getElementById("mobileNumber");
            const contentSentBy = document.getElementById("sentBy");
            const contentDateSent = document.getElementById("dateSent");
            const contentRecipient = document.getElementById("recipient");
            const contentMessage = document.getElementById("message");

            document.getElementById("message").className = "popupMessage";

            contentDateCreated.innerHTML = "<strong>Date & Time Created:</strong> " + dateCreated;
            contentMobile.innerHTML = "<strong>Mobile Number:</strong> " + mobileNumber;
            contentSentBy.innerHTML = "<strong>Sent By:</strong> " + sentBy;
            contentDateSent.innerHTML = "<strong>Date & Time Sent:</strong> " + dateSent;
            contentRecipient.innerHTML = "<strong>Recipient:</strong> " + recipient;
            contentMessage.innerHTML = "<strong>Text Message:</strong> " + escapeHtml(fullMessage);

            popup.style.display = "flex";
        }

        // Function to escape HTML entities (similar to htmlspecialchars)
        function escapeHtml(text) {
            var div = document.createElement("div");
            div.innerText = text;
            return div.innerHTML;
        }

        function closePopup() {
            const popup = document.getElementById("popup");
            popup.style.display = "none";
        }        
    </script>
</body>

</html>