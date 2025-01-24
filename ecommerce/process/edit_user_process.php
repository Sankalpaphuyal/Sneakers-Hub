<?php
session_start();
require_once '../database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if (isset($_POST['id'], $_POST['name'], $_POST['email'], $_POST['role'])) {
    $user_id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Check if password is provided
    $password = isset($_POST['password']) && !empty($_POST['password']) ? hash('sha256', $_POST['password']) : null;

    $conn = db_connect();

    // If password is not provided, update the user without changing the password
    if ($password) {
        $sql = "UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $name, $email, $password, $role, $user_id);
    } else {
        $sql = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $name, $email, $role, $user_id);
    }

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        $_SESSION['success'] = "User updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update user.";
    }

    // Close the statement and redirect back to the admin panel
    $stmt->close();
    $conn->close();
    header('Location: ../admin.php');
    exit;
} else {
    $_SESSION['error'] = 'Invalid data received.';
    header('Location: ../admin.php');
    exit;
}
