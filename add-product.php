<link rel="stylesheet" href="../assets/style.css">

<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $created_by = $_SESSION['admin'];

    // Check if the product already exists by checking the name
    $check_query = "SELECT * FROM products WHERE name = '$name'";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // If product exists, show an error message
        echo "Error: Product already exists. <a href='add-product.php'>Go back</a>";
    } else {
        // If product doesn't exist, proceed with the insertion
        $query = "INSERT INTO products (name, description, price, category_id, created_by) 
                  VALUES ('$name', '$desc', '$price', '$category_id', '$created_by')";
        if (mysqli_query($con, $query)) {
            echo "Product added successfully. <a href='dashboard.php'>Go back</a>";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
}
?>

<form method="POST">
    <input type="text" name="name" placeholder="Product Name" required><br>
    <textarea name="description" placeholder="Description" required></textarea><br>
    <input type="number" step="0.01" name="price" placeholder="Price" required><br>
    <select name="category_id" required>
        <option value="1">Computer Parts</option>
        <option value="2">Miscellaneous</option>
    </select><br>
    <button type="submit">Add Product</button>
</form>
