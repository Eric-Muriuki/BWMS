<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bank_wifi_system";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
