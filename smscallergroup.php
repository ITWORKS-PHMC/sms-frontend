<?php
session_start();
$username = $_SESSION['username'];
$selectedCallerCode = $_SESSION['selectedCallerCode'];

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

<body>
    <?php include("./nav/navbar.php"); ?>
    <?php include("./menu/menu.php"); ?>

    <div class="container colorgroup">
        <h1> Changing of Caller Group </h1>
        <p>Selected Caller Code: <?php echo $selectedCallerCode; ?></p>
        
        <form action="callergroupSelection.php" method="post">
            <span> Want to change caller code? Choose here: </span>
            <select id="callercode" name="callercode">
                <?php
                //database connection 
                include("./database/connection.php");
                $conn = sqlsrv_connect($serverName, $connectionInfo);
                if ($conn === false) {
                    die(print_r(sqlsrv_errors(), true));
                }

                $sql = "SELECT [caller_group_code] FROM vw_caller_group_members WHERE [username] = ?";
                $params = array($username);
                $query = sqlsrv_prepare($conn, $sql, $params);

                if (!sqlsrv_execute($query)) {
                    die(print_r(sqlsrv_errors(), true));
                }

                $callerGroupCode = "";
                while ($obj = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
                    echo "<option value='" . $obj['caller_group_code'] . "'>" . $obj['caller_group_code'] . "</option>";
                }

                sqlsrv_close($conn);
                ?>
            </select>

            <button type="submit" class="submit">Change</button>
        </form>
    </div>
</body>

</html>