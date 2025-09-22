<?php
// Koneksi database harus dipanggil di sini agar semua halaman bisa menggunakannya
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classic Coffee 789</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </head>
<body>
<style>
.profile-avatar{width:32px;height:32px;border-radius:50%;margin-right:8px;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;color:white;background:linear-gradient(135deg,#8B4513,#D2691E);border:2px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,0.1);vertical-align:middle}.profile-avatar img{width:100%;height:100%;border-radius:50%;object-fit:cover}
.user-dropdown{display:flex;align-items:center;padding:8px 12px;border-radius:20px;background:rgba(255,255,255,0.1);transition:all .3s ease}
.user-dropdown:hover{background:rgba(255,255,255,0.2)}
.username-text{max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-right:5px}
@media (max-width:768px){.username-text{max-width:80px}.profile-avatar{width:28px;height:28px;font-size:12px}}
    /* ================================== */
    /* FONT & BODY                        */
    /* ================================== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

    .logo a {
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
    /* KONTEN UTAMA & HALAMAN MENU        */
    /* ================================== */
    .container { 
        display: flex; 
    }

    main { 
        flex: 1; 
        padding: 30px 20px; 
    }

    .home-container, .menu-page-container { 
        max-width: 1200px; 
        margin: 30px auto; 
        padding: 0 20px; 
    }

    .hero-section {
        background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('../images/bg.jpg');
        height: 50vh; 
        background-position: center; 
        background-size: cover; 
        border-radius: 15px;
        display: flex; 
        justify-content: center; 
        align-items: center; 
        text-align: center; 
        color: white;
        min-height: 300px;
        position: relative;
        z-index: 1;
    }

    .hero-text h1 { 
        color: white; 
        font-family: 'Playfair Display', serif; 
        font-size: 3em; 
        margin-bottom: 20px;
    }

    .hero-text p { 
        font-size: 1.5em; 
    }

    .featured-products, .menu-section { 
        padding: 50px 0; 
    }

    .featured-products h2, .menu-section h2, .menu-page-container h1 {
        text-align: center; 
        font-family: 'Playfair Display', serif;
        font-size: 2.5em; 
        color: #5a3a22; 
        margin-bottom: 40px;
    }

    .menu-section h2 { 
        text-align: left; 
        border-bottom: 3px solid #a0522d; 
        padding-bottom: 10px; 
    }

/* Grid & Kartu Produk */
    .product-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 30px; 
    }

    .product-card { 
        background-color: #fff; 
        border-radius: 12px; 
        box-shadow: 0 6px 20px rgba(0,0,0,0.08); 
        display: flex; 
        flex-direction: column; 
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 10px 25px rgba(0,0,0,0.12); 
    }

    .product-card img { 
        width: 100%; 
        height: 200px; 
        object-fit: cover; 
        border-radius: 12px 12px 0 0;
    }

    .product-card h4 { 
        margin: 15px 10px 5px 10px; 
        color: #5a3a22; 
        font-family: 'Playfair Display', serif; 
        font-size: 1.3em; 
        min-height: 44px; 
    }

    .product-card .description { 
        font-size: 0.9em; 
        color: #666; 
        line-height: 1.5; 
        padding: 0 15px; 
        flex-grow: 1; 
        min-height: 60px; 
    }

    .product-card .price { 
        font-size: 1.3em; 
        font-weight: bold; 
        color: #333; 
        margin: 15px 10px; 
    }

 /* Tombol Jumlah (+/-) */
    .add-to-cart-form { 
        display: flex; 
        flex-direction: column; 
        gap: 12px; 
        padding: 0 15px 20px 15px; 
        margin-top: auto; 
    }

    .quantity-container { 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }

    .quantity-btn {
        background-color: #8B4513;
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
        transition: background-color 0.2s ease;
    }

    .quantity-btn:hover {
        background-color: #A0522D;
    }

    .quantity-container .quantity-btn:first-child { 
        border-radius: 8px 0 0 8px; 
    }

    .quantity-container .quantity-btn:last-child { 
        border-radius: 0 8px 8px 0; 
    }

    .quantity-input { 
        width: 60px; 
        height: 40px; 
        text-align: center; 
        border: 1px solid #ccc; 
        border-left: none; 
        border-right: none; 
        font-size: 16px; 
        font-weight: 600; 
    }

    .quantity-input::-webkit-outer-spin-button, 
    .quantity-input::-webkit-inner-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }

    .add-to-cart-form .btn-buy { 
        border-radius: 8px; 
        padding: 12px; 
        font-size: 1em; 
        background: #28a745; 
        color: white; 
        border: none; 
        cursor: pointer; 
        font-weight: 600; 
        transition: background-color 0.3s ease;
    }

    .add-to-cart-form .btn-buy:hover {
        background: #218838;
    }

    /* ================================== */
    /* HALAMAN LOGIN/REGISTRASI           */
    /* ================================== */
    .login-container { 
        width: 100%; 
        max-width: 450px; 
        background: white; 
        border-radius: 15px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        overflow: hidden; 
        margin: 20px auto;
    }

    .login-section { 
        padding: 30px; 
    }

    .login-section h2 { 
        color: #5a3a22; 
        margin-bottom: 20px; 
        font-size: 1.5em; 
        text-align: center; 
    }

    .form-group { 
        margin-bottom: 20px; 
    }

    .form-group label { 
        display: block; 
        margin-bottom: 8px; 
        font-weight: 500; 
    }

    .form-group input { 
        width: 100%; 
        padding: 12px 15px; 
        border: 1px solid #e0e0e0; 
        border-radius: 8px; 
        font-size: 14px; 
    }

    .btn { 
        padding: 12px 20px; 
        border: none; 
        background: #8B4513; 
        color: white; 
        cursor: pointer; 
        border-radius: 8px; 
        text-align: center; 
        text-decoration: none; 
        display: block; 
        width: 100%; 
        font-weight: bold; 
        font-size: 16px; 
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background: #A0522D;
    }

    .back-link { 
        text-align: center; 
        margin-top: 20px; 
        padding-top: 20px; 
        border-top: 1px solid #eee; 
    }

    .back-link a { 
        color: #5a3a22; 
        text-decoration: none; 
        font-weight: 500; 
    }

    /* ================================== */
    /* HALAMAN KERANJANG BELANJA          */
    /* ================================== */
    .cart-page-container { 
        max-width: 900px; 
        margin: 40px auto; 
        padding: 20px; 
    }

    .cart-page-container h1 { 
        text-align: center; 
        color: #5a3a22; 
        margin-bottom: 30px; 
    }

    .cart-table { 
        width: 100%; 
        border-collapse: collapse; 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 5px 25px rgba(0,0,0,0.08); 
        overflow: hidden; 
    }

    .cart-table th { 
        background-color: #f8f9fa; 
        padding: 18px 20px; 
        text-align: left; 
        text-transform: uppercase; 
        color: #666; 
    }

    .cart-table td { 
        padding: 20px; 
        border-bottom: 1px solid #f0f0f0; 
        vertical-align: middle; 
    }

    .product-info { 
        display: flex; 
        align-items: center; 
        gap: 20px; 
    }

    .product-info img { 
        width: 70px; 
        height: 70px; 
        object-fit: cover; 
        border-radius: 8px; 
    }

    .product-info .product-name { 
        font-weight: 600; 
    }

    .checkout-container { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-top: 30px; 
        flex-wrap: wrap;
        gap: 15px;
    }

    .checkout-container .btn {
        width: auto;
        padding: 12px 25px;
        font-size: 15px;
    }

    .btn-secondary { 
        background-color: #6c757d; 
        color: white; 
    }

    .btn-secondary:hover {
        background-color: #5a6268;
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

        .logo a {
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

        .home-container, .menu-page-container {
            padding: 0 10px;
        }

        .hero-section {
            height: 40vh;
            min-height: 250px;
            border-radius: 10px;
        }

        .hero-text h1 {
            font-size: 2em;
        }

        .hero-text p {
            font-size: 1.2em;
        }

        .featured-products h2, .menu-section h2, .menu-page-container h1 {
            font-size: 2em;
        }

        /* Product Grid Mobile */
        .product-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-card img {
            height: 180px;
        }

        /* Cart Table Mobile */
        .cart-table {
            font-size: 14px;
        }

        .cart-table th,
        .cart-table td {
            padding: 10px 8px;
        }

        .product-info {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }

        .product-info img {
            width: 60px;
            height: 60px;
        }

        .checkout-container {
            flex-direction: column;
            gap: 15px;
        }

        .checkout-container .btn {
            width: 100%;
        }

        /* Form Mobile */
        .login-container {
            margin: 20px 15px;
        }

        .login-section {
            padding: 20px;
        }

        /* Quantity Controls Mobile */
        .quantity-container {
            flex-wrap: wrap;
        }

        .quantity-btn {
            width: 35px;
            height: 35px;
            font-size: 18px;
        }

        .quantity-input {
            width: 50px;
            height: 35px;
        }
    }
    /* Find Store + Coffee Anim */
    a.find-store-btn {
        margin-left: 16px;
        margin-right: 10px;
        background: #fff;
        color: #8B4513;
        border-radius: 16px;
        padding: 6px 14px;
        font-weight: 600;
        text-decoration: none;
        font-size: 0.95em;
        box-shadow: 0 2px 8px rgba(139,69,19,0.08);
        border: 1px solid #fff;
        transition: background 0.2s, color 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    a.find-store-btn:hover { background: #f6eee6; }

    .header-coffee-anim {
        display: inline-flex;
        align-items: center;
        vertical-align: middle;
        position: relative;
        width: 42px;
        height: 34px;
        margin-right: 8px;
    }
     /* Bee Animation */
    .bee-anim {
        animation: beeFly 4.5s linear infinite;
    }
    @keyframes beeFly {
        0% { opacity:0; transform: translateX(60px) translateY(-10px) scale(0.7) rotate(-10deg); }
        10% { opacity:1; }
        20% { opacity:1; transform: translateX(20px) translateY(0) scale(1) rotate(-5deg); }
        35% { opacity:1; transform: translateX(0px) translateY(0) scale(1.1) rotate(0deg); }
        45% { opacity:1; transform: translateX(-5px) translateY(-2px) scale(1.1) rotate(5deg); }
        55% { opacity:1; transform: translateX(0px) translateY(0) scale(1.1) rotate(0deg); }
        65% { opacity:1; transform: translateX(20px) translateY(0) scale(1) rotate(-5deg); }
        80% { opacity:1; }
        90% { opacity:0; }
        100% { opacity:0; transform: translateX(60px) translateY(-10px) scale(0.7) rotate(-10deg); }
    }

    /* Responsive overrides for the find store + anim (header scope) */
    @media (max-width: 768px) {
        a.find-store-btn {
            padding: 5px 12px;
            font-size: 0.9em;
            border-radius: 14px;
            margin-left: 10px;
        }
        .header-coffee-anim {
            width: 32px;
            height: 26px;
        }
    }
    @media (max-width: 480px) {
        a.find-store-btn {
            padding: 4px 10px;
            font-size: 0.85em;
            border-radius: 12px;
            margin-left: 8px;
        }
        .header-coffee-anim { display: none; }
    }
    @media (max-width: 480px) {
        /* Extra Small Mobile */
        .logo a {
            font-size: 1.1em;
        }

        .hero-text h1 {
            font-size: 1.5em;
        }

        .hero-text p {
            font-size: 1em;
        }

        .product-grid {
            grid-template-columns: 1fr;
        }

        .featured-products h2, .menu-section h2, .menu-page-container h1 {
            font-size: 1.5em;
        }

        .loyalty-display {
            flex-direction: column;
            gap: 5px;
        }

        .coffee-beans .bean {
            font-size: 1.1em;
        }

        /* Hide some table columns on very small screens */
        .cart-table .hide-mobile {
            display: none;
        }
            /* Styling untuk Bintang Rating */
        .product-rating {
            padding: 0 15px 10px;
            font-size: 14px;
            color: #666;
        }
        }
</style>
<header>
    <div class="logo">
        <a href="<?php echo BASE_URL; ?>/index.php">
            <img src="<?php echo BASE_URL; ?>/images/logo.png" alt="Classic Coffee 789 Logo">
            <span>Classic Coffee 789</span>
        </a>
    </div>
    <a href="https://maps.app.goo.gl/XycEFrMubEgNMFPt5" target="_blank" class="find-store-btn">
        <i class="fas fa-map-marker-alt"></i> Find a store
    <span class="header-coffee-anim">
        <!-- Bee Animation -->
        <svg class="bee-anim" width="24" height="24" viewBox="0 0 32 32" style="position:absolute;left:0;top:0;z-index:2;pointer-events:none;animation:beeFly 4.5s linear infinite;">
            <!-- Bee body -->
            <ellipse cx="16" cy="18" rx="7" ry="5" fill="#FFD700" stroke="#8B4513" stroke-width="1.5"/>
            <!-- Bee stripes -->
            <rect x="13" y="15" width="2" height="6" fill="#8B4513"/>
            <rect x="17" y="15" width="2" height="6" fill="#8B4513"/>
            <!-- Bee wings -->
            <ellipse cx="13" cy="13" rx="3" ry="2" fill="#e0f7fa" stroke="#8B4513" stroke-width="0.7"/>
            <ellipse cx="19" cy="13" rx="3" ry="2" fill="#e0f7fa" stroke="#8B4513" stroke-width="0.7"/>
            <!-- Bee face -->
            <circle cx="16" cy="18" r="1.2" fill="#fff"/>
            <ellipse cx="14.5" cy="17.5" rx="0.5" ry="0.7" fill="#333"/>
            <ellipse cx="17.5" cy="17.5" rx="0.5" ry="0.7" fill="#333"/>
            <path d="M15.5 19.5 Q16 20 16.5 19.5" stroke="#333" stroke-width="0.5" fill="none"/>
        </svg>
        <svg width="30" height="36" viewBox="0 0 38 44" fill="none" xmlns="http://www.w3.org/2000/svg" style="overflow:visible;z-index:1;">
            <ellipse cx="19" cy="34" rx="15" ry="7" fill="#fff" stroke="#8B4513" stroke-width="2"/>
            <rect x="4" y="14" width="30" height="20" rx="10" fill="#fff" stroke="#8B4513" stroke-width="2"/>
            <ellipse cx="19" cy="14" rx="15" ry="7" fill="#fff" stroke="#8B4513" stroke-width="2"/>
            <ellipse cx="19" cy="14" rx="12" ry="5" fill="#8B4513"/>
            <path d="M33 24 Q38 28 33 34" stroke="#8B4513" stroke-width="3" fill="none"/>
            <g class="coffee-steam-header">
                <path d="M13 7 Q15 2 19 7 Q23 12 25 7" stroke="#bca37f" stroke-width="2" fill="none"/>
                <path d="M20 2 Q22 -2 26 2 Q30 6 32 2" stroke="#bca37f" stroke-width="1.5" fill="none"/>
            </g>
        </svg>
    </span>
     </a>
    <button class="mobile-menu-toggle"><i class="fas fa-bars"></i></button>
    <nav>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>/index.php">Beranda</a></li>
            <li class="dropdown-li">
                <a href="#">Produk <i class="fas fa-caret-down"></i></a>

                 <ul class="dropdown-menu">
                <li><a href="<?php echo BASE_URL; ?>/pilihan_paket.php">Paket</a></li>
                <li><a href="<?php echo BASE_URL; ?>/makanan.php">Makanan</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pilihan_minuman.php">Minuman</a></li>
                <li><a href="<?php echo BASE_URL; ?>/preorder.php">Produk Pre-Order</a></li>
                </ul>
            </li>
            <li><a href="<?php echo BASE_URL; ?>/keranjang.php">Keranjang (<?php echo count($_SESSION['cart'] ?? []); ?>)</a></li>
            <li><a href="<?php echo BASE_URL; ?>/about.php">About</a></li>
            <li><a href="<?php echo BASE_URL; ?>/service.php">Service</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                // Logika untuk menampilkan foto profil/inisial (tidak berubah)
                $user_id_for_pic = $_SESSION['user_id'];
                $stmt_pic = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
                $stmt_pic->bind_param("i", $user_id_for_pic);
                $stmt_pic->execute();
                $user_data = $stmt_pic->get_result()->fetch_assoc();
                $profile_pic_path = $user_data['profile_picture'] ?? '';
                $has_profile_pic = !empty($profile_pic_path) && file_exists('uploads/profiles/' . $profile_pic_path);
                $username = $_SESSION['username'];
                $initials = strtoupper(substr($username, 0, 2)); // Disederhanakan
                ?>
                <li class="dropdown-li">
                    <a href="#" class="user-dropdown">
                        <div class="profile-avatar">
                            <?php if ($has_profile_pic): ?>
                                <img src="<?php echo BASE_URL; ?>/uploads/profiles/<?php echo $profile_pic_path; ?>" alt="profil">
                            <?php else: ?>
                                <?php echo $initials; ?>
                            <?php endif; ?>
                        </div>
                        <span class="username-text"><?php echo htmlspecialchars($username); ?></span>
                        <i class="fas fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo BASE_URL; ?>/profil.php">Profil Saya</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/voucher_saya.php">Voucher Saya</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/logout.php">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li class="nav-login-item"><a href="<?php echo BASE_URL; ?>/login.php" class="nav-login-btn">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<?php if (isset($_SESSION['user_id'])):
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    // =====================================================================
    // ============= PERBAIKAN UTAMA LOGIKA POIN LOYALITAS =================
    // =====================================================================
    
    // 1. Hitung SEMUA minuman dari pesanan yang sudah lunas (paid/free)
    $sql_total_drinks = "
        SELECT SUM(oi.quantity) as total
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ? AND o.status IN ('paid', 'free') AND p.category LIKE 'minuman%'
    ";
    $stmt_total = $conn->prepare($sql_total_drinks);
    $stmt_total->bind_param("i", $user_id);
    $stmt_total->execute();
    $total_drinks_purchased = $stmt_total->get_result()->fetch_assoc()['total'] ?? 0;

    // 2. Hitung berapa banyak VOUCHER GRATIS yang telah digunakan
    $sql_vouchers = "
        SELECT COUNT(id) as total
        FROM orders
        WHERE user_id = ? AND voucher_discount > 0 AND status IN ('paid', 'free')
    ";
    $stmt_vouchers = $conn->prepare($sql_vouchers);
    $stmt_vouchers->bind_param("i", $user_id);
    $stmt_vouchers->execute();
    $vouchers_used_count = $stmt_vouchers->get_result()->fetch_assoc()['total'] ?? 0;

    // 3. Poin loyalitas yang valid adalah total minuman dikurangi voucher yang dipakai
    $net_drinks_for_loyalty = $total_drinks_purchased - $vouchers_used_count;
    if ($net_drinks_for_loyalty < 0) {
        $net_drinks_for_loyalty = 0; // Pastikan tidak minus
    }

    $drinks_to_go = 10 - ($net_drinks_for_loyalty % 10);

    // =====================================================================
    // ======================= AKHIR PERBAIKAN LOGIKA ======================
    // =====================================================================
?>
<div class="loyalty-bar">
    <div class="loyalty-bar-content">
        <span class="welcome-text">Selamat Datang, <strong><?php echo htmlspecialchars(ucfirst($username)); ?>!</strong></span>
        <div class="loyalty-display">
            <span>Program Loyalitas: </span>
            <div class="coffee-beans">
                <?php
                $used_beans = $net_drinks_for_loyalty % 10;
                for ($i = 0; $i < 10; $i++) {
                    echo '<span class="bean ' . ($i < $used_beans ? 'used' : '') . '">☕</span>';
                }
                ?>
            </div>
            <span class="loyalty-text">(Beli <?php echo $drinks_to_go; ?> minuman lagi untuk 1 gratis)</span>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Script untuk mobile menu (tidak berubah)
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