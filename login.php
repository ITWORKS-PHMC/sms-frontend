<!-- Programmer: Chasey Larrisse V. Elizarde
Project: SMS 
Date: May 8, 2023
Description: Frontend SMS  -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="login">
        <form action="authenticate.php" method="post">
            <h2> Perpetual Help Medical Center - Las Piñas Hospital </h2>

            <div class="signin">
                <input type="login" class="username" name="username" placeholder="Username" id="username" required>

                <input type="password" class="password" name="password" placeholder="Password" id="password" required>

                <input type="submit" class="login-submit" value="Login">
            </div>
        </form>
    </div>
</body>

</html>
