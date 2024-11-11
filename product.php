<?php
// Start the session
session_start();

// Include database connection
include 'db_connection.php';

// Check if product ID is provided
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Fetch product details from the database
    $stmt = $conn->prepare("SELECT * FROM Products WHERE ID = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if product exists
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Path to the directory containing product images
        $imageDir = "uploads/products/" . $product['ID'] . "/";
        // Get the list of files in the directory
        $files = glob($imageDir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
        // Check if any files are found
        if (!empty($files)) {
            // Get the first image file
            $productImage = $files[0];
        } else {
            // If no image found, use a placeholder
            $productImage = "placeholder.jpg"; // Provide the path to your placeholder image
        }
    } else {
        // Redirect to home page if product doesn't exist
        header("Location: index.php");
        exit();
    }
} else {
    // Redirect to home page if product ID is not provided
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?></title>
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
        <h1><?php echo $product['name']; ?></h1>
        <div class="product-details">
            <img src="<?php echo $productImage; ?>" alt="<?php echo $product['name']; ?>" class="product-image" width="200">
            <p class="product-description"><?php echo $product['description']; ?></p>
            <p class="product-price">$<?php echo $product['price']; ?></p>
            <form action="checkout.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $product['ID']; ?>">
                <input type="number" name="quantity" value="1"> <!-- Assuming quantity is always 1 -->
                <input type="hidden" name="product_image" value="<?php echo $productImage; ?>">
                <button type="submit" class="purchase-button">Purchase Now</button>
            </form>
        </div>
    </div>
</body>
</html>
