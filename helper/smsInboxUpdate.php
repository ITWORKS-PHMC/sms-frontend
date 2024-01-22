<?php
// Database connection 
include("../database/connection.php");

if (isset($_POST['id'])) {
    $conn = sqlsrv_connect($serverName, $connectionInfo);
    
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $sql = "UPDATE sms_received SET read_status = 1 WHERE sms_received_id = '{$_POST['id']}'";
    $stmt = sqlsrv_query($conn, $sql);

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Record updated successfully";

    sqlsrv_close($conn);
}
?>