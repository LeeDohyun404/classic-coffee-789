<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
require_once '../config.php';

// Ambil semua produk yang aktif untuk modal
$products_query = "SELECT id, name, price, image_url, category, discount_percentage, discount_methods FROM products WHERE category != 'arsip' ORDER BY category, name ASC";
$products_result = $conn->query($products_query);
$all_products = [];
$categories = [];
if ($products_result) {
    while ($row = $products_result->fetch_assoc()) {
        $all_products[] = $row;
        // Kumpulkan kategori unik untuk tombol filter
        if (!in_array($row['category'], $categories)) {
            $categories[] = $row['category'];
        }
    }
}
sort($categories);
$products_json = json_encode($all_products);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pesanan Manual - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1, h2 { color: #5a3a22; margin-bottom: 25px; }
        h1 { text-align: center; font-size: 2em; }
        h2 { font-size: 1.5em; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;}
        .form-section { margin-bottom: 30px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 1rem; transition: all 0.2s ease-in-out; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #5a3a22; box-shadow: 0 0 0 3px rgba(90, 58, 34, 0.1); }
        .btn { padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; font-weight: 600; transition: all 0.3s ease; }
        .btn-primary { background-color: #28a745; color: white; }
        .btn-primary:hover { background-color: #218838; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-danger { background-color: #e74c3c; color: white; }
        .order-items-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .order-items-table th, .order-items-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .order-items-table th { background-color: #f8f9fa; font-weight: 600; }
        .order-items-table img { width: 50px; height: 50px; border-radius: 5px; object-fit: cover; }
        .quantity-input { width: 70px; text-align: center; padding: 8px; border-radius: 5px; border: 1px solid #ccc;}
        #total-summary { margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 10px; text-align: right; font-size: 1.3em; font-weight: bold; color: #5a3a22; }

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 800px; border-radius: 10px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 15px; }
        .modal-header h2 { margin: 0; }
        .close-btn { color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close-btn:hover { color: #000; }
        
        /* BARU: Filter Kategori */
        #category-filters { margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 8px; }
        .filter-btn { background-color: #f1f1f1; border: 1px solid #ddd; padding: 8px 15px; border-radius: 20px; cursor: pointer; transition: all 0.2s ease; }
        .filter-btn:hover { background-color: #e0e0e0; }
        .filter-btn.active { background-color: #5a3a22; color: white; border-color: #5a3a22; }

        #modal-search { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .product-grid-modal { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; max-height: 50vh; overflow-y: auto; padding-top: 5px; }
        .product-card { border: 1px solid #ddd; border-radius: 8px; padding: 10px; text-align: center; cursor: pointer; transition: all 0.2s ease; }
        .product-card:hover { border-color: #5a3a22; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transform: translateY(-3px); }
        .product-card img { width: 100%; height: 100px; object-fit: cover; border-radius: 5px; margin-bottom: 10px; }
        .product-card .name { font-weight: 600; font-size: 0.9em; }
        .product-card .price { color: #28a745; font-weight: bold; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-plus-circle"></i> Tambah Pesanan Manual</h1>
        <form action="proses_tambah_pesanan.php" method="POST" id="orderForm">
            <div class="form-section">
                <h2><i class="fas fa-user"></i> Detail Pelanggan</h2>
                <div class="form-grid">
                    <div class="form-group"><label for="customer_name">Nama Pelanggan</label><input type="text" name="customer_name" required></div>
                    <div class="form-group"><label for="customer_phone">No. HP</label><input type="tel" name="customer_phone" required></div>
                    <div class="form-group"><label for="customer_email">Email (Opsional)</label><input type="email" name="customer_email"></div>
                </div>
            </div>

            <div class="form-section">
                <table class="order-items-table">
                    <thead><tr><th>Produk</th><th width="100px">Jumlah</th><th width="150px" style="text-align:right;">Subtotal</th><th width="50px">Aksi</th></tr></thead>
                    <tbody id="items-container"></tbody>
                </table>
                <button type="button" style="margin-top:15px;" class="btn btn-primary" onclick="openProductModal()"><i class="fas fa-plus"></i> Tambah Produk</button>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-info-circle"></i> Detail Pesanan</h2>
                <div class="form-grid">
                    <div class="form-group"><label for="order_date">Tanggal & Waktu Pesan</label><input type="text" id="order_date" name="order_date" required></div>
                    <div class="form-group"><label for="delivery_method">Metode Pengambilan</label><select id="delivery_method" name="delivery_method" onchange="toggleAddressField()"><option value="pickup">Ambil Ditempat</option><option value="delivery">Ongkir</option><option value="cod">COD</option></select></div>
                    <div class="form-group"><label for="payment_choice">Metode Pembayaran</label><select name="payment_choice"><option value="cash">Cash</option><option value="qris">QRIS</option></select></div>
                </div>
                <div class="form-group" id="address-container" style="display:none;"><label for="guest_address">Alamat Pengiriman/COD</label><textarea name="guest_address" rows="3"></textarea></div>
                <div class="form-group"><label for="order_notes">Catatan Pesanan (Opsional)</label><textarea name="order_notes" rows="3"></textarea></div>
            </div>

            <div id="total-summary">Total Belanja: Rp 0</div>
            <div style="display:flex; justify-content:flex-end; gap:15px; margin-top:30px;">
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pesanan</button>
            </div>
        </form>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Pilih Produk</h2>
                <span class="close-btn" onclick="closeProductModal()">&times;</span>
            </div>
            <div id="category-filters">
                <button class="filter-btn active" onclick="filterByCategory('all')">Semua</button>
                <?php foreach($categories as $category): ?>
                    <button class="filter-btn" onclick="filterByCategory('<?php echo $category; ?>')"><?php echo str_replace('-', ' ', $category); ?></button>
                <?php endforeach; ?>
            </div>
            <input type="text" id="modal-search" onkeyup="filterProducts()" placeholder="Cari produk...">
            <div class="product-grid-modal" id="product-grid-modal"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const productsData = <?php echo $products_json; ?>;
        
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#order_date", { enableTime: true, dateFormat: "Y-m-d H:i", defaultDate: new Date() });
        });

        function openProductModal() {
            const grid = document.getElementById('product-grid-modal');
            grid.innerHTML = '';
            productsData.forEach(p => {
                grid.innerHTML += `
                    <div class="product-card" data-category="${p.category}" onclick="addProductToOrder(${p.id})">
                        <img src="../images/${p.image_url}" alt="${p.name}">
                        <div class="name">${p.name}</div>
                        <div class="price">Rp ${parseFloat(p.price).toLocaleString('id-ID')}</div>
                    </div>
                `;
            });
            document.getElementById('productModal').style.display = 'block';
            document.getElementById('modal-search').focus();
        }

        function closeProductModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        // BARU: Fungsi untuk filter berdasarkan tombol kategori
        function filterByCategory(category) {
            document.querySelectorAll('#category-filters .filter-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            filterProducts(); // Panggil filter utama yang akan menangani gabungan search dan kategori
        }

        // DIMODIFIKASI: Fungsi filter utama yang menggabungkan search dan kategori
        function filterProducts() {
            const searchInput = document.getElementById('modal-search');
            const filterText = searchInput.value.toLowerCase();
            const activeCategoryBtn = document.querySelector('#category-filters .filter-btn.active');
            const activeCategory = activeCategoryBtn.getAttribute('onclick').match(/'([^']+)'/)[1];
            
            const cards = document.querySelectorAll('#product-grid-modal .product-card');
            cards.forEach(card => {
                const name = card.querySelector('.name').textContent.toLowerCase();
                const category = card.getAttribute('data-category');

                const nameMatch = name.includes(filterText);
                const categoryMatch = (activeCategory === 'all' || category === activeCategory);

                card.style.display = (nameMatch && categoryMatch) ? "" : "none";
            });
        }

        function addProductToOrder(productId) {
            const container = document.getElementById('items-container');
            const existingRow = document.getElementById(`row-product-${productId}`);
            if (existingRow) {
                const quantityInput = existingRow.querySelector('.quantity-input');
                quantityInput.value = parseInt(quantityInput.value) + 1;
            } else {
                const product = productsData.find(p => p.id == productId);
                const newRow = document.createElement('tr');
                newRow.id = `row-product-${productId}`;
                newRow.innerHTML = `
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <img src="../images/${product.image_url}" alt="${product.name}">
                            <span>${product.name}</span>
                            <input type="hidden" name="product_ids[]" value="${product.id}">
                        </div>
                    </td>
                    <td><input type="number" name="quantities[]" value="1" min="1" class="quantity-input" oninput="calculateTotal()"></td>
                    <td class="price-display" style="text-align:right;">-</td>
                    <td><button type="button" class="btn btn-danger" onclick="removeItemRow(${productId})"><i class="fas fa-trash"></i></button></td>
                `;
                container.appendChild(newRow);
            }
            calculateTotal();
            closeProductModal();
        }

        function removeItemRow(productId) {
            document.getElementById(`row-product-${productId}`).remove();
            calculateTotal();
        }

        function toggleAddressField() {
            const deliveryMethod = document.getElementById('delivery_method').value;
            const addressContainer = document.getElementById('address-container');
            addressContainer.style.display = (deliveryMethod === 'delivery' || deliveryMethod === 'cod') ? 'block' : 'none';
            document.getElementById('guest_address').required = (deliveryMethod === 'delivery' || deliveryMethod === 'cod');
            calculateTotal();
        }

        function calculateTotal() {
            let grandTotal = 0;
            const deliveryMethod = document.getElementById('delivery_method').value;
            const isGlobalDiscountActive = <?php echo GLOBAL_DISKON_AKTIF ? 'true' : 'false'; ?>;
            const itemRows = document.querySelectorAll('#items-container tr');

            itemRows.forEach(row => {
                const productId = row.querySelector('input[name="product_ids[]"]').value;
                const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
                const priceDisplay = row.querySelector('.price-display');
                
                const product = productsData.find(p => p.id == productId);
                if (product && quantity > 0) {
                    let pricePerItem = parseFloat(product.price);
                    if (isGlobalDiscountActive && product.discount_percentage > 0 && product.discount_methods) {
                        if (product.discount_methods.split(',').includes(deliveryMethod)) {
                            pricePerItem -= (pricePerItem * product.discount_percentage / 100);
                        }
                    }
                    const subtotal = pricePerItem * quantity;
                    grandTotal += subtotal;
                    priceDisplay.innerHTML = `Rp ${subtotal.toLocaleString('id-ID')}`;
                }
            });

            document.getElementById('total-summary').innerText = `Total Belanja: Rp ${grandTotal.toLocaleString('id-ID')}`;
        }
    </script>
</body>
</html>