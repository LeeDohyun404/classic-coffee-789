<?php include 'header.php'; ?>
<title>Menu Makanan - Classic Coffee 789</title>
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
    <h1>Menu Makanan</h1>

    <div class="menu-section">
        <h2>Burger</h2>
        <div class="product-grid">
            <?php
            $result = $conn->query("SELECT * FROM products WHERE name LIKE '%Burger%'");
            while($row = $result->fetch_assoc()) {
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

    <div class="menu-section">
        <h2>Aneka Makanan</h2>
        <div class="product-grid">
            <?php
            $result = $conn->query("SELECT * FROM products WHERE name IN ('Dimsum', 'Spaghetti Bolognese', 'Mango Sticky Rice')");
            while($row = $result->fetch_assoc()) {
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
</div>

<?php include 'footer.php'; ?>
</body>
</html>