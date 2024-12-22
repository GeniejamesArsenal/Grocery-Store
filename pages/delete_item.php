<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if the 'item_key' is set in the POST request
if (isset($_POST['item_key'])) {
    $itemKey = $_POST['item_key'];

    // Check if the item exists in the cart session
    if (isset($_SESSION['cart'][$itemKey])) {
        // Remove the item from the cart
        unset($_SESSION['cart'][$itemKey]);

        // Reindex the cart array to fill any gaps in the array indexes
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Redirect back to the cart page
header('Location: view_cart.php');
exit;
