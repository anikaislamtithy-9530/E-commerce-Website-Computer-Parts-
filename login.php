<?php
session_start();
include '../includes/config.php';

// Check if the user is already logged in
if (isset($_SESSION['user'])) {
    // If logged in, redirect to browse page or main page
    header("Location: browse.php");
    exit();
}

// Handle login submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check if the user exists in the database
    $query = "SELECT * FROM users WHERE username='$username' AND role='user'";
    $result = mysqli_query($con, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        // If user exists and password is correct, create session and redirect to browse page
        $_SESSION['user'] = $user['id'];
        header("Location: browse.php");
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<h2>Login</h2>

<?php if (isset($error)): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register here</a></p>

</body>
</html>
