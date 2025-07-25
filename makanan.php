<?php 
include 'header.php'; 
?>
<title>Menu Makanan - Classic Coffee 789</title>

<div class="menu-page-container">
    <h1>Menu Makanan</h1>
    <div class="product-grid">
        <?php
        $result = $conn->query("SELECT * FROM products WHERE category = 'makanan' ORDER BY name ASC");
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="product-card">';
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
            echo '<p style="text-align: center; width: 100%;">Menu makanan belum tersedia.</p>';
        }
        ?>
    </div>
</div>

<?php 
include 'footer.php'; 
?> 