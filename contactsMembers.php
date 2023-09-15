<?php
//database connection 
include("./database/connection.php");

$callerGroupCode = $_POST['caller_group_code'];

// Prepare a SQL query to fetch data from vw_caller_group_members
$sql = "SELECT caller_group_code, caller_group_name, CONCAT(contact_lname, ' ', contact_fname, ' ', contact_mname) AS full_name, mobile_no FROM vw_caller_group_members WHERE caller_group_code = ?";
$params = array($callerGroupCode);
$query = sqlsrv_query($conn, $sql, $params);

if (!$query) {
    die(print_r(sqlsrv_errors(), true));
}

$membersData = array();

while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
    // Add each row to the result array
    $membersData[] = $row;
}

sqlsrv_close($conn);
header('Content-Type: application/json');
echo json_encode($membersData);
?>
