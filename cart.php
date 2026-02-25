<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user'];

// Add item to cart
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $check = mysqli_query($con, "SELECT * FROM cart WHERE user_id=$user_id AND product_id=$product_id");

    if (mysqli_num_rows($check) == 0) {
        mysqli_query($con, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");
    }
    header("Location: cart.php");
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    mysqli_query($con, "DELETE FROM cart WHERE user_id=$user_id AND product_id=$product_id");
    header("Location: cart.php");
}

// Update quantity
if (isset($_GET['update']) && isset($_GET['id']) && isset($_GET['quantity'])) {
    $product_id = $_GET['id'];
    $quantity = $_GET['quantity'];

    // Only update if the quantity is valid
    if ($quantity > 0) {
        mysqli_query($con, "UPDATE cart SET quantity=$quantity WHERE user_id=$user_id AND product_id=$product_id");
    } else {
        // If quantity is 0 or less, remove item
        mysqli_query($con, "DELETE FROM cart WHERE user_id=$user_id AND product_id=$product_id");
    }
    header("Location: cart.php");
}

// Query to fetch products in the cart
$query = "SELECT products.name, products.price, cart.product_id, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.id 
          WHERE cart.user_id = $user_id";
$result = mysqli_query($con, $query);

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
            text-align: left;
        }

        th, td {
            padding: 12px;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        .cart-actions a {
            margin: 0 10px;
            background-color: #3498db;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 5px;
        }

        .cart-actions a:hover {
            background-color: #2980b9;
        }

        .total-section {
            text-align: center;
            margin-top: 20px;
        }

        .total-section h3 {
            font-size: 24px;
            color: #e74c3c;
        }

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

<h2>Your Cart</h2>

<table>
    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Action</th></tr>
    <?php while ($row = mysqli_fetch_assoc($result)) {
        // Calculate the total price for each product
        $total += $row['price'] * $row['quantity'];
    ?>
    <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td>$<?= $row['price'] ?></td>
        <td class="cart-actions">
            <a href="cart.php?update&id=<?= $row['product_id'] ?>&quantity=<?= $row['quantity'] - 1 ?>" <?= $row['quantity'] <= 1 ? 'disabled' : '' ?>>-</a>
            <?= $row['quantity'] ?>
            <a href="cart.php?update&id=<?= $row['product_id'] ?>&quantity=<?= $row['quantity'] + 1 ?>">+</a>
        </td>
        <td><a href="cart.php?remove=<?= $row['product_id'] ?>" class="cart-actions">Remove</a></td>
    </tr>
    <?php } ?>
</table>

<div class="total-section">
    <h3>Total: $<?= number_format($total, 2) ?></h3>
</div>

<a href="checkout.php" class="checkout-btn">Checkout</a>

</body>
</html>
