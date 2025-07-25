-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql113.infinityfree.com
-- Waktu pembuatan: 24 Jul 2025 pada 06.25
-- Versi server: 11.4.7-MariaDB
-- Versi PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39411554_kopi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `guest_address` text DEFAULT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `voucher_discount` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'WhatsApp',
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `whatsapp_url` text DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_voucher_usage`
--

CREATE TABLE `order_voucher_usage` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `voucher_code` varchar(50) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image_url`, `category`) VALUES
(15, 'Hazelnut Latte (Ella)', '29000.00', 'prod_6880b8315b735.jfif', 'minuman-kopi'),
(76, 'Paket Hemat Kopi', '29500.00', 'prod_6880b9820fccc.png', 'paket-kopi'),
(77, 'Paket Kenyang Kopi', '33500.00', 'prod_6880b9ab7a251.png', 'paket-kopi'),
(78, 'Paket Sharing Kopi', '33500.00', 'prod_6880b9e2b9d40.png', 'paket-kopi'),
(79, 'Paket Sultan Kopi', '47500.00', 'prod_6880b9f73064a.png', 'paket-kopi'),
(80, 'Paket Santai Kopi', '38000.00', 'prod_6880b9c4bea7b.png', 'paket-kopi'),
(81, 'Paket Sultan Thai Tea', '36000.00', 'prod_6880baca81b9c.png', 'paket-teh'),
(82, 'Paket Sharing Thai Tea', '22000.00', 'prod_6880ba906e895.png', 'paket-teh'),
(83, 'Paket Kenyang Thai Tea', '22000.00', 'prod_6880ba5f497d1.png', 'paket-teh'),
(84, 'Paket Santai Thai Tea', '27000.00', 'prod_6880ba774cd39.png', 'paket-teh'),
(85, 'Paket Hemat Thai Tea', '18000.00', 'prod_6880ba3fee0da.png', 'paket-teh'),
(86, 'Burger Mini Beef Patties', '11000.00', 'prod_6880b6d3360a4.jfif', 'makanan'),
(87, 'Burger Mini Beef Slice', '8000.00', 'prod_6880b6eac0bbe.jfif', 'makanan'),
(88, 'Burger Mini Chicken', '9000.00', 'prod_6880b72ca99a9.jfif', 'makanan'),
(89, 'Special Burger Beef Patties', '20000.00', 'prod_6880b7784d9c9.jfif', 'makanan'),
(90, 'Special Burger Beef Slice', '13000.00', 'prod_6880b78234936.jfif', 'makanan'),
(91, 'Special Burger Chicken', '15000.00', 'prod_6880b78e00b5f.jfif', 'makanan'),
(92, 'Dimsum', '15000.00', 'prod_6880b73b6fdbc.jfif', 'makanan'),
(93, 'Spaghetti Bolognese', '15000.00', 'prod_6880b75fcbc76.jfif', 'makanan'),
(94, 'Mango Sticky Rice', '15000.00', 'prod_6880b748271c0.jfif', 'makanan'),
(95, 'Sakura Latte (Ralat)', '18000.00', 'prod_6880b872a5474.jfif', 'minuman-kopi'),
(96, 'Lychee Latte (Lyla)', '18000.00', 'prod_6880b88eb8e1b.png', 'minuman-kopi'),
(97, 'Caramel Latte (Carla)', '20000.00', 'prod_6880b81ea3d59.jfif', 'minuman-kopi'),
(98, 'Kopi Susu Original', '18000.00', 'prod_6880b858a9ead.jfif', 'minuman-kopi'),
(99, 'Kopi Susu Gula Aren', '21000.00', 'prod_6880b84b871c9.jfif', 'minuman-kopi'),
(100, 'Milk Base Mangga', '17000.00', 'prod_6880b8c563f5d.png', 'minuman-nonkopi'),
(101, 'Milk Base Red Velvet', '17000.00', 'prod_6880b8daa54ba.jfif', 'minuman-nonkopi'),
(102, 'Thai Tea Original', '8000.00', 'prod_6880b94c6baeb.jfif', 'minuman-nonkopi'),
(103, 'Thai Tea Lychee', '8000.00', 'prod_6880b926948f1.png', 'minuman-nonkopi'),
(104, 'Thai Tea Milk', '8000.00', 'prod_6880b938a6e0d.jfif', 'minuman-nonkopi'),
(105, 'Thai Tea Lemon', '8000.00', 'prod_6880b90fc4a00.png', 'minuman-nonkopi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `voucher_code` varchar(50) NOT NULL,
  `status` enum('tersedia','terpakai') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` date NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `used_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `vouchers`
--

INSERT INTO `vouchers` (`id`, `user_id`, `voucher_code`, `status`, `created_at`, `expires_at`, `is_used`, `used_at`) VALUES
(3, NULL, 'GRATIS-4AAF91', 'tersedia', '2025-07-23 13:57:40', '2025-08-22', 0, NULL),
(4, NULL, 'GRATIS-2958EF', 'tersedia', '2025-07-23 14:17:06', '2025-08-22', 0, NULL),
(5, NULL, 'GRATIS-D09C50', 'tersedia', '2025-07-24 08:33:33', '2025-08-23', 1, '2025-07-24 08:34:11'),
(6, NULL, 'GRATIS-E547C2', 'tersedia', '2025-07-24 08:44:30', '2025-08-23', 1, '2025-07-24 08:45:07'),
(7, NULL, 'GRATIS-990FAD', 'tersedia', '2025-07-24 09:02:17', '2025-08-23', 1, '2025-07-24 09:02:53'),
(8, NULL, 'GRATIS-2B62DD', 'tersedia', '2025-07-24 09:09:06', '2025-08-23', 1, '2025-07-24 09:09:43'),
(9, NULL, 'GRATIS-0F1406', 'tersedia', '2025-07-24 09:15:44', '2025-08-23', 1, '2025-07-24 09:16:17'),
(10, NULL, 'GRATIS-581C3A', 'tersedia', '2025-07-24 09:24:37', '2025-08-23', 1, '2025-07-24 09:26:14'),
(11, NULL, 'GRATIS-E8FFA1', 'tersedia', '2025-07-24 09:27:26', '2025-08-23', 1, '2025-07-24 09:28:04'),
(12, NULL, 'GRATIS-51675A', 'tersedia', '2025-07-24 09:37:41', '2025-08-23', 1, '2025-07-24 09:38:04'),
(13, NULL, 'GRATIS-772FE7', 'tersedia', '2025-07-24 09:42:15', '2025-08-23', 1, '2025-07-24 09:42:53');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_on_user_delete` (`user_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `order_voucher_usage`
--
ALTER TABLE `order_voucher_usage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_order_voucher` (`order_id`,`voucher_id`),
  ADD KEY `voucher_id` (`voucher_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voucher_code` (`voucher_code`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT untuk tabel `order_voucher_usage`
--
ALTER TABLE `order_voucher_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_on_user_delete` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ketidakleluasaan untuk tabel `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `fk_vouchers_on_user_delete` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
