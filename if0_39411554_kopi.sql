-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql113.infinityfree.com
-- Waktu pembuatan: 05 Agu 2025 pada 10.24
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
(1, 'slide_688df03b51863.jpg', 'â˜• Selamat Datang di Classic Coffee 789', '\"Satu Botol Kopi Klasik Yang Kamu Beli, 1000 Doa Dan Harapan Untuk Masa Depan Petani Lokal\".', 1, 1),
(2, 'slide_688df72c2be84.jpg', 'â˜• Selamat Datang di Classic Coffee 789', '\"Beli Satu Kurang, Beli Dua Nambah.\"', 1, 2),
(4, 'slide_6892105fefa31.jpg', '\"Hemat 10% dengan Ambil di Tempat!\"', 'ðŸŽ‰ Mau lebih hemat?\r\nDapatkan DISKON 10% khusus untuk pembelian dengan metode Ambil di Tempat (Pick Up)!\r\nðŸ“ Cukup pesan, datang, dan ambil langsung di lokasi kami â€” tanpa biaya kirim, tanpa ribet!\r\nðŸ“† Promo berlaku 3 - 31 Agustus 2025!\r\nðŸ’¬ Info Lokasi: Desa Kebanaran, Tamanwinangun RT 03/ RW 08 No.59\r\nYuk, manfaatkan kesempatan ini. Hematnya dapet, cepatnya juga iya!', 1, 3),
(5, 'slide_68920fb91370d.jpg', 'Promo Merdeka! Diskon 10% ðŸŽ‰', 'ðŸ”¥ Spesial 17 Agustus\r\nðŸ“ Ambil di tempat, langsung hemat\r\nðŸŽ Diskon 10% tanpa syarat ribet!\r\nðŸ“² Info: [kontak Anda]', 0, 4),
(6, 'slide_6892113e0743d.jpg', 'ðŸŽ‰ Promo Spesial 7â€¢8â€¢9 Agustus â€“ Diskon 10%!', 'ðŸ“† Hanya 3 Hari!\r\nðŸ›ï¸ Diskon 10% untuk pembelian Ambil di Tempat\r\nðŸ“ Tanpa ongkir, langsung hemat!\r\nâ³ Berlaku: 7 â€“ 9 Agustus 2025\r\nðŸ“² Info & pesanan: [kontak Anda]', 0, 5);

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
(225, NULL, 'Zulfa', 'Ambil Ditempat', '081578134267', '', '18000.00', '0.00', NULL, '0.00', '0.00', 'WhatsApp', 'cash', 'paid', 'https://api.whatsapp.com/send?phone=6289669505208&text=Halo+Classic+Coffee+789%2C+ada+pesanan+baru%3A%0A%0A-%3E+Caramel+Latte+%28Carla%29%0A+++Qty%3A+1+x+Rp+18.000+%3D+Rp+18.000%0A%0ASubtotal+Asli%3A+Rp+20.000%0ATotal+Diskon+Produk%3A+-Rp+2.000%0A%2ATotal+Akhir%3A+Rp+18.000%2A%0A%0ABerikut+data+pelanggan%3A%0ANama%3A+Zulfa%0ANo.+HP%3A+081578134267%0A%0A%2AJadwal%3A%2A%0ASelasa%2C+05+Aug+2025+-+Jam+15%3A30%0AMetode%3A+%2AAmbil+Ditempat%2A%0APembayaran%3A+%2ACASH+%28Bayar+di+Tempat%29%2A%0A%0AMohon+konfirmasinya.+Terima+kasih.%0A%28Ref+Order+ID%3A+225%29', '', 'Selasa, 05 Aug 2025 - Jam 15:30', '2025-08-05 08:18:16');

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
(230, 225, 97, 1, '20000.00');

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
  `image_url` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `discount_percentage`, `discount_methods`, `discount_name`, `image_url`, `category`) VALUES
(15, 'Hazelnut Latte (Ella)', '20000.00', 10, 'pickup', NULL, 'prod_688b21a691c58.jpg', 'minuman-kopi'),
(76, 'Paket Hemat Kopi', '29500.00', 10, 'pickup', NULL, 'prod_6880b9820fccc.png', 'paket-kopi'),
(77, 'Paket Kenyang Kopi', '33500.00', 10, 'pickup', NULL, 'prod_6880b9ab7a251.png', 'paket-kopi'),
(78, 'Paket Sharing Kopi', '33500.00', 10, 'pickup', NULL, 'prod_6880b9e2b9d40.png', 'paket-kopi'),
(79, 'Paket Sultan Kopi', '47500.00', 10, 'pickup', NULL, 'prod_6880b9f73064a.png', 'paket-kopi'),
(80, 'Paket Santai Kopi', '38000.00', 10, 'pickup', NULL, 'prod_6880b9c4bea7b.png', 'paket-kopi'),
(81, 'Paket Sultan Thai Tea', '36000.00', 10, 'pickup', NULL, 'prod_6880baca81b9c.png', 'paket-teh'),
(82, 'Paket Sharing Thai Tea', '22000.00', 10, 'pickup', NULL, 'prod_6880ba906e895.png', 'paket-teh'),
(83, 'Paket Kenyang Thai Tea', '22000.00', 10, 'pickup', NULL, 'prod_6880ba5f497d1.png', 'paket-teh'),
(84, 'Paket Santai Thai Tea', '27000.00', 10, 'pickup', NULL, 'prod_6880ba774cd39.png', 'paket-teh'),
(85, 'Paket Hemat Thai Tea', '18000.00', 10, 'pickup', NULL, 'prod_6880ba3fee0da.png', 'paket-teh'),
(86, 'Burger Mini Beef Patties', '11000.00', 10, 'pickup', NULL, 'prod_6880b6d3360a4.jfif', 'makanan'),
(87, 'Burger Mini Beef Slice', '8000.00', 10, 'pickup', NULL, 'prod_6880b6eac0bbe.jfif', 'makanan'),
(88, 'Burger Mini Chicken', '9000.00', 10, 'pickup', NULL, 'prod_6880b72ca99a9.jfif', 'makanan'),
(89, 'Special Burger Beef Patties', '20000.00', 10, 'pickup', NULL, 'prod_6880b7784d9c9.jfif', 'makanan'),
(90, 'Special Burger Beef Slice', '13000.00', 10, 'pickup', NULL, 'prod_6880b78234936.jfif', 'makanan'),
(91, 'Special Burger Chicken', '15000.00', 10, 'pickup', NULL, 'prod_6880b78e00b5f.jfif', 'makanan'),
(92, 'Dimsum', '15000.00', 10, 'pickup', NULL, 'prod_6880b73b6fdbc.jfif', 'makanan'),
(93, 'Spaghetti Bolognese', '15000.00', 10, 'pickup', NULL, 'prod_6880b75fcbc76.jfif', 'makanan'),
(95, 'Sakura Latte (Ralat)', '18000.00', 10, 'pickup', NULL, 'prod_688b21c67e47c.jpg', 'minuman-kopi'),
(96, 'Lychee Latte (Lyla)', '18000.00', 10, 'pickup', NULL, 'prod_688b21b5a8e7e.jpg', 'minuman-kopi'),
(97, 'Caramel Latte (Carla)', '20000.00', 10, 'pickup', NULL, 'prod_688b208fee4c4.jpg', 'minuman-kopi'),
(98, 'Kopi Susu Original', '18000.00', 10, 'pickup', NULL, 'prod_6880b858a9ead.jfif', 'minuman-kopi'),
(99, 'Kopi Susu Gula Aren', '21000.00', 10, 'pickup', NULL, 'prod_6880b84b871c9.jfif', 'minuman-kopi'),
(100, 'Milk Base Mangga', '17000.00', 10, 'pickup', NULL, 'prod_6880b8c563f5d.png', 'minuman-nonkopi'),
(101, 'Milk Base Red Velvet', '17000.00', 10, 'pickup', NULL, 'prod_6880b8daa54ba.jfif', 'minuman-nonkopi'),
(102, 'Thai Tea Original', '8000.00', 10, 'pickup', NULL, 'prod_6880b94c6baeb.jfif', 'minuman-nonkopi'),
(103, 'Thai Tea Lychee', '8000.00', 10, 'pickup', NULL, 'prod_6880b926948f1.png', 'minuman-nonkopi'),
(104, 'Thai Tea Milk', '8000.00', 10, 'pickup', NULL, 'prod_6880b938a6e0d.jfif', 'makanan'),
(105, 'Thai Tea Lemon', '8000.00', 10, 'pickup', NULL, 'prod_6880b90fc4a00.png', 'minuman-nonkopi'),
(110, 'Kopi V60', '15000.00', 10, 'pickup', NULL, 'prod_68889cc50b750.png', 'minuman-kopi');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT untuk tabel `order_voucher_usage`
--
ALTER TABLE `order_voucher_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
-- Ketidakleluasaan untuk tabel `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `fk_vouchers_on_user_delete` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
