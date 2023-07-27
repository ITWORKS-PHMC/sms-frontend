<?php
// session_start();
// if (!isset($_SESSION['loggedin'])) {
//     header('Location: login.php');
//     exit;
// }

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
    <meta charset="utf-8">
    <title>SMS</title>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <link rel="stylesheet" href="style.css">
</head>

<body class="loggedin">
    <nav class="navtop">
        <div>
            <h1>PHMC</h1>
            <a href="home.php"><i class="fas fa-solid fa-house"></i>HOME</a>
            <a href="sms.php"><i class="fas fa-solid fa-message"></i>SMS</a>
            <a href="contacts.php"><i class="fa-solid fa-address-book"></i>CONTACTS</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i>LOGOUT</a>
        </div>
    </nav>

    <div class="menu">
        <div class="menubuttons">
            <h1> Main Menu </h1>
            <a href="sms.php"><button type="button" class="sms button"> Create New Message </button></a>
            <a href="smsqueued.php"><button type="button" class="sms button"> Queue Messages </button></a>
            <a href="smsinbox.php"><button type="button" class="sms button"> Inbox </button></a>
            <a href="smssent.php"><button type="button" class="sms button"> Sent Messages </button></a>
            <a href="smsunsent.php"><button type="button" class="sms button"> Unsent Messages </button></a>
            <a href="smscancelled.php"><button type="button" class="sms button"> Cancelled Messages </button></a>
            <a href="smsbroadcastsent.php"><button type="button" class="sms button"> Broadcast Sent
                    Messages</button></a>
            <a href="smsbroadcastunsent.php"><button type="button" class="sms button"> Broadcast Unsent Messages
                </button></a>
        </div>
    </div>

    <div class="container new-message">
        <h1> Create New Messages </h1>
        <!-- <form onsubmit="return sendToQueue(event)"> -->
        <form action="#" id="send-message" method="post">
            <div class="contact">
                <div class="top">
                    <span class="country">+63</span>
                    <input type="number" id="recipientInput" name="number"
                        onKeyPress="if(this.value.length==10) return false;" placeholder="Input number here..">
                    <button type="button" class="add-button" onClick="addRecipient()">Add to Recipient</button>
                </div>
                <a href="contacts.php" class="contacts-button">Contacts</a>
            </div>

            <div class="sms-message">
                <textarea id="message" name="message" rows="5" placeholder="Type something here.." required
                    maxlength="140"></textarea>
                <p id="result"></p>
                <input type="submit" id="submit-msg" class="submit" name="submit-msg" placeholder="Send here"
                    onclick="showAlert()" value="Send">
            </div>
            <input type="hidden" id="hidden-numbers" name="hidden-numbers" required>
        </form>

        <div id="myAlert">
        <div class="myAlert-text-icon">
            <div class="myAlert-message">
            Message sending to queue
        </div>
        
        <button class="close" onclick="hideAlert()">
        <i class='bx bx-x'></i>
        </button>
        </div>
        <div id="myAlertProgress">
            <div id="myAlertBar"></div>
        </div>
        </div>

        <!-- for broadcast -->
        <!-- <div class="sms-broadcast">
            <form action="" method="post">
                <input type="checkbox" class="checkbox" id="checkbox" name="checkbox"> Broadcast Schedule: </input>
                <input type="datetime-local" class="schedule" id="schedule" name="schedule"></input>
                <input type="submit" class="broadcast-submit" value="Submit">
                
                <div class="broadcasttitle">
                    <label class="title">  Broadcast Title: </label>
                    <input type="text" class="titlebox" id="title" name="broadcast-msg"></input>
                </div>
            </form>
        </div> -->

        <!-- Recipient Table -->
        <div class="sms-recipient">
            <table id="recipientTable">
                <thead>
                    <tr>
                        <th>Recipient Name</th>
                        <th>Mobile Number</th>
                        <th>Edit/Delete</th>
                    </tr>
                </thead>
                <tbody id="recipientTableBody"></tbody>
            </table>
        </div>

        <script>
            // Retrieve the required elements
            const recipientInput = document.getElementById("recipientInput");
            const recipientTableBody = document.getElementById("recipientTableBody");

            const pageUrl = window.location.search.substring(1)
            const urlVariables = pageUrl.split('&')
            let recipients = [];
            checkGetParameter();

            // Store the recipients
            function checkGetParameter() {
                let contactNumbers = document.getElementById('hidden-numbers').value

                for (let i = 0; i < urlVariables.length; i++) {
                    let parameters = urlVariables[i].split('=');

                    if (parameters[0] === 'to' && parameters[1] !== undefined) {
                        let decodedParameter = decodeURIComponent(parameters[1])
                        let contact_details = decodedParameter.split(",");

                        contact_details.forEach(person => {
                            // Concatenate numbers in hidden-numbers
                            let contactNumber = person.split("~")[2];
                            contactNumbers += contactNumber + ';'
                            document.getElementById('hidden-numbers').value = contactNumbers

                            recipients.push(
                                {
                                    "id": person.split("~")[0],
                                    "name": person.split("~")[1],
                                    "contact_num": contactNumber
                                })
                        })
                    }
                }
                renderRecipientTable();
            }

            // Function to create a new row in the recipient table
            function createRecipientRow(recipient) {
                const row = document.createElement("tr");
                // row.setAttribute("data-recipient-id", recipient.id);

                // Recipient cell for Recipient Number <UNKNOWN USER>
                const recipientCell2 = document.createElement("td");
                recipientCell2.classList.add(0);
                recipientCell2.textContent = recipient.name;
                row.appendChild(recipientCell2);

                // Recipient cell for Mobile Number 
                const recipientCell = document.createElement("td");
                recipientCell.classList.add("contactNum");
                // recipientCell.textContent = "+63" + recipient.contact_num;
                recipientCell.textContent = recipient.contact_num;
                row.appendChild(recipientCell);

                // Action cell
                const actionCell = document.createElement("td");
                const editBtn = document.createElement("button");
                editBtn.textContent = "Edit";
                editBtn.addEventListener("click", () => editRecipient(recipient.id));
                actionCell.appendChild(editBtn);

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
            function addRecipient() {
                const recipientValue = "+63" + recipientInput.value.trim();
                let contactNumbers = document.getElementById('hidden-numbers').value

                // Concatenate numbers in hidden-numbers
                contactNumbers += recipientValue + ';'
                document.getElementById('hidden-numbers').value = contactNumbers

                if (recipientValue !== "") {
                    recipients.push({
                        "id": 0 ,
                        "name": 'Unknown', // TODO: If number is existing in contacts, name must be the contact's name
                        "contact_num": recipientValue
                    })

                    recipientInput.value = "";
                    renderRecipientTable();
                }
            }

            // Function to edit a recipient
            function editRecipient(recipientId) {
                const recipient = recipients.find(recipient => recipient.id === recipientId);

                if (recipient) {
                    // const newRecipientValue = prompt("Enter the new value for the recipient", recipient.name); //name
                    const newRecipientValue = prompt("Enter the new value for the recipient", recipient.contact_num); //contact number

                    if (newRecipientValue && newRecipientValue.trim() !== "") {
                        recipient.name = newRecipientValue.trim();
                        renderRecipientTable();
                    }
                }
            }

            //Function to delete a recipient
            function deleteRecipient(recipientId) {
                recipients = recipients.filter(recipient => recipient.id !== recipientId);
                renderRecipientTable();
            }

            //Function to send the data to queue
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

            /* attach a submit handler to the form */
            $("#send-message").submit(function (event) {

                /* stop form from submitting normally */
                event.preventDefault();

                /* get the action attribute from the <form action=""> element */
                const url = 'sendToQueue.php'
                
                /* Send the data using post with element id name and name2*/
                console.log(document.getElementById("message").value);
             
                var posting = $.post(url, {
                    data: recipients,
                    message: document.getElementById("message").value
                });

                /* Alerts the results */
                posting.done(function (response) {
                    console.log(response);
                    if (response === "Success") {
                        console.log("Hello");
                    }
                    // console.log(data)
                    // window.location.href = window.location;
                });
                posting.fail(function () {
                    console.log("Failed")
                });
            });
        </script>
        <script src="script.js"></script>
    </div>
</body>

</html>