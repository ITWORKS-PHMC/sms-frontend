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
        echo "Login failed!";
    }

    // Close the LDAP connection.
    ldap_close($ldap);

?>










