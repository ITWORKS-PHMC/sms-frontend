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
        <div class="container queue">
            <h1> Queue Messages </h1>
            <div class="sms-queue">
                <div id="ticketServing" class="messageCounter">
                    <?php
                    $query = "SELECT MIN(sms_id) AS first_id, COUNT(sms_id) AS total_records FROM sms_queue";
                    $result = sqlsrv_query($conn, $query);

                    if ($result === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }

                    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

                    if ($row) {
                        $firstId = $row['first_id'];
                        $totalRecords = $row['total_records'];

                        echo "<p>Now serving ticket #: $firstId<br></p>";
                        echo "<p>Total # of message in queue: $totalRecords</p>";
                    } else {
                        echo "<p>No message in the queue at the moment.</p>";
                    }
                    ?>
                </div>
            </div>
            <table id="recipientTable" class="recipient-table">
                <thread>
                    <tr>
                        <th>Ticket #</th>
                        <th>Sent By</th>
                        <th>Mobile Number</th>
                        <th>Recipient</th>
                        <th>Text Message</th>
                        <th>Date/Time Created</th>
                        <th>View</th>
                        <th>Cancel</th>
                    </tr>
                </thread>

                <tbody id="recipientTableBody">
                    <?php
                    $tsql = "SELECT DISTINCT s.sms_id, s.contact_id, s.mobile_no, s.sms_message, s.stat, s.date_created, s.created_by, s.date_resend, s.resend_by, c.contact_lname, c.contact_fname, c.contact_mname
                        FROM sms_queue s
                        LEFT JOIN vw_caller_group_members c ON s.mobile_no = c.mobile_no;";

                    $stmt = sqlsrv_query($conn, $tsql);
                    if ($stmt == false) {
                        echo 'ERROR';
                    }

                    while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        $smsMessage = $obj['sms_message'];
                        $mobileNo = $obj['mobile_no'];
                        $formattedDateCreated = $obj['date_created']->format('Y-m-d h:i:s A');

                        if (strpos($smsMessage, '<' . $selectedCallerCode . '>') !== false) {
                            echo "<tr id='msg-{$obj['sms_id']}' contact='{$obj['contact_id']}' mobile='{$obj['mobile_no']}' msg='{$obj['sms_message']}' stat='{$obj['stat']}' date='{$formattedDateCreated}' created='{$obj['created_by']}'>";

                            echo "<td>{$obj['sms_id']}</td>";

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

                            echo "<td>$formattedDateCreated</td>";

                            echo "<td><button onclick='showPopup({$obj['sms_id']})' class='viewButton'>View</button></td>";

                            echo "<td><button onclick='cancelPopup({$obj['sms_id']})' class='viewButton'>Cancel</button></td>";
                            echo "</tr>";
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

            <div id="viewPopup" class="overlay">
                <div class="popup">
                    <div class="popup-header">
                        <h2 class="title">Queue Messages</h2>
                        <button onclick="closePopup()" class="closePopup">&times;</button>
                    </div>
                    <div class="popup-body">
                        <div id="ticketNum"></div>
                        <br>
                        <div id="dateCreated"></div>
                        <br>
                        <div id="sentBy"></div>
                        <br>
                        <div id="mobileNumber"></div>
                        <br>
                        <div id="recipient"></div>
                        <br>
                        <div id="message"></div>
                    </div>
                </div>
            </div>

            <div id="cancelPopup" class="overlay">
                <div class="popup">
                    <div class="cancelpopup-header">
                        <h3 class="title">Are you sure you want to cancel this message?</h3>
                        <h5 class="title">Once this message is cancelled, it cannot be resent.</h5>
                    </div>
                    <div class="cancelpopup-body">
                        <button onclick="cancelMsg()" id="cancelMessage" class="cancelButton">Yes</button>
                        <button onclick="closePopup()" class="cancelButton">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function cancelPopup(id) {
            const row = document.getElementById(`msg-${id}`);
            const popup = document.getElementById("cancelPopup");

            document.getElementById("cancelMessage").setAttribute("data-id", id);
            document.getElementById("cancelMessage").setAttribute("data-contact", row.getAttribute("contact"));
            document.getElementById("cancelMessage").setAttribute("data-mobile", row.getAttribute("mobile"));
            document.getElementById("cancelMessage").setAttribute("data-message", row.getAttribute("msg"));
            document.getElementById("cancelMessage").setAttribute("data-stat", row.getAttribute("stat"));
            document.getElementById("cancelMessage").setAttribute("data-date", row.getAttribute("date"));
            document.getElementById("cancelMessage").setAttribute("data-created", row.getAttribute("created"));
            console.log(row);

            popup.style.display = "flex";
        }

        function cancelMsg(element){
            const id = document.getElementById("cancelMessage").getAttribute("data-id");
            const contact = document.getElementById("cancelMessage").getAttribute("data-contact");
            const mobile = document.getElementById("cancelMessage").getAttribute("data-mobile");
            const msg = document.getElementById("cancelMessage").getAttribute("data-message");
            const stat = document.getElementById("cancelMessage").getAttribute("data-stat");
            const date = document.getElementById("cancelMessage").getAttribute("data-date");
            const created = document.getElementById("cancelMessage").getAttribute("data-created");

            /* Send the data using post with element id name and name */
            if (stat == "1") {
                let cancelled = $.post("helper/smsCancelMsg.php", {
                    id: id,
                    contact: contact,
                    mobile: mobile,
                    msg: msg,
                    date: date,
                    created: created
                });

                /* Alerts the results */
                cancelled.done(function (response) {
                    console.log("RESPONSE", response);
                    if (response === "Record updated successfully") {
                        alert("Message Cancelled!");
                        window.location.href = window.location.href.split('?')[0];
                    }
                });
                cancelled.fail(function () {
                    console.log("Failed");
                    alert("Failed to cancelled!");
                });
            }
        }

        function showPopup(id) {
            const row = document.getElementById(`msg-${id}`);
            console.log(row);
            const cells = document.querySelectorAll(`#msg-${id} > td`);
            console.log(cells);

            const ticket = cells[0].textContent;
            const sentBy = cells[1].textContent;
            const mobileNumber = cells[2].textContent;
            const recipient = cells[3].textContent;
            const fullMessage = cells[4].getAttribute("data-full-message");
            const dateCreated = cells[5].textContent;

            const popup = document.getElementById("viewPopup");

            const contentTicket = document.getElementById("ticketNum");
            const contentDateCreated = document.getElementById("dateCreated");
            const contentMobile = document.getElementById("mobileNumber");
            const contentSentBy = document.getElementById("sentBy");
            const contentRecipient = document.getElementById("recipient");
            const contentMessage = document.getElementById("message");

            document.getElementById("message").className = "popupMessage";

            contentTicket.innerHTML = "<strong>Ticket #:</strong> " + ticket;
            contentDateCreated.innerHTML = "<strong>Date & Time Created:</strong> " + dateCreated;
            contentMobile.innerHTML = "<strong>Mobile Number:</strong> " + mobileNumber;
            contentSentBy.innerHTML = "<strong>Sent By:</strong> " + sentBy;
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
            const popup = document.getElementById("viewPopup");
            const cancelButton = document.getElementById("cancelPopup");
            popup.style.display = "none";
            cancelButton.style.display = "none";
        }        
    </script>
</body>

</html>