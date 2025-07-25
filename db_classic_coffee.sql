-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jul 2025 pada 11.58
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_classic_coffee`
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
  `payment_method` varchar(50) DEFAULT 'WhatsApp',
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `guest_name`, `guest_address`, `guest_phone`, `guest_email`, `total_price`, `payment_method`, `status`, `order_date`) VALUES
(41, 8, 'Panda', 'jakarta', '0895361619272', 'umamrafa1@gmail.com', 100000.00, 'WhatsApp', 'pending', '2025-07-23 08:44:11'),
(42, 8, 'Panda', 'upb', '0895361619272', 'umamrafa1@gmail.com', 29000.00, 'WhatsApp', 'pending', '2025-07-23 08:48:21'),
(43, 8, 'Panda', 'sd', '0895361619272', 'umamrafa1@gmail.com', 33000.00, 'WhatsApp', 'paid', '2025-07-23 09:21:39'),
(44, 8, 'Panda', 'kebumen', '0895361619272', 'jamal@yahoo.com', 15000.00, 'WhatsApp', 'pending', '2025-07-23 09:46:23'),
(45, 8, 'Panda', 'as', '0895361619272', 'umamrafa1@gmail.com', 33500.00, 'WhatsApp', 'pending', '2025-07-23 09:48:02'),
(46, 8, 'Panda', 'x', '0895361619272', 'umamrafa1@gmail.com', 21000.00, 'WhatsApp', 'pending', '2025-07-23 09:48:37'),
(47, 8, 'Panda', 'a', '0895361619272', 'umamrafa1@gmail.com', 15000.00, 'WhatsApp', 'paid', '2025-07-23 09:49:57'),
(48, 8, 'Panda', 'as', '0895361619272', 'umamrafa1@gmail.com', 15000.00, 'WhatsApp', 'pending', '2025-07-23 09:50:56'),
(49, 8, 'Panda', 'a', '0895361619272', 'umamrafa1@gmail.com', 17000.00, 'WhatsApp', 'pending', '2025-07-23 09:56:34');

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

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_per_item`) VALUES
(32, 41, 87, 4, 8000.00),
(33, 41, 101, 4, 17000.00),
(34, 42, 15, 1, 29000.00),
(35, 43, 86, 3, 11000.00),
(36, 44, 94, 1, 15000.00),
(37, 45, 77, 1, 33500.00),
(38, 46, 99, 1, 21000.00),
(39, 47, 91, 1, 15000.00),
(40, 48, 93, 1, 15000.00),
(41, 49, 100, 1, 17000.00);

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
(15, 'Hazelnut Latte (Ella)', 29000.00, 'prod_688092bab3dc1.jfif', 'minuman-kopi'),
(76, 'Paket Hemat Kopi', 29500.00, 'prod_688094f617b58.png', 'paket-kopi'),
(77, 'Paket Kenyang Kopi', 33500.00, 'prod_68809515a290c.png', 'paket-kopi'),
(78, 'Paket Sharing Kopi', 33500.00, 'prod_688095e409fbe.png', 'paket-kopi'),
(79, 'Paket Sultan Kopi', 47500.00, 'prod_68809590164ef.png', 'paket-kopi'),
(80, 'Paket Santai Kopi', 38000.00, 'prod_6880957c6cac1.png', 'paket-kopi'),
(81, 'Paket Sultan Thai Tea', 36000.00, 'prod_6880968280b26.png', 'paket-teh'),
(82, 'Paket Sharing Thai Tea', 22000.00, 'prod_6880966bda536.png', 'paket-teh'),
(83, 'Paket Kenyang Thai Tea', 22000.00, 'prod_6880963586a4e.png', 'paket-teh'),
(84, 'Paket Santai Thai Tea', 27000.00, 'prod_688096525b1ba.png', 'paket-teh'),
(85, 'Paket Hemat Thai Tea', 18000.00, 'prod_68809619b4822.png', 'paket-teh'),
(86, 'Burger Mini Beef Patties', 11000.00, 'prod_68809270aafe7.jfif', 'makanan'),
(87, 'Burger Mini Beef Slice', 8000.00, 'prod_68809285e6b49.jfif', 'makanan'),
(88, 'Burger Mini Chicken', 9000.00, 'prod_6880928c269ef.jfif', 'makanan'),
(89, 'Special Burger Beef Patties', 20000.00, 'prod_688092df05f28.jfif', 'makanan'),
(90, 'Special Burger Beef Slice', 13000.00, 'prod_688092e6191b4.jfif', 'makanan'),
(91, 'Special Burger Chicken', 15000.00, 'prod_688092ef8a019.jfif', 'makanan'),
(92, 'Dimsum', 15000.00, 'prod_688092adb5ed5.jfif', 'makanan'),
(93, 'Spaghetti Bolognese', 15000.00, 'prod_688092d4df8e6.jfif', 'makanan'),
(94, 'Mango Sticky Rice', 15000.00, 'prod_688092c7a60a1.jfif', 'makanan'),
(95, 'Sakura Latte (Ralat)', 18000.00, 'prod_6880948d6b87e.jfif', 'minuman-kopi'),
(96, 'Lychee Latte (Lyla)', 18000.00, 'prod_6880942c97514.png', 'minuman-nonkopi'),
(97, 'Caramel Latte (Carla)', 20000.00, 'prod_68809322b9be2.jfif', 'minuman-kopi'),
(98, 'Kopi Susu Original', 18000.00, 'prod_6880939e3ab9f.jfif', 'minuman-kopi'),
(99, 'Kopi Susu Gula Aren', 21000.00, 'prod_6880938c8fb57.jfif', 'minuman-kopi'),
(100, 'Milk Base Mangga', 17000.00, 'prod_688094646b29c.png', 'minuman-nonkopi'),
(101, 'Milk Base Red Velvet', 17000.00, 'prod_68809473a7dd8.jfif', 'minuman-nonkopi'),
(102, 'Thai Tea Original', 8000.00, 'prod_688094d3e0ede.jfif', 'minuman-nonkopi'),
(103, 'Thai Tea Lychee', 8000.00, 'prod_688094afe4da1.png', 'minuman-nonkopi'),
(104, 'Thai Tea Milk', 8000.00, 'prod_688094c748b27.jfif', 'minuman-nonkopi'),
(105, 'Thai Tea Lemon', 8000.00, 'prod_688094a57dee7.png', 'minuman-nonkopi');

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

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `profile_picture`, `created_at`) VALUES
(8, 'Panda', '$2y$10$Wa9VKTkelSBkojbcQPpA2.UmYGOmRXUnsaMiGgUWSxClwexeaNWmO', 'default.png', '2025-07-23 08:37:43');

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
  `expires_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
