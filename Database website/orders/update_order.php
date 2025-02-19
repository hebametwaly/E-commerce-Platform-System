<?php
session_start();
include('../includes/connection.php'); // Database connection

if (!isset($_POST['order_id'])) {
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

$order_id = $_POST['order_id'];
$status = $_POST['status']; // 'delivered'

$sql = "UPDATE orders SET status = ? WHERE order_id = ?";
$stmt = sqlsrv_query($con, $sql, [$status, $order_id]);

if ($stmt) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to update order']);
}
?>
