<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="text-center">
            <h1 class="my-4">Thank You for Your Purchase!</h1>
            <p>Your order has been successfully processed.</p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>
</body>
</html>