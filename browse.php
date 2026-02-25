<?php
session_start();
include '../includes/config.php';

$category_filter = isset($_GET['category']) ? $_GET['category'] : null;

$query = "SELECT * FROM products";
if ($category_filter) {
    $query .= " WHERE category_id = " . ($category_filter == 'computer' ? 1 : 2);
}
$result = mysqli_query($con, $query);

// Handle rating submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user'])) {
    $product_id = $_POST['product_id'];
    $rating = intval($_POST['rating']);
    $user_id = $_SESSION['user'];

    // Check if the user has already rated the product
    $check = mysqli_query($con, "SELECT * FROM ratings WHERE user_id=$user_id AND product_id=$product_id");
    if (mysqli_num_rows($check) > 0) {
        // If already rated, update the rating
        mysqli_query($con, "UPDATE ratings SET rating=$rating WHERE user_id=$user_id AND product_id=$product_id");
    } else {
        // If not rated before, insert new rating
        mysqli_query($con, "INSERT INTO ratings (user_id, product_id, rating) VALUES ($user_id, $product_id, $rating)");
    }
    echo "<script>alert('Rating submitted successfully');</script>";
}

?>

<!DOCTYPE html>

<html>
<head>
    <title>Browse Products</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .nav-links {
            margin-bottom: 20px;
        }


    .nav-links a {
        margin-right: 15px;
        font-weight: bold;
    }

    .product-card {
        background: #fff;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .product-card h3 {
        margin-top: 0;
    }

    .product-card a {
        background: #3498db;
        color: white;
        padding: 6px 10px;
        text-decoration: none;
        border-radius: 4px;
    }

    .product-card a:hover {
        background: #2980b9;
    }

    .rating {
        color: #f39c12;
        font-weight: bold;
    }

    .rating-form input {
        width: 30px;
        padding: 5px;
    }
</style>
```

</head>
<body>

<h2>Browse Products</h2>

<div class="nav-links">
    <a href="?category=computer">Computer Parts</a> | 
    <a href="?category=misc">Miscellaneous</a> | 
    <a href="cart.php">View Cart</a> | 
    <a href="logout.php">Logout</a>
</div>

<?php while ($row = mysqli_fetch_assoc($result)) { 
    // Fetch average rating for each product
    $product_id = $row['id'];
    $avg_query = mysqli_query($con, "SELECT AVG(rating) AS avg_rating FROM ratings WHERE product_id = $product_id");
    $avg = mysqli_fetch_assoc($avg_query);
    $avg_rating = round($avg['avg_rating'], 1); // rounded to 1 decimal place
?>

```
<div class="product-card">
    <h3><?= htmlspecialchars($row['name']) ?> - $<?= $row['price'] ?></h3>
    <p><?= htmlspecialchars($row['description']) ?></p>

    <!-- Display the average rating -->
    <p class="rating">Average Rating: <?= $avg_rating ?> / 5</p>

    <!-- Rating Form -->
    <?php if (isset($_SESSION['user'])): ?>
        <form class="rating-form" method="POST">
            <label for="rating">Rate this product:</label>
            <input type="number" name="rating" min="1" max="5" required>
            <input type="hidden" name="product_id" value="<?= $product_id ?>">
            <button type="submit">Submit Rating</button>
        </form>
    <?php else: ?>
        <p><a href="user/login.php">Login</a> to rate this product.</p>
    <?php endif; ?>

    <!-- Review Form -->
    <?php if (isset($_SESSION['user'])): ?>
        <form method="POST" action="submit_review.php">
            <input type="hidden" name="product_id" value="<?= $product_id; ?>">
            <textarea name="review" required placeholder="Write your review here..."></textarea><br>
            <label>Rating:</label>
            <select name="rating">
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select><br>

            <button type="submit">Submit Review</button>
        </form>
    <?php else: ?>
        <p><a href="user/login.php">Login</a> to submit a review.</p>
    <?php endif; ?>

    <a href="cart.php?action=add&id=<?= $row['id'] ?>">Add to Cart</a>
    
    
    <!-- Reviews for this product -->
    <?php
        $pid = intval($product_id);
        $review_sql = "SELECT r.*, u.username AS name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = $pid ORDER BY r.created_at DESC";
        $review_result = mysqli_query($con, $review_sql);
        if ($review_result && mysqli_num_rows($review_result) > 0) {
            echo '<h4>Reviews</h4>';
            while ($r = mysqli_fetch_assoc($review_result)) {
                echo '<p><strong>' . htmlspecialchars($r['name']) . '</strong></p>';
                echo '<p>' . nl2br(htmlspecialchars($r['review'])) . '</p>';
                echo '<p>Rating: ' . intval($r['rating']) . '/5</p>';
                if (isset($_SESSION['user']) && $_SESSION['user'] == $r['user_id']) {
                    echo '<a href="edit_review.php?id=' . $r['id'] . '">Edit</a> ';
                }

                // show delete link only to admin users
                $is_admin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
                         || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
                if ($is_admin) {
                    echo '<a href="delete_review.php?id=' . $r['id'] . '" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                }
                echo '<hr>'; 
            }
        }
    ?>

</div>
```

<?php } ?>

</body>
</html>
