<?php
session_start();
include '../includes/config.php';

// Check if the user is an admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get the product ID from the URL parameter
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Check if the product ID is valid
    $check_query = "SELECT * FROM products WHERE id = $product_id";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Delete the product
        $delete_query = "DELETE FROM products WHERE id = $product_id";
        if (mysqli_query($con, $delete_query)) {
            echo "<script>alert('Product deleted successfully!'); window.location.href = 'dashboard.php';</script>";
        } else {
            echo "<script>alert('Error deleting product!'); window.location.href = 'dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Product not found!'); window.location.href = 'dashboard.php';</script>";
    }
} else {
    // Redirect to dashboard if no product ID is provided
    header("Location: dashboard.php");
    exit();
}
?>
