<?php 
include 'header.php'; 
?>
<title>Produk Pre-Order - Classic Coffee 789</title>

<div class="menu-page-container">
    <h1>Produk Pre-Order</h1>
    <p style="text-align: center; margin-top: -20px; margin-bottom: 40px;">Produk berikut memerlukan pemesanan di awal. Hubungi kami untuk detail harga dan ketersediaan.</p>
    <div class="product-grid">
        <?php
        // Ambil semua data produk dengan kategori 'pre-order'
        $result = $conn->query("SELECT * FROM products WHERE category = 'pre-order' ORDER BY name ASC");
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '  <img src="images/' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                echo '  <div class="card-content">';
                echo '      <h4>' . htmlspecialchars($row['name']) . '</h4>';

                // ================== PERUBAHAN LOGIKA HARGA ==================
                echo '      <p class="price" style="color: #e74c3c; font-size: 1.5em;">Pre-Order</p>';
                // ================== AKHIR PERUBAHAN ==================

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
    echo '
    <div style="text-align: center; padding: 60px 20px; background-color: #fff; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
        <i class="fas fa-box-open" style="font-size: 64px; color: #d1d5db; margin-bottom: 25px;"></i>
        <h3 style="font-size: 1.5em; color: #4b5563; margin-bottom: 10px;">Belum Ada Produk Pre-Order</h3>
        <p style="color: #6b7280; font-size: 1.1em; max-width: 500px; margin: 0 auto 25px;">Saat ini belum ada produk yang tersedia untuk pre-order. Nantikan pembaruan dari kami untuk menu spesial berikutnya!</p>
        <a href="index.php" class="btn" style="display: inline-block; width: auto; background-color: #8B4513; color: white; text-decoration: none; padding: 12px 25px;">
            <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Kembali ke Beranda
        </a>
    </div>
    ';
}
// ================== AKHIR BLOK PENGGANTI ==================
?>
    </div>
</div>

<?php include 'footer.php'; ?>