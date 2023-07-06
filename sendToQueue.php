<?php
//Database Connection 
include("./database/connection.php");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

function getData()
{
    $data = array();
    $data[2] = $_POST['hidden-numbers'];
    $data[3] = $_POST['message'];
    return $data;
}

// This is for inserting unregistered numbers messages to sms_queue table 
if (isset($_POST['submit-msg'])) {
    $info = getData();

    $num_arr = explode(";", rtrim($info[2], ";"));

    $values = "";

    for ($i = 0; $i < count($num_arr); $i++) {
        $values .= "('0', '$num_arr[$i]', '$info[3]')" . ",";
    }

    $values = rtrim($values, ',');


    $insert = "INSERT INTO [sms_queue] ([contact_id], [mobile_no], [sms_message]) VALUES $values";

    $stmt = sqlsrv_query($conn, $insert);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    header("Location: sms.php");
    die();
}