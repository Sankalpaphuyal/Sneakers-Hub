<?php
session_start();


if(!isset($_SESSION['email']) ) {
  header('Location: login.php');
}

require_once 'database.php';

$conn = db_connect();

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/card_styles.css">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> 
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

  <title>SNEAKERSHUB</title>
</head>
<body>
<?php
if(isset($_SESSION['error'])){
  echo "<span class='error-text auth-err'>". $_SESSION['error'] . "</span>";
  unset($_SESSION['error']);
}
if(isset($_SESSION['success'])){
  echo "<span class='success-text auth-err'>". $_SESSION['success'] . "</span>";
  unset($_SESSION['success']);
}
?>


  <!-- Header -->
  <header class="sticky-header">
    <div class="container">
      <div class="logo">SNEAKERS HUB</div>
      
      <nav>
        
        <ul>
          <li><a href="#hero">Home</a></li>
          <li><a href="#products">Products</a></li>
          <li><a href="#contact">Contact Us</a></li>
          <li><a href="cart.php">Cart</a></li>
          <li><a href="orders.php">Order</a></li> 
          <li>
            <?php
            if(isset($_SESSION['email'])) {
              echo "<span>Welcome, " . $_SESSION['name'] . "</span>";
            ?>
              <form action="process/logout_process.php" method="post" style="display: inline;">
                <button type="submit" class="btn btn-danger">Logout</button>
              </form>
            <?php
            } 
            ?>
          </li>
        </ul>
      </nav>
    </div>
  </header>



  <!-- Hero Section -->
<section id="hero" class="hero" style="background-image: url('https://images.pexels.com/photos/6153367/pexels-photo-6153367.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1'); background-size: cover; background-position: center; color: white; text-align: center; padding: 50px 0;">
  <div class="hero-content">
    <h1>SNEAKERS HUB</h1>
    <p>Your one-stop shop for amazing products at unbeatable prices.</p>
    <a href="#products" class="btn hero-btn">Start Shopping</a>
  </div>
</section>

<!-- Products Section -->
<main id="products">
  <h2 class="section-title">Featured Products</h2>
  <section class="products-section">
    <div class="item-container">
        <?php
        // Fetch products from the database
        $query = "SELECT id, name, category, price, image_url FROM products";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Loop through each product and display it
            while ($row = mysqli_fetch_assoc($result)) {
                $product_id = $row['id'];
                $product_name = $row['name'];
                $product_category = $row['category'];
                $product_price = $row['price'];
                $product_image = $row['image_url'];
                ?>
                <div class="item-card">
                    <div class="img-container">
                      <a href="product_details.php?id=<?php echo $product_id; ?>">
                          <img src="<?php echo $product_image; ?>" alt="<?php echo $product_name; ?>" class="product-image">
                      </a>
                    </div>
                    <h1 class="item-name"><?php echo $product_name; ?></h1>
                    <h2 class="item-category"><?php echo $product_category; ?></h2>
                    <h3 class="item-price">$<?php echo $product_price; ?></h3>
                    <form action="./process/cart_process.php" method="post" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['id']; ?>">
                        <input type="hidden" name="user_name" value="<?php echo $_SESSION['name']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo $product_name; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $product_price; ?>">
                        <button class="cart-btn" type="submit">Add to Cart</button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo "<p>No products available at the moment. Please check back later.</p>";
        }
        ?>
    </div>
  </section>
</main>



  <!-- Footer -->
  <footer id="contact">
    <div class="footer-container">
      <p>&copy; 2024 SNEAKERSHUB. All rights reserved.</p>
      <div class="social-icons">
        <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
      </div>
      <div>
        <a href="about.html" class="footer-link">About Us</a> |
        <a href="contact.html" class="footer-link">Contact Us</a>
      </div>
    </div>
  </footer>

  <script src="js/script.js"></script>

  
</body>
</html>
