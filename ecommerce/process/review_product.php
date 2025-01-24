<?php
session_start();
require_once '../database.php';
$conn = db_connect();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $review = $_POST['review'];
    $user_id = $_SESSION['id']; // Assuming user's ID is stored in session

    // Prepare the query to insert the review into the database
    $query = "INSERT INTO product_reviews (product_id, user_id, review) VALUES ('$product_id', '$user_id', '$review')";
    
    if (mysqli_query($conn, $query)) {
        // Redirect back to the product details page
        header("Location: ../product_details.php?id=$product_id");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
