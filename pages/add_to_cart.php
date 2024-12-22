<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../includes/db.php';

// Check if item ID is provided
if (isset($_GET['id'])) {
    $itemId = $_GET['id'];
    
    // Fetch item details from the database
    $query = "SELECT * FROM items WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    
    if ($item) {
        // Add the item to the cart in the session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $itemInCart = false;
        // Check if the item is already in the cart
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['id'] == $itemId) {
                $cartItem['quantity']++;
                $itemInCart = true;
                break;
            }
        }

        if (!$itemInCart) {
            // If the item is not in the cart, add it
            $_SESSION['cart'][] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => 1
            ];
        }

        // Redirect to the customer dashboard
        header('Location: index.php');
        exit;
    } else {
        echo "Item not found.";
    }
} else {
    echo "Invalid request.";
}
?>
