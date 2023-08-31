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

    //getting the prefix 
    $valuesPrefix = rtrim($values, ',');
    $pattern = "/\+639\d{2}/";

    if (preg_match($pattern, $valuesPrefix, $matches)) {
        $prefix_number = $matches[0];
        $sql = "SELECT [prefix_number] FROM prefix_numbers WHERE [prefix_number] = ?";
        $params = array($prefix_number);
        $query = sqlsrv_prepare($conn, $sql, $params);

        if (!sqlsrv_execute($query)) {
            die(print_r(sqlsrv_errors(), true));
        }

        $result = sqlsrv_fetch_array($query);

        if (is_array($result)) {
            echo "Prefix number is valid! ";
            $insert = "INSERT INTO [sms_queue] ([contact_id], [mobile_no], [sms_message], [date_created], [date_resend]) VALUES $values";
            $stmt = sqlsrv_query($conn, $insert);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
            echo "Success";
        } else {
            // //TODO
            // // the values doesn't have sms_id this is required 
            // $error = "Prefix number does not exist in the database.";
            // $unsent = "INSERT INTO [sms_unsent] ([sms_id], [contact_id], [mobile_no], [sms_message], [date_created], [date_unsent]) VALUES ($values)";
            // $stmt = sqlsrv_query($conn, $unsent);
            // if ($stmt === false) {
            //     die(print_r(sqlsrv_errors(), true));
            // }
            echo "Prefix number does not exist in the database.";
            //alert "Message unsent! <mobile_no> prefix is not valid please contact IT Department local: 458
        }

    } else {
        // //TODO
        // // the values doesn't have sms_id this is required 
        // $error = "Prefix number does not exist in the database.";
        // $cancelled = "INSERT INTO [sms_cancelled] ([sms_id], [contact_id], [mobile_no], [sms_message], [date_created], [date_unsent]) VALUES ($values)";
        // $stmt = sqlsrv_query($conn, $cancelled);
        // if ($stmt === false) {
        //     die(print_r(sqlsrv_errors(), true));
        // }
        echo "Phone number is not valid";
        //alert "Message cancelled! <mobile_no> is not valid please contact IT Department local: 458
    }
}
?>