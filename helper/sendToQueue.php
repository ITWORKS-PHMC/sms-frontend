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
    date_default_timezone_set('Asia/Manila');
    $currentDateTime = date("Y-m-d H:i:s");
    $user = $username;
    $pattern = "/(\+639)(\d{2})/";
    $response = array(
        "message" => "",
        "invalid_num" => [],
        "valid_num" => []
    );

    $values = "";
    for ($i = 0; $i < count($data); $i++) {
        $contactId = $data[$i]['id'] > 0 ? $data[$i]['id'] : 0;
        for ($j = 0; $j < $messages_count; $j++) {
            $contact_num = $data[$i]['contact_num'];
            $firstFiveDigits = substr($contact_num, 0, 6);

            if (!preg_match($pattern, $firstFiveDigits, $matches)) {
                $response['message'] = "Invalid Number";
                array_push($response['invalid_num'], $contact_num);
            }

            $sql = "SELECT [prefix_number] FROM prefix_numbers WHERE [prefix_number] = ?";
            $params = array($firstFiveDigits);
            $query = sqlsrv_prepare($conn, $sql, $params);

            if (!sqlsrv_execute($query)) {
                die(print_r(sqlsrv_errors(), true));
            }

            $result = sqlsrv_fetch_array($query);
            $cur = $j + 1;

            //TODO for unsend and inbox - if message is from unsend and inbox dateresend and modifiedby must update depending on user who is currently log-on  
            if (is_array($result)) {
                $insert_msg = $messages[$j];

                if ($messages_count > 1) {
                    $insert_msg .= " [$cur/$messages_count]";
                }

                if ($cur === $messages_count) {
                    $insert_msg .= "...<$selectedCallerCode>";
                }

                $values .= "('{$contactId}', '{$contact_num}', '$insert_msg', '$currentDateTime', '$user', '$currentDateTime'),";

                array_push($response['valid_num'], $contact_num);
            } else {
                $response['message'] = "Invalid Prefix";
                array_push($response['invalid_num'], $contact_num);
            }
        }
    }

    if (empty($response['invalid_num'])) {
        $response['message'] = "Success";
        $values = rtrim($values, ',');

        $insert = "INSERT INTO [sms_queue] ([contact_id], [mobile_no], [sms_message], [date_created], [created_by], [date_resend]) VALUES $values";
        $stmt = sqlsrv_query($conn, $insert);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
    }
    echo json_encode($response);
}
?>