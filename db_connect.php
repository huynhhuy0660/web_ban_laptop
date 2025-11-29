<?php
$servername = "localhost"; 
$username = "root";       
$password = "";           
$database = "db_ban_laptop"; 
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>