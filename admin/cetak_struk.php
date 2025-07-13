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

// Ambil data pesanan utama
$sql_order = "SELECT o.id, o.order_date, o.total_price, COALESCE(u.username, o.guest_name) AS customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

// Ambil semua item dari pesanan tersebut
$sql_items = "SELECT oi.quantity, oi.price_per_item, p.name AS product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pesanan #<?php echo $order['id']; ?></title>
    <style>
        body { font-family: 'Courier New', monospace; width: 300px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .item { margin-bottom: 5px; }
        .total { display: flex; justify-content: space-between; font-weight: bold; border-top: 1px dashed #000; margin-top: 10px; padding-top: 10px;}
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="header">
            <h2>Classic Coffee 789</h2>
            <p>Jl. Kopi Nikmat No. 123, Jakarta</p>
        </div>
        <hr>
        <p>No. Pesanan: <?php echo $order['id']; ?></p>
        <p>Tanggal: <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
        <p>Pelanggan: <?php echo htmlspecialchars($order['customer_name']); ?></p>
        <hr>
        
        <?php while($item = $items->fetch_assoc()): ?>
            <div class="item">
                <div><?php echo htmlspecialchars($item['product_name']); ?></div>
                <div><?php echo $item['quantity']; ?> x Rp <?php echo number_format($item['price_per_item'], 0, ',', '.'); ?></div>
            </div>
        <?php endwhile; ?>

        <div class="total">
            <span>TOTAL</span>
            <span>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></span>
        </div>
        <div style="text-align: center; margin-top: 30px;">
            <p>Terima Kasih!</p>
        </div>
    </div>
</body>
</html>