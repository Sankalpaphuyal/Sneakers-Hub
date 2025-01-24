<?php
session_start();
require_once '../database.php';
$conn = db_connect();

// Get POST data
$user_id = $_POST['user_id'];
$user_name = $_POST['user_name'];
$product_name = $_POST['product_name'];
$product_price = $_POST['product_price'];
$created_at = date('Y-m-d H:i:s');

// Insert data into `cart` table
$sql = "INSERT INTO cart (user_id, user_name, product_name, created_at, price) VALUES ('$user_id', '$user_name', '$product_name', '$created_at', '$product_price')";

if ($conn->query($sql) === TRUE) {
    $_SESSION['success'] = 'Item added to cart';
    header('Location: ../index.php');
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
    $_SESSION['error'] = "Error: " . $conn->error;
    header('Location: ../index.php');
    
}

$conn->close();
?>
