<?php
// Include database connection
include 'db_connection.php';

// Define variables and initialize with empty values
$firstname = $lastname = $email = $password = $confirm_password = "";
$firstname_err = $lastname_err = $email_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate first name
    if (empty(trim($_POST["firstname"]))) {
        $firstname_err = "Please enter your first name.";
    } else {
        $firstname = trim($_POST["firstname"]);
    }

    // Validate last name
    if (empty(trim($_POST["lastname"]))) {
        $lastname_err = "Please enter your last name.";
    } else {
        $lastname = trim($_POST["lastname"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);

        // Check if email already exists
        $sql = "SELECT ID FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            
            $email_err = "This email is already registered.";
        }

        $stmt->close();
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting into database
    if (empty($firstname_err) && empty($lastname_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (firstname, lastname, email, password, isAdmin) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssssi", $param_firstname, $param_lastname, $param_email, $param_password, $param_isAdmin);

            // Set parameters
            $param_firstname = $firstname;
            $param_lastname = $lastname;
            $param_email = $email;
            $param_password = md5($password); // Use MD5 hashing for password
            $param_isAdmin = 0; // Set isAdmin to 0 for regular users

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Send confirmation email to user
                $to = $email;
                $subject = 'Account Created Successfully';
                $message = 'Dear ' . $firstname . ',<br><br>Your account has been successfully created.<br><br>Thank you for registering with us.';
                $headers = 'From: yourdomain@example.com' . "\r\n" 

                mail($to, $subject, $message, $headers);

                // Redirect to login page with success message
                header("location: login.php?register=success");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
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
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <p>Please fill this form to create an account.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($firstname_err)) ? 'has-error' : ''; ?>">
            <label>First Name</label>
            <input type="text" name="firstname" class="form-control" value="<?php echo $firstname; ?>">
            <span class="help-block"><?php echo $firstname_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($lastname_err)) ? 'has-error' : ''; ?>">
            <label>Last Name</label>
            <input type="text" name="lastname" class="form-control" value="<?php echo $lastname; ?>">
            <span class="help-block"><?php echo $lastname_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
            <span class="help-block <?php echo (!empty($email_err)) ? 'error-message' : ''; ?>"><?php echo $email_err; ?></span>
        </div>

        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <label>Password</label>
            <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
            <span class="help-block"><?php echo $confirm_password_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-default" value="Reset">
        </div>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
</div>
</body>
</html>
