<?php
    // if($open_connect !=1){
    //     die(header("Location: ../Register_system/login.php")); // Redirect to login page with error)
    // }   
    $servername = "localhost";
    $username = "root"; // Database username
    $password = ""; // Database password        
    $dbname = "report"; // Database name
    // Create connection    
    $connect = mysqli_connect($servername, $username, $password, $dbname);
    mysqli_set_charset($connect, "utf8");
    // Check connection
    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error($connect));
   
    }
?>

