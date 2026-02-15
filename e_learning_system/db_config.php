<?php
$host = "localhost";
$user = "root";
$password = "root@123";
$dbname = "e_learning";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
