<?php
// Include your database connection file
include('../includes/connection.php');

// Check if the form is submitted and the required fields are set
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) && isset($_POST['price']) && isset($_POST['image']) && isset($_POST['quantity'])) {
    
    // Get the product title, price, and image URL from the form
    $title = $_POST['title'];
    $price = $_POST['price'];
    $image = $_POST['image']; // Get the image URL from the form
    $quantity = $_POST['quantity']; // Get the image URL from the form
    
    // Prepare the SQL query to insert data into the Products table
    $sql = "INSERT INTO Products (Title, Price, Image, Stock_quantity) VALUES (?, ?, ?, ?)";
    
    // Prepare the SQL statement
    $stmt = sqlsrv_prepare($con, $sql, array(&$title, &$price, &$image, $quantity));

    // Execute the query
    if (sqlsrv_execute($stmt)) {
        echo "Product uploaded successfully!";
    } else {
        // Display any SQL errors
        echo "Error uploading product: ";
        print_r(sqlsrv_errors());
    }
} else {
    echo "Form not submitted correctly.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uplaod Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- /* -------------------------------------------------------------------------- */
  /*                              admin upload data                             */
  /* -------------------------------------------------------------------------- */ -->

  <form action="upload.php" method="POST" enctype="multipart/form-data" class="container p-5">
            <div class="mb-3">
                <label for="title" class="form-label">Product Title:</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price:</label>
                <input type="text" name="price" id="price" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Product Image:</label>
                <input type="text" name="image" id="image" class="form-control" accept="image/*" required>
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">stock_quantity:</label>
                <input type="text" name="quantity" id="quantity" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Upload Product</button>
      </form>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

