<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Home Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="stylesheet" href="style.css">
</head>

<body class="loggedin">
    <nav class="navtop">
        <div>
            <h1>PHMC</h1>
            <a href="home.php"><i class="fas fa-solid fa-house"></i>HOME</a>
            <a href="sms.php"><i class="fas fa-solid fa-message"></i>SMS</a>
            <a href="sms.php"><i class="fa-solid fa-magnifying-glass"></i>SEARCH</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </nav>

    <div class="content">
        <h2>Home Page</h2>
        <p>Welcome back, <?= $_SESSION['name'] ?>!</p>
    </div>
</body>

</html>