<?php 
session_start();

if (isset($_POST['contactNum'])) {
    $response = array(
        "message" => "",
        "data" => $_POST['contactNum'],
        "status" => 200
    );
    
    unset($_SESSION['recipients'][$_POST['contactNum']]);
    
    echo json_encode($response);
} else {
    // TODO: Change URL to Production URL if available
    header('Location: http://localhost/sms-frontend/sms.php');
    die();
}