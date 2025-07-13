-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Jul 2025 pada 15.08
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
(1, 'Espresso', 18000.00, 'images/espresso.jpg', NULL),
(2, 'Americano', 20000.00, 'images/americano.jpg', NULL),
(3, 'Latte', 25000.00, 'images/latte.jpg', NULL),
(4, 'Cappuccino', 25000.00, 'images/coffee4.jpg', NULL),
(5, 'Macchiato', 22000.00, 'images/coffee5.jpg', NULL),
(6, 'Mocha', 28000.00, 'images/coffee6.jpg', NULL),
(7, 'Flat White', 26000.00, 'images/coffee7.jpg', NULL),
(8, 'Affogato', 30000.00, 'images/coffee8.jpg', NULL),
(9, 'V60', 27000.00, 'images/coffee9.jpg', NULL),
(10, 'Vietnam Drip', 24000.00, 'images/coffee10.jpg', NULL),
(11, 'Cold Brew', 28000.00, 'images/coffee11.jpg', NULL),
(12, 'Kopi Tubruk', 15000.00, 'images/coffee12.jpg', NULL),
(13, 'Irish Coffee', 35000.00, 'images/coffee13.jpg', NULL),
(14, 'Caramel Latte', 29000.00, 'images/coffee14.jpg', NULL),
(15, 'Hazelnut Latte (Ella)', 29000.00, 'images/coffee15.jpg', NULL),
(76, 'Paket Hemat Kopi', 29500.00, 'images/paket_kopi1.jpg', 'paket'),
(77, 'Paket Kenyang Kopi', 33500.00, 'images/paket_kopi2.jpg', 'paket'),
(78, 'Paket Sharing Kopi', 33500.00, 'images/paket_kopi3.jpg', 'paket'),
(79, 'Paket Sultan Kopi', 47500.00, 'images/paket_kopi4.jpg', 'paket'),
(80, 'Paket Santai Kopi', 38000.00, 'images/paket_kopi5.jpg', 'paket'),
(81, 'Paket Sultan Thai Tea', 36000.00, 'images/paket_teh1.jpg', 'paket'),
(82, 'Paket Sharing Thai Tea', 22000.00, 'images/paket_teh2.jpg', 'paket'),
(83, 'Paket Kenyang Thai Tea', 22000.00, 'images/paket_teh3.jpg', 'paket'),
(84, 'Paket Santai Thai Tea', 27000.00, 'images/paket_teh4.jpg', 'paket'),
(85, 'Paket Hemat Thai Tea', 18000.00, 'images/paket_teh5.jpg', 'paket'),
(86, 'Burger Mini Beef Patties', 11000.00, 'images/burger1.jpg', 'makanan'),
(87, 'Burger Mini Beef Slice', 8000.00, 'images/burger2.jpg', 'makanan'),
(88, 'Burger Mini Chicken', 9000.00, 'images/burger3.jpg', 'makanan'),
(89, 'Special Burger Beef Patties', 20000.00, 'images/burger_special1.jpg', 'makanan'),
(90, 'Special Burger Beef Slice', 13000.00, 'images/burger_special2.jpg', 'makanan'),
(91, 'Special Burger Chicken', 15000.00, 'images/burger_special3.jpg', 'makanan'),
(92, 'Dimsum', 15000.00, 'images/dimsum.jpg', 'makanan'),
(93, 'Spaghetti Bolognese', 15000.00, 'images/spaghetti.jpg', 'makanan'),
(94, 'Mango Sticky Rice', 15000.00, 'images/mango_sticky.jpg', 'makanan'),
(95, 'Sakura Latte (Ralat)', 18000.00, 'images/sakura_latte.jpg', 'minuman'),
(96, 'Lychee Latte (Lyla)', 18000.00, 'images/lychee_latte.jpg', 'minuman'),
(97, 'Caramel Latte (Carla)', 20000.00, 'images/caramel_latte.jpg', 'minuman'),
(98, 'Kopi Susu Original', 18000.00, 'images/kopsus_ori.jpg', 'minuman'),
(99, 'Kopi Susu Gula Aren', 21000.00, 'images/kopsus_gula_aren.jpg', 'minuman'),
(100, 'Milk Base Mangga', 17000.00, 'images/milk_mangga.jpg', 'minuman'),
(101, 'Milk Base Red Velvet', 17000.00, 'images/milk_redvelvet.jpg', 'minuman'),
(102, 'Thai Tea Original', 8000.00, 'images/thai_tea_ori.jpg', 'minuman'),
(103, 'Thai Tea Lychee', 8000.00, 'images/thai_tea_lychee.jpg', 'minuman'),
(104, 'Thai Tea Milk', 8000.00, 'images/thai_tea_milk.jpg', 'minuman'),
(105, 'Thai Tea Lemon', 8000.00, 'images/thai_tea_lemon.jpg', 'minuman');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
