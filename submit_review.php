<?php
session_start();
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user'])) {
    header('Location: browse.php');
    exit;
}

$user_id = intval($_SESSION['user']);
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$review = trim($_POST['review'] ?? '');
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

$redirect = $_SERVER['HTTP_REFERER'] ?? 'browse.php';

if ($product_id <= 0 || $rating < 1 || $rating > 5 || $review === '') {
    header('Location: ' . $redirect);
    exit;
}

if (!($con instanceof mysqli)) {
    error_log('DB connection ($con) is not a mysqli instance');
    header('Location: ' . $redirect);
    exit;
}

mysqli_begin_transaction($con);

try {
    // Insert or update review
    $stmt = mysqli_prepare($con, "SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
    if ($stmt === false) {
        error_log('Prepare failed (reviews select): ' . mysqli_error($con));
        mysqli_rollback($con);
        header('Location: ' . $redirect);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $existing_review_id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        $upd = mysqli_prepare($con, "UPDATE reviews SET review = ?, rating = ?, created_at = NOW() WHERE id = ?");
        if ($upd === false) {
            error_log('Prepare failed (reviews update): ' . mysqli_error($con));
            mysqli_rollback($con);
            header('Location: ' . $redirect);
            exit;
        }
        mysqli_stmt_bind_param($upd, "sii", $review, $rating, $existing_review_id);
        mysqli_stmt_execute($upd);
        mysqli_stmt_close($upd);
    } else {
        mysqli_stmt_close($stmt);

        $ins = mysqli_prepare($con, "INSERT INTO reviews (user_id, product_id, review, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
        if ($ins === false) {
            error_log('Prepare failed (reviews insert): ' . mysqli_error($con));
            mysqli_rollback($con);
            header('Location: ' . $redirect);
            exit;
        }
        mysqli_stmt_bind_param($ins, "iisi", $user_id, $product_id, $review, $rating);
        mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);
    }

    // Insert or update ratings table to keep average rating consistent
    $stmt2 = mysqli_prepare($con, "SELECT id FROM ratings WHERE user_id = ? AND product_id = ?");
    if ($stmt2 === false) {
        error_log('Prepare failed (ratings select): ' . mysqli_error($con));
        mysqli_rollback($con);
        header('Location: ' . $redirect);
        exit;
    }
    mysqli_stmt_bind_param($stmt2, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_store_result($stmt2);

    if (mysqli_stmt_num_rows($stmt2) > 0) {
        mysqli_stmt_bind_result($stmt2, $existing_rating_id);
        mysqli_stmt_fetch($stmt2);
        mysqli_stmt_close($stmt2);

        $upd2 = mysqli_prepare($con, "UPDATE ratings SET rating = ? WHERE id = ?");
        if ($upd2 === false) {
            error_log('Prepare failed (ratings update): ' . mysqli_error($con));
            mysqli_rollback($con);
            header('Location: ' . $redirect);
            exit;
        }
        mysqli_stmt_bind_param($upd2, "ii", $rating, $existing_rating_id);
        mysqli_stmt_execute($upd2);
        mysqli_stmt_close($upd2);
    } else {
        mysqli_stmt_close($stmt2);

        $ins2 = mysqli_prepare($con, "INSERT INTO ratings (user_id, product_id, rating) VALUES (?, ?, ?)");
        if ($ins2 === false) {
            error_log('Prepare failed (ratings insert): ' . mysqli_error($con));
            mysqli_rollback($con);
            header('Location: ' . $redirect);
            exit;
        }
        mysqli_stmt_bind_param($ins2, "iii", $user_id, $product_id, $rating);
        mysqli_stmt_execute($ins2);
        mysqli_stmt_close($ins2);
    }

    mysqli_commit($con);
} catch (Exception $e) {
    error_log('Exception in submit_review.php: ' . $e->getMessage());
    mysqli_rollback($con);
    // fall back to redirect
}

header('Location: ' . $redirect);
exit;
?>

##inside browse.php
  show Reviews + Edit/Delete Buttons  

<?php
$sql = "SELECT r.*, u.name
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE product_id = $product_id";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
?>
    <p><strong><?php echo $row['name']; ?></strong></p>
    <p><?php echo $row['review_text']; ?></p>
    <p>Rating: <?php echo $row['rating']; ?>/5</p>

<?php if($_SESSION['user_id'] == $row['user_id']) { ?>
    <a href="edit_review.php?id=<?php echo $row['review_id']; ?>">Edit</a>
    <a href="delete_review.php?id=<?php echo $row['review_id']; ?>"
       onclick="return confirm('Are you sure?')">Delete</a>
<?php } ?>

<hr>
<?php } ?>