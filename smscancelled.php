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
    <?php include("./layouts/header.php"); ?>
</head>

<body>
    <?php include("./layouts/navbar.php"); ?>
    <div class="content">
        <?php include("./layouts/menu.php"); ?>
        <div class="container cancelled">
            <h1> Cancelled Messages </h1>
            <table id="recipientTable" class="recipient-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sent By</th>
                        <th>Mobile Number</th>
                        <th>Recipient</th>
                        <th>Text Message</th>
                        <th>Date & Time Created</th>
                        <th>Date & Time Cancelled</th>
                        <th>Cancelled by</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody id="recipientTableBody">
                    <?php
                    $tsql = "SELECT DISTINCT s.sms_id, s.contact_id, s.mobile_no, s.sms_message, s.date_created, s.created_by, s.date_cancelled, s.cancelled_by, c.contact_lname, c.contact_fname, c.contact_mname
                    FROM sms_cancelled s
                    LEFT JOIN vw_caller_group_members c ON s.mobile_no = c.mobile_no
                    ORDER BY s.date_cancelled DESC;";

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

                            echo "<td>{$obj['date_cancelled']->format('Y-m-d h:i:s A')}</td>";

                            echo "<td>{$obj['cancelled_by']}</td>";

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
                        <div id="dateCancelled"></div>
                        <br>
                        <div id="sentBy"></div>
                        <br>
                        <div id="cancelledBy"></div>
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
            const dateCancelled = cells[6].textContent;
            const cancelled = cells[7].textContent;

            const popup = document.getElementById("popup");

            const contentDateCreated = document.getElementById("dateCreated");
            const contentMobile = document.getElementById("mobileNumber");
            const contentSentBy = document.getElementById("sentBy");
            const contentDateCancelled = document.getElementById("dateCancelled");
            const contentCancelledBy = document.getElementById("cancelledBy");
            const contentRecipient = document.getElementById("recipient");
            const contentMessage = document.getElementById("message");

            document.getElementById("message").className = "popupMessage";

            contentDateCreated.innerHTML = "<strong>Date & Time Created:</strong> " + dateCreated;
            contentMobile.innerHTML = "<strong>Mobile Number:</strong> " + mobileNumber;
            contentSentBy.innerHTML = "<strong>Sent By:</strong> " + sentBy;
            contentDateCancelled.innerHTML = "<strong>Date & Time Cancelled:</strong> " + dateCancelled;
            contentCancelledBy.innerHTML = "<strong>Cancelled By:</strong> " + cancelled;
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