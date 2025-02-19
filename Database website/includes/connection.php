<?php

$serverName = "LAPTOP-7B5GV2GT"; // Change to your server name
$connectionOptions = array(
    "Database" => "E-commerceApp", // Replace with your database name
    "Uid" => "",          // Replace with your database username
    "PWD" => ""           // Replace with your database password
);

// Establish the connection
$con = sqlsrv_connect($serverName, $connectionOptions);

if ($con === false) {
    die(print_r(sqlsrv_errors(), true));
}

?>