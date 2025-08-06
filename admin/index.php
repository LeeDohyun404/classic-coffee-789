<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

// Aksi untuk Konfirmasi Pembayaran
if (isset($_GET['action']) && $_GET['action'] == 'confirm_payment' && isset($_GET['id'])) {
    $order_id_to_confirm = $_GET['id'];
    
    $conn->begin_transaction();
    try {
        // 1. Ubah status pesanan menjadi 'paid'
        $stmt_update_status = $conn->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
        $stmt_update_status->bind_param("i", $order_id_to_confirm);
        $stmt_update_status->execute();

        // 2. Cek apakah pesanan ini milik member
        $stmt_get_user = $conn->prepare("SELECT user_id FROM orders WHERE id = ?");
        $stmt_get_user->bind_param("i", $order_id_to_confirm);
        $stmt_get_user->execute();
        $order_data = $stmt_get_user->get_result()->fetch_assoc();
        $user_id = $order_data['user_id'];

        // Hanya jalankan logika loyalitas jika pesanan ini dari member
        if ($user_id) {
            // 3. Hitung jumlah minuman dalam pesanan ini
            $sql_drinks_this_order = "SELECT SUM(oi.quantity) as count FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ? AND p.category LIKE 'minuman%'";
            $stmt_drinks_this_order = $conn->prepare($sql_drinks_this_order);
            $stmt_drinks_this_order->bind_param("i", $order_id_to_confirm);
            $stmt_drinks_this_order->execute();
            $drinks_bought_count = $stmt_drinks_this_order->get_result()->fetch_assoc()['count'] ?? 0;

            if ($drinks_bought_count > 0) {
                // 4. Hitung semua minuman dari pesanan 'paid' sebelumnya
                $sql_total_drinks = "SELECT SUM(oi.quantity) as total_drinks FROM order_items oi JOIN orders o ON oi.order_id = o.id JOIN products p ON oi.product_id = p.id WHERE o.user_id = ? AND o.status = 'paid' AND o.id != ?";
                $stmt_total = $conn->prepare($sql_total_drinks);
                $stmt_total->bind_param("ii", $user_id, $order_id_to_confirm);
                $stmt_total->execute();
                $total_drinks_before = $stmt_total->get_result()->fetch_assoc()['total_drinks'] ?? 0;

                // 5. Cek apakah ada voucher baru yang didapat
                $vouchers_before = floor($total_drinks_before / 10);
                $vouchers_after = floor(($total_drinks_before + $drinks_bought_count) / 10);
                $new_vouchers_earned = $vouchers_after - $vouchers_before;

                if ($new_vouchers_earned > 0) {
                    $stmt_new_voucher = $conn->prepare("INSERT INTO vouchers (user_id, voucher_code, expires_at) VALUES (?, ?, ?)");
                    $expires_at = date('Y-m-d', strtotime('+30 days'));
                    for ($i = 0; $i < $new_vouchers_earned; $i++) {
                        $new_voucher_code = 'GRATIS-' . strtoupper(substr(uniqid(), 7, 6));
                        $stmt_new_voucher->bind_param("iss", $user_id, $new_voucher_code, $expires_at);
                        $stmt_new_voucher->execute();
                    }
                }
            }
        }
        
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Terjadi kesalahan saat konfirmasi pembayaran: " . $e->getMessage());
    }
    
    header("Location: index.php?status=confirmed");
    exit();
}
// ... (Sisa kode untuk statistik dan HTML tetap sama, pastikan Anda menggunakan versi terakhir)
?>
<?php
// Menghitung Statistik Dashboard (KODE DIPERBAIKI AGAR LEBIH AMAN)
$stats_sql = "SELECT 
    COUNT(*) as total_orders,
    SUM(total_price) as total_revenue,
    COUNT(CASE WHEN DATE(order_date) = CURDATE() THEN 1 END) as today_orders
FROM orders";
$stats_result = $conn->query($stats_sql);
$stats = ($stats_result && $stats_result->num_rows > 0) ? $stats_result->fetch_assoc() : [
    'total_orders' => 0,
    'total_revenue' => 0,
    'today_orders' => 0
];

// Menghitung pesanan bulan ini (KODE DIPERBAIKI AGAR LEBIH AMAN)
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$month_orders_sql = "SELECT COUNT(*) as month_orders FROM orders WHERE DATE(order_date) BETWEEN ? AND ?";
$stmt_month = $conn->prepare($month_orders_sql);
$stmt_month->bind_param("ss", $month_start, $month_end);
$stmt_month->execute();
$month_orders_result = $stmt_month->get_result();
$month_data = ($month_orders_result && $month_orders_result->num_rows > 0) ? $month_orders_result->fetch_assoc() : null;
$stats['month_orders'] = $month_data['month_orders'] ?? 0;

// Mengambil data pesanan untuk ditampilkan di tabel
$sql_orders_list = "SELECT 
                        o.id AS order_id, 
                        o.order_date,
                        o.total_price,
                        o.voucher_discount,
                        o.guest_phone,
                        o.guest_email,
                        o.status,
                        COALESCE(u.username, o.guest_name) AS customer_name,
                        CASE WHEN o.user_id IS NOT NULL THEN 'Member' ELSE 'Tamu' END AS customer_type
                    FROM orders o
                    LEFT JOIN users u ON o.user_id = u.id
                    ORDER BY o.order_date DESC";
$result_orders_list = $conn->query($sql_orders_list);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Classic Coffee 789</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: #5a3a22; color: white; padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 24px; margin: 0; display: flex; align-items: center; gap: 10px;}
        .header-nav a { background: rgba(255,255,255,0.2); color: white; border: none; padding: 10px 18px; border-radius: 20px; cursor: pointer; text-decoration:none; margin-left: 10px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; padding: 30px; background: #f8f9fa; }
        .stat-card { background: white; border-radius: 10px; padding: 25px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .stat-card h3 { font-size: 28px; margin: 0 0 5px 0; }
        .table-container { padding: 30px; overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        .table th { background: #f8f9fa; font-size: 13px; text-transform: uppercase; color: #666; }
        .customer-type { font-size: 12px; padding: 3px 8px; border-radius: 10px; color: white; display: inline-block; }
        .type-member { background: #28a745; }
        .type-guest { background: #6c757d; }
        .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-action { color: #fff; padding: 6px 12px; font-size: 13px; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 5px; }
        .btn-edit { background: #3498db; }
        .btn-delete { background: #e74c3c; }
        .btn-print { background: #2ecc71; }
        .status-badge { padding: 5px 10px; border-radius: 15px; color: white; font-size: 12px; font-weight: bold; text-transform: capitalize; }
        .status-pending { background-color: #f39c12; }
        .status-paid { background-color: #28a745; }
        .status-free { background-color: #6c757d; } /* Abu-abu */
        .price-details { line-height: 1.4; }
        .original-price { color: #666; }
        .discount { color: #28a745; font-weight: bold; }
        .final-price { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-coffee"></i> Dashboard Admin</h1>
           <div class="header-nav">
              <a href="kelola_produk.php">Kelola Produk</a>
              <a href="kelola_pelanggan.php">Kelola Pelanggan</a>
              <a href="kelola_slider.php">Kelola Slider</a> 
              <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><h3><?php echo $stats['total_orders']; ?></h3><p>Total Pesanan</p></div>
            <div class="stat-card"><h3>Rp <?php echo number_format($stats['total_revenue'] ?? 0, 0, ',', '.'); ?></h3><p>Total Pendapatan</p></div>
            <div class="stat-card"><h3><?php echo $stats['today_orders']; ?></h3><p>Pesanan Hari Ini</p></div>
            <div class="stat-card"><h3><?php echo $stats['month_orders']; ?></h3><p>Pesanan Bulan Ini</p></div>
        </div>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pemesan</th>
                        <th>Tanggal</th>
                        <th>Total Harga</th>
                        <th>Detail Kontak</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
    <?php if ($result_orders_list && $result_orders_list->num_rows > 0): ?>
        <?php while($row = $result_orders_list->fetch_assoc()): ?>
            <tr>
                <td><strong>#<?php echo htmlspecialchars($row['order_id']); ?></strong></td>
                <td>
                    <?php echo htmlspecialchars($row['customer_name']); ?><br>
                    <span class="customer-type <?php echo ($row['customer_type'] == 'Member' ? 'type-member' : 'type-guest'); ?>"><?php echo $row['customer_type']; ?></span>
                </td>
                <td><?php echo date('d M Y, H:i', strtotime($row['order_date'])); ?></td>
                <td>
                    <div class="price-details">
                        <?php if ($row['voucher_discount'] > 0): ?>
                            <div class="original-price">
                                Subtotal: Rp <?php echo number_format($row['total_price'] + $row['voucher_discount'], 0, ',', '.'); ?>
                            </div>
                            <div class="discount">
                                <i class="fas fa-ticket-alt"></i> Diskon: -Rp <?php echo number_format($row['voucher_discount'], 0, ',', '.'); ?>
                            </div>
                            <div class="final-price">
                                Total: Rp <?php echo number_format($row['total_price'], 0, ',', '.'); ?>
                            </div>
                        <?php else: ?>
                            <div class="final-price">
                                <strong>Rp <?php echo number_format($row['total_price'], 0, ',', '.'); ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($row['guest_phone']); ?><br><?php echo htmlspecialchars($row['guest_email']); ?></td>
                
                <td>
                    <?php
                        $status_class = '';
                        switch ($row['status']) {
                            case 'paid':
                                $status_class = 'status-paid';
                                break;
                            case 'free':
                                $status_class = 'status-free';
                                break;
                            default: // pending
                                $status_class = 'status-pending';
                                break;
                        }
                    ?>
                    <span class="status-badge <?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </span>
                </td>
                
                <td class="action-buttons">
                    <?php if ($row['status'] == 'pending'): ?>
                        <a href="index.php?action=confirm_payment&id=<?php echo (int)$row['order_id']; ?>" class="btn-action" style="background-color:#28a745;" onclick="return confirm('Anda yakin ingin mengonfirmasi pembayaran untuk pesanan ini?');">Konfirmasi Bayar</a>
                    <?php endif; ?>
                    
                    <a href="edit_pesanan.php?id=<?php echo (int)$row['order_id']; ?>" class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</a>
                    <a href="hapus_pesanan.php?id=<?php echo (int)$row['order_id']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus pesanan ini?');"><i class="fas fa-trash"></i> Hapus</a>
                    <a href="cetak_struk.php?order_id=<?php echo (int)$row['order_id']; ?>" class="btn-action btn-print" target="_blank"><i class="fas fa-print"></i> Cetak</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7" style="text-align:center;">Tidak ada pesanan yang ditemukan.</td></tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </div>
</body>
</html>
