<?php
session_start();

// Redirect to login if the user is not logged in or is not an admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once 'database.php';
$conn = db_connect();

// Fetch all orders from the database
$query = "SELECT o.id AS order_id, u.name AS user_name, o.address, o.payment_method, o.notes, o.total_amount, o.created_at, o.status 
          FROM orders o
          JOIN users u ON o.user_id = u.id
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Order status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update order status.";
    }

    $stmt->close();
    header('Location: admin_orders.php');
    exit;
}

// Handle order deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    // Delete order items first
    $query = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $stmt->close();

    // Delete the order
    $query = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $order_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Order deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete order.";
    }

    $stmt->close();
    header('Location: admin_orders.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="css/admin_styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .order-table th,
        .order-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .order-table th {
            background-color: #343a40;
            color: white;
        }

        .order-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    ?>

    <header>
        <h1>Admin Panel</h1>
        <?php if (isset($_SESSION['email'])): ?>
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

    <!-- Orders Table -->
    <main class="main-content">
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User Name</th>
                    <th>Address</th>
                    <th>Payment Method</th>
                    <th>Notes</th>
                    <th>Total Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['order_id']; ?></td>
                        <td><?php echo $row['user_name']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><?php echo $row['payment_method']; ?></td>
                        <td><?php echo $row['notes']; ?></td>
                        <td>$<?php echo $row['total_amount']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <form action="admin_orders.php" method="post" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <select name="status" class="status-select" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $row['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="completed" <?php echo $row['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $row['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td class="action-buttons">
                            <form action="admin_orders.php" method="post" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                <button type="submit" name="delete_order" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</body>
</html>