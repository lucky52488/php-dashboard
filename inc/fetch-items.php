<?php 
require("components/_dataConnect.php");

$sql = "SELECT * FROM item";
$result = mysqli_query($conn, $sql);

// Fetch data and encode as JSON
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
echo json_encode($data);
?>