<!-- Programmer: Chasey Larrisse V. Elizarde
Project: SMS 
Description: Frontend SMS  -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SMS</title>
    <link rel="sms icon" type="x-icon" href="img\logo.png">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="style.css">
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