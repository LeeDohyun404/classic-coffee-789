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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan - Classic Coffee 789</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            background: linear-gradient(135deg, #f4f7f6 0%, #e8f0ef 100%); 
            color: #333; 
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(90, 58, 34, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1.5" fill="rgba(255,255,255,0.1)"/></svg>');
            pointer-events: none;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            position: relative;
            z-index: 1;
        }
        
        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header h1 i {
            font-size: 2.5rem;
            animation: pulse 2s infinite;
        }
        
        .back-btn {
            background: rgba(255,255,255,0.15);
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .form-container { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
        }
        
        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #5a3a22 0%, #8b6f47 100%);
        }
        
        .form-title {
            text-align: center;
            color: #5a3a22;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 30px;
            position: relative;
        }
        
        .form-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, #5a3a22 0%, #8b6f47 100%);
            border-radius: 2px;
        }
        
        .form-group { 
            margin-bottom: 25px; 
            position: relative;
        }
        
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #5a3a22;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-group input, 
        .form-group textarea { 
            width: 100%; 
            padding: 15px 20px; 
            border: 2px solid #e1e5e9; 
            border-radius: 12px; 
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fafbfc;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group input:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: #5a3a22;
            background: white;
            box-shadow: 0 0 0 3px rgba(90, 58, 34, 0.1);
            transform: translateY(-2px);
        }
        
        .form-group.focused label {
            color: #5a3a22;
            transform: translateY(-2px);
        }
        
        /* Choice Options Styling */
        .choice-section {
            margin-bottom: 30px;
        }
        
        .choice-section > label {
            display: block;
            margin-bottom: 15px;
            font-weight: 600;
            color: #5a3a22;
            font-size: 1rem;
        }
        
        .choice-options { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px; 
            margin-bottom: 20px; 
        }
        
        .choice-option {
            position: relative;
        }
        
        .choice-option input[type="radio"] { 
            display: none; 
        }
        
        .choice-option label { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            border: 2px solid #e1e5e9; 
            padding: 18px; 
            border-radius: 12px; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            background: white;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .choice-option label::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(90, 58, 34, 0.05), transparent);
            transition: left 0.5s;
        }
        
        .choice-option input[type="radio"]:checked + label::before {
            left: 100%;
        }
        
        .choice-option input[type="radio"]:checked + label { 
            border-color: #5a3a22; 
            background: linear-gradient(135deg, #fff7ed 0%, #fef7f0 100%); 
            box-shadow: 0 5px 15px rgba(90, 58, 34, 0.15);
            transform: translateY(-2px);
        }
        
        .choice-option:hover label {
            border-color: #8b6f47;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .icon-bg { 
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%); 
            color: white; 
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.1rem;
            box-shadow: 0 3px 10px rgba(90, 58, 34, 0.3);
        }
        
        .choice-text {
            flex: 1;
            font-weight: 500;
            color: #333;
        }
        
        /* QRIS Container */
        #qris-container {
            margin-top: 20px;
            padding: 25px;
            border: 2px dashed #5a3a22;
            border-radius: 15px;
            background: linear-gradient(135deg, #fffaf0 0%, #fef7f0 100%);
            text-align: center;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
        }
        
        #qris-container.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        #qris-container img {
            max-width: 250px;
            width: 100%;
            height: auto;
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        #qris-container img:hover {
            transform: scale(1.05);
        }
        
        #qris-container p {
            margin: 0;
            color: #5a3a22;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        /* Address Container */
        #address-container {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s ease;
        }
        
        #address-container.show {
            opacity: 1;
            max-height: 200px;
        }
        
        /* Scheduler */
        .scheduler-row { 
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px; 
        }
        
        .scheduler-row .form-group { 
            margin-bottom: 0; 
        }
        
        /* Submit Button */
        .btn { 
            width: 100%; 
            padding: 18px; 
            border: none; 
            border-radius: 12px; 
            background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%); 
            color: white; 
            font-size: 1.1rem; 
            font-weight: 600;
            cursor: pointer; 
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            margin-top: 20px;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover {
            background: linear-gradient(135deg, #4a2f1d 0%, #3a251a 100%);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(90, 58, 34, 0.3);
        }
        
        .btn:active {
            transform: translateY(-1px);
        }
        
        .btn.loading {
            opacity: 0.8;
            pointer-events: none;
        }
        
        .btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        /* User Info Badge */
        .user-info {
            background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
            border: 1px solid #c3e6cb;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-info i {
            color: #28a745;
            font-size: 1.2rem;
        }
        
        .user-info span {
            color: #155724;
            font-weight: 500;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .container { padding: 15px; }
            .form-container { padding: 30px; }
            .header h1 { font-size: 1.8rem; }
        }
        
        @media (max-width: 768px) {
            .container { padding: 10px; }
            
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 0 20px;
            }
            
            .header h1 { 
                font-size: 1.6rem; 
                justify-content: center;
            }
            
            .form-container { 
                padding: 25px; 
                border-radius: 15px;
            }
            
            .form-title { 
                font-size: 1.6rem; 
            }
            
            .choice-options { 
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .choice-option label {
                padding: 15px;
            }
            
            .scheduler-row { 
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .icon-bg {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
            
            #qris-container {
                padding: 20px;
            }
            
            #qris-container img {
                max-width: 200px;
            }
        }
        
        @media (max-width: 480px) {
            .header h1 { 
                font-size: 1.4rem; 
            }
            
            .form-container { 
                padding: 20px; 
            }
            
            .form-title { 
                font-size: 1.4rem; 
            }
            
            .form-group input, 
            .form-group textarea { 
                padding: 12px 15px; 
                font-size: 0.95rem;
            }
            
            .choice-option label {
                padding: 12px;
                gap: 10px;
            }
            
            .icon-bg {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
            }
            
            .choice-text {
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 15px;
                font-size: 1rem;
            }
            
            #qris-container {
                padding: 15px;
            }
            
            #qris-container img {
                max-width: 180px;
            }
        }
        
        /* Touch Device Optimizations */
        @media (hover: none) and (pointer: coarse) {
            .choice-option:hover label {
                transform: none;
                box-shadow: none;
            }
            
            .choice-option label:active {
                transform: scale(0.98);
            }
            
            .btn:hover {
                transform: none;
                background: linear-gradient(135deg, #5a3a22 0%, #4a2f1d 100%);
            }
            
            .btn:active {
                transform: scale(0.98);
            }
        }
        
        /* Flatpickr Custom Styling */
        .flatpickr-input {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%235a3a22"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 20px;
            padding-right: 50px !important;
        }
        
        .flatpickr-time .flatpickr-input {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%235a3a22"><path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.7L16.2,16.2Z"/></svg>');
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1><i class="fas fa-clipboard-list"></i> Form Pemesanan</h1>
                <a href="keranjang.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                </a>
            </div>
        </div>
        
        <div class="form-container">
            <h2 class="form-title">Formulir Pemesanan</h2>
            
            <?php if ($is_logged_in && $user_data): ?>
                <div class="user-info">
                    <i class="fas fa-user-check"></i>
                    <span>Masuk sebagai: <strong><?php echo htmlspecialchars($user_data['username']); ?></strong></span>
                </div>
            <?php endif; ?>
            
            <form id="orderForm" action="proses_pesanan.php" method="POST">
                <?php if (!$is_logged_in): ?>
                    <div class="form-group">
                        <label for="customer_name"><i class="fas fa-user"></i> Nama Lengkap</label>
                        <input type="text" name="customer_name" id="customer_name" placeholder="Masukkan nama lengkap Anda..." required>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="customer_phone"><i class="fas fa-phone"></i> No. HP (WhatsApp)</label>
                    <input type="tel" name="customer_phone" id="customer_phone" placeholder="Contoh: 08123456789" required>
                </div>
                
                <div class="form-group">
                    <label for="guest_email"><i class="fas fa-envelope"></i> Email (Opsional)</label>
                    <input type="email" name="guest_email" id="guest_email" placeholder="email@example.com">
                </div>

                <div class="choice-section">
                    <label>Metode Pengambilan</label>
                    <div class="choice-options">
                        <div class="choice-option">
                            <input type="radio" id="pickup" name="delivery_method" value="pickup" checked>
                            <label for="pickup">
                                <span class="icon-bg"><i class="fas fa-store"></i></span>
                                <span class="choice-text">Ambil Ditempat</span>
                            </label>
                        </div>
                        
                        <div class="choice-option">
                            <input type="radio" id="delivery" name="delivery_method" value="delivery">
                            <label for="delivery">
                                <span class="icon-bg"><i class="fas fa-motorcycle"></i></span>
                                <span class="choice-text">Ongkir</span>
                            </label>
                        </div>
                        
                        <div class="choice-option">
                            <input type="radio" id="cod" name="delivery_method" value="cod">
                            <label for="cod">
                                <span class="icon-bg"><i class="fas fa-hand-holding-usd"></i></span>
                                <span class="choice-text">COD</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="choice-section">
                    <label>Metode Pembayaran</label>
                    <div class="choice-options">
                        <div class="choice-option">
                            <input type="radio" id="cash" name="payment_choice" value="cash" checked>
                            <label for="cash">
                                <span class="icon-bg"><i class="fas fa-money-bill-wave"></i></span>
                                <span class="choice-text">Cash (Bayar Ditempat)</span>
                            </label>
                        </div>
                        
                        <div class="choice-option">
                            <input type="radio" id="qris" name="payment_choice" value="qris">
                            <label for="qris">
                                <span class="icon-bg"><i class="fas fa-qrcode"></i></span>
                                <span class="choice-text">QRIS</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div id="qris-container">
                    <img src="images/QR.jpg" alt="QRIS Classic Coffee 789">
                    <p><i class="fas fa-info-circle"></i> Silakan scan QR Code di atas dan kirim bukti transfer saat konfirmasi via WhatsApp.</p>
                </div>
                
                <div class="form-group" id="address-container">
                    <label for="guest_address"><i class="fas fa-map-marker-alt"></i> Alamat Lengkap (untuk Ongkir/COD)</label>
                    <textarea name="guest_address" id="guest_address" rows="3" placeholder="Masukkan alamat lengkap untuk pengiriman..."></textarea>
                </div>

                <div class="form-group" id="scheduler-container">
                    <label><i class="fas fa-clock"></i> Jadwalkan Waktu Ambil/Kirim</label>
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
                    <label for="order_notes"><i class="fas fa-sticky-note"></i> Catatan Pesanan (Opsional)</label>
                    <textarea name="order_notes" id="order_notes" rows="3" placeholder="Contoh: less sugar, extra milk, tanpa es..."></textarea>
                </div>

                <button type="submit" class="btn" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Lanjutkan Pesanan
                </button>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== FLATPICKR INITIALIZATION =====
            
            // Date picker
            flatpickr("#pickup_date", {
                dateFormat: "d-m-Y",
                minDate: "today",
                altInput: true,
                altFormat: "j F Y",
                locale: {
                    firstDayOfWeek: 1,
                    weekdays: {
                        shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                    },
                    months: {
                        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                        longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                    }
                }
            });

            // Time picker
            flatpickr("#pickup_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: 9,
                defaultMinute: 0,
                minTime: "08:00",
                maxTime: "21:00"
            });

            // ===== FORM INTERACTIONS =====
            
            const deliveryRadios = document.querySelectorAll('input[name="delivery_method"]');
            const paymentRadios = document.querySelectorAll('input[name="payment_choice"]');
            const qrisContainer = document.getElementById('qris-container');
            const addressContainer = document.getElementById('address-container');
            const addressInput = document.getElementById('guest_address');

            // Toggle address field based on delivery method
            function toggleAddressField() {
                const isDelivery = document.getElementById('delivery').checked;
                const isCod = document.getElementById('cod').checked;
                
                if (isDelivery || isCod) {
                    addressContainer.classList.add('show');
                    addressInput.required = true;
                } else {
                    addressContainer.classList.remove('show');
                    addressInput.required = false;
                }
            }

            // Toggle QRIS display based on payment method
            function toggleQRISDisplay() {
                const isQRIS = document.getElementById('qris').checked;
                
                if (isQRIS) {
                    qrisContainer.classList.add('show');
                } else {
                    qrisContainer.classList.remove('show');
                }
            }

            // Event listeners for delivery method
            deliveryRadios.forEach(radio => {
                radio.addEventListener('change', toggleAddressField);
            });

            // Event listeners for payment method
            paymentRadios.forEach(radio => {
                radio.addEventListener('change', toggleQRISDisplay);
            });

            // ===== FORM FIELD ANIMATIONS =====
            
            const formInputs = document.querySelectorAll('.form-group input, .form-group textarea');
            
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.closest('.form-group').classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.closest('.form-group').classList.remove('focused');
                });
            });

            // ===== FORM VALIDATION & SUBMISSION =====
            
            const orderForm = document.getElementById('orderForm');
            const submitBtn = document.getElementById('submitBtn');
            
            orderForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate required fields
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#e74c3c';
                        field.focus();
                        
                        // Reset border color after 3 seconds
                        setTimeout(() => {
                            field.style.borderColor = '';
                        }, 3000);
                    }
                });
                
                if (!isValid) {
                    showNotification('Mohon lengkapi semua field yang wajib diisi!', 'error');
                    return;
                }
                
                
                // Show loading state
                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                submitBtn.disabled = true;
                
                // Simulate processing time
                setTimeout(() => {
                    showNotification('Pesanan berhasil diproses!', 'success');
                    
                    // Submit the form
                    setTimeout(() => {
                        this.submit();
                    }, 1000);
                }, 1500);
            });

            // ===== NOTIFICATION SYSTEM =====
            
            function showNotification(message, type = 'success') {
                // Remove existing notification
                const existingNotification = document.querySelector('.notification');
                if (existingNotification) {
                    existingNotification.remove();
                }
                
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                `;
                
                // Add notification styles
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${type === 'success' ? 'linear-gradient(135deg, #28a745 0%, #20c997 100%)' : 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)'};
                    color: white;
                    padding: 15px 20px;
                    border-radius: 10px;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-weight: 500;
                    transform: translateX(400px);
                    transition: transform 0.3s ease;
                `;
                
                document.body.appendChild(notification);
                
                // Show notification
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 100);
                
                // Hide notification after 4 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(400px)';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 4000);
            }

            // ===== MOBILE OPTIMIZATIONS =====
            
            // Handle orientation change
            window.addEventListener('orientationchange', () => {
                setTimeout(() => {
                    // Recalculate form dimensions if needed
                    const formContainer = document.querySelector('.form-container');
                    formContainer.style.minHeight = 'auto';
                }, 100);
            });

            // ===== INITIAL SETUP =====
            
            // Set initial states
            toggleAddressField();
            toggleQRISDisplay();
            
            // Add entrance animation to form elements
            const formElements = document.querySelectorAll('.form-group, .choice-section');
            formElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    element.style.transition = 'all 0.5s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            });

            console.log('Form pemesanan initialized successfully');
        });

        // ===== UTILITY FUNCTIONS =====
        
        // Format phone number as user types
        document.getElementById('customer_phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // Add formatting
            if (value.length > 0) {
                if (value.startsWith('62')) {
                    value = '+' + value;
                } else if (value.startsWith('8')) {
                    value = '0' + value;
                }
            }
            
            e.target.value = value;
        });

        // Auto-resize textarea
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
    </script>
</body>
</html>