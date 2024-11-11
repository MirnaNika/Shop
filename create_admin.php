<?php
// Include database connection
include 'db_connection.php';

// Example admin email and password
$email = "admin@example.com";
$password = "admin123";

// Example first and last name
$firstname = "Admin";
$lastname = "User";

// Hash the password using MD5 (not recommended for secure password hashing)
$hashed_password = md5($password);

// Prepare SQL statement to insert the admin into the database
$stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, isAdmin) VALUES (?, ?, ?, ?, 1)");
$stmt->bind_param("ssss", $firstname, $lastname, $email, $hashed_password);

// Execute the statement
$stmt->execute();

echo "Admin added successfully.";

// Close statement and connection
$stmt->close();
$conn->close();
?>
