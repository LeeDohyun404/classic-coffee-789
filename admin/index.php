<?php
session_start();

// BAGIAN INI SANGAT PENTING UNTUK KEAMANAN
// Jika tidak ada sesi login admin, tendang kembali ke halaman login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

// Statistik Dashboard
$stats_sql = "SELECT 
    COUNT(*) as total_orders,
    SUM(total_price) as total_revenue,
    COUNT(CASE WHEN DATE(order_date) = CURDATE() THEN 1 END) as today_orders,
    COUNT(CASE WHEN MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE()) THEN 1 END) as month_orders
FROM orders";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// Filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

$where_conditions = [];
if ($search) {
    $search_escaped = $conn->real_escape_string($search);
    $where_conditions[] = "COALESCE(u.username, o.guest_name) LIKE '%$search_escaped%'";
}
if ($date_filter) {
    $date_escaped = $conn->real_escape_string($date_filter);
    $where_conditions[] = "DATE(o.order_date) = '$date_escaped'";
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Classic Coffee 789</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: #5a3a22; color: white; padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 24px; gap: 15px; display: flex; align-items: center; }
        .logout-btn { background: rgba(255,255,255,0.2); color: white; border: none; padding: 10px 18px; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; text-decoration:none; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; padding: 30px; background: #f8f9fa; border-bottom: 1px solid #eee; }
        .stat-card { background: white; border-radius: 10px; padding: 25px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .stat-card i { font-size: 32px; margin-bottom: 15px; }
        .stat-card h3 { font-size: 28px; font-weight: 700; }
        .card-blue { color: #3498db; }
        .card-green { color: #2ecc71; }
        .card-orange { color: #f39c12; }
        .card-red { color: #e74c3c; }
        .controls { padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
        .search-form { display: flex; gap: 10px; align-items: center; }
        .search-input, .date-input { padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .btn { padding: 10px 18px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: #5a3a22; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .table-container { padding: 0 30px 30px 30px; overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: #f8f9fa; font-size: 13px; text-transform: uppercase; color: #666; }
        .table tbody tr:hover { background: #f4f7f6; }
        .customer-name { font-weight: 600; }
        .customer-type { font-size: 12px; padding: 3px 8px; border-radius: 10px; }
        .type-member { background: #d4edda; color: #155724; }
        .type-guest { background: #f8d7da; color: #721c24; }
        .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-action { color: #fff; padding: 6px 10px; font-size: 13px; text-decoration: none; border-radius: 5px; display:inline-flex; align-items:center; gap: 5px; }
        .btn-edit { background: #3498db; }
        .btn-delete { background: #e74c3c; }
        .btn-print { background: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-coffee"></i> Dashboard Admin</h1>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-shopping-cart card-blue"></i>
                <h3><?php echo $stats['total_orders']; ?></h3><p>Total Pesanan</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign card-green"></i>
                <h3>Rp <?php echo number_format($stats['total_revenue'] ?? 0, 0, ',', '.'); ?></h3><p>Total Pendapatan</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar-day card-orange"></i>
                <h3><?php echo $stats['today_orders']; ?></h3><p>Pesanan Hari Ini</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar-alt card-red"></i>
                <h3><?php echo $stats['month_orders']; ?></h3><p>Pesanan Bulan Ini</p>
            </div>
        </div>
        
        <div class="controls">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari nama pelanggan..." class="search-input" value="<?php echo htmlspecialchars($search); ?>">
                <input type="date" name="date" class="date-input" value="<?php echo htmlspecialchars($date_filter); ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
            </form>
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT 
                                o.id AS order_id, 
                                o.order_date,
                                o.total_price,
                                o.guest_phone,
                                o.guest_email,
                                COALESCE(u.username, o.guest_name) AS customer_name,
                                CASE WHEN o.user_id IS NOT NULL THEN 'Member' ELSE 'Tamu' END AS customer_type
                            FROM orders o
                            LEFT JOIN users u ON o.user_id = u.id
                            $where_clause
                            ORDER BY o.order_date DESC";
                    
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>#" . htmlspecialchars($row['order_id']) . "</strong></td>";
                            echo "<td><span class='customer-name'>" . htmlspecialchars($row['customer_name']) . "</span><br><span class='customer-type " . ($row['customer_type'] == 'Member' ? 'type-member' : 'type-guest') . "'>" . $row['customer_type'] . "</span></td>";
                            echo "<td>" . date('d M Y, H:i', strtotime($row['order_date'])) . "</td>";
                            echo "<td><strong>Rp " . number_format($row['total_price'], 0, ',', '.') . "</strong></td>";
                            echo "<td>" . htmlspecialchars($row['guest_phone']) . "<br>" . htmlspecialchars($row['guest_email']) . "</td>";
                            
                            // BAGIAN AKSI YANG LENGKAP
                            echo "<td>
                                    <div class='action-buttons'>
                                        <a href='edit_pesanan.php?id=" . (int)$row['order_id'] . "' class='btn-action btn-edit'>
                                            <i class='fas fa-edit'></i> Edit
                                        </a>
                                        <a href='hapus_pesanan.php?id=" . (int)$row['order_id'] . "' class='btn-action btn-delete' 
                                           onclick='return confirm(\"Yakin ingin menghapus pesanan ini?\");'>
                                            <i class='fas fa-trash'></i> Hapus
                                        </a>
                                        <a href='cetak_struk.php?order_id=" . (int)$row['order_id'] . "' class='btn-action btn-print' target='_blank'>
                                            <i class='fas fa-print'></i> Cetak
                                        </a>
                                    </div>
                                  </td>";
                            
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; padding: 40px;'>Tidak ada pesanan yang ditemukan.</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        document.querySelector('.date-input').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>