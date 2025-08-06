<?php
require_once 'config.php';
$is_logged_in = isset($_SESSION['user_id']);
$user_data = null;
if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_data = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pemesanan - Classic Coffee 789</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap');
        body { font-family: 'Poppins', sans-serif; margin: 0; color: #333; background-color: #f4f7f6; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 15px 50px; background: #fff; border-bottom: 1px solid #ddd; }
        main { padding: 40px; }
        .form-container { max-width: 600px; margin: auto; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .form-container h1 { text-align: center; color: #5a3a22; font-family: 'Playfair Display', serif; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn { display: block; width: 100%; padding: 15px; border: none; border-radius: 8px; background-color: #8B4513; color: white; font-size: 16px; cursor: pointer; }
        
        /* === STYLE BARU UNTUK PILIHAN === */
        .choice-options { display: flex; gap: 15px; margin-bottom: 20px; }
        .choice-options label { display: flex; align-items: center; gap: 10px; border: 1px solid #ddd; padding: 15px; border-radius: 8px; cursor: pointer; flex: 1; transition: all 0.3s ease; }
        .choice-options input[type="radio"] { display: none; }
        /* === STYLE BARU UNTUK MEMISAHKAN TANGGAL DAN JAM === */
.scheduler-row { display: flex; gap: 15px; }
.scheduler-row .form-group { flex: 1; margin-bottom: 0; }
        .choice-options input[type="radio"]:checked + label { border-color: #8B4513; background-color: #fff7ed; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .icon-bg { background-color: #8B4513; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        
        /* === STYLE BARU UNTUK TAMPILAN QRIS === */
        #qris-container {
            margin-top: 20px;
            padding: 20px;
            border: 1px dashed #8B4513;
            border-radius: 8px;
            background-color: #fffaf0;
            text-align: center;
        }
        #qris-container img {
            max-width: 80%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        #qris-container p {
            margin: 0;
            color: #5a3a22;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <header></header>
    <main>
        <div class="form-container">
            <h1>Formulir Pemesanan</h1>
            <form action="proses_pesanan.php" method="POST">

                <?php if (!$is_logged_in): ?>
                    <div class="form-group"><label for="customer_name">Nama Lengkap</label><input type="text" name="customer_name" id="customer_name" required></div>
                <?php endif; ?>
                <div class="form-group"><label for="customer_phone">No. HP (WhatsApp)</label><input type="tel" name="customer_phone" id="customer_phone" required></div>
                <div class="form-group"><label for="guest_email">Email (Opsional)</label><input type="email" name="guest_email" id="guest_email"></div>

                <div class="form-group">
                    <label>Metode Pengambilan</label>
                    <div class="choice-options">
                        <input type="radio" id="pickup" name="delivery_method" value="pickup" checked onchange="toggleFields()">
                        <label for="pickup"><span class="icon-bg"><i class="fas fa-store"></i></span> Ambil Ditempat</label>
                        
                        <input type="radio" id="delivery" name="delivery_method" value="delivery" onchange="toggleFields()">
                        <label for="delivery"><span class="icon-bg"><i class="fas fa-motorcycle"></i></span> Ongkir</label>
                        
                        <input type="radio" id="cod" name="delivery_method" value="cod" onchange="toggleFields()">
                        <label for="cod"><span class="icon-bg"><i class="fas fa-hand-holding-usd"></i></span> COD</label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <div class="choice-options">
                        <input type="radio" id="cash" name="payment_choice" value="cash" checked>
                        <label for="cash"><span class="icon-bg"><i class="fas fa-money-bill-wave"></i></span> Cash (Bayar Ditempat)</label>
                        
                        <input type="radio" id="qris" name="payment_choice" value="qris">
                        <label for="qris"><span class="icon-bg"><i class="fas fa-qrcode"></i></span> QRIS</label>
                    </div>
                </div>

                <div id="qris-container" style="display:none;">
                    <img src="images/QR.jpg" alt="QRIS Classic Coffee 789">
                    <p>Silakan scan QR Code di atas dan kirim bukti transfer saat konfirmasi via WhatsApp.</p>
                </div>
                <div class="form-group" id="address-container" style="display:none;">
                    <label for="guest_address">Alamat Lengkap (untuk Ongkir/COD)</label>
                    <textarea name="guest_address" id="guest_address" rows="3"></textarea>
                </div>

                <div class="form-group" id="scheduler-container">
    <label>Jadwalkan Waktu Ambil/Kirim</label>
    <div class="scheduler-row">
        <div class="form-group">
            <input type="text" name="pickup_date" id="pickup_date" placeholder="Pilih tanggal..." required>
        </div>
        <div class="form-group">
            <input type="text" name="pickup_time" id="pickup_time" placeholder="Pilih jam..." required>
        </div>
    </div>
</div>

                <div class="form-group">
                    <label for="order_notes">Catatan Pesanan (Opsional)</label>
                    <textarea name="order_notes" id="order_notes" rows="3" placeholder="Contoh: less sugar, extra milk..."></textarea>
                </div>

                <button type="submit" class="btn">Lanjutkan Pesanan</button>
            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
      // ================== Inisialisasi Flatpickr BARU (TERPISAH) ==================
// Inisialisasi untuk Pemilih TANGGAL
flatpickr("#pickup_date", {
    dateFormat: "d-m-Y",
    minDate: "today",
    altInput: true,       // Menampilkan format yang lebih ramah pengguna
    altFormat: "j F Y", // Contoh: 5 August 2025
});

// Inisialisasi untuk Pemilih JAM
flatpickr("#pickup_time", {
    enableTime: true,
    noCalendar: true, // Hanya tampilkan pilihan jam
    dateFormat: "H:i",
    time_24hr: true,
});
// ================== AKHIR INISIALISASI BARU ==================

        // ================== JAVASCRIPT BARU UNTUK KONTROL TAMPILAN ==================
        const cashRadio = document.getElementById('cash');
        const qrisRadio = document.getElementById('qris');
        const qrisContainer = document.getElementById('qris-container');

        function togglePaymentView() {
            if (qrisRadio.checked) {
                qrisContainer.style.display = 'block';
            } else {
                qrisContainer.style.display = 'none';
            }
        }
        
        cashRadio.addEventListener('change', togglePaymentView);
        qrisRadio.addEventListener('change', togglePaymentView);
        // ================== AKHIR JAVASCRIPT BARU ==================

        function toggleFields() {
            const isDelivery = document.getElementById('delivery').checked;
            const isCod = document.getElementById('cod').checked;
            const addressContainer = document.getElementById('address-container');
            const addressInput = document.getElementById('guest_address');
            
            if (isDelivery || isCod) {
                addressContainer.style.display = 'block';
                addressInput.required = true;
            } else {
                addressContainer.style.display = 'none';
                addressInput.required = false;
            }
        }
        
        // Panggil fungsi sekali saat halaman dimuat untuk mengatur tampilan awal
        document.addEventListener('DOMContentLoaded', function() {
            toggleFields();
            togglePaymentView();
        });
    </script>
</body>
</html>