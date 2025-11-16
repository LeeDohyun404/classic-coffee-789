<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

// Validasi request method harus POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$conn->begin_transaction();

try {
    // 1. Ambil semua data dari form
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $customer_email = $_POST['customer_email'] ?? '';
    $order_date = $_POST['order_date'];
    $delivery_method = $_POST['delivery_method'];
    $payment_choice = $_POST['payment_choice'];
    $address = ($delivery_method === 'pickup') ? 'Ambil Ditempat' : ($_POST['guest_address'] ?? '');
    $order_notes = $_POST['order_notes'] ?? '';
    
    $product_ids = $_POST['product_ids'];
    $quantities = $_POST['quantities'];

    if (empty($product_ids)) {
        throw new Exception("Tidak ada produk yang dipilih.");
    }
    
    // 2. Kalkulasi ulang total harga di sisi SERVER untuk keamanan
    $total_price = 0;
    $ids_string = implode(',', array_map('intval', $product_ids));
    $sql_products = "SELECT id, price, discount_percentage, discount_methods FROM products WHERE id IN ($ids_string)";
    $result_products = $conn->query($sql_products);
    $products_from_db = [];
    while ($row = $result_products->fetch_assoc()) {
        $products_from_db[$row['id']] = $row;
    }
    
    foreach ($product_ids as $index => $pid) {
        if(!isset($products_from_db[$pid])) continue;

        $product = $products_from_db[$pid];
        $quantity = (int)$quantities[$index];
        $price_per_item = (float)$product['price'];

        // Terapkan diskon jika kondisi terpenuhi (sama seperti di frontend)
        if (GLOBAL_DISKON_AKTIF && $product['discount_percentage'] > 0 && !empty($product['discount_methods'])) {
            $allowed_methods = explode(',', $product['discount_methods']);
            if (in_array($delivery_method, $allowed_methods)) {
                $price_per_item -= ($price_per_item * (int)$product['discount_percentage'] / 100);
            }
        }
        $total_price += $price_per_item * $quantity;
    }

    // 3. Simpan data pesanan ke tabel `orders`
    $payment_method_db = ($delivery_method === 'cod') ? 'COD' : 'WhatsApp';
    $status = 'paid'; // Asumsikan pesanan manual oleh admin selalu lunas

    $sql_order = "INSERT INTO orders (guest_name, guest_address, guest_phone, guest_email, total_price, payment_method, payment_choice, status, order_notes, order_date, pickup_datetime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    // Menggunakan order_date juga untuk pickup_datetime agar konsisten
    $stmt_order->bind_param("ssssdssssss", $customer_name, $address, $customer_phone, $customer_email, $total_price, $payment_method_db, $payment_choice, $status, $order_notes, $order_date, $order_date);
    $stmt_order->execute();
    $order_id = $conn->insert_id;

    if ($order_id == 0) {
        throw new Exception("Gagal menyimpan data pesanan utama.");
    }

    // 4. Simpan item-item pesanan ke tabel `order_items`
    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($sql_items);
    foreach ($product_ids as $index => $pid) {
        $product = $products_from_db[$pid];
        $quantity = (int)$quantities[$index];
        $original_price = (float)$product['price']; // Selalu simpan harga asli per item
        $stmt_items->bind_param("iiid", $order_id, $pid, $quantity, $original_price);
        $stmt_items->execute();
    }
    
    // 5. Commit transaksi jika semua berhasil
    $conn->commit();

    // 6. Arahkan kembali ke dashboard dengan notifikasi sukses
    header("Location: index.php?status=manual_order_added");
    exit();

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan di database
    $conn->rollback();
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>