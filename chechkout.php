<?php
// Start the session
session_start();

// Include database connection
include 'db_connection.php';

// Check if product ID is provided
if (isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $productImage = $_POST['product_image'];

    // Fetch product details from the database based on product ID
} else {
    // Redirect to home page if product ID is not provided
    header("Location: index.php");
    exit();
}

// Check if the payment form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {
    // Handle payment processing based on the selected payment method
    if ($_POST['payment_method'] == 'credit_card') {
        // Process credit card payment
        $card_number = $_POST['card_number'];
        // Save payment information to the database
        $userId = isset($_SESSION["id"]) ? $_SESSION["id"] : 1; // Assuming user ID is 1 if not logged in
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $userId, $productId, $quantity);
        $stmt->execute();
        $stmt->close();
    } elseif ($_POST['payment_method'] == 'paypal') {
        // Process PayPal payment
        // Save payment information to the database
        $userId = isset($_SESSION["id"]) ? $_SESSION["id"] : 1; // Assuming user ID is 1 if not logged in
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $userId, $productId, $quantity);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: success.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="navbar">
    <a href="index.php">Home</a>
</div>
<div class="container">
    <h1>Checkout</h1>
    <div class="product-details">
        <img src="<?php echo $productImage;?>" width="100"/>
        <p>Product ID: <?php echo $productId; ?></p>
        <p>Quantity: <?php echo $quantity; ?></p>
    </div>
    <div class="payment-options">
        <h2>Payment Options</h2>
        <form action="" method="post">
        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
               
            <label for="payment_method">Select Payment Method:</label>
            <select id="payment_method" name="payment_method" class="select-box">
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
            </select>
            <div id="credit_card_details" style="display: none;">
                <label for="card_number">Credit Card Number:</label>
                <input type="text" id="card_number" name="card_number">
            </div>
            <button type="submit" name="submit_payment">Pay Now</button>
            <input type="hidden" name="quantity" value="<?php echo $quantity; ?>"> <!-- Assuming quantity is always 1 -->
                <input type="hidden" name="product_image" value="<?php echo $productImage;?>">
              
        </form>
    </div>
</div>
<script>
    // Function to show credit card details field if credit card option is selected
    document.getElementById('payment_method').addEventListener('change', function () {
        var paymentMethod = this.value;
        var creditCardDetails = document.getElementById('credit_card_details');
        if (paymentMethod === 'credit_card') {
            creditCardDetails.style.display = 'block';
        } else {
            creditCardDetails.style.display = 'none';
        }
    });
</script>
</body>
</html>
