<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user'])) {
    header('Location: browse.php');
    exit;
}

// Determine request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $review_text = trim($_POST['review'] ?? '');
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $user_id = intval($_SESSION['user']);
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'browse.php';

    if ($review_id <= 0 || $rating < 1 || $rating > 5 || $review_text === '') {
        header('Location: ' . $redirect);
        exit;
    }

    // Update only if review belongs to current user
    $stmt = mysqli_prepare($con, "UPDATE reviews SET review = ?, rating = ?, created_at = NOW() WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "siii", $review_text, $rating, $review_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: ' . $redirect);
    exit;
}

// GET: show edit form
$review_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($review_id <= 0) {
    header('Location: browse.php');
    exit;
}

$stmt = mysqli_prepare($con, "SELECT id, product_id, review, rating, user_id FROM reviews WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $review_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id, $product_id, $review_text, $rating, $owner_id);
if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: browse.php');
    exit;
}
mysqli_stmt_close($stmt);

// Ensure ownership
if (!isset($_SESSION['user']) || intval($_SESSION['user']) !== intval($owner_id)) {
    header('Location: browse.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Review</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Edit Review</h2>
    <form method="POST" action="edit_review.php">
        <input type="hidden" name="id" value="<?= intval($id) ?>">
        <label for="review">Review</label><br>
        <textarea name="review" required rows="6" cols="60"><?= htmlspecialchars($review_text) ?></textarea><br>
        <label for="rating">Rating</label>
        <select name="rating">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <option value="<?= $i ?>" <?= ($i == intval($rating)) ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
        </select><br><br>
        <button type="submit">Save</button>
        <a href="browse.php">Cancel</a>
    </form>
</body>
</html>