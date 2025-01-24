<?php
session_start();
require_once '../database.php';

if (!isset($_SESSION['email'])) {
  header('Location: ../login.php');
  exit;
}

$conn = db_connect();
$user_id = $_SESSION['id'];
$product_id = $_POST['product_id'];
$rating = (int) $_POST['rating'];

// Check if user already rated the product
$query = "SELECT id FROM product_ratings WHERE product_id = '$product_id' AND user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
  // Update rating if already exists
  $update_query = "UPDATE product_ratings SET rating = '$rating' WHERE product_id = '$product_id' AND user_id = '$user_id'";
  mysqli_query($conn, $update_query);
  $_SESSION['success'] = 'Your rating has been updated.';
} else {
  // Insert new rating
  $insert_query = "INSERT INTO product_ratings (product_id, user_id, rating) VALUES ('$product_id', '$user_id', '$rating')";
  mysqli_query($conn, $insert_query);
  $_SESSION['success'] = 'Your rating has been submitted.';
}

header("Location: ../product_details.php?id=$product_id");
exit;
?>
