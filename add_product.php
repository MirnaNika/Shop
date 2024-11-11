<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Define variables and initialize with empty values
$name = $description = $price = "";
$name_err = $description_err = $price_err = "";
$image_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter the product name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter the product description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate price
        if (empty(trim($_POST["price"]))) {
            $price_err = "Please enter the product price.";
        } elseif (!is_numeric($_POST["price"]) || floatval($_POST["price"]) <= 0) { // Check if price is is either not a number or is a number less than or equal to zero
            $price_err = "Please enter a valid price.";
        } else {
            $price = trim($_POST["price"]);
        }


    // Validate and process image upload
    if ($_FILES['image']['size'] == 0) {
        $image_err = "Please select an image.";
    } elseif ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
        $image_err = "Error uploading image. Please try again.";
    } else {
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $image_err = "Only JPG, JPEG, and PNG files are allowed.";
        }
    }

    // Check input errors before inserting into database
    if (empty($name_err) && empty($description_err) && empty($price_err) && empty($image_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO Products (name, description, price) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssd", $param_name, $param_description, $param_price);

            // Set parameters
            $param_name = $name;
            $param_description = $description;
            $param_price = $price;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Get the ID of the inserted product
                $product_id = $conn->insert_id;

                // Define the target directory
                $target_dir = "uploads/products/$product_id/";

                // Create the directory if it doesn't exist
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                // Define the target file path
                $target_file = $target_dir . basename($_FILES["image"]["name"]);

                // Upload the image file
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    // Image uploaded successfully
                    header("location: admin_products.php");
                    exit();
                } else {
                    $image_err = "Error uploading image. Please try again.";
                }
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="admin-panel">
        <h2>Add Product</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                <span class="invalid-feedback"><?php echo $description_err; ?></span>
            </div>
            <div class="form-group">
                <label>Price</label>
                <input type="text" name="price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
                <span class="invalid-feedback"><?php echo $price_err; ?></span>
            </div>

            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control-file <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $image_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="admin_products.php" class="btn btn-secondary ml-2">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
