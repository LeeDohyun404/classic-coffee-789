<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Kami - Classic Coffee 789</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header>
        <div class="logo">
    <img src="images/logo.png" alt="Classic Coffee 789 Logo">
    Classic Coffee 789
</div>
      <nav>
    <ul>
        <li><a href="index.php">Beranda</a></li>
        <li><a href="index.php" class="active">Produk</a></li>
        <li><a href="keranjang.php">Keranjang (<?php echo count($_SESSION['cart'] ?? []); ?>)</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="service.php">Service</a></li>
    </ul>
</nav>
    </header>

    <main style="padding: 40px 80px;">
        <h1>Layanan Kami</h1>
        <ul>
            <li><strong>Dine-in:</strong> Nikmati suasana kedai kami yang nyaman dengan Wi-Fi gratis.</li>
            <li><strong>Takeaway:</strong> Pesan kopi favoritmu untuk dinikmati di mana saja.</li>
            <li><strong>Coffee Beans:</strong> Jual biji kopi pilihan (roasted beans) untuk kamu seduh sendiri di rumah.</li>
            <li><strong>Loyalty Program:</strong> Program spesial Beli 10 Gratis 1 untuk para member setia kami.</li>
            <li><strong>Private Event:</strong> Sediakan tempat untuk acara komunitas atau pertemuan kecil.</li>
        </ul>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>