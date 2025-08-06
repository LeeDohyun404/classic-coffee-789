<?php 
include 'header.php';
$kategori = $_GET['kategori'] ?? 'kopi';

$db_category = ($kategori == 'kopi') ? 'paket-kopi' : 'paket-teh';
$page_title = ($kategori == 'kopi') ? 'Paket Kopi' : 'Paket Thai Tea';
?>
<title>Menu <?php echo $page_title; ?> - Classic Coffee 789</title>
<style>
    /* Tambahan CSS untuk Tampilan Diskon */
    .product-card { position: relative; overflow: hidden; }
    .discount-badge { position: absolute; top: 15px; right: -30px; background-color: #e74c3c; color: white; padding: 5px 30px; font-weight: bold; transform: rotate(45deg); font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    .price-container { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .original-price { text-decoration: line-through; color: #999; font-size: 1em; }
    .discounted-price { color: #e74c3c; font-weight: bold; font-size: 1.3em; }
</style>

<div class="menu-page-container">
    <h1><?php echo $page_title; ?></h1>
    <div class="product-grid">
        <?php
        $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY name ASC");
        $stmt->bind_param("s", $db_category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="product-card">';

                if ($row['discount_percentage'] > 0) {
                    echo '<div class="discount-badge">' . $row['discount_percentage'] . '% OFF</div>';
                }

                echo '  <img src="images/' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                echo '  <h4>' . htmlspecialchars($row['name']) . '</h4>';
                
                if ($row['discount_percentage'] > 0) {
                    $original_price = $row['price'];
                    $discount_amount = ($original_price * $row['discount_percentage']) / 100;
                    $discounted_price = $original_price - $discount_amount;
                    
                    echo '<div class="price-container">';
                    echo '  <span class="original-price">Rp ' . number_format($original_price, 0, ',', '.') . '</span>';
                    echo '  <span class="discounted-price">Rp ' . number_format($discounted_price, 0, ',', '.') . '</span>';
                    echo '</div>';
                } else {
                    echo '  <p class="price">Rp ' . number_format($row['price'], 0, ',', '.') . '</p>';
                }
                
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
            echo '<p>Menu paket ini belum tersedia.</p>';
        }
        ?>
    </div>
</div>
<?php include 'footer.php'; ?>