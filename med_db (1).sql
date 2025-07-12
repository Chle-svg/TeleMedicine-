-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 12, 2025 at 06:42 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `med_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `appointment_type` enum('in_person','video') NOT NULL DEFAULT 'in_person',
  `location` varchar(255) DEFAULT NULL,
  `zoom_link` varchar(255) DEFAULT NULL,
  `chapa_payment_link` varchar(255) DEFAULT NULL,
  `payment_link` varchar(255) DEFAULT NULL,
  `payment_tx_ref` varchar(255) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `consult_fee` int NOT NULL DEFAULT '100',
  `rejection_reason` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `created_at`, `appointment_type`, `location`, `zoom_link`, `chapa_payment_link`, `payment_link`, `payment_tx_ref`, `payment_status`, `consult_fee`, `rejection_reason`) VALUES
(1, 47, 41, '2025-06-13', '14:39:00', 'accepted', '2025-06-12 18:37:03', 'in_person', 'lamberte', NULL, NULL, NULL, NULL, 'pending', 0, NULL),
(3, 47, 41, '2025-06-28', '14:47:00', 'accepted', '2025-06-12 18:44:10', 'in_person', 'shola tena tabiya', NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(4, 47, 42, '2025-06-28', '20:46:00', 'pending', '2025-06-12 18:46:22', 'in_person', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(5, 47, 44, '2025-06-13', '14:59:00', 'pending', '2025-06-12 18:57:27', 'in_person', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(6, 48, 46, '2025-06-20', '05:40:00', 'pending', '2025-06-12 19:38:45', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(7, 46, 46, '2025-06-11', '07:43:00', 'pending', '2025-06-12 19:40:36', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(8, 48, 41, '2025-06-21', '19:56:00', 'rejected', '2025-06-12 19:54:03', 'in_person', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(9, 48, 41, '2025-06-20', '05:31:00', 'accepted', '2025-06-13 18:29:43', 'video', NULL, 'https://zoom.us/j/9336222333?pwd=43884c22', NULL, NULL, NULL, 'pending', 100, NULL),
(10, 48, 41, '2025-06-20', '14:37:00', 'accepted', '2025-06-13 18:35:33', 'video', NULL, 'https://zoom.us/j/7300917936?pwd=9a61ef4b', NULL, NULL, NULL, 'pending', 100, NULL),
(11, 48, 41, '2025-06-19', '14:41:00', 'accepted', '2025-06-13 18:38:48', 'in_person', 'rtyv', NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(12, 48, 41, '2025-06-27', '15:02:00', 'accepted', '2025-06-13 19:00:26', 'in_person', 'shgxhgggg', NULL, NULL, 'https://checkout.chapa.co/checkout/payment_link_example_12', NULL, 'pending', 100, NULL),
(13, 48, 41, '2025-06-19', '15:07:00', 'accepted', '2025-06-13 19:05:33', 'video', NULL, 'https://zoom.us/j/7105965607', NULL, 'https://checkout.chapa.co/checkout/payment_link_example_13', NULL, 'pending', 100, NULL),
(14, 48, 41, '2025-06-19', '15:07:00', 'accepted', '2025-06-13 19:08:36', 'video', NULL, 'https://zoom.us/j/6937270394', NULL, NULL, NULL, 'pending', 0, NULL),
(15, 48, 41, '2025-06-14', '19:08:00', 'accepted', '2025-06-13 19:27:00', 'video', NULL, 'https://zoom.us/j/2603260216', NULL, NULL, NULL, 'pending', 0, NULL),
(17, 48, 41, '2025-06-21', '15:00:00', 'rejected', '2025-06-13 19:56:19', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(18, 48, 41, '2025-06-19', '16:40:00', 'rejected', '2025-06-13 20:34:37', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(19, 48, 41, '2025-06-28', '22:59:00', 'rejected', '2025-06-13 20:59:38', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(20, 48, 41, '2025-06-20', '17:26:00', 'accepted', '2025-06-13 21:24:08', 'video', NULL, 'https://zoom.us/j/3483285806', NULL, 'https://checkout.chapa.co/checkout/payment/bhVU6G1Xh47sPX8F8FrSZvoO0Q7o3f4fn8M0n1uyxYbsJ', 'appt20_1749854033', 'pending', 100, NULL),
(21, 48, 41, '2025-06-20', '17:26:00', 'accepted', '2025-06-13 22:36:27', 'video', NULL, 'https://zoom.us/j/2612789166', NULL, 'https://checkout.chapa.co/checkout/payment/W8LKgsMOqTsKnoVcIgFgV2LFKkUDm5KaIRYSFW4mSwfqy', 'appt21_1749854246', 'pending', 100, NULL),
(22, 48, 41, '2025-06-20', '21:49:00', 'accepted', '2025-06-13 22:49:31', 'video', NULL, 'https://zoom.us/j/2937691040', NULL, 'https://checkout.chapa.co/checkout/payment/9lwE1l5fbKr5CRZkm2wGeW5GdbTCYxoH1xdk4Im8dqm22', 'medcon_apt_22_1749855130', 'pending', 100, NULL),
(23, 48, 41, '2025-06-20', '20:57:00', 'accepted', '2025-06-13 22:57:13', 'video', NULL, 'https://zoom.us/j/4540668364', NULL, 'https://checkout.chapa.co/checkout/payment/CFpRaNV7zhPn4q5VWltsSOvEHi5LS6jbB0zMo7VpCC08P', 'appt23_1749855929', 'pending', 45, NULL),
(24, 48, 41, '2025-06-26', '19:03:00', 'accepted', '2025-06-13 23:01:47', 'video', NULL, 'https://zoom.us/j/8424410701', NULL, 'https://checkout.chapa.co/checkout/payment/NGNGV65CXYkp06uSrqVdMWUhmWWmEFAge2IXEyytrSRLp', 'appt24_1749856048', 'pending', 45, NULL),
(25, 49, 41, '2025-06-20', '21:28:00', 'accepted', '2025-06-13 23:28:06', 'video', NULL, 'https://zoom.us/j/4429758684', NULL, 'https://checkout.chapa.co/checkout/payment/X4uMXJAkt0E20px8f6Si7rejDe3d3rYa5lvicqvOEHIMj', 'appt25_1749858319', 'pending', 45, NULL),
(26, 49, 41, '2025-06-20', '23:20:00', 'accepted', '2025-06-14 00:20:45', 'in_person', 'arabesa', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/3qNqxV1S1tswv4rp6EYYrpL3Axooyl9xW9WwcbmxfBVMK', 'appt26_1749861979', 'pending', 100, NULL),
(27, 49, 41, '2025-06-20', '20:50:00', 'accepted', '2025-06-14 00:48:49', 'video', NULL, 'https://zoom.us/j/6683749641', NULL, 'https://checkout.chapa.co/checkout/payment/lSnyxXD8S2dRcHdSMDm3Az20SugWD2I3aAC79v8kFtAYA', 'appt27_1749862284', 'paid', 45, NULL),
(28, 49, 41, '2025-06-30', '22:10:00', 'accepted', '2025-06-14 01:10:58', 'video', NULL, 'https://zoom.us/j/6792394411', 'https://checkout.chapa.co/checkout/payment/dd3N8GQl3fui9AGVWEI6Zxnpda2E3zCA4uLDTlOCoxDfJ', 'https://checkout.chapa.co/checkout/payment/VvnU084r8Kp7vSWaUEbupYfY41ooQZyMk4sFLjGz7wDTB', 'appt_28_684d0e9e0ac11', 'paid', 45, NULL),
(29, 49, 41, '2025-07-06', '21:38:00', 'accepted', '2025-06-14 01:34:44', 'video', NULL, 'https://zoom.us/j/8735085479', NULL, 'https://checkout.chapa.co/checkout/payment/baMK5WPAIePhfxJzTEBP8xskB4tE0EO00Yldu1TQdimfv', 'appt29_1749865760', 'pending', 45, NULL),
(30, 50, 41, '2025-06-18', '12:00:00', 'accepted', '2025-06-14 01:49:12', 'video', NULL, 'https://zoom.us/j/7303804803', NULL, 'https://checkout.chapa.co/checkout/payment/mocl2V2IVdeTMDyZFaD9Vefgb4xMeDb59JGKYMZ7yxiJL', 'appt30_1749866218', 'pending', 45, NULL),
(31, 49, 41, '2025-06-26', '23:59:00', 'accepted', '2025-06-14 01:59:52', 'in_person', 'uuuuui', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/kXzO2qCfYOPMtgutVlHJM6TUNM1y8npqGWPN4BQsc14Z3', 'appt31_1749866666', 'pending', 45, NULL),
(32, 49, 41, '2025-06-27', '13:00:00', 'accepted', '2025-06-14 02:11:20', 'video', NULL, 'https://zoom.us/j/1722237265', NULL, 'https://checkout.chapa.co/checkout/payment/ADFhDj09qI7PWwUXc1BybNCadKsUTUk01ncjqx94CcRzH', 'appt32_1749869262', 'pending', 45, NULL),
(33, 49, 41, '2025-06-20', '12:47:00', 'accepted', '2025-06-14 02:47:34', 'in_person', 'ty', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/wamvMnV7IXKWYYA7STQF6jQ9L3OiHU0pVGY5Vsfom7Ad7', 'appt33_1749869696', 'pending', 45, NULL),
(34, 49, 41, '2025-06-28', '15:55:00', 'accepted', '2025-06-14 02:55:17', 'in_person', 'yuuuuuuuuuuuuuuuuuu', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/rqXf7ylQqLNr9NfCynz9kMmTI36rWdWyfHibnrPG2GHer', 'medconnect_684d02584a421_1749877336', 'pending', 45, NULL),
(35, 49, 41, '2026-04-28', '23:03:00', 'accepted', '2025-06-14 03:00:33', 'video', NULL, 'https://zoom.us/j/5341276435', NULL, 'https://checkout.chapa.co/checkout/payment/BCMSXzV0muh6WlxDKwfUctsw4N9rF5vb6t7srMtWn5LQR', 'appt35_1749870067', 'pending', 45, NULL),
(36, 49, 41, '2025-07-03', '19:07:00', 'accepted', '2025-06-14 04:00:30', 'in_person', 'ghghhjhjgjgjhlikujhgfdsfghujiolikujhgf', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/0jTnAWN75m9L3fv0l4HK6aTjNgjDhdsi21NMLWLlje9kh', 'appt36_1749880596', 'pending', 45, NULL),
(37, 52, 51, '2025-06-26', '05:14:00', 'accepted', '2025-06-25 09:12:36', 'in_person', 'CMC', NULL, 'https://checkout.chapa.co/checkout/payment/geO1WukyBbD245LzbezabtoH4hcPaG2OamyCbp4IIUPb2', 'https://checkout.chapa.co/checkout/payment/fOKqjV3qfOsCTdCtD2br3D3WD1EXDIKtnBZZg9AeirMT5', 'appt_37_685bbeb21eea4', 'paid', 1000, NULL),
(38, 52, 51, '2025-06-26', '05:14:00', 'accepted', '2025-06-25 09:14:38', 'in_person', 'atlas', NULL, 'https://checkout.chapa.co/checkout/payment/lHpXLrDTVQBgRLHNMi0rYqmB3hVn9ZshJ90cZx7Ks6U19', 'https://checkout.chapa.co/checkout/payment/FJAHdyWWzhq08ResjvXgwcVC7YCEvurCn98iIkby9s1JJ', 'appt_38_685bc114ebd32', 'paid', 1000, NULL),
(39, 52, 51, '2025-06-18', '05:31:00', 'accepted', '2025-06-25 09:27:11', 'video', NULL, 'https://zoom.us/j/6215888656', 'https://checkout.chapa.co/checkout/payment/VLCBvZNhBPYdMsIGEcZ22wRae8p2IexeJoL9SPodnZibd', 'https://checkout.chapa.co/checkout/payment/9VAWLeTIsc3BTRIgf1s4fuFzIJYPMW4Ton8CBsfyxSG6J', 'appt_39_685bc164cc599', 'paid', 1000, NULL),
(40, 52, 51, '2025-06-26', '05:34:00', 'accepted', '2025-06-25 09:31:42', 'in_person', 'ooo', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/AzqBuKEeWYBPxs8hnZHasKU4BOw52zEwQRKomRysp6htl', 'appt_40_685bc2337b13d', 'paid', 1000, NULL),
(41, 52, 51, '2025-06-26', '05:34:00', 'accepted', '2025-06-25 09:32:17', 'in_person', 'tytyy', NULL, 'https://checkout.chapa.co/checkout/payment/vjCD01dp0Wt3UQGC9qepwVG06UBogqaxM8BDU59ZjuPg5', 'https://checkout.chapa.co/checkout/payment/SIfLh26O1aalonGtl8sIGyvI0JRTlN314mTrE4fHZmQdr', 'appt_41_685bc29491074', 'paid', 1000, NULL),
(42, 52, 51, '2025-06-26', '05:37:00', 'accepted', '2025-06-25 09:37:03', 'video', NULL, 'https://zoom.us/j/4974724960', 'https://checkout.chapa.co/checkout/payment/V5ypfdtXvu7SYZIc3sONEE4vM5Sa7gOMziGW3sAM5XR1R', 'https://checkout.chapa.co/checkout/payment/Tm6K3t7HS7Ng3V01U2jERHiDGWvNjqyIggNKuwUcy2WwX', 'appt_42_685bc361a2173', 'paid', 1000, NULL),
(43, 52, 51, '2025-06-26', '07:46:00', 'accepted', '2025-06-25 09:46:48', 'in_person', 'nnnnnnnnnnn', NULL, 'https://checkout.chapa.co/checkout/payment/ZFiiVx4xEymmcM5tH00z5Iuj5aJnzNT5qrE19a1JprWU5', 'https://checkout.chapa.co/checkout/payment/oYwoJrvaehVjey3AgPwNPBAsrthyCJYdxOMzovFcen8WY', 'appt_43_685bc5ac77967', 'paid', 1000, NULL),
(44, 52, 51, '2025-06-27', '08:58:00', 'accepted', '2025-06-25 09:58:07', 'in_person', 'lllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllll', NULL, 'https://checkout.chapa.co/checkout/payment/8pyCJtx6CvX9Prjkn9fq5XEuMeyRZU8nsmjAgfhLtutGB', 'https://checkout.chapa.co/checkout/payment/DwqJr0ErSCeWXHFNPtwhLDNtgcDY4SrKwOqsAtZxpdi7k', 'appt_44_685bc8513c85a', 'paid', 1000, NULL),
(45, 52, 51, '2025-06-26', '10:02:00', 'accepted', '2025-06-25 10:02:45', 'in_person', 'tttttttttttttttttttttttttttttttttt', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/xC3XNzAEw9cLZy9XthZk0Qrc90VidmbRsYFwJcbHnUrNa', 'appt_45_685bc9adcc36a', 'paid', 1000, NULL),
(46, 52, 51, '2025-06-19', '09:05:00', 'accepted', '2025-06-25 10:05:49', 'in_person', 'tyty', NULL, 'https://checkout.chapa.co/checkout/payment/yXD63KAhToM3XHSfwI3nRCNJDHa6SNjw5lJYSCvdFzmhC', 'https://checkout.chapa.co/checkout/payment/HH07KtfUu1dV5Glk5EBMin4Pf3W6LzgVB85VzdjDmjnO2', 'appt_46_685bca266a5b0', 'paid', 1000, NULL),
(47, 52, 51, '2025-06-24', '06:16:00', 'accepted', '2025-06-25 10:14:14', 'in_person', 't', NULL, 'https://checkout.chapa.co/checkout/payment/IISmubZtRfVqyZwd0b0MYvQtbYKJKVlNPVygaJBrloycC', 'https://checkout.chapa.co/checkout/payment/tNjKSIGXqhV5aOUq9t9N3dlIKoDXRZ7yEInCu3tEriIEE', 'appt_47_685bcc1089e8b', 'paid', 1000, NULL),
(48, 52, 51, '2025-06-26', '06:21:00', 'accepted', '2025-06-25 10:18:39', 'video', NULL, 'https://zoom.us/j/6453633108', 'https://checkout.chapa.co/checkout/payment/BBPZaGZnLXV2WJOI3wjiJsItiu2n5FoPcWSpXSnFjNaYm', 'https://checkout.chapa.co/checkout/payment/F4j3jWuMXwSzQSVuDelA9IW1z2Fhr7WDEaqJkauSud93R', 'appt_48_685bf4d59826e', 'paid', 1000, NULL),
(49, 52, 51, '2025-06-26', '08:50:00', 'accepted', '2025-06-25 12:48:39', 'in_person', 'aaaaaaa', NULL, 'https://checkout.chapa.co/checkout/payment/bJCZxzjkidZ8zOqUCorEvgi0sY40cicHUOIHLqyQmaB5l', 'https://checkout.chapa.co/checkout/payment/VJRlCFIxSAVBzc0EoAKshfyeVQ7cPIKZuxXqQkP6EozAL', 'appt_49_685d66ec34df0', 'paid', 1000, NULL),
(50, 52, 51, '2025-06-26', '09:44:00', 'rejected', '2025-06-25 13:42:46', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(51, 52, 51, '2025-06-27', '10:37:00', 'rejected', '2025-06-25 14:35:01', 'in_person', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(52, 52, 51, '2025-06-28', '11:19:00', 'accepted', '2025-06-26 15:15:56', 'video', NULL, 'https://zoom.us/j/4348517879', 'https://checkout.chapa.co/checkout/payment/Jl9wGws6QrHTEe2IvJM8vRAAXyss6cZ1lnLNreV0lVmnS', 'https://checkout.chapa.co/checkout/payment/LyvQzI9CLIzlF1ITEcftoGeXkDE3nRV8XKvzDiyRImSP8', 'appt_52_685d662153582', 'paid', 1000, NULL),
(53, 52, 51, '2025-06-28', '11:19:00', 'accepted', '2025-06-26 15:22:29', 'video', NULL, 'https://zoom.us/j/7273033758', 'https://checkout.chapa.co/checkout/payment/efcxjcMwARRl2zPyPOaThUOf8WTg3vqHyRrlkXTsrDdrt', 'https://checkout.chapa.co/checkout/payment/8nWr2yMNKEUPIR4RtuUrMAVANd5Xy8Tlcm14VkUi0t4Ds', 'appt53_1750951901', 'pending', 1000, NULL),
(54, 52, 51, '2025-06-27', '23:31:00', 'accepted', '2025-06-26 15:31:09', 'in_person', 'tg', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/MWdDIS3Ak4LBVgMvqTu1feoLiLqd05XiPiDWyIZZkMhEk', 'appt54_1750952130', 'pending', 1000, NULL),
(55, 52, 51, '2025-06-27', '11:39:00', 'accepted', '2025-06-26 15:37:38', 'in_person', 'o', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/bhsTwmjamdKUZWXVmXgDmVGj4YJmTcW6vbu0jwoDVMObk', 'appt55_1750952990', 'pending', 1000, NULL),
(56, 52, 51, '2025-06-27', '00:00:00', 'accepted', '2025-06-26 15:50:57', 'in_person', 'op', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/TB385POKRuDsfqCZvtWwD8Zu3TXBdbxnyssy1iIr0LTaB', 'appt56_1750953091', 'pending', 1000, NULL),
(57, 52, 51, '2025-06-27', '18:30:00', 'accepted', '2025-06-28 17:29:39', 'video', NULL, 'https://zoom.us/j/9375063869', 'https://checkout.chapa.co/checkout/payment/yTWtJSlkD14tCji3XmPvC2V30zIBIIrfy19EwROSo0QfR', 'https://checkout.chapa.co/checkout/payment/AabcYu8MDCpFQ5EzXmOwaqkWcLYAsdtF9hY5NmBFbmeoC', 'appt_57_686026ca640ca', 'paid', 1000, NULL),
(58, 52, 51, '2025-06-25', '19:00:00', 'accepted', '2025-06-28 17:42:35', 'video', NULL, 'https://zoom.us/j/2373829243', 'https://checkout.chapa.co/checkout/payment/c9hSt4RaOqJonDzMO12TfiZz7ubgAvhsTU58SDgEefTWv', 'https://checkout.chapa.co/checkout/payment/i4HjOmaNIavx7bNpbYZH3SdskBSFjhjD3FsVGEYqVx3fD', 'appt_58_686029c6cd5c1', 'paid', 1000, NULL),
(59, 52, 51, '2025-06-27', '18:00:00', 'accepted', '2025-06-28 17:52:02', 'video', NULL, 'https://zoom.us/j/8512744969', 'https://checkout.chapa.co/checkout/payment/6PYrQ1WbXkAkznTStiWKA7gaIwW9vKxVw3WQNxrKswhDO', 'https://checkout.chapa.co/checkout/payment/qh6a2mtoKO1QNs2VEXASDojhmvODMFNFH38IiXZ0e4LQT', 'appt_59_68602c359feb8', 'paid', 1000, NULL),
(60, 52, 51, '2025-06-30', '13:01:00', 'accepted', '2025-06-28 17:56:34', 'in_person', 'kkkkkk', NULL, 'https://checkout.chapa.co/checkout/payment/CKehIVO5KiZAMO3NVOoBQp40QkN5phX24CYdnYvBSnjDw', 'https://checkout.chapa.co/checkout/payment/zuBVjdsYkFc23xraSDKzI2HsmnYTEgyCzI05pSZOsPQKD', 'appt_60_68602cf1ad84f', 'paid', 1000, NULL),
(61, 52, 51, '2025-06-30', '19:05:00', 'accepted', '2025-06-28 18:05:05', 'video', NULL, 'https://zoom.us/j/3078727518', 'https://checkout.chapa.co/checkout/payment/D52NPxE4dlLosnlnFQqfdNbJkIXxmTw1woFxenQee1g41', 'https://checkout.chapa.co/checkout/payment/RTJCOfMLGGiQTr36hYevCTVZHO5VPZhIqLDi4RsyFdISG', 'appt_61_68602f57796c8', 'paid', 1000, NULL),
(62, 52, 51, '2025-06-27', '20:15:00', 'accepted', '2025-06-28 18:15:24', 'video', NULL, 'https://zoom.us/j/9249772526', 'https://checkout.chapa.co/checkout/payment/aoPfSkyS8J75S1muZhvbkXdaqjfpXfRbLpkbZDtwtpD7M', 'https://checkout.chapa.co/checkout/payment/T6GvBRLSu22LsvAAR8Nq7VO7WWzVndhRN7j1GqoYOsbz9', 'appt62_1751134697', 'pending', 1000, NULL),
(63, 52, 51, '2025-06-30', '14:24:00', 'accepted', '2025-06-28 18:20:14', 'video', NULL, 'https://zoom.us/j/8866067854', 'https://checkout.chapa.co/checkout/payment/V8IYq4z3l5UPMjzsRjXzy1kxx46MNMyc0BIKZ48MdD1v1', 'https://checkout.chapa.co/checkout/payment/x74QQq221gZWRRQj1DGT686wNguJm7cKezAKhz7tG1qsC', 'appt63_1751134921', 'pending', 1000, NULL),
(64, 52, 51, '2025-07-04', '18:23:00', 'accepted', '2025-06-28 18:24:00', 'video', NULL, 'https://zoom.us/j/5079108847', 'https://checkout.chapa.co/checkout/payment/qz7qRjORffONft01F7diYigQSFwngGcWBnMSKBexMElsO', 'https://checkout.chapa.co/checkout/payment/IrhOJ9zwJ64Jfapf5Q2UuUE13FG8C9XS9jz2pPTX4z0dW', 'appt64_1751135193', 'pending', 1000, NULL),
(65, 52, 51, '2025-07-10', '14:31:00', 'accepted', '2025-06-28 18:26:55', 'video', NULL, 'https://zoom.us/j/5886809788', 'https://checkout.chapa.co/checkout/payment/2KOy3dBwRkVSRDwPNHi6ayzIZRtqSuVgYimk5CyHxZCOD', 'https://checkout.chapa.co/checkout/payment/DsAJqJkrYPv1QFBpeAgRmh1sk108DdhHsPekfxaPPcZKN', 'appt_65_6860346e7f169', 'paid', 1000, NULL),
(66, 52, 51, '2025-07-04', '20:33:00', 'accepted', '2025-06-28 18:33:34', 'video', NULL, 'https://zoom.us/j/3826927474', 'https://checkout.chapa.co/checkout/payment/FyKAX0X1e5ASES1NK5Aq1qIErScvvvcE1m7qHvBsNtDge', 'https://checkout.chapa.co/checkout/payment/QgnktN5Wn5YjrF2kDbqwHNe5KdLfXc8574ZC5wAdMpqdL', 'appt66_1751135772', 'pending', 1000, NULL),
(67, 52, 51, '2025-07-04', '20:37:00', 'accepted', '2025-06-28 18:37:26', 'video', NULL, 'https://zoom.us/j/2990352057', 'https://checkout.chapa.co/checkout/payment/iC1sWjG5PYvrLHsZAclabe7Ok6fMaEdt7brjDzNdRUuuv', 'https://checkout.chapa.co/checkout/payment/4y4cJo00b2VpMulgaveO6E2RwvXPc2MSJE9euTlz51aD7', 'appt_67_68603b1251e27', 'paid', 1000, NULL),
(68, 52, 51, '2025-07-04', '13:40:00', 'accepted', '2025-06-28 18:41:02', 'in_person', 'gh', NULL, 'https://checkout.chapa.co/checkout/payment/NQgGloeJlKkMbn9Lshtx72PtFHmgVEb9TTTP17S1dKQjx', 'https://checkout.chapa.co/checkout/payment/MMbDe5r6KmWSLrgqIQXXMzvZTFwpbCmblNmBGqi20BKRf', 'appt_68_6860382dd9be2', 'pending', 1000, NULL),
(69, 52, 51, '2025-07-04', '20:46:00', 'accepted', '2025-06-28 18:46:12', 'video', NULL, 'https://zoom.us/j/1717565806', 'https://checkout.chapa.co/checkout/payment/AJPUhNSIeoLHh8oYxrriu0dznTu9XxEPvBjbczis8VHLL', 'https://checkout.chapa.co/checkout/payment/j9C9pxsGR8lo2ICuvKeXUlBt5xZK14i66DW93fZvcH7Uc', 'appt69_1751136442', 'pending', 1000, NULL),
(70, 52, 51, '2025-07-12', '18:48:00', 'accepted', '2025-06-28 18:48:58', 'video', NULL, 'https://zoom.us/j/8069480041', 'https://checkout.chapa.co/checkout/payment/TL52WiAXhQmf2FaYyaC7MEeTma7ZTfwmGL0qFJD6oN7cv', 'https://checkout.chapa.co/checkout/payment/iqe0U0ImKA9ervR0oX7mIJxarukPaJEdDeAHn3eXdWVUH', 'appt_70_686039fae1aaa', 'paid', 1000, NULL),
(71, 52, 51, '2025-07-12', '19:59:00', 'accepted', '2025-06-28 18:54:01', 'in_person', 'gmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm', NULL, 'https://checkout.chapa.co/checkout/payment/qW4spF4mNMvQt0RuZLVIgABysrrGH2pdQSh5cLwIKrumN', 'https://checkout.chapa.co/checkout/payment/UiRxmzWpzpMwsrSFs6VRbqnuYWxgTXkK6vE741t91EZQs', 'appt_71_6866ee3447263', 'paid', 1000, NULL),
(72, 52, 51, '2025-06-28', '14:02:00', 'accepted', '2025-06-28 18:58:42', 'in_person', 'u', NULL, 'https://checkout.chapa.co/checkout/payment/O4Vmy2ff9cySeZgenhjhpyIVkr99R5vdmYCyhQ8O9L7H9', 'https://checkout.chapa.co/checkout/payment/sxuXxs94HKdT3Gd0nFOvp2tNnJSeATucmduulhvhcbV5c', 'appt72_1751137236', 'pending', 1000, NULL),
(73, 53, 54, '2025-07-05', '22:08:00', 'accepted', '2025-06-28 19:08:37', 'in_person', 'shola', NULL, 'https://checkout.chapa.co/checkout/payment/w39vYLsbgDwBIXSOAMa4yLoJIktUD3ZgwedOkamACteph', 'https://checkout.chapa.co/checkout/payment/azrwk0KpEvtherLxbJdxZNGN2ZS5PG3XhcnVPUiCKRVJw', 'appt_73_68603dd39e89a', 'paid', 100, NULL),
(74, 53, 54, '2025-07-04', '21:14:00', 'accepted', '2025-06-28 19:14:32', 'in_person', 'yy', NULL, 'https://checkout.chapa.co/checkout/payment/mbICdlId3DttTt0BGG8J2zICIOUaH0DvqbHR7xdORSeBf', 'https://checkout.chapa.co/checkout/payment/lwDctalIof6Vxnuu8Jwkns7dGbKqYVvjoAgtNU2BnK86W', 'appt_74_68604036bd1bc', 'paid', 100, NULL),
(75, 53, 54, '2025-06-27', '18:23:00', 'accepted', '2025-06-28 19:23:49', 'in_person', 'uuuuu', NULL, 'https://checkout.chapa.co/checkout/payment/bkgN05BBu2uyKfodOD7o7VynCv9hVZPM7TChjg61qbu5M', 'https://checkout.chapa.co/checkout/payment/G8xSa2ON8r8mZFiwHIBS2SWDXXl7aKFN0J0YafWyUYeEh', 'appt_75_6860415eb27de', 'paid', 100, NULL),
(76, 52, 54, '2025-07-08', '15:45:00', 'accepted', '2025-06-28 19:41:02', 'video', NULL, 'http://localhost/doctor/join_meeting.php?room=medconnect_appointment_76_d32f4959', NULL, 'https://checkout.chapa.co/checkout/payment/dzaHPOllwOw99iC1c4tGAvBqvCc9LbypSST5IDSh4WRCd', 'appt76_1751142960', 'pending', 100, NULL),
(77, 52, 54, '2025-07-08', '23:44:00', 'accepted', '2025-06-28 20:45:03', 'video', NULL, 'http://localhost/doctor/join_meeting.php?room=medconnect_appointment_77_45b55503', 'https://checkout.chapa.co/checkout/payment/uL8u8Q1JIetF3ssuv4Z2y0n6nnNVPWhnpYzX0bGf4ST29', 'https://checkout.chapa.co/checkout/payment/q7qSoWtlNCzPNJU3olLOQJIGwwanUfgw23XBiEP0uli60', 'appt_77_686054a78d312', 'paid', 100, NULL),
(79, 52, 54, '2025-07-10', '17:10:00', 'accepted', '2025-06-28 21:05:21', 'video', NULL, 'http://localhost/online_medication2/doctor/join_meeting.php?room=medconnect_appointment_79_89664eae', NULL, 'https://checkout.chapa.co/checkout/payment/LtnEANHZ1XPJNamEXIHHTlgUtSTXPztfotshV6xxNO9f9', 'appt79_1751144906', 'pending', 100, NULL),
(80, 52, 54, '2025-07-03', '22:21:00', 'accepted', '2025-06-28 21:21:58', 'in_person', 'm', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/n9BkpD5D8dc4kovHSD2LnhiuwmZ9IoiObkzA4iFnxjkXo', 'appt80_1751145761', 'pending', 100, NULL),
(81, 52, 54, '2025-07-05', '17:22:00', 'accepted', '2025-06-28 21:22:58', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_81_1327a399', 'https://checkout.chapa.co/checkout/payment/bmLjSE78vevWbFtWIvApYsyYHNMTJ1LZWKN1J5WYr4i1e', 'https://checkout.chapa.co/checkout/payment/8VutbZu7JImoGQErulcw5050IMuqvmxmkk5zlET48yapY', 'appt81_1751146510', 'pending', 100, NULL),
(82, 52, 54, '2025-07-11', '22:36:00', 'accepted', '2025-06-28 21:36:10', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_82_ec2d44be', NULL, 'https://checkout.chapa.co/checkout/payment/8gfACdu7DDpbNeTBioHTLUsLOcbrHVFwQzcFgE5Uh6Ii0', 'appt_82_686952e79f626', 'paid', 100, NULL),
(83, 52, 54, '2025-07-12', '17:52:00', 'accepted', '2025-06-28 21:49:34', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_83_0a50d21c', NULL, 'https://checkout.chapa.co/checkout/payment/MncSateG2A1d5IzYIz3u2EJeMz9OxJmGLZPOcEUoE8VmW', 'appt_83_68694fb8aee08', 'paid', 100, NULL),
(84, 52, 54, '2025-07-01', '22:00:00', 'accepted', '2025-06-28 21:56:17', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_84_c245bc12', 'https://checkout.chapa.co/checkout/payment/g6u4eEUUoxGLSMJIDrxCW02Wn5GRmfWlSUgwEd6YY24D1', 'https://checkout.chapa.co/checkout/payment/WUYg8myEATzoOt234TI4eH5uIBuvHjob7AjGhXzUFdmjq', 'appt_84_686065306912b', 'paid', 100, NULL),
(85, 52, 51, '2025-07-31', '23:07:00', 'accepted', '2025-07-03 21:01:18', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_85_1c0b7bcf', 'https://checkout.chapa.co/checkout/payment/87yPiPgPxbvBYl9DSAQ3NbDGlggDgDzJwz9bD7l7715Q8', 'https://checkout.chapa.co/checkout/payment/5wBxUdLfczEwIOLQx8dyyNt0DwDjpWuyc44EE1o3YxX4n', 'appt_85_6866f067b9123', 'paid', 1000, NULL),
(86, 52, 51, '2025-08-01', '22:10:00', 'accepted', '2025-07-03 21:05:25', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_86_5c3b7feb', 'https://checkout.chapa.co/checkout/payment/sKhJsczbcezrThlCZ3iPt8Yl5KlSVjmsQktVeEcEwbllY', 'https://checkout.chapa.co/checkout/payment/qTTU8a4rMjVoEPgjDj2pQuxGCCflIe7hIJKFIpJhvyQ84', 'appt_86_6866f0af1bbc2', 'paid', 1000, NULL),
(87, 59, 58, '2025-07-25', '20:14:00', 'accepted', '2025-07-03 21:12:55', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_87_bedbe229', 'https://checkout.chapa.co/checkout/payment/npjPxYAueljnQhO6QMh8OCXS8Q3DRcGORcpNeXeWaK9Bv', 'https://checkout.chapa.co/checkout/payment/wSwsM9rUvrc650IGQVTz9r1FjLojKQwviJ8iwMxgYFZSz', 'appt_87_6866f26feec4c', 'paid', 500, NULL),
(88, 52, 41, '2025-07-25', '05:55:00', 'accepted', '2025-07-05 09:51:07', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_88_96a2595d', 'https://checkout.chapa.co/checkout/payment/fw4oRoQfAPzNT3cva6DbgimzwCNkS6fmCxag0AXs5DTXt', 'https://checkout.chapa.co/checkout/payment/Et3GK2s6XVAzGjKZHYLpE86N43YPfv5rUNi5NtzezmKSA', 'appt_88_6868f602c63f6', 'paid', 45, NULL),
(89, 52, 41, '2025-07-26', '01:39:00', 'rejected', '2025-07-05 14:39:11', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(90, 52, 41, '2025-08-07', '10:52:00', 'accepted', '2025-07-05 14:46:45', 'in_person', 'sssssssssssssssssssssss', NULL, NULL, 'https://checkout.chapa.co/checkout/payment/OWwK5QCbHH4QNEtHIygmKCCXfI4s20wlCLYF9h7NnfDLW', 'appt_90_68694eb8b08ea', 'paid', 45, NULL),
(91, 52, 41, '2025-08-07', '10:52:00', 'rejected', '2025-07-05 15:13:15', 'in_person', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(92, 52, 41, '2025-07-31', '11:40:00', 'accepted', '2025-07-05 15:37:09', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_92_7d9074ac', NULL, 'https://checkout.chapa.co/checkout/payment/UZp9fRinJSo2l0LqB92ZqxfAlEbfynhPfFRl8qt88mwj1', 'appt_92_68694f03d8fd5', 'paid', 45, NULL),
(93, 52, 41, '2025-07-31', '18:00:00', 'accepted', '2025-07-05 16:21:45', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_93_3223e48c', 'https://checkout.chapa.co/checkout/payment/buY9xLUG6iwMbUbNFmH1hQ6FSyze2ZaMQWup2klCCuLZ3', 'https://checkout.chapa.co/checkout/payment/gpNuUHKCUlNgiCYnznQuWBuS5RSja0vG5v7QDm2VTwTZn', 'appt_93_68695225af575', 'pending', 45, NULL),
(94, 52, 41, '2025-08-05', '23:55:00', 'accepted', '2025-07-05 16:30:13', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_94_7fcf5684', 'https://checkout.chapa.co/checkout/payment/TPoaTKfGjoKq6FmfdW75Nvvahc24CHCh3jUtoqNj0tfX8', 'https://checkout.chapa.co/checkout/payment/xbBAZ4gyeZSrMt5zBRlEPSIbh5P1xoMELUtWzWX0DjSaQ', 'appt_94_686953352733c', 'paid', 45, NULL),
(95, 52, 60, '2025-10-09', '08:00:00', 'accepted', '2025-07-05 17:20:06', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_95_914e81e0', 'https://checkout.chapa.co/checkout/payment/b9hxfrKld2fwHSPFCGkzq0mbiX2UfPfXN1YWsx71rRTpn', 'https://checkout.chapa.co/checkout/payment/D8qwQYVZAZtYCXGT1fQhEZdeMb5Tei3zxwVAzkWkb1ktu', 'appt_95_68695f0fafb4d', 'paid', 1, NULL),
(96, 52, 41, '2025-07-31', '13:24:00', 'accepted', '2025-07-06 17:22:42', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_96_88360b26', NULL, 'https://checkout.chapa.co/checkout/payment/g49znUJzQ0oNBwlqVgsi0AgPjx9svEHdDcU7YxzzKwxeH', 'appt_96_686ab12179fbf', 'paid', 45, NULL),
(97, 52, 41, '2025-08-09', '20:27:00', 'accepted', '2025-07-06 17:28:06', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_97_12ee344b', 'https://checkout.chapa.co/checkout/payment/Iddn4JMkBIeY6kykOCrq0fUQXgGcdcuN3h8WMOI2Nkbo1', 'https://checkout.chapa.co/checkout/payment/nASfq737jqIhP3g6NZ7gR9NetCEjct9IeWoRJFxzuuHph', 'appt_97_686ab253cea9a', 'paid', 45, NULL),
(98, 52, 41, '2025-08-08', '14:37:00', 'accepted', '2025-07-06 17:37:28', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_98_3f3dc1bf', 'https://checkout.chapa.co/checkout/payment/gM354xV8Vcl322hFkcV8nK49vJgrlEJ6KdQaxy5ZLtQZ5', 'https://checkout.chapa.co/checkout/payment/JjbT9dE7oqya8Oks4eN6Je3jSouEvDzxjKB5eMjvw1BcE', 'appt_98_686ab49931538', 'paid', 45, NULL),
(99, 52, 41, '2025-08-08', '14:37:00', 'accepted', '2025-07-06 17:38:09', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_99_e6686ddf', 'https://checkout.chapa.co/checkout/payment/hWK2htiamUsLN76ynCb7E2qrK7NVekjG3U3hVE2FLj0yp', 'https://checkout.chapa.co/checkout/payment/N1mCWc7VJEmk2tBKwWcq1bD2ZDX3LyKLQzkclQrHiIj83', 'appt_99_686ab658409d3', 'paid', 45, NULL),
(100, 52, 41, '2025-08-09', '18:00:00', 'accepted', '2025-07-06 17:57:29', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_100_a92757c6', 'https://checkout.chapa.co/checkout/payment/FGqc1ox7jf8V5vVKXLmHLjqqCTGNIoNPkrL7QDeEtEhDv', 'https://checkout.chapa.co/checkout/payment/6hJr8u1gShSys30CfyQ5lsruR6V2qjEj8VRSM4Sx6LgKf', 'appt_100_686abb18dca5a', 'paid', 45, NULL),
(101, 52, 41, '2025-08-09', '03:33:00', 'pending', '2025-07-07 07:27:59', 'in_person', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(102, 63, 62, '2025-08-01', '06:34:00', 'accepted', '2025-07-08 09:35:49', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_102_58a8fec3', 'https://checkout.chapa.co/checkout/payment/MrWseEJtkgIHPZnjHUbMUBtoO1m4z3k4DWoDPokrrueLB', 'https://checkout.chapa.co/checkout/payment/7BW7jI7bYp7VW2vQqfaCmgBkXy7C5PTS5BBJhzUCAMGGv', 'appt_102_686ce791d1868', 'paid', 600, NULL),
(103, 63, 62, '2025-07-31', '11:53:00', 'accepted', '2025-07-08 09:54:04', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_103_0faf2e80', 'https://checkout.chapa.co/checkout/payment/TRKIDWqFmj0FIk8QXwclQ0y9w9WeGcJvenT1YWBdYx9fQ', 'https://checkout.chapa.co/checkout/payment/daVCOD62XOgqII2xwWzdkljz1jt2nSE5rncBlQ0Q7Szmu', 'appt_103_686ceb1545daf', 'paid', 600, NULL),
(104, 63, 62, '2025-08-09', '06:15:00', 'accepted', '2025-07-08 10:11:54', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_104_82f9ed8a', 'https://checkout.chapa.co/checkout/payment/P3bUD7f9g9JSqkcPWh6tL3gZNtZEgcsKqwkGtdGdLqS6q', 'https://checkout.chapa.co/checkout/payment/RAvbcPi2KJpwfaTyCfk3KBP7dDMuzPsEusS59CurDgDXP', 'appt_104_686cf2a33ca0c', 'paid', 600, NULL),
(105, 63, 62, '2025-08-25', '05:53:00', 'rejected', '2025-07-11 09:09:38', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, 'kxdufjbcm'),
(106, 63, 62, '2025-07-25', '17:14:00', 'rejected', '2025-07-11 09:28:01', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, 'h'),
(107, 63, 62, '2025-07-12', '05:31:00', 'rejected', '2025-07-11 09:29:11', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, 'k'),
(108, 63, 62, '2025-07-12', '05:31:00', 'accepted', '2025-07-11 09:30:14', 'video', NULL, 'https://meet.jit.si/medconnect_appointment_108_1d61702c', 'https://checkout.chapa.co/checkout/payment/8FCG3kA2egGyfLS6lW5gccPMMspsX83xzUc3rjjEtQkYn', 'https://checkout.chapa.co/checkout/payment/ZY3Ufl9euXBSNWg9vEM0pgibuwryHkk9EN1uxhsq6nwGc', 'appt_108_6870dde989a0c', 'paid', 600, NULL),
(109, 65, 64, '2025-07-09', '23:30:00', 'rejected', '2025-07-11 12:23:41', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, 'am not able on that time'),
(110, 65, 64, '2025-07-16', '23:30:00', 'accepted', '2025-07-11 12:26:54', 'video', NULL, 'https://meet.jit.si/TeleMedicine_appointment_110_b2fb5abc', 'https://checkout.chapa.co/checkout/payment/2fHBKHQMmxd0mJFnWfmW7eD5xKUYgUk8dxIyjnoQd90TM', 'https://checkout.chapa.co/checkout/payment/jhmjRkE3mJAQNPruL7P89ZnNNOrgNfK4UHPNFlKavP9OB', 'appt_110_687103d0aa5d3', 'paid', 400, NULL),
(111, 65, 64, '2025-07-10', '11:49:00', 'pending', '2025-07-11 12:47:06', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, NULL),
(112, 68, 67, '2025-07-15', '15:00:00', 'rejected', '2025-07-12 09:57:18', 'video', NULL, NULL, NULL, NULL, NULL, 'pending', 100, 'am taken'),
(113, 68, 67, '2025-07-16', '14:20:00', 'accepted', '2025-07-12 10:21:55', 'video', NULL, 'https://meet.jit.si/TeleMedicine_appointment_113_ab07067e', 'https://checkout.chapa.co/checkout/payment/QSGgv6cr1IxzF3cEUu4FhLiMAQkS2ZJdaihY92hzaXLLL', 'https://checkout.chapa.co/checkout/payment/v5VpBAP35O2sh6W3Oyvr1zKCd9G0stfV3WhSrhDjcgp7R', 'appt_113_687238f889ee9', 'paid', 450, NULL),
(114, 68, 67, '2025-07-16', '16:00:00', 'accepted', '2025-07-12 11:12:37', 'video', NULL, 'https://meet.jit.si/TeleMedicine_appointment_114_06146a99', NULL, 'https://checkout.chapa.co/checkout/payment/lXgjFZhJrPPQw1afdsiyIixQAC2AhVwCkRPNEGCCgqACO', 'appt114_1752318883', 'pending', 450, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

DROP TABLE IF EXISTS `doctor`;
CREATE TABLE IF NOT EXISTS `doctor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `specialty` varchar(255) NOT NULL,
  `experience` text NOT NULL,
  `consult_fee` decimal(10,2) NOT NULL,
  `bio` text,
  `cv` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`id`, `user_id`, `specialty`, `experience`, `consult_fee`, `bio`, `cv`, `created_at`) VALUES
(1, 41, 'Cardiologist', '45', '45.00', '45', '1749727714_1749261912_Apply for Ethiopian Passport Online.pdf', '2025-06-12 07:28:46'),
(2, 42, '', '', '0.00', '', '1749727655_1749261912_Apply for Ethiopian Passport Online.pdf', '2025-06-12 07:29:14'),
(3, 44, '', '5555', '0.00', '', '1749733086_1749263481_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 09:11:22'),
(4, 46, 'Pediatrician', '23', '1995.00', '0', 'CV_1749746033_8926.pdf', '2025-06-12 12:34:57'),
(5, 51, '0', '4', '1000.00', 'am a cardiologiest sepcilest on the health secvtor for the last 4 year am graduated from univeristy of unity ', 'CV_1750839830_4949.pdf', '2025-06-25 04:26:38'),
(6, 54, 'General Practitioner', '10', '100.00', 'my name is eden am a docotr for the last 10 year ', 'CV_1751137587_8070.pdf', '2025-06-28 15:07:37'),
(7, 56, 'General Practitioner', '12', '122.00', 'testtesttest tefrd', 'CV_1751465797_9073.pdf', '2025-07-02 10:17:29'),
(8, 58, 'Neurologist', '0', '500.00', 'yes chlotaw', 'CV_1751577010_5958.pdf', '2025-07-03 17:10:47'),
(9, 60, 'General Practitioner', '1000', '1.00', 'barku endazhu endasu kahun ekoesua enbi telalch ene tayki alkem yes ', 'CV_1751735802_7319.pdf', '2025-07-05 13:17:07'),
(10, 62, 'General Practitioner', '8', '600.00', 'krethghtrjhpknngfbnnbdghl', 'CV_1751966705_5790.pdf', '2025-07-08 05:30:29'),
(11, 64, 'Neurologist', '1', '400.00', 'jhdkjkjfnf baeitiy lhaihrteirt laihrtorti lierhonrt oawiierhtose paiehgpnst oiashrgoseitn liashgoieg ihrgoaerg blauhtgaeitg oiahrgoet oaihrgnwr oaihroni ', 'CV_1752235308_1531.pdf', '2025-07-11 08:10:28'),
(12, 67, 'Psychiatrist', '3', '450.00', 'fjgh slkdtg lstg listny erityp irpt iertr ipe4tnoJWE POQPRHT IHAWERT QPOERPTN IAERT OIER TY', 'CV_1752312844_9226.pdf', '2025-07-12 05:44:47');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_applications`
--

DROP TABLE IF EXISTS `doctor_applications`;
CREATE TABLE IF NOT EXISTS `doctor_applications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `age` int DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `specialty` varchar(255) NOT NULL,
  `experience` varchar(100) NOT NULL,
  `consult_fee` decimal(10,2) NOT NULL,
  `bio` text NOT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `application_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','Approve','Reject') NOT NULL DEFAULT 'pending',
  `available_time` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_applications`
--

INSERT INTO `doctor_applications` (`id`, `user_id`, `name`, `email`, `age`, `city`, `phone`, `photo`, `specialty`, `experience`, `consult_fee`, `bio`, `cv`, `application_date`, `status`, `available_time`) VALUES
(35, 1, 'Dr. Alice Johnson', 'alice.johnson@example.com', 35, 'New York', '123-456-7890', 'alice_photo.jpg', 'Cardiology', '5 years at City Hospital', '100.00', 'Experienced cardiologist specializing in heart diseases.', 'alice_cv.pdf', '2025-06-07 03:36:48', 'Approve', NULL),
(36, 2, 'Dr. Bob Smith', 'bob.smith@example.com', 42, 'Los Angeles', '234-567-8901', 'bob_photo.jpg', 'Neurology', '7 years in private practice', '120.00', 'Neurologist with a focus on brain disorders.', 'bob_cv.pdf', '2025-06-07 03:36:48', 'Approve', NULL),
(37, 3, 'Dr. Clara Lee', 'clara.lee@example.com', 30, 'Chicago', '345-678-9012', 'clara_photo.jpg', 'Pediatrics', '3 years in pediatric clinic', '90.00', 'Pediatrician passionate about child health.', 'clara_cv.pdf', '2025-06-07 03:36:48', 'Approve', NULL),
(38, 4, 'Dr. David Kim', 'david.kim@example.com', 45, 'Houston', '456-789-0123', 'david_photo.jpg', 'Orthopedics', '10 years experience', '150.00', 'Orthopedic surgeon specializing in joint replacement.', 'david_cv.pdf', '2025-06-07 03:36:48', 'Reject', NULL),
(39, 5, 'Dr. Emma Wilson', 'emma.wilson@example.com', 38, 'Miami', '567-890-1234', 'emma_photo.jpg', 'Dermatology', '4 years in dermatology center', '110.00', 'Dermatologist expert in skin care and treatments.', 'emma_cv.pdf', '2025-06-07 03:36:48', 'Approve', NULL),
(40, 31, 'chlotaw', 'chle@gmail.com', 22, 'addiss', '0933804482', NULL, 'Cardiologist', '0', '0.00', 'hj', '1749267541_1749263976_1749263537_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-07 03:39:01', 'Approve', NULL),
(41, 31, 'chlotaw', 'chle@gmail.com', 22, 'addiss', '0933804482', NULL, 'Cardiologist', '0', '0.00', 'hj', '1749268207_1749263976_1749263537_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-07 03:50:07', 'Approve', NULL),
(42, 35, 'enana', 'en@gmail.com', 222, 'dfdf', '0913744392', NULL, 'Neurologist', '5', '34.00', 'hjnkmfvfg', '1749270118_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-07 04:21:58', 'Approve', NULL),
(43, 35, 'enana', 'en@gmail.com', 222, 'dfdf', '0913744392', NULL, 'Neurologist', '5', '34.00', 'hjnkmfvfg', '1749270461_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-07 04:27:41', 'Approve', NULL),
(44, 35, 'enana', 'en@gmail.com', 222, 'dfdf', '0913744392', NULL, 'Neurologist', '56', '0.00', 'dfghdfcg', '1749274494_1749261912_Apply for Ethiopian Passport Online.pdf', '2025-06-07 05:34:54', 'Approve', NULL),
(45, 38, 'mastu', 'mastu@gmail.com', 12, 'rtr', '1234567890', NULL, 'Psychiatrist', '5', '0.00', '', '1749274640_1749261912_Apply for Ethiopian Passport Online.pdf', '2025-06-07 05:37:20', 'Approve', NULL),
(46, 39, 'mahi', 'mahi@gmail.com', 25, 'dese', '0909090909', NULL, 'Pediatrician', '4', '100.00', 'yes this is test', '1749473272_1749261744_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-09 12:47:52', 'Reject', NULL),
(50, 40, 'aseres', 'as@gmail.com', 222, 'is', '0000000000', NULL, 'General Practitioner', '000', '0.00', 'fuck', '1749725802_1749261744_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 10:56:42', 'Approve', NULL),
(51, 40, 'aseres', 'as@gmail.com', 222, 'is', '0000000000', NULL, 'General Practitioner', '000', '0.00', 'fuck', '1749726252_1749261744_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 11:04:12', 'Approve', NULL),
(52, 40, 'aseres', 'as@gmail.com', 222, 'is', '0000000000', NULL, 'General Practitioner', '000', '0.00', 'fuck', '1749726396_1749261744_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 11:06:36', 'Approve', NULL),
(53, 40, 'aseres', 'as@gmail.com', 222, 'is', '0000000000', NULL, 'General Practitioner', '0001', '1.00', 'fuckdfthrj', '1749726501_1749261744_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 11:08:21', 'Approve', NULL),
(54, 41, 'gedamu', 'ge@gmail.com', 33, 'isreal', '0909898978', NULL, 'Cardiologist', '45', '45.00', '45', '1749727714_1749261912_Apply for Ethiopian Passport Online.pdf', '2025-06-12 11:28:34', '', NULL),
(55, 42, 'abel', 'ab@gmail.com', 33, 'f', '0000000000', NULL, '', '', '0.00', '', '1749727655_1749261912_Apply for Ethiopian Passport Online.pdf', '2025-06-12 11:27:35', '', NULL),
(56, 42, 'abel', 'ab@gmail.com', 33, 'f', '0000000000', NULL, '', '', '0.00', '', '1749728172_1749261744_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 11:36:12', '', NULL),
(57, 42, 'abel', 'ab@gmail.com', 33, 'f', '0000000000', NULL, '', '', '0.00', 'vfvbrdhyrjgryjnetdgnwhrdghetdgn', '1749728808_1749262054_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 11:46:48', '', NULL),
(58, 43, 'fuck', 'fuck@gmail.com', 22, 'fg', '1111111122', NULL, '', '', '0.00', '', '1749730944_1749261912_Apply for Ethiopian Passport Online.pdf', '2025-06-12 12:22:24', '', NULL),
(59, 44, 'bbbbbb', 'b@gmail.com', 1, 'g', '1234567890', NULL, '', '', '0.00', '', '1749731732_1749263260_1749262054_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 12:35:32', '', NULL),
(60, 44, 'bbbbbb', 'b@gmail.com', 1, 'g', '1234567890', NULL, '', '', '0.00', '', '1749731797_1749263260_1749262054_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 12:36:37', '', NULL),
(61, 44, 'bbbbbb', 'b@gmail.com', 1, 'g', '1234567890', NULL, '', '', '0.00', '', '1749732329_1749263481_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 12:45:29', '', NULL),
(62, 44, 'bbbbbb', 'b@gmail.com', 1, 'g', '1234567890', NULL, '', '5555', '0.00', '', '1749733086_1749263481_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 12:58:06', '', NULL),
(63, 44, 'bbbbbb', 'b@gmail.com', 1, 'g', '1234567890', NULL, '', '5555', '0.00', '', '1749734281_1749263481_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 13:18:01', 'Approve', NULL),
(64, 44, 'bbbbbb', 'b@gmail.com', 1, 'g', '1234567890', NULL, '', '5555', '0.00', '', '1749735008_1749263481_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 13:30:08', 'Approve', NULL),
(65, 44, 'bbbbbb', 'b@gmail.com', 1, 'g', '1234567890', NULL, '', '5555', '0.00', '', '1749736578_1749263481_1749239061_Apply for Ethiopian Passport Online.pdf', '2025-06-12 13:56:18', 'pending', NULL),
(66, 46, '', '', NULL, NULL, NULL, NULL, 'Neurologist', '5555', '0.00', 'drfgrdf chlotaw', 'CV_1749745520_4406.pdf', '2025-06-12 16:25:20', 'Reject', NULL),
(67, 46, '', '', NULL, NULL, NULL, NULL, 'Pediatrician', '23', '1995.00', '0', 'CV_1749746033_8926.pdf', '2025-06-12 16:33:53', 'Approve', NULL),
(68, 46, 'abdu', 'abdu@gmail.com', 12, 'arada', '1234567890', NULL, '0', '1', '11.00', 'meta', 'CV_1749746358_4136.pdf', '2025-06-12 16:39:18', 'pending', NULL),
(69, 51, 'saron', 'saron@gmail.com', 23, 'bole', '996664284', NULL, 'rtygcvubv', '4', '1000.00', 'am a cardiologiest sepcilest on the health secvtor for the last 4 year am graduated from univeristy of unity ', 'CV_1750839830_4949.pdf', '2025-06-25 08:23:50', 'Approve', NULL),
(70, 52, 'redu', 'redu@gmail.com', 22, 'yeka', '999887766', NULL, 'Neurologist', '44', '4.00', 'hhhhhhhh', 'CV_1750842635_5403.pdf', '2025-06-25 09:10:35', 'Reject', NULL),
(71, 52, 'redu', 'redu@gmail.com', 22, 'yeka', '999887766', NULL, '0', '5', '55.00', 'zsdtfygjkhgftgrd', 'CV_1750946529_4407.pdf', '2025-06-26 14:02:09', 'Reject', NULL),
(72, 52, 'redu', 'redu@gmail.com', 22, 'yeka', '999887766', NULL, '0', '5', '55.00', 'zsdtfygjkhgftgrd', 'CV_1750946888_2053.pdf', '2025-06-26 14:08:08', 'Reject', NULL),
(73, 52, 'redu', 'redu@gmail.com', 22, 'yeka', '999887766', NULL, '0', '3', '56.00', 'ytrfhggfre', 'CV_1750947660_5661.pdf', '2025-06-26 14:21:00', 'Reject', NULL),
(74, 52, 'redu', 'redu@gmail.com', 22, 'yeka', '999887766', NULL, '0', '0', '45.00', 'jghftrgf', 'CV_1750947711_9822.pdf', '2025-06-26 14:21:51', 'Reject', NULL),
(75, 52, 'redu', 'redu@gmail.com', 22, 'yeka', '0999887766', NULL, 'Neurologist', '1', '1.00', 's', 'CV_1750948510_3189.pdf', '2025-06-26 14:35:10', 'Reject', NULL),
(76, 52, 'redu', 'redu@gmail.com', 22, 'yeka', '0999887766', NULL, 'General Practitioner', '0', '0.00', 'y', 'CV_1750949474_2961.pdf', '2025-06-26 14:51:14', 'Reject', NULL),
(77, 52, 'redu', 'redu@gmail.com', 22, 'yeka', '0999887766', NULL, 'General Practitioner', '0', '0.00', 'y', 'CV_1750949500_7561.pdf', '2025-06-26 14:51:40', 'pending', NULL),
(78, 54, 'Eden', 'eden@gmail.com', 23, 'yeka', '0973858428', NULL, 'General Practitioner', '10', '100.00', 'my name is eden am a docotr for the last 10 year ', 'CV_1751137587_8070.pdf', '2025-06-28 19:06:27', 'Approve', NULL),
(79, 54, 'Eden', 'eden@gmail.com', 23, 'yeka', '0973858428', NULL, 'General Practitioner', '10', '100.00', 'my name is eden am a docotr for the last 10 year ', 'CV_1751137672_7273.pdf', '2025-06-28 19:07:52', 'pending', NULL),
(80, 56, 'dagi', 'dagi@gmail.com', 22, 'f', '0909090988', NULL, 'General Practitioner', '12', '122.00', 'testtesttest tefrd', 'CV_1751465797_9073.pdf', '2025-07-02 14:16:37', 'Approve', NULL),
(81, 58, 'dave', 'dave@gmail.com', 24, 'adama', '0912345443', NULL, 'Neurologist', '0', '500.00', 'yes chlotaw', 'CV_1751577010_5958.pdf', '2025-07-03 21:10:10', 'Approve', NULL),
(82, 59, 'alakem', 'alk@gmail.com', 33, 'nn', '1234567890', NULL, 'Pediatrician', '3333', '33333.00', 'dfgfdfv', 'CV_1751581004_3142.pdf', '2025-07-03 22:16:44', 'Reject', NULL),
(83, 60, 'english', 'Eng@gmail.com', 30, 'USA', '5647382910', NULL, 'General Practitioner', '1000', '1.00', 'barku endazhu endasu kahun ekoesua enbi telalch ene tayki alkem yes ', 'CV_1751735802_7319.pdf', '2025-07-05 17:16:42', 'Approve', NULL),
(85, 62, 'sssssssss', 'ss@gmail.com', 23, 'addis', '0909090909', NULL, 'General Practitioner', '8', '600.00', 'krethghtrjhpknngfbnnbdghl', 'CV_1751966705_5790.pdf', '2025-07-08 09:25:05', 'Approve', 'mon-fri 5pm-12pm'),
(86, 63, 'mmmmmm', 'mm@gmail.com', 25, 'addis', '9999999999', NULL, 'Dermatologist', '3', '323.00', 'ryewgj', 'CV_1752097776_6924.pdf', '2025-07-09 21:49:36', 'pending', 'mon-fri 5pm-12pm'),
(87, 64, 'hi', 'hi@gmail.com', 35, 'bole', '0988776655', NULL, 'Neurologist', '1', '400.00', 'jhdkjkjfnf baeitiy lhaihrteirt laihrtorti lierhonrt oawiierhtose paiehgpnst oiashrgoseitn liashgoieg ihrgoaerg blauhtgaeitg oiahrgoet oaihrgnwr oaihroni ', 'CV_1752235308_1531.pdf', '2025-07-11 12:01:48', 'Approve', 'thu-fir,10pm-12pm'),
(88, 67, 'home', 'home@gmail.com', 22, 'yeka', '0911121314', NULL, 'Psychiatrist', '3', '450.00', 'fjgh slkdtg lstg listny erityp irpt iertr ipe4tnoJWE POQPRHT IHAWERT QPOERPTN IAERT OIER TY', 'CV_1752312844_9226.pdf', '2025-07-12 09:34:04', 'Approve', 'Mon-fir,2pm-4pm');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_ratings`
--

DROP TABLE IF EXISTS `doctor_ratings`;
CREATE TABLE IF NOT EXISTS `doctor_ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `appointment_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int NOT NULL,
  `review` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rating` (`appointment_id`,`user_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `user_id` (`user_id`)
) ;

--
-- Dumping data for table `doctor_ratings`
--

INSERT INTO `doctor_ratings` (`id`, `appointment_id`, `doctor_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(5, 73, 54, 53, 5, NULL, '2025-06-28 15:10:03'),
(6, 84, 54, 52, 4, NULL, '2025-06-28 17:58:07'),
(8, 87, 58, 59, 1, NULL, '2025-07-03 17:36:05'),
(9, 103, 62, 63, 3, NULL, '2025-07-08 06:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `medical_history`
--

DROP TABLE IF EXISTS `medical_history`;
CREATE TABLE IF NOT EXISTS `medical_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `appointment_id` int NOT NULL,
  `prescription_id` int DEFAULT NULL,
  `symptoms` text,
  `past_illnesses` text,
  `current_medications` text,
  `allergies` text,
  `family_history` text,
  `social_history` text,
  `doctor_notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `prescription_id` (`prescription_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medical_history`
--

INSERT INTO `medical_history` (`id`, `appointment_id`, `prescription_id`, `symptoms`, `past_illnesses`, `current_medications`, `allergies`, `family_history`, `social_history`, `doctor_notes`, `created_at`) VALUES
(1, 88, NULL, 'srgf', 'SEUKGJ', 'WRE', 'wrg', 'WT', 'askjefksJGBbv flgjvnsjf,mvkjz,mxv', 'SEUKGJbszkjvkfjs,vmzkfsjfvxmchgkfsjxnckhrssfjlfknxvrshkfj,nxmcgrsfihkjx,nvhsifkx,mrhskfn,xmisfkl,nxvlihsfkn,vlihkfj,n', '2025-07-05 10:23:49'),
(2, 95, NULL, ',janrlgeq ysherthe 4 6rytge45pyjlgw4p5ojl4owi5jlkyoi45kjtygioh45kjt', 'q4 eaotislh3oi5welntu35iontroi3k54e', 'aptizer', 'qp 34uijowtnu935iojtnbu954iojrk', 'n9 3up4oijetn9u3io5tn09u5oitrno8ui45r', '9nu3o4ijtbn09uoi5tu954oir', 'o3ui4etrou54irou45itrj', '2025-07-05 17:23:12'),
(3, 97, NULL, 'aorihlgkarijlsk', 'oerilkf,nm', 'aptizer', 'rukj', '5yeoihdfkjnm', '8erihj', '834yurj', '2025-07-06 18:14:28'),
(4, 102, NULL, 'tyuiop', 'rtdyfugihojp', 'efghj', 'rt7y8u', '47y8u9', '45rt6y7ui', 'uy35iyt8uoiryt894w85io', '2025-07-08 10:06:31'),
(5, 110, NULL, 'tgcvhbjnxfchgvjhbkjnklmxcgvbhjnkmtrcyvuybiunjtyguhu', 'a8uwraht08oesihg', 'hhhh', 'aiuwejksfnoeirgioer', '8wy5orshtidf', '5yierudhfkjb', 'gsbeouthdtogfi aoeirhtogise oawhrtoe oihWRET LOirwhstog oHWRTG IOHRT OIUhwrt iohWERT F', '2025-07-11 12:41:53'),
(6, 113, NULL, 'oeirhyoiwtey', 'paeirutuw45o5ijt', 'hlfsnsn', 'o95eiyjtrkl', 'kkkkkkkkkkkkkkkkkkk', 'hhhhhhhhhhhhhhhhhhhh', '47wrtsh', '2025-07-12 10:58:03');

-- --------------------------------------------------------

--
-- Table structure for table `medications`
--

DROP TABLE IF EXISTS `medications`;
CREATE TABLE IF NOT EXISTS `medications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `dosage` varchar(50) NOT NULL,
  `instructions` text,
  `manufacturer` varchar(255) NOT NULL,
  `expiration_date` date NOT NULL,
  `side_effects` text,
  `storage` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medications`
--

INSERT INTO `medications` (`id`, `name`, `type`, `dosage`, `instructions`, `manufacturer`, `expiration_date`, `side_effects`, `storage`, `created_at`) VALUES
(1, 'amoxacilen', 'tablet', '500ml', 'take one pice befor food and if you fell pain', 'in ethiopia ', '0000-00-00', 'awrukthoileyrhghsietl', 'sdthdsthsftuh', '2025-06-26 08:49:03'),
(2, 'aptizer', 'tablet', '1000ml', 'phdksjbhkdbhndkbvfnbvjfkd', 'in china', '2026-11-28', 'rghejvhjdjnjjujhjfndkdk', 'fgydshkajgdyriuiefjdc', '2025-06-26 09:23:51'),
(4, 'efghj', 'tablet', '700ml', 'agjhefjgkhrsjkhfjijldkhjhcghggh', 'in et', '2025-08-01', 'dfgdtrfhzdfxfhygbdfgdgu', 'w45y6utyhfgdf', '2025-07-08 10:03:35'),
(5, 'hhhh', 'tablet', '50ml', 'take one 50ml morning', 'inindia', '2026-11-11', 'jdhfildfhxglnsfv', 'keep in dry place', '2025-07-11 12:39:13'),
(6, 'hlfsnsn', 'syrup', '100ml', 'djfgn dtrk htk trhk rth', 'in italy', '2026-11-13', 'gebfdsrbkg rkugtbg kjarbt krbrt', 'jsrdbgnrhgnniurt kerubt', '2025-07-12 10:49:48');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 41, 'New appointment request from user ID 52 on 2025-07-26 at 01:39.', 1, '2025-07-05 14:39:11'),
(2, 52, 'Your appointment request with Dr. ID 41 on 2025-07-26 at 01:39 has been sent.', 1, '2025-07-05 14:39:11'),
(3, 41, 'New appointment request from <strong>redu</strong> on 2025-08-07 at 10:52.', 1, '2025-07-05 14:46:45'),
(4, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-08-07 at 10:52 has been sent.', 1, '2025-07-05 14:46:45'),
(5, 52, 'Your appointment #90 has been accepted by the doctor. Please proceed with payment.', 1, '2025-07-05 15:04:22'),
(6, 52, 'Your appointment #90 has been accepted by the doctor. Please proceed with payment.', 1, '2025-07-05 15:04:37'),
(7, 41, 'New appointment request from <strong>redu</strong> on 2025-08-07 at 10:52.', 1, '2025-07-05 15:13:15'),
(8, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-08-07 at 10:52 has been sent.', 1, '2025-07-05 15:13:15'),
(9, 52, 'Your appointment #90 has been accepted by the doctor. Please proceed with payment.', 1, '2025-07-05 15:14:36'),
(10, 52, 'Your appointment #90 has been accepted by the doctor. Please proceed with payment.', 1, '2025-07-05 15:14:55'),
(11, 52, 'Your appointment #91 has been rejected by the doctor.', 1, '2025-07-05 15:37:00'),
(12, 41, 'New appointment request from <strong>redu</strong> on 2025-07-31 at 11:40.', 1, '2025-07-05 15:37:09'),
(13, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-07-31 at 11:40 has been sent.', 1, '2025-07-05 15:37:09'),
(14, 52, 'Your appointment #91 has been rejected by the doctor.', 1, '2025-07-05 15:37:46'),
(15, 52, 'Your appointment request with Dr. <strong></strong> has been rejected by the doctor.', 1, '2025-07-05 15:41:46'),
(16, 52, 'Your appointment #91 has been rejected by the doctor.', 1, '2025-07-05 15:42:24'),
(17, 52, 'Dr. gedamu has rejected your appointment.', 1, '2025-07-05 15:46:20'),
(18, 52, 'Your appointment #91 has been rejected by the doctor.', 1, '2025-07-05 15:46:50'),
(19, 52, 'Dr. gedamu has rejected your appointment.', 1, '2025-07-05 15:47:35'),
(20, 52, 'Dr. gedamu has accepted your video appointment. Please join the meeting and proceed with payment.', 1, '2025-07-05 15:47:51'),
(21, 41, 'Payment received for appointment ID 92 with patient redu on 2025-07-31 at 11:40:00.', 1, '2025-07-05 16:13:16'),
(22, 41, 'New appointment request from <strong>redu</strong> on 2025-07-31 at 18:00.', 1, '2025-07-05 16:21:45'),
(23, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-07-31 at 18:00 has been sent.', 1, '2025-07-05 16:21:45'),
(24, 52, 'well Dr. gedamu has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-05 16:22:00'),
(25, 41, 'New appointment request from <strong>redu</strong> on 2025-08-05 at 23:55.', 1, '2025-07-05 16:30:13'),
(26, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-08-05 at 23:55 has been sent.', 1, '2025-07-05 16:30:13'),
(27, 52, 'well Dr. gedamu has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-05 16:30:31'),
(28, 60, 'Congratulations! Your doctor application has been accepted.', 1, '2025-07-05 17:17:07'),
(29, 60, 'New appointment request from <strong>redu</strong> on 2025-10-09 at 08:00.', 1, '2025-07-05 17:20:06'),
(30, 52, 'Your appointment request with Dr. <strong>english</strong> on 2025-10-09 at 08:00 has been sent.', 1, '2025-07-05 17:20:06'),
(31, 52, 'well Dr. english has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-05 17:20:52'),
(32, 41, 'New appointment request from <strong>redu</strong> on 2025-07-31 at 13:24.', 1, '2025-07-06 17:22:42'),
(33, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-07-31 at 13:24 has been sent.', 1, '2025-07-06 17:22:42'),
(34, 52, 'well Dr. gedamu has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-06 17:23:00'),
(35, 41, 'New appointment request from <strong>redu</strong> on 2025-08-09 at 20:27.', 1, '2025-07-06 17:28:06'),
(36, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-08-09 at 20:27 has been sent.', 1, '2025-07-06 17:28:06'),
(37, 52, 'well Dr. gedamu has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-06 17:28:39'),
(38, 41, 'New appointment request from <strong>redu</strong> on 2025-08-08 at 14:37.', 1, '2025-07-06 17:37:28'),
(39, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-08-08 at 14:37 has been sent.', 1, '2025-07-06 17:37:28'),
(40, 52, 'well Dr. gedamu has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-06 17:38:03'),
(41, 41, 'New appointment request from <strong>redu</strong> on 2025-08-08 at 14:37.', 1, '2025-07-06 17:38:09'),
(42, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-08-08 at 14:37 has been sent.', 1, '2025-07-06 17:38:09'),
(43, 52, 'well Dr. gedamu has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-06 17:45:39'),
(44, 41, 'New appointment request from <strong>redu</strong> on 2025-08-09 at 18:00.', 1, '2025-07-06 17:57:29'),
(45, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-08-09 at 18:00 has been sent.', 1, '2025-07-06 17:57:29'),
(46, 52, 'well Dr. gedamu has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-06 18:04:01'),
(47, 52, 'well Dr. gedamu has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-06 18:04:47'),
(48, 52, 'Dr. gedamu has written a new prescription for your appointment.', 1, '2025-07-06 18:06:49'),
(49, 52, 'Dr. gedamu has written your medical history after your appointment.', 1, '2025-07-06 18:14:28'),
(50, 41, 'New appointment request from <strong>redu</strong> on 2025-08-09 at 03:33.', 1, '2025-07-07 07:27:59'),
(51, 52, 'Your appointment request with Dr. <strong>gedamu</strong> on 2025-08-09 at 03:33 has been sent.', 1, '2025-07-07 07:27:59'),
(52, 62, 'Congratulations! Your doctor application has been accepted so after this you can logout and login back and you get you docotr dashboard.', 1, '2025-07-08 09:30:29'),
(53, 62, 'New appointment request from <strong>mmmmmm</strong> on 2025-08-01 at 06:34.', 1, '2025-07-08 09:35:49'),
(54, 63, 'Your appointment request with Dr. <strong>sssssssss</strong> on 2025-08-01 at 06:34 has been sent.', 1, '2025-07-08 09:35:49'),
(55, 63, 'well Dr. sssssssss has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-08 09:37:57'),
(56, 62, 'New appointment request from <strong>mmmmmm</strong> on 2025-07-31 at 11:53.', 1, '2025-07-08 09:54:04'),
(57, 63, 'Your appointment request with Dr. <strong>sssssssss</strong> on 2025-07-31 at 11:53 has been sent.', 1, '2025-07-08 09:54:04'),
(58, 63, 'well Dr. sssssssss has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-08 09:54:34'),
(59, 63, 'Dr. sssssssss has written a new prescription for your appointment.', 1, '2025-07-08 10:04:17'),
(60, 63, 'Dr. sssssssss has written your medical history after your appointment.', 1, '2025-07-08 10:06:31'),
(61, 62, 'New appointment request from <strong>mmmmmm</strong> on 2025-08-09 at 06:15.', 1, '2025-07-08 10:11:54'),
(62, 63, 'Your appointment request with Dr. <strong>sssssssss</strong> on 2025-08-09 at 06:15 has been sent.', 1, '2025-07-08 10:11:54'),
(63, 63, 'well Dr. sssssssss has accepted your video appointment. Please proceed with payment and join the meeting .', 1, '2025-07-08 10:27:20'),
(64, 62, 'New appointment request from <strong>mmmmmm</strong> on 2025-08-25 at 05:53.', 1, '2025-07-11 09:09:38'),
(65, 63, 'Your appointment request with Dr. <strong>sssssssss</strong> was sent.', 1, '2025-07-11 09:09:38'),
(66, 63, 'Dr. sssssssss has rejected your appointment. Reason: <em>kxdufjbcm</em>', 1, '2025-07-11 09:27:50'),
(67, 62, 'New appointment request from <strong>mmmmmm</strong> on 2025-07-25 at 17:14.', 1, '2025-07-11 09:28:01'),
(68, 63, 'Your appointment request with Dr. <strong>sssssssss</strong> was sent.', 1, '2025-07-11 09:28:01'),
(69, 63, 'Dr. sssssssss has rejected your appointment. Reason: <em>kxdufjbcm</em>', 1, '2025-07-11 09:28:37'),
(70, 63, 'Dr. sssssssss has rejected your appointment. Reason: <em>h</em>', 1, '2025-07-11 09:28:50'),
(71, 62, 'New appointment request from <strong>mmmmmm</strong> on 2025-07-12 at 05:31.', 1, '2025-07-11 09:29:11'),
(72, 63, 'Your appointment request with Dr. <strong>sssssssss</strong> was sent.', 1, '2025-07-11 09:29:11'),
(73, 63, 'Dr. sssssssss has rejected your appointment. Reason: <em>k</em>', 1, '2025-07-11 09:30:07'),
(74, 62, 'New appointment request from <strong>mmmmmm</strong> on 2025-07-12 at 05:31.', 1, '2025-07-11 09:30:14'),
(75, 63, 'Your appointment request with Dr. <strong>sssssssss</strong> was sent.', 1, '2025-07-11 09:30:14'),
(76, 63, 'Dr. sssssssss has accepted your appointment. Please proceed with payment.', 1, '2025-07-11 09:47:56'),
(77, 64, 'Congratulations! Your doctor application has been accepted so after this you can logout and login back and you get you docotr dashboard.', 1, '2025-07-11 12:10:28'),
(78, 64, 'New appointment request from <strong>user</strong> on 2025-07-09 at 23:30.', 1, '2025-07-11 12:23:41'),
(79, 65, 'Your appointment request with Dr. <strong>hi</strong> was sent.', 1, '2025-07-11 12:23:41'),
(80, 65, 'Dr. hi has rejected your appointment. Reason: <em>am not able on that time</em>', 1, '2025-07-11 12:25:56'),
(81, 64, 'New appointment request from <strong>user</strong> on 2025-07-16 at 23:30.', 1, '2025-07-11 12:26:54'),
(82, 65, 'Your appointment request with Dr. <strong>hi</strong> was sent.', 1, '2025-07-11 12:26:54'),
(83, 65, 'Dr. hi has accepted your appointment. Please proceed with payment.', 1, '2025-07-11 12:27:31'),
(84, 65, 'Dr. hi has written a new prescription for your appointment.', 1, '2025-07-11 12:39:56'),
(85, 65, 'Dr. hi has written your medical history after your appointment.', 1, '2025-07-11 12:41:53'),
(86, 64, 'New appointment request from <strong>user</strong> on 2025-07-10 at 11:49.', 1, '2025-07-11 12:47:06'),
(87, 65, 'Your appointment request with Dr. <strong>hi</strong> was sent.', 1, '2025-07-11 12:47:06'),
(88, 67, 'Congratulations! Your doctor application has been accepted so after this you can logout and login back and you get you docotr dashboard.', 1, '2025-07-12 09:44:47'),
(89, 67, 'New appointment request from <strong>welcome</strong> on 2025-07-15 at 15:00.', 1, '2025-07-12 09:57:18'),
(90, 68, 'Your appointment request with Dr. <strong>home</strong> was sent.', 1, '2025-07-12 09:57:18'),
(91, 68, 'Dr. home has rejected your appointment. Reason: <em>am taken</em>', 1, '2025-07-12 10:17:05'),
(92, 67, 'New appointment request from <strong>welcome</strong> on 2025-07-16 at 14:20.', 1, '2025-07-12 10:21:55'),
(93, 68, 'Your appointment request with Dr. <strong>home</strong> was sent.', 1, '2025-07-12 10:21:55'),
(94, 68, 'Dr. home has accepted your appointment. Please proceed with payment.', 1, '2025-07-12 10:22:39'),
(95, 68, 'Dr. home has written a new prescription for your appointment.', 1, '2025-07-12 10:51:46'),
(96, 68, 'Dr. home has written your medical history after your appointment.', 1, '2025-07-12 10:58:03'),
(97, 67, 'New appointment request from <strong>welcome</strong> on 2025-07-16 at 16:00.', 1, '2025-07-12 11:12:37'),
(98, 68, 'Your appointment request with Dr. <strong>home</strong> was sent.', 1, '2025-07-12 11:12:37'),
(99, 68, 'Dr. home has accepted your appointment. Please proceed with payment.', 1, '2025-07-12 11:14:51');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

DROP TABLE IF EXISTS `prescriptions`;
CREATE TABLE IF NOT EXISTS `prescriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `appointment_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `user_id` int NOT NULL,
  `medication_id` int NOT NULL,
  `dosage` varchar(255) NOT NULL,
  `instructions` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `appointment_id` (`appointment_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `user_id` (`user_id`),
  KEY `medication_id` (`medication_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `appointment_id`, `doctor_id`, `user_id`, `medication_id`, `dosage`, `instructions`, `created_at`) VALUES
(3, 73, 54, 53, 1, '500ml', 'take one pice befor food and if you fell pain', '2025-06-28 15:12:01'),
(4, 84, 54, 52, 2, '1000ml', 'phdksjbhkdbhndkbvfnbvjfkd', '2025-06-28 17:58:39'),
(5, 87, 58, 59, 1, '500ml', 'take one pice befor food and if you fell pain', '2025-07-03 17:14:13'),
(6, 88, 41, 52, 1, '500ml', 'take one pice befor food and if you fell pain', '2025-07-05 06:25:40'),
(7, 94, 41, 52, 2, '1000ml', 'phdksjbhkdbhndkbvfnbvjfkd', '2025-07-05 12:34:22'),
(8, 90, 41, 52, 2, '1000ml', 'phdksjbhkdbhndkbvfnbvjfkd', '2025-07-05 12:44:30'),
(9, 92, 41, 52, 2, '1000ml', 'phdksjbhkdbhndkbvfnbvjfkd', '2025-07-05 12:47:01'),
(10, 95, 60, 52, 2, '1000ml', 'phdksjbhkdbhndkbvfnbvjfkd', '2025-07-05 13:22:24'),
(11, 96, 41, 52, 2, '1000ml', 'phdksjbhkdbhndkbvfnbvjfkd', '2025-07-06 13:24:37'),
(12, 97, 41, 52, 2, '1000ml', 'phdksjbhkdbhndkbvfnbvjfkd', '2025-07-06 13:30:12'),
(13, 98, 41, 52, 1, '500ml', 'take one pice befor food and if you fell pain', '2025-07-06 13:39:34'),
(14, 99, 41, 52, 2, '1000ml', 'phdksjbhkdbhndkbvfnbvjfkd', '2025-07-06 13:46:48'),
(15, 100, 41, 52, 1, '500ml', 'take one pice befor food and if you fell pain', '2025-07-06 14:06:49'),
(16, 102, 62, 63, 4, '700ml', 'agjhefjgkhrsjkhfjijldkhjhcghggh', '2025-07-08 06:04:17'),
(17, 110, 64, 65, 5, '50ml', 'take one 50ml morning', '2025-07-11 08:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','doctor','admin') DEFAULT 'user',
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `age` int DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `gender`, `email`, `password`, `role`, `status`, `created_at`, `age`, `city`, `phone`, `photo`) VALUES
(7, 'chloooo', NULL, 'zkdj@gmail.com', '$2y$10$x4azPc2fctLrN2vHBhnTHOjI2qLlo65IfpfljvxEbNrgbTBq8mkTO', 'doctor', '', '2025-05-22 10:41:05', 15, 'er', '1234567890', NULL),
(8, 'admin', NULL, 'mom@gmail.com', '$2y$10$Al7jCLji9bPpVXixnAofmOnMhgePz3v4Jk7msadpXYfWxK0nGSypW', 'admin', '', '2025-05-22 10:56:02', 0, 'adlw', '0913744392', NULL),
(9, 'aaa', NULL, 'aaa@gmail.com', '$2y$10$v7P6OhagyZ7cTyXnzD3bJ.sUKpIILDdXAnLT.xFvbuEuOxO8a.yEi', 'user', '', '2025-05-22 12:47:44', 14, 'fg', '0998877665', NULL),
(10, 'mast', NULL, 'mmm@gmail.com', '$2y$10$bpWdxPastO0hKM8iS2Y6DeDbsiXlVVetMNvAKg3BiyyiEsykUEPve', 'doctor', '', '2025-05-22 14:12:05', 111, 'etiopiya', '1234567880', NULL),
(11, 'mom', NULL, 'mom@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'admin', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(12, 'john_doe', NULL, 'john@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'user', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(13, 'jane_smith', NULL, 'jane@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'user', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(14, 'alice_wonder', NULL, 'alice@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'doctor', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(15, 'bob_builder', NULL, 'bob@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'user', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(16, 'charlie_brown', NULL, 'charlie@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'user', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(17, 'diana_prince', NULL, 'diana@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'doctor', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(18, 'eric_cartman', NULL, 'eric@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'user', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(19, 'frank_underwood', NULL, 'frank@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'admin', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(20, 'grace_hopper', NULL, 'grace@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'doctor', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(21, 'hank_moody', NULL, 'hank@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'user', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(22, 'ivy_lee', NULL, 'ivy@example.com', '$2y$10$abcdefghijk1234567890abcdefghijk1234567890abcdefghijk12', 'user', '', '2025-05-22 14:25:35', NULL, NULL, NULL, NULL),
(23, 'luna', NULL, 'luna@gmail.com', '$2y$10$pac8RvFBDihUz9j0Rgk7kuxpMNjb118.tlMQYtDkLx2mFQgLmv/pa', 'user', '', '2025-05-22 15:50:30', 20, 'yu', '0000000000', NULL),
(24, 'mmm', NULL, 'mmmm@gmail.com', '$2y$10$bBL.fBnLyswMiXha1RzkueUABVYgiKXIOaUrIb85BlU04hdGXEqLS', 'doctor', '', '2025-05-23 14:35:05', 23, 'addis', '0967564534', 'profile_68308799655da8.16440387.jpg'),
(25, 'set', NULL, 'set@gmail.com', '$2y$10$7LF5K60nIpGPAguqhkvo7uhpNlONhmJOblT9DvkXkADPielULr8oS', 'user', '', '2025-06-05 12:11:19', 34, 'addis ababa', '0909890909', 'profile_684189672825b4.86242395.jpeg'),
(26, 'nnn', NULL, 'nnnm@gmail.com', '$2y$10$HBajLe/sNUhSWTC4DODan.z7OVHOXbzgKlcpGkxaaHHAdaCTqoaoi', 'doctor', '', '2025-06-05 13:05:50', 1212, 'fg', '1234567890', 'mastu.jpg'),
(27, 'tttt', NULL, 'ttt@gmail.com', '$2y$10$X.wcUpR1NRBekpzwHIRBiecMggtfaVysSMNDXK/calB9OkSRS4lWW', 'doctor', '', '2025-06-06 18:44:34', 22, 'addis', '0000000000', '1d.jpg'),
(28, 'chubaw', NULL, 'ch@gmail.com', '$2y$10$ejZejqmwODr9Lh89/jZnI.ZLNrEcQfh8HoU3CL4suGRhKf0ZGSjze', 'admin', '', '2025-06-06 19:48:39', 22, 'ffdf', '1111111111', 'profile_6830863c6b5104.80950870.jpg'),
(29, 'abel', NULL, 'abel@gmail.com', '$2y$10$FUUFrY4DF8CT20w/cSHAGernTRuMkOFpS53/Sk4rWPVks5Nzvyqby', 'user', '', '2025-06-06 21:46:09', 11, 'fggg', '2222222222', 'mastu.jpg'),
(30, 'bbb', NULL, 'bb@gmail.com', '$2y$10$r2jBXVU.dVrnjXtEfYW6reRzYQ027Q8ZuCSzgNjR3EHch5BqQoBbC', 'user', '', '2025-06-06 21:55:56', 67, 'dxcv', '1234554321', 'profile_68308903673241.05972583.jpg'),
(31, 'chlotaw', NULL, 'chle@gmail.com', '$2y$10$3G5UHNKtPdJcJcYLB598hudCwXFr0kT4CM/.nJzUphK3YGA6WByZi', 'doctor', '', '2025-06-07 01:33:41', 22, 'addiss', '0933804482', 'profile_68308903673241.05972583.jpg'),
(32, 'almaz', NULL, 'al@gmail.com', '$2y$10$qhxdb/7mtm2p1kaYxOBg4uQNPD6jZ1rTGWgyBZFIJk7Z/Z8VCPpi.', 'user', '', '2025-06-07 02:30:33', 11, 'dg', '1122334455', 'profile_68308903673241.05972583.jpg'),
(33, 'bina', NULL, 'bina@gamil.com', '$2y$10$/EVT.EybWmPIwfHvKRONLeJ9hFuRJMVWsE9ZYnki9CwKOvn8vUYp2', 'user', '', '2025-06-07 02:36:58', 21, 'df', '0913768099', 'profile_68308903673241.05972583.jpg'),
(34, 'llll', NULL, 'll@gmail.com', '$2y$10$Y7Syn4BADXPQAP9IjulDNek9Gytlepa107vJ7YOEy2luH5Hxzknau', 'user', '', '2025-06-07 02:38:52', 3, 'f', '0000000000', 'profile_68308903673241.05972583.jpg'),
(35, 'enana', NULL, 'en@gmail.com', '$2y$10$91PA2VuVRGjPzD6cSebijewbZdBWh7/QQDZXy3Xf8/eBekS9wjuAm', 'doctor', '', '2025-06-07 04:21:20', 222, 'dfdf', '0913744392', 'profile_68308903673241.05972583.jpg'),
(36, 'oo', NULL, 'oo@gmail.com', '$2y$10$d/yIdZ7rot9Xq3Jrz2YINOcgQgHxu36uDV5ZC.6peNvPz3GxnjjKi', 'user', '', '2025-06-07 04:49:06', 12, 'df', '9090909090', 'profile_68308903673241.05972583.jpg'),
(37, 'nati', NULL, 'nati@gmail.com', '$2y$10$ttuDcLUflTaojt7T51RyeOwWgz.4Ej29RKVv2q4HfkCr0kkLxDBqm', 'user', '', '2025-06-07 05:07:38', 12, 'kott', '090909090', 'profile_68308903673241.05972583.jpg'),
(38, 'mastu', NULL, 'mastu@gmail.com', '$2y$10$irAMI1soCYf1gQyIjIScPeR3ZL3x0C34sFkJT9UPd8fKHc.WAhcq2', 'doctor', '', '2025-06-07 05:36:46', 12, 'rtr', '1234567890', 'profile_68308903673241.05972583.jpg'),
(39, 'mahiuu', NULL, 'mahi@gmail.com', '$2y$10$zKPaUt8cO/ME5TV.U5rzAOneGZNcfp7h9EddJ5pjOIwyt3drMD09G', '', '', '2025-06-09 12:47:14', 2, 'uuu', '09090909099', 'profile_68308799655da8.16440387.jpg'),
(40, 'aseres', NULL, 'as@gmail.com', '$2y$10$vMDPBbThyNIYKYnBbUTn9.Jqvmv3X1LbElKc15e6VP4NBJW9FGImW', 'doctor', '', '2025-06-12 10:55:41', 222, 'is', '0000000000', 'profile_68308903673241.05972583.jpg'),
(41, 'gedamu', NULL, 'ge@gmail.com', '$2y$10$OnhXq0lHKeQRAE5uzBibYeWmi7JjGBOFCF6I9PXhDI9DxJqBKwC6W', 'doctor', 'active', '2025-06-12 11:27:42', 10000, '', '3333333333', 'profile_686932214924f8.62008707.jpeg'),
(42, 'abel', NULL, 'ab@gmail.com', '$2y$10$bp6XBoOj4cvW/F1uO5YV2eOrgWaxK0cOD9yybVdQxL9RmP7sq3riW', 'doctor', '', '2025-06-12 11:27:08', 33, 'f', '0000000000', 'profile_68308799655da8.16440387.jpg'),
(43, 'fuck', NULL, 'fuck@gmail.com', '$2y$10$Vu0x.uGrTsK27h2n.Iyhn.h1dauuOPLuUI7vHKA5s7jcvIyEBEiBi', 'doctor', '', '2025-06-12 12:22:00', 22, 'fg', '1111111122', 'profile_68308903673241.05972583.jpg'),
(47, 'adane', NULL, 'adane@gmail.com', '$2y$10$JFwMKg3OOydMctQn4XpfVu9dxvKDjfWC6jmmRwZNQY9ka582Jk82W', 'user', '', '2025-06-12 16:50:33', 12, '12', '0000000000', 'profile_68308903673241.05972583.jpg'),
(48, 'test', NULL, 'test@gmail.com', '$2y$10$nXgD9z/aoHl50hmFxd5Wv.gP0FBEMcjWYnfy46V0Q9M62qNWkr6i6', 'user', 'active', '2025-06-12 19:38:03', 12, 'tyy', '0000000000', 'profile_68308799655da8.16440387.jpg'),
(52, 'redu', NULL, 'redu@gmail.com', '$2y$10$Ok251TCyACjQ8sQoVfuJ1OYeyvK1PTaIpIhfJ7QYmLvMX5tm8koy6', 'user', 'active', '2025-06-25 09:10:01', 122, 'fffffffff', '1234567890', '1d (2).jpg'),
(53, 'Meba', NULL, 'meba@gmail.com', '$2y$10$XqvbISyL4lCvByAPLZdgC.9J2dbbZMVscX3u0GsqskjmNoMI/IbPC', 'user', 'active', '2025-06-28 19:04:01', 23, 'yeka', '0977070904', '1d (1).jpeg'),
(54, 'Eden', NULL, 'eden@gmail.com', '$2y$10$6QhvdKbqrXvQXKATr8eykeCeK73nU.ZMyL6GvvfAo/UN2eIewGFnC', 'doctor', 'active', '2025-06-28 19:05:20', 23, 'yeka', '0973858428', 'profile_68308903673241.05972583.jpg'),
(55, 'teddy', NULL, 'teddy@gmail.com', '$2y$10$XcEk7VChB/snUskMAiY.Ie1MM2NBPpwhQTDIl/CHqRyEIuVg80Jni', 'user', 'active', '2025-07-02 13:58:18', 40, 'ui', '0909112233', '1d (1).jpeg'),
(56, 'dagi', NULL, 'dagi@gmail.com', '$2y$10$o1jwJqbmVLKs7PvXTGJsZ.4ME/bZbPY0Z4U5Rh1PITkm08FBz7Es.', 'doctor', 'active', '2025-07-02 14:15:57', 22, 'f', '0909090988', '1d (2).jpg'),
(57, 'abel', NULL, 'chlotawgedamu@gmail.com', '$2y$10$/iq47jsm/cfe0apSJfCbnOBVbOuJF6S6VNzlspZ9UjEDZxxOYSkTS', 'user', 'active', '2025-07-03 19:32:26', 67, 'yuu', '1234567890', '1d (2).jpg'),
(58, 'dave', NULL, 'dave@gmail.com', '$2y$10$YTzD5VcFWxm.RfMAjidYpOcnn2Co8Ryck8z2kEt3S5Z/C6DwxsPIy', 'doctor', 'active', '2025-07-03 21:09:29', 24, 'adama', '0912345443', '1d (2).jpg'),
(59, 'alakem', NULL, 'alk@gmail.com', '$2y$10$FVp32oXwPfuS6g8ZWLHble934IQjNkcd1TQ6mleuuG103AfdZXvdG', 'user', 'active', '2025-07-03 21:12:25', 33, 'nn', '1234567890', 'profile_68308903673241.05972583.jpg'),
(60, 'english', NULL, 'Eng@gmail.com', '$2y$10$vKT205eVWOOvuEoVAE3Ih.mc4u1lBojhn/IpOOLlWK9F2Ti3i78B2', 'doctor', 'active', '2025-07-05 17:15:28', 30, 'USA', '5647382910', '1d (2).jpg'),
(61, 'henok', NULL, 'henok@gmail.com', '$2y$10$.bl2L4pFuYBa4DuFokrw9eC.hskC..ce2V6op.xvUbpNFFyVgEVMS', 'user', 'active', '2025-07-07 09:38:26', 16, 'lamberte', '0909090909', 'profile_68308799655da8.16440387.jpg'),
(62, 'sssssssss', NULL, 'ss@gmail.com', '$2y$10$.P0WJopNmV8ibYN2VxrfmONUNh0qXLQGaf1stKoBus6mmuN.Um2vS', 'doctor', 'active', '2025-07-08 09:19:06', 23, 'addis', '0909090909', '1d (1).jpeg'),
(63, 'mmmmmm', NULL, 'mm@gmail.com', '$2y$10$2erVxaaPThg3/1FHRVwGjebSicVbYBztUSyvojlMuhF7y2NFSzX8m', 'user', 'active', '2025-07-08 09:32:38', 25, 'addis', '9999999999', '1d (2).jpg'),
(64, 'hi', NULL, 'hi@gmail.com', '$2y$10$UaV7tIKdSMd7KyWMZyieAejHUVcSDcESiI56OdpTm5g93ia3pJ/t2', 'doctor', 'active', '2025-07-11 11:53:40', 35, 'bole', '0988776655', 'profile_686932214924f8.62008707.jpeg'),
(65, 'user', NULL, 'user@gmail.com', '$2y$10$0SqGsQUOjnTLUq0SNf57U.Tk.VCb99eIw8KhqgLsfKaozCvKnIwye', 'user', 'active', '2025-07-11 12:19:20', 55, 'arada', '0911223344', 'profile_687106e077cf66.75006132.jpg'),
(66, 'Login', 'Male', 'logingmail.com', '$2y$10$EL8g62Q5/FXoCjXBcFNLLOCydGjhphuGBnvf4kGLsjXtfmZIbTUpy', 'user', 'active', '2025-07-12 09:08:15', 33, 'arada', '0913744392', 'profile_68308799655da8.16440387.jpg'),
(67, 'home', 'Male', 'home@gmail.com', '$2y$10$f8fqWyocGZsWInVdj68yDeSMDq5rhWnSDYJBHW3cAYB0k.cJaFpf2', 'doctor', 'active', '2025-07-12 09:26:02', 22, 'yeka', '0911121314', 'doctor_41_1751722281.jpg');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `doctor_ratings`
--
ALTER TABLE `doctor_ratings`
  ADD CONSTRAINT `doctor_ratings_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_ratings_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_ratings_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_4` FOREIGN KEY (`medication_id`) REFERENCES `medications` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
