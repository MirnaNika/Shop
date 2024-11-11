<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Fetch admin information from the database
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE ID = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Check if the form is submitted to update admin settings
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input values from the form
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];



    // Prepare SQL statement to update admin information
    $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ? WHERE ID = ?");
    $stmt->bind_param("sssi", $firstname, $lastname, $email, $admin_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Update session with new email
        $_SESSION['admin_email'] = $email;
        echo "Admin settings updated successfully.";
    } else {
        echo "Error updating admin settings: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="admin-panel">
    <div class="menu">
            <ul>
                <li><a href="admin_panel.php">Settings</a></li>
                <li><a href="admin_products.php">Products</a></li>
                <li><a href="admin_analytics.php">Analytics</a></li>
                <li><a href="admin_users.php">Users</a></li>
            </ul>
        </div>
        <h2>Welcome, <?php echo $admin['firstname']; ?>!</h2>
       
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="firstname">First Name:</label>
            <input type="text" name="firstname" value="<?php echo $admin['firstname']; ?>" required>
            <label for="lastname">Last Name:</label>
            <input type="text" name="lastname" value="<?php echo $admin['lastname']; ?>" required>
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo $admin['email']; ?>" required>
             <button type="submit">Update Settings</button>
        </form>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
</html>
