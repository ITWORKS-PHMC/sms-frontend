<?php
session_start();
// Redirect to the login page if not login 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$selectedCallerCode = $_SESSION['selectedCallerCode'];

// Database connection 
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
    <title>SMS</title>
    <link rel="sms icon" type="x-icon" href="img\logo.png">
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include("./nav/navbar.php"); ?>
    <div class="content">
        <?php include("./menu/menu.php"); ?>
        <div class="container">
            <h1> Changing of Caller Group </h1>
            <div class="callergroupSelection">
                <div class="callerGroupOption">
                    <p>Current Caller Group Code:
                        <?php echo "<u>" . $selectedCallerCode . "</u>"; ?>
                    </p>
                </div>
                <form action="callergroupSelection.php" method="post">
                    <div class="callerGroupOption">
                        <span> Want to change caller code? Choose here: </span>
                        <select id="callercode" name="callercode" class="callerOptionsBtn">
                            <?php
                            $sql = "SELECT caller_group_code FROM vw_caller_group_members WHERE username = ?";
                            $params = array($username);
                            $query = sqlsrv_prepare($conn, $sql, $params);

                            if (!sqlsrv_execute($query)) {
                                die(print_r(sqlsrv_errors(), true));
                            }

                            $callerGroupCode = "";
                            echo "<option value=''>Select Caller Group Code</option>";

                            while ($obj = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
                                $selected = ($obj['caller_group_code'] == $callerGroupCode) ? "selected" : "";
                                echo "<option value='" . $obj['caller_group_code'] . "' $selected>" . $obj['caller_group_code'] . "</option>";
                            }

                            sqlsrv_close($conn);
                            ?>
                        </select>
                        <div class="callerGroupOptionBtn">
                            <button type="submit" class="callerOptionsSubmitBtn">Change</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>