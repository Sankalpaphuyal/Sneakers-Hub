<?php
session_start();

if (!isset($_SESSION['email'])) {
  header('Location: login.php');
  exit;
}

require_once 'database.php';
$conn = db_connect();

$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($product_id == 0) {
  die('Invalid product ID');
}

// Fetch product details from the database
$query = "SELECT * FROM products WHERE id = '$product_id'";
$product_result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($product_result);

// Fetch the reviews for this product
$query_reviews = "SELECT r.review, r.created_at, u.name FROM product_reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = '$product_id' ORDER BY r.created_at DESC";
$reviews_result = mysqli_query($conn, $query_reviews);

if (!$product) {
  die('Product not found');
}

// Fetch the average rating
$query_avg = "SELECT AVG(rating) AS avg_rating FROM product_ratings WHERE product_id = '$product_id'";
$avg_result = mysqli_query($conn, $query_avg);
$avg_rating = mysqli_fetch_assoc($avg_result);
$avg_rating = $avg_rating['avg_rating'] ? number_format($avg_rating['avg_rating'], 1) : 0;

// Fetch the user's rating for this product (if exists)
$user_id = $_SESSION['id'];
$query_user_rating = "SELECT rating FROM product_ratings WHERE product_id = '$product_id' AND user_id = '$user_id'";
$user_rating_result = mysqli_query($conn, $query_user_rating);
$user_rating = mysqli_fetch_assoc($user_rating_result);
$user_rating = $user_rating ? $user_rating['rating'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Details</title>
  <link rel="stylesheet" href="css/product_details_styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
  <header>
    <div class="container">
      <h1><?php echo $product['name']; ?></h1>
      <a href="index.php" class="btn btn-primary">Back to Products</a>
    </div>
  </header>
  
  <main class="container">
    <div class="product-details">
      <div class="product-image">
        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
      </div>
      <div class="product-info">
        <h3><?php echo $product['name']; ?></h3>
        <p>Category: <?php echo $product['category']; ?></p>
        <p>Price: $<?php echo $product['price']; ?></p>
        <p>Description: <?php echo $product['description']; ?></p>
        
        <!-- Average rating -->
        <p>Average Rating: <?php echo $avg_rating; ?> / 5</p>

        <!-- User's rating form -->
        <div class="rating-info">
          <p>Your Rating:</p>
          <form action="process/rate_product.php" method="post">
            <div class="stars">
                <?php 
                for ($i = 1; $i <= 5; $i++) {
                    $star_class = ($i <= $user_rating) ? 'star filled' : 'star'; 
                ?>
                <span class="<?php echo $star_class; ?>" data-value="<?php echo $i; ?>">&#9733;</span> 
                <?php } ?>
            </div>
            <input type="hidden" name="rating" id="rating_value" value="<?php echo $user_rating ? $user_rating : 0; ?>">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <button type="submit" class="btn btn-success">Submit Rating</button>
            </form>
        </div>
        
        <?php if ($user_rating !== null) { ?>
          <p>You have rated this product: <?php echo $user_rating; ?> / 5</p>
        <?php } ?>

        <!-- Add to Cart with Shoe Size -->
        <form action="./process/cart_process.php" method="post" style="display: inline;">
          <p>Shoe Size:</p>
          <select name="shoe_size" class="form-select" style="width: auto;" required>
            <option value="" disabled selected>Select Size</option>
            <?php
            // Example sizes array for shoe products
            $shoe_sizes = [6, 7, 8, 9, 10, 11, 12];
            foreach ($shoe_sizes as $size) {
              echo "<option value=\"$size\">$size</option>";
            }
            ?>
          </select>

          <input type="hidden" name="user_id" value="<?php echo $_SESSION['id']; ?>">
          <input type="hidden" name="user_name" value="<?php echo $_SESSION['name']; ?>">
          <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
          <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
          <button class="cart-btn" type="submit">Add to Cart</button>
        </form>
      </div>
    </div>

    <!-- Review Views -->
    <div class="reviews">
    <h4>Reviews</h4>
    <?php if (mysqli_num_rows($reviews_result) > 0) { ?>
        <?php while ($review = mysqli_fetch_assoc($reviews_result)) { ?>
            <div class="review">
                <p><strong><?php echo $review['name']; ?></strong> (<?php echo $review['created_at']; ?>)</p>
                <p><?php echo $review['review']; ?></p>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>No reviews yet. Be the first to write one!</p>
    <?php } ?>
    </div>
    
    <!-- Review submission form -->
    <h4>Leave a Review</h4>
    <form action="process/review_product.php" method="post">
        <textarea name="review" rows="5" placeholder="Write your review here..." required></textarea>
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
  </main>

  <script>
    // Add event listeners to stars
    const stars = document.querySelectorAll('.star');
    stars.forEach(star => {
      star.addEventListener('click', () => {
        let ratingValue = star.getAttribute('data-value');
        document.getElementById('rating_value').value = ratingValue;

        // Update the UI to show the selected stars
        stars.forEach(s => s.classList.remove('filled'));
        for (let i = 0; i < ratingValue; i++) {
          stars[i].classList.add('filled');
        }
      });
    });
  </script>

</body>
</html>
