<?php

include 'getConnection.php'; // Include the database connection file

$error = '';
try {
    // Establish a database connection using the getConnection() function
    $pdo = getConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);
        $password_hash = password_hash($password, PASSWORD_BCRYPT); // Hash the password
    
        // Check if the email already exists in the database
        $query = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $query->execute([$email]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            $error .= '<p class="error">The email address is already registered!</p>';
        } else {
            // Password validation
            if (strlen($password) < 8) {
                $error .= '<p class="error">Password must have at least 8 characters!</p>';
            }
    
            // Confirm password validation
            if ($password !== $confirmPassword) {
                $error .= '<p class="error">Passwords do not match!</p>';
            }
    
            if (empty($error)) {
                // Insert the user into the database
                $insertQuery = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
                $result = $insertQuery->execute([$firstName, $lastName, $email, $password_hash]);
    
                if ($result) {
                    $error .= '<p class="success">Your registration was successful!</p>';
                } else {
                    $error .= '<p class="error">Something went wrong! Please try again.</p>';
                }
            }
        }
    }
}

 catch (PDOException $e) {
    // Display an error message if the connection fails
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>User Registration</title>
</head>
<body>
    <div class="registerContainer">
        <h2>User Registration</h2>
        <form action="" method="post" id="registration-form">
            <label for="firstName">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
            <label for="lastName">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
            <input type="submit" name="submit" value="Register">
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
        <?php echo $error; ?> <!-- Display error/success message -->
    </div>
</body>
</html>
