<?php
require_once 'config.php';
// ... (logika sinkronisasi keranjang di awal, jika ada) ...
$cart = $_SESSION['cart'] ?? [];
if (!empty($cart)) {
    $product_ids_in_session = array_keys($cart);
    $ids_string_for_check = implode(',', array_map('intval', $product_ids_in_session));
    if(!empty($ids_string_for_check)) {
        $sql_check = "SELECT id FROM products WHERE id IN ($ids_string_for_check)";
        $result_check = $conn->query($sql_check);
        $valid_product_ids = [];
        if ($result_check) { while ($row = $result_check->fetch_assoc()) { $valid_product_ids[] = $row['id']; } }
        $invalid_ids = array_diff($product_ids_in_session, $valid_product_ids);
        if (!empty($invalid_ids)) {
            foreach ($invalid_ids as $id) { unset($_SESSION['cart'][$id]); }
            $cart = $_SESSION['cart'];
        }
    }
}
include 'header.php';

$cart_items = [];
$subtotal_produk = 0;
$discounts_summary = [];
$is_pre_order_cart = false; // <-- VARIABEL KUNCI UNTUK MENANDAI KERANJANG PRE-ORDER

// ================== LOGIKA KALKULASI DENGAN PRIORITAS VOUCHER ==================

// Array untuk menyimpan info minuman termahal
$highest_drink_info = [
    'id' => null,
    'final_price' => 0,
    'original_price' => 0, // PENTING: Simpan harga asli untuk voucher
    'manual_discount_amount' => 0
];

if (!empty($cart)) {
    $product_ids = array_keys($cart);
    $ids_string = implode(',', array_map('intval', $product_ids));
    $sql = "SELECT id, name, price, image_url, category, discount_percentage, discount_methods, discount_name FROM products WHERE id IN ($ids_string)";
    $result = $conn->query($sql);
    $products_data = [];
    if ($result) { while ($row = $result->fetch_assoc()) { $products_data[$row['id']] = $row; } }
    // ================== TAMBAHAN BARU: DETEKSI PRE-ORDER ==================
if (!empty($products_data)) {
    foreach ($cart as $id => $quantity) {
        if (isset($products_data[$id]) && $products_data[$id]['category'] === 'pre-order') {
            $is_pre_order_cart = true;
            break; // Jika sudah ketemu satu, hentikan pengecekan
        }
    }
}
// ================== AKHIR TAMBAHAN ==================

    foreach ($cart as $id => $quantity) {
        if (isset($products_data[$id])) {
            $product = $products_data[$id];
            $original_price = $product['price'];
            $final_price = $original_price;
            $manual_discount_amount_per_item = 0;

            if (GLOBAL_DISKON_AKTIF && $product['discount_percentage'] > 0) {
                $manual_discount_amount_per_item = ($original_price * $product['discount_percentage']) / 100;
                $final_price = $original_price - $manual_discount_amount_per_item;
                
                $discount_label = !empty(trim($product['discount_name'])) ? trim($product['discount_name']) : 'Diskon Produk';
                if (!isset($discounts_summary[$discount_label])) {
                    $discounts_summary[$discount_label] = 0;
                }
                $discounts_summary[$discount_label] += $manual_discount_amount_per_item * $quantity;
            }

            // Cari minuman termahal SETELAH diskon manual untuk perbandingan
            if (stripos($product['category'], 'minuman') !== false) {
                if ($final_price > $highest_drink_info['final_price']) {
                    $highest_drink_info['id'] = $id;
                    $highest_drink_info['final_price'] = $final_price;
                    $highest_drink_info['original_price'] = $original_price; // Simpan harga ASLI
                    $highest_drink_info['manual_discount_amount'] = $manual_discount_amount_per_item;
                }
            }

            $subtotal_produk += $original_price * $quantity;
            $cart_items[] = [
                'id' => $id, 'name' => $product['name'], 'original_price' => $original_price,
                'final_price' => $final_price, 'image_url' => $product['image_url'],
                'quantity' => $quantity, 'subtotal' => $final_price * $quantity,
                'discount_percentage' => $product['discount_percentage'],
                'discount_methods' => $product['discount_methods'],
                'discount_name' => $product['discount_name']
            ];
        }
    }
}

// Logika Validasi dan Kalkulasi Voucher
$voucher_code = $_SESSION['applied_voucher'] ?? null;
$voucher_discount = 0;
if ($voucher_code && $highest_drink_info['id'] !== null) {
    $user_id = $_SESSION['user_id'] ?? null;
    if ($user_id) {
        $stmt_check_voucher = $conn->prepare("SELECT id FROM vouchers WHERE user_id = ? AND voucher_code = ? AND is_used = 0 AND expires_at >= CURDATE()");
        $stmt_check_voucher->bind_param("is", $user_id, $voucher_code);
        $stmt_check_voucher->execute();
        $result_check_voucher = $stmt_check_voucher->get_result();
        if ($result_check_voucher && $result_check_voucher->num_rows > 0) {
            
            
            // ================== PERBAIKAN LOGIKA VOUCHER ==================
            // Voucher menggratiskan harga ASLI minuman, bukan harga diskon.
            $voucher_discount = $highest_drink_info['original_price'];
            // ================== AKHIR PERBAIKAN ==================

            // PRIORITAS: Hapus/batalkan diskon manual dari minuman yang kena voucher gratis
            if ($highest_drink_info['manual_discount_amount'] > 0) {
                $product_info = $products_data[$highest_drink_info['id']];
                $discount_label = !empty(trim($product_info['discount_name'])) ? trim($product_info['discount_name']) : 'Diskon Produk';
                
                $discounts_summary[$discount_label] -= $highest_drink_info['manual_discount_amount'];
                if ($discounts_summary[$discount_label] <= 0) {
                    unset($discounts_summary[$discount_label]);
                }
            }
        } else {
            unset($_SESSION['applied_voucher']); 
        }
    }
}

$total_diskon_produk = array_sum($discounts_summary);
$total_setelah_diskon_produk = $subtotal_produk - $total_diskon_produk;
$total_belanja = $total_setelah_diskon_produk - $voucher_discount;
if ($total_belanja < 0) $total_belanja = 0;

?>
<title>Keranjang Belanja - Classic Coffee 789</title>
<style>
    /* Base styles for all devices */
    .cart-page-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .cart-page-container h1 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 2em;
        color: #333;
    }

    .voucher-container {
        text-align: center;
        margin-bottom: 25px;
        margin-top: -10px;
    }

    .voucher-button {
        display: inline-block; 
        background-color: #f39c12; 
        color: white !important;
        padding: 10px 20px; 
        font-size: 14px; 
        font-weight: 600;
        text-decoration: none; 
        border-radius: 8px;
        transition: background-color 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .voucher-button:hover {
        background-color: #e67e22;
        color: white !important;
    }

    .price-display .original-price { 
        text-decoration: line-through; 
        color: #999; 
        font-size: 14px; 
    }
    
    .price-display .final-price { 
        font-weight: bold; 
    }
    
    .discount-info { 
        font-size: 11px; 
        color: #28a745; 
        margin-top: 4px; 
        font-style: italic; 
    }

    /* Desktop table styles */
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .cart-table th,
    .cart-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .cart-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #333;
    }

    .cart-table .text-center {
        text-align: center;
    }

    .cart-table .text-right {
        text-align: right;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .product-info img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }

    .product-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .quantity-in-cart {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .quantity-in-cart a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        background-color: #f8f9fa;
        color: #333;
        text-decoration: none;
        border-radius: 50%;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .quantity-in-cart a:hover {
        background-color: #e9ecef;
    }

    .cart-table tfoot tr {
        font-weight: 600;
    }

    .checkout-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 24px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.3s ease;
        display: inline-block;
        text-align: center;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #545b62;
    }

    /* Mobile Card Layout (hidden by default) */
    .mobile-cart-items {
        display: none;
    }

    .mobile-cart-item {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .mobile-product-header {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .mobile-product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
        flex-shrink: 0;
    }

    .mobile-product-details {
        flex: 1;
    }

    .mobile-product-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .mobile-price-info {
        margin-bottom: 10px;
    }

    .mobile-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .mobile-quantity {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .mobile-quantity a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        background-color: #f8f9fa;
        color: #333;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
        font-size: 18px;
    }

    .mobile-subtotal {
        font-weight: 600;
        color: #333;
        font-size: 16px;
    }

    .mobile-summary {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .mobile-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }

    .mobile-summary-row:last-child {
        border-bottom: none;
        font-weight: 600;
        font-size: 18px;
        margin-top: 10px;
        padding-top: 15px;
        border-top: 2px solid #333;
    }

    .mobile-summary-row.discount {
        color: #e74c3c;
    }

    .mobile-summary-row.voucher {
        color: #28a745;
    }

    /* Media Queries for Responsive Design */
    @media (max-width: 768px) {
        .cart-page-container {
            padding: 15px;
        }

        .cart-page-container h1 {
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .voucher-container {
            margin-bottom: 20px;
            margin-top: 0;
        }

        .voucher-button {
            padding: 12px 20px;
            font-size: 14px;
            width: auto;
            display: inline-block;
        }

        /* Hide desktop table */
        .cart-table {
            display: none;
        }

        /* Show mobile card layout */
        .mobile-cart-items {
            display: block;
        }

        .checkout-container {
            flex-direction: column;
            gap: 10px;
        }

        .checkout-container .btn {
            width: 100%;
            padding: 15px;
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .cart-page-container {
            padding: 10px;
        }

        .voucher-container {
            margin-bottom: 15px;
        }

        .voucher-button {
            width: 100%;
            text-align: center;
            padding: 12px;
            font-size: 15px;
            display: block;
        }

        .mobile-cart-item {
            padding: 15px;
        }

        .mobile-product-header {
            gap: 10px;
        }

        .mobile-product-image {
            width: 70px;
            height: 70px;
        }

        .mobile-product-name {
            font-size: 15px;
        }

        .mobile-quantity a {
            width: 32px;
            height: 32px;
            font-size: 16px;
        }
    }
</style>

<main class="cart-page-container">
    <h1> 🛒  <?php echo $is_pre_order_cart ? 'Konfirmasi Pre-Order' : 'Keranjang Belanja Anda'; ?></h1>
    <?php if ($is_pre_order_cart): ?>
    <div style="text-align:center; padding: 15px; background-color: #fff7ed; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e0d5c6;">
        <p style="margin:0; color:#5a3a22;"><i class="fas fa-info-circle"></i> Anda memesan produk Pre-Order. Harga akan diinformasikan oleh admin via WhatsApp setelah konfirmasi.</p>
    </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['user_id'])): ?>
    <div class="voucher-container">
        <a href="voucher_saya.php" class="voucher-button">
            <i class="fas fa-ticket-alt"></i> Lihat Voucher Saya
        </a>
    </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div style="text-align:center; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <p style="font-size: 18px; color: #666; margin: 0;">Keranjang Anda masih kosong.</p>
            <a href="index.php" class="btn" style="margin-top: 20px;">Mulai Belanja</a>
        </div>
    <?php else: ?>
    
    <table class="cart-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Jumlah</th>
                <?php if (!$is_pre_order_cart): ?>
                    <th class="text-right">Subtotal</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart_items as $item): ?>
            <tr>
                <td>
                    <div class="product-info">
                        <img src="images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div>
                            <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="price-display">
                                <?php if ($is_pre_order_cart): ?>
                                    <span class="final-price" style="color:#e74c3c;">Pre-Order</span>
                                <?php elseif (GLOBAL_DISKON_AKTIF && $item['discount_percentage'] > 0 && !($voucher_discount > 0 && $item['id'] == $highest_drink_info['id'])): ?>
                                    <span class="original-price">Rp <?php echo number_format($item['original_price'], 0, ',', '.'); ?></span>
                                    <span class="final-price">Rp <?php echo number_format($item['final_price'], 0, ',', '.'); ?></span>
                                <?php else: ?>
                                    <span class="final-price">Rp <?php echo number_format($item['original_price'], 0, ',', '.'); ?></span>
                                <?php endif; ?>
                                
                                <?php if (!$is_pre_order_cart && GLOBAL_DISKON_AKTIF && $item['discount_percentage'] > 0): ?>
                                    <?php if (!empty(trim($item['discount_name']))): ?>
                                        <div class="discount-info">Promo: <strong><?php echo htmlspecialchars(trim($item['discount_name'])); ?></strong></div>
                                    <?php elseif (!empty($item['discount_methods'])): ?>
                                        <div class="discount-info">Diskon untuk: <strong><?php echo htmlspecialchars(str_replace(['pickup', 'delivery', 'cod'], ['Ambil Ditempat', 'Ongkir', 'COD'], $item['discount_methods'])); ?></strong></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <div class="quantity-in-cart">
                        <a href="update_keranjang.php?id=<?php echo $item['id']; ?>&action=remove">-</a>
                        <span> <?php echo $item['quantity']; ?> </span>
                        <a href="update_keranjang.php?id=<?php echo $item['id']; ?>&action=add">+</a>
                    </div>
                </td>
                <?php if (!$is_pre_order_cart): ?>
                    <td class="text-right">Rp <?php echo number_format($item['original_price'] * $item['quantity'], 0, ',', '.'); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <?php if (!$is_pre_order_cart): ?>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right">Subtotal Harga Asli</td>
                <td class="text-right">Rp <?php echo number_format($subtotal_produk, 0, ',', '.'); ?></td>
            </tr>
            
            <?php foreach ($discounts_summary as $label => $amount): ?>
            <?php if ($amount > 0): ?>
            <tr style="color: #e74c3c;">
                <td colspan="2" class="text-right"><?php echo htmlspecialchars($label); ?></td>
                <td class="text-right">- Rp <?php echo number_format($amount, 0, ',', '.'); ?></td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($voucher_discount > 0): ?>
            <tr style="color: #28a745;">
                <td colspan="2" class="text-right">Voucher Minuman Gratis (<?php echo htmlspecialchars($voucher_code); ?>)</td>
                <td class="text-right">- Rp <?php echo number_format($voucher_discount, 0, ',', '.'); ?></td>
            </tr>
            <?php endif; ?>
            
            <tr style="font-size: 1.2em; font-weight: bold;">
                <td colspan="2" class="text-right">Total Belanja</td>
                <td class="text-right">Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>

    <div class="mobile-cart-items">
        <?php foreach ($cart_items as $item): ?>
        <div class="mobile-cart-item">
            <div class="mobile-product-header">
                <img src="images/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="mobile-product-image">
                <div class="mobile-product-details">
                    <div class="mobile-product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                    <div class="mobile-price-info">
                         <?php if ($is_pre_order_cart): ?>
                            <div class="final-price" style="color:#e74c3c;">Pre-Order</div>
                        <?php elseif (GLOBAL_DISKON_AKTIF && $item['discount_percentage'] > 0 && !($voucher_discount > 0 && $item['id'] == $highest_drink_info['id'])): ?>
                            <div class="original-price">Rp <?php echo number_format($item['original_price'], 0, ',', '.'); ?></div>
                            <div class="final-price">Rp <?php echo number_format($item['final_price'], 0, ',', '.'); ?></div>
                        <?php else: ?>
                            <div class="final-price">Rp <?php echo number_format($item['original_price'], 0, ',', '.'); ?></div>
                        <?php endif; ?>
                        
                        <?php if (!$is_pre_order_cart && GLOBAL_DISKON_AKTIF && $item['discount_percentage'] > 0): ?>
                            <?php if (!empty(trim($item['discount_name']))): ?>
                                <div class="discount-info">Promo: <strong><?php echo htmlspecialchars(trim($item['discount_name'])); ?></strong></div>
                            <?php elseif (!empty($item['discount_methods'])): ?>
                                <div class="discount-info">Diskon untuk: <strong><?php echo htmlspecialchars(str_replace(['pickup', 'delivery', 'cod'], ['Ambil Ditempat', 'Ongkir', 'COD'], $item['discount_methods'])); ?></strong></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="mobile-controls">
                <div class="mobile-quantity">
                    <a href="update_keranjang.php?id=<?php echo $item['id']; ?>&action=remove">-</a>
                    <span><?php echo $item['quantity']; ?></span>
                    <a href="update_keranjang.php?id=<?php echo $item['id']; ?>&action=add">+</a>
                </div>
                <div class="mobile-subtotal">
                     <?php if ($is_pre_order_cart): ?>
                        <span>Pre-Order</span>
                    <?php else: ?>
                        Rp <?php echo number_format($item['original_price'] * $item['quantity'], 0, ',', '.'); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($is_pre_order_cart): ?>
        <div class="mobile-summary">
            <div class="mobile-summary-row" style="border-top: 2px solid #333; font-size: 18px; font-weight: 600;">
                <span>Total Belanja</span>
                <span>Akan Dikonfirmasi</span>
            </div>
        </div>
    <?php else: ?>
        <div class="mobile-summary">
            <div class="mobile-summary-row">
                <span>Subtotal Harga Asli</span>
                <span>Rp <?php echo number_format($subtotal_produk, 0, ',', '.'); ?></span>
            </div>
            
            <?php foreach ($discounts_summary as $label => $amount): ?>
            <?php if ($amount > 0): ?>
            <div class="mobile-summary-row discount">
                <span><?php echo htmlspecialchars($label); ?></span>
                <span>- Rp <?php echo number_format($amount, 0, ',', '.'); ?></span>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            
            <?php if ($voucher_discount > 0): ?>
            <div class="mobile-summary-row voucher">
                <span>Voucher Minuman Gratis (<?php echo htmlspecialchars($voucher_code); ?>)</span>
                <span>- Rp <?php echo number_format($voucher_discount, 0, ',', '.'); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="mobile-summary-row">
                <span>Total Belanja</span>
                <span>Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="checkout-container">
    <a href="index.php" class="btn btn-secondary">Lanjutkan Belanja</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="form-pemesanan.php" class="btn">Lanjutkan ke Pembayaran</a>
    <?php else: ?>
        <div>
            <p style="text-align:right; margin:0 0 5px 0; font-size:14px; color:#5a3a22;">Silakan login untuk menyelesaikan pesanan Anda.</p>
            <a href="login.php?redirect=keranjang" class="btn">Login untuk Melanjutkan</a>
        </div>
    <?php endif; ?>
</div>
    
<?php endif; ?>
</main>
<?php include 'footer.php'; ?>