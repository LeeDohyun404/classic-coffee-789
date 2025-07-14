<?php 
session_start();
require_once 'config.php'; 

// Cek apakah user sudah login untuk loyalty program
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    // Hitung total drinks untuk loyalty program
    $sql_drinks = "SELECT SUM(oi.quantity) as total_drinks FROM order_items oi JOIN orders o ON oi.order_id = o.id JOIN products p ON oi.product_id = p.id WHERE o.user_id = ? AND p.category = 'minuman'";
    $stmt = $conn->prepare($sql_drinks);
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $total_drinks = $data['total_drinks'] ?? 0;
        $drinks_to_go = 10 - ($total_drinks % 10);
        if ($drinks_to_go == 10 && $total_drinks > 0) {
            $drinks_to_go = 10;
        }
    } else {
        $total_drinks = 0;
        $drinks_to_go = 10;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Kami - Classic Coffee 789</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<style>
    /* ================================== */
    /* FONT & BODY                        */
    /* ================================== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        margin: 0;
        color: #333;
        background-color: #f4f7f6;
    }

    /* ================================== */
    /* HEADER & NAVIGASI                  */
    /* ================================== */
    header {
        background: #8B4513;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #654321;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        position: relative;
        z-index: 999;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: white;
        font-size: 1.5em;
        font-weight: bold;
        font-family: 'Playfair Display', serif;
    }

    .logo img {
        height: 35px;
    }

    /* Mobile Menu Toggle */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 5px;
    }

    nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
    }

    nav ul li {
        margin-left: 35px;
    }

    nav ul li > a {
        text-decoration: none;
        color: #fff;
        font-weight: 500;
        padding: 20px 0;
        display: block;
    }

    /* Dropdown Menu Produk */
    nav ul li.dropdown-li {
        position: relative;
    }

    nav ul li.dropdown-li .dropdown-menu {
        display: none;
        position: absolute;
        top: 90%;
        left: -20px;
        background-color: white;
        list-style: none;
        padding: 10px;
        margin: 0;
        width: 220px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-radius: 10px;
        z-index: 1000;
    }

    nav ul li.dropdown-li:hover .dropdown-menu {
        display: block;
    }

    nav ul li .dropdown-menu li {
        margin: 0;
        width: 100%;
    }

    nav ul li .dropdown-menu li a {
        padding: 12px 20px;
        display: block;
        color: #555;
        border-radius: 6px;
    }

    nav ul li .dropdown-menu li a:hover {
        background-color: #f5f5f5;
        color: #a0522d;
    }

    nav ul li a i.fa-caret-down {
        margin-left: 5px;
    }

    /* Tombol Login/Logout di Header */
    .nav-login-item a.nav-login-btn,
    .nav-logout-item a.nav-logout-btn {
        color: white;
        padding: 10px 22px;
        border-radius: 20px;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .nav-login-item a.nav-login-btn { 
        background-color: #39cc0c; 
    }

    .nav-logout-item a.nav-logout-btn { 
        background-color: #dc3545; 
    }

    .nav-login-item a.nav-login-btn:hover { 
        background-color: #5a3a22; 
        color: white; 
    }

    .nav-logout-item a.nav-logout-btn:hover { 
        background-color: #c82333; 
        color: white; 
    }

    /* Bar Loyalitas */
    .loyalty-bar {
        background: #fff7ed;
        padding: 12px 20px;
        border-bottom: 1px solid #e0d5c6;
    }

    .loyalty-bar-content {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .welcome-text { 
        font-size: 16px; 
        font-weight: 500; 
        color: #444; 
    }

    .welcome-text strong { 
        color: #8B4513; 
    }

    .loyalty-display { 
        display: flex; 
        align-items: center; 
        gap: 10px; 
        font-size: 15px; 
        flex-wrap: wrap;
    }

    .loyalty-text { 
        font-size: 14px; 
        color: #666; 
        font-style: italic; 
    }

    .coffee-beans .bean { 
        font-size: 1.3em; 
    }

    .coffee-beans .bean.used { 
        text-decoration: line-through; 
        opacity: 0.5; 
    }

    /* ================================== */
    /* MAIN CONTENT                       */
    /* ================================== */
    main {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .service-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 40px;
        margin: 20px 0;
    }

    .service-container h1 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5em;
        color: #5a3a22;
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 3px solid #a0522d;
        padding-bottom: 15px;
    }

    .service-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .service-item {
        background: #f8f9fa;
        margin: 20px 0;
        padding: 25px;
        border-radius: 10px;
        border-left: 5px solid #8B4513;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .service-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .service-item strong {
        color: #8B4513;
        font-size: 1.2em;
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
    }

    .service-item p {
        color: #666;
        line-height: 1.6;
        margin: 0;
    }

    /* ================================== */
    /* FOOTER                             */
    /* ================================== */
    footer {
        background-color: #333;
        color: #f4f4f4;
        padding: 40px 20px;
        margin-top: 50px;
    }

    .footer-content {
        display: flex;
        justify-content: center;
        text-align: center;
        max-width: 1100px;
        margin: auto;
    }

    .footer-section h3 {
        color: #FFE4B5;
        font-family: 'Playfair Display', serif;
        margin-bottom: 15px;
    }

    .footer-section .contact span {
        display: block;
        margin-bottom: 8px;
    }

    .footer-section .socials a {
        color: #f4f4f4;
        margin: 0 10px;
        font-size: 1.5em;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-section .socials a:hover {
        color: #8B4513;
    }

    .footer-bottom {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #555;
        margin-top: 20px;
    }

    /* ================================== */
    /* RESPONSIVE DESIGN - MOBILE         */
    /* ================================== */
    @media (max-width: 768px) {
        /* Header Mobile */
        header {
            padding: 10px 15px;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 1.3em;
        }

        .logo img {
            height: 30px;
        }

        .mobile-menu-toggle {
            display: block;
        }

        nav {
            width: 100%;
            order: 3;
        }

        nav ul {
            flex-direction: column;
            width: 100%;
            background: #8B4513;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            display: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        nav ul.show {
            display: flex;
        }

        nav ul li {
            margin: 0;
            width: 100%;
            border-bottom: 1px solid #A0522D;
        }

        nav ul li > a {
            padding: 15px 20px;
            border-bottom: none;
        }

        nav ul li.dropdown-li .dropdown-menu {
            position: static;
            display: block;
            width: 100%;
            box-shadow: none;
            background: #A0522D;
            border-radius: 0;
            padding: 0;
        }

        nav ul li.dropdown-li .dropdown-menu li a {
            padding: 10px 40px;
            color: white;
        }

        nav ul li.dropdown-li .dropdown-menu li a:hover {
            background: #8B4513;
        }

        /* Loyalty Bar Mobile */
        .loyalty-bar {
            padding: 10px 15px;
        }

        .loyalty-bar-content {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .loyalty-display {
            justify-content: center;
        }

        .welcome-text {
            font-size: 14px;
        }

        .loyalty-text {
            font-size: 12px;
        }

        /* Main Content Mobile */
        main {
            padding: 20px 15px;
        }

        .service-container {
            padding: 25px;
        }

        .service-container h1 {
            font-size: 2em;
        }

        .service-item {
            padding: 20px;
        }

        .service-item strong {
            font-size: 1.1em;
        }
    }

    @media (max-width: 480px) {
        /* Extra Small Mobile */
        .logo {
            font-size: 1.1em;
        }

        .service-container h1 {
            font-size: 1.5em;
        }

        .loyalty-display {
            flex-direction: column;
            gap: 5px;
        }

        .coffee-beans .bean {
            font-size: 1.1em;
        }
    }
</style>

<script>
// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('nav ul');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('show');
        });
    }
});
</script>

<header>
    <div class="logo">
        <img src="<?php echo BASE_URL; ?>/images/logo.png" alt="Classic Coffee 789 Logo">
        <span>Classic Coffee 789</span>
    </div>
    
    <button class="mobile-menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <nav>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>/index.php">Beranda</a></li>
            <li class="dropdown-li">
                <a href="#">Produk <i class="fas fa-caret-down"></i></a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo BASE_URL; ?>/pilihan_paket.php">Paket</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/makanan.php">Makanan</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pilihan_minuman.php">Minuman</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/voucher_saya.php">Voucher Saya</a></li>
                </ul>
            </li>
            <li><a href="<?php echo BASE_URL; ?>/keranjang.php">Keranjang (<?php echo count($_SESSION['cart'] ?? []); ?>)</a></li>
            <li><a href="<?php echo BASE_URL; ?>/about.php">About</a></li>
            <li><a href="<?php echo BASE_URL; ?>/service.php" class="active">Service</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-logout-item">
                    <a href="<?php echo BASE_URL; ?>/logout.php" class="nav-logout-btn">Logout</a>
                </li>
            <?php else: ?>
                <li class="nav-login-item">
                    <a href="<?php echo BASE_URL; ?>/login.php" class="nav-login-btn">Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="loyalty-bar">
        <div class="loyalty-bar-content">
            <span class="welcome-text">Selamat Datang, <strong><?php echo htmlspecialchars(ucfirst($username)); ?>!</strong></span>
            <div class="loyalty-display">
                <span>Program Loyalitas: </span>
                <div class="coffee-beans">
                    <?php
                        $used_beans = $total_drinks % 10;
                        for ($i = 0; $i < 10; $i++) { 
                            echo '<span class="bean ' . ($i < $used_beans ? 'used' : '') . '">☕</span>'; 
                        }
                    ?>
                </div>
                <span class="loyalty-text">
                    <?php if ($drinks_to_go == 10 && $total_drinks > 0): ?>
                        (Beli <?php echo $drinks_to_go; ?> minuman lagi untuk 1 gratis)
                    <?php else: ?>
                        (Beli <?php echo $drinks_to_go; ?> minuman lagi untuk 1 gratis)
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
<?php endif; ?>

<main>
    <div class="service-container">
        <h1>Layanan Kami</h1>
        <ul class="service-list">
            <li class="service-item">
                <strong><i class="fas fa-utensils"></i> Dine-in</strong>
                <p>Nikmati suasana kedai kami yang nyaman dengan Wi-Fi gratis dan tempat duduk yang cozy untuk bersantai atau bekerja.</p>
            </li>
            <li class="service-item">
                <strong><i class="fas fa-shopping-bag"></i> Takeaway</strong>
                <p>Pesan kopi favorit Anda untuk dinikmati di mana saja. Praktis dan cepat untuk aktivitas Anda yang padat.</p>
            </li>
            <li class="service-item">
                <strong><i class="fas fa-seedling"></i> Coffee Beans</strong>
                <p>Jual biji kopi pilihan (roasted beans) berkualitas tinggi untuk Anda seduh sendiri di rumah dengan cita rasa yang autentik.</p>
            </li>
            <li class="service-item">
                <strong><i class="fas fa-gift"></i> Loyalty Program</strong>
                <p>Program spesial Beli 10 Gratis 1 untuk para member setia kami. Setiap pembelian minuman akan dihitung untuk mendapatkan voucher gratis.</p>
            </li>
            <li class="service-item">
                <strong><i class="fas fa-calendar-alt"></i> Private Event</strong>
                <p>Sediakan tempat untuk acara komunitas atau pertemuan kecil dengan suasana yang hangat dan pelayanan yang ramah.</p>
            </li>
        </ul>
    </div>
</main>

<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>Classic Coffee 789</h3>
            <div class="contact">
                <span><i class="fas fa-map-marker-alt"></i> Desa Kebanaran, Tamanwinangun RT 03/ RW 08 No.59</span>
                <span><i class="fas fa-phone"></i> +62 896-6950-5208</span>
                <span><i class="fas fa-envelope"></i> classiccoffee789.com | Designed by Firman Gandhi ☕</span>
            </div>
            <div class="socials">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 Classic Coffee 789. All rights reserved.</p>
    </div>
</footer>

</body>
</html>