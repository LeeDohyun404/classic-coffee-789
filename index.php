<?php 
include 'header.php'; 

// Ambil semua slide yang aktif dari database
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
    /* CSS SLIDER & PRODUK UNGGULAN */
    .hero-slider { 
        position: relative; width: 100%; height: 50vh; 
        min-height: 350px; border-radius: 15px; 
        overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
    }
    .slide { 
        position: absolute; top: 0; left: 0; 
        width: 100%; height: 100%; opacity: 0; 
        transition: opacity 1s ease-in-out; 
    }
    .slide.active { opacity: 1; }
    .slide-image { width: 100%; height: 100%; object-fit: cover; }
    .slide-overlay { 
        position: absolute; top: 0; left: 0; 
        width: 100%; height: 100%; 
        background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)); 
        display: flex; justify-content: center; align-items: center; 
        text-align: center; color: white; padding: 20px;
    }
    .slide-text h1 { font-size: 3em; margin-bottom: 20px; font-family: 'Playfair Display', serif; }
    .slide-text p { font-size: 1.5em; }
    .slider-nav { 
        position: absolute; bottom: 20px; left: 50%; 
        transform: translateX(-50%); display: flex; 
        gap: 10px; z-index: 2; 
    }
    .slider-dot { 
        width: 12px; height: 12px; border-radius: 50%; 
        background-color: rgba(255,255,255,0.5); 
        cursor: pointer; transition: background-color 0.3s ease; 
    }
    .slider-dot.active { background-color: white; }

    /* CSS FINAL untuk Tombol Panah Slider */
    .slider-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(0, 0, 0, 0.3); 
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        color: white;
        font-size: 24px;
        cursor: pointer;
        z-index: 2;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .slider-arrow:hover {
        background-color: rgba(0, 0, 0, 0.6);
    }
    .slider-arrow.prev { left: 15px; }
    .slider-arrow.next { right: 15px; }

    /* Grid & Kartu Produk */
    .product-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 30px; 
        align-items: stretch;
    }
    .product-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .product-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 10px 25px rgba(0,0,0,0.12); 
    }
    .product-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 12px 12px 0 0;
    }
    .product-card .card-content {
        padding: 15px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .product-card h4 { 
        color: #5a3a22; 
        font-family: 'Playfair Display', serif; 
        font-size: 1.3em; 
        min-height: 44px;
        margin: 0;
    }
    .product-card .price-container, .product-card .price { 
        margin-top: auto;
        padding-top: 10px;
    }
    .add-to-cart-form { 
        padding: 15px; 
        margin-top: auto;
    }

    /* CSS untuk Tampilan Diskon */
    .discount-badge { 
        position: absolute; top: 15px; right: -30px; 
        background-color: #e74c3c; color: white; 
        padding: 5px 30px; font-weight: bold; 
        transform: rotate(45deg); font-size: 14px; 
    }
    .price-container { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .original-price { text-decoration: line-through; color: #999; font-size: 1em; }
    .discounted-price { color: #e74c3c; font-weight: bold; font-size: 1.3em; }

    /* CSS Tampilan Mobile */
    @media (max-width: 768px) {
        .slide-text h1 { font-size: 2em; }
        .slide-text p { font-size: 1.1em; }
    }
</style>

<div class="home-container">
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
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.getElementById('prevSlide');
    const nextBtn = document.getElementById('nextSlide');
    
    if (slides.length <= 1) {
        if(prevBtn) prevBtn.style.display = 'none';
        if(nextBtn) nextBtn.style.display = 'none';
        const sliderNav = document.querySelector('.slider-nav');
        if(sliderNav) sliderNav.style.display = 'none';
        return; 
    }

    let currentSlide = 0;
    let slideInterval;

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

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            stopSlider();
            nextSlide();
            startSlider();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            stopSlider();
            prevSlide();
            startSlider();
        });
    }

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            stopSlider();
            showSlide(parseInt(dot.dataset.slide));
            startSlider();
        });
    });
    
    startSlider();
});
</script>

<?php 
include 'footer.php'; 
?>