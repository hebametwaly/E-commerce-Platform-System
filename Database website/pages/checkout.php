<?php
// Start session
session_start();

// Include database connection
include('../includes/connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: ../auth/login.php');
    exit;
}

// Define the query to fetch cart data
$query = "SELECT product_title, product_price, quantity FROM cart WHERE user_id = ?";
$params = array($_SESSION['user_id']); // Replace with your user identifier

// Execute the query
$stmt = sqlsrv_query($con, $query, $params);

if ($stmt === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}

// Fetch all cart items and calculate the total price
$cartItems = [];
$total = 0;

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $cartItems[] = $row; // Store each cart item
    $total += $row['product_price'] * $row['quantity']; // Use 'product_price' instead of 'price'
}

// Free the statement
sqlsrv_free_stmt($stmt);

?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout || Hazem Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
  </head>

  <body class="bg-light">
    <div class="container">
      <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="../assets/logo-black.svg" alt="Logo" width="200" height="100">
        <h2>Checkout</h2>
      </div>

      <div class="row">
        <!-- Cart Summary -->
        <div class="col-md-4 order-md-2 mb-4">
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">Your cart</span>
            <span class="badge bg-secondary"><?php echo count($cartItems); ?></span>
          </h4>
          <ul class="list-group mb-3">
            <?php foreach ($cartItems as $item): ?>
              <li class="list-group-item d-flex justify-content-between lh-sm">
                <div>
                  <h6 class="my-0"><?php echo htmlspecialchars($item['product_title']); ?></h6>
                  <small class="text-muted"><?php echo htmlspecialchars($item['product_title']); ?></small>
                </div>
                <span class="text-muted"><?php echo '$' . number_format($item['product_price'], 2); ?></span>
              </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between">
              <span>Total (USD)</span>
              <strong><?php echo '$' . number_format($total, 2); ?></strong>
            </li>
          </ul>

          <!-- Promo Code -->
          <form class="card p-2">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Promo code">
              <button type="submit" class="btn btn-secondary">Redeem</button>
            </div>
          </form>
        </div>

        <!-- Billing Form -->
        <div class="col-md-8 order-md-1">
          <h4 class="mb-3">Billing address</h4>
          <form action="../orders/checkoutBTN.php" method="POST" class="needs-validation" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="firstName">First name</label>
                <input type="text" class="form-control" id="firstName" name="first_name" required>
                <div class="invalid-feedback">First name is required.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="lastName">Last name</label>
                <input type="text" class="form-control" id="lastName" name="last_name" required>
                <div class="invalid-feedback">Last name is required.</div>
              </div>
            </div>

            <div class="mb-3">
              <label for="email">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
              <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>

            <div class="mb-3">
              <label for="address">Address</label>
              <input type="text" class="form-control" id="address" name="address" required>
              <div class="invalid-feedback">Please enter your address.</div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="city">City</label>
                <input type="text" class="form-control" id="city" name="city" required>
                <div class="invalid-feedback">City is required.</div>
              </div>
              <div class="col-md-3 mb-3">
                <label for="state">State</label>
                <input type="text" class="form-control" id="state" name="state" required>
                <div class="invalid-feedback">State is required.</div>
              </div>
              <div class="col-md-3 mb-3">
                <label for="zip">Zip</label>
                <input type="text" class="form-control" id="zip" name="zip" required>
                <div class="invalid-feedback">Zip code is required.</div>
              </div>
            </div>

            <hr class="mb-4">

            <h4 class="mb-3">Payment</h4>
            <div class="d-block my-3">
              <div class="form-check">
                <input id="credit" name="payment_method" value="credit" type="radio" class="form-check-input" checked required>
                <label class="form-check-label" for="credit">Credit card</label>
              </div>
              <div class="form-check">
                <input id="paypal" name="payment_method" value="paypal" type="radio" class="form-check-input" required>
                <label class="form-check-label" for="paypal">PayPal</label>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="cc-name">Name on card</label>
                <input type="text" class="form-control" id="cc-name" name="cc_name" required>
                <div class="invalid-feedback">Name on card is required.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="cc-number">Card number</label>
                <input type="text" class="form-control" id="cc-number" name="cc_number" required>
                <div class="invalid-feedback">Card number is required.</div>
              </div>
            </div>

            <hr class="mb-4">
            <button class="btn btn-primary btn-lg btn-block" type="submit">Place order</button>
          </form>
        </div>
      </div>

      <footer class="my-5 text-muted text-center">
        <p class="mb-1">&copy; 2024 Hazem Store</p>
      </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      (function() {
        'use strict';
        window.addEventListener('load', function() {
          var forms = document.getElementsByClassName('needs-validation');
          Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
              if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
              }
              form.classList.add('was-validated');
            }, false);
          });
        }, false);
      })();
    </script>
  </body>
</html>
