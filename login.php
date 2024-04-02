<?php
require_once "getConnection.php";
require_once "session.php";

$error = '';

try {
    // Establish a database connection using the getConnection() function
    $pdo = getConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Empty email check
        if (empty($email)) {
            $error .= '<p class="error">Please enter email!</p>';
        }

        // Empty password check
        if (empty($password)) {
            $error .= '<p class="error">Please enter your password!</p>';
        }

        if (empty($error)) {
            $query = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $query->execute([$email]);
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION["userid"] = $user['user_id'];
                    $_SESSION["user"] = $user;

                    // Check if the user is an organizer
                    if ($user['organizer'] == 1) {
                        // Set a session variable to indicate the user is logged in and is an organizer
                        $_SESSION["logged_in"] = true;
                        $_SESSION["organizer"] = true;
                    } else {
                        // Set a session variable to indicate the user is logged in but is not an organizer
                        $_SESSION["logged_in"] = true;
                        $_SESSION["organizer"] = false;
                    }

                    // Redirect to events page
                    header("location: eventManament.php");
                    exit;
                } else {
                    $error .= '<p class="error">The password is invalid!</p>';
                }
            } else {
                $error .= '<p class="error">Invalid email address!</p>';
            }
        }
    }
    
} catch (PDOException $e) {
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
    <title>Login</title>
</head>
<body>
    <div class="loginContainer">
        <h2>Login</h2>
        <?php echo $error; ?>
        <form action="" method="post" id="login-form">
            <label for="email">Email Address:</label>
            <input type="text" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Login" name="submit">
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        </div>
    </div>
</body>
</html>
