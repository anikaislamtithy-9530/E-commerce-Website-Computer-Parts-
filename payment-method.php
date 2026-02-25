<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: logout.php");
    exit();
}

// Get the total amount passed from checkout
$total_amount = $_GET['total_amount'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Payment Method</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding-top: 50px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        p {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }

        .payment-method {
            display: block;
            width: 200px;
            margin: 15px auto;
            background-color: #3498db;
            color: white;
            padding: 12px;
            text-align: center;
            border-radius: 5px;
            font-size: 18px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .payment-method:hover {
            background-color: #2980b9;
        }

        .payment-method:active {
            background-color: #1c5980;
        }

        .payment-method:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Select Payment Method</h2>
    <p>Total Amount: $<?php echo number_format($total_amount, 2); ?></p>
    
    <!-- Payment method options -->
    <a href="payment-confirmation.php?payment_method=credit_card&total_amount=<?php echo $total_amount; ?>" class="payment-method">Credit Card</a>
    <a href="payment-confirmation.php?payment_method=paypal&total_amount=<?php echo $total_amount; ?>" class="payment-method">PayPal</a>
    <a href="payment-confirmation.php?payment_method=bank_transfer&total_amount=<?php echo $total_amount; ?>" class="payment-method">Bank Transfer</a>
    <a href="logout.php" class="payment-method" style="background-color: #e74c3c;">Logout</a>


</div>

</body>
</html>
