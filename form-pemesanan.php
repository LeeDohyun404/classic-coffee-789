<?php
require_once 'config.php';
if (empty($_SESSION['cart'])) {
    header("Location: keranjang.php");
    exit();
}
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pemesanan - Classic Coffee 789</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap');
        body { font-family: 'Poppins', sans-serif; margin: 0; color: #333; background-color: #f4f7f6; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 15px 50px; background: #fff; border-bottom: 1px solid #ddd; }
        .logo a { display: flex; align-items: center; gap: 12px; text-decoration: none; color: #5a3a22; font-family: 'Playfair Display', serif; font-size: 1.8em; font-weight: bold; }
        .logo img { height: 40px; }
        main { padding: 40px; }
        .form-container { max-width: 600px; margin: auto; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-container h1 { text-align: center; color: #5a3a22; font-family: 'Playfair Display', serif; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn { display: block; width: 100%; padding: 15px; border: none; border-radius: 8px; background-color: #8B4513; color: white; font-size: 16px; cursor: pointer; }
    </style>
</head>
<body>
    <header>
         <div class="logo">
            <a href="index.php"><img src="images/logo.png" alt="Logo"><span>Classic Coffee 789</span></a>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h1>Formulir Pengiriman</h1>
            <form action="proses_pesanan.php" method="POST">
                <?php if (!$is_logged_in): ?>
                    <div class="form-group">
                        <label for="guest_name">Nama Lengkap</label>
                        <input type="text" name="guest_name" id="guest_name" required>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="guest_address">Alamat Pengiriman</label>
                    <textarea name="guest_address" id="guest_address" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="guest_phone">No. HP (WhatsApp)</label>
                    <input type="tel" name="guest_phone" id="guest_phone" required>
                </div>
                <div class="form-group">
                    <label for="guest_email">Email</label>
                    <input type="email" name="guest_email" id="guest_email" required>
                </div>
                <button type="submit" class="btn">Selesaikan Pesanan</button>
            </form>
        </div>
    </main>
</body>
</html>