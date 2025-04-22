<?php

$servername = "localhost";
$username = "root"; // Database username
$password = ""; // Database password        
$dbname = "example"; // Database name
$port= NULL; // Database port, NULL for default
$soket = NULL; // Database socket, NULL for default
// Create connection    
$connect = mysqli_connect($servername, $username, $password, $dbname, $port, $soket);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error($connect));
} else {
    mysqli_set_charset($connect, "utf8"); // Set character set to utf8mb4
}
?>
