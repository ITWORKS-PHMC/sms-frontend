<?php
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
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous"> -->

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
            <a href="smsqueued.php"><button type="button" class="sms button"> Queued Messages </button></a>
            <a href="smsinbox.php"><button type="button" class="sms button"> Inbox </button></a>
            <a href="smssent.php"><button type="button" class="sms button"> Sent Messages </button></a>
            <a href="smsunsent.php"><button type="button" class="sms button"> Unsent Messages </button></a>
            <a href="smscancelled.php"><button type="button" class="sms button"> Cancelled Messages </button></a>
            <a href="smsbroadcastsent.php"><button type="button" class="sms button"> Broadcast Sent Messages
                </button></a>
            <a href="smsbroadcastunsent.php"><button type="button" class="sms button"> Broadcast Unsent Messages
                </button></a>
        </div>
    </div>

    <!-- <div class="vertical-line"></div> -->

    <div class="container new-message">
        <h1> Create New Messages </h1>
        <!-- <form onsubmit="return sendToQueue(event)"> -->
        <form action="" method="post">
            <div class="contact">
                <div class="top">
                    <span class="country">+63</span>
                    <input type="number" id="recipientInput" name="number"
                        onKeyPress="if(this.value.length==10) return false;" placeholder="Input number here..">
                    <button type="button" id="addToRecipientBtn" class="add-button">Add to Recipient</button>
                </div>
                <a href="contacts.php" class="contacts-button">Contacts</a>
            </div>
    
            <div class="sms-message">
                <textarea id="message" name="message" rows="5" placeholder="Type something here.." required
                    maxlength="140"></textarea>
                <p id="result"></p>
                <input type="hidden" id="hidden-numbers" name="numbers">
                <input type="submit" id="submit-msg" class="submit" name="submit-msg" placeholder="Send here"
                    value="Send" required>
            </div>
        </form>

        <!-- for broadcast -->
        <!-- <div class="sms-broadcast">
            <form action="" method="post">
                <input type="checkbox" class="checkbox" id="checkbox" name="checkbox"> Broadcast Schedule: </input>
                <input type="datetime-local" class="schedule" id="schedule" name="schedule"></input>
                <input type="submit" class="broadcast-submit" value="Submit">
                
                <div class="broadcasttitle">
                    <label class="title">  Broadcast Title: </label>
                    <input type="text" class="titlebox" id="title" name="title"></input>
                </div>
            </form>
        </div> -->

        <!-- Recipient Table -->
        <div class="sms-recipient">
            <table id="recipientTable">
                <thead>
                    <tr>
                        <th>Recipient</th>
                        <th>Edit/Delete</th>
                    </tr>
                </thead>
                <tbody id="recipientTableBody"></tbody>
            </table>
        </div>

    <script>
    // Retrieve the required elements
    const recipientInput = document.getElementById("recipientInput");
    const addToRecipientBtn = document.getElementById("addToRecipientBtn");
    const recipientTableBody = document.getElementById("recipientTableBody");

    // Store the recipients
    let recipients = [];

    // Function to generate a unique ID for each recipient
    function generateUniqueId() {
        return Math.random().toString(36).substr(2, 9);
    }

    // Function to create a new row in the recipient table
    function createRecipientRow(recipient) {
        const row = document.createElement("tr");
        row.setAttribute("data-recipient-id", recipient.id);

        // Recipient cell
        const recipientCell = document.createElement("td");
        recipientCell.classList.add("contactNum");
        recipientCell.textContent = "+63" + recipient.name;
        row.appendChild(recipientCell);

        // Action cell
        const actionCell = document.createElement("td");
        const editBtn = document.createElement("button");
        editBtn.textContent = "Edit";
        editBtn.addEventListener("click", () => editRecipient(recipient.id));
        actionCell.appendChild(editBtn);

        const deleteBtn = document.createElement("button");
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
        const recipientValue = recipientInput.value.trim();

        if (recipientValue !== "") {
            const recipient = {
                id: generateUniqueId(),
                name: recipientValue
            };

            recipients.push(recipient);
            recipientInput.value = "";
            renderRecipientTable();
        }
    }

    // Function to edit a recipient
    function editRecipient(recipientId) {
        const recipient = recipients.find(recipient => recipient.id === recipientId);

        if (recipient) {
            const newRecipientValue = prompt("Enter the new value for the recipient", recipient.name);

            if (newRecipientValue && newRecipientValue.trim() !== "") {
                recipient.name = newRecipientValue.trim();
                renderRecipientTable();
            }
        }
    }

    // Function to delete a recipient
    function deleteRecipient(recipientId) {
        recipients = recipients.filter(recipient => recipient.id !== recipientId);
        renderRecipientTable();
    }

    // Add click event listener to the "Add to Recipient" button
    addToRecipientBtn.addEventListener("click", addRecipient);

    // Add click event listener to the submit button
    submitBtn.addEventListener("click", function() {
        const checkedRecipients = Array.from(document.querySelectorAll('input[name="recipients[]"]:checked'));
        const recipientValues = checkedRecipients.map(recipient => recipient.value);
    // Send recipientValues array to the server using AJAX or submit the form

    // AJAX request:
    // const data = { recipients: recipientValues };
    // fetch('your-php-file.php', {
    //     method: 'POST',
    //     body: JSON.stringify(data)
    // }).then(response => response.json())
    //   .then(data => console.log(data))
    //   .catch(error => console.log(error));

    console.log(recipientValues);
    });

    function sendToQueue(e) {
        e.preventDefault();

        let contactNums = document.getElementsByClassName("contactNum")
        
        let contactNumsArray = [];
        for (let i = 0; i < contactNums.length; i++) {
            // console.log(contactNums[i].innerHTML)
            contactNumsArray.push(contactNums[i].innerHTML)
        }

        let dictionary = {
            "contactNumbers": contactNumsArray,
            "message": document.getElementById("message")
        }

    
        // console.log(JSON.parse(dictionary))
        console.log(contactNumsArray) //echo phone number

        // AJAX request:
        // fetch('https://localhost:7277/Sms/QueueMessage', {
        //     method: 'POST',
        //     body: JSON.stringify(dictionary)
        // }).then(response => response.json())
        //   .then(data => console.log(data))
        
        // data: {contactNumsArray : JSON.stringify( contactNumsArray )},
        // console.log(res + JSON.stringify(contactNumsArray) );

        $.ajax({
            type:"post",
            data: {ajax: 1, contactNumsArray : JSON.stringify(contactNumsArray)},
            dataType: 'json',                
            success: function(res) {
                $('#res').text(+ JSON.stringify(contactNumsArray) );
            }
        });

    }

    </script>

    <script src="script.js">
        /* attach a submit handler to the form */
        $("#sendMessage").submit(function (event) {

            /* stop form from submitting normally */
            event.preventDefault();

            /* get the action attribute from the <form action=""> element */
            var $form = $(this),
                url = $form.attr('action');

            /* Send the data using post with element id name and name2*/
            var posting = $.post(url, {
                number: $('#number').val(),
                message: $('#message').val()
            });

            /* Alerts the results */
            posting.done(function (data) {
                console.log("Success")
            });
            posting.fail(function () {
                console.log("Failed")
            });
        });
    </script>

    <?php
        // $contactNumArray = json_decode($_POST['contactNumsArray']);
        // print_r($contactNumArray);

        // if(isset($_POST['ajax']) && isset($_POST['contactNumsArray'])){
        //     echo $_POST['contactNumsArray'];
        //     exit;
        // }

        //HandLe AJAX request
        if( isset($_POST['ajax']) && isset($_POST['checked'])){
            $checked_arr = $_POST['check'];

        echo json_decode($checked_arr);  
        exit;
        }

        //Database Connection 
        include("./database/connection.php");
        $conn = sqlsrv_connect( $serverName, $connectionInfo);
        if( $conn === false ) {
             die( print_r( sqlsrv_errors(), true));
        }
       
        function getData(){
            $data = array();
            $data[2] = $_POST['number'];
            $data[3] = $_POST['message'];
            return $data;    
        }

        // This is for inserting unregistered numbers to sms_queue table 
        if(isset($_POST['submit-msg'])){
            $info = getData();
            $insert = "INSERT INTO [sms_queue] ([contact_id], [mobile_no],
            [sms_message]) VALUES ('0', '$info[2]', '$info[3]')";
        
            $stmt = sqlsrv_query($conn, $insert);
            if( $stmt === false ) {
                die( print_r( sqlsrv_errors(), true));
           }
        }
    ?>

    </div>
</body>

</html>