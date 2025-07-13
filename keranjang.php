<?php 
require_once 'config.php';
include 'header.php'; 

$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$total_price = 0;
$discount = 0;
$highest_drink_price = 0; // <-- PERBAIKAN: Variabel diinisialisasi di sini

if (!empty($cart)) {
    $product_ids = array_keys($cart);
    if (!empty($product_ids)) {
        $ids_string = implode(',', array_map('intval', $product_ids));
        $sql = "SELECT id, name, price, image_url, category FROM products WHERE id IN ($ids_string)";
        $result = $conn->query($sql);
        $products_data = [];
        if ($result) { while ($row = $result->fetch_assoc()) { $products_data[$row['id']] = $row; } }
        
        foreach ($cart as $id => $quantity) {
            if (isset($products_data[$id])) {
                $product = $products_data[$id];
                $total_price += $product['price'] * $quantity;
                // Cari harga minuman termahal untuk diskon
                if ($product['category'] == 'minuman' && $product['price'] > $highest_drink_price) {
                    $highest_drink_price = $product['price'];
                }
                $cart_items[] = [ 
                    'id' => $id, 'name' => $product['name'], 'price' => $product['price'], 
                    'image_url' => $product['image_url'], 'quantity' => $quantity, 'subtotal' => $product['price'] * $quantity 
                ];
            }
        }
    }
}

// Cek dan terapkan voucher jika ada
$voucher_code = $_SESSION['applied_voucher'] ?? null;
if ($voucher_code && $highest_drink_price > 0) {
    $discount = $highest_drink_price;
    $total_price -= $discount;
}
?>
<title>Keranjang Belanja - Classic Coffee 789</title>
<style>
    .cart-page-container { max-width: 900px; margin: 40px auto; padding: 20px; }
    .cart-page-container h1 { text-align: center; color: #5a3a22; margin-bottom: 30px; font-family: 'Playfair Display', serif; }
    .cart-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; box-shadow: 0 5px 25px rgba(0,0,0,0.08); overflow: hidden; }
    .cart-table th { background-color: #f8f9fa; padding: 18px 20px; text-align: left; text-transform: uppercase; font-size: 14px; color: #666; }
    .cart-table td { padding: 20px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .product-info { display: flex; align-items: center; gap: 20px; }
    .product-info img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; }
    .product-info .product-name { font-weight: 600; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .cart-table tfoot td { font-weight: bold; font-size: 1.2em; }
    .checkout-container { display: flex; justify-content: space-between; align-items: center; margin-top: 30px; }
    .btn { padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; font-size: 15px; font-weight: 500; }
    .btn-secondary { background-color: #6c757d; color: white; }
    .btn:not(.btn-secondary) { background-color: #8B4513; color: white; }
    .quantity-in-cart { display: flex; align-items: center; justify-content: center; gap: 15px; }
    .quantity-in-cart a { text-decoration: none; font-weight: bold; color: #5a3a22; padding: 5px 12px; border: 1px solid #ddd; border-radius: 5px; }
</style>

<main class="cart-page-container">
    <h1>🛒 Keranjang Belanja Anda</h1>
    
    <?php if(isset($_SESSION['user_id'])): ?>
        <div style="text-align:center; margin-bottom:20px;">
            <a href="voucher_saya.php" class="btn" style="width:auto; background-color:#f39c12;">Lihat Voucher Saya</a>
        </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <p style="text-align:center; padding: 40px; background: #fff; border-radius: 8px;">Keranjang Anda masih kosong.</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr><th>Produk</th><th class="text-center">Jumlah</th><th class="text-right">Subtotal</th></tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div><div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div></div>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="quantity-in-cart">
                                <a href="update_keranjang.php?id=<?php echo $item['id']; ?>&action=remove">-</a>
                                <span><?php echo $item['quantity']; ?></span>
                                <a href="update_keranjang.php?id=<?php echo $item['id']; ?>&action=add">+</a>
                            </div>
                        </td>
                        <td class="text-right">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <?php if ($discount > 0): ?>
                <tr>
                    <td colspan="2" class="text-right">Subtotal</td>
                    <td class="text-right">Rp <?php echo number_format($total_price + $discount, 0, ',', '.'); ?></td>
                </tr>
                <tr style="color: #28a745;">
                    <td colspan="2" class="text-right">Diskon Voucher (<?php echo htmlspecialchars($voucher_code); ?>)</td>
                    <td class="text-right">- Rp <?php echo number_format($discount, 0, ',', '.'); ?></td>
                </tr>
                <?php endif; ?>
                <tr style="font-size: 1.2em; font-weight: bold;">
                    <td colspan="2" class="text-right">Total Belanja</td>
                    <td class="text-right">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
        <div class="checkout-container">
            <a href="index.php" class="btn btn-secondary">Lanjutkan Belanja</a>
            <a href="form-pemesanan.php" class="btn">Lanjutkan ke Pembayaran</a>
        </div>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
</body>
</html>