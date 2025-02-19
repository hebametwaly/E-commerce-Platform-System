

<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to login page if not logged in
    header('Location: ./auth/login.php');
    exit;
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user']) && isset($_SESSION['role']);

// Retrieve the user's name and role from the session
$username = $_SESSION['user'];
$role = $_SESSION['role'];
?>




<?php

include("../includes/connection.php");

$sql = 'SELECT * FROM Products';
// Execute the query using sqlsrv_query
$allproduct = sqlsrv_query($con, $sql);

// Check for query execution errors
if ($allproduct === false) {
    die(print_r(sqlsrv_errors(), true));
}

?>


<?php
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Base query
$sql = 'SELECT * FROM Products WHERE 1=1';

// Fetch categories from the Categories table
$categoryQuery = 'SELECT * FROM Categories';
$categories = sqlsrv_query($con, $categoryQuery);

// Check for errors
if ($categories === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Add search filter
if (!empty($search)) {
  $sql .= " AND (title LIKE '%' + ? + '%' OR description LIKE '%' + ? + '%')"; 
}

// Add category filter
if (!empty($category) && $category !== 'all') {
  $sql .= " AND Categories = ?";
}

// Prepare and execute query
$params = [];
if (!empty($search)) {
  $params[] = $search;
  $params[] = $search;
}
if (!empty($category) && $category !== 'all') {
  $params[] = $category;
}
$allproduct = sqlsrv_query($con, $sql, $params);

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
    <link rel="shortcut icon" href="../assets/logo-white.svg" type="image/x-icon">
    <title>Products || Hazem Store</title>
    <!-- font awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
    />
    <!-- main css -->
    <link rel="stylesheet" href="../css/style.css" />
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
        <div style="cursor:pointer;">
          <img 
            onclick="window.location.href = '/';"
            src="../assets/logo-black.svg" 
            class="nav-logo2" 
            alt="logo" 
          />
        </div>
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
          <h3 class="text-center text-dark" style="font-size: medium;">
            <?php if ($isLoggedIn && $role == 'customer'): ?>
              Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?> <br/>
              You: <?php echo htmlspecialchars($_SESSION['role']); ?>
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
              Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?> <br/>
              You: <?php echo htmlspecialchars($_SESSION['role']); ?>
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
              <a href="../auth/login.php" class="btn btn-primary w-100">Login</a>
            <?php endif; ?>
          </h3>
        </div>
      </div>    
    </nav>
    <!-- end of navbar -->
    <!-- page hero -->
    <section class="page-hero">
      <div class="section-center">
        <h3 class="page-hero-title">Home / Products</h3>
      </div>
    </section>
    <!-- products -->
    <section class="products position-relative">

    <!-- filters -->
    <div class="filters">
      <div class="filters-container">
        <!-- search -->
        <form class="input-form" method="get" action="products.php">
          <input 
            type="text" 
            name="search" 
            class="search-input" 
            placeholder="Search products..." 
            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
          />
          <button type="submit" class="btn btn-primary mt-2">Search</button>
        </form>
        <!-- categories -->
        <h5>Category</h5>
        <article class="companies">
            <button class="company-btn" data-category="all">All</button>
            <?php while ($category = sqlsrv_fetch_array($categories, SQLSRV_FETCH_ASSOC)): ?>
                <button class="company-btn" data-category="<?php echo htmlspecialchars($category['name']); ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </button>
            <?php endwhile; ?>
        </article>

      </div>
    </div>

      <!-- products -->
      <div class="products-container" id="cont-products">
        <?php while ($row = sqlsrv_fetch_array($allproduct, SQLSRV_FETCH_ASSOC)) { ?>
        <div class="card p-2 product-container">
          <img
            src="<?php echo htmlspecialchars($row['Image']); ?>"
            class="product-img img"
            alt="product"
          />
          <div class="product-icons">
            <a class="product-icon" onclick="showProductDetails(<?php echo htmlspecialchars($row['id']); ?>)">
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
            <span class="product-price"><?php echo htmlspecialchars($row['price']); ?>$</span> <br/>
            <span class="product-quantity">quantity: <?php echo htmlspecialchars($row['stock_quantity']); ?></span>
          </footer>
        </div>
        <?php } ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="cartAlert" style="display: none; position:absolute; bottom:0; right:0;">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
            <path d="M2.5 8a5.5..."/>
          </svg>
          <strong>Success!</strong> The product has been added to your cart.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
    </section>
    <!-- sidebar -->
    <!-- cart overlay -->

    <!-- Bootstrap and JS scripts -->
          <!-- Bootstrap JS -->
          <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

          <!-- Global App Scripts -->
          <script defer src="../js/app.js"></script>

          <!-- Cart Functionality -->
          <script defer src="../js/addToCart.js"></script>
          <script defer src="../js/loadDataCart.js"></script>
  </body>
</html>
