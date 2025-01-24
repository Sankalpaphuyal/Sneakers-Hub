<?php
session_start();
require_once '../database.php';

$conn = db_connect();

if (isset($_POST['cart_id'])) {
  $cart_id = $_POST['cart_id'];
  $query = "DELETE FROM cart WHERE id = '$cart_id'";

  if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Item removed from cart.";
  } else {
    $_SESSION['error'] = "Failed to remove item from cart.";
  }
} else {
  $_SESSION['error'] = "Invalid request.";
}

header('Location: ../cart.php');
exit;
?>
