<?php
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve product ID from the form
    $product_id = $_POST['product_id'];
    
    // Determine user ID
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        // If no user ID, use default ID (e.g., 1)
        $user_id = 1;
    }
    
    // Set quantity
    $quantity = 1; // Default quantity
    
    // Include database connection
    include 'db_connection.php';
    
    // Prepare and execute SQL statement to insert into cart table
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt->execute();
    
    // Close statement and database connection
    $stmt->close();
    $conn->close();
    
    // Redirect to payment confirmation page 
    header("Location: payment_confirmation.php");
    exit();
} else {
    // Redirect if form is not submitted
    header("Location: index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Process</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Payment Options</h1>
        <div class="payment-options">
            <form action="paypal_payment.php" method="post">
                <!-- PayPal payment button -->
                <button type="submit" class="payment-button">PayPal</button>
            </form>
            <form action="credit_card_payment.php" method="post">
                <!-- Credit card payment button -->
                <button type="submit" class="payment-button">Credit Card</button>
            </form>
        </div>
    </div>
</body>
</html>
