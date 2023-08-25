<?php
// session_start();
// if (!isset($_SESSION['username'])) {
//     header("Location: login.php"); // Redirect to the login page if not login 
//     exit();
// }


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
    <title>SMS Inbox</title>
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

    <div class="container inbox">
        <h1> Inbox </h1>
        <table id="recipientTable" class="recipient-table">
            <form class="" action="" method="post">
                <tr>
                    <th> Mobile Number </th>
                    <th> Text Message </th>
                    <th> Stat </th>
                    <th> Date/Time Created </th>
                    <th> Created By </th>
                </tr>
                
                <?php
                $tsql = "SELECT * from sms_received";
                $stmt = sqlsrv_query($conn, $tsql);
                if ($stmt == false) {
                    echo 'ERROR';
                }

                while($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
                    echo "<tr>";
                    echo "<td>"; echo$obj['mobile_no']; echo "</td>"; 
                    echo "<td>"; echo wordwrap($obj['sms_message'], 35, "<br>\n", true); echo "</td>";
                    echo "<td>"; echo $obj['read_status']; echo "</td>"; 
                    echo "<td>"; echo $obj['date_received']->format('Y-m-d H:i:s'); echo "</td>"; 
                    echo "<td>"; echo $obj['sms_status']; echo "</td>"; 
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