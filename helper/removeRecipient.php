<?php 
session_start();

if (isset($_POST['contactNum'])) {
    $response = array(
        "message" => "",
        "data" => $_POST['contactNum'],
        "status" => 200
    );
    
    unset($_SESSION["recipients"]["contacts"][$_POST['contactNum']]);
    
    echo json_encode($response);
} else if (isset($_POST['groupCode'])) {
    // TODO: unset group code here
} else {
    // TODO: Change URL to Production URL if available
    // header('Location: http://localhost/sms-frontend/sms.php');
    header('Location: http://phmc-sms/sms-frontend/sms.php'); // server phmc-sms01
    die();
}