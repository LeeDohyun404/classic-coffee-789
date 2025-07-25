<?php 
require_once 'config.php';
// Hanya bisa diakses jika sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'header.php'; 

$user_id = $_SESSION['user_id'];
$vouchers = [];

// PERBAIKAN KUNCI: Tambahkan "AND is_used = 0"
$sql = "SELECT id, voucher_code, status, expires_at FROM vouchers WHERE user_id = ? AND is_used = 0 AND expires_at >= CURDATE() ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()){
    $vouchers[] = $row;
}
?>
<title>Voucher Saya - Classic Coffee 789</title>
<style>
    .voucher-card { background: #fff; border: 1px solid #ddd; border-left: 5px solid #a0522d; border-radius: 8px; padding: 20px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
    .voucher-info h4 { margin: 0 0 5px 0; color: #a0522d; }
    .voucher-info p { margin: 0; font-size: 14px; color: #666; }
    .btn-use-voucher { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
</style>
<div class="menu-page-container">
    <h1><i class="fas fa-ticket-alt"></i> Voucher Saya</h1>
    
    <?php if (empty($vouchers)): ?>
        <p style="text-align:center;">Anda tidak memiliki voucher yang aktif saat ini.</p>
    <?php else: ?>
        <p style="text-align:center; margin-bottom: 25px;">Pilih voucher yang ingin Anda gunakan. Voucher akan otomatis memotong harga minuman termahal di keranjang Anda.</p>
        <?php foreach ($vouchers as $voucher): ?>
            <div class="voucher-card">
                <div class="voucher-info">
                    <h4>Voucher Minuman Gratis</h4>
                    <p>Kode: <strong><?php echo htmlspecialchars($voucher['voucher_code']); ?></strong></p>
                    <p>Berlaku hingga: <?php echo date('d M Y', strtotime($voucher['expires_at'])); ?></p>
                </div>
                <div class="voucher-action">
                    <a href="gunakan_voucher.php?code=<?php echo htmlspecialchars($voucher['voucher_code']); ?>" class="btn-use-voucher">Gunakan</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>