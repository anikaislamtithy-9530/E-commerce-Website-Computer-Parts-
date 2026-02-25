<?php



ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include '../includes/config.php';


if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id']); // ensure it's a number
$query = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($con, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Product not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $desc = mysqli_real_escape_string($con, $_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = intval($_POST['category_id']);

    $update = "UPDATE products 
               SET name = '$name', description = '$desc', price = $price, category_id = $category_id 
               WHERE id = $id";

    if (mysqli_query($con, $update)) {
        echo "Product updated successfully. <a href='dashboard.php'>Go back</a>";
        // Optionally refresh to load new values
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Product</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<h2>Update Product</h2>

<form method="POST">
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>
    <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea><br>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br>
    <select name="category_id" required>
        <option value="1" <?= $product['category_id'] == 1 ? 'selected' : '' ?>>Computer Parts</option>
        <option value="2" <?= $product['category_id'] == 2 ? 'selected' : '' ?>>Miscellaneous</option>
    </select><br>
    <button type="submit">Update Product</button>

    

</form>

</body>
</html>
