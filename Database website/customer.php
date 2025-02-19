<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: ./auth/login.php');
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

  include("./includes/connection.php");

  $sql = 'SELECT * FROM Products';

  // Execute the query using sqlsrv_query
  $allproduct = sqlsrv_query($con, $sql);

  // Check for query execution errors
  if ($allproduct === false) {
    die(print_r(sqlsrv_errors(), true));
  }


?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="./assets/logo-white.svg" type="image/x-icon">
    <title>Home || Hazem store</title>
    <!-- font awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
    />

    <!-- main css -->
    <link rel="stylesheet" href="./css/style.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
  </head>
  <body>


        <!-- navbar -->
        <nav class="navbar">
          <div class="nav-center2 d-flex justify-content-between align-items-center w-100 px-5">
            <!-- links -->
            <div>
              <button class="toggle-nav">
                <i class="fas fa-bars"></i>
              </button>
              <ul class="nav-links text-light">
                <li>
                  <a href="/" class="nav-link"> home </a>
                </li>
                <li>
                  <a href="./pages/products.php" class="nav-link"> products </a>
                </li>
                <li>
                  <a href="./pages/about.php" class="nav-link"> about </a>
                </li>
              </ul>
            </div>
            <!-- logo -->
            <div>
              <img src="./assets/logo-white.svg" class="nav-logo2" alt="logo" />
            </div>
            <!-- cart icon -->

            <div class="toggle-container">
              <button class="toggle-cart">
                <i class="fas fa-shopping-cart"></i>
              </button>
              <span class="cart-item-count">0</span>
            </div>


            <!-- login -->
            <div class="p-2 shadow-sm ">
                  <h3 class="text-center" style="color: white; font-size: 20px;">
                      <?php if ($isLoggedIn): ?>
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
                                  <li><a class="dropdown-item" href="./pages/customer_profile.php">Profile</a></li>
                                  <li><hr class="dropdown-divider"></li>
                                  <li><a class="dropdown-item text-danger" href="./auth/logout.php">Logout</a></li>
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
    
    <!-- ----------------------------------------------------------------------- -->
    <!--                                  Hero                                   -->
    <!-- ----------------------------------------------------------------------- -->
    <section class="hero">
      <div class="hero-container">
        <h1 class="text-slanted" id="text-1">rest, relax, unwind</h1>
        <h3 class="text-slanted" id="text-2">embrace your choices - we do</h3>
        <a href="./pages/products.php" class="btn hero-btn text-slanted bg-warning">
          shop now
        </a>
      </div>
    </section>

    <!-- ----------------------------------------------------------------------- -->
    <!--                            featured products                            -->
    <!-- ----------------------------------------------------------------------- -->
    <section class="section featured position-relative">
      <div class="title">
        <span></span>
        <h2>Featured Products</h2>
        <span></span>
      </div>

      <div class="section-center featured-center" id="cont-products">
        <!-- Loop to display products -->
        <?php while ($row = sqlsrv_fetch_array($allproduct, SQLSRV_FETCH_ASSOC)) {

          ?>

        <div class="product-container">
          <img
            src="<?php echo htmlspecialchars($row['Image']); ?>"
            class="product-img img"
            alt="product"
          />

          <div class="product-icons">

            <a class="product-icon" 
              href="./pages/single-product.php?product_id=<?php echo htmlspecialchars($row['id']); ?>"
              data-image="<?php echo htmlspecialchars($row['Image']); ?>"
              data-id="<?php echo htmlspecialchars($row['id']); ?>" 
              data-title="<?php echo htmlspecialchars($row['title']); ?>" 
              data-price="<?php echo htmlspecialchars($row['price']); ?>">
              <i class="fas fa-search" title="View Product"></i>
            </a>


            <button 
              class="product-cart-btn product-icon add-to-cart"
              data-Image="<?php echo htmlspecialchars($row['Image']); ?>"
              data-id="<?php echo htmlspecialchars($row['id']); ?>" 
              data-title="<?php echo htmlspecialchars($row['title']); ?>" 
              data-price="<?php echo htmlspecialchars($row['price']); ?>">

              <i class="fas fa-shopping-cart" title="Purchase"></i>
            </button>
          </div>

          <footer>
            <h5 class="product-name"><?php echo htmlspecialchars($row['title']); ?></h5>
            <span class="product-price"><?php echo htmlspecialchars($row['price']); ?>$</span> </br>
            <span class="product-quantity">quantity: <?php echo htmlspecialchars($row['stock_quantity']); ?></span>
          </footer>
        </div>
        <?php
          }
          ?>
      </div>

      <a href="./pages/products.php" class="btn bg-warning">All Products</a>

        <div class="alert alert-success alert-dismissible fade show" role="alert" id="cartAlert" style="display: none; position:absolute; bottom:0; right:0;">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
            <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/>
            <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
          </svg>
          <strong>Success!</strong> The product has been added to your cart.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        
    </section>
    
    <!-- ----------------------------------------------------------------------- -->
    <!--                                 Sidebar                                 -->
    <!-- ----------------------------------------------------------------------- -->
    <div class="sidebar-overlay">
      <aside class="sidebar">
        <!-- close -->
        <button class="sidebar-close">
          <i class="fas fa-times"></i>
        </button>
        <!-- links -->
        <ul class="sidebar-links">
          <li>
            <a href="#" class="sidebar-link">
              <i class="fas fa-home fa-fw"></i>
              home
            </a>
          </li>
          <li>
            <a href="./pages/products.php" class="sidebar-link">
              <i class="fas fa-couch fa-fw"></i>
              products
            </a>
          </li>
          <li>
            <a href="./pages/customer_about.php" class="sidebar-link">
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
          <!--                            single cart item                             -->
          <!-- ----------------------------------------------------------------------- -->
          <article class="cart-item">
            <img
              src="https://dl.airtable.com/.attachments/14ac9e946e1a02eb9ce7d632c83f742f/4fd98e64/product-1.jpeg"
              class="cart-item-img"
              alt="product"
            />
            <div class="cart-item-info">
              <h5 class="cart-item-name">high-back bench</h5>
              <span class="cart-item-price">$19.99</span>
              <button class="cart-item-remove-btn">remove</button>
            </div>

            <div>
              <button class="cart-item-increase-btn">
                <i class="fas fa-chevron-up"></i>
              </button>
              <span class="cart-item-amount">1</span>
              <button class="cart-item-decrease-btn">
                <i class="fas fa-chevron-down"></i>
              </button>
            </div>
          </article>
          <!-- ----------------------------------------------------------------------- -->
          <!--                         end of single cart item                         -->
          <!-- ----------------------------------------------------------------------- -->
          
        </div>
        <!-- footer -->
        <footer>
          <h3 class="cart-total text-slanted" id="total">total : $12.99</h3>
          <a href="./pages/checkout.php" class="cart-checkout btn btn-primary btn-lg btn-block w-100">checkout</a>
        </footer>
      </aside>
    </div>

      <!-- Bootstrap JS -->
      <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

      <!-- Global App Scripts -->
      <script defer src="./js/app.js"></script>

      <!-- Cart Functionality -->
      <script defer src="./js/addToCart.js"></script>
      <script defer src="./js/loadDataCart.js"></script>

  </body>
</html>
