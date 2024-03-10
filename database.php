<?php
    $dbHost = '127.0.0.1';  // Use the local loopback address.
    $dbUser = 'root';
    $dbPass = 'yvan2021';
    $dbName = 'e-commerce';

    $conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

    if (!$conn) {
        die('Could not Connect MySQL: ' . mysqli_connect_error());
    }
?>
