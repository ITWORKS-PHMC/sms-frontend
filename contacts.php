<?php
    include("./database/connection.php");

    $conn = sqlsrv_connect( $serverName, $connectionInfo);
    if( $conn === false ) {
        die( print_r( sqlsrv_errors(), true));
    }

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
    <title>SMS Unsent</title>
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


    <div class="container contacts">
        <h1> Contacts </h1>
        <table action="" method="post">
        <tr>
            <th> Contact ID </th>
            <th> Employee Number </th>
            <th> First Name </th>
            <th> Middle Name </th>
            <th> Last Name </th>
            <th> Phone Number </th>
        </tr>

        <?php 
        $tsql = "SELECT * from contacts";
        $stmt = sqlsrv_query($conn,$tsql);

        if($stmt == false){
            echo 'ERROR';
        }

        while($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
            echo "<tr>";
            echo "<th>"; echo $obj['contact_id']; echo "</th>"; 
            echo "<th>"; echo $obj['employee_no']; echo "</th>"; 
            echo "<th>"; echo $obj['contact_fname']; echo "</th>"; 
            echo "<th>"; echo $obj['contact_mname']; echo "</th>"; 
            echo "<th>"; echo $obj['contact_lname']; echo "</th>"; 
            echo "<th>"; echo $obj['mobile_no']; echo "</th>"; 
            echo "</tr>";
        }

        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
        ?>
        </table>
    </div>

</body>

</html>