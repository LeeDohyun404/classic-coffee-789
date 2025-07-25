<?php 
include 'header.php'; 
?>
<title>Beranda - Classic Coffee 789</title>

<div class="home-container">
    <section class="hero-section">
        <div class="hero-text">
            <h1>☕ Selamat Datang di Classic Coffee 789</h1>
            <p>Temukan secangkir kebahagiaan Anda di sini.</p>
        </div>
    </section>

    <section class="featured-products">
        <h2>Produk Unggulan Kami</h2>
        <div class="product-grid">
            <?php
            // Mengambil 3 produk secara acak yang bukan minuman
            $result_unggulan = $conn->query("SELECT * FROM products WHERE category NOT LIKE '%minuman%' ORDER BY RAND() LIMIT 3");
            
            if ($result_unggulan && $result_unggulan->num_rows > 0) {
                while($row = $result_unggulan->fetch_assoc()) {
                    echo '<div class="product-card">';
                    
                    // PERBAIKAN UTAMA ADA DI BARIS DI BAWAH INI
                    echo '  <img src="images/' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    
                    echo '  <h4>' . htmlspecialchars($row['name']) . '</h4>';
                    echo '  <p class="price">Rp ' . number_format($row['price'], 0, ',', '.') . '</p>';
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

<?php 
include 'footer.php'; 
?>