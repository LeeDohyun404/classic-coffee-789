<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header('Location: keranjang.php');
    exit();
}

$nomor_whatsapp_admin = "6289669505208";
$conn->begin_transaction();

try {
    // 1. Ambil data dari Form dan Cart
    $delivery_method = $_POST['delivery_method'] ?? 'pickup';
    $payment_choice = $_POST['payment_choice'] ?? 'cash';
    $cart = $_SESSION['cart'];
    $product_ids = array_keys($cart);
    $ids_string = implode(',', array_map('intval', $product_ids));
    $user_id = $_SESSION['user_id'] ?? null;

    // Variabel untuk kalkulasi
    $subtotal_produk = 0;
    $total_diskon_produk = 0;
    $highest_drink_info = [
        'id' => null,
        'final_price' => 0,
        'original_price' => 0,
        'manual_discount_amount' => 0
    ];
    $products_from_db = [];

    // 2. Kalkulasi Ulang Harga dari Awal berdasarkan Metode Pengiriman
    if (!empty($ids_string)) {
        $sql_products = "SELECT id, name, price, category, discount_percentage, discount_methods, discount_name FROM products WHERE id IN ($ids_string)";
        $result_products = $conn->query($sql_products);
        while ($row = $result_products->fetch_assoc()) {
            $products_from_db[$row['id']] = $row;
        }

        foreach ($cart as $product_id => $quantity) {
            $product = $products_from_db[$product_id];
            $original_price = $product['price'];
            $final_price_per_item = $original_price;
            $manual_discount_amount_per_item = 0;

            $is_discount_applicable = false;
                if (GLOBAL_DISKON_AKTIF && $product['discount_percentage'] > 0 && !empty(trim($product['discount_methods']))) {
                $allowed_methods = explode(',', $product['discount_methods']);
                if (in_array($delivery_method, $allowed_methods)) {
                    $is_discount_applicable = true;
                }
            }

            if ($is_discount_applicable) {
                $manual_discount_amount_per_item = ($original_price * $product['discount_percentage']) / 100;
                $final_price_per_item = $original_price - $manual_discount_amount_per_item;
                $total_diskon_produk += $manual_discount_amount_per_item * $quantity;
            }

            if (stripos($product['category'], 'minuman') !== false) {
                if ($final_price_per_item > $highest_drink_info['final_price']) {
                    $highest_drink_info['id'] = $product_id;
                    $highest_drink_info['final_price'] = $final_price_per_item;
                    $highest_drink_info['original_price'] = $original_price;
                    $highest_drink_info['manual_discount_amount'] = $manual_discount_amount_per_item;
                }
            }
            $subtotal_produk += $original_price * $quantity;
        }
    }

    // 3. Logika Voucher dengan Prioritas
    $voucher_discount = 0;
    $voucher_used = false;
    $voucher_id = null;
    if (isset($_SESSION['applied_voucher']) && !empty($_SESSION['applied_voucher']) && $user_id && $highest_drink_info['id'] !== null) {
        $voucher_code = $_SESSION['applied_voucher'];
        $stmt_voucher = $conn->prepare("SELECT id FROM vouchers WHERE user_id = ? AND voucher_code = ? AND is_used = 0 AND expires_at >= CURDATE()");
        $stmt_voucher->bind_param("is", $user_id, $voucher_code);
        $stmt_voucher->execute();
        $voucher_result = $stmt_voucher->get_result();
        if ($voucher_result->num_rows > 0) {
            $voucher_data = $voucher_result->fetch_assoc();
            $voucher_id = $voucher_data['id'];
            $voucher_discount = $highest_drink_info['original_price'];
            $voucher_used = true;

            if ($highest_drink_info['manual_discount_amount'] > 0) {
                $total_diskon_produk -= $highest_drink_info['manual_discount_amount'];
            }
        }
    }

    $total_setelah_diskon_produk = $subtotal_produk - $total_diskon_produk;
    $final_total = $total_setelah_diskon_produk - $voucher_discount;
    if ($final_total < 0) $final_total = 0;

    // 4. Ambil Sisa Data dari Form
    $shipping_fee = 0;
    $final_total += $shipping_fee;
    $is_logged_in = isset($_SESSION['user_id']);
    $customer_name = $_POST['customer_name'] ?? ($is_logged_in ? $_SESSION['username'] : 'Pelanggan');
    $customer_phone = $_POST['customer_phone'] ?? '';
    $address = $_POST['guest_address'] ?? '';
    // ================== LOGIKA BARU UNTUK MENGGABUNGKAN TANGGAL DAN JAM ==================
$pickup_date_str = $_POST['pickup_date'] ?? '';
$pickup_time_str = $_POST['pickup_time'] ?? '';
$pickup_datetime_formatted = null;

if (!empty($pickup_date_str) && !empty($pickup_time_str)) {
    // Gabungkan tanggal dan jam, lalu format ulang agar lebih rapi di pesan WhatsApp
    $date_obj = DateTime::createFromFormat('d-m-Y', $pickup_date_str);
    if ($date_obj) {
        $hari_indonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $nama_hari = $hari_indonesia[$date_obj->format('w')];
        $pickup_datetime_formatted = $nama_hari . ", " . $date_obj->format('d M Y') . " - Jam " . $pickup_time_str;
    } else {
         // Fallback jika format tanggal tidak sesuai, gabungkan apa adanya
        $pickup_datetime_formatted = $pickup_date_str . ' ' . $pickup_time_str;
    }
}
// ================== AKHIR LOGIKA BARU ==================
    
    if($delivery_method === 'pickup') { $address = 'Ambil Ditempat'; }
    $email = $_POST['guest_email'] ?? '';
    $order_notes = $_POST['order_notes'] ?? '';
    
    $payment_method_wa = "WhatsApp"; // Ini untuk status di DB lama, bisa disesuaikan
    if ($delivery_method === 'cod') { $payment_method_wa = "COD"; }
    elseif ($voucher_used && $final_total == 0) { $payment_method_wa = "Voucher"; }
    
    $status = ($final_total > 0) ? "pending" : "paid";
    if ($voucher_used && $final_total <= $shipping_fee) {
        $status = ($shipping_fee > 0) ? "pending" : "free";
    }

    // 5. Simpan Pesanan ke Database
    $sql_order = "INSERT INTO orders (user_id, guest_name, guest_address, guest_phone, guest_email, total_price, payment_method, status, voucher_discount, shipping_fee, order_notes, pickup_datetime, payment_choice) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("issssdssddsss", $user_id, $customer_name, $address, $customer_phone, $email, $final_total, $payment_method_wa, $status, $voucher_discount, $shipping_fee, $order_notes, $pickup_datetime_formatted, $payment_choice);
    $stmt_order->execute();
    $order_id = $conn->insert_id;
    if ($order_id == 0) { throw new Exception("Gagal menyimpan pesanan."); }

    // Simpan Item Pesanan
    $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)";
    $stmt_items = $conn->prepare($sql_items);
    foreach ($cart as $product_id => $quantity) {
        $price_per_item = $products_from_db[$product_id]['price'];
        $stmt_items->bind_param("iiid", $order_id, $product_id, $quantity, $price_per_item);
        $stmt_items->execute();
    }

    // Update status voucher jika digunakan
    if ($voucher_used && $voucher_id) {
        $stmt_use_voucher = $conn->prepare("UPDATE vouchers SET is_used = 1, used_at = NOW() WHERE id = ?");
        $stmt_use_voucher->bind_param("i", $voucher_id);
        $stmt_use_voucher->execute();
    }

    // 6. Format Pesan WhatsApp
    $pesan_wa_items = "";
    foreach ($cart as $product_id => $quantity) {
        $product = $products_from_db[$product_id];
        $price_per_item = $product['price'];
        
        $is_item_free_by_voucher = ($voucher_used && $product_id == $highest_drink_info['id']);
        
        if ($is_item_free_by_voucher) {
            $price_per_item = 0;
        } elseif (GLOBAL_DISKON_AKTIF && $product['discount_percentage'] > 0) {
            $is_discount_applicable_wa = false;
            if (!empty(trim($product['discount_methods']))) {
                $allowed_methods_wa = explode(',', $product['discount_methods']);
                if (in_array($delivery_method, $allowed_methods_wa)) {
                    $is_discount_applicable_wa = true;
                }
            }
            if ($is_discount_applicable_wa) {
                $price_per_item -= ($price_per_item * $product['discount_percentage'] / 100);
            }
        }
        
        $item_total_price = $price_per_item * $quantity;
        $pesan_wa_items .= "-> " . htmlspecialchars($product['name']) . "\n";
        $pesan_wa_items .= "   Qty: " . $quantity . " x Rp " . number_format($price_per_item, 0, ',', '.') . " = Rp " . number_format($item_total_price, 0, ',', '.') . "\n";
    }

    $pesan_whatsapp = "Halo Classic Coffee 789, ada pesanan baru:\n\n";
    $pesan_whatsapp .= $pesan_wa_items;
    $pesan_whatsapp .= "\nSubtotal Asli: Rp " . number_format($subtotal_produk, 0, ',', '.');
    if($total_diskon_produk > 0) {
        $pesan_whatsapp .= "\nTotal Diskon Produk: -Rp " . number_format($total_diskon_produk, 0, ',', '.');
    }
    if ($voucher_used) {
        $pesan_whatsapp .= "\nVoucher Gratis: -Rp " . number_format($voucher_discount, 0, ',', '.');
    }
    $pesan_whatsapp .= "\n*Total Akhir: Rp " . number_format($final_total, 0, ',', '.') . "*\n\n";
    
    $pesan_whatsapp .= "Berikut data pelanggan:\n";
    $pesan_whatsapp .= "Nama: " . htmlspecialchars($customer_name) . "\nNo. HP: " . htmlspecialchars($customer_phone) . "\n\n";
    if (!empty(trim($order_notes))) { $pesan_whatsapp .= "*Catatan Pesanan:*\n" . htmlspecialchars($order_notes) . "\n\n"; }
    
    // Info Pengambilan & Pembayaran
    $pesan_whatsapp .= "*Jadwal:*\n" . htmlspecialchars($pickup_datetime_formatted) . "\n";
    if ($delivery_method === 'pickup') { $pesan_whatsapp .= "Metode: *Ambil Ditempat*\n"; }
    elseif ($delivery_method === 'delivery') { $pesan_whatsapp .= "Metode: *Ongkir*\nAlamat Pengiriman:\n" . htmlspecialchars($address) ."\n"; }
    elseif ($delivery_method === 'cod') { $pesan_whatsapp .= "Metode: *COD*\nLokasi COD:\n" . htmlspecialchars($address) . "\n"; }
    
    if ($payment_choice === 'qris') {
        $pesan_whatsapp .= "Pembayaran: *QRIS*\n_(Mohon kirim bukti transfer)_";
    } else {
        $pesan_whatsapp .= "Pembayaran: *CASH (Bayar di Tempat)*";
    }

    $pesan_whatsapp .= "\n\nMohon konfirmasinya. Terima kasih.\n(Ref Order ID: " . $order_id . ")";
    
    $encoded_message = urlencode($pesan_whatsapp);
    $whatsapp_url = "https://api.whatsapp.com/send?phone=" . $nomor_whatsapp_admin . "&text=" . $encoded_message;
    
    $stmt_update_url = $conn->prepare("UPDATE orders SET whatsapp_url = ? WHERE id = ?");
    $stmt_update_url->bind_param("si", $whatsapp_url, $order_id);
    $stmt_update_url->execute();
    
    // 7. Selesaikan Transaksi
    $conn->commit();
    unset($_SESSION['cart']);
    unset($_SESSION['applied_voucher']);
    header("Location: informasi-pemesanan.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Terjadi kesalahan fatal: " . $e->getMessage());
}
?>