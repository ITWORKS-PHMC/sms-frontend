<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        $selectedCallerCode = $_POST['callercode'];

        $_SESSION['selectedCallerCode'] = $selectedCallerCode;

        header("Location: home.php");
        exit();
    }
?>