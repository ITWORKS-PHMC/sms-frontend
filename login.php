<!-- Programmer: Chasey Larrisse V. Elizarde
Project: SMS 
Description: Frontend SMS  -->

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("./layouts/header.php"); ?>
</head>

<body>
    <div class="login">
        <img src="img\logo_login.png" alt="logo_header" class="imgLogin">
        <h2 style="font-family:times"> Text Messaging System </h2>
        <div class="signin">
            <form action="authenticate.php" method="post">
                <input type="login" id="username" name="username" placeholder="Username" required>
                <input type="password" id="password" name="password" placeholder="Password" required>

                <input type="checkbox" id="checkbox" onclick="showPassword()">Show Password

                <input type="submit" class="submit" value="Login">
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>