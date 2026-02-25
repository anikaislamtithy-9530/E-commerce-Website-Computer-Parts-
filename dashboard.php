<link rel="stylesheet" href="../assets/style.css">

<?php
session_start();
include '../includes/config.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch products and categories
$query = "SELECT products.*, categories.name as category FROM products 
          JOIN categories ON products.category_id = categories.id";
$result = mysqli_query($con, $query);
?>

<div class="container mt-5">
    <h2 class="text-center">Admin Dashboard</h2>
    <div class="text-end mb-3">
        <a href="add-product.php" class="btn btn-success">Add New Product</a> |
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
                
                <th>Description</th>
                
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>$<?= number_format($row['price'], 2) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    
                    <td>
                        <a href="view_reviews.php?product_id=<?= $row['id'] ?>" class="btn btn-info btn-sm">View Reviews</a>
                        <a href="update-product.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete-product.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Optionally include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
