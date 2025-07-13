<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

// Pastikan item_id dan order_id ada di URL
if (!isset($_GET['item_id']) || !isset($_GET['order_id'])) {
    die("Aksi tidak valid.");
}

$item_id = $_GET['item_id'];
$order_id = $_GET['order_id'];

// Memulai transaction untuk menjaga konsistensi data
$conn->begin_transaction();

try {
    // 1. Ambil data item yang akan dihapus untuk menghitung ulang total harga
    $stmt_get_item = $conn->prepare("SELECT quantity, price_per_item FROM order_items WHERE id = ?");
    $stmt_get_item->bind_param("i", $item_id);
    $stmt_get_item->execute();
    $item = $stmt_get_item->get_result()->fetch_assoc();

    if ($item) {
        $subtracted_price = $item['quantity'] * $item['price_per_item'];

        // 2. Hapus item dari tabel order_items
        $stmt_delete = $conn->prepare("DELETE FROM order_items WHERE id = ?");
        $stmt_delete->bind_param("i", $item_id);
        $stmt_delete->execute();

        // 3. Update total_price di tabel orders
        $stmt_update_order = $conn->prepare("UPDATE orders SET total_price = total_price - ? WHERE id = ?");
        $stmt_update_order->bind_param("di", $subtracted_price, $order_id);
        $stmt_update_order->execute();

        // Jika semua berhasil, konfirmasi transaksi
        $conn->commit();
    } else {
        // Jika item tidak ditemukan, batalkan
        $conn->rollback();
    }

} catch (mysqli_sql_exception $exception) {
    // Jika ada error SQL, batalkan semua
    $conn->rollback();
    die("Gagal menghapus item: " . $exception->getMessage());
}

// Kembalikan admin ke halaman edit pesanan
header("Location: edit_pesanan.php?id=" . $order_id);
exit();
?>