<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['user'])) {
    header('Location: browse.php');
    exit;
}

// Determine admin flag (support both `is_admin` and `role` session patterns)
$is_admin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
         || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

if (! $is_admin) {
    // Non-admins are not allowed to delete reviews
    header('Location: browse.php');
    exit;
}

$review_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$redirect = $_SERVER['HTTP_REFERER'] ?? 'browse.php';

if ($review_id <= 0) {
    header('Location: ' . $redirect);
    exit;
}

$stmt = mysqli_prepare($con, "DELETE FROM reviews WHERE id = ?");
if ($stmt === false) {
    error_log('Prepare failed (delete_review): ' . mysqli_error($con));
    header('Location: ' . $redirect);
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $review_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header('Location: ' . $redirect);
exit;
?>