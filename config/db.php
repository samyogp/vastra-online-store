<?php
$host     = "localhost";
$username = "root";
$password = "";           
$database = "storee_db";   

$conn = mysqli_connect($host, $username, $password, $database, 3306);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>