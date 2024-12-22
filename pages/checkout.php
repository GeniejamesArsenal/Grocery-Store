<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        header('Location: index.php');
        exit;
    }

    // Calculate total price
    $totalPrice = 0;
    foreach ($_SESSION['cart'] as $cartItem) {
        $totalPrice += $cartItem['price'] * $cartItem['quantity'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .order-summary-table th, .order-summary-table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <!-- Navbar (optional) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Grocery System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>

    <!-- Checkout Page -->
    <div class="container my-5">
        <h2 class="my-4">Checkout</h2>

        <!-- Order Summary Section -->
        <h4>Order Summary</h4>
        <table class="table order-summary-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $cartItem) { 
                    $itemTotal = $cartItem['price'] * $cartItem['quantity'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cartItem['name']); ?></td>
                        <td>$<?php echo number_format($cartItem['price'], 2); ?></td>
                        <td><?php echo $cartItem['quantity']; ?></td>
                        <td>$<?php echo number_format($itemTotal, 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Total Price -->
        <div class="d-flex justify-content-between mt-4">
            <h5>Total: $<?php echo number_format($totalPrice, 2); ?></h5>
        </div>

        <hr class="my-4">

        <!-- Shipping Information Form -->
        <h4>Shipping Information</h4>
    
        <form method="POST" action="process_checkout.php">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="mb-3">
                <label for="zip" class="form-label">Zip Code</label>
                <input type="text" class="form-control" id="zip" name="zip" required>
            </div>
            <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <input type="text" class="form-control" id="country" name="country" required>
            </div>

            <hr class="my-4">

            <!-- Payment Method Section -->
            <h4>Payment Method</h4>
            <div class="form-group">
                <label for="paymentMethod">Choose Payment Method:</label>
                <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                    <option value="">Select Payment Method</option>
                    <option value="creditCard">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="paypal">Gcash</option>
                </select>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-success">Proceed to Payment</button>
                <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
