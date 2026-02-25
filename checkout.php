<?php
session_start();
include '../includes/config.php';

// Get user_id from session (assuming the user is logged in)
$user_id = $_SESSION['user'];

// Get the total amount from the cart
$total_query = "SELECT SUM(products.price * cart.quantity) AS total_amount
                FROM cart 
                JOIN products ON cart.product_id = products.id
                WHERE cart.user_id = '$user_id'";
$total_result = mysqli_query($con, $total_query);
$total = mysqli_fetch_assoc($total_result)['total_amount'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insert order
    $insert_order = "INSERT INTO orders (user_id, total_amount) VALUES ('$user_id', '$total')";
    if (mysqli_query($con, $insert_order)) {
        // Clear the cart
        $clear_cart = "DELETE FROM cart WHERE user_id = '$user_id'";
        mysqli_query($con, $clear_cart);

        // Redirect or show success
        echo "<script>alert('Order placed successfully!'); window.location.href='logout.php';</script>";
        exit();
    } else {
        echo "Error placing order: " . mysqli_error($con);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .checkout-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            background-color: #2ecc71;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            font-size: 18px;
            text-decoration: none;
        }

        .checkout-btn:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>

<form method="POST">
    <p>Total Amount: $<?php echo number_format($total, 2); ?></p>
    <button type="submit" class="checkout-btn">Place Order</button>
</form>


</body>
</html>
