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
        <div class="container inbox">
            <h1>Received Messages</h1>
            <table id="recipientTable" class="recipient-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Read Status</th>
                        <th>Mobile Number</th>
                        <th>Sender</th>
                        <th>Text Message</th>
                        <th>Date/Time Received</th>
                        <th>View</th>
                    </tr>
                </thead>

                <tbody id="recipientTableBody"></tbody>

                <?php
                $tsql = "SELECT DISTINCT s.sms_received_id, s.caller_group_code, s.mobile_no, s.sms_message, s.date_received, s.read_status, s.sms_status, s.error_log, c.contact_lname, c.contact_fname, c.contact_mname
                FROM sms_received s
                LEFT JOIN vw_caller_group_members c ON s.mobile_no = c.mobile_no
                ORDER BY s.date_received DESC;";

                $stmt = sqlsrv_query($conn, $tsql);
                if ($stmt == false) {
                    echo 'ERROR';
                }

                $rowNumber = 1;
                while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $smsMessage = $obj['sms_message'];
                    $mobileNo = $obj['mobile_no'];
                    $class = "";
                    if ($obj['read_status'] == 0) {
                        $class .= "highlight";
                    }

                    if (strpos($smsMessage, '<' . $selectedCallerCode . '>') !== false) {
                        echo "<tr class='$class' id='msg-{$obj['sms_received_id']}'>";
                        echo '<td>' . $rowNumber . '</td>';

                        echo "<td class='read_status' id='stats-{$obj['read_status']}'>";
                        if ($obj['read_status'] == 0) {
                            echo "Unread";
                        } else {
                            echo "Read";
                        }
                        echo "</td>";

                        if ($obj['contact_lname']) {
                            $maskedMobileNo = substr($mobileNo, 0, 6) . str_repeat('*', strlen($mobileNo) - 8) . substr($mobileNo, -2);
                            echo "<td>{$maskedMobileNo}</td>";
                            echo "<td>{$obj['contact_lname']}, {$obj['contact_fname']} {$obj['contact_mname']} </td>";
                        } else {
                            echo "<td>{$mobileNo}</td>";
                            echo "<td>Unknown User</td>";
                        }

                        echo "<td data-full-message='" . htmlspecialchars($smsMessage) . "'>" . htmlspecialchars(mb_substr($smsMessage, 0, 5)) . "...</td>";

                        echo "<td>{$obj['date_received']->format('Y-m-d h:i:s A')}</td>";

                        echo "<td><button onclick='showPopup({$obj['sms_received_id']})' class='viewButton'>View</button></td>";
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
            </table>

            <div id="popup" class="overlay">
                <div class="popup">
                    <div class="popup-header">
                        <h2 class="title">Inbox</h2>
                        <button onclick="closePopup()" class="closePopup">&times;</button>
                    </div>
                    <div class="popup-body">
                        <div id="date"></div>
                        <br>
                        <div id="readStatus"></div>
                        <br>
                        <div id="mobileNumber"></div>
                        <br>
                        <div id="sender"></div>
                        <br>
                        <div id="message"></div>
                    </div>
                    <div class="popup-button">
                        <button onclick="addRecipient(this)" id="replyButton" class="replyButton">Reply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPopup(id) {
            document.getElementById("replyButton").value = id;
            const row = document.getElementById(`msg-${id}`);
            console.log(row);
            const cells = document.querySelectorAll(`#msg-${id} > td`);

            const status = cells[1].textContent;
            const mobileNumber = cells[2].textContent;
            const recipient = cells[3].textContent;
            const fullMessage = cells[4].getAttribute("data-full-message");
            const receiveDate = cells[5].textContent;

            const popup = document.getElementById("popup");

            const contentStatus = document.getElementById("readStatus");
            const contentMobile = document.getElementById("mobileNumber");
            const contentSender = document.getElementById("sender");
            const contentMessage = document.getElementById("message");
            const contentDate = document.getElementById("date");

            document.getElementById("message").className = "popupMessage";

            contentStatus.innerHTML = "<strong>Read Status:</strong> " + status;
            contentMobile.innerHTML = "<strong>Sender Mobile Number:</strong> " + mobileNumber;
            contentSender.innerHTML = "<strong>Sender Name:</strong> " + recipient;
            contentMessage.innerHTML = "<strong>Message:</strong> " + escapeHtml(fullMessage);
            contentDate.innerHTML = "<strong>Date/Time Received:</strong> " + receiveDate;

            /* Send the data using post with element id name and name */
            if (status == "Unread") {
                let update = $.post("helper/smsInboxUpdate.php", {
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

        function addRecipient(element) {
            const cells = document.querySelectorAll(`#msg-${element.value} > td`);
            const status = cells[1].textContent;
            const contact_num = cells[2].textContent;
            const message = cells[3].textContent;
            const receiveDate = cells[4].textContent;

            let contact_string = `0~${contact_num}~${contact_num}`
            // window.location.href = `http://localhost/sms-frontend/sms.php?to=${contact_string}`;
            window.location.href = `http://phmc-sms/sms-frontend/sms.php?to=${contact_string}`; //server phmc-sms
        } 
    </script>
</body>

</html>