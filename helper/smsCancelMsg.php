<?php
session_start();
$username = $_SESSION['username'];
$selectedCallerCode = $_SESSION['selectedCallerCode'];

// Database connection 
include("../database/connection.php");


if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $contact = $_POST['contact'];
    $mobile = $_POST['mobile'];
    $msg = $_POST['msg'];
    $stat = '0';
    $date = $_POST['date'];
    $created = $_POST['created'];
 


    $conn = sqlsrv_connect($serverName, $connectionInfo);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $sql = "INSERT INTO [sms_cancelled] ([sms_id], [contact_id], [mobile_no], [sms_message], [stat], [date_created], [created_by], [date_cancelled], [cancelled_by]) 
    VALUES ('$id', '$contact', '$mobile', '$msg', '$stat', '$date', '$created', getdate(), '$username')
    DELETE FROM [sms_queue] WHERE [sms_id] = '$id';";
    $stmt = sqlsrv_query($conn, $sql);

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Record updated successfully";

    sqlsrv_close($conn);
}
?>