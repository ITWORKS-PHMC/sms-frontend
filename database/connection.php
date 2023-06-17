<?php 
session_start();
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'officialsms'; //database name

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
?>

<?php
// function OpenConnection()
// {
    // $serverName = "uphmc-dc33";
    // $connectionOptions = array("Database"=>"ITWorksSMS",
    //     "Uid"=>"dalta", "PWD"=>"dontshareit");
    // $conn = sqlsrv_connect($serverName, $connectionOptions);
    // if($conn == false)
    //     die(FormatErrors(sqlsrv_errors()));

    // return $conn;

//}


//function ReadData()
//{
    // try
    // {
    //     $conn = OpenConnection();
    //     $tsql = "SELECT [CompanyName] FROM SalesLT.Customer";
    //     $getProducts = sqlsrv_query($conn, $tsql);
    //     if ($getProducts == FALSE)
    //         die(FormatErrors(sqlsrv_errors()));
    //     $productCount = 0;
    //     while($row = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC))
    //     {
    //         echo($row['CompanyName']);
    //         echo("<br/>");
    //         $productCount++;
    //     }
    //     sqlsrv_free_stmt($getProducts);
    //     sqlsrv_close($conn);
    // }
    // catch(Exception $e)
    // {
    //     echo("Error!");
    // }
//}
?>

