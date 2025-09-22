-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql113.infinityfree.com
-- Waktu pembuatan: 16 Sep 2025 pada 22.48
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
-- Struktur dari tabel `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `image_url`, `title`, `subtitle`, `is_active`, `sort_order`) VALUES
(1, 'slide_689373154fb74.jpg', 'â˜• Selamat Datang di Classic Coffee 789', '\"Beli Satu Kurang, Beli Dua Nambah\"', 1, 5),
(2, 'slide_68937304d8752.jpg', 'â˜• Selamat Datang di Classic Coffee 789', '\"Classic Coffee, Se-classic Rasanya\" ', 1, 3),
(4, 'slide_689372dee83cd.jpg', '\"Hemat 10% dengan Ambil di Tempat!\"', 'ðŸŽ‰ Mau lebih hemat?\r\nDapatkan DISKON 10% khusus untuk pembelian dengan metode Ambil di Tempat (Pick Up) atau COD min pembelian!\r\nðŸ“ Cukup pesan, datang, dan ambil langsung di lokasi kami â€” tanpa biaya kirim, tanpa ribet!\r\nðŸ“† Promo berlaku 1 - 30 September 2025!\r\nðŸ’¬ Info Lokasi: Desa Kebanaran, Tamanwinangun RT 03/ RW 08 No.59\r\nYuk, manfaatkan kesempatan ini. Hematnya dapet, cepatnya juga iya!', 1, 2),
(5, 'slide_68920fb91370d.jpg', 'Promo Merdeka! Diskon 10% ðŸŽ‰', 'ðŸ”¥ Spesial 17 Agustus\r\nðŸŽ Diskon 10% tanpa syarat ribet!', 0, 1),
(6, 'slide_689372915e0c5.jpeg', 'ðŸŽ‰ Promo Spesial 789 - Pemesanan Melalui Website 10%!', 'ðŸ“† Berlaku September 2025!\r\nðŸ›ï¸ Diskon 10% untuk semua varian\r\nðŸ“ Semua metode pembelian ditempat dan pengambilan menggunakan kurir ', 0, 4);

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
  `applied_discount_type` varchar(50) DEFAULT NULL,
  `pickup_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'WhatsApp',
  `payment_choice` varchar(50) DEFAULT 'cash',
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `whatsapp_url` text DEFAULT NULL,
  `order_notes` text DEFAULT NULL,
  `pickup_datetime` varchar(100) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `guest_name`, `guest_address`, `guest_phone`, `guest_email`, `total_price`, `voucher_discount`, `applied_discount_type`, `pickup_discount`, `shipping_fee`, `payment_method`, `payment_choice`, `status`, `whatsapp_url`, `order_notes`, `pickup_datetime`, `order_date`) VALUES
(116, NULL, 'Fina', 'Ambil Ditempat', '081215952969', 'nursafina058@gmail.com', '18000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289518530077&text=Halo+Classic+Coffee+789%2C+saya+ingin+memesan%3A%0A%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+18.000+%3D+Rp+18.000%0A%0ASubtotal+Belanja%3A+Rp+18.000%0A%2ATotal+Pesanan%3A+Rp+18.000%2A%0A%0A%2ACatatan+Pesanan%3A%2A%0AV60+yaa%0D%0ABesok+ketemu+di+plut%0A%0ABerikut+data+saya%3A%0ANama%3A+Fina%0ANo.+HP%3A+081215952969%0AMetode+Pengambilan%3A+%2AAmbil+Ditempat%2A%0A%0AMohon+informasikan+kapan+pesanan+bisa+diambil.+Terima+kasih.%0A%0A%0A%28Ref+Order+ID%3A+116%29', 'V60 yaa\r\nBesok ketemu di plut', NULL, '2025-07-28 12:25:16'),
(118, NULL, 'Paryatun ', 'Ambil Ditempat', '087769745676', 'aryat4659@gmail.com', '20000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289518530077&text=Halo+Classic+Coffee+789%2C+saya+ingin+memesan%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+20.000+%3D+Rp+20.000%0A%0ASubtotal+Belanja%3A+Rp+20.000%0A%2ATotal+Pesanan%3A+Rp+20.000%2A%0A%0ABerikut+data+saya%3A%0ANama%3A+Paryatun+%0ANo.+HP%3A+087769745676%0AMetode+Pengambilan%3A+%2AAmbil+Ditempat%2A%0A%0AMohon+informasikan+kapan+pesanan+bisa+diambil.+Terima+kasih.%0A%0A%0A%28Ref+Order+ID%3A+118%29', '', NULL, '2025-07-29 02:38:08'),
(135, NULL, 'KIRANA PUTRI', 'selang', '08973702600', 'aryanikirana11@gmail.com', '20000.00', '0.00', NULL, '0.00', '0.00', 'COD', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289518530077&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+29.000+%3D+Rp+29.000%0A%0ASubtotal+Belanja%3A+Rp+29.000%0A%2ATotal+%28sebelum+ongkir%29%3A+Rp+29.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+KIRANA+PUTRI%0ANo.+HP%3A+08973702600%0A%0A%2AJadwal%3A%2A%0ARabu%2C+30+Jul+2025+-+Jam+19%3A32%0A%0AMetode%3A+%2ACOD+%28Cash+on+Delivery%29%2A%0ALokasi+COD%3A%0Aselang%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+135%29', '', 'Rabu, 30 Jul 2025 - Jam 19:32', '2025-07-30 12:32:46'),
(136, NULL, 'Ulima Dinda', 'Ambil Ditempat', '087833112539', 'ulimadinda139@gmail.com', '16000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289518530077&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Thai+Tea+Milk%0A+++Qty%3A+2+x+Rp+8.000+%3D+Rp+16.000%0A%0ASubtotal+Belanja%3A+Rp+16.000%0A%2ATotal+Pesanan%3A+Rp+16.000%2A%0A%0A%2ACatatan+Pesanan%3A%2A%0Aless+sugar%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ulima+Dinda%0ANo.+HP%3A+087833112539%0A%0A%2AJadwal%3A%2A%0ARabu%2C+30+Jul+2025+-+Jam+21%3A00%0A%0AMetode%3A+%2AAmbil+Ditempat%2A%0A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+136%29', 'less sugar', 'Rabu, 30 Jul 2025 - Jam 21:00', '2025-07-30 14:04:51'),
(148, NULL, 'Rofi Aprilia', 'Ambil Ditempat', '085771538098', 'rofiaprilia72@gmail.com', '20000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+20.000+%3D+Rp+20.000%0A%0ASubtotal+Belanja%3A+Rp+20.000%0A%2ATotal+Pesanan%3A+Rp+20.000%2A%0A%0A%2ACatatan+Pesanan%3A%2A%0ADi+ambil+di+gedung+metrologi%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Rofi+Aprilia%0ANo.+HP%3A+085771538098%0A%0A%2AJadwal%3A%2A%0AJumat%2C+01+Aug+2025+-+Jam+13%3A15%0A%0AMetode%3A+%2AAmbil+Ditempat%2A%0A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+148%29', 'Di ambil di gedung metrologi', 'Jumat, 01 Aug 2025 - Jam 13:15', '2025-08-01 06:15:59'),
(149, NULL, 'tri wahyuni', 'perwira brilliant+box packaging', '085351712243', 'triw95976@gmail.com', '80000.00', '0.00', NULL, '0.00', '0.00', 'COD', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+20.000+%3D+Rp+20.000%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+3+x+Rp+20.000+%3D+Rp+60.000%0A%0ASubtotal+Belanja%3A+Rp+80.000%0A%2ATotal+%28sebelum+ongkir%29%3A+Rp+80.000%2A%0A%0A%2ACatatan+Pesanan%3A%2A%0Apakai+box+coffee+isi+3%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+tri+wahyuni%0ANo.+HP%3A+085351712243%0A%0A%2AJadwal%3A%2A%0AJumat%2C+01+Aug+2025+-+Jam+05%3A00%0A%0AMetode%3A+%2ACOD+%28Cash+on+Delivery%29%2A%0ALokasi+COD%3A%0Aperwira+brilliant%2Bbox+packaging%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+149%29', 'pakai box coffee isi 3', 'Jumat, 01 Aug 2025 - Jam 05:00', '2025-08-01 08:34:45'),
(152, NULL, 'Irma Wahyu Novitasari ', 'Ambil Ditempat', '+62 857-9907-9729', '', '61000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Gula+Aren%0A+++Qty%3A+1+x+Rp+21.000+%3D+Rp+21.000%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+20.000+%3D+Rp+20.000%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+20.000+%3D+Rp+20.000%0A%0ASubtotal+Belanja%3A+Rp+61.000%0A%2ATotal+Pesanan%3A+Rp+61.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Irman+Wahyu+Novitasari+%0ANo.+HP%3A+%2B62+857-9907-9729%0A%0A%2AJadwal%3A%2A%0AMinggu%2C+03+Aug+2025+-+Jam+07%3A30%0A%0AMetode%3A+%2AAmbil+Ditempat%2A%0A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+152%29', '', 'Minggu, 03 Aug 2025 - Jam 07:30', '2025-08-02 15:26:36'),
(153, NULL, 'Irma Wahyu Novitasari', 'Ambil Ditempat', '085799079729', 'irmawahyunovitasari@gmail.com', '38000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+18.000+%3D+Rp+18.000%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+20.000+%3D+Rp+20.000%0A%0ASubtotal+Belanja%3A+Rp+38.000%0A%2ATotal+Pesanan%3A+Rp+38.000%2A%0A%0A%2ACatatan+Pesanan%3A%2A%0AJangan+dikasih+gula+kalau+belum+dibuatkan%2C+jd+ori+kak%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Irma+Wahyu+Novitasari%0ANo.+HP%3A+085799079729%0A%0A%2AJadwal%3A%2A%0ASenin%2C+04+Aug+2025+-+Jam+12%3A45%0A%0AMetode%3A+%2AAmbil+Ditempat%2A%0A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+153%29', 'Jangan dikasih gula kalau belum dibuatkan, jd ori kak', 'Senin, 04 Aug 2025 - Jam 12:45', '2025-08-03 21:47:54'),
(154, NULL, 'Arindu', 'Plut UMKM KEBUMEN', '087809626533', 'Arinalhaqiqoh24@gmail.com', '20000.00', '0.00', NULL, '0.00', '0.00', 'COD', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+20.000+%3D+Rp+20.000%0A%0ASubtotal+Belanja%3A+Rp+20.000%0A%2ATotal+%28sebelum+ongkir%29%3A+Rp+20.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Arindu%0ANo.+HP%3A+087809626533%0A%0A%2AJadwal%3A%2A%0ASenin%2C+04+Aug+2025+-+Jam+12%3A05%0A%0AMetode%3A+%2ACOD+%28Cash+on+Delivery%29%2A%0ALokasi+COD%3A%0APlut+UMKM+KEBUMEN%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+154%29', '', 'Senin, 04 Aug 2025 - Jam 12:05', '2025-08-04 05:05:40'),
(184, NULL, 'fafa', 'Ambil Ditempat', '085726054622', 'latifahfaa@gmail.com', '18000.00', '0.00', 'Takeaway', '0.00', '0.00', 'Confirmation Required', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=%F0%9F%A7%BE+%2APesanan+Baru+Diterima%2A+%F0%9F%A7%BE%0A%0A%2ARef+Order+ID%3A+%23184%2A%0A%0A%E2%9C%A8+%2ADETAIL+PESANAN%2A+%E2%9C%A8%0A-%3E+%2ACaramel+Latte+%28Carla%29%2A%0A+++1+x+Rp+20.000+%3D+Rp+20.000%0A%0A%F0%9F%92%B0+%2ARINCIAN+BIAYA%2A+%F0%9F%92%B0%0ASubtotal%3A+Rp+20.000%0ADiskon+Ambil+Ditempat%3A+-Rp+2.000%0A--------------------%0A%2ATOTAL+AKHIR%3A+Rp+18.000%2A%0A%0A%F0%9F%91%A4+%2ADATA+PELANGGAN%2A+%F0%9F%91%A4%0ANama%3A+fafa%0ANo.+HP%3A+085726054622%0A%0A%F0%9F%9A%9A+%2APENGIRIMAN+%26+PEMBAYARAN%2A+%F0%9F%9A%9A%0APembayaran%3A+%2ACASH%2A%0APengambilan%3A+%2APickup%2A%0AJadwal%3A+Senin%2C+04+Aug+2025%2C+00%3A00%0A%0A_Mohon+segera+diproses.+Terima+kasih%21_', '', 'Senin, 04 Aug 2025, 00:00', '2025-08-04 10:03:20'),
(185, NULL, 'Fersellia', 'Ambil Ditempat', '08988836421', 'fersellia98@gmail.com', '18900.00', '0.00', 'Takeaway', '0.00', '0.00', 'Confirmation Required', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=%F0%9F%A7%BE+%2APesanan+Baru+Diterima%2A+%F0%9F%A7%BE%0A%0A%2ARef+Order+ID%3A+%23185%2A%0A%0A%E2%9C%A8+%2ADETAIL+PESANAN%2A+%E2%9C%A8%0A-%3E+%2AKopi+Susu+Gula+Aren%2A%0A+++1+x+Rp+21.000+%3D+Rp+21.000%0A%0A%F0%9F%92%B0+%2ARINCIAN+BIAYA%2A+%F0%9F%92%B0%0ASubtotal%3A+Rp+21.000%0ADiskon+Ambil+Ditempat%3A+-Rp+2.100%0A--------------------%0A%2ATOTAL+AKHIR%3A+Rp+18.900%2A%0A%0A%F0%9F%91%A4+%2ADATA+PELANGGAN%2A+%F0%9F%91%A4%0ANama%3A+Fersellia%0ANo.+HP%3A+08988836421%0A%0A%F0%9F%9A%9A+%2APENGIRIMAN+%26+PEMBAYARAN%2A+%F0%9F%9A%9A%0APembayaran%3A+%2ACASH%2A%0APengambilan%3A+%2APickup%2A%0AJadwal%3A+Senin%2C+04+Aug+2025%2C+00%3A00%0A%0A_Mohon+segera+diproses.+Terima+kasih%21_', '', 'Senin, 04 Aug 2025, 00:00', '2025-08-04 10:03:40'),
(189, NULL, 'Zulfa', 'Ambil Ditempat', '081578134267', 'zulfapratamaningtyas@gmail.com', '9900.00', '0.00', 'Takeaway', '0.00', '0.00', 'Confirmation Required', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=%F0%9F%A7%BE++%2APesanan+Baru+Diterima%2A+%F0%9F%A7%BE+%0A%0A%2ARef+Order+ID%3A+%23189%2A%0A%0A%E2%9C%A8++%2ADETAIL+PESANAN%2A+%E2%9C%A8+%0A-%3E+%2ABurger+Mini+Beef+Patties%2A%0A+++1+x+Rp+11.000+%3D+Rp+11.000%0A%0A%F0%9F%92%B0++%2ARINCIAN+BIAYA%2A+%F0%9F%92%B0+%0ASubtotal%3A+Rp+11.000%0ADiskon+Ambil+Ditempat%3A+-Rp+1.100%0A--------------------%0A%2ATOTAL+AKHIR%3A+Rp+9.900%2A%0A%0A%F0%9F%91%A4++%2ADATA+PELANGGAN%2A+%F0%9F%91%A4+%0ANama%3A+Zulfa%0ANo.+HP%3A+081578134267%0A%0A%F0%9F%9A%9A++%2APENGIRIMAN+%26+PEMBAYARAN%2A+%F0%9F%9A%9A+%0APembayaran%3A+%2ACASH%2A%0APengambilan%3A+%2APickup%2A%0AJadwal%3A+Senin%2C+04+Aug+2025%2C+20%3A00%0A%0A_Mohon+segera+diproses.+Terima+kasih%21_', '', 'Senin, 04 Aug 2025, 20:00', '2025-08-04 12:49:10'),
(209, NULL, 'Ani Wahyuni ', 'Ambil Ditempat', '087737620014', 'aniwahyuni562@gmail.com', '18000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+18.000+%3D+Rp+18.000%0A%0ASubtotal+Asli%3A+Rp+20.000%0ADiskon+Produk%3A+-Rp+2.000%0A%2ATotal+Akhir%3A+Rp+18.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ani+Wahyuni+%0ANo.+HP%3A+087737620014%0A%0A%2ACatatan+Pesanan%3A%2A%0ASugar+normal%0D%0ACapuccino+%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+05+Aug+2025+-+Jam+08%3A54%0A%0AMetode%3A+%2AAmbil+Ditempat%2A%0A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+209%29', 'Sugar normal\r\nCapuccino ', 'Selasa, 05 Aug 2025 - Jam 08:54', '2025-08-05 05:55:29'),
(225, NULL, 'Zulfa', 'Ambil Ditempat', '081578134267', '', '18000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+18.000+%3D+Rp+18.000%0A%0ASubtotal+Asli%3A+Rp+20.000%0ATotal+Diskon+Produk%3A+-Rp+2.000%0A%2ATotal+Akhir%3A+Rp+18.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Zulfa%0ANo.+HP%3A+081578134267%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+05+Aug+2025+-+Jam+15%3A30%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+225%29', '', 'Selasa, 05 Aug 2025 - Jam 15:30', '2025-08-05 08:18:16'),
(226, NULL, 'Tari', 'Ambil Ditempat', '', '', '87300.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+18.000+%3D+Rp+18.000%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+18.000+%3D+Rp+18.000%0A-%3E+Kopi+Susu+Gula+Aren%0A+++Qty%3A+1+x+Rp+18.900+%3D+Rp+18.900%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+16.200+%3D+Rp+16.200%0A-%3E+Lychee+Latte+%28Lyla%29%0A+++Qty%3A+1+x+Rp+16.200+%3D+Rp+16.200%0A%0ASubtotal+Asli%3A+Rp+97.000%0ATotal+Diskon+Produk%3A+-Rp+9.700%0A%2ATotal+Akhir%3A+Rp+87.300%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Tari%0ANo.+HP%3A+%0A%0A%2AJadwal%3A%2A%0AKamis%2C+07+Aug+2025+-+Jam+17%3A00%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+226%29', '', 'Kamis, 07 Aug 2025 - Jam 17:00', '2025-08-07 09:52:04'),
(228, NULL, 'Yuli Febriana', 'Ambil Ditempat', '082226052645', '', '18000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'qris', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+18.000+%3D+Rp+18.000%0A%0ASubtotal+Asli%3A+Rp+20.000%0ATotal+Diskon+Produk%3A+-Rp+2.000%0A%2ATotal+Akhir%3A+Rp+18.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Yuli+Febriana%0ANo.+HP%3A+082226052645%0A%0A%2AJadwal%3A%2A%0AKamis%2C+07+Aug+2025+-+Jam+17%3A10%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2AQRIS%2A%0A_%28Mohon+kirim+bukti+transfer%29_%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+228%29', '', 'Kamis, 07 Aug 2025 - Jam 17:10', '2025-08-07 10:02:33'),
(231, NULL, 'Ani Wahyuni ', 'Ambil Ditempat', '087737620014', 'aniwahyuni562@gmail.com', '18000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+18.000+%3D+Rp+18.000%0A%0ASubtotal+Asli%3A+Rp+20.000%0ATotal+Diskon+Produk%3A+-Rp+2.000%0A%2ATotal+Akhir%3A+Rp+18.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ani+Wahyuni+%0ANo.+HP%3A+087737620014%0A%0A%2AJadwal%3A%2A%0AKamis%2C+07+Aug+2025+-+Jam+19%3A08%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+231%29', '', 'Kamis, 07 Aug 2025 - Jam 19:08', '2025-08-07 12:08:18'),
(232, NULL, 'rachmad siswono', 'Ambil Ditempat', '08981382024', 'rachmadsiswono@gmail.com', '54000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+3+x+Rp+18.000+%3D+Rp+54.000%0A%0ASubtotal+Asli%3A+Rp+60.000%0ATotal+Diskon+Produk%3A+-Rp+6.000%0A%2ATotal+Akhir%3A+Rp+54.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+rachmad+siswono%0ANo.+HP%3A+08981382024%0A%0A%2ACatatan+Pesanan%3A%2A%0ADi+ambil+jm+9%0A%0A%2AJadwal%3A%2A%0AJumat%2C+08+Aug+2025+-+Jam+21%3A30%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+232%29', 'Di ambil jm 9', 'Jumat, 08 Aug 2025 - Jam 21:30', '2025-08-08 14:30:36'),
(233, NULL, 'Bu Jury', 'Ambil Ditempat', '+62 822-2101-9491', '', '54900.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+2+x+Rp+18.000+%3D+Rp+36.000%0A-%3E+Kopi+Susu+Gula+Aren%0A+++Qty%3A+1+x+Rp+18.900+%3D+Rp+18.900%0A%0ASubtotal+Asli%3A+Rp+61.000%0ATotal+Diskon+Produk%3A+-Rp+6.100%0A%2ATotal+Akhir%3A+Rp+54.900%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Bu+Jury%0ANo.+HP%3A+%2B62+822-2101-9491%0A%0A%2AJadwal%3A%2A%0AJumat%2C+08+Aug+2025+-+Jam+22%3A33%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+233%29', '', 'Jumat, 08 Aug 2025 - Jam 22:33', '2025-08-08 15:31:50'),
(234, NULL, 'Paryatun', 'Ambil Ditempat', '087769745676', 'aryat4659@gmail.com', '16200.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+16.200+%3D+Rp+16.200%0A%0ASubtotal+Asli%3A+Rp+18.000%0ATotal+Diskon+Produk%3A+-Rp+1.800%0A%2ATotal+Akhir%3A+Rp+16.200%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Paryatun%0ANo.+HP%3A+087769745676%0A%0A%2AJadwal%3A%2A%0ASabtu%2C+09+Aug+2025+-+Jam+11%3A00%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+234%29', '', 'Sabtu, 09 Aug 2025 - Jam 11:00', '2025-08-08 15:41:07'),
(236, NULL, 'Bu Novi Disnaker', 'BLK', '+62 812-2522-5065', '', '40000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Special+Burger+Beef+Patties%0A+++Qty%3A+2+x+Rp+20.000+%3D+Rp+40.000%0A%0ASubtotal+Asli%3A+Rp+40.000%0A%2ATotal+Akhir%3A+Rp+40.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Bu+Novi+Disnaker%0ANo.+HP%3A+%2B62+812-2522-5065%0A%0A%2AJadwal%3A%2A%0AMinggu%2C+10+Aug+2025+-+Jam+18%3A00%0AMetode%3A+%2AOngkir%2A%0AAlamat+Pengiriman%3A%0ABLK%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+236%29', '', 'Minggu, 10 Aug 2025 - Jam 18:00', '2025-08-10 10:48:51'),
(237, NULL, 'Retno W', 'Karangrejo,karanggayam', '081291849029', '', '27000.00', '0.00', NULL, '0.00', '0.00', 'COD', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+27.000+%3D+Rp+27.000%0A%0ASubtotal+Asli%3A+Rp+27.000%0A%2ATotal+Akhir%3A+Rp+27.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Retno+W%0ANo.+HP%3A+081291849029%0A%0A%2AJadwal%3A%2A%0ASenin%2C+11+Aug+2025+-+Jam+12%3A00%0AMetode%3A+%2ACOD%2A%0ALokasi+COD%3A%0AKarangrejo%2Ckaranggayam%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+237%29', '', 'Senin, 11 Aug 2025 - Jam 12:00', '2025-08-11 02:50:47'),
(240, NULL, 'Bu sar', 'Ambil Ditempat', '', '', '22140.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+22.140+%3D+Rp+22.140%0A%0ASubtotal+Asli%3A+Rp+27.000%0ATotal+Diskon+Produk%3A+-Rp+4.860%0A%2ATotal+Akhir%3A+Rp+22.140%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Bu+sar%0ANo.+HP%3A+%0A%0A%2AJadwal%3A%2A%0AKamis%2C+14+Aug+2025+-+Jam+16%3A37%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+240%29', '', 'Kamis, 14 Aug 2025 - Jam 16:37', '2025-08-14 09:38:09'),
(241, NULL, 'Ananda', 'Ambil Ditempat', '', '', '42640.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+22.140+%3D+Rp+22.140%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+20.500+%3D+Rp+20.500%0A%0ASubtotal+Asli%3A+Rp+52.000%0ATotal+Diskon+Produk%3A+-Rp+9.360%0A%2ATotal+Akhir%3A+Rp+42.640%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ananda%0ANo.+HP%3A+%0A%0A%2AJadwal%3A%2A%0AKamis%2C+14+Aug+2025+-+Jam+21%3A45%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+241%29', '', 'Kamis, 14 Aug 2025 - Jam 21:45', '2025-08-14 15:34:26'),
(245, NULL, 'Rizki Ardana reswari ', 'Ambil Ditempat', '08999220343', 'rizkiardanareswari@gmail.com', '72340.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Burger+Mini+Beef+Patties%0A+++Qty%3A+3+x+Rp+9.900+%3D+Rp+29.700%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+22.140+%3D+Rp+22.140%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+20.500+%3D+Rp+20.500%0A%0ASubtotal+Asli%3A+Rp+85.000%0ATotal+Diskon+Produk%3A+-Rp+12.660%0A%2ATotal+Akhir%3A+Rp+72.340%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Rizki+Ardana+reswari+%0ANo.+HP%3A+08999220343%0A%0A%2ACatatan+Pesanan%3A%2A%0AMakan+di+tempat%0A%0A%2AJadwal%3A%2A%0ASabtu%2C+16+Aug+2025+-+Jam+17%3A19%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+245%29', 'Makan di tempat', 'Sabtu, 16 Aug 2025 - Jam 17:19', '2025-08-16 10:19:35'),
(246, NULL, 'Bu Riska Bimba', 'Ambil Ditempat', '', '', '49500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Ketan+Mangga+Rp+20.000+%2F+BOX%0A+++Qty%3A+5+x+Rp+20.000+%3D+Rp+100.000%0A%0ASubtotal+Asli%3A+Rp+100.000%0A%2ATotal+Akhir%3A+Rp+100.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Bu+Riska+Bimba%0ANo.+HP%3A+%0A%0A%2AJadwal%3A%2A%0AMinggu%2C+17+Aug+2025+-+Jam+08%3A38%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+246%29', '', 'Minggu, 17 Aug 2025 - Jam 08:38', '2025-08-16 22:38:22'),
(247, 41, 'Difa Rahmalia Putri', 'Ambil Ditempat', '628397668163', 'romlhtukiyem123@gmail.com', '51840.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Burger+Mini+Beef+Patties%0A+++Qty%3A+3+x+Rp+9.900+%3D+Rp+29.700%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+22.140+%3D+Rp+22.140%0A%0ASubtotal+Asli%3A+Rp+60.000%0ATotal+Diskon+Produk%3A+-Rp+8.160%0A%2ATotal+Akhir%3A+Rp+51.840%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Difa+Rahmalia+Putri%0ANo.+HP%3A+628397668163%0A%0A%2ACatatan+Pesanan%3A%2A%0Aburger+di+bungkus+2+gabung+1+pisah%0A%0A%2AJadwal%3A%2A%0AKamis%2C+21+Aug+2025+-+Jam+12%3A35%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+247%29', 'burger di bungkus 2 gabung 1 pisah', 'Kamis, 21 Aug 2025 - Jam 12:35', '2025-08-21 05:34:37'),
(248, NULL, 'Mba Nunu', 'PT Global Media Data Prima Jl. Letnan Jenderal Suprapto No. 37-39, Keposan, Kebumen', '+62 877-9221-6725', '', '0.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+permintaan+PRE-ORDER+baru%3A%0A%0A%2ADETAIL+PRODUK+PRE-ORDER%3A%2A%0A-%3E+Rice+Bowl%0A+++Qty%3A+48%0A%0A%2ATotal+Pesanan%3A+Mohon+dikonfirmasi+oleh+admin.%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+PT+Global+Media+Data+Prima%0ANo.+HP%3A+%0A%0A%2ACatatan+Pesanan%3A%2A%0ADikirim+tepat+waktu%0A%0A%2AJadwal%3A%2A%0AJumat%2C+22+Aug+2025+-+Jam+07%3A00%0AMetode%3A+%2AOngkir%2A%0AAlamat+Pengiriman%3A%0AJl.+Letnan+Jenderal+Suprapto+No.+37-39%2C+Keposan%2C+Kebumen%0APembayaran%3A+%2AAkan+Dikonfirmasi+Admin%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+248%29', 'Dikirim tepat waktu', 'Jumat, 22 Aug 2025 - Jam 07:00', '2025-08-21 15:05:13'),
(249, NULL, 'Bu Ruri', 'Ambil Ditempat', '', '', '86100.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+22.140+%3D+Rp+22.140%0A-%3E+Kopi+Susu+Gula+Aren%0A+++Qty%3A+1+x+Rp+22.960+%3D+Rp+22.960%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+20.500+%3D+Rp+20.500%0A-%3E+Lychee+Latte+%28Lyla%29%0A+++Qty%3A+1+x+Rp+20.500+%3D+Rp+20.500%0A%0ASubtotal+Asli%3A+Rp+105.000%0ATotal+Diskon+Produk%3A+-Rp+18.900%0A%2ATotal+Akhir%3A+Rp+86.100%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Bu+Ruri%0ANo.+HP%3A+%0A%0A%2AJadwal%3A%2A%0ASabtu%2C+23+Aug+2025+-+Jam+07%3A00%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+249%29', '', 'Sabtu, 23 Aug 2025 - Jam 07:00', '2025-08-22 22:24:55'),
(250, NULL, 'Bu Ruri', 'Ambil Ditempat', '', '', '0.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+permintaan+PRE-ORDER+baru%3A%0A%0A%2ADETAIL+PRODUK+PRE-ORDER%3A%2A%0A-%3E+Rice+Bowl%0A+++Qty%3A+1%0A%0A%2ATotal+Pesanan%3A+Mohon+dikonfirmasi+oleh+admin.%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Bu+Ruri%0ANo.+HP%3A+%0A%0A%2AJadwal%3A%2A%0ASabtu%2C+23+Aug+2025+-+Jam+07%3A00%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2AAkan+Dikonfirmasi+Admin%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+250%29', '', 'Sabtu, 23 Aug 2025 - Jam 07:00', '2025-08-22 22:25:57'),
(251, NULL, 'Ci ita', 'Ambil Ditempat', '', '', '30000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Bubuk+Rp+30.000%2F+100gr%0A+++Qty%3A+1+x+Rp+30.000+%3D+Rp+30.000%0A%0ASubtotal+Asli%3A+Rp+30.000%0A%2ATotal+Akhir%3A+Rp+30.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ci+ita%0ANo.+HP%3A+%0A%0A%2AJadwal%3A%2A%0ASabtu%2C+23+Aug+2025+-+Jam+08%3A00%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+251%29', '', 'Sabtu, 23 Aug 2025 - Jam 08:00', '2025-08-22 22:27:02'),
(253, NULL, 'Ani Wahyuni ', ' Desa bonjok ', '087737620014', 'aniwahyuni562@gmail.com', '40500.00', '0.00', NULL, '0.00', '0.00', 'COD', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+3+x+Rp+13.500+%3D+Rp+40.500%0A%0ASubtotal+Asli%3A+Rp+81.000%0ATotal+Diskon+Produk%3A+-Rp+40.500%0A%2ATotal+Akhir%3A+Rp+40.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ani+Wahyuni+%0ANo.+HP%3A+087737620014%0A%0A%2ACatatan+Pesanan%3A%2A%0ALess+sugar.+Strong+%0A%0A%2AJadwal%3A%2A%0ASenin%2C+25+Aug+2025+-+Jam+15%3A00%0AMetode%3A+%2ACOD%2A%0ALokasi+COD%3A%0A+Desa+bonjok+%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+253%29', 'Less sugar. Strong ', 'Senin, 25 Aug 2025 - Jam 15:00', '2025-08-24 16:02:45'),
(254, NULL, 'Ani ', 'Ambil Ditempat', '085227285758', '', '13500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+13.500+%3D+Rp+13.500%0A%0ASubtotal+Asli%3A+Rp+27.000%0ATotal+Diskon+Produk%3A+-Rp+13.500%0A%2ATotal+Akhir%3A+Rp+13.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ani+%0ANo.+HP%3A+085227285758%0A%0A%2ACatatan+Pesanan%3A%2A%0AKebumen+festival+%0A%0A%2AJadwal%3A%2A%0ASenin%2C+25+Aug+2025+-+Jam+11%3A55%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+254%29', 'Kebumen festival ', 'Senin, 25 Aug 2025 - Jam 11:55', '2025-08-25 04:55:31'),
(255, NULL, 'Zahra Rizky Nur Azizah', 'Ambil Ditempat', '081250466066', 'azizahzahra474@gmail.com', '13500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+13.500+%3D+Rp+13.500%0A%0ASubtotal+Asli%3A+Rp+27.000%0ATotal+Diskon+Produk%3A+-Rp+13.500%0A%2ATotal+Akhir%3A+Rp+13.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Zahra+Rizky+Nur+Azizah%0ANo.+HP%3A+081250466066%0A%0A%2ACatatan+Pesanan%3A%2A%0AMengambil+di+kebumen+festival%0A%0A%2AJadwal%3A%2A%0ASenin%2C+25+Aug+2025+-+Jam+12%3A05%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+255%29', 'Mengambil di kebumen festival', 'Senin, 25 Aug 2025 - Jam 12:05', '2025-08-25 05:05:41'),
(256, NULL, 'Sarah', 'Ambil Ditempat', '089629807180', 'sarahhanifa17@gmail.com', '14000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Gula+Aren%0A+++Qty%3A+1+x+Rp+14.000+%3D+Rp+14.000%0A%0ASubtotal+Asli%3A+Rp+28.000%0ATotal+Diskon+Produk%3A+-Rp+14.000%0A%2ATotal+Akhir%3A+Rp+14.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Sarah%0ANo.+HP%3A+089629807180%0A%0A%2ACatatan+Pesanan%3A%2A%0AKebumen+festival%0A%0A%2AJadwal%3A%2A%0ASenin%2C+25+Aug+2025+-+Jam+12%3A23%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+256%29', 'Kebumen festival', 'Senin, 25 Aug 2025 - Jam 12:23', '2025-08-25 05:23:36'),
(257, NULL, 'Iqlima', 'Ambil Ditempat', '0895414112442', '', '12500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+12.500+%3D+Rp+12.500%0A%0ASubtotal+Asli%3A+Rp+25.000%0ATotal+Diskon+Produk%3A+-Rp+12.500%0A%2ATotal+Akhir%3A+Rp+12.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Iqlima%0ANo.+HP%3A+0895414112442%0A%0A%2ACatatan+Pesanan%3A%2A%0ABeli+di+kebumen+fest+2025%0A%0A%2AJadwal%3A%2A%0ASenin%2C+25+Aug+2025+-+Jam+15%3A02%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+257%29', 'Beli di kebumen fest 2025', 'Senin, 25 Aug 2025 - Jam 15:02', '2025-08-25 08:02:21'),
(258, NULL, 'Alam', 'Ambil Ditempat', '082138474080', 'vollimaniaaaa@gmail.com', '12500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+12.500+%3D+Rp+12.500%0A%0ASubtotal+Asli%3A+Rp+25.000%0ATotal+Diskon+Produk%3A+-Rp+12.500%0A%2ATotal+Akhir%3A+Rp+12.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Alam%0ANo.+HP%3A+082138474080%0A%0A%2ACatatan+Pesanan%3A%2A%0ABeli+dikebumen+fest%0A%0A%2AJadwal%3A%2A%0ASenin%2C+25+Aug+2025+-+Jam+19%3A51%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+258%29', 'Beli dikebumen fest', 'Senin, 25 Aug 2025 - Jam 19:51', '2025-08-25 12:51:26'),
(259, NULL, 'Fitria ningsih', 'Ambil Ditempat', '089676903808', 'fitriananing91@gmail.com', '67500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'qris', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+5+x+Rp+13.500+%3D+Rp+67.500%0A%0ASubtotal+Asli%3A+Rp+135.000%0ATotal+Diskon+Produk%3A+-Rp+67.500%0A%2ATotal+Akhir%3A+Rp+67.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Fitria+ningsih%0ANo.+HP%3A+089676903808%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+26+Aug+2025+-+Jam+11%3A00%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2AQRIS%2A%0A_%28Mohon+kirim+bukti+transfer%29_%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+259%29', '', 'Selasa, 26 Aug 2025 - Jam 11:00', '2025-08-25 13:33:13'),
(260, NULL, 'Desi Kurniasih', 'Ambil Ditempat', '', '', '51300.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+24.300+%3D+Rp+24.300%0A-%3E+Dimsum%0A+++Qty%3A+2+x+Rp+13.500+%3D+Rp+27.000%0A%0ASubtotal+Asli%3A+Rp+57.000%0ATotal+Diskon+Produk%3A+-Rp+5.700%0A%2ATotal+Akhir%3A+Rp+51.300%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Desi+Kurniasih%0ANo.+HP%3A+%0A%0A%2AJadwal%3A%2A%0ASenin%2C+25+Aug+2025+-+Jam+22%3A00%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+260%29', '', 'Senin, 25 Aug 2025 - Jam 22:00', '2025-08-25 14:51:38'),
(261, NULL, 'Laela Meida', 'Ambil Ditempat', '085942138982', '', '27500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Gula+Aren%0A+++Qty%3A+1+x+Rp+14.000+%3D+Rp+14.000%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+13.500+%3D+Rp+13.500%0A%0ASubtotal+Asli%3A+Rp+55.000%0ATotal+Diskon+Produk%3A+-Rp+27.500%0A%2ATotal+Akhir%3A+Rp+27.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Laela+Meida%0ANo.+HP%3A+085942138982%0A%0A%2ACatatan+Pesanan%3A%2A%0AThanku%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+26+Aug+2025+-+Jam+11%3A52%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+261%29', 'Thanku', 'Selasa, 26 Aug 2025 - Jam 11:52', '2025-08-26 04:52:41'),
(262, NULL, 'Ani Retnowati', 'Ambil Ditempat', '081916827039', '', '13500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+13.500+%3D+Rp+13.500%0A%0ASubtotal+Asli%3A+Rp+27.000%0ATotal+Diskon+Produk%3A+-Rp+13.500%0A%2ATotal+Akhir%3A+Rp+13.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ani+Retnowati%0ANo.+HP%3A+081916827039%0A%0A%2ACatatan+Pesanan%3A%2A%0AKebumen+Festival%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+26+Aug+2025+-+Jam+15%3A21%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+262%29', 'Kebumen Festival', 'Selasa, 26 Aug 2025 - Jam 15:21', '2025-08-26 08:21:25'),
(263, NULL, 'Pak Nanang', 'Ambil Ditempat', '+62 812-2664-873', '', '12500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+12.500+%3D+Rp+12.500%0A%0ASubtotal+Asli%3A+Rp+25.000%0ATotal+Diskon+Produk%3A+-Rp+12.500%0A%2ATotal+Akhir%3A+Rp+12.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Pak+Nanang%0ANo.+HP%3A+%2B62+812-2664-873%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+26+Aug+2025+-+Jam+16%3A34%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+263%29', '', 'Selasa, 26 Aug 2025 - Jam 16:34', '2025-08-26 09:34:34'),
(264, NULL, 'Bu Umma', 'Ambil Ditempat', '+62 813-2019-1088', '', '27500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Gula+Aren%0A+++Qty%3A+1+x+Rp+14.000+%3D+Rp+14.000%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+13.500+%3D+Rp+13.500%0A%0ASubtotal+Asli%3A+Rp+55.000%0ATotal+Diskon+Produk%3A+-Rp+27.500%0A%2ATotal+Akhir%3A+Rp+27.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Bu+Umma%0ANo.+HP%3A+%2B62+813-2019-1088%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+26+Aug+2025+-+Jam+18%3A16%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+264%29', '', 'Selasa, 26 Aug 2025 - Jam 18:16', '2025-08-26 11:16:29'),
(265, 42, 'shodik', 'Ambil Ditempat', '082220808885', '', '12500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+12.500+%3D+Rp+12.500%0A%0ASubtotal+Asli%3A+Rp+25.000%0ATotal+Diskon+Produk%3A+-Rp+12.500%0A%2ATotal+Akhir%3A+Rp+12.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+shodik%0ANo.+HP%3A+082220808885%0A%0A%2AJadwal%3A%2A%0ARabu%2C+27+Aug+2025+-+Jam+08%3A38%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+265%29', '', 'Rabu, 27 Aug 2025 - Jam 08:38', '2025-08-27 01:38:54'),
(266, NULL, 'Tsania maya ', 'Ambil Ditempat', '0881010188823', 'tsaniamaya7@gmail.com', '12500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Lychee+Latte+%28Lyla%29%0A+++Qty%3A+1+x+Rp+12.500+%3D+Rp+12.500%0A%0ASubtotal+Asli%3A+Rp+25.000%0ATotal+Diskon+Produk%3A+-Rp+12.500%0A%2ATotal+Akhir%3A+Rp+12.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Tsania+maya+%0ANo.+HP%3A+0881010188823%0A%0A%2ACatatan+Pesanan%3A%2A%0A-%0A%0A%2AJadwal%3A%2A%0ARabu%2C+27+Aug+2025+-+Jam+17%3A10%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+266%29', '-', 'Rabu, 27 Aug 2025 - Jam 17:10', '2025-08-27 02:26:59'),
(267, NULL, 'Dini Septina', 'Ambil Ditempat', '085325573075', '', '27000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+2+x+Rp+13.500+%3D+Rp+27.000%0A%0ASubtotal+Asli%3A+Rp+54.000%0ATotal+Diskon+Produk%3A+-Rp+27.000%0A%2ATotal+Akhir%3A+Rp+27.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Dini+Septina%0ANo.+HP%3A+085325573075%0A%0A%2ACatatan+Pesanan%3A%2A%0ADiambil+di+kebumen+fest%0A%0A%2AJadwal%3A%2A%0ARabu%2C+27+Aug+2025+-+Jam+03%3A07%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+267%29', 'Diambil di kebumen fest', 'Rabu, 27 Aug 2025 - Jam 03:07', '2025-08-27 04:08:17'),
(268, NULL, 'Hany', 'Tamanwinangun ', '+62 812-2838-3426', '', '32500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Original%0A+++Qty%3A+1+x+Rp+12.500+%3D+Rp+12.500%0A-%3E+Special+Burger+Beef+Patties%0A+++Qty%3A+1+x+Rp+20.000+%3D+Rp+20.000%0A%0ASubtotal+Asli%3A+Rp+45.000%0ATotal+Diskon+Produk%3A+-Rp+12.500%0A%2ATotal+Akhir%3A+Rp+32.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Hany%0ANo.+HP%3A+%2B62+812-2838-3426%0A%0A%2AJadwal%3A%2A%0AKamis%2C+28+Aug+2025+-+Jam+15%3A17%0AMetode%3A+%2AOngkir%2A%0AAlamat+Pengiriman%3A%0ATamanwinangun+%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+268%29', '', 'Kamis, 28 Aug 2025 - Jam 15:17', '2025-08-28 08:17:06'),
(269, NULL, 'Ibu amalia', 'Ambil Ditempat', '081215907171', 'amalia@gmail.com', '13500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Hazelnut+Latte+%28Ella%29%0A+++Qty%3A+1+x+Rp+13.500+%3D+Rp+13.500%0A%0ASubtotal+Asli%3A+Rp+27.000%0ATotal+Diskon+Produk%3A+-Rp+13.500%0A%2ATotal+Akhir%3A+Rp+13.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ibu+amalia%0ANo.+HP%3A+081215907171%0A%0A%2ACatatan+Pesanan%3A%2A%0AKebumen+festival+2025%0A%0A%2AJadwal%3A%2A%0AKamis%2C+28+Aug+2025+-+Jam+17%3A54%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+269%29', 'Kebumen festival 2025', 'Kamis, 28 Aug 2025 - Jam 17:54', '2025-08-28 10:54:38'),
(270, NULL, 'Fersellia Lia', 'Ambil Ditempat', '08988836421', '', '14000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'qris', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Kopi+Susu+Gula+Aren%0A+++Qty%3A+1+x+Rp+14.000+%3D+Rp+14.000%0A%0ASubtotal+Asli%3A+Rp+28.000%0ATotal+Diskon+Produk%3A+-Rp+14.000%0A%2ATotal+Akhir%3A+Rp+14.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Fersellia+Lia%0ANo.+HP%3A+08988836421%0A%0A%2AJadwal%3A%2A%0AKamis%2C+28+Aug+2025+-+Jam+20%3A47%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2AQRIS%2A%0A_%28Mohon+kirim+bukti+transfer%29_%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+270%29', '', 'Kamis, 28 Aug 2025 - Jam 20:47', '2025-08-28 13:47:48'),
(271, NULL, 'Rohman', 'Ambil Ditempat', '08985593480', 'jama48008@gmail.com', '13500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+13.500+%3D+Rp+13.500%0A%0ASubtotal+Asli%3A+Rp+27.000%0ATotal+Diskon+Produk%3A+-Rp+13.500%0A%2ATotal+Akhir%3A+Rp+13.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Rohman%0ANo.+HP%3A+08985593480%0A%0A%2ACatatan+Pesanan%3A%2A%0ACuma+contoh%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+02+Sep+2025+-+Jam+09%3A00%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+271%29', 'Cuma contoh', 'Selasa, 02 Sep 2025 - Jam 09:00', '2025-09-01 14:51:02'),
(272, NULL, 'Sekar Larasati ', 'Ambil Ditempat', '087837978399', 'slarasati845@gmail.com', '24300.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+24.300+%3D+Rp+24.300%0A%0ASubtotal+Asli%3A+Rp+27.000%0ATotal+Diskon+Produk%3A+-Rp+2.700%0A%2ATotal+Akhir%3A+Rp+24.300%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Sekar+Larasati+%0ANo.+HP%3A+087837978399%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+02+Sep+2025+-+Jam+08%3A55%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+272%29', '', 'Selasa, 02 Sep 2025 - Jam 08:55', '2025-09-02 01:54:58'),
(274, 43, 'farah', 'Ambil Ditempat', '6283897668163', 'divarahmalia@gmail.com', '19800.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Burger+Mini+Beef+Patties%0A+++Qty%3A+2+x+Rp+9.900+%3D+Rp+19.800%0A%0ASubtotal+Asli%3A+Rp+22.000%0ATotal+Diskon+Produk%3A+-Rp+2.200%0A%2ATotal+Akhir%3A+Rp+19.800%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+farah%0ANo.+HP%3A+6283897668163%0A%0A%2AJadwal%3A%2A%0ARabu%2C+03+Sep+2025+-+Jam+13%3A00%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+274%29', '', 'Rabu, 03 Sep 2025 - Jam 13:00', '2025-09-03 14:22:52'),
(275, 44, 'Rizkiana Putri', 'Polres', '6289665571422', 'Rizkianaputri@gmail.com', '52000.00', '0.00', NULL, '0.00', '0.00', 'COD', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+27.000+%3D+Rp+27.000%0A-%3E+Lychee+Latte+%28Lyla%29%0A+++Qty%3A+1+x+Rp+25.000+%3D+Rp+25.000%0A%0ASubtotal+Asli%3A+Rp+52.000%0A%2ATotal+Akhir%3A+Rp+52.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Rizkiana+Putri%0ANo.+HP%3A+6289665571422%0A%0A%2ACatatan+Pesanan%3A%2A%0Areseller+kopi%0A%0A%2AJadwal%3A%2A%0ARabu%2C+03+Sep+2025+-+Jam+16%3A00%0AMetode%3A+%2ACOD%2A%0ALokasi+COD%3A%0APolres%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+275%29', 'reseller kopi', 'Rabu, 03 Sep 2025 - Jam 16:00', '2025-09-03 14:29:56'),
(276, NULL, 'Umay', 'Gesing tamanwinangun', '082242009792', 'umairoh210@gmail.com', '40000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'qris', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Special+Burger+Beef+Patties%0A+++Qty%3A+2+x+Rp+20.000+%3D+Rp+40.000%0A%0ASubtotal+Asli%3A+Rp+40.000%0A%2ATotal+Akhir%3A+Rp+40.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Umay%0ANo.+HP%3A+082242009792%0A%0A%2ACatatan+Pesanan%3A%2A%0ASaus+di+pisah%0A%0A%2AJadwal%3A%2A%0AMinggu%2C+07+Sep+2025+-+Jam+15%3A08%0AMetode%3A+%2AOngkir%2A%0AAlamat+Pengiriman%3A%0AGesing+tamanwinangun%0APembayaran%3A+%2AQRIS%2A%0A_%28Mohon+kirim+bukti+transfer%29_%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+276%29', 'Saus di pisah', 'Minggu, 07 Sep 2025 - Jam 15:08', '2025-09-07 08:08:48'),
(277, NULL, 'Bu Rifa', 'Belakang Masjid Agung Kauman Kebumen ', '+62 877-3256-9324', 'rifa@gmail.com', '60000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Dimsum%0A+++Qty%3A+4+x+Rp+15.000+%3D+Rp+60.000%0A%0ASubtotal+Asli%3A+Rp+60.000%0A%2ATotal+Akhir%3A+Rp+60.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Bu+Rifa%0ANo.+HP%3A+%2B62+877-3256-9324%0A%0A%2ACatatan+Pesanan%3A%2A%0APembelian+2000+total+29pcs+untuk+kegiatan+ngaji+diantar+kena+ongkir+5000%0A%0A%2AJadwal%3A%2A%0AMinggu%2C+07+Sep+2025+-+Jam+14%3A30%0AMetode%3A+%2AOngkir%2A%0AAlamat+Pengiriman%3A%0ABelakang+Masjid+Agung+Kauman+Kebumen+%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+277%29', 'Pembelian 2000 total 29pcs untuk kegiatan ngaji diantar kena ongkir 5000', 'Minggu, 07 Sep 2025 - Jam 14:30', '2025-09-07 16:05:14'),
(278, NULL, 'Fina NN ', 'Ambil Ditempat', '+62 812-1595-2969', 'nursafina058@gmail.com', '13500.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Dimsum%0A+++Qty%3A+1+x+Rp+13.500+%3D+Rp+13.500%0A%0ASubtotal+Asli%3A+Rp+15.000%0ATotal+Diskon+Produk%3A+-Rp+1.500%0A%2ATotal+Akhir%3A+Rp+13.500%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Fina+NN+%0ANo.+HP%3A+%2B62+812-1595-2969%0A%0A%2ACatatan+Pesanan%3A%2A%0APromo+50%25+Inkubasi+Bisnis+Ambil+Ditempat%0A%0A%2AJadwal%3A%2A%0AMinggu%2C+07+Sep+2025+-+Jam+17%3A00%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+278%29', 'Promo 50% Inkubasi Bisnis Ambil Ditempat', 'Minggu, 07 Sep 2025 - Jam 17:00', '2025-09-07 16:08:40'),
(279, NULL, 'Ani Wahyuni ', 'Kebumen ', '087737620014', 'aniwahyuni562@gmail.com', '48600.00', '0.00', NULL, '0.00', '0.00', 'COD', 'qris', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+2+x+Rp+27.000+%3D+Rp+54.000%0A%0ASubtotal+Asli%3A+Rp+54.000%0A%2ATotal+Akhir%3A+Rp+54.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ani+Wahyuni+%0ANo.+HP%3A+087737620014%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+09+Sep+2025+-+Jam+13%3A10%0AMetode%3A+%2ACOD%2A%0ALokasi+COD%3A%0AKebumen+%0APembayaran%3A+%2AQRIS%2A%0A_%28Mohon+kirim+bukti+transfer%29_%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+279%29', '', 'Selasa, 09 Sep 2025 - Jam 13:10', '2025-09-08 12:58:52'),
(280, NULL, 'ade', 'Ambil Ditempat', '0895347675061', 'adebani7662@gmail.com', '18000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Special+Burger+Beef+Patties%0A+++Qty%3A+1+x+Rp+18.000+%3D+Rp+18.000%0A%0ASubtotal+Asli%3A+Rp+20.000%0ATotal+Diskon+Produk%3A+-Rp+2.000%0A%2ATotal+Akhir%3A+Rp+18.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+ade%0ANo.+HP%3A+0895347675061%0A%0A%2ACatatan+Pesanan%3A%2A%0Adiskon+kartar%0A%0A%2AJadwal%3A%2A%0ASenin%2C+08+Sep+2025+-+Jam+21%3A11%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+280%29', 'diskon kartar', 'Senin, 08 Sep 2025 - Jam 21:11', '2025-09-08 14:11:27'),
(281, NULL, 'Ade sabani ', 'Ambil Ditempat', '+62 895-3476-7506', 'adesabani@gmail.com', '7200.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Thai+Tea+Lychee%0A+++Qty%3A+1+x+Rp+7.200+%3D+Rp+7.200%0A%0ASubtotal+Asli%3A+Rp+8.000%0ATotal+Diskon+Produk%3A+-Rp+800%0A%2ATotal+Akhir%3A+Rp+7.200%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Ade+sabani+%0ANo.+HP%3A+%2B62+895-3476-7506%0A%0A%2ACatatan+Pesanan%3A%2A%0AKartar+diskon+50%25%0A%0A%2AJadwal%3A%2A%0ASenin%2C+08+Sep+2025+-+Jam+22%3A02%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+281%29', 'Kartar diskon 50%', 'Senin, 08 Sep 2025 - Jam 22:02', '2025-09-08 15:02:53');
INSERT INTO `orders` (`id`, `user_id`, `guest_name`, `guest_address`, `guest_phone`, `guest_email`, `total_price`, `voucher_discount`, `applied_discount_type`, `pickup_discount`, `shipping_fee`, `payment_method`, `payment_choice`, `status`, `whatsapp_url`, `order_notes`, `pickup_datetime`, `order_date`) VALUES
(282, NULL, 'Rosyidah', 'Ambil Ditempat', '085875928809', 'rosyijetis98@gmail.com', '21600.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'qris', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Burger+Mini+Chicken%0A+++Qty%3A+1+x+Rp+8.100+%3D+Rp+8.100%0A-%3E+Dimsum%0A+++Qty%3A+1+x+Rp+13.500+%3D+Rp+13.500%0A%0ASubtotal+Asli%3A+Rp+24.000%0ATotal+Diskon+Produk%3A+-Rp+2.400%0A%2ATotal+Akhir%3A+Rp+21.600%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Rosyidah%0ANo.+HP%3A+085875928809%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+09+Sep+2025+-+Jam+14%3A15%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2AQRIS%2A%0A_%28Mohon+kirim+bukti+transfer%29_%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+282%29', '', 'Selasa, 09 Sep 2025 - Jam 14:15', '2025-09-09 01:38:50');

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
(116, 116, 98, 1, '18000.00'),
(118, 118, 97, 1, '20000.00'),
(135, 135, 15, 1, '20000.00'),
(136, 136, 104, 2, '8000.00'),
(148, 148, 15, 1, '20000.00'),
(149, 149, 15, 1, '20000.00'),
(150, 149, 97, 3, '20000.00'),
(153, 152, 99, 1, '21000.00'),
(154, 152, 15, 1, '20000.00'),
(155, 152, 97, 1, '20000.00'),
(156, 153, 98, 1, '18000.00'),
(157, 153, 15, 1, '20000.00'),
(158, 154, 15, 1, '20000.00'),
(188, 184, 97, 1, '20000.00'),
(189, 185, 99, 1, '21000.00'),
(193, 189, 86, 1, '11000.00'),
(213, 209, 97, 1, '20000.00'),
(230, 225, 97, 1, '20000.00'),
(231, 226, 97, 1, '20000.00'),
(232, 226, 15, 1, '20000.00'),
(233, 226, 99, 1, '21000.00'),
(234, 226, 98, 1, '18000.00'),
(235, 226, 96, 1, '18000.00'),
(237, 228, 97, 1, '20000.00'),
(240, 231, 97, 1, '20000.00'),
(241, 232, 97, 3, '20000.00'),
(242, 233, 97, 2, '20000.00'),
(243, 233, 99, 1, '21000.00'),
(244, 234, 98, 1, '18000.00'),
(246, 236, 89, 2, '20000.00'),
(247, 237, 97, 1, '27000.00'),
(250, 240, 97, 1, '27000.00'),
(251, 241, 97, 1, '27000.00'),
(252, 241, 98, 1, '25000.00'),
(256, 245, 86, 3, '11000.00'),
(257, 245, 97, 1, '27000.00'),
(258, 245, 98, 1, '25000.00'),
(259, 246, 86, 5, '11000.00'),
(260, 247, 86, 3, '11000.00'),
(261, 247, 97, 1, '27000.00'),
(262, 248, 120, 48, '0.00'),
(263, 249, 15, 1, '27000.00'),
(264, 249, 99, 1, '28000.00'),
(265, 249, 98, 1, '25000.00'),
(266, 249, 96, 1, '25000.00'),
(267, 250, 120, 1, '0.00'),
(268, 251, 113, 1, '30000.00'),
(270, 253, 97, 3, '27000.00'),
(271, 254, 15, 1, '27000.00'),
(272, 255, 15, 1, '27000.00'),
(273, 256, 99, 1, '28000.00'),
(274, 257, 98, 1, '25000.00'),
(275, 258, 98, 1, '25000.00'),
(276, 259, 97, 5, '27000.00'),
(277, 260, 97, 1, '27000.00'),
(278, 260, 92, 2, '15000.00'),
(279, 261, 99, 1, '28000.00'),
(280, 261, 15, 1, '27000.00'),
(281, 262, 15, 1, '27000.00'),
(282, 263, 98, 1, '25000.00'),
(283, 264, 99, 1, '28000.00'),
(284, 264, 15, 1, '27000.00'),
(285, 265, 98, 1, '25000.00'),
(286, 266, 96, 1, '25000.00'),
(287, 267, 97, 2, '27000.00'),
(288, 268, 98, 1, '25000.00'),
(289, 268, 89, 1, '20000.00'),
(290, 269, 15, 1, '27000.00'),
(291, 270, 99, 1, '28000.00'),
(292, 271, 97, 1, '27000.00'),
(293, 272, 97, 1, '27000.00'),
(295, 274, 86, 2, '11000.00'),
(296, 275, 97, 1, '27000.00'),
(297, 275, 96, 1, '25000.00'),
(298, 276, 89, 2, '20000.00'),
(299, 277, 92, 4, '15000.00'),
(300, 278, 92, 1, '15000.00'),
(301, 279, 97, 2, '27000.00'),
(302, 280, 89, 1, '20000.00'),
(303, 281, 103, 1, '8000.00'),
(304, 282, 88, 1, '9000.00'),
(305, 282, 92, 1, '15000.00');

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
  `discount_percentage` int(3) NOT NULL DEFAULT 0,
  `discount_methods` varchar(255) DEFAULT NULL,
  `discount_name` varchar(100) DEFAULT NULL,
  `average_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `review_count` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `discount_percentage`, `discount_methods`, `discount_name`, `average_rating`, `review_count`, `image_url`, `category`) VALUES
(15, 'Hazelnut Latte (Ella)', '27000.00', 10, 'pickup,cod', NULL, '0.00', 0, 'prod_68949da856b6c.jpg', 'minuman-kopi'),
(76, 'Paket Hemat Kopi', '36500.00', 8, 'pickup', NULL, '0.00', 0, 'prod_68949f0320500.png', 'paket-kopi'),
(77, 'Paket Kenyang Kopi', '40500.00', 8, 'pickup', NULL, '0.00', 0, 'prod_68949f203f2f8.png', 'paket-kopi'),
(78, 'Paket Sharing Kopi', '40500.00', 8, 'pickup', NULL, '0.00', 0, 'prod_68949f5bef0e1.png', 'paket-kopi'),
(79, 'Paket Sultan Kopi', '54500.00', 8, 'pickup', NULL, '0.00', 0, 'prod_68949f6f04221.png', 'paket-kopi'),
(80, 'Paket Santai Kopi', '45000.00', 8, 'pickup', NULL, '0.00', 0, 'prod_68949f4699b5c.png', 'paket-kopi'),
(81, 'Paket Sultan Thai Tea', '36000.00', 8, 'pickup', NULL, '0.00', 0, 'prod_68949fddd1d48.png', 'paket-teh'),
(82, 'Paket Sharing Thai Tea', '22000.00', 8, 'pickup', NULL, '0.00', 0, 'prod_68949fbdcff42.png', 'paket-teh'),
(83, 'Paket Kenyang Thai Tea', '22000.00', 8, 'pickup', NULL, '0.00', 0, 'prod_68949f9e6beab.png', 'paket-teh'),
(84, 'Paket Santai Thai Tea', '27000.00', 8, 'pickup', NULL, '0.00', 0, 'prod_68949fae718ec.png', 'paket-teh'),
(85, 'Paket Hemat Thai Tea', '18000.00', 8, 'pickup', NULL, '0.00', 0, 'prod_68949f84bfcb7.png', 'paket-teh'),
(86, 'Burger Mini Beef Patties', '11000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949d203b2ae.jfif', 'makanan'),
(87, 'Burger Mini Beef Slice', '8000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949d31b6524.jfif', 'makanan'),
(88, 'Burger Mini Chicken', '9000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949d4065e50.jfif', 'makanan'),
(89, 'Special Burger Beef Patties', '20000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949d686ee6f.jfif', 'makanan'),
(90, 'Special Burger Beef Slice', '13000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949d79c6afd.jfif', 'makanan'),
(91, 'Special Burger Chicken', '15000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949d88761d9.jfif', 'makanan'),
(92, 'Dimsum', '15000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949d4bd3c0f.jfif', 'makanan'),
(93, 'Spaghetti Bolognese', '15000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949d580ed71.jfif', 'makanan'),
(95, 'Sakura Latte (Ralat)', '25000.00', 10, 'pickup,cod', NULL, '0.00', 0, 'prod_68949e04880ff.jpg', 'minuman-kopi'),
(96, 'Lychee Latte (Lyla)', '25000.00', 10, 'pickup,cod', NULL, '0.00', 0, 'prod_68949df515d55.jpg', 'minuman-kopi'),
(97, 'Caramel Latte (Carla)', '27000.00', 10, 'pickup,cod', NULL, '5.00', 1, 'prod_68949d9795918.jpg', 'minuman-kopi'),
(98, 'Kopi Susu Original', '25000.00', 10, 'pickup,cod', NULL, '0.00', 0, 'prod_68949dd55c92d.jfif', 'minuman-kopi'),
(99, 'Kopi Susu Gula Aren', '25000.00', 10, 'pickup,cod', NULL, '0.00', 0, 'prod_68949dc894454.jfif', 'minuman-kopi'),
(100, 'Milk Base Mangga', '17000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949e14abbeb.png', 'minuman-nonkopi'),
(101, 'Milk Base Red Velvet', '17000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949e217c7a7.jfif', 'minuman-nonkopi'),
(102, 'Thai Tea Original', '8000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949eec34364.jfif', 'minuman-nonkopi'),
(103, 'Thai Tea Lychee', '8000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949ecf761e6.png', 'minuman-nonkopi'),
(104, 'Thai Tea Milk', '8000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949ee00d10f.jfif', 'minuman-nonkopi'),
(105, 'Thai Tea Lemon', '8000.00', 10, 'pickup', NULL, '0.00', 0, 'prod_68949e9d834e0.png', 'minuman-nonkopi'),
(110, 'Kopi V60', '15000.00', 18, 'pickup', NULL, '0.00', 0, 'prod_68949de6447ff.png', 'arsip'),
(113, 'Kopi Bubuk Rp 30.000/ 100gr', '30000.00', 0, NULL, NULL, '0.00', 0, 'prod_68949db996a01.jpg', 'minuman-kopi'),
(115, 'Kopi Bubuk Rp 115.000/ 500gr', '115000.00', 0, NULL, NULL, '0.00', 0, 'prod_689b472a46ec0.jpg', 'minuman-kopi'),
(116, 'Kopi Bubuk Rp 15.000/ 55gr', '15000.00', 0, NULL, NULL, '0.00', 0, 'prod_689b47616c80b.jpg', 'minuman-kopi'),
(117, 'Ketan Mangga ', '0.00', 0, 'pickup', NULL, '0.00', 0, 'prod_68a2c85d96b9a.jpeg', 'pre-order'),
(119, 'Rice Bowl (Pesan Banyak PO Custom)', '15000.00', 0, NULL, NULL, '0.00', 0, 'prod_68a6899a1641f.png', 'arsip'),
(120, 'Rice Bowl', '0.00', 0, NULL, NULL, '0.00', 0, 'prod_68a725ee15f84.png', 'pre-order'),
(121, 'icon', '1000000.00', 0, NULL, NULL, '0.00', 0, 'prod_68b7dccc88282.png', 'arsip');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` tinyint(1) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `review_text`, `created_at`) VALUES
(2, 97, 43, 5, 'Udh nyobain varian classic dari carla,ella sama ralat. Coffenya beda dari yg lain menurutku seperti ada rasa yg unik yg bikin pgn nyoba varian classic yg lainya. Selain itu pemilik classic coffee jg sangat mengedepankan kualitas produknya jdi rasanya selalu enak seperti rasa kopi di cafe2 .Kopinya jg tahan lama waktu itu aku simpen 2 harian rasanya tetep fresh dan enak .Pelayanan sangat ramah beli nya jg sekarang gapang karena classic udh punya website. Selain produk kopi aku jg udh pernah nyoba burger mini patties, burger nya jg gak kalah enak sama kopi nya . Buat yg suka kopi yg bisa nyoba kopi dari Classic Coffee 780 menurutku tempat ini recommend banget', '2025-09-17 02:39:16');

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
(41, 'Difa Rahmalia Putri', '$2y$10$V0QJxcbzKIC2Qi1Yfn/6uOXh9W7jDS9TZv42n6M.Yo1ay0XwPzv2a', 'default.png', '2025-08-21 05:27:58'),
(42, 'shodik', '$2y$10$h38XWfXFmu70pdpbpNCeCevgPKLbbM0Dine8c2ZzotGBko0w8kndO', 'default.png', '2025-08-27 01:37:20'),
(43, 'farah', '$2y$10$HkO9Zg/4co2KGeWrCJ9Lxu09iqutrSBs4tBHRyZPXivjDCEyzduA.', 'default.png', '2025-09-03 14:20:22'),
(44, 'Rizkiana Putri', '$2y$10$qGX/RFfUVQcnO5YcW84ebOl2W1DGtdolEsKU1VK5T/Bi2Q.pXFEDa', 'default.png', '2025-09-03 14:24:12'),
(45, 'admin', '$2y$10$MouwQQUcEkJhX6hPNyMAkehCsaL0la699k10wKujaD6EH0cxtvHhW', 'default.png', '2025-09-17 02:42:41');

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
(13, NULL, 'GRATIS-772FE7', 'tersedia', '2025-07-24 09:42:15', '2025-08-23', 1, '2025-07-24 09:42:53'),
(14, NULL, 'GRATIS-2D4CDE', 'tersedia', '2025-07-25 05:55:14', '2025-08-24', 1, '2025-07-25 05:56:08'),
(15, NULL, 'GRATIS-0912ED', 'tersedia', '2025-07-25 06:05:35', '2025-08-24', 1, '2025-07-25 06:06:09'),
(16, NULL, 'GRATIS-C42F76', 'tersedia', '2025-07-27 15:38:52', '2025-08-26', 1, '2025-07-27 15:40:54'),
(17, NULL, 'GRATIS-A85D1A', 'tersedia', '2025-07-27 15:55:06', '2025-08-26', 1, '2025-07-27 15:55:46'),
(18, NULL, 'GRATIS-D31004', 'tersedia', '2025-07-27 15:57:33', '2025-08-26', 1, '2025-07-27 15:58:31'),
(19, NULL, 'GRATIS-ED3677', 'tersedia', '2025-07-27 16:04:30', '2025-08-26', 0, NULL),
(20, NULL, 'GRATIS-32976F', 'tersedia', '2025-07-28 10:24:51', '2025-08-27', 1, '2025-07-28 10:25:30'),
(21, NULL, 'GRATIS-4C8893', 'tersedia', '2025-07-28 10:27:16', '2025-08-27', 1, '2025-07-28 10:27:43'),
(22, NULL, 'GRATIS-26A0A7', 'tersedia', '2025-07-29 12:03:46', '2025-08-28', 1, '2025-07-29 12:04:17'),
(23, NULL, 'GRATIS-D79D62', 'tersedia', '2025-07-31 07:01:49', '2025-08-30', 1, '2025-07-31 07:02:37'),
(24, NULL, 'GRATIS-AF3688', 'tersedia', '2025-08-04 06:40:58', '2025-09-03', 1, '2025-08-04 06:43:41'),
(25, NULL, 'GRATIS-85348A', 'tersedia', '2025-08-04 06:55:52', '2025-09-03', 1, '2025-08-04 06:56:21'),
(26, NULL, 'GRATIS-FE489A', 'tersedia', '2025-08-04 07:27:43', '2025-09-03', 0, NULL),
(27, NULL, 'GRATIS-9E25AA', 'tersedia', '2025-08-04 07:56:25', '2025-09-03', 1, '2025-08-04 07:58:10'),
(28, NULL, 'GRATIS-132487', 'tersedia', '2025-08-04 08:04:17', '2025-09-03', 1, '2025-08-04 08:04:48'),
(29, NULL, 'GRATIS-9DD8C7', 'tersedia', '2025-08-04 08:06:34', '2025-09-03', 1, '2025-08-04 08:13:20'),
(30, NULL, 'GRATIS-CC5D67', 'tersedia', '2025-08-04 08:07:24', '2025-09-03', 1, '2025-08-04 08:08:57'),
(31, NULL, 'GRATIS-8746E5', 'tersedia', '2025-08-04 08:20:24', '2025-09-03', 1, '2025-08-04 08:31:38'),
(32, NULL, 'GRATIS-5E2C56', 'tersedia', '2025-08-04 10:44:53', '2025-09-03', 1, '2025-08-04 10:50:06'),
(33, NULL, 'GRATIS-345CE2', 'tersedia', '2025-08-04 10:50:27', '2025-09-03', 0, NULL),
(34, NULL, 'GRATIS-251935', 'tersedia', '2025-08-05 05:59:14', '2025-09-04', 1, '2025-08-05 06:10:53'),
(35, NULL, 'GRATIS-AD9085', 'tersedia', '2025-08-05 06:12:26', '2025-09-04', 1, '2025-08-05 06:13:08'),
(36, NULL, 'GRATIS-02A917', 'tersedia', '2025-08-05 06:21:20', '2025-09-04', 1, '2025-08-05 06:22:21'),
(37, NULL, 'GRATIS-9C896C', 'tersedia', '2025-08-05 06:37:13', '2025-09-04', 1, '2025-08-05 06:37:55');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

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
-- Indeks untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT untuk tabel `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=283;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT untuk tabel `order_voucher_usage`
--
ALTER TABLE `order_voucher_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT untuk tabel `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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
-- Ketidakleluasaan untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `fk_vouchers_on_user_delete` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
