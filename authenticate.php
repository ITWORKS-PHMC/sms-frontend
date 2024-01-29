<?php
session_start();
?>
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
                <input type="login" id="username" name="username" placeholder="Username" required> <br>
                <input type="password" id="password" name="password" placeholder="Password" required> <br>
                <input type="checkbox" onclick="showPassword()">Show Password<br>

                <?php
                //database connection 
                include("./database/connection.php");

                $conn = sqlsrv_connect($serverName, $connectionInfo);
                if ($conn === false) {
                    die(print_r(sqlsrv_errors(), true));
                }

                //ldap connection 
                $domain = "uphmc.com.ph";
                $ldap_server = "ldap://uphmc-dc104.uphmc.com.ph";

                // Get the username and password from the user.
                $username = $_POST["username"];
                $password = $_POST["password"];

                // Create a new LDAP connection.
                $ldap = ldap_connect($ldap_server);

                // Bind to the domain.
                $bind = ldap_bind($ldap, "$username@$domain", $password);

                // If the bind was successful, display a message.
                if ($bind) {
                    //Checking if the username match the username in the database
                    $sql = "SELECT [username] FROM vw_caller_group_members WHERE [username] = ?";
                    $params = array($username);
                    $query = sqlsrv_prepare($conn, $sql, $params);

                    if (!sqlsrv_execute($query)) {
                        die(print_r(sqlsrv_errors(), true));
                    }

                    $result = sqlsrv_fetch_array($query);

                    if (is_array($result)) {
                        $_SESSION["username"] = $result['username'];
                        echo "Login Successfully!";

                        if (isset($_SESSION["username"])) {
                            header("Location: callergroup.php");
                        }
                    } else {
                        echo "Username does not exist in the database.";
                    }
                } else {
                    echo "Login Failed!";
                }
                // Close the LDAP connection.
                ldap_close($ldap);
                ?>

                <input type="submit" class="submit" value="Login">
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>