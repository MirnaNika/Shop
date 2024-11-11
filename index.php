<?php
// Start the session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // If logged in, store user information in variables
    $firstname = $_SESSION["firstname"];
    $lastname = $_SESSION["lastname"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="navbar">
    <a href="index.php">Home</a>
    <?php
    // Check if the user is logged in
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        // If logged in, show the user's name
        echo '<div class="user-info">Welcome, ' . $firstname . ' ' . $lastname . '</div>';
    } else {
        // If not logged in, show Register and Login buttons
        echo '<a href="register.php">Register</a>';
        echo '<a href="login.php">Login</a>';
    }
    ?>
</div>

<div class="container">
    <h1>Welcome to Our Online Store</h1>

    <div class="product-list">
        <?php
        // Include database connection
        include 'db_connection.php';

        // Fetch products from the database
        $stmt = $conn->prepare("SELECT * FROM Products");
        $stmt->execute();
        $result = $stmt->get_result();

        // Display products++
        while ($row = $result->fetch_assoc()) {

            // Path to the directory containing product images
            $imageDir = "uploads/products/" . $row['ID'] . "/";

            // Get the list of files in the directory
            $files = glob($imageDir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);

            echo '<div class="product">';
            // Check if any files are found
            if (!empty($files)) {
                // Get the first image file
                $firstImage = $files[0];
                // Display the image with a link to the product page
                echo '<a href="product.php?id=' . $row['ID'] . '"><img src="' . $firstImage . '" alt="' . $row['name'] . '" class="product-image" width="100"></a>';
            } else {
                // If no image found, display a placeholder
                echo "No image available";
            }
            echo '<h2 class="product-name">' . $row['name'] . '</h2>';
            echo '<p class="product-description">' . $row['description'] . '</p>';
            echo '<p class="product-price">$' . $row['price'] . '</p>';
            echo '</div>';
        }

        // Close database connection
        $conn->close();
        ?>
    </div>
</div>
</body>
</html>
