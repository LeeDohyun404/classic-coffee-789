<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header('Location: keranjang.php');
    exit();
}

// Nomor WhatsApp Admin
$nomor_whatsapp_admin = "6289518530077"; 

$conn->begin_transaction();

try {
    $cart = $_SESSION['cart'];
    $product_ids = array_keys($cart);
    $ids_string = implode(',', array_map('intval', $product_ids));
    $total_price = 0;
    $voucher_discount = 0;
    
    $sql_products = "SELECT id, name, price, category FROM products WHERE id IN ($ids_string)";
    $result_products = $conn->query($sql_products);
    $products_from_db = [];
    while ($row = $result_products->fetch_assoc()) { $products_from_db[$row['id']] = $row; }

    foreach ($cart as $product_id => $quantity) {
        $product = $products_from_db[$product_id];
        $total_price += $product['price'] * $quantity;
    }

    $voucher_used = false;
    $voucher_id = null;
    $highest_drink_price = 0;

    if (isset($_SESSION['applied_voucher']) && !empty($_SESSION['applied_voucher'])) {
        $voucher_code = $_SESSION['applied_voucher'];
        $user_id_check = $_SESSION['user_id'] ?? null;
        if ($user_id_check) {
            $stmt_voucher = $conn->prepare("SELECT id FROM vouchers WHERE user_id = ? AND voucher_code = ? AND is_used = 0 AND expires_at >= CURDATE()");
            $stmt_voucher->bind_param("is", $user_id_check, $voucher_code);
            $stmt_voucher->execute();
            $voucher_result = $stmt_voucher->get_result();
            if ($voucher_result->num_rows > 0) {
                $voucher_data = $voucher_result->fetch_assoc();
                $voucher_id = $voucher_data['id'];
                foreach ($cart as $product_id => $quantity) {
                    $product = $products_from_db[$product_id];
                    if (stripos($product['category'], 'minuman') !== false) {
                        if ($product['price'] > $highest_drink_price) {
                            $highest_drink_price = $product['price'];
                        }
                    }
                }
                $voucher_discount = $highest_drink_price;
                $voucher_used = true;
            }
        }
    }

    $final_total = $total_price - $voucher_discount;
    if ($final_total < 0) $final_total = 0;

    $is_logged_in = isset($_SESSION['user_id']);
    $user_id = $is_logged_in ? $_SESSION['user_id'] : null;
    $guest_name = !$is_logged_in ? ($_POST['guest_name'] ?? 'Pelanggan') : $_SESSION['username'];
    $address = $_POST['guest_address'] ?? '';
    $phone = $_POST['guest_phone'] ?? '';
    $email = $_POST['guest_email'] ?? '';
    $payment_method = ($final_total > 0) ? "WhatsApp" : "Voucher"; 
    
    // Logika status: pending, paid, atau free
    if ($voucher_used && $final_total == 0) {
        $status = "free";
    } elseif ($final_total > 0) {
        $status = "pending";
    } else {
        $status = "paid";
    }

    // Simpan Pesanan ke Database
    $sql_order = "INSERT INTO orders (user_id, guest_name, guest_address, guest_phone, guest_email, total_price, payment_method, status, voucher_discount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("issssdssd", $user_id, $guest_name, $address, $phone, $email, $final_total, $payment_method, $status, $voucher_discount);
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

    // Tandai voucher sebagai terpakai
    if ($voucher_used && $voucher_id) {
        $stmt_use_voucher = $conn->prepare("UPDATE vouchers SET is_used = 1, used_at = NOW() WHERE id = ?");
        $stmt_use_voucher->bind_param("i", $voucher_id);
        $stmt_use_voucher->execute();
    }
    
    // KEMBALIKAN FORMAT PESAN WHATSAPP YANG BENAR
    $pesan_whatsapp = "Halo Classic Coffee 789, saya ingin memesan:\n\n";
    if ($voucher_used) {
        foreach ($cart as $product_id => $quantity) {
            $product = $products_from_db[$product_id];
            $pesan_whatsapp .= "-> " . htmlspecialchars($product['name']) . " (Qty: " . $quantity . ")\n";
        }
        $pesan_whatsapp .= "\nSubtotal: Rp " . number_format($total_price, 0, ',', '.');
        $pesan_whatsapp .= "\nDiskon Voucher: -Rp " . number_format($voucher_discount, 0, ',', '.');
        $pesan_whatsapp .= "\n*Total Akhir: Rp " . number_format($final_total, 0, ',', '.') . "*\n\n";
    } else {
        foreach ($cart as $product_id => $quantity) {
            $product = $products_from_db[$product_id];
            $subtotal = $product['price'] * $quantity;
            $pesan_whatsapp .= "-> " . htmlspecialchars($product['name']) . "\n";
            $pesan_whatsapp .= "   Qty: " . $quantity . " x Rp " . number_format($product['price'], 0, ',', '.') . " = Rp " . number_format($subtotal, 0, ',', '.') . "\n";
        }
        $pesan_whatsapp .= "\n*Total Pesanan: Rp " . number_format($total_price, 0, ',', '.') . "*\n\n";
    }
    
    $pesan_whatsapp .= "Berikut data saya:\n";
    $pesan_whatsapp .= "Nama: " . htmlspecialchars($guest_name) . "\n";
    $pesan_whatsapp .= "No. HP: " . htmlspecialchars($phone) . "\n";
    $pesan_whatsapp .= "Alamat Pengiriman:\n" . htmlspecialchars($address) . "\n\n";
    
    if ($final_total > 0) {
        $pesan_whatsapp .= "Mohon informasikan total akhir beserta ongkir dan instruksi pembayaran. Terima kasih.\n";
    } else {
        $pesan_whatsapp .= "Pesanan ini sudah lunas dengan voucher. Mohon untuk segera diproses. Terima kasih.\n";
    }
    $pesan_whatsapp .= "\n(Ref Order ID: " . $order_id . ")";
    
    $encoded_message = urlencode($pesan_whatsapp);
    $whatsapp_url = "https://api.whatsapp.com/send?phone=" . $nomor_whatsapp_admin . "&text=" . $encoded_message;
    
    $stmt_update_url = $conn->prepare("UPDATE orders SET whatsapp_url = ? WHERE id = ?");
    $stmt_update_url->bind_param("si", $whatsapp_url, $order_id);
    $stmt_update_url->execute();

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