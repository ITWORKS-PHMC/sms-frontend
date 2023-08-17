<?php
session_start();
// if (!isset($_SESSION['loggedin'])) {
//     header('Location: login.php');
//     exit;
// }
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Home Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <ul class="navigation">
        <li class="navlistleft">PHMC SMS</li>
        <li class="navlist"><a href="logout.php">LOGOUT</a></li>
        <li class="navlist"><a href="contacts.php">CONTACTS</a></li>
        <li class="navlist"><a href="sms.php">SMS</a></li>
        <li class="navlist"><a class="active" href="home.php">HOME</a></li>
    </ul>

    <div class="Home">
        <h3 class="title" id="title"> Perpetual Help Medical Center - Las Piñas City </h3>
        <p class="datetime" id="datetime"></p> <br>
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