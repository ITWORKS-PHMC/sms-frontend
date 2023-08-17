<?php
session_start();
$username = $_SESSION['username'];

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

<!DOCTYPE html>
<html>
<head>
    <title>Caller Group</title>
</head>
<body>
    <form action="home.php" method="post">
        <span> Caller Group Code: </span>

        <select>
             <?php echo $callerGroupCode; ?>
        </select>

        <input type="submit" value="Proceed">
    </form>
</body>
</html>


