<?php
session_start();
include '../includes/config.php';

// Check if the user is already logged in
if (isset($_SESSION['user'])) {
    // If logged in, redirect to the dashboard or main page
    header("Location: browse.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check_query = "SELECT * FROM users WHERE email = '$email'";
        $check_result = mysqli_query($con, $check_query);
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Email is already registered.";
        } else {
            // Hash the password before saving it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $insert_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
            if (mysqli_query($con, $insert_query)) {
                // Redirect to login page after successful registration
                header("Location: login.php");
                exit();
            } else {
                $error = "Error registering user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<h2>Register</h2>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" required><br><br>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br><br>

    <label for="password">Password:</label>
    <input type="password" name="password" required><br><br>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" name="confirm_password" required><br><br>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login here</a></p>

</body>
</html>
