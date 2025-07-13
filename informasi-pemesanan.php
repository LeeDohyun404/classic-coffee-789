<?php
require_once 'config.php';

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}
$order_id = $_GET['order_id'];

// 1. Ambil data pesanan utama dari tabel 'orders'
$sql_order = "SELECT 
            o.id, 
            o.order_date,
            o.total_price,
            o.guest_address,
            o.guest_phone,
            o.guest_email,
            COALESCE(u.username, o.guest_name) AS customer_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

// 2. Ambil semua item dari pesanan tersebut dari tabel 'order_items'
$sql_items = "SELECT oi.quantity, oi.price_per_item, p.name AS product_name 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Informasi Pemesanan - Classic Coffee 789</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo">Classic Coffee 789</div>
    </header>

    <main style="padding: 40px; text-align: center;">
        <div style="border: 1px solid #ddd; padding: 30px; max-width: 600px; margin: auto; background: #fafafa; border-radius: 10px;">
            <h2>✅ Pesanan Berhasil!</h2>
            <p>Terima kasih, <?php echo htmlspecialchars($order['customer_name']); ?>. Berikut adalah detail pesanan Anda:</p>
            
            <?php
            // Tampilkan notifikasi loyalitas jika ada
            if (isset($_SESSION['loyalty_message'])) {
                echo '<div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;">';
                echo '<strong>' . $_SESSION['loyalty_message'] . '</strong>';
                echo '</div>';
                unset($_SESSION['loyalty_message']); // Hapus pesan agar tidak muncul lagi
            }
            ?>
            
            <table style="width: 100%; text-align: left; border-collapse: collapse; margin-top: 20px;">
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; font-weight: bold;">No. Pesanan</td>
                    <td style="padding: 10px;">#<?php echo $order['id']; ?></td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; font-weight: bold;">Alamat</td>
                    <td style="padding: 10px;"><?php echo nl2br(htmlspecialchars($order['guest_address'])); ?></td>
                </tr>
                 <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; font-weight: bold;">No. HP</td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($order['guest_phone']); ?></td>
                </tr>
                 <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; font-weight: bold;">Email</td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($order['guest_email']); ?></td>
                </tr>
            </table>

            <h3 style="margin-top: 30px; text-align:left; color: #5a3a22;">Rincian Produk:</h3>
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
                    <tr style="background: #e9ecef; font-size: 1.1em; font-weight: bold;">
                        <td colspan="2" style="padding: 12px; text-align:right;">Total Harga</td>
                        <td style="padding: 12px; text-align:right;">Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>

            <a href="index.php" class="btn" style="margin-top: 30px; width: auto; padding: 10px 30px; background: #5a3a22;">Kembali ke Beranda</a>
        </div>
    </main>
</body>
</html>