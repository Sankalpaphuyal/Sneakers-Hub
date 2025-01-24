<?php
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['email'])) {
  header('Location: login.php');
  exit;
}

require_once 'database.php';

$conn = db_connect();
$user_id = $_SESSION['id']; // Assuming user_id is stored in session

// Fetch orders for the logged-in user
$query = "SELECT o.id AS order_id, o.address, o.payment_method, o.notes, o.total_amount, o.created_at, 
                 oi.product_name, oi.price, oi.quantity 
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          WHERE o.user_id = '$user_id'
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order History</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/card_styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
  <?php
  if (isset($_SESSION['error'])) {
    echo "<span class='error-text auth-err'>" . $_SESSION['error'] . "</span>";
    unset($_SESSION['error']);
  }
  if (isset($_SESSION['success'])) {
    echo "<span class='success-text auth-err'>" . $_SESSION['success'] . "</span>";
    unset($_SESSION['success']);
  }
  ?>

  <!-- Header -->
  <header class="sticky-header">
    <div class="container">
      <div class="logo">SNEAKERS HUB</div>
      <nav>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="index.php#products">Products</a></li>
          <li><a href="index.php#contact">Contact Us</a></li>
          <li><a href="cart.php">Cart</a></li>
          <li><a href="orders.php">Order</a></li>
          <li>
            <?php
            if (isset($_SESSION['email'])) {
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

  <!-- Order History Section -->
  <main class="container mt-4">
    <div class="row">
      <?php if (mysqli_num_rows($result) > 0) { ?>
        <?php
        $current_order_id = null;
        while ($row = mysqli_fetch_assoc($result)) {
          if ($row['order_id'] !== $current_order_id) {
            // Display order header for a new order
            if ($current_order_id !== null) {
              echo "</table></div></div>"; // Close the previous order table and card
            }
            $current_order_id = $row['order_id'];
            ?>
            <div class="col-md-4 mb-4">
              <div class="order-card">
                <h3>Order ID: <?php echo $row['order_id']; ?></h3>
                <p>Date: <?php echo $row['created_at']; ?></p>
                <p>Address: <?php echo $row['address']; ?></p>
                <p>Payment Method: <?php echo $row['payment_method']; ?></p>
                <p>Notes: <?php echo $row['notes']; ?></p>
                <p>Total Amount: $<?php echo $row['total_amount']; ?></p>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Product Name</th>
                      <th>Price</th>
                      <th>Quantity</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
          <?php
          }
          // Display order items
          ?>
          <tr>
            <td><?php echo $row['product_name']; ?></td>
            <td>$<?php echo $row['price']; ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td>$<?php echo $row['price'] * $row['quantity']; ?></td>
          </tr>
          <?php
        }
        if ($current_order_id !== null) {
          echo "</tbody></table></div></div>"; // Close the last order table and card
        }
        ?>
      <?php } else { ?>
        <div class="col-12">
          <p>No orders found.</p>
        </div>
      <?php } ?>
    </div>
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