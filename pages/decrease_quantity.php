
<?php

session_start();



if (isset($_POST['item_key'])) {

    $itemKey = $_POST['item_key'];



    if (isset($_SESSION['cart'][$itemKey])) {

        $_SESSION['cart'][$itemKey]['quantity']--;



        if ($_SESSION['cart'][$itemKey]['quantity'] <= 0) {

            unset($_SESSION['cart'][$itemKey]);

        }

    }

}



header('Location: view_cart.php');

exit;

?>
