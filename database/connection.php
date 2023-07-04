<?php
	$serverName = "uphmc-dc33"; //serverName\instanceName
	$connectionInfo = array("Database"=>"ITWorksSMS", //database
							"UID"=>"dalta",           //user 
							"PWD"=>"dontshareit");    //password

	$conn = sqlsrv_connect($serverName,$connectionInfo);
?>