<?php

$host = "localhost"; 
$user = "root";
$password = "";
$database = "childrens_store";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed.");
}

?>
