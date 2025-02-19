<?php
session_start();
header('Content-Type: application/json'); // Ensure the response is JSON

include './includes/connection.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

// Get cart data from the database
$sql = "SELECT product_id, product_title, product_price, product_image, quantity FROM cart WHERE user_id = ?";
$stmt = sqlsrv_query($con, $sql, [$userId]);

if ($stmt === false) {
    echo json_encode(['error' => 'Database error: ' . print_r(sqlsrv_errors(), true)]);
    exit;
}

$cartItems = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $cartItems[] = [
        'product_id' => $row['product_id'],
        'product_title' => $row['product_title'],
        'product_price' => $row['product_price'],
        'product_image' => $row['product_image'],
        'quantity' => $row['quantity']
    ];
}

// Calculate the total count of items in the cart
$sqlCount = "SELECT SUM(quantity) AS cartCount FROM cart WHERE user_id = ?";
$stmtCount = sqlsrv_query($con, $sqlCount, [$userId]);
$cartCount = sqlsrv_fetch_array($stmtCount, SQLSRV_FETCH_ASSOC)['cartCount'];


// Return the cart data and count as JSON
echo json_encode([
    'cartCount' => $cartCount,
    'cartItems' => $cartItems
]);
?>
