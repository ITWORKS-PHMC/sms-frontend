<?php
//database connection 
include("./database/connection.php");

$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SMS Sent</title>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    
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
            <a href="smsbroadcastsent.php"><button type="button" class="sms button"> Broadcast Sent Messages </button></a>
            <a href="smsbroadcastunsent.php"><button type="button" class="sms button"> Broadcast Unsent Messages </button></a>
        </div>
    </div>

    <div class="container sent">
        <h1> Sent Messages </h1>
        <table action="" method="post">
            <form class="" action="" method="post">
                <tr>
                    <!-- <th> Contact ID </th> -->
                    <th> Mobile Number </th>
                    <th> Text Message </th>
                    <!-- <th> Stat </th>
                    <th> Date/Time Created </th>
                    <th> Created By </th> -->
                </tr>


                <?php
                $tsql = "SELECT * from sms_sent";
                $stmt = sqlsrv_query($conn, $tsql);

                if ($stmt == false) {
                    echo 'ERROR';
                }

                while($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
                    echo "<tr>";
                    // echo "<td>"; echo $obj['contact_id']; echo "</td>"; 
                    echo "<td>"; echo $obj['mobile_no']; echo "</td>"; 
                    echo "<td>"; echo $obj['sms_message']; echo "</td>"; 
                    // echo "<td>"; echo $obj['stat']; echo "</td>"; 
                    // echo "<td>"; echo $obj['date_created']; echo "</td>"; 
                    // echo "<td>"; echo $obj['created_by']; echo "</td>"; 
                    echo "</tr>";
                }
                sqlsrv_free_stmt($stmt);
                sqlsrv_close($conn);
                ?>

            </form>
        </table>
    </div>

</body>

</html>