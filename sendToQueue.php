<?php
//Database Connection 
include("./database/connection.php");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// This is for inserting numbers messages to sms_queue table 
if (isset($_POST['data'])) {
    $data = $_POST['data'];
    $messages = str_split($_POST['message'], 140);
    $messages_count = count($messages);

    $currentDateTime = date("Y-m-d H:i:s");

    $values = "";
    for ($i = 0; $i < count($data); $i++) {
        $contactId = $data[$i]['id'] > 0 ? $data[$i]['id'] : 0;
        for ($j = 0; $j < $messages_count; $j++) {
            $cur = $j + 1;
            $values .= "('{$contactId}', '{$data[$i]['contact_num']}', '$messages[$j] Part $cur of $messages_count', '$currentDateTime', '$currentDateTime'),";
        }
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