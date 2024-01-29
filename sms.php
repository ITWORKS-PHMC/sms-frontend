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

$_SESSION["recipients"] = array(
    "contacts" => [],
    "groups" => []
);

if (isset($_GET["to"])) {
    // Storing the cipher method
    $ciphering = "AES-128-CTR";

    // Using OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;

    // Non-NULL Initialization Vector for decryption
    $decryption_iv = '1234567891011121';

    // Storing the decryption key
    $decryption_key = "chasey";

    // Using openssl_decrypt() function to decrypt the data
    $decrypted = openssl_decrypt($_GET["to"], $ciphering, $decryption_key, $options, $decryption_iv);

    $recipients_arr = explode(",", $decrypted);

    foreach ($recipients_arr as $recipient) {
        $data = explode("~", $recipient);
        $number = str_replace(" ", "+", $data[2]);

        $_SESSION["recipients"]["contacts"][$number] = [
            "id" => $data[0],
            "name" => $data[1]
        ];
    }
}

if (isset($_GET["group"])) {
    $groups_arr = explode(",", $_GET['group']);

    foreach ($groups_arr as $group) {
        array_push($_SESSION["recipients"]["groups"], $group);
    }
}

//HandLe AJAX request
if (isset($_POST['ajax']) && isset($_POST['checked'])) {
    $checked_arr = $_POST['check'];

    echo json_decode($checked_arr);
    exit;
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
        <div class="container new-message">
            <h1>Create New Messages</h1>
            <?php $selectedCallerCode ?>
            <div class="contact">
                <div class="top">
                    <span class="country">+63</span>
                    <form>
                        <input type="text" id="recipientInput" name="number"
                            onKeyPress="if(this.value.length==10) return false;" oninput="validateInput()"
                            placeholder="Input number here..">

                        <button id="submitButton" type="button" class="add-button" onClick="addRecipient(this)"
                            disabled>
                            Add to Recipient
                        </button>
                    </form>
                </div>
                <a href="contacts.php" class="contacts-button">Contacts</a>
            </div>

            <form action="#" id="send-message" method="post">
                <div class="sms-message">
                    <!-- Broadcast -->
                    <div class="contact-broadcast">
                        <?php
                        $tsql = "SELECT access_level FROM caller_group WHERE caller_group_code = '$selectedCallerCode'";
                        $stmt = sqlsrv_query($conn, $tsql);

                        if ($stmt) {
                            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                            $accessLevel = $row['access_level'];
                            if (in_array($accessLevel, [2, 3, 4])) {
                                echo '<div class="sms-broadcast">
                                        <form action="" method="post">
                                            <div class="broadcastInput">
                                                <label class="title"> Broadcast Title: </label>
                                                <input type="text" class="broadcastTitle" id="title" name="broadcast-msg"></input>
                                            </div>
                                            <div class="broadcastDate">
                                                <label for="schedule">Broadcast Schedule: </label>
                                                <input type="datetime-local" class="broadcastSchedule" id="schedule" name="schedule"></input>
                                            </div>
                                            <div class="broadcastSubmit">
                                                <input type="submit" class="broadcastSet" value="Set Schedule">
                                            </div>
                                        </form>
                                    </div>';
                            } else {
                                echo '<div style="display:none;"></div>';
                            }
                        }
                        ?>
                    </div>
                    <textarea id="message" name="message" class="message" rows="5" placeholder="Type something here.."
                        onInput="countCharactersAndPages()" required></textarea>
                    <div class="messageCounter">
                        <p>
                            Character count:
                            <span id="charCount"></span>
                            <span>/</span>
                            <span id="charLimit"></span>
                        </p>
                        <p>
                            Page count:
                            <span id="pageCount"></span>
                            <span>/</span>
                            <span id="pageCountLimit"></span>
                        </p>
                    </div>

                    <div class="messageSubmit">
                        <input id="submit-msg" type="submit" class="submit" name="submit-msg" placeholder="Send here"
                            value="Send">
                    </div>
                    <input type="hidden" id="hidden-numbers" name="hidden-numbers" required>
                </div>
            </form>

            <!-- Recipient Table -->
            <div class="sms-recipient">
                <table id="recipientTable" class="recipient-table">
                    <thead>
                        <tr>
                            <th>Recipient Name</th>
                            <th>Mobile Number</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="recipientTableBody"></tbody>
                </table>
            </div>

            <!-- Group Table -->
            <div class="sms-recipient">
                <table id="groupTable" class="recipient-table">
                    <thead>
                        <tr>
                            <th>Group Name</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="groupTableBody"></tbody>
                </table>
            </div>
        </div>

        <script>
            // Retrieve the required elements
            const recipientInput = document.getElementById("recipientInput");
            const recipientTableBody = document.getElementById("recipientTableBody");
            const tableData = document.getElementById("recipientTable").rows.length;
            const submitButton = document.getElementById('submit-msg');
            const pageUrl = window.location.search.substring(1)
            const urlVariables = pageUrl.split('&')
            let recipients = [];
            checkGetParameter();

            // Store the recipients
            async function checkGetParameter() {
                let contactNumbers = document.getElementById('hidden-numbers').value
                let groupTable = document.getElementById("groupTable");

                groupTable.style.display = "none";

                for (let i = 0; i < urlVariables.length; i++) {
                    let parameters = urlVariables[i].split('=');

                    if (parameters[0] === 'to' && parameters[1] !== undefined) {

                        const url = 'helper/decrypt.php'

                        let decrypted = await $.post(url, {
                            toDecrypt: decodeURIComponent(parameters[1])
                        }).done((result) => {
                            return result
                        }).fail((err) => {
                            console.log('Failed', err)
                        });
                        console.log(decrypted)

                        // let decodedParameter = decodeURIComponent(parameters[1])
                        let contact_details = decrypted.split(",");

                        contact_details.forEach(person => {
                            // Concatenate numbers in hidden-numbers
                            let contactNumber = person.split("~")[2];
                            contactNumbers += contactNumber + ';'
                            document.getElementById('hidden-numbers').value = contactNumbers

                            recipients.push({
                                "id": person.split("~")[0],
                                "name": person.split("~")[1],
                                "contact_num": contactNumber
                            })
                        })
                    }

                    if (parameters[0] === "group" && parameters[1] !== undefined) {
                        groupTable.style.display = "table";

                        let decodedParameter = decodeURIComponent(parameters[1]).split(",")
                        decodedParameter.forEach(group => {
                            let htmlString = `
                                <tr id="${group}">
                                    <td>${group}</td>
                                    <td>
                                        <button>Delete</button>
                                    </td>
                                </tr>
                            `
                            document.getElementById("groupTableBody").innerHTML += htmlString
                        });
                    }
                }
                renderRecipientTable();
            }

            // Function to create a new row in the recipient table
            function createRecipientRow(recipient) {
                const row = document.createElement("tr");
                row.classList.add("contactNumRow");
                row.setAttribute("id", recipient.contact_num);

                // Recipient cell for Recipient Number <UNKNOWN USER> contact id 
                const recipientCell2 = document.createElement("td");
                recipientCell2.classList.add(0);
                recipientCell2.textContent = recipient.name;
                row.appendChild(recipientCell2);

                // Recipient cell for Mobile Number 
                const recipientCell = document.createElement("td");
                recipientCell.classList.add("contactNum");
                recipientCell.textContent = recipient.contact_num;
                row.appendChild(recipientCell);

                // Action cell
                const actionCell = document.createElement("td");
                const deleteBtn = document.createElement("button");
                deleteBtn.setAttribute("id", recipient.id);
                deleteBtn.textContent = "Delete";
                deleteBtn.addEventListener("click", () => deleteRecipient(recipient.id));
                actionCell.appendChild(deleteBtn);
                row.appendChild(actionCell);

                return row;
            }

            // Function to render the recipient table
            function renderRecipientTable() {
                recipientTableBody.innerHTML = "";
                for (const recipient of recipients) {
                    const row = createRecipientRow(recipient);
                    recipientTableBody.appendChild(row);
                }
            }

            // Function to add a recipient
            function addRecipient(element) {
                // TODO: Check if there's duplicate numbers
                const recipientValue = "+63" + recipientInput.value.trim();
                let contactNumbers = document.getElementById('hidden-numbers').value

                // Concatenate numbers in hidden-numbers
                contactNumbers += recipientValue + ';'
                document.getElementById('hidden-numbers').value = contactNumbers

                if (recipientValue !== "") {
                    recipients.push({
                        "id": Math.random() * -1,
                        "name": 'Unknown', // TODO: If number is existing in contacts, name must be the contact's name
                        "contact_num": recipientValue
                    })
                    // Recipient input clearing after inputing unknown number
                    recipientInput.value = "";
                    renderRecipientTable();
                }
                element.disabled = true;
            }

            // Function to delete a recipient
            function deleteRecipient(recipientId) {
                let toDelete = recipients.filter(recipient => recipient.id === recipientId)[0]
                recipients = recipients.filter(recipient => recipient.id !== recipientId);

                let posting = $.post('helper/RemoveRecipient.php', {
                    contactNum: toDelete["contact_num"]
                });

                // Alerts the results
                posting.done(function (response) {
                    console.log(JSON.parse(response))
                });
                posting.fail(function () {
                    console.log("Failed")
                    alert("Failed to send!");
                });

                renderRecipientTable();
            }


            // Function to send the data to queue
            function sendToQueue(e) {
                e.preventDefault();

                let contactNums = document.getElementsByClassName("contactNum")
                let hiddenNumbers = ""

                let contactNumsArray = [];
                for (let i = 0; i < contactNums.length; i++) {
                    contactNumsArray.push(contactNums[i].innerHTML)
                    hiddenNumbers += contactNums[i].innerHTML + ';'
                }
                hiddenNumbers.trimRight(';')

                console.log(hiddenNumbers)
                document.getElementById('hidden-numbers').value = hiddenNumbers

                let dictionary = {
                    "contactNumbers": contactNumsArray,
                    "message": document.getElementById("message")
                }
                return true
            }
            
            // Stop form from submitting normally 
            $("#send-message").submit(function (event) {
               
                event.preventDefault();

                const url = 'helper/sendToQueue.php'

                var posting = $.post(url, {
                    data: recipients,
                    message: document.getElementById("message").value
                });

                // Alerts the results - message is in queue
                posting.done(function (response) {
                    const obj = JSON.parse(response)
                    if (obj['message'] === "Success") {
                        alert("Message Sent to Queue!");
                        window.location.href = window.location.href.split('?')[0];
                    } else if (obj['message'] === "Invalid Prefix") {
                        alert("Message cannot be send due to unauthorize number. Please contact IT Department at local: 458/459 for further questions.");
                        obj["invalid_num"].forEach(number => {
                            document.getElementById(number).classList.add("invalid");
                        });
                    }
                });
                posting.fail(function () {
                    console.log("Failed")
                    alert("Failed to send!");
                });
            });
        </script>
        <script src="script.js"></script>
    </div>
</body>

</html>