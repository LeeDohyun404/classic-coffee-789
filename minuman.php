<?php 
include 'header.php'; 
$kategori = $_GET['kategori'] ?? 'kopi'; 
?>
<title>Menu Minuman - Classic Coffee 789</title>
<style>
    /* CSS KHUSUS UNTUK MENGHILANGKAN PANAH INPUT JUMLAH */
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    .quantity-input[type=number] {
      -moz-appearance: textfield;
    }
</style>

<div class="menu-page-container">
    
    <?php if ($kategori == 'kopi'): ?>
        <div class="menu-section">
            <h1>Menu Minuman - Coffee</h1>
            <div class="product-grid">
                <?php
                $sql_coffee = "SELECT * FROM products WHERE name IN ('Sakura Latte (Ralat)', 'Lychee Latte (Lyla)', 'Caramel Latte (Carla)', 'Kopi Susu Original', 'Kopi Susu Gula Aren', 'Hazelnut Latte (Ella)') ORDER BY FIELD(name, 'Sakura Latte (Ralat)', 'Lychee Latte (Lyla)', 'Caramel Latte (Carla)', 'Kopi Susu Original', 'Kopi Susu Gula Aren', 'Hazelnut Latte (Ella)')";
                $result_coffee = $conn->query($sql_coffee);
                while($row = $result_coffee->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '  <img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '  <h4>' . htmlspecialchars($row['name']) . '</h4>';
                    echo '  <p class="price">Rp ' . number_format($row['price'], 0, ',', '.') . '</p>';
                    echo '  <form action="tambah_keranjang.php" method="POST" class="add-to-cart-form">';
                    echo '      <div class="quantity-container">';
                    echo '          <button type="button" class="quantity-btn" onclick="changeQuantity(this, -1)">-</button>';
                    echo '          <input type="number" name="quantity" value="1" min="1" class="quantity-input">';
                    echo '          <button type="button" class="quantity-btn" onclick="changeQuantity(this, 1)">+</button>';
                    echo '      </div>';
                    echo '      <input type="hidden" name="product_id" value="' . $row['id'] . '">';
                    echo '      <button type="submit" class="btn-buy">Tambah ke Keranjang</button>';
                    echo '  </form>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    <?php elseif ($kategori == 'nonkopi'): ?>
        <div class="menu-section">
            <h1>Menu Minuman - Non-Coffee</h1>
            <div class="product-grid">
                <?php
                $sql_non_coffee = "SELECT * FROM products WHERE name IN ('Milk Base Mangga', 'Milk Base Red Velvet', 'Thai Tea Original', 'Thai Tea Lychee', 'Thai Tea Milk', 'Thai Tea Lemon') ORDER BY FIELD(name, 'Milk Base Mangga', 'Milk Base Red Velvet', 'Thai Tea Original', 'Thai Tea Lychee', 'Thai Tea Milk', 'Thai Tea Lemon')";
                $result_non_coffee = $conn->query($sql_non_coffee);
                while($row = $result_non_coffee->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '  <img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '  <h4>' . htmlspecialchars($row['name']) . '</h4>';
                    echo '  <p class="price">Rp ' . number_format($row['price'], 0, ',', '.') . '</p>';
                    echo '  <form action="tambah_keranjang.php" method="POST" class="add-to-cart-form">';
                    echo '      <div class="quantity-container">';
                    echo '          <button type="button" class="quantity-btn" onclick="changeQuantity(this, -1)">-</button>';
                    echo '          <input type="number" name="quantity" value="1" min="1" class="quantity-input">';
                    echo '          <button type="button" class="quantity-btn" onclick="changeQuantity(this, 1)">+</button>';
                    echo '      </div>';
                    echo '      <input type="hidden" name="product_id" value="' . $row['id'] . '">';
                    echo '      <button type="submit" class="btn-buy">Tambah ke Keranjang</button>';
                    echo '  </form>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>