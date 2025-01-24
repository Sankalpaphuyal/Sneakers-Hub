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

// Fetch cart items for the logged-in user
$query = "SELECT * FROM cart WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
  <link rel="stylesheet" href="css/cart_styles.css"> <!-- Link to the CSS file -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

</head>
<body>
  
  <!-- Header -->
  <header class="sticky-header">
    <div class="container mt-3">
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
  
  <main class="container mt-4">
    <?php if (mysqli_num_rows($result) > 0) { ?>
      <!-- Checkout Button -->
      <div class="mb-3">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#checkoutModal">Checkout</button>
      </div>

      <!-- Cart Table -->
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?php echo $row['product_name']; ?></td>
              <td>$<?php echo $row['price']; ?></td>
              <td>
                <div class="quantity-control">
                  <input type="number" class="quantity" value="1" min="1">
                </div>
              </td>
              <td class="total-price">$<?php echo $row['price']; ?></td>
              <td>
                <form action="process/remove_cart.php" method="post">
                  <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                  <button type="submit" class="btn btn-danger">Remove</button>
                </form>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
      <div class="text-end">
        <h4>Grand Total: <span id="grand-total">$0.00</span></h4>
      </div>
    <?php } else { ?>
      <p>Your cart is empty. <a href="index.php">Shop now!</a></p>
    <?php } ?>
  </main>

 <!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="checkoutModalLabel">Confirm Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="checkoutForm">
          <div class="mb-3">
            <label for="address" class="form-label">Shipping Address</label>
            <input type="text" class="form-control" id="address" name="address" required>
          </div>
          <div class="mb-3">
            <label for="paymentMethod" class="form-label">Payment Method</label>
            <select class="form-select" id="paymentMethod" name="paymentMethod" required>
              <option value="credit_card">Credit Card</option>
              <option value="paypal">E-Sewa</option>
              <option value="cash_on_delivery">Cash on Delivery</option>
            </select>
          </div>
          <!-- QR Code Section -->
          <div class="mb-3" id="qrCodeSection" style="display: none;">
            <label class="form-label">Scan QR Code to Pay</label>
            <img src="images/QR.jpg" alt="QR Code" class="img-fluid">
            <p class="text-muted mt-2">Please scan the QR code to complete your payment.</p>
          </div>
          <div class="mb-3">
            <label for="notes" class="form-label">Additional Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="confirmCheckout">Confirm Order</button>
      </div>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Order Confirmed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Your order has been placed successfully!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>



  <!-- Link to the external JavaScript file -->
  <script src="js/cart_script.js"></script>
  <!-- Bootstrap JS -->
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>