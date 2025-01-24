<?php
session_start();
require_once 'database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$conn = db_connect();

// Check if 'id' is set in the URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user data from the database
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id); // 'i' means integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        // If user not found
        $_SESSION['error'] = 'User not found.';
        header('Location: admin.php');
        exit;
    }
} else {
    // Redirect if no ID is passed
    header('Location: admin.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/edit_styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Edit User: <?php echo $user['name']; ?></h1>
            <a href="admin.php" class="btn btn-primary">Back to Admin</a>
        </div>
    </header>

    <main class="container mt-4">
        <div class="container">
        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <form action="process/edit_user_process.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

            <div class="mb-3">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Leave blank to keep current password" class="form-control">
            </div>

            <div class="mb-3">
                <label for="role">Role:</label>
                <select name="role" id="role" class="form-control">
                    <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update User</button>
        </form>
        </div>
    </main>
</body>
</html>