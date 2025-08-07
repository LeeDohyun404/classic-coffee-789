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

// Menghitung Statistik Dashboard
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

// Menghitung pesanan bulan ini
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Classic Coffee 789</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #f4f7f6 0%, #e8f0ef 100%); 
            margin: 0; 
            padding: 20px; 
            min-height: 100vh;
        }

        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s ease forwards;
        }

        .header { 
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%); 
            color: white; 
            padding: 25px 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1.5" fill="rgba(255,255,255,0.1)"/></svg>');
            pointer-events: none;
        }

        .header h1 { 
            font-size: 28px; 
            margin: 0; 
            display: flex; 
            align-items: center; 
            gap: 12px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .header h1 i {
            font-size: 32px;
            animation: pulse 2s infinite;
        }

        .header-nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .header-nav a { 
            background: rgba(255,255,255,0.15); 
            color: white; 
            border: none; 
            padding: 12px 20px; 
            border-radius: 25px; 
            cursor: pointer; 
            text-decoration: none; 
            transition: all 0.3s ease;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .header-nav a:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 25px; 
            padding: 40px 30px; 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .stat-card { 
            background: white; 
            border-radius: 15px; 
            padding: 30px; 
            text-align: center; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(90, 58, 34, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #5a3a22 0%, #8b6f47 100%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        }

        .stat-card h3 { 
            font-size: 32px; 
            margin: 0 0 8px 0; 
            color: #5a3a22;
            font-weight: 700;
        }

        .stat-card p {
            color: #666;
            font-size: 16px;
            font-weight: 500;
            margin: 0;
        }

        .table-container { 
            padding: 30px; 
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-title {
            font-size: 24px;
            font-weight: 700;
            color: #5a3a22;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box {
            position: relative;
            max-width: 300px;
            width: 100%;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 25px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #5a3a22;
            box-shadow: 0 0 0 3px rgba(90, 58, 34, 0.1);
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .table { 
            width: 100%; 
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .table th, .table td { 
            padding: 18px 15px; 
            text-align: left; 
            border-bottom: 1px solid #f1f3f4; 
            vertical-align: middle; 
        }

        .table th { 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); 
            font-size: 13px; 
            text-transform: uppercase; 
            color: #5a3a22; 
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .customer-type { 
            font-size: 11px; 
            padding: 4px 10px; 
            border-radius: 12px; 
            color: white; 
            display: inline-block; 
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .type-member { 
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
        }

        .type-guest { 
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%); 
        }

        .action-buttons { 
            display: flex; 
            gap: 8px; 
            flex-wrap: wrap; 
        }

        .btn-action { 
            color: #fff; 
            padding: 8px 14px; 
            font-size: 12px; 
            text-decoration: none; 
            border-radius: 8px; 
            display: inline-flex; 
            align-items: center; 
            gap: 6px;
            transition: all 0.3s ease;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-edit { 
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); 
        }

        .btn-delete { 
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); 
        }

        .btn-print { 
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); 
        }

        .btn-confirm {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }

        .status-badge { 
            padding: 6px 12px; 
            border-radius: 20px; 
            color: white; 
            font-size: 11px; 
            font-weight: 600; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .status-pending { 
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); 
        }

        .status-paid { 
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
        }

        .status-free { 
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%); 
        }

        .price-details { 
            line-height: 1.5; 
        }

        .original-price { 
            color: #999; 
            font-size: 13px;
        }

        .discount { 
            color: #28a745; 
            font-weight: 600; 
            font-size: 13px;
        }

        .final-price { 
            font-weight: 600; 
            color: #5a3a22;
            font-size: 14px;
        }

        .order-id {
            font-weight: 700;
            color: #5a3a22;
            font-size: 16px;
        }

        .customer-info {
            line-height: 1.4;
        }

        .customer-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .contact-info {
            font-size: 13px;
            color: #666;
            line-height: 1.3;
        }

        .date-info {
            font-weight: 500;
            color: #5a3a22;
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        /* Success Message */
        .success-message {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 20px 30px;
            border: 1px solid #c3e6cb;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInDown 0.5s ease;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Mobile Responsive */
        @media (max-width: 1024px) {
            body { padding: 15px; }
            .container { border-radius: 15px; }
            .header { padding: 20px 25px; }
            .header h1 { font-size: 24px; }
            .stats-grid { padding: 30px 25px; gap: 20px; }
            .table-container { padding: 25px; }
        }

        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { border-radius: 12px; }
            
            .header { 
                padding: 20px; 
                flex-direction: column; 
                gap: 15px; 
                text-align: center;
            }
            
            .header h1 { 
                font-size: 22px; 
                justify-content: center;
            }
            
            .header-nav { 
                justify-content: center; 
                width: 100%;
            }
            
            .header-nav a { 
                padding: 10px 16px; 
                font-size: 14px;
            }
            
            .stats-grid { 
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
                padding: 25px 20px; 
                gap: 15px;
            }
            
            .stat-card { 
                padding: 20px; 
            }
            
            .stat-card h3 { 
                font-size: 28px; 
            }
            
            .table-container { 
                padding: 20px; 
            }
            
            .table-header {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }
            
            .table-title {
                font-size: 20px;
                text-align: center;
            }
            
            .search-box {
                max-width: 100%;
            }
            
            .table th, .table td { 
                padding: 12px 8px; 
                font-size: 13px;
            }
            
            .action-buttons { 
                flex-direction: column; 
                gap: 5px;
            }
            
            .btn-action { 
                padding: 8px 12px; 
                font-size: 11px;
                justify-content: center;
            }
            
            .customer-type { 
                font-size: 10px; 
                padding: 3px 8px; 
            }
            
            .status-badge { 
                font-size: 10px; 
                padding: 4px 8px; 
            }
        }

        @media (max-width: 480px) {
            .header h1 { font-size: 20px; }
            .header-nav a { padding: 8px 12px; font-size: 12px; }
            .stats-grid { grid-template-columns: 1fr; }
            .stat-card h3 { font-size: 24px; }
            .table-title { font-size: 18px; }
            
            .table {
                font-size: 12px;
            }
            
            .table th, .table td { 
                padding: 10px 6px; 
            }
            
            .order-id { font-size: 14px; }
            .customer-name { font-size: 13px; }
            .contact-info { font-size: 11px; }
            .date-info { font-size: 12px; }
            .price-details { font-size: 12px; }
        }

        /* Table Scroll Indicator */
        .table-scroll-indicator {
            display: none;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            color: #666;
            font-size: 12px;
            border-radius: 0 0 10px 10px;
        }

        @media (max-width: 768px) {
            .table-scroll-indicator {
                display: block;
            }
        }

        /* Hide some columns on mobile */
        @media (max-width: 600px) {
            .table th:nth-child(5),
            .table td:nth-child(5) {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .table th:nth-child(3),
            .table td:nth-child(3),
            .table th:nth-child(4),
            .table td:nth-child(4) {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-coffee"></i> Dashboard Admin</h1>
            <div class="header-nav">
                <a href="kelola_produk.php"><i class="fas fa-box"></i> Kelola Produk</a>
                <a href="kelola_pelanggan.php"><i class="fas fa-users"></i> Kelola Pelanggan</a>
                <a href="kelola_slider.php"><i class="fas fa-images"></i> Kelola Slider</a> 
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'confirmed'): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <span>Pembayaran berhasil dikonfirmasi!</span>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3 id="total-orders"><?php echo $stats['total_orders']; ?></h3>
                <p><i class="fas fa-shopping-cart"></i> Total Pesanan</p>
            </div>
            <div class="stat-card">
                <h3 id="total-revenue">Rp <?php echo number_format($stats['total_revenue'] ?? 0, 0, ',', '.'); ?></h3>
                <p><i class="fas fa-money-bill-wave"></i> Total Pendapatan</p>
            </div>
            <div class="stat-card">
                <h3 id="today-orders"><?php echo $stats['today_orders']; ?></h3>
                <p><i class="fas fa-calendar-day"></i> Pesanan Hari Ini</p>
            </div>
            <div class="stat-card">
                <h3 id="month-orders"><?php echo $stats['month_orders']; ?></h3>
                <p><i class="fas fa-calendar-alt"></i> Pesanan Bulan Ini</p>
            </div>
        </div>
        
        <div class="table-container">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-list"></i> Daftar Pesanan
                </div>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Cari pesanan...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            
            <table class="table" id="ordersTable">
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
                                <td>
                                    <span class="order-id">#<?php echo htmlspecialchars($row['order_id']); ?></span>
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                                        <span class="customer-type <?php echo ($row['customer_type'] == 'Member' ? 'type-member' : 'type-guest'); ?>">
                                            <?php echo $row['customer_type']; ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="date-info"><?php echo date('d M Y, H:i', strtotime($row['order_date'])); ?></span>
                                </td>
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
                                                Rp <?php echo number_format($row['total_price'], 0, ',', '.'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        <div><?php echo htmlspecialchars($row['guest_phone']); ?></div>
                                        <div><?php echo htmlspecialchars($row['guest_email']); ?></div>
                                    </div>
                                </td>
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
                                        <button class="btn-action btn-confirm" onclick="confirmPayment(<?php echo (int)$row['order_id']; ?>)">
                                            <i class="fas fa-check"></i> Konfirmasi
                                        </button>
                                    <?php endif; ?>
                                    
                                    <a href="edit_pesanan.php?id=<?php echo (int)$row['order_id']; ?>" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button class="btn-action btn-delete" onclick="deleteOrder(<?php echo (int)$row['order_id']; ?>)">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                    <a href="cetak_struk.php?order_id=<?php echo (int)$row['order_id']; ?>" class="btn-action btn-print" target="_blank">
                                        <i class="fas fa-print"></i> Cetak
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 40px; color: #666;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                                <div>Tidak ada pesanan yang ditemukan.</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="table-scroll-indicator">
                <i class="fas fa-arrows-alt-h"></i> Geser tabel untuk melihat lebih banyak kolom
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== SEARCH FUNCTIONALITY =====
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('ordersTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    const cells = row.getElementsByTagName('td');
                    let found = false;
                    
                    for (let j = 0; j < cells.length; j++) {
                        const cellText = cells[j].textContent.toLowerCase();
                        if (cellText.includes(searchTerm)) {
                            found = true;
                            break;
                        }
                    }
                    
                    row.style.display = found ? '' : 'none';
                }
            });

            // ===== ANIMATED COUNTERS =====
            function animateCounter(element, target) {
                const start = 0;
                const duration = 1500;
                const startTime = performance.now();
                
                function updateCounter(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    const current = Math.floor(progress * target);
                    
                    if (element.id === 'total-revenue') {
                        element.textContent = 'Rp ' + current.toLocaleString('id-ID');
                    } else {
                        element.textContent = current;
                    }
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    }
                }
                
                requestAnimationFrame(updateCounter);
            }

            // Start counter animations
            setTimeout(() => {
                const totalOrders = document.getElementById('total-orders');
                const totalRevenue = document.getElementById('total-revenue');
                const todayOrders = document.getElementById('today-orders');
                const monthOrders = document.getElementById('month-orders');

                if (totalOrders) {
                    const target = parseInt(totalOrders.textContent);
                    animateCounter(totalOrders, target);
                }

                if (totalRevenue) {
                    const target = parseInt(totalRevenue.textContent.replace(/[^\d]/g, ''));
                    animateCounter(totalRevenue, target);
                }

                if (todayOrders) {
                    const target = parseInt(todayOrders.textContent);
                    animateCounter(todayOrders, target);
                }

                if (monthOrders) {
                    const target = parseInt(monthOrders.textContent);
                    animateCounter(monthOrders, target);
                }
            }, 500);

            // ===== TABLE ROW ANIMATIONS =====
            const tableRows = document.querySelectorAll('.table tbody tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 100 * index);
            });

            // ===== SUCCESS MESSAGE AUTO HIDE =====
            const successMessage = document.querySelector('.success-message');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.transition = 'all 0.5s ease';
                    successMessage.style.opacity = '0';
                    successMessage.style.transform = 'translateY(-20px)';
                    
                    setTimeout(() => {
                        successMessage.remove();
                    }, 500);
                }, 3000);
            }

            // ===== MOBILE TABLE SCROLL HINT =====
            const tableContainer = document.querySelector('.table-container');
            const table_element = document.querySelector('.table');
            
            if (window.innerWidth <= 768) {
                let hasScrolled = false;
                
                tableContainer.addEventListener('scroll', function() {
                    if (!hasScrolled) {
                        hasScrolled = true;
                        const indicator = document.querySelector('.table-scroll-indicator');
                        if (indicator) {
                            indicator.style.display = 'none';
                        }
                    }
                });
            }

            console.log('Admin dashboard initialized successfully');
        });

        // ===== GLOBAL FUNCTIONS =====
        
        function confirmPayment(orderId) {
            if (confirm('Anda yakin ingin mengonfirmasi pembayaran untuk pesanan ini?')) {
                const button = event.target.closest('.btn-confirm');
                const originalContent = button.innerHTML;
                
                button.innerHTML = '<span class="loading"></span> Memproses...';
                button.disabled = true;
                
                // Simulate processing time
                setTimeout(() => {
                    window.location.href = `index.php?action=confirm_payment&id=${orderId}`;
                }, 800);
            }
        }

        function deleteOrder(orderId) {
            if (confirm('Yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan.')) {
                const button = event.target.closest('.btn-delete');
                const originalContent = button.innerHTML;
                
                button.innerHTML = '<span class="loading"></span> Menghapus...';
                button.disabled = true;
                
                // Simulate processing time
                setTimeout(() => {
                    window.location.href = `hapus_pesanan.php?id=${orderId}`;
                }, 800);
            }
        }

        // ===== RESPONSIVE TABLE HANDLING =====
        function handleResponsiveTable() {
            const table = document.querySelector('.table');
            const container = document.querySelector('.table-container');
            
            if (window.innerWidth <= 768) {
                // Add touch scroll indicators for mobile
                container.style.overflowX = 'auto';
                container.style.webkitOverflowScrolling = 'touch';
            }
        }

        window.addEventListener('resize', handleResponsiveTable);
        handleResponsiveTable();
    </script>
</body>
</html>