<?php
session_start();

function processCheckout($postData) {
    $requiredFields = ['name', 'address', 'city', 'zip', 'country', 'paymentMethod'];
    foreach ($requiredFields as $field) {
        if (empty($postData[$field])) {
            return ['success' => false, 'message' => 'Please fill in all required fields.'];
        }
    }

    $paymentMethod = $postData['paymentMethod'];
    $paymentSuccess = false;

    if ($paymentMethod == 'creditCard') {
        $paymentSuccess = true; // Assume payment is successful
    } elseif ($paymentMethod == 'paypal') {
        $paymentSuccess = true; // Assume payment is successful
    } elseif ($paymentMethod == 'gcash') {
        $paymentSuccess = true; // Assume payment is successful
    } else {
        return ['success' => false, 'message' => 'Invalid payment method.'];
    }

    if ($paymentSuccess) {
        unset($_SESSION['cart']);
        return ['success' => true, 'message' => 'Checkout successful.'];
    } else {
        return ['success' => false, 'message' => 'Payment failed. Please try again.'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = processCheckout($_POST);

    if ($result['success']) {
        header('Location: success.php');
    } else {
        echo '<div class="alert alert-danger">' . htmlspecialchars($result['message']) . '</div>';
    }
}
?>