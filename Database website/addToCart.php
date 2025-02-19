<?php
session_start();
include('./includes/connection.php'); // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$product_title = $_POST['product_title'];
$product_price = $_POST['product_price'];
$product_image = $_POST['product_image'];

// Check the available stock for the product
$sql = "SELECT stock_quantity FROM products WHERE id = ?";
$stmt = sqlsrv_query($con, $sql, [$product_id]);

if ($stmt === false) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}

$product = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
$stock_quantity = $product['stock_quantity'];

// Check if the product already exists in the cart
$sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = sqlsrv_query($con, $sql, [$user_id, $product_id]);

if ($stmt === false) {
    echo json_encode(['error' => 'Error checking cart']);
    exit;
}

$cart_item = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($cart_item) {
    // If the product is already in the cart, update the quantity
    $new_quantity = $cart_item['quantity'] + 1;

    // Ensure that the quantity doesn't exceed available stock
    if ($new_quantity > $stock_quantity) {
        echo json_encode(['error' => 'Not enough stock available']);
        exit;
    }

    // Update the quantity in the cart
    $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
    $params = [$new_quantity, $user_id, $product_id];
    sqlsrv_query($con, $sql, $params);
} else {
    // If the product is not in the cart, add it
    if ($stock_quantity <= 0) {
        echo json_encode(['error' => 'Out of stock']);
        exit;
    }

    $sql = "INSERT INTO cart (user_id, product_id, product_title, product_price, product_image, quantity) VALUES (?, ?, ?, ?, ?, ?)";
    $params = [$user_id, $product_id, $product_title, $product_price, $product_image, 1];
    sqlsrv_query($con, $sql, $params);
}

// Get the updated cart count and cart items
$sql = "SELECT SUM(quantity) AS cart_count FROM cart WHERE user_id = ?";
$stmt = sqlsrv_query($con, $sql, [$user_id]);
$cart_count = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)['cart_count'];

$sql = "SELECT * FROM cart WHERE user_id = ?";
$stmt = sqlsrv_query($con, $sql, [$user_id]);
$cart_items = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $cart_items[] = $row;
}

// Return updated cart data as JSON
echo json_encode([
    'cartCount' => $cart_count,
    'cartItems' => $cart_items
]);
?>
