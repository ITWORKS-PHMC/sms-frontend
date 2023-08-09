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
            <h2> Perpetual Help Medical Center - Las Piñas Hospital </h2>
            <div class="signin">
                <form action="authenticate.php" method="post">
                    <label for="username">Username: </label>
                    <input type="text" id="username" name="username" required><br>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required><br>
                    <input type="checkbox" onclick="showPassword()">Show Password
                    <?php
                        $domain = "uphmc.com.ph";
                        $ldap_server = "ldap://uphmc-dc103.uphmc.com.ph";

                        // Get the username and password from the user.
                        $username = $_POST["username"];
                        $password = $_POST["password"];

                        // Create a new LDAP connection.
                        $ldap = ldap_connect($ldap_server);

                        // Bind to the domain.
                        $bind = ldap_bind($ldap, "$username@$domain", $password);

                        // If the bind was successful, display a message.
                        if ($bind) {
                            echo "Login successful!";
                            header('Location: home.php');
                        } else {
                            echo "Login Failed!";
                        }

                        // Close the LDAP connection.
                        ldap_close($ldap);

                    ?>
                    <input type="submit" value="Login">
                </form> 
            </div>
        </div>
        <script src="script.js"></script>
    </body>
</html>










