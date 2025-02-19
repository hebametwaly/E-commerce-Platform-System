<?php
session_start(); // Start the session
include("../includes/connection.php"); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL query to check for the user
    $sql = "SELECT * FROM users WHERE username = ?";
    $params = [$username];

    // Execute the query
    $stmt = sqlsrv_query($con, $sql, $params);

    // Check if the query executed successfully
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));  // Output the error if the query fails
    }

    // Check if a matching user is found
    if ($user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Verify the password using password_verify (bcrypt or argon2)
        if (password_verify($password, $user['password'])) {
            // Password is correct, store username and role in the session
            $_SESSION['user_id'] = $user['id'];  // Store the user ID
            $_SESSION['user'] = $user['username'];  // Store the username
            $_SESSION['role'] = $user['role'];  // Store the role (admin or customer)

            // Redirect to the index page after login
            header('Location: ../index.php');
            exit; // Stop further script execution
        } else {
            echo "Invalid login credentials.";
        }
    } else {
        echo "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card p-4 shadow-sm" style="width: 400px;">
            <h3 class="text-center">Login</h3>

            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" value="Login">Login</button>
                <p class="text-center mt-3">
                    Don't have an account? <a href="register.php">Register</a>
                </p>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
