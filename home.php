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
        <div class="container home">
            <h3 class="title" id="title"> Perpetual Help Medical Center - Las Piñas City </h3>
            <p class="datetime" id="datetime"></p>
            <div class="userCredentials">
                <div class="dashboardUsername">
                    <p> Good day,
                        <?php echo str_replace('.', ' ', $username . '!'); ?>
                </div>
                <div class="dashboardCallerCode">
                    <p> Selected Caller Code:
                        <?php echo "<u>" . $selectedCallerCode . "</u>"; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to update the date and time
        function updateDateTime() {
            const datetimeElement = document.getElementById('datetime');
            const now = new Date();

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric', timeZoneName: 'short' };
            const formattedDateTime = now.toLocaleString('en-US', options);

            datetimeElement.textContent = formattedDateTime;
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
</body>

</html>