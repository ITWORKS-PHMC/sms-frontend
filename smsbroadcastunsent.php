<?php
session_start();
// Redirect to the login page if not login 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$selectedCallerCode = $_SESSION['selectedCallerCode'];

// Database connection 
include("./database/connection.php");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!DOCTYPE html>
<html>

<head>
    <?php include("./layouts/header.php"); ?>
</head>

<body>
    <?php include("./layouts/navbar.php"); ?>
    <div class="content">
        <?php include("./layouts/menu.php"); ?>
        <div class="container broadcast-unsent">
            <h1> Broadcast Unsent Messages </h1>
            <table id="recipientTable" class="recipient-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mobile Number</th>
                        <th>Text Message</th>
                        <th>Date & Time Created</th>
                        <th>Cancelled By</th>
                        <th>Date & Time Cancelled</th>
                    </tr>
                </thead>
                <tbody id="recipientTableBody"></tbody>
            </table>
        </div>
    </div>
</body>

</html>