<?php
//Database Connection 
include("./database/connection.php");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// This is for inserting unregistered numbers messages to sms_queue table 
if (isset($_POST['data'])) {
    $data = $_POST['data'];
    $message = $_POST['message'];

    $currentDateTime = date("Y-m-d H:i:s");

    $values = "";
    for ($i = 0; $i < count($data); $i++){
        $values .= "('{$data[$i]['id']}', '{$data[$i]['contact_num']}', '$message', '$currentDateTime', '$currentDateTime'),";
    }

    $values = rtrim($values, ',');
    $insert = "INSERT INTO [sms_queue] ([contact_id], [mobile_no], [sms_message], [date_created],[date_resend]) VALUES $values";

    $stmt = sqlsrv_query($conn, $insert);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    echo "Success";
}

?>
