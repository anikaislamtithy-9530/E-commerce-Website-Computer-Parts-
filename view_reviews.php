<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (!($con instanceof mysqli)) {
    error_log('view_reviews.php: $con is not a mysqli instance');
    die('Database connection error. Check error log.');
}

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
if ($product_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// ensure reviews table exists
$tbl_check = mysqli_query($con, "SHOW TABLES LIKE 'reviews'");
if (! $tbl_check || mysqli_num_rows($tbl_check) === 0) {
    error_log('view_reviews.php: reviews table not found');
    die('Reviews table not found. Check database.');
}

// fetch product info
$pq = mysqli_query($con, "SELECT id, name FROM products WHERE id = $product_id LIMIT 1");
if (! $pq) {
    error_log('view_reviews.php product query error: ' . mysqli_error($con));
    die('Database query error. Check error log.');
}
$product = mysqli_fetch_assoc($pq);
if (! $product) {
    header('Location: dashboard.php');
    exit;
}

// fetch reviews using COALESCE to support either `review` or `review_text` column names
$sql = "
    SELECT r.id,
           r.user_id,
           COALESCE(r.review, r.review_text, '') AS review_text,
           r.rating,
           r.created_at,
           u.username
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.product_id = " . intval($product_id) . "
    ORDER BY r.created_at DESC
";
$res = mysqli_query($con, $sql);
if ($res === false) {
    error_log('view_reviews.php reviews query error: ' . mysqli_error($con));
    die('Database query error. Check error log.');
}
?>

<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="../assets/style.css">
    <title>Reviews for <?= htmlspecialchars($product['name']) ?></title>
</head>
<body>
    <div class="container mt-4">
        <h2>Reviews for <?= htmlspecialchars($product['name']) ?></h2>
        <p><a href="dashboard.php">← Back to Dashboard</a></p>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reviewer</th>
                    <th>Review</th>
                    <th>Rating</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                <tr>
                    <td><?= intval($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['username'] ?? 'User #' . intval($row['user_id'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['review_text'])) ?></td>
                    <td><?= intval($row['rating']) ?>/5</td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <a href="../user/delete_review.php?id=<?= intval($row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this review?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>