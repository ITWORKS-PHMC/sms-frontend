<?php
session_start();
$username = $_SESSION['username'];
$selectedCallerCode = $_SESSION['selectedCallerCode'];

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
    $user = $username;
    $pattern = "/(\+639)(\d{2})/";

    $values = "";
    // print_r($data);
    for ($i = 0; $i < count($data); $i++) {
        $contactId = $data[$i]['id'] > 0 ? $data[$i]['id'] : 0;
        for ($j = 0; $j < $messages_count; $j++) {
            $contact_num = $data[$i]['contact_num'];
            $firstFiveDigits = substr($contact_num, 0, 6);

            if (!preg_match($pattern, $firstFiveDigits, $matches)) {
                //* Number will not add in insert string query
                // TODO
                // $error = "Prefix number does not exist in the database.";
                echo "Incorrect Prefix. Invalid Number";
                echo '<script>alert("No match found!");</script>';
            }

            $sql = "SELECT [prefix_number] FROM prefix_numbers WHERE [prefix_number] = ?";
            $params = array($firstFiveDigits);
            $query = sqlsrv_prepare($conn, $sql, $params);

            if (!sqlsrv_execute($query)) {
                die(print_r(sqlsrv_errors(), true));
            }

            $result = sqlsrv_fetch_array($query);
            $cur = $j + 1;
            if (is_array($result)) {
                // $values .= "('{$contactId}', '{$contact_num}', '$messages[$j] Part $cur of $messages_count', '$currentDateTime', '$currentDateTime'),";
                $values .= "('{$contactId}', '{$contact_num}', '$messages[$j]...<$selectedCallerCode>', '$currentDateTime', '$user', '$currentDateTime'),";
            }
        }
    }

    $values = rtrim($values, ',');

    $insert = "INSERT INTO [sms_queue] ([contact_id], [mobile_no], [sms_message], [date_created], [created_by], [date_resend]) VALUES $values";
    $stmt = sqlsrv_query($conn, $insert);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    echo "Success";
}
?>