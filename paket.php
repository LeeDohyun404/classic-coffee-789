<?php 
include 'header.php'; 
$kategori = $_GET['kategori'] ?? 'kopi'; 
?>
<title>Menu Paket - Classic Coffee 789</title>
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
            <h1>Paket Kopi Original</h1>
            <div class="product-grid">
                <?php
                $paket_kopi_desc = [
                    'Paket Hemat Kopi' => 'Pilihan Kopi (Original/Ella/Carla/Gula Aren) + Burger Mini.',
                    'Paket Kenyang Kopi' => 'Pilihan Kopi (Original/Ella/Carla/Gula Aren) + Spaghetti.',
                    'Paket Sharing Kopi' => 'Pilihan Kopi (Original/Ella/Carla/Gula Aren) + Dimsum.',
                    'Paket Sultan Kopi' => 'Pilihan Kopi (Original/Ella/Carla/Gula Aren) + Pizza.',
                    'Paket Santai Kopi' => 'Pilihan Kopi (Original/Ella/Carla/Gula Aren) + Risol Ayam Suwir.'
                ];
                $result = $conn->query("SELECT * FROM products WHERE name LIKE 'Paket%Kopi'");
                while($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '  <img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '  <h4>' . htmlspecialchars($row['name']) . '</h4>';
                    echo '  <p class="description">' . ($paket_kopi_desc[$row['name']] ?? '') . '</p>';
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
    <?php elseif ($kategori == 'teh'): ?>
        <div class="menu-section">
            <h1>Paket Thai Tea</h1>
            <div class="product-grid">
                <?php
                $paket_teh_desc = [
                    'Paket Sultan Thai Tea' => 'Pilihan Thai Tea (Original/Milk/Lemon/Lychee) + Pizza.',
                    'Paket Sharing Thai Tea' => 'Pilihan Thai Tea (Original/Milk/Lemon/Lychee) + Dimsum.',
                    'Paket Kenyang Thai Tea' => 'Pilihan Thai Tea (Original/Milk/Lemon/Lychee) + Spaghetti.',
                    'Paket Santai Thai Tea' => 'Pilihan Thai Tea (Original/Milk/Lemon/Lychee) + Risol Ayam Suwir.',
                    'Paket Hemat Thai Tea' => 'Pilihan Thai Tea (Original/Milk/Lemon/Lychee) + Burger Mini.'
                ];
                $result = $conn->query("SELECT * FROM products WHERE name LIKE 'Paket%Thai Tea'");
                while($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '  <img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '  <h4>' . htmlspecialchars($row['name']) . '</h4>';
                    echo '  <p class="description">' . ($paket_teh_desc[$row['name']] ?? '') . '</p>';
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