<?php 
include 'header.php'; 

// Ambil slide aktif dari database
$slides_result = $conn->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort_order ASC");
$slides = [];
if ($slides_result && $slides_result->num_rows > 0) {
    while ($row = $slides_result->fetch_assoc()) {
        $slides[] = $row;
    }
}
?>
<title>Beranda - Classic Coffee 789</title>
<style>
    * { box-sizing: border-box; }
    
    .home-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px 0 20px;
    }

    /* Hero Slider */
    .hero-slider { 
        position: relative; 
        width: 100%; 
        height: 60vh; 
        min-height: 400px; 
        border-radius: 20px; 
        overflow: hidden; 
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        margin-bottom: 60px;
        /* Brick pattern background */
        background-color: #e8e0d1;
        background-image:
            repeating-linear-gradient(90deg, #bfa07a 0 40px, transparent 40px 80px),
            repeating-linear-gradient(180deg, #bfa07a 0 20px, transparent 20px 40px),
            repeating-linear-gradient(90deg, #a67c52 0 42px, transparent 42px 82px),
            repeating-linear-gradient(180deg, #a67c52 0 22px, transparent 22px 42px);
        background-size: 80px 40px, 80px 40px, 82px 42px, 82px 42px;
        background-position: 0 0, 0 20px, 41px 21px, 41px 41px;
    }
    
    .slide { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        opacity: 0; 
        transition: opacity 1s ease;
        transform: scale(1.02);
    }
    
    .slide.active { 
        opacity: 1; 
        transform: scale(1);
    }
    
    .slide-image { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
        filter: brightness(0.8);
    }
    
    .slide-overlay { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        background: linear-gradient(135deg, rgba(90, 58, 34, 0.7) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(90, 58, 34, 0.6) 100%); 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        text-align: center; 
        color: white; 
        padding: 40px;
    }
    
    .slide-text { 
        max-width: 800px; 
        animation: slideInUp 0.8s ease-out;
    }
    
    .slide-text h1 { 
        font-size: 3.5em; 
        margin-bottom: 20px; 
        font-family: 'Playfair Display', serif; 
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        line-height: 1.2;
    }
    
    .slide-text p { 
        font-size: 1.4em; 
        line-height: 1.6;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        font-weight: 300;
    }
    
    /* Slider Navigation */
    .slider-nav { 
        position: absolute; 
        bottom: 30px; 
        left: 50%; 
        transform: translateX(-50%); 
        display: flex; 
        gap: 12px; 
        z-index: 3;
        background: rgba(0,0,0,0.2);
        padding: 10px 20px;
        border-radius: 25px;
        backdrop-filter: blur(10px);
    }
    
    .slider-dot { 
        width: 14px; 
        height: 14px; 
        border-radius: 50%; 
        background-color: rgba(255,255,255,0.4); 
        cursor: pointer; 
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .slider-dot:hover {
        background-color: rgba(255,255,255,0.7);
        transform: scale(1.2);
    }
    
    .slider-dot.active { 
        background-color: white; 
        border-color: rgba(90, 58, 34, 0.8);
        transform: scale(1.3);
    }

    /* Slider Arrows */
    .slider-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, rgba(90, 58, 34, 0.8) 0%, rgba(0, 0, 0, 0.6) 100%);
        border: none;
        width: 55px;
        height: 55px;
        border-radius: 50%;
        color: white;
        font-size: 20px;
        cursor: pointer;
        z-index: 3;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .slider-arrow:hover {
        background: linear-gradient(135deg, rgba(90, 58, 34, 1) 0%, rgba(0, 0, 0, 0.8) 100%);
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    
    .slider-arrow.prev { left: 20px; }
    .slider-arrow.next { right: 20px; }

    /* Featured Products */
    .featured-products { 
        margin: 80px 0; 
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s ease;
    }
    
    .featured-products.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    .featured-products h2 {
        text-align: center;
        font-size: 2.5em;
        color: #5a3a22;
        font-family: 'Playfair Display', serif;
        margin-bottom: 50px;
        position: relative;
        font-weight: 700;
    }
    
    .featured-products h2::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(135deg, #5a3a22 0%, #8b6f47 100%);
        border-radius: 2px;
    }

    /* Product Grid */
    .product-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
        gap: 30px; 
    }
    
    .product-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border: 1px solid rgba(90, 58, 34, 0.1);
        opacity: 0;
        transform: translateY(30px);
    }
    
    .product-card.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    .product-card:hover { 
        transform: translateY(-8px) scale(1.02); 
        box-shadow: 0 15px 30px rgba(0,0,0,0.12); 
    }
    
    .product-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transition: transform 0.4s ease;
        border-radius: 15px 15px 0 0;
    }
    
    .product-card:hover img { 
        transform: scale(1.05); 
    }
    
    .product-card .card-content {
        padding: 25px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .product-card h4 { 
        color: #5a3a22; 
        font-family: 'Playfair Display', serif; 
        font-size: 1.4em; 
        min-height: 50px;
        margin: 0 0 15px 0;
        font-weight: 600;
        line-height: 1.3;
    }
    
    .add-to-cart-form { 
        padding: 20px 25px 25px; 
        margin-top: auto;
    }

    /* Discount Badge */
    .discount-badge { 
        position: absolute; 
        top: 20px; 
        right: -35px; 
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white; 
        padding: 8px 40px; 
        font-weight: bold; 
        transform: rotate(45deg); 
        font-size: 14px; 
        z-index: 2;
        box-shadow: 0 2px 10px rgba(231, 76, 60, 0.3);
    }
    
    /* Price Display */
    .price-container { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        margin-top: 10px;
        flex-wrap: wrap;
    }
    
    .original-price { 
        text-decoration: line-through; 
        color: #999; 
        font-size: 1.1em; 
    }
    
    .discounted-price { 
        color: #e74c3c; 
        font-weight: bold; 
        font-size: 1.4em; 
    }
    
    .price {
        color: #5a3a22;
        font-weight: bold;
        font-size: 1.4em;
        margin: 10px 0;
    }

    /* Quantity Controls */
    .quantity-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 10px;
    }
    
    .quantity-btn {
        background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .quantity-btn:hover { 
        background: linear-gradient(135deg, #4a2f1d 0%, #3a251a 100%);
        transform: scale(1.1);
    }
    
    .quantity-input {
        width: 60px;
        text-align: center;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        padding: 8px;
        font-size: 16px;
        font-weight: bold;
        color: #5a3a22;
        background: white;
    }

    /* Add to Cart Button */
    .btn-buy {
        background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%);
        color: white;
        border: none;
        padding: 15px 25px;
        border-radius: 12px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }
    
    .btn-buy::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn-buy:hover::before {
        left: 100%;
    }
    
    .btn-buy:hover {
        background: linear-gradient(135deg, #4a2f1d 0%, #3a251a 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(90, 58, 34, 0.3);
    }

    /* Loading Animation */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Enhanced Mobile Responsive */
    @media (max-width: 1024px) {
        .home-container { padding: 30px 15px 0 15px; }
        .hero-slider { height: 50vh; min-height: 350px; }
        .slide-text h1 { font-size: 2.8em; }
        .slide-text p { font-size: 1.2em; }
        .product-grid { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
    }

    @media (max-width: 768px) {
        .home-container { padding: 20px 15px 0 15px; }
        .hero-slider { 
            height: 45vh; 
            min-height: 320px; 
            margin-bottom: 40px; 
            border-radius: 15px;
        }
        .slide-overlay { padding: 30px 20px; }
        .slide-text h1 { 
            font-size: 2.2em; 
            margin-bottom: 15px;
            line-height: 1.1;
        }
        .slide-text p { 
            font-size: 1.1em; 
            line-height: 1.4;
        }
        .slider-arrow { 
            width: 45px; 
            height: 45px; 
            font-size: 16px; 
        }
        .slider-arrow.prev { left: 15px; }
        .slider-arrow.next { right: 15px; }
        .slider-nav { 
            bottom: 20px; 
            padding: 8px 15px; 
        }
        .slider-dot { 
            width: 12px; 
            height: 12px; 
        }
        .featured-products { margin: 50px 0; }
        .featured-products h2 { 
            font-size: 2em; 
            margin-bottom: 35px;
        }
        .product-grid { 
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 20px; 
        }
        .product-card { border-radius: 12px; }
        .product-card img { 
            height: 220px; 
            border-radius: 12px 12px 0 0;
        }
        .product-card .card-content { padding: 20px; }
        .add-to-cart-form { padding: 15px 20px 20px; }
        .quantity-container { 
            gap: 12px; 
            padding: 8px; 
            border-radius: 8px;
        }
        .quantity-btn { 
            width: 35px; 
            height: 35px; 
            font-size: 16px; 
        }
        .quantity-input { 
            width: 50px; 
            font-size: 14px; 
            padding: 6px;
        }
        .btn-buy { 
            padding: 12px 20px; 
            font-size: 14px; 
            border-radius: 8px;
        }
    }

    @media (max-width: 480px) {
        .home-container { padding: 15px 10px 0 10px; }
        .hero-slider { 
            height: 40vh; 
            min-height: 280px; 
            border-radius: 12px;
        }
        .slide-overlay { padding: 20px 15px; }
        .slide-text h1 { 
            font-size: 1.8em; 
            margin-bottom: 12px;
        }
        .slide-text p { 
            font-size: 1em; 
            line-height: 1.3;
        }
        .slider-arrow { 
            width: 40px; 
            height: 40px; 
            font-size: 14px; 
        }
        .slider-arrow.prev { left: 10px; }
        .slider-arrow.next { right: 10px; }
        .slider-nav { 
            bottom: 15px; 
            padding: 6px 12px; 
        }
        .slider-dot { 
            width: 10px; 
            height: 10px; 
        }
        .featured-products { margin: 40px 0; }
        .featured-products h2 { 
            font-size: 1.8em; 
            margin-bottom: 25px;
        }
        .product-grid { 
            grid-template-columns: 1fr; 
            gap: 15px; 
        }
        .product-card { border-radius: 10px; }
        .product-card img { 
            height: 200px; 
            border-radius: 10px 10px 0 0;
        }
        .product-card h4 { 
            font-size: 1.2em; 
            min-height: 40px; 
            margin-bottom: 10px;
        }
        .product-card .card-content { padding: 15px; }
        .add-to-cart-form { padding: 12px 15px 15px; }
        .quantity-container { 
            gap: 10px; 
            padding: 6px; 
        }
        .quantity-btn { 
            width: 30px; 
            height: 30px; 
            font-size: 14px; 
        }
        .quantity-input { 
            width: 45px; 
            font-size: 12px; 
            padding: 4px;
        }
        .btn-buy { 
            padding: 10px 15px; 
            font-size: 12px; 
            border-radius: 6px;
        }
        .discount-badge { 
            font-size: 11px; 
            padding: 6px 30px; 
            right: -25px;
        }
    }

    @media (max-width: 360px) {
        .home-container { padding: 10px 8px 0 8px; }
        .hero-slider { 
            height: 35vh; 
            min-height: 250px; 
        }
        .slide-text h1 { font-size: 1.5em; }
        .slide-text p { font-size: 0.9em; }
        .featured-products h2 { font-size: 1.6em; }
        .product-card h4 { font-size: 1.1em; }
    }

    /* Touch Device Optimizations */
    @media (hover: none) and (pointer: coarse) {
        .product-card:hover { 
            transform: none; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.08); 
        }
        .product-card:active { 
            transform: scale(0.98); 
            transition: transform 0.1s ease;
        }
        .slider-arrow:hover { 
            transform: translateY(-50%); 
            background: linear-gradient(135deg, rgba(90, 58, 34, 0.8) 0%, rgba(0, 0, 0, 0.6) 100%);
        }
        .btn-buy:hover { 
            transform: none; 
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%);
        }
        .btn-buy:active { 
            transform: scale(0.95); 
            transition: transform 0.1s ease;
        }
    }
</style>

<div class="home-container" style="position:relative;">
    <!-- Bee Animation di luar slider -->
    <svg class="bee-fly-slider" width="48" height="48" viewBox="0 0 32 32" style="position:absolute;top:40px;left:calc(100% - 60px);z-index:10;pointer-events:none;animation:beeFlySlider 6s linear infinite;">
        <ellipse cx="16" cy="18" rx="7" ry="5" fill="#FFD700" stroke="#8B4513" stroke-width="1.5"/>
        <rect x="13" y="15" width="2" height="6" fill="#8B4513"/>
        <rect x="17" y="15" width="2" height="6" fill="#8B4513"/>
        <ellipse cx="13" cy="13" rx="3" ry="2" fill="#e0f7fa" stroke="#8B4513" stroke-width="0.7"/>
        <ellipse cx="19" cy="13" rx="3" ry="2" fill="#e0f7fa" stroke="#8B4513" stroke-width="0.7"/>
        <circle cx="16" cy="18" r="1.2" fill="#fff"/>
        <ellipse cx="14.5" cy="17.5" rx="0.5" ry="0.7" fill="#333"/>
        <ellipse cx="17.5" cy="17.5" rx="0.5" ry="0.7" fill="#333"/>
        <path d="M15.5 19.5 Q16 20 16.5 19.5" stroke="#333" stroke-width="0.5" fill="none"/>
    </svg>
    <style>
    @keyframes beeFlySlider {
        0% { opacity:0; transform: translateX(0) translateY(0) scale(0.8) rotate(-10deg); }
        10% { opacity:1; }
        20% { opacity:1; transform: translateX(-30vw) translateY(10px) scale(1) rotate(-5deg); }
        35% { opacity:1; transform: translateX(-45vw) translateY(0) scale(1.1) rotate(0deg); }
        45% { opacity:1; transform: translateX(-50vw) translateY(-10px) scale(1.1) rotate(5deg); }
        55% { opacity:1; transform: translateX(-45vw) translateY(0) scale(1.1) rotate(0deg); }
        65% { opacity:1; transform: translateX(-30vw) translateY(10px) scale(1) rotate(-5deg); }
        80% { opacity:1; }
        90% { opacity:0; }
        100% { opacity:0; transform: translateX(0) translateY(0) scale(0.8) rotate(-10deg); }
    }
    .bee-fly-slider {
        filter: drop-shadow(0 2px 6px rgba(90,58,34,0.12));
    }
    </style>
    <section class="hero-slider">
        <?php foreach ($slides as $index => $slide): ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="images/<?php echo htmlspecialchars($slide['image_url']); ?>" class="slide-image" alt="Slide Image">
                <div class="slide-overlay">
                    <div class="slide-text">
                        <h1><?php echo htmlspecialchars($slide['title']); ?></h1>
                       <p><?php echo nl2br(htmlspecialchars($slide['subtitle'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <button class="slider-arrow prev" id="prevSlide">&#10094;</button>
        <button class="slider-arrow next" id="nextSlide">&#10095;</button>
        
        <div class="slider-nav">
            <?php foreach ($slides as $index => $slide): ?>
                <div class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="featured-products">
        <h2>Produk Unggulan Kami</h2>
        <div class="product-grid">
            <?php
            $result_unggulan = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 3");
            
            if ($result_unggulan && $result_unggulan->num_rows > 0) {
                while($row = $result_unggulan->fetch_assoc()) {
                    echo '<div class="product-card">';
                    if (!empty($row['discount_percentage']) && $row['discount_percentage'] > 0) {
                        echo '<div class="discount-badge">' . $row['discount_percentage'] . '% OFF</div>';
                    }
                    echo '  <img src="images/' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '  <div class="card-content">';
                    echo '      <h4>' . htmlspecialchars($row['name']) . '</h4>';
                    if (!empty($row['discount_percentage']) && $row['discount_percentage'] > 0) {
                        $original_price = $row['price'];
                        $discounted_price = $original_price - (($original_price * $row['discount_percentage']) / 100);
                        echo '  <div class="price-container">';
                        echo '      <span class="original-price">Rp ' . number_format($original_price, 0, ',', '.') . '</span>';
                        echo '      <span class="discounted-price">Rp ' . number_format($discounted_price, 0, ',', '.') . '</span>';
                        echo '  </div>';
                    } else {
                        echo '  <p class="price">Rp ' . number_format($row['price'], 0, ',', '.') . '</p>';
                    }
                    echo '  </div>';
                    echo '  <form action="tambah_keranjang.php" method="POST" class="add-to-cart-form">';
                    echo '      <div class="quantity-container">';
                    echo '          <button type="button" class="quantity-btn" onclick="changeQuantity(this, -1)">-</button>';
                    echo '          <input type="number" name="quantity" value="1" min="1" class="quantity-input" readonly>';
                    echo '          <button type="button" class="quantity-btn" onclick="changeQuantity(this, 1)">+</button>';
                    echo '      </div>';
                    echo '      <input type="hidden" name="product_id" value="' . $row['id'] . '">';
                    echo '      <button type="submit" class="btn-buy">Tambah ke Keranjang</button>';
                    echo '  </form>';
                    echo '</div>';
                }
            } else {
                echo '<p>Produk unggulan belum tersedia.</p>';
            }
            ?>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== SLIDER FUNCTIONALITY =====
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.getElementById('prevSlide');
    const nextBtn = document.getElementById('nextSlide');
    const slider = document.querySelector('.hero-slider');
    
    if (slides.length <= 1) {
        if(prevBtn) prevBtn.style.display = 'none';
        if(nextBtn) nextBtn.style.display = 'none';
        const sliderNav = document.querySelector('.slider-nav');
        if(sliderNav) sliderNav.style.display = 'none';
        return; 
    }

    let currentSlide = 0;
    let slideInterval;
    let touchStartX = 0;
    let touchEndX = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if(dots[i]) dots[i].classList.remove('active');
        });

        slides[index].classList.add('active');
        if(dots[index]) dots[index].classList.add('active');
        currentSlide = index;
    }

    function nextSlide() {
        let newIndex = (currentSlide + 1) % slides.length;
        showSlide(newIndex);
    }
    
    function prevSlide() {
        let newIndex = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(newIndex);
    }

    function startSlider() {
        slideInterval = setInterval(nextSlide, 5000); 
    }

    function stopSlider() {
        clearInterval(slideInterval);
    }

    function restartSlider() {
        stopSlider();
        setTimeout(startSlider, 2000);
    }

    // Button Event Listeners
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            restartSlider();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            restartSlider();
        });
    }

    // Dot Navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            restartSlider();
        });
    });

    // Touch/Swipe Support for Mobile
    if (slider) {
        slider.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
            stopSlider();
        });

        slider.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
            restartSlider();
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide(); // Swipe left - next slide
                } else {
                    prevSlide(); // Swipe right - previous slide
                }
            }
        }
    }

    // Pause slider when tab is not visible
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopSlider();
        } else {
            startSlider();
        }
    });

    // Start the slider
    startSlider();

    // ===== SCROLL ANIMATIONS =====
    
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const featuredSection = document.querySelector('.featured-products');
    if (featuredSection) {
        observer.observe(featuredSection);
    }

    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
        card.style.transitionDelay = `${index * 0.1}s`;
        observer.observe(card);
    });

    // ===== QUANTITY CONTROL =====
    window.changeQuantity = function(button, change) {
        const quantityInput = button.parentElement.querySelector('.quantity-input');
        let currentValue = parseInt(quantityInput.value);
        let newValue = currentValue + change;
        
        if (newValue >= 1) {
            quantityInput.value = newValue;
            
            // Add visual feedback
            quantityInput.style.transform = 'scale(1.1)';
            setTimeout(() => {
                quantityInput.style.transform = 'scale(1)';
            }, 150);
        }
    };

    // ===== ENHANCED ADD TO CART =====
    const addToCartForms = document.querySelectorAll('.add-to-cart-form');
    
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.btn-buy');
            const originalText = submitBtn.textContent;
            
            // Add loading state
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Menambahkan...';
            submitBtn.disabled = true;
            
            // Simulate processing
            setTimeout(() => {
                // Success animation
                submitBtn.innerHTML = '✓ Berhasil Ditambahkan!';
                submitBtn.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
                
                // Reset after 2 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.style.background = '';
                    submitBtn.disabled = false;
                }, 2000);
                
                // Actually submit the form
                this.submit();
            }, 800);
        });
    });

    // ===== SMOOTH SCROLLING =====
    document.documentElement.style.scrollBehavior = 'smooth';

    // ===== MOBILE OPTIMIZATIONS =====
    
    // Handle orientation change
    window.addEventListener('orientationchange', () => {
        setTimeout(() => {
            // Recalculate slider dimensions if needed
            const currentHeight = slider.offsetHeight;
            slides.forEach(slide => {
                slide.style.height = currentHeight + 'px';
            });
        }, 100);
    });

    // ===== PERFORMANCE OPTIMIZATIONS =====
    
    // Preload next slide image
    function preloadNextImage() {
        const nextIndex = (currentSlide + 1) % slides.length;
        const nextSlide = slides[nextIndex];
        const nextImg = nextSlide.querySelector('.slide-image');
        
        if (nextImg && !nextImg.complete) {
            const preloadImg = new Image();
            preloadImg.src = nextImg.src;
        }
    }

    // Preload images periodically
    setInterval(preloadNextImage, 3000);

    console.log('Enhanced responsive homepage initialized successfully');
});
</script>

<?php 
include 'footer.php'; 
?>