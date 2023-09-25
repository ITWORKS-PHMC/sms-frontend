<?php
	$serverName = "uphmc-dc33"; //serverName\instanceName
	$connectionInfo = array("Database"=>"ITWorksSMS", //database
							"UID"=>"dalta",           //user 
							"PWD"=>"dontshareit",      //password
							"Encrypt" => true, // Enable encryption
    						"TrustServerCertificate" => true // Trust the server certificate
						);
						
	$conn = sqlsrv_connect($serverName,$connectionInfo);
?>
