<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if product ID is provided in
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Include database connection
    include 'db_connection.php';

    // Get product ID from the URL
    $productId = $_GET['id'];

    // Prepare SQL statement to delete the product
    $stmt = $conn->prepare("DELETE FROM Products WHERE ID = ?");
    $stmt->bind_param("i", $productId);

    // Execute the statement
    if ($stmt->execute()) {
        // Product deleted successfully
        header("Location: admin_products.php");
        exit();
    } else {
        // Error deleting product
        echo "Error deleting product: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Redirect if product ID is not provided or empty
    header("Location: admin_products.php");
    exit();
}
?>
