<?php
session_start();
// Redirect to the login page if not login 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];

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
    <div class="login">
        <div class="callergroupSelection">
            <form action="callergroupSelection.php" method="post">
                <div class="callerGroupOption">
                    <span> Caller Group Code: </span>
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
                </div>
                <div class="callerGroupOptionBtn">
                    <button type="submit" class="callerOptionsSubmitBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>