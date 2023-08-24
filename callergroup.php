<?php
    session_start();
    $username = $_SESSION['username'];
    
    if (!isset($_SESSION['username'])) {
        header("Location: login.php"); // Redirect to the login page if not login 
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Caller Group</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login">
        <form action="home.php" method="post">
            <span> Please Choose your Caller Group Code: </span>
            
            <?php 
                 //database connection 
                include("./database/connection.php");
                $conn = sqlsrv_connect($serverName, $connectionInfo);
                if ($conn === false) {
                    die(print_r(sqlsrv_errors(), true));
                }

                // Fetch caller_group_code for the given username
                $sql = "SELECT [caller_group_code] FROM vw_caller_group_members WHERE [username] = ?";
                $params = array($username);
                $query = sqlsrv_prepare($conn, $sql, $params);

                if (!sqlsrv_execute($query)) {
                    die(print_r(sqlsrv_errors(), true));
                }

                $callerGroupCode = "";
                while ($obj = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
                    $callerGroupCode .= "<option value='" . $obj ['caller_group_code'] . "'>" . $obj ['caller_group_code'] . "</option>";
                }

                sqlsrv_close($conn);
            ?>
            
            <select>
                <?php echo $callerGroupCode; ?>
            </select>

            <input type="submit" class="submit" value="Proceed">
        </form>
    </div>
</body>
</html>


