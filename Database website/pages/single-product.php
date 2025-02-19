<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: ../auth/login.php');
    exit;
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user']) && isset($_SESSION['role']);

// Retrieve the user's name and role from the session
$username = $_SESSION['user'];
$role = $_SESSION['role'];


// Debugging code
// echo '<pre>';
// print_r($_SESSION);  // Print session data
// echo '</pre>';

?>



<?php

include('../includes/connection.php'); // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Get product ID from GET request
if (!isset($_GET['product_id'])) {
    echo "Product not specified.";
    exit;
}

$product_id = intval($_GET['product_id']);

// Fetch product details
$product_query = "SELECT * FROM products WHERE id = ?";
$product_stmt = sqlsrv_query($con, $product_query, [$product_id]);

if ($product_stmt === false || sqlsrv_has_rows($product_stmt) === false) {
    echo "Product not found.";
    exit;
}

$product = sqlsrv_fetch_array($product_stmt, SQLSRV_FETCH_ASSOC);

// Fetch product reviews
$reviews_query = "SELECT r.review_text, r.rating, r.created_at, u.username 
                  FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.product_id = ?
                  ORDER BY r.created_at DESC";
$reviews_stmt = sqlsrv_query($con, $reviews_query, [$product_id]);
$reviews = [];
while ($review = sqlsrv_fetch_array($reviews_stmt, SQLSRV_FETCH_ASSOC)) {
    $reviews[] = $review;
}

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../assets/logo-white.svg" type="image/x-icon">
    <title>Single Product || Hazem Store</title>
    <!-- font awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
    />

    <!-- main css -->
    <link rel="stylesheet" href="../css/style.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
  </head>
  <body>
<!-- navbar -->
<nav class="navbar m-2">
          <div class="nav-center2 d-flex justify-content-between align-items-center w-100 px-5">
            <!-- links -->
            <div>
              <button class="toggle-nav">
                <i class="fas fa-bars"></i>
              </button>
              <ul class="nav-links text-dark">
                <li>
                  <a href="../customer.php" class="nav-link"> home </a>
                </li>
                <li>
                  <a href="products.php" class="nav-link"> products </a>
                </li>
                <li>
                  <a href="about.php" class="nav-link"> about </a>
                </li>
              </ul>
            </div>
            <!-- logo -->
            <a href="/" class="pointer">
              <img src="../assets/logo-black.svg" class="nav-logo2" alt="logo" />
            </a>

            <!-- cart icon -->

            <?php if ($isLoggedIn && $role == 'customer'): ?>

            <div class="toggle-container">
              <button class="toggle-cart">
                <i class="fas fa-shopping-cart text-dark"></i>
              </button>
              <span class="cart-item-count">0</span>
            </div>

            <?php else: ?>

              <div class="rounded bg-info p-2 text-light badge">admin</div>

            <?php endif ?>


            <!-- login -->
            <div class="p-2 shadow-sm ">
                  <h3 class="text-center text-dark" style="color: white; font-size: 20px;">
                      <?php if ($isLoggedIn && $role == 'customer'): ?>
                          Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?> </br>!
                          You : <?php echo htmlspecialchars($_SESSION['role']); ?>
                          <?php print_r($_SESSION['user_id']); ?>
                          <!-- Show a logout button if the user is logged in -->
                          <div class="container mt-5">
                          <!-- Dropdown -->
                          <div class="dropdown">
                              <button 
                                  class="btn btn-info dropdown-toggle" 
                                  type="button" 
                                  id="dropdownMenuButton" 
                                  data-bs-toggle="dropdown" 
                                  aria-expanded="false">
                                  Customer Menu
                              </button>
                              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                  <li><a class="dropdown-item" href="customer_profile.php">Profile</a></li>
                                  <li><hr class="dropdown-divider"></li>
                                  <li><a class="dropdown-item text-danger" href="../auth/logout.php">Logout</a></li>
                              </ul>
                          </div>
                      </div>
                      <?php elseif ($isLoggedIn && $role == 'admin'): ?>
                        Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?> </br>!
                          You : <?php echo htmlspecialchars($_SESSION['role']); ?>
                          <?php print_r($_SESSION['user_id']); ?>
                          <!-- Show a logout button if the user is logged in -->
                          <div class="container mt-5">
                          <!-- Dropdown -->
                          <div class="dropdown">
                              <button 
                                  class="btn btn-info dropdown-toggle" 
                                  type="button" 
                                  id="dropdownMenuButton" 
                                  data-bs-toggle="dropdown" 
                                  aria-expanded="false">
                                  Admin Menu
                              </button>
                              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                  <li><a class="dropdown-item" href="admin_profile.php">Profile</a></li>
                                  <li><hr class="dropdown-divider"></li>
                                  <li><a class="dropdown-item text-danger" href="../auth/logout.php">Logout</a></li>
                              </ul>
                          </div>
                      </div>

                      <?php else: ?>
                          Please log in to continue.
                          <!-- Show login button if the user is not logged in -->
                          <a href="pages/login.php" class="btn btn-primary w-100">Login</a>
                      <?php endif; ?>
                  </h3>
              </div>
            
              
          </div>    
        </nav>
    <!-- end of navbar-->
    <!-- page hero -->
    <section class="page-hero">
      <div class="section-center">
        <h3 class="page-hero-title">Home / Single Product</h3>
      </div>
    </section>
    <!-- product info -->
    <section class="single-product section">
      <div class="section-center single-product-center">
        <img id="image"
          src="<?php echo htmlspecialchars($product['Image']); ?>"
          class="single-product-img img"
          alt=""
        />
        <article class="single-product-info">
          <div>
            <h2 class="single-product-title" id="title"><?php echo htmlspecialchars($product['title']); ?></h2>
            <span class="single-product-price" id="price"><?php echo htmlspecialchars($product['price']); ?></span>
            <div class="single-product-colors">
              <span class="product-color"></span>
              <span class="product-color product-color-red"></span>
            </div>

            <p class="single-product-desc" >
              <!-- Lorem ipsum dolor sit amet consectetur adipisicing elit. Vero quod sunt voluptates velit optio? Maiores, consequatur fuga? Expedita incidunt illo doloremque architecto, error dolore doloribus provident at temporibus laudantium mollitia! -->
              <?php echo htmlspecialchars($product['description']); ?>
            </p>

            <button 
              class="btn add-to-cart"
              data-Image="<?php echo htmlspecialchars($product['Image']); ?>"
              data-id="<?php echo htmlspecialchars($product['id']); ?>" 
              data-title="<?php echo htmlspecialchars($product['title']); ?>" 
              data-price="<?php echo htmlspecialchars($product['price']); ?>">

              Purchase now
            </button>

          </div>
        </article>
      </div>
    </section>
    <!-- Add a Review Section -->
    <section class="add-review section py-5">
        <div class="container">
            <h3 class="text-center mb-4">Write a Review</h3>
            <form method="POST" action="../review.php" class="bg-light p-4 rounded shadow-sm">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">

                <div class="mb-3">
                    <label for="review_text" class="form-label">Your Review</label>
                    <textarea 
                        id="review_text" 
                        name="review_text" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Write your review here..." 
                        required>
                    </textarea>
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <select id="rating" name="rating" class="form-select" required>
                        <option value="" disabled selected>Select a rating</option>
                        <option value="1">1 - Poor</option>
                        <option value="2">2 - Fair</option>
                        <option value="3">3 - Good</option>
                        <option value="4">4 - Very Good</option>
                        <option value="5">5 - Excellent</option>
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews section py-5">
        <div class="container">
            <h3 class="text-center mb-4">Customer Reviews</h3>
            <div class="reviews-list">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item mb-3 p-3 bg-light rounded shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                                <span class="badge bg-success">
                                    <?php echo htmlspecialchars($review['rating']); ?>/5
                                </span>
                            </div>
                            <p class="mt-2 mb-1"><?php echo htmlspecialchars($review['review_text']); ?></p>
                            <small class="text-muted">
                                <?php echo htmlspecialchars($review['created_at']->format('Y-m-d H:i')); ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">No reviews yet for this product.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <!-- sidebar -->
    <div class="sidebar-overlay">
      <aside class="sidebar">
        <!-- close -->
        <button class="sidebar-close">
          <i class="fas fa-times"></i>
        </button>
        <!-- links -->
        <ul class="sidebar-links">
          <li>
            <a href="../customer.php" class="sidebar-link">
              <i class="fas fa-home fa-fw"></i>
              home
            </a>
          </li>
          <li>
            <a href="products.php" class="sidebar-link">
              <i class="fas fa-couch fa-fw"></i>
              products
            </a>
          </li>
          <li>
            <a href="about.php" class="sidebar-link">
              <i class="fas fa-book fa-fw"></i>
              about
            </a>
          </li>
        </ul>
      </aside>
    </div>
    <!-- end of sidebar -->
    <!-- cart -->
    <div class="cart-overlay">
      <aside class="cart">
        <button class="cart-close">
          <i class="fas fa-times"></i>
        </button>
        <header>
          <h3 class="text-slanted bg-light">your bag</h3>
        </header>
        <!-- cart items -->
        <div class="cart-items">
          <!-- ----------------------------------------------------------------------- -->
          <!--                                cartList                                 -->
          <!-- ----------------------------------------------------------------------- -->
        </div>
        <!-- footer -->
        <footer>
          <h3 class="cart-total text-slanted" id="total">total : $12.99</h3>
          <button class="cart-checkout btn w-100 bg-primary text-light">checkout</button>
        </footer>
      </aside>
    </div>


      <!-- Bootstrap JS -->
      <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

      <!-- Global App Scripts -->
      <script defer src="../js/app.js"></script>

      <!-- Cart Functionality -->
      <script defer src="../js/addToCart.js"></script>
      <script defer src="../js/loadDataCart.js"></script>

  </body>
</html>















