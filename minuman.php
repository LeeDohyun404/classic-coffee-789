<?php 
include 'header.php';
$kategori = $_GET['kategori'] ?? 'kopi'; // default ke 'kopi'

// Tentukan kategori database dan judul halaman berdasarkan parameter
$db_category = ($kategori == 'kopi') ? 'minuman-kopi' : 'minuman-nonkopi';
$page_title = ($kategori == 'kopi') ? 'Coffee' : 'Non-Coffee';
?>
<title>Menu Minuman <?php echo $page_title; ?> - Classic Coffee 789</title>

<div class="menu-page-container">
    <h1>Menu Minuman - <?php echo $page_title; ?></h1>
    <div class="product-grid">
        <?php
        $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY name ASC");
        $stmt->bind_param("s", $db_category);
        $stmt->execute();
        $result = $stmt->get_result();
        
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
            echo '<p style="text-align: center; width: 100%;">Menu minuman ini belum tersedia.</p>';
        }
        ?>
    </div>
</div>

<?php 
include 'footer.php'; 
?>