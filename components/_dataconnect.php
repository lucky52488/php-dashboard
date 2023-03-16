<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "sweet";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    die("connection failed");
}

?>