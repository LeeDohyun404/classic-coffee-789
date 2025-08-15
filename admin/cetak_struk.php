<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

if (!isset($_GET['order_id'])) {
    die("ID Pesanan tidak ditemukan.");
}
$order_id = $_GET['order_id'];

// PERBAIKAN 1: Ambil semua data pesanan yang relevan
$sql_order = "SELECT o.*, COALESCE(u.username, o.guest_name) AS customer_name 
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

// Ambil semua item dari pesanan tersebut
$sql_items = "SELECT oi.quantity, p.name AS product_name, p.price as price_per_item 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
$items = [];
$subtotal_produk = 0;
while($item = $items_result->fetch_assoc()){
    $items[] = $item;
    // Hitung subtotal dari harga asli produk, bukan dari order_items
    $subtotal_produk += $item['price_per_item'] * $item['quantity'];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pesanan #<?php echo $order['id']; ?></title>
    <style>
        body { font-family: 'Courier New', monospace; width: 300px; margin: 0 auto; padding: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; }
        .header p { margin: 0; font-size: 10px; }
        .details p { margin: 3px 0; }
        .item-list { margin-top: 15px; }
        .item { margin-bottom: 5px; }
        .summary { margin-top: 15px; border-top: 1px dashed #000; padding-top: 10px; }
        .summary .row { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .summary .total { font-weight: bold; }
        .footer { text-align: center; margin-top: 20px; }
        hr { border: none; border-top: 1px dashed #000; margin: 10px 0; }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="header">
            <h2>Classic Coffee 789</h2>
            <p>Desa Kebanaran, Tamanwinangun RT 03/ RW 08 No.59</p>
        </div>
        <hr>
        <div class="details">
            <p>No. Pesanan: #<?php echo $order['id']; ?></p>
            <p>Tanggal: <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
            <p>Pelanggan: <?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p>No. HP: <?php echo htmlspecialchars($order['guest_phone']); ?></p>
            <?php if (!empty($order['guest_email'])): ?>
            <p>Email: <?php echo htmlspecialchars($order['guest_email']); ?></p>
            <?php endif; ?>
            <p>Status: <?php echo htmlspecialchars($order['status']); ?></p>
        </div>
        <hr>
        
        <div class="item-list">
            <?php foreach($items as $item): ?>
                <div class="item">
                    <div><?php echo htmlspecialchars($item['product_name']); ?></div>
                    <div><?php echo $item['quantity']; ?> x Rp <?php echo number_format($item['price_per_item'], 0, ',', '.'); ?> = Rp <?php echo number_format($item['price_per_item'] * $item['quantity'], 0, ',', '.'); ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="summary">
            <div class="row">
                <span>Subtotal Belanja</span>
                <span>Rp <?php echo number_format($subtotal_produk, 0, ',', '.'); ?></span>
            </div>
            
            <?php 
                $total_diskon = 0;
                $diskon_produk = $subtotal_produk - ($order['total_price'] - $order['shipping_fee'] + $order['voucher_discount']);
                if ($diskon_produk > 0) {
                    $total_diskon += $diskon_produk;
                    echo '<div class="row"><span>Diskon Produk</span><span>-Rp '.number_format($diskon_produk, 0, ',', '.').'</span></div>';
                }
                if ($order['voucher_discount'] > 0) {
                    $total_diskon += $order['voucher_discount'];
                    echo '<div class="row"><span>Diskon Voucher</span><span>-Rp '.number_format($order['voucher_discount'], 0, ',', '.').'</span></div>';
                }
            ?>

            <?php if ($order['shipping_fee'] > 0): ?>
            <div class="row">
                <span>Ongkos Kirim</span>
                <span>Rp <?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="row total">
                <span>TOTAL AKHIR</span>
                <span>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></span>
            </div>
        </div>
        
        <hr>
        <div class="details">
            <p><strong>Metode Pengambilan:</strong> <?php
                if ($order['guest_address'] === 'Ambil Ditempat') {
                    echo 'Ambil Ditempat';
                } elseif (stripos($order['payment_method'], 'cod') !== false) {
                    echo 'COD';
                } else {
                    echo 'Delivery';
                }
            ?></p>
            <p><strong>Metode Pembayaran:</strong> <?php echo !empty($order['payment_choice']) ? htmlspecialchars(ucfirst($order['payment_choice'])) : htmlspecialchars($order['payment_method']); ?></p>
            <p><strong>Status Pembayaran:</strong> <?php echo htmlspecialchars(ucfirst($order['status'])); ?></p>
            <p><strong>Metode (Sistem):</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
            <?php if ($order['guest_address'] !== 'Ambil Ditempat'): ?>
            <p><strong>Alamat:</strong> <?php echo htmlspecialchars($order['guest_address']); ?></p>
            <?php endif; ?>

            <?php if (!empty(trim($order['pickup_datetime']))): ?>
            <p><strong>Jadwal:</strong> <?php echo htmlspecialchars($order['pickup_datetime']); ?></p>
            <?php endif; ?>

            <?php if (!empty(trim($order['order_notes']))): ?>
            <p><strong>Catatan:</strong> <?php echo htmlspecialchars($order['order_notes']); ?></p>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Terima Kasih!</p>
            <p>-- classic-coffee-789.wuaze.com --</p>
        </div>
    </div>
</body>
</html>