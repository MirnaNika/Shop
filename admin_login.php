<?php
session_start();

// Check if admin is already logged in, redirect to admin panel if so
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_panel.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'db_connection.php';

    // Get input values from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND isAdmin = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        // Admin found, verify password
        $row = $result->fetch_assoc();
        if (md5($password) == $row['password']) {
            // Password is correct, start a new session
            $_SESSION['admin_id'] = $row['ID'];
            $_SESSION['admin_email'] = $row['email'];
            header("Location: admin_panel.php");
            exit();
        } else {
            // Password is incorrect
            $_SESSION['login_error'] = "Invalid email or password";
            header("Location: admin_login.php");
            exit();
        }
    } else {
        // Admin not found
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: admin_login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php 
        // Display login error message if set
        if (isset($_SESSION['login_error'])) {
            echo "<p class='error-message'>" . $_SESSION['login_error'] . "</p>";
            unset($_SESSION['login_error']);
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input value="admin@example.com" type="text" name="email" placeholder="Email" required>
            <input value="admin123" type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
