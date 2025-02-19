<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php'); // Redirect to login if not logged in
    exit();
}

// Include database connection
include('../includes/connection.php');

// Fetch user information from the session
$userName = $_SESSION['user'];
$userId = $_SESSION['user_id']; // Assume user_id is stored in the session during login
$userPrivilege = $_SESSION['role']; // Example: 'Admin', 'User', etc.

// Fetch last orders from the database
$sql = "SELECT order_id, status, created_at AS date 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC";
$stmt = sqlsrv_query($con, $sql, [$userId]);

$orders = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile || Hazem Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <!-- User Information -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="card-title">Profile</h3>
                        <p class="card-text"><strong>Name:</strong> <?php echo htmlspecialchars($userName); ?></p>
                        <p class="card-text"><strong>Privilege:</strong> <?php echo htmlspecialchars($userPrivilege); ?></p>
                    </div>
                </div>
            </div>

            <!-- Last Orders -->
            <div class="col-12 col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">Last Orders</h3>
                        <?php if (!empty($orders)): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $order['status'] === 'Pending' ? 'warning' : ($order['status'] === 'Accepted' ? 'primary' : ($order['status'] === 'Delivered' ? 'success' : 'danger')); ?>">
                                                    <?php echo htmlspecialchars($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($order['date']->format('Y-m-d')); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">No orders found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
