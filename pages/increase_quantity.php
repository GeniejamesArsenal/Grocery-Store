<?php

session_start();

if (isset($_POST['item_key'])) {
    $itemKey = $_POST['item_key'];

    if (isset($_SESSION['cart'][$itemKey])) {
        $_SESSION['cart'][$itemKey]['quantity']++;
    }
}

header('Location: view_cart.php');
exit;

?>
