<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header('Location: keranjang.php'); exit();
}

$cart = $_SESSION['cart'];
$product_ids = array_keys($cart);
$ids_string = implode(',', array_map('intval', $product_ids));
$total_price = 0; $drinks_in_cart = [];
$sql_products = "SELECT id, name, price, category FROM products WHERE id IN ($ids_string)";
$result_products = $conn->query($sql_products);
$products_from_db = [];
while ($row = $result_products->fetch_assoc()) { $products_from_db[$row['id']] = $row; }

foreach ($cart as $product_id => $quantity) {
    if (!isset($products_from_db[$product_id])) { die("Error: Produk tidak valid."); }
    $total_price += $products_from_db[$product_id]['price'] * $quantity;
    if ($products_from_db[$product_id]['category'] == 'minuman') {
        for ($i=0; $i < $quantity; $i++) { $drinks_in_cart[] = $products_from_db[$product_id]['price']; }
    }
}

$voucher_code = $_SESSION['applied_voucher'] ?? null;
if ($voucher_code && !empty($drinks_in_cart)) {
    $highest_price_drink = max($drinks_in_cart);
    $total_price -= $highest_price_drink;
}

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$guest_name = !$is_logged_in ? ($_POST['guest_name'] ?? null) : null;
$address = $_POST['guest_address'] ?? null; $phone = $_POST['guest_phone'] ?? null; $email = $_POST['guest_email'] ?? null;

$conn->begin_transaction();
try {
    $sql_order = "INSERT INTO orders (user_id, guest_name, guest_address, guest_phone, guest_email, total_price) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("issssd", $user_id, $guest_name, $address, $phone, $email, $total_price);
    $stmt_order->execute();
    $order_id = $conn->insert_id;
    if ($order_id == 0) { throw new Exception("Gagal membuat pesanan."); }

    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($sql_items);
    $drinks_bought_count = 0;
    foreach ($cart as $product_id => $quantity) {
        $price_per_item = $products_from_db[$product_id]['price'];
        $stmt_items->bind_param("iiid", $order_id, $product_id, $quantity, $price_per_item);
        $stmt_items->execute();
        if ($products_from_db[$product_id]['category'] == 'minuman') { $drinks_bought_count += $quantity; }
    }

    if ($voucher_code) {
        $stmt_use_voucher = $conn->prepare("UPDATE vouchers SET status = 'terpakai' WHERE voucher_code = ? AND user_id = ?");
        $stmt_use_voucher->bind_param("si", $voucher_code, $user_id);
        $stmt_use_voucher->execute();
    }
    
    if ($is_logged_in && $drinks_bought_count > 0) {
        // Ambil total minuman yang sudah dibeli SEBELUM pembelian ini (tanpa mengurangi pembelian sekarang)
        $sql_total_drinks = "SELECT SUM(oi.quantity) as total_drinks FROM order_items oi JOIN orders o ON oi.order_id = o.id JOIN products p ON oi.product_id = p.id WHERE o.user_id = ? AND p.category = 'minuman' AND o.id != ?";
        $stmt_total = $conn->prepare($sql_total_drinks);
        $stmt_total->bind_param("ii", $user_id, $order_id);
        $stmt_total->execute();
        $total_drinks_before = $stmt_total->get_result()->fetch_assoc()['total_drinks'] ?? 0;

        // Hitung berapa voucher yang harus diberikan
        $drinks_before_purchase = $total_drinks_before;
        $drinks_after_purchase = $total_drinks_before + $drinks_bought_count;
        
        // Hitung berapa kelipatan 10 yang tercapai dari pembelian ini
        $vouchers_before = floor($drinks_before_purchase / 10);
        $vouchers_after = floor($drinks_after_purchase / 10);
        $new_vouchers_earned = $vouchers_after - $vouchers_before;

        // Buat voucher baru sebanyak yang layak didapat
        for ($i = 0; $i < $new_vouchers_earned; $i++) {
            $new_voucher_code = 'GRATIS-' . strtoupper(substr(uniqid(), 7, 6));
            $expires_at = date('Y-m-d', strtotime('+30 days'));
            $stmt_new_voucher = $conn->prepare("INSERT INTO vouchers (user_id, voucher_code, expires_at) VALUES (?, ?, ?)");
            $stmt_new_voucher->bind_param("iss", $user_id, $new_voucher_code, $expires_at);
            $stmt_new_voucher->execute();
        }
        
        // Set pesan voucher yang didapat
        if ($new_vouchers_earned > 0) {
            if ($new_vouchers_earned == 1) {
                $_SESSION['new_voucher_message'] = "Selamat! Anda mendapatkan 1 voucher minuman gratis!";
            } else {
                $_SESSION['new_voucher_message'] = "Selamat! Anda mendapatkan " . $new_vouchers_earned . " voucher minuman gratis!";
            }
        }
    }

    $conn->commit();
    unset($_SESSION['cart']);
    unset($_SESSION['applied_voucher']);
    header("Location: informasi-pemesanan.php?order_id=" . $order_id);
    exit();
} catch (Exception $e) {
    $conn->rollback();
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>