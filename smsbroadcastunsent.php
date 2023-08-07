<?php
// //If not loggedIn cannot passthrough
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
    <title>SMS Broadcast Unsent</title>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    
   <!-- bootstrap -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
   
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <ul class="navigation">
        <li class="navlistleft">PHMC SMS</li>
        <li class="navlist"><a href="logout.php">LOGOUT</a></li>
        <li class="navlist"><a href="contacts.php">CONTACTS</a></li>
        <li class="navlist"><a href="sms.php">SMS</a></li>
        <li class="navlist"><a class="active" href="home.php">HOME</a></li>
    </ul>

    <ul class="menu">
        <li class="menulabel"> MENU BUTTON </li>
        <li class="menulist"><a class="active" href="sms.php">Create New Message</a></li>
        <li class="menulist"><a href="smsqueued.php">Queue Messages</a></li>
        <li class="menulist"><a href="smsinbox.php">Inbox</a></li>
        <li class="menulist"><a href="smssent.php">Sent Messages</a></li>
        <li class="menulist"><a href="smsunsent.php">Unsent Messages</a></li>
        <li class="menulist"><a href="smscancelled.php">Cancelled Messages </a></li>
        <li class="menulist"><a href="smsbroadcastsent.php">Broadcast Sent Messages</a></li>
        <li class="menulist"><a href="smsbroadcastunsent.php">Broadcast Unsent Messages</a></li>
    </ul>

    <div class="container broadcast-unsent">
        <h1> Broadcast Unsent Messages </h1>
        <form action="" method="post">
        </form>
    </div>

</body>

</html>