<?php
// Database configuration
$servername = "localhost:3306";
$username = 'mirna'; // Replace with your database username
$password = '123456'; // Replace with your database password
$dbname = 'webshop';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
