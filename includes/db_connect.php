<?php
// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$database = "vinpearl_db";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to support unicode
$conn->set_charset("utf8mb4");
?>