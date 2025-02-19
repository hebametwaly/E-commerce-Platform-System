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

// Remove the item from the cart
$sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = sqlsrv_query($con, $sql, [$user_id, $product_id]);

if ($stmt === false) {
    echo json_encode(['error' => 'Error removing item from cart']);
    exit;
}

// Get the updated cart count
$sql = "SELECT SUM(quantity) AS cart_count FROM cart WHERE user_id = ?";
$stmt = sqlsrv_query($con, $sql, [$user_id]);
$cart_count = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)['cart_count'];

// Get the updated cart total
$sql = "SELECT SUM(product_price * quantity) AS cart_total FROM cart WHERE user_id = ?";
$stmt = sqlsrv_query($con, $sql, [$user_id]);
$cart_total = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)['cart_total'];

// Get the updated cart items
$sql = "SELECT * FROM cart WHERE user_id = ?";
$stmt = sqlsrv_query($con, $sql, [$user_id]);
$cart_items = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $cart_items[] = $row;
}

// If cart_total is null, set it to 0
if ($cart_total === null) {
    $cart_total = 0;
}

echo json_encode([
    'cartCount' => $cart_count,
    'cartTotal' => $cart_total, // Ensure this is never null
    'cartItems' => $cart_items
]);

?>
