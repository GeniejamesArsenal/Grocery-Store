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

    
$totalPrice = 0;
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light p-3 mb-4 shadow-sm">
    <a class="navbar-brand" href="index.php">Grocery System</a>
    
    <!-- Mobile Toggle Button for Navbar -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Content -->
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="view_cart.php">
                    <i class="fas fa-shopping-cart"></i> Cart
                </a>
            </li>
        </ul>
    </div>

    <!-- Welcome Message for Logged-in Users -->
    <p class="mb-0 d-none d-lg-inline-block ms-4 me-3">
        Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>!
    </p>

    <!-- Search Form (aligned to the right on larger screens) -->
    <form method="GET" action="index.php" class="d-flex ms-auto mb-0">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search items" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>
</nav>

    <div class="container">
        <h2 class="my-4">Your Cart</h2>
        <a href="index.php" class="btn btn-secondary mb-3">Back to Shop</a>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $key => $cartItem) {
                    $itemTotal = $cartItem['price'] * $cartItem['quantity'];
                    $totalPrice += $itemTotal;
                    ?>
                    <tr>
                        <td><?php echo $cartItem['name']; ?></td>
                        <td>$<?php echo number_format($cartItem['price'], 2); ?></td>
                        <td><?php echo $cartItem['quantity']; ?></td>
                        <td>$<?php echo number_format($itemTotal, 2); ?></td>
                        <td>
                            <form action="delete_item.php" method="post" style="display:inline;">
                                <input type="hidden" name="item_key" value="<?php echo $key; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            <form action="decrease_quantity.php" method="post" style="display:inline;">
                                <input type="hidden" name="item_key" value="<?php echo $key; ?>">
                                <button type="submit" class="btn btn-warning btn-sm">-</button>
                            </form>
                            <form action="increase_quantity.php" method="post" style="display:inline;">
                                <input type="hidden" name="item_key" value="<?php echo $key; ?>">
                                <button type="submit" class="btn btn-success btn-sm">+</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h4>Total: $<?php echo number_format($totalPrice, 2); ?></h4>

        <!-- Checkout Button -->
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    </div>
</body>
</html>
