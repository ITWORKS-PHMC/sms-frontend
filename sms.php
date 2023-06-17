<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SMS</title>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    
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
            <a href="smsbroadcastsent.php"><button type="button" class="sms button"> Broadcast Sent Messages </button></a>
            <a href="smsbroadcastunsent.php"><button type="button" class="sms button"> Broadcast Unsent Messages </button></a>
        </div>
      
    </div>

    <div class="vertical-line"></div>
    
    <div class="container new-message">
        <h1> Create New Messages </h1>   
        <form action="" method="post">
            <div class="contact">
                <div class="top">
                    <!-- <img src="./img/ph-flag.png" alt="ph-flag"><span class="country">+639</span> -->
                    <span class="country">+639</span>
                    <input type="number" name="number" id="contact-input" onKeyPress="if(this.value.length==13) return false;" placeholder="Input number here..">
                    <button type="button" class="add-button" onclick="addContact()">Add to Recipient</button>
                </div>

                <a href="contacts.php" class="contacts-button">Contacts</a>
                
            </div>
            
            <div class="sms-broadcast">
                <form action="" method="post">
                    <input type="checkbox" class="checkbox" id="checkbox" name="checkbox"> Broadcast Schedule: </input>
                    <input type="datetime-local" class="schedule" id="schedule" name="schedule"></input>
                    <input type="submit" class="submit" value="Submit">
                    
                    <div class="broadcasttitle">
                        <label class="title">  Broadcast Title: </label>
                        <input type="text" class="titlebox" id="title" name="title"></input>
                    </div>
                </form>
            </div>

            <div class="sms-message">
                <textarea id="message" name="message" rows="5" placeholder="Type something here.." required maxlength="140"></textarea>
                <p id="result"></p>
                <input type="hidden" id="hidden-numbers" name="numbers" >
                <input type="submit" class="submit" name="submit" placeholder="Send here" value="Send" required>
            </div>
        </form>


        <script src="script.js">
            /* attach a submit handler to the form */
            $("#sendMessage").submit(function(event) {

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
            posting.done(function(data) {
                console.log("Success")
            });
            posting.fail(function() {
                console.log("Failed")
            });
            });
        </script>


        <?php
        //for input db
        /*
        $number = $_POST['number'];
        $mytext = $_POST['my-text'];
        
        $conn = new mysqli('localhost','root','','phplogin');
        
        if ($conn->connect_error){
            die('Connection Failed : '.$conn->connect_error);
        }else{
            $stmt = $conn->prepare("insert into messages(number, mytext) values(?, ?)");
            $stmr->bind_param("is",$number, $mytext);
            $stmt->execute();
            echo "Message sent successfully...";
            $stmt->close();
        }
        */
        if (isset($_POST['submit'])) {
            // echo "+639"; 
            $number = $_POST['numbers'];
            // echo "<fieldset> <legend> Recipient </legend>";
            echo $number;
            //echo " </fieldset>";
        }
        ?>
    </div>
    <!-- <script src="script.js"></script> -->
</body>

</html>