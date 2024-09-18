<?php
// Start the session
session_start();

// Destroy any existing session when visiting the login page
session_destroy();

// Re-start the session to capture the new login attempt
session_start();

// Database connection
$servername = "localhost";
$username = "ots";  // Your database username
$password = "ots";  // Your database password
$dbname = "ecommerce";  // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login form submission
if (isset($_POST['login'])) {
    $inputUsername = $conn->real_escape_string($_POST['username']);
    $inputPassword = $conn->real_escape_string($_POST['password']); // Plaintext for now, hash later

    // Fetch user from the database
    $sql = "SELECT * FROM users WHERE username = '$inputUsername' AND password = '$inputPassword'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Valid credentials
        $_SESSION['loggedin'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css"> <!-- Your CSS file -->
</head>
<body>
    <h1>Login</h1>

    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <form action="login.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <button type="submit" name="login">Login</button>
    </form>
</body>
</html>
