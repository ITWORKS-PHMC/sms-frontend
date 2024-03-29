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
        <div class="container unsent">
            <h1> Unsent Messages </h1>
            <table id="recipientTable" class="recipient-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mobile Number</th>
                        <th>Recipient</th>
                        <th>Text Message</th>
                        <th>View</th>
                    </tr>
                </thead>

                <tbody id="recipientTableBody">
                    <?php
                    $tsql = "SELECT DISTINCT su.sms_id, su.contact_id, su.mobile_no, su.sms_message, su.stat, su.date_created, su.created_by, su.date_unsent, su.error_log, cgm.contact_lname, cgm.contact_fname, cgm.contact_mname
                    FROM sms_unsent su
                    LEFT JOIN vw_caller_group_members cgm ON su.mobile_no = cgm.mobile_no;";

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

                            if ($obj['contact_lname']) {
                                $maskedMobileNo = substr($mobileNo, 0, 6) . str_repeat('*', strlen($mobileNo) - 8) . substr($mobileNo, -2);
                                echo "<td>{$maskedMobileNo}</td>";
                                echo "<td>{$obj['contact_lname']}, {$obj['contact_fname']} {$obj['contact_mname']} </td>";
                            } else {
                                echo "<td>{$mobileNo}</td>";
                                echo "<td>Unknown User</td>";
                            }

                            echo "<td data-full-message='" . htmlspecialchars($smsMessage) . "'>" . htmlspecialchars(mb_substr($smsMessage, 0, 5)) . "...</td>";

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
                        <h2 class="title">Unsent Messages</h2>
                        <button onclick="closePopup()" class="closePopup">&times;</button>
                    </div>
                    <div class="popup-body">
                        <div id="mobileNumber"></div>
                        <br>
                        <div id="recipient"></div>
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

            const mobileNumber = cells[1].textContent;
            const recipient = cells[2].textContent;
            const fullMessage = cells[3].getAttribute("data-full-message");

            const popup = document.getElementById("popup");

            const contentMobile = document.getElementById("mobileNumber");
            const contentRecipient = document.getElementById("recipient");
            const contentMessage = document.getElementById("message");

            document.getElementById("message").className = "popupMessage";

            contentMobile.innerHTML = "<strong>Mobile Number:</strong> " + mobileNumber;
            contentRecipient.innerHTML = "<strong>Recipient:</strong> " + recipient;
            contentMessage.innerHTML = "<strong>Message:</strong> " + escapeHtml(fullMessage);

            // /* Send the data using post with element id name and name */
            // if (status == 0) {
            //     let update = $.post("smsInboxUpdate.php", {
            //     id: id,
            //     });

            //     /* Alerts the results */
            //     update.done(function (response) {
            //     console.log("RESPONSE", response);
            //     if (response === "Record updated successfully") {
            //         row.classList.remove("highlight");
            //         document.querySelector(`#msg-${id} > .read_status`).textContent = 1;
            //         document.getElementById("counterInbox").textContent =
            //         Number(document.getElementById("counterInbox").textContent) - 1;
            //         popup.style.display = "flex";
            //     }
            //     });

            //     update.fail(function () {
            //     console.log("Failed");
            //     });
            // }

            popup.style.display = "flex";
        }

        // Function to escape HTML entities (similar to htmlspecialchars)
        function escapeHtml(text) {
            var div = document.createElement("div");
            div.innerText = text;
            return div.innerHTML;
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