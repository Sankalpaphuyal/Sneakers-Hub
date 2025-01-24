<?php
session_start();
require_once '../database.php';

if (isset($_POST['id'])) {
    $user_id = $_POST['id'];
    $conn = db_connect();

    // Prepare and execute the delete query
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id); // 'i' means integer
    $stmt->execute();

    // Check if the query was successful
    if ($stmt->affected_rows > 0) {
        // If the user was deleted, redirect back to the admin page with success message
        $_SESSION['success'] = "User deleted successfully.";
    } else {
        // If something went wrong
        $_SESSION['error'] = "Failed to delete user.";
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();

    // Redirect back to the admin page
    header('Location: ../admin.php');
    exit;
} else {
    // Redirect to the admin page if no user ID is passed
    header('Location: ../admin.php');
    exit;
}
