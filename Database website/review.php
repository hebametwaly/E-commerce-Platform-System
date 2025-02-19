<?php
session_start();
include('./includes/connection.php'); // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ./auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$review_text = $_POST['review_text'];
$rating = intval($_POST['rating']);

// Insert review into the database
$review_query = "INSERT INTO reviews (product_id, user_id, review_text, rating) VALUES (?, ?, ?, ?)";
$review_stmt = sqlsrv_query($con, $review_query, [$product_id, $user_id, $review_text, $rating]);

if ($review_stmt) {
    // Show thank you message and redirect to home page
    echo "<script>
        alert('Thank you for your review!');
        window.location.href = './index.php'; // Redirect to the home page
    </script>";
    exit;
} else {
    echo "Failed to add review.";
}
?>
