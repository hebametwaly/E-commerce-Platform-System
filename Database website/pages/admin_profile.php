<?php
// Start session and check if admin is logged in
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php'); // Redirect to login if not an admin
    exit();
}

// Include database connection
include('../includes/connection.php');

// Fetch orders from the database
$sql = "SELECT o.order_id, u.username AS customer_name, o.status, o.created_at AS date
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC";
$stmt = sqlsrv_query($con, $sql);

$orders = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $row['date'] = $row['date'] instanceof DateTime ? $row['date']->format('Y-m-d H:i:s') : $row['date'];
    $orders[] = $row;
}

// Update order status logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];

    // Update order status in the database
    $update_sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $update_stmt = sqlsrv_query($con, $update_sql, [$newStatus, $orderId]);

    if ($update_stmt) {
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status.']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile || Hazem Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <!-- Admin Information -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="card-title">Admin Profile</h3>
                        <p class="card-text"><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user']); ?></p>
                        <p class="card-text"><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Orders Management -->
            <div class="col-12 col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">Manage Orders</h3>
                        <?php if (!empty($orders)): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $order['status'] === 'Pending' ? 'warning' : ($order['status'] === 'Accepted' ? 'primary' : 'success'); ?>">
                                                    <?php echo htmlspecialchars($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($order['date']); ?></td>
                                            <td>
                                                <?php
                                                $status = strtolower($order['status']); // Convert to lowercase for consistency
                                                switch ($status) {
                                                    case 'pending':
                                                        echo '<button class="btn btn-success btn-sm" onclick="updateOrderStatus(' . $order['order_id'] . ', \'Accepted\')">Accept</button>';
                                                        echo '<button class="btn btn-danger btn-sm" onclick="updateOrderStatus(' . $order['order_id'] . ', \'Declined\')">Decline</button>';
                                                        break;

                                                    case 'accepted':
                                                        echo '<button class="btn btn-primary btn-sm" onclick="updateOrderStatus(' . $order['order_id'] . ', \'Delivered\')">Deliver</button>';
                                                        break;

                                                    case 'delivered':
                                                        echo '<span class="text-muted">Delivered</span>';
                                                        break;

                                                    case 'declined':
                                                        echo '<span class="text-muted">Declined</span>';
                                                        break;

                                                    default:
                                                        echo '<span class="text-muted">No actions available</span>';
                                                        break;
                                                }
                                                ?>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">No orders to manage.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateOrderStatus(orderId, status) {
            fetch('admin_profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&status=${status}`,
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Reload the page to update the table
                } else {
                    alert('Failed to update order status.');
                }
            })
            .catch((error) => console.error('Error:', error));
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
