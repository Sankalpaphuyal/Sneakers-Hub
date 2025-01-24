<?php
session_start();
require_once 'database.php';
$conn = db_connect();

// Check if the user is an admin
if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header('Location: admin_products.php');
    exit;
}

// Fetch the product details
$query = "SELECT * FROM products WHERE id = '$product_id'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: admin_products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission to update the product
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];

    // Update the product in the database
    $query = "UPDATE products SET name = '$name', category = '$category', price = '$price', description = '$description', image_url = '$image_url' WHERE id = '$product_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['success'] = 'Product updated successfully';
        header('Location: admin_products.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error updating product';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="css/edit_styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Edit Product</h1>
            <a href="admin_products.php" class="btn btn-primary">Back to Products</a>
        </div>
    </header>

    <main class="container mt-4">
        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php } ?>

        <form action="edit_product.php?id=<?php echo $product['id']; ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" id="category" name="category" class="form-control" value="<?php echo $product['category']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" required><?php echo $product['description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="image_url" class="form-label">Image URL</label>
                <input type="text" id="image_url" name="image_url" class="form-control" value="<?php echo $product['image_url']; ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update Product</button>
        </form>
    </main>
</body>
</html>
