<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

// Pastikan ini adalah request POST dan data yang dibutuhkan ada
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['item_id']) || !isset($_POST['order_id'])) {
    die("Aksi tidak valid.");
}

$item_id = $_POST['item_id'];
$order_id = $_POST['order_id'];
$new_quantity = (int)$_POST['quantity'];
$new_product_id = (int)$_POST['product_id'];

if ($new_quantity < 1) {
    header("Location: edit_pesanan.php?id=" . $order_id);
    exit();
}

$conn->begin_transaction();

try {
    // 1. Ambil data item LAMA (untuk menghitung selisih)
    $stmt_get_item = $conn->prepare("SELECT quantity, price_per_item FROM order_items WHERE id = ?");
    $stmt_get_item->bind_param("i", $item_id);
    $stmt_get_item->execute();
    $item = $stmt_get_item->get_result()->fetch_assoc();
    
    // 2. Ambil data produk BARU (untuk mendapatkan harga baru)
    $stmt_get_product = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt_get_product->bind_param("i", $new_product_id);
    $stmt_get_product->execute();
    $product = $stmt_get_product->get_result()->fetch_assoc();

    if ($item && $product) {
        $old_subtotal = $item['quantity'] * $item['price_per_item'];
        $new_subtotal = $new_quantity * $product['price'];
        $price_difference = $new_subtotal - $old_subtotal;

        // 3. Update item di tabel order_items dengan produk dan jumlah baru
        $stmt_update_item = $conn->prepare("UPDATE order_items SET product_id = ?, quantity = ?, price_per_item = ? WHERE id = ?");
        $stmt_update_item->bind_param("iidi", $new_product_id, $new_quantity, $product['price'], $item_id);
        $stmt_update_item->execute();

        // 4. Update total_price di tabel orders
        $stmt_update_order = $conn->prepare("UPDATE orders SET total_price = total_price + ? WHERE id = ?");
        $stmt_update_order->bind_param("di", $price_difference, $order_id);
        $stmt_update_order->execute();

        $conn->commit();
    } else {
        $conn->rollback();
    }

} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    die("Gagal mengupdate item: " . $exception->getMessage());
}

// Kembalikan admin ke halaman edit pesanan
header("Location: edit_pesanan.php?id=" . $order_id);
exit();
?>