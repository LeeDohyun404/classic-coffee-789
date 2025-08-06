<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

$message = '';
// Proses update pengaturan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_active = isset($_POST['pickup_discount_active']) ? '1' : '0';
    $percentage = (int)$_POST['pickup_discount_percentage'];

    // Update status aktif
    $stmt_active = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_name = 'pickup_discount_active'");
    $stmt_active->bind_param("s", $is_active);
    $stmt_active->execute();

    // Update persentase
    $stmt_percent = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_name = 'pickup_discount_percentage'");
    $stmt_percent->bind_param("s", $percentage);
    $stmt_percent->execute();

    $message = "Pengaturan berhasil diperbarui!";
}

// Ambil pengaturan saat ini dari database
$settings = [];
$result = $conn->query("SELECT * FROM site_settings");
while($row = $result->fetch_assoc()){
    $settings[$row['setting_name']] = $row['setting_value'];
}
$is_discount_active = $settings['pickup_discount_active'] ?? '0';
$discount_percentage = $settings['pickup_discount_percentage'] ?? '10';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pengaturan - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.07); }
        h1 { color: #5a3a22; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .form-container { border: 1px solid #eee; padding: 20px; border-radius: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input[type="number"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .form-group .checkbox-label { display: flex; align-items: center; gap: 10px; font-weight: normal; }
        .message { padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; color: white; text-decoration: none; font-weight: 600; }
        .btn-primary { background-color: #5a3a22; }
        .btn-dashboard { background-color: #6c757d; }
        .button-group { margin-top: 20px; display: flex; gap: 10px; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-cogs"></i> Kelola Pengaturan Website</h1>
        
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h2>Diskon Pesanan "Ambil Ditempat"</h2>
            <form action="kelola_pengaturan.php" method="POST">
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="pickup_discount_active" value="1" <?php echo ($is_discount_active == '1') ? 'checked' : ''; ?>>
                        Aktifkan Diskon 10% untuk Metode "Ambil Ditempat"
                    </label>
                    <p style="font-size: 12px; color: #666;">Jika diaktifkan, setiap pesanan dengan metode "Ambil Ditempat" akan otomatis mendapatkan potongan harga sebesar persentase di bawah ini.</p>
                </div>
                <div class="form-group">
                    <label for="pickup_discount_percentage">Persentase Diskon (%)</label>
                    <input type="number" name="pickup_discount_percentage" id="pickup_discount_percentage" value="<?php echo htmlspecialchars($discount_percentage); ?>" min="0" max="100" required>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pengaturan</button>
                    <a href="index.php" class="btn btn-dashboard"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>