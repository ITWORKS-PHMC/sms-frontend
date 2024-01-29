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
    <?php include("./layouts/header.php"); ?>
</head>

<body>
    <?php include("./layouts/navbar.php"); ?>
    <div class="content">
        <?php include("./layouts/menu.php"); ?>
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