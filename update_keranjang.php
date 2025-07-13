<?php
session_start();

if (isset($_GET['id']) && isset($_SESSION['cart'][$_GET['id']])) {
    $product_id = $_GET['id'];
    $action = $_GET['action'] ?? 'add';

    if ($action == 'add') {
        $_SESSION['cart'][$product_id]++;
    } elseif ($action == 'remove') {
        $_SESSION['cart'][$product_id]--;
        if ($_SESSION['cart'][$product_id] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

header('Location: keranjang.php');
exit();
?>