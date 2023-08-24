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
    <?php include("./nav/navbar.php"); ?>
    <?php include("./menu/menu.php"); ?>

    <div class="container broadcast-unsent">
        <h1> Broadcast Unsent Messages </h1>
        <div class="sms-recipient">
            <table id="recipientTable" class="recipient-table">
                    <tr>
                        <th> Date/Time of Error </th>
                        <th> Option </th>
                    </tr>
                    <tr>
                        <th> 5/9/2023 11:41:50 AM </th>
                        <th> <a href="smsbroadcastunsent.php"><button type="button" class="sms button"> View </button></a> </th>
                    </tr>
                <tbody id="recipientTableBody"></tbody>
            </table>
        </div>
    </div>

</body>
</html>