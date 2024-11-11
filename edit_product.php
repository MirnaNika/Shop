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

    // Fetch product details from the database
    $stmt = $conn->prepare("SELECT * FROM Products WHERE ID = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if product exists
    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();

        // Check if the form is submitted to update product details
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get input values from the form
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];

            // Prepare SQL statement to update product details
            $stmt = $conn->prepare("UPDATE Products SET name = ?, description = ?, price = ? WHERE ID = ?");
            $stmt->bind_param("ssdi", $name, $description, $price, $productId);

            // Execute the statement
            if ($stmt->execute()) {
                // Product details updated successfully
                header("Location: admin_products.php");
                exit();
            } else {
                // Error updating product details
                echo "Error updating product: " . $conn->error;
            }

            // Close statement
            $stmt->close();
        }
    } else {
        // Product not found
        echo "Product not found.";
    }

    // Close connection
    $conn->close();
} else {
    // Redirect if product ID is not provided or empty
    header("Location: admin_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="admin-panel">
        <h2>Edit Product</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $productId; ?>" method="post">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
            <label for="description">Description:</label>
            <textarea name="description" rows="4" required><?php echo $product['description']; ?></textarea>
            <label for="price">Price:</label>
            <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
            <button type="submit">Update Product</button>
        </form>
    </div>
</body>
</html>
