<?php
require_once 'config.php';

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}
$order_id = $_GET['order_id'];

// Mengambil data pesanan dari database, termasuk link whatsapp dan diskon
$sql_order = "SELECT o.*, COALESCE(u.username, o.guest_name) AS customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();

if (!$order) { die("Pesanan tidak ditemukan."); }

// Mengambil item-item pesanan
$sql_items = "SELECT oi.quantity, p.name AS product_name, oi.price_per_item FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();

// Hitung subtotal asli sebelum diskon
$subtotal_price = $order['total_price'] + $order['voucher_discount'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Informasi Pemesanan - Classic Coffee 789</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header style="text-align:center; padding: 20px; background-color: #8B4513; color: white;">
        <h1>Classic Coffee 789</h1>
    </header>

    <main style="padding: 40px; text-align: center;">
        <div style="border: 1px solid #ddd; padding: 30px; max-width: 600px; margin: auto; background: #fafafa; border-radius: 10px;">
            
            <h2>🛒 Pesanan Telah Diterima!</h2>
            <p>Terima kasih, <?php echo htmlspecialchars($order['customer_name']); ?>. Berikut adalah rincian pesanan Anda.</p>

            <?php if (!empty($order['whatsapp_url'])): ?>
                <a href="<?php echo htmlspecialchars($order['whatsapp_url']); ?>" target="_blank" class="btn" style="display:inline-block; margin-top: 20px; margin-bottom: 20px; width: auto; padding: 15px 30px; background: #25D366; text-decoration:none; color:white; border-radius:8px; font-size: 1.2em;">
                    <i class="fab fa-whatsapp"></i> Lanjutkan ke WhatsApp
                </a>
            <?php endif; ?>
            
            <hr style="margin: 30px 0;">
            <h3 style="color: #5a3a22;">Rincian Pesanan Anda</h3>
            
            <table style="width: 100%; text-align: left; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #f1f1f1;">
                        <th style="padding: 10px;">Produk</th>
                        <th style="padding: 10px; text-align:center;">Jumlah</th>
                        <th style="padding: 10px; text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $items->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td style="padding: 10px; text-align:center;"><?php echo $item['quantity']; ?></td>
                        <td style="padding: 10px; text-align:right;">Rp <?php echo number_format($item['price_per_item'] * $item['quantity'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="padding: 10px; text-align:right;">Subtotal Harga</td>
                        <td style="padding: 10px; text-align:right;">Rp <?php echo number_format($subtotal_price, 0, ',', '.'); ?></td>
                    </tr>
                    <?php if ($order['voucher_discount'] > 0): ?>
                    <tr style="color: green;">
                        <td colspan="2" style="padding: 10px; text-align:right;">Diskon Voucher</td>
                        <td style="padding: 10px; text-align:right;">- Rp <?php echo number_format($order['voucher_discount'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr style="background: #e9ecef; font-size: 1.1em; font-weight: bold;">
                        <td colspan="2" style="padding: 12px; text-align:right;">Total Akhir</td>
                        <td style="padding: 12px; text-align:right;">Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
            <a href="index.php" class="btn" style="display:inline-block; margin-top: 30px; width: auto; padding: 10px 30px; background: #5a3a22; text-decoration:none; color:white; border-radius:8px;">Kembali ke Beranda</a>
        </div>
    </main>
</body>
</html>