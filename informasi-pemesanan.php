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

// Mengambil item-item pesanan beserta info diskon
$sql_items = "SELECT oi.quantity, p.name AS product_name, oi.price_per_item, p.discount_name, p.discount_methods FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();

// Cek apakah ada diskon produk karena metode tertentu
$diskon_keterangan = '';
$metode_pengambilan = isset($order['payment_method']) ? strtolower($order['payment_method']) : '';
$items->data_seek(0); // reset pointer
while($item_diskon = $items->fetch_assoc()) {
    if (!empty($item_diskon['discount_methods'])) {
        $allowed_methods = array_map('trim', explode(',', $item_diskon['discount_methods']));
        if (in_array($metode_pengambilan, $allowed_methods)) {
            $diskon_keterangan = 'Diskon: ' . ($item_diskon['discount_name'] ? $item_diskon['discount_name'] : 'Promo Produk') . ' berlaku untuk metode ' . ucfirst($metode_pengambilan);
            break;
        }
    }
}
$items->data_seek(0); // reset pointer lagi untuk loop tabel

// Hitung subtotal asli sebelum diskon
$subtotal_price = $order['total_price'] + $order['voucher_discount'];
?>
<?php
// ================== TAMBAHAN BARU: DETEKSI PRE-ORDER ==================
$is_pre_order = ($order && $order['total_price'] == 0.00);
// Untuk lebih akurat, Anda bisa melakukan pengecekan ke kategori produk seperti yang saya sarankan sebelumnya,
// namun pengecekan harga 0 sudah cukup kuat untuk sistem ini.
// ================== AKHIR TAMBAHAN ==================
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
            
           <h2> 🛒 <?php echo $is_pre_order ? 'Pre-Order Telah Diterima!' : 'Pesanan Telah Diterima!'; ?></h2>
            <p>Terima kasih, <?php echo htmlspecialchars($order['customer_name']); ?>. <?php echo $is_pre_order ? 'Admin kami akan segera menghubungi Anda melalui WhatsApp untuk konfirmasi harga, ketersediaan, dan detail pembayaran.' : 'Berikut adalah rincian pesanan Anda.'; ?></p>

            <?php if (!empty($order['whatsapp_url'])): ?>
                <a href="<?php echo htmlspecialchars($order['whatsapp_url']); ?>" target="_blank" class="btn" style="display:inline-block; margin-top: 20px; margin-bottom: 20px; width: auto; padding: 15px 30px; background: #25D366; text-decoration:none; color:white; border-radius:8px; font-size: 1.2em;">
                    <i class="fab fa-whatsapp"></i> <?php echo $is_pre_order ? 'Konfirmasi Pre-Order via WhatsApp' : 'Lanjutkan ke WhatsApp'; ?>
                </a>
            <?php endif; ?>
            
            <hr style="margin: 30px 0;">
            <h3 style="color: #5a3a22;">Detail Informasi Pesanan</h3>
            <table style="width:100%; margin-bottom: 25px; text-align:left;">
                <tr><td style="padding:6px 0;">Nomor Pesanan</td><td style="padding:6px 0;">: <?php echo $order['id']; ?></td></tr>
                <tr><td style="padding:6px 0;">Tanggal Pemesanan</td><td style="padding:6px 0;">: <?php echo isset($order['order_date']) ? htmlspecialchars($order['order_date']) : '-'; ?></td></tr>
                <tr><td style="padding:6px 0;">Metode Pengambilan</td><td style="padding:6px 0;">: <?php echo isset($order['payment_method']) ? htmlspecialchars($order['payment_method']) : '-'; ?></td></tr>
                <tr><td style="padding:6px 0;">Status Pesanan</td><td style="padding:6px 0;">: <?php echo isset($order['status']) ? htmlspecialchars($order['status']) : '-'; ?></td></tr>
                <tr><td style="padding:6px 0;">Alamat Pengiriman</td><td style="padding:6px 0;">: <?php echo isset($order['guest_address']) ? nl2br(htmlspecialchars($order['guest_address'])) : '-'; ?></td></tr>
                <tr><td style="padding:6px 0;">Catatan Pesanan</td><td style="padding:6px 0;">: <?php echo isset($order['order_notes']) ? nl2br(htmlspecialchars($order['order_notes'])) : '-'; ?></td></tr>
                <tr><td style="padding:6px 0;">Kontak Pelanggan</td><td style="padding:6px 0;">: <?php echo isset($order['guest_phone']) ? htmlspecialchars($order['guest_phone']) : '-'; ?><?php if(isset($order['guest_email'])) echo ' / ' . htmlspecialchars($order['guest_email']); ?></td></tr>
                <tr><td style="padding:6px 0;">Jadwal Ambil/Kirim</td><td style="padding:6px 0;">: <?php echo isset($order['pickup_datetime']) ? htmlspecialchars($order['pickup_datetime']) : '-'; ?></td></tr>
                <tr><td style="padding:6px 0;">Pilihan Pembayaran</td><td style="padding:6px 0;">: <?php echo isset($order['payment_choice']) ? htmlspecialchars($order['payment_choice']) : '-'; ?></td></tr>
                            </table>
            <h3 style="color: #5a3a22;">Rincian Pesanan Anda</h3>
            
            <table style="width: 100%; text-align: left; border-collapse: collapse; margin-top: 10px;">
                <table style="width: 100%; ...">
                <thead>
                    <tr style="background-color: #f1f1f1;">
                        <th style="padding: 10px;">Produk</th>
                        <th style="padding: 10px; text-align:center;">Jumlah</th>
                        <th style="padding: 10px; text-align:right;">Subtotal</th>
                         <?php if (!$is_pre_order): ?>
                <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                     <?php while($item = $items->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td style="padding: 10px; text-align:center;"><?php echo $item['quantity']; ?></td>
                        <?php if (!$is_pre_order): ?>
                        <td style="padding: 10px; text-align:right;">Rp <?php echo number_format($item['price_per_item'] * $item['quantity'], 0, ',', '.'); ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                 <?php if (!$is_pre_order): ?>
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
                <?php endif; ?>
            </table>
            <a href="index.php" class="btn" style="display:inline-block; margin-top: 30px; width: auto; padding: 10px 30px; background: #5a3a22; text-decoration:none; color:white; border-radius:8px;">Kembali ke Beranda</a>
            </div>
    </main>
<script>
// Animasi pesan sukses
window.addEventListener('DOMContentLoaded', function() {
    const pesan = document.querySelector('h2');
    if (pesan) {
        pesan.style.opacity = 0;
        pesan.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            pesan.style.transition = 'all 0.7s cubic-bezier(.68,-0.55,.27,1.55)';
            pesan.style.opacity = 1;
            pesan.style.transform = 'translateY(0)';
        }, 200);
    }
    // Highlight baris tabel saat hover
    document.querySelectorAll('tbody tr').forEach(tr => {
        tr.addEventListener('mouseenter', () => tr.style.background = '#ffe5b4');
        tr.addEventListener('mouseleave', () => tr.style.background = '');
    });
    // Smooth scroll ke detail pesanan jika ada hash
    if (window.location.hash === '#detail') {
        const detail = document.querySelector('h3');
        if (detail) detail.scrollIntoView({behavior:'smooth'});
    }
});
</script>
<style>
body {
    background: linear-gradient(120deg, #f3e7e9 0%, #e3eeff 100%);
    font-family: 'Segoe UI', 'Arial', sans-serif;
    margin: 0;
}
header {
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}
main > div {
    box-shadow: 0 4px 24px 0 rgba(139,69,19,0.10);
    transition: box-shadow 0.3s;
}
main > div:hover {
    box-shadow: 0 8px 32px 0 rgba(139,69,19,0.18);
}
h2 {
    color: #25D366;
    letter-spacing: 1px;
    margin-bottom: 10px;
}
h3 {
    margin-top: 30px;
    margin-bottom: 10px;
    color: #5a3a22;
    font-size: 1.2em;
    border-left: 5px solid #8B4513;
    padding-left: 10px;
}
table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
thead th {
    background: #f8f5f2;
    color: #5a3a22;
    font-weight: 600;
}
tbody td, tfoot td, thead th {
    border: none;
}
tbody tr {
    transition: background 0.2s;
}
tfoot tr {
    background: #f6f6f6;
}
.btn {
    box-shadow: 0 2px 8px rgba(90,58,34,0.10);
    transition: background 0.2s, box-shadow 0.2s;
}
.btn:hover {
    background: #a05a2c !important;
    color: #fff !important;
    box-shadow: 0 4px 16px rgba(90,58,34,0.18);
}
@media (max-width: 700px) {
    main > div { padding: 10px; }
    table, thead, tbody, tfoot, tr, td, th { font-size: 0.97em; }
}
</style>
</body>
</html>