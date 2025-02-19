<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to login page if not logged in
    header('Location: pages/login.php');
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

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="../assets/logo-white.svg" type="image/x-icon">
    <title>About || Hazem Store</title>
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
    <nav class="navbar my-2">
          <div class="nav-center2 d-flex justify-content-between align-items-center w-100 px-5">
            <!-- links -->
            <div>
              <button class="toggle-nav">
                <i class="fas fa-bars"></i>
              </button>
              <ul class="nav-links text-dark">
                <li>
                  <a href="/" class="nav-link"> home </a>
                </li>
                <li>
                  <a href="/pages/products.php" class="nav-link"> products </a>
                </li>
                <li>
                  <a href="/pages/about.php" class="nav-link"> about </a>
                </li>
              </ul>
            </div>
            <!-- logo -->
            <div>
              <img src="../assets/logo-black.svg" class="nav-logo" alt="logo" />
            </div>
            <!-- cart icon -->

            <div class="toggle-container">
              <button class="toggle-cart text-dark">
                <i class="fas fa-shopping-cart"></i>
              </button>
              <span class="cart-item-count">0</span>
            </div>


            <!-- login -->
            <div class="p-2 shadow-sm ">
                  <h3 class="text-center text-dark" style="color: white; font-size: 20px;">
                      <?php if ($isLoggedIn): ?>
                          Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?> </br>!
                          You : <?php echo htmlspecialchars($_SESSION['role']); ?>
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
                                  <li><a class="dropdown-item" href="/pages/customer_profile.php">Profile</a></li>
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
        <h3 class="page-hero-title">Home / About</h3>
      </div>
    </section>
    <!-- about -->
    <section class="section section-center">
      <div class="title">
        <span></span>
        <h2>our history</h2>
        <span></span>
      </div>
      <p class="about-text">
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Fugiat
        accusantium sapiente tempora sed dolore esse deserunt eaque excepturi,
        delectus error accusamus vel eligendi, omnis beatae. Quisquam, dicta.
        Eos quod quisquam esse recusandae vitae neque dolore, obcaecati incidunt
        sequi blanditiis est exercitationem molestiae delectus saepe odio
        eligendi modi porro eaque in libero minus unde sapiente consectetur
        architecto. Ullam rerum, nemo iste ex, eaque perspiciatis nisi, eum
        totam velit saepe sed quos similique amet. Ex, voluptate accusamus
        nesciunt totam vitae esse iste.
      </p>
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
            <a href="/customer_products.php" class="sidebar-link">
              <i class="fas fa-couch fa-fw"></i>
              products
            </a>
          </li>
          <li>
            <a href="/" class="sidebar-link">
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
          <h3 class="text-slanted">your bag</h3>
        </header>
        <!-- cart items -->
        <div class="cart-items">
          
        </div>
        <!-- footer -->
        <footer>
          <h3 class="cart-total text-slanted">total : $0</h3>
          <button class="cart-checkout btn">checkout</button>
        </footer>
      </aside>
    </div>

    <!-- app.js and scripts-->
    <script src="../js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="../js/addToCart.js"></script>
  </body>
</html>
