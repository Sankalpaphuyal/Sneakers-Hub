<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

require_once '../database.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Debugging: Log the received data
error_log('Received data: ' . print_r($data, true));

// Validate the data
if (!isset($data['address'], $data['paymentMethod'], $data['cartItems'])) {
  echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
  exit;
}

$user_id = $_SESSION['id'];
$address = $data['address'];
$paymentMethod = $data['paymentMethod'];
$notes = $data['notes'] ?? ''; // Optional field
$cartItems = $data['cartItems'];

// Calculate the total amount
$totalAmount = 0;
foreach ($cartItems as $item) {
  $totalAmount += $item['price'] * $item['quantity'];
}

$conn = db_connect();

// Insert the order into the database
$sql = "INSERT INTO orders (user_id, address, payment_method, notes, total_amount) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('isssd', $user_id, $address, $paymentMethod, $notes, $totalAmount);

if ($stmt->execute()) {
  $order_id = $stmt->insert_id; // Get the ID of the newly created order

  // Insert order items
  $sql = "INSERT INTO order_items (order_id, product_name, price, quantity) 
          VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);

  foreach ($cartItems as $item) {
    $productName = $item['productName'];
    $price = $item['price'];
    $quantity = $item['quantity'];
    $stmt->bind_param('isdi', $order_id, $productName, $price, $quantity);
    $stmt->execute();
  }

  // Clear the cart
  $sql = "DELETE FROM cart WHERE user_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $user_id);
  $stmt->execute();

  echo json_encode(['success' => true]);
} else {
  // Debugging: Log the database error
  error_log('Database error: ' . $conn->error);
  echo json_encode(['success' => false, 'message' => 'Failed to place order.']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>