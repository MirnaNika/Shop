<?php
// Include database connection
include 'db_connection.php';

// Retrieve user input from the login form
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare SQL statement to retrieve the hashed password from the database based on email
$stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

// Check if the user exists
if ($stmt->num_rows == 1) {
    // Bind the result variable
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    
    // Hash the input password using MD5
    $hashed_input_password = md5($password);
    
    // Compare the hashed input password with the stored hashed password
    if ($hashed_input_password == $hashed_password) {
        // Passwords match, login successful
        echo "Login successful!";
        // Proceed with your login logic
    } else {
        // Passwords don't match, login failed
        echo "Incorrect email or password.";
    }
} else {
    // User doesn't exist, login failed
    echo "Incorrect email or password.";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
