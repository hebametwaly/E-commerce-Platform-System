<?php
session_start();
include('../includes/connection.php'); // Database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Start a transaction
sqlsrv_begin_transaction($con);

// Insert into orders table
$order_sql = "INSERT INTO orders (user_id, status) OUTPUT INSERTED.order_id VALUES (?, 'pending')";
$order_stmt = sqlsrv_query($con, $order_sql, [$user_id]);
$order_data = sqlsrv_fetch_array($order_stmt, SQLSRV_FETCH_ASSOC);
$order_id = $order_data['order_id'] ?? null;

if (!$order_id) {
    sqlsrv_rollback($con);
    die("Failed to create order. Please try again.");
}

// Retrieve items from the cart
$cart_sql = "SELECT product_id, quantity FROM cart WHERE user_id = ?";
$cart_stmt = sqlsrv_query($con, $cart_sql, [$user_id]);

while ($item = sqlsrv_fetch_array($cart_stmt, SQLSRV_FETCH_ASSOC)) {
    $insert_item_sql = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_item_stmt = sqlsrv_query($con, $insert_item_sql, [$order_id, $item['product_id'], $item['quantity']]);
    if (!$insert_item_stmt) {
        sqlsrv_rollback($con);
        die("Failed to add items to order. Please try again.");
    }
}

// Clear the cart
$delete_cart_sql = "DELETE FROM cart WHERE user_id = ?";
$delete_cart_stmt = sqlsrv_query($con, $delete_cart_sql, [$user_id]);

if (!$delete_cart_stmt) {
    sqlsrv_rollback($con);
    die("Failed to clear cart. Please try again.");
}

// Commit the transaction
sqlsrv_commit($con);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center">
            <h1>Thank You for Your Order!</h1>
            <p>Your order has been placed successfully.</p>
            <p>Order ID: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
            <a href="../pages/customer_profile.php" class="btn btn-primary">View Your Profile</a>
        </div>
    </div>
</body>
</html>
