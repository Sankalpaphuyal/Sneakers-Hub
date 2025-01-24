<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Parse JSON input
    $data = json_decode(file_get_contents('php://input'), true);

    // Get product ID and size from input data
    $productId = $data['productId'];
    $size = $data['size'];
    $userId = 1; // Temporary, replace with session-based user ID (e.g., $_SESSION['id'])

    // Validate input
    if (empty($productId) || empty($size)) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID and size are required']);
        exit;
    }

    // Insert the product, size, and user ID into the cart
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, size) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $userId, $productId, $size);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add product to cart']);
    }

    $stmt->close();
}
?>
