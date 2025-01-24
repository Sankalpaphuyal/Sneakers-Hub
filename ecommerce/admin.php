<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

require_once 'database.php';
$conn = db_connect();

// Fetch users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>
    <!-- Header -->
    <header>
        <h1>Admin Panel</h1>
        <?php
            if(isset($_SESSION['email'])) {
              echo "<span>Welcome, " . $_SESSION['name'] . "</span>";
            ?>
              <form action="process/logout_process.php" method="post" style="display: inline;">
                <button type="submit" class="btn btn-danger">Logout</button>
              </form>
            <?php
            } 
            ?>
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

    <!-- Main Content -->
    <div class="main-content">
        <h2>User Management</h2>
        
        <!-- User Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Created At</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo '********'; // Displaying a placeholder for password ?></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($row['created_at'])); ?></td>
                            <td><?php echo ucfirst($row['role']); ?></td>
                            <td>
                                <!-- Admin Actions: Delete, Edit, etc. -->
                                                          <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                
                                <!-- Delete button -->
                                <form action="process/delete_user.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7">No users found.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
