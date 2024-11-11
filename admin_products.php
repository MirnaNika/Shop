<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Fetch all products from the database
$stmt = $conn->prepare("SELECT * FROM Products ORDER BY ID DESC");
$stmt->execute();
$result = $stmt->get_result();

// Check if the form is submitted to add a new product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products</title>
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
<h2>View, Update, Delete or Add New products</h2>
      

    <div class="admin-panel">
         <a href="add_product.php" class="add-product-btn">Add Product</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Actions</th> <!-- Added column for action buttons -->
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['ID']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td>
    <?php
    // Path to the directory containing product images
    $imageDir = "uploads/products/" . $row['ID'] . "/";

    // Get the list of files in the directory
    $files = glob($imageDir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);

    // Check if any files are found
    if (!empty($files)) {
        // Get the first image file
        $firstImage = $files[0];
        // Display the image
        echo '<img src="' . $firstImage . '" alt="' . $row['name'] . '" width="100">';
    } else {
        // If no image found, display a placeholder
        echo "No image available";
    }
    ?>
</td>
                        <td class="action-buttons">
                            <button class="edit-button" onclick="editProduct(<?php echo $row['ID']; ?>)">Edit</button>
                            <button class="delete-button" onclick="deleteProduct(<?php echo $row['ID']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    </div>
    <script>
        function editProduct(productId) {
            // Redirect to edit_product.php with productId as parameter
            window.location.href = "edit_product.php?id=" + productId;
        }

        function deleteProduct(productId) {
            // Confirm before deletion
            if (confirm("Are you sure you want to delete this product?")) {
                // Redirect to delete_product.php with productId as parameter
                window.location.href = "delete_product.php?id=" + productId;
            }
        }
    </script>
</body>
</html>
