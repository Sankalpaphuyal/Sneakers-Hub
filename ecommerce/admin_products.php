<?php
session_start();
require_once 'database.php';
$conn = db_connect();

// Check if the user is an admin
if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Fetch all products
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="css/admin_styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <?php if(isset($_SESSION['email'])): ?>
            <span>Welcome, <?php echo $_SESSION['name']; ?></span>
            <form action="process/logout_process.php" method="post" style="display: inline;">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        <?php endif; ?>
    </header>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="admin.php">Dashboard</a>
        <a href="admin.php">Manage Users</a>
        <a href="admin_orders.php">View Orders</a>
        <a href="admin_products.php">Manage Products</a>
        <div class="admin-section">
            <a href="admin_settings.php">Settings</a>
        </div>
    </div>

    <main class="main-content">
    <h2>Product Management</h2>

        <div class="mb-3">
            <a href="add_product.php" class="btn btn-success">Add New Product</a>
        </div>
        
            <table class="table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <form action="process/delete_product.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products found. <a href="add_product.php">Add a new product!</a></p>
        <?php endif; ?>
    </main>
</body>
</html>