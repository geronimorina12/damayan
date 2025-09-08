-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2025 at 02:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `damayan_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `assistance_distribution`
--

CREATE TABLE `assistance_distribution` (
  `distribution_id` bigint(20) UNSIGNED NOT NULL,
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `distribution_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beneficiary`
--

CREATE TABLE `beneficiary` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `relation` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `birth_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beneficiary`
--

INSERT INTO `beneficiary` (`id`, `user_id`, `name`, `relation`, `age`, `birth_date`, `created_at`, `updated_at`) VALUES
(8, 10, 'Mellie Joy G Arevalo', 'Spouse', 39, '1986-06-04', NULL, NULL),
(9, 10, 'Chim Chim Gotis', 'Daughter', 20, '2005-07-24', NULL, NULL),
(10, 11, 'Marjorie Gotis', 'Spouse', 48, '1977-06-04', NULL, NULL),
(11, 11, 'Ayessa Marie Gotis', 'Daughter', 25, '2000-04-14', NULL, NULL),
(12, 11, 'Alexander Akon Gotis', 'Son', 22, '2003-01-12', NULL, NULL),
(13, 12, 'Conchita R. Gotis', 'Spouse', 50, '1975-09-18', NULL, NULL),
(14, 12, 'Abegail R. Gotis', 'Daughter', 30, '1995-08-28', NULL, NULL),
(15, 12, 'Charlen R. Gotis', 'Daughter', 25, '2000-04-18', NULL, NULL),
(16, 14, 'Aileen J. Gotis', 'Spouse', 49, '1976-06-04', NULL, NULL),
(17, 14, 'Ar-lyn J. Gotis', 'Daughter', 30, '1995-07-24', NULL, NULL),
(18, 16, 'Medina A. Gisalan', 'Daughter', 30, '1995-02-04', NULL, NULL),
(19, 16, 'Meldave A. Gisalan', 'Daughter', 30, '1995-07-30', NULL, NULL),
(20, 16, 'Jimboy A. Gisalan', 'Son', 30, '1995-09-04', NULL, NULL),
(21, 17, 'Rowena Ambalong Cid', 'Spouse', 45, '1980-06-04', NULL, NULL),
(22, 17, 'Rizza Ambalong Cid', 'Daughter', 25, '2000-06-04', NULL, NULL),
(23, 17, 'Rendel Ambalong Cid', 'Son', 24, '2001-06-04', NULL, NULL),
(24, 18, 'Melanie Guna Armenta', 'Daughter', 30, '1995-06-04', NULL, NULL),
(25, 18, 'Merlisa R. Guna', 'Daughter', 25, '2000-06-04', NULL, NULL),
(26, 18, 'Andrea G. Armenta', 'Daughter', 29, '1996-06-04', NULL, NULL),
(27, 19, 'Maribel Guna', 'Spouse', 45, '1980-06-04', NULL, NULL),
(28, 19, 'Archie Guna', 'Son', 25, '2000-06-04', NULL, NULL),
(29, 19, 'Alisha Guna', 'Daughter', 22, '2003-06-04', NULL, NULL),
(30, 20, 'Rogelio G. Miranda Jr.', 'Son', 22, '2003-12-24', NULL, NULL),
(31, 20, 'Mark ryan G. Miranda', 'Son', 23, '2002-07-14', NULL, NULL),
(32, 22, 'Enrique B. Revilla', 'Son', 22, '2003-01-03', NULL, NULL),
(33, 22, 'Erica B. Revilla', 'Daughter', 23, '2002-06-09', NULL, NULL),
(34, 22, 'Leslie B. Revilla', 'Daughter', 24, '2001-05-04', NULL, NULL),
(35, 23, 'Cristine Jane R. Guna', 'Spouse', 45, '1980-04-14', NULL, NULL),
(36, 23, 'Jaira R. Guna', 'Daughter', 22, '2003-05-01', NULL, NULL),
(37, 23, 'Jath R. Guna', 'Son', 25, '2000-06-14', NULL, NULL),
(38, 24, 'Nadia Balansay Bermillo', 'Sibling', 45, '1980-09-05', NULL, NULL),
(39, 25, 'Zeny Gotis Balansay', 'Daughter', 30, '1995-06-24', NULL, NULL),
(40, 25, 'Christine Joy Gotis Balansay', 'Daughter', 22, '2003-07-08', NULL, NULL),
(41, 26, 'Erma Gotis Geraldino', 'Spouse', 45, '1980-06-04', NULL, NULL),
(42, 26, 'Livirjoy Gotis Geraldino', 'Daughter', 22, '2003-06-04', NULL, NULL),
(43, 26, 'Jiecer Gotis Geraldino', 'Son', 23, '2002-06-18', NULL, NULL),
(44, 28, 'Leony D. Gotis', 'Spouse', 44, '1981-11-14', NULL, NULL),
(45, 28, 'Jay D. Gotis', 'Son', 22, '2003-07-24', NULL, NULL),
(46, 30, 'Jovelyn Tolete David', 'Spouse', 44, '1981-01-06', NULL, NULL),
(47, 30, 'Mary Grace T. David', 'Daughter', 22, '2003-08-28', NULL, NULL),
(48, 30, 'Ashley Nicole T. David', 'Daughter', 25, '2000-05-25', NULL, NULL),
(49, 31, 'Leonista Endaya Estidona', 'Spouse', 45, '1980-06-14', NULL, NULL),
(50, 31, 'Mark B. Zacarias', 'Son', 22, '2003-01-07', NULL, NULL),
(51, 31, 'Kenjie E. Zacarias', 'Son', 23, '2002-04-14', NULL, NULL),
(52, 32, 'Leonista Endaya Estidona', 'Spouse', 45, '1980-06-14', NULL, NULL),
(53, 32, 'Mark B. Zacarias', 'Son', 22, '2003-01-07', NULL, NULL),
(54, 32, 'Kenjie E. Zacarias', 'Son', 23, '2002-04-14', NULL, NULL),
(55, 33, 'Lailanie B. Zacarias', 'Daughter', 25, '2000-07-06', NULL, NULL),
(56, 33, 'Joanafe B. Zacarias', 'Daughter', 22, '2003-06-09', NULL, NULL),
(57, 33, 'Ambher Zacarias', 'Daughter', 23, '2002-06-07', NULL, NULL),
(58, 35, 'Enan G. Canale', 'Spouse', 45, '1980-05-09', NULL, NULL),
(59, 35, 'Mary-Ann Canale', 'Daughter', 22, '2003-02-19', NULL, NULL),
(60, 37, 'Janeth B. Gernale', 'Spouse', 45, '1980-09-30', NULL, NULL),
(61, 37, 'Erica B. Gernale', 'Daughter', 22, '2003-10-05', NULL, NULL),
(62, 38, 'Pura Bejerano Vargas', 'Spouse', 45, '1980-05-05', NULL, NULL),
(63, 38, 'Allan Bejerano Vargas', 'Daughter', 22, '2003-10-24', NULL, NULL),
(64, 39, 'Generose Gotis Geraldino', 'Spouse', 45, '1980-04-05', NULL, NULL),
(65, 39, 'Julie Gotis Geraldino', 'Daughter', 22, '2003-12-04', NULL, NULL),
(66, 41, 'Meycailla Gestiada', 'Spouse', 45, '1980-06-30', NULL, NULL),
(67, 41, 'Cairen G. Golde', 'Daughter', 22, '2003-09-15', NULL, NULL),
(68, 41, 'Relan G. Golde', 'Son', 23, '2002-01-05', NULL, NULL),
(69, 42, 'Princess Venus Golde Almodiel', 'Spouse', 45, '1980-05-31', NULL, NULL),
(70, 45, 'Jowey Patiño Malisa', 'Spouse', 45, '1980-06-24', NULL, NULL),
(71, 45, 'John Louie Patiño Malisa', 'Son', 22, '2003-07-14', NULL, NULL),
(72, 45, 'Kenneth Patiño Malisa', 'Son', 23, '2002-06-09', NULL, NULL),
(73, 46, 'EJ Andaya Gotis', 'Son', 23, '2002-05-14', NULL, NULL),
(74, 46, 'Elyn Andaya Gotis', 'Daughter', 22, '2003-10-04', NULL, NULL),
(75, 47, 'Joel L. Perolino', 'Spouse', 45, '1980-06-18', NULL, NULL),
(76, 47, 'Janice A. Perolino', 'Daughter', 22, '2003-07-17', NULL, NULL),
(77, 48, 'Maricris Being', 'Spouse', 45, '1980-09-09', NULL, NULL),
(78, 48, 'Mark John Being', 'Son', 22, '2003-12-04', NULL, NULL),
(79, 48, 'Jams Axel Being', 'Son', 23, '2002-07-16', NULL, NULL),
(80, 49, 'Ronie Benig Gestiada', 'Spouse', 45, '1980-06-04', NULL, NULL),
(81, 49, 'Rose Benig Gestiada', 'Daughter', 22, '2003-01-04', NULL, NULL),
(82, 53, 'Lyra Hibe Gipa Bermillo', 'Spouse', 45, '1980-06-04', NULL, NULL),
(83, 55, 'Vidalyn Benig', 'Sibling', 35, '1990-06-11', NULL, NULL),
(84, 60, 'John Jovy L. Gotis', 'Son', 22, '2003-06-05', NULL, NULL),
(85, 64, 'Herminia Gotis', 'Spouse', 45, '1980-06-04', NULL, NULL),
(86, 65, 'Vicitacion G. Gisalan', 'Spouse', 45, '1980-06-04', NULL, NULL),
(87, 66, 'Macmila B. Gimoro', '', 0, '0000-00-00', NULL, NULL),
(88, 66, 'Madelyn P. Gimoro', '', 0, '0000-00-00', NULL, NULL),
(89, 66, 'Micaella Mae P. Gimoro', '', 0, '0000-00-00', NULL, NULL),
(90, 66, 'Mark Jervis P. Gimoro', '', 0, '0000-00-00', NULL, NULL),
(91, 67, 'Sonny B. Gotis', 'Spouse', 35, '1990-04-18', NULL, NULL),
(92, 67, 'Aizel Juta', 'Son', 10, '2015-07-03', NULL, NULL),
(93, 67, 'Sedrick J. Gotis', 'Son', 10, '2015-07-03', NULL, NULL),
(94, 67, 'Siyhan J. Gotis', 'Son', 10, '2015-07-03', NULL, NULL),
(95, 68, 'Lilibeth R. Gotis', 'Spouse', 50, '1975-07-01', NULL, NULL),
(96, 68, 'Charlemien R. Gotis', 'Daughter', 25, '2000-06-25', NULL, NULL),
(97, 68, 'Belly Ray Cyrus R. Gotis', 'Son', 24, '2001-07-30', NULL, NULL),
(98, 69, 'Romeo Geraldino', 'Son', 50, '1975-08-25', NULL, NULL),
(99, 70, 'Rachel Ann Gliban', 'Daughter', 25, '2000-07-30', NULL, NULL),
(100, 70, 'Rizzalyn May Gliban', 'Daughter', 24, '2001-05-03', NULL, NULL),
(101, 70, 'Rolandoil Gliban', 'Son', 23, '2002-06-03', NULL, NULL),
(102, 70, 'Roldan Gliban', 'Son', 22, '2003-07-03', NULL, NULL),
(103, 71, 'Cristina G. Gotis', 'Spouse', 40, '1985-12-12', NULL, NULL),
(104, 71, 'Andrew G. Gotis', 'Son', 20, '2005-04-03', NULL, NULL),
(105, 71, 'Eugene G. Gotis', 'Daughter', 15, '2010-05-05', NULL, NULL),
(106, 71, 'Wesley G. Gotis', 'Son', 14, '2016-07-13', NULL, NULL),
(107, 71, 'Maria Cathrine Gimoro', 'Spouse', 45, '1980-04-01', NULL, NULL),
(108, 71, 'Mike Clark G. Tolintino', 'Son', 25, '2000-07-13', NULL, NULL),
(109, 71, 'Caint G. Tolintino', 'Son', 22, '2003-01-23', NULL, NULL),
(110, 72, 'Mylly D. Demetrial', 'Spouse', 60, '1965-09-22', NULL, NULL),
(111, 72, 'Nelly Gotis', 'Daughter', 35, '1990-05-11', NULL, NULL),
(112, 72, 'Justine Culmatico', 'Son', 35, '1991-03-25', NULL, NULL),
(113, 73, 'Harold G. Gimoro', 'Son', 35, '1990-07-03', NULL, NULL),
(114, 73, 'Jesabelle B. Gimoro', 'Daughter', 34, '1991-01-16', NULL, NULL),
(115, 73, 'Mike Francis Gimoro', 'Son', 33, '1992-07-11', NULL, NULL),
(116, 74, 'Marily G. Gotis', 'Spouse', 65, '1955-04-11', NULL, NULL),
(117, 74, 'Marvin G. Golpeo', 'Son', 35, '1990-07-14', NULL, NULL),
(118, 75, 'Mary Grace V. Gimoro', 'Spouse', 35, '1990-07-31', NULL, NULL),
(119, 75, 'Mary Rose G. Villanueva', 'Daughter', 15, '2010-06-28', NULL, NULL),
(120, 76, 'Alex B. Gimoro', 'Spouse', 48, '1977-06-02', NULL, NULL),
(121, 76, 'Emerson Mabanan', 'Son', 22, '2003-05-24', NULL, NULL),
(122, 77, 'Myra T. Bitanguar', 'Spouse', 33, '1992-09-22', NULL, NULL),
(123, 77, 'Michele B. Juta', 'Daughter', 13, '2012-06-13', NULL, NULL),
(124, 78, 'Emelyn G. Juta', 'Spouse', 35, '1990-08-30', NULL, NULL),
(125, 78, 'Messy G. Juta', 'Daughter', 15, '2010-11-13', NULL, NULL),
(126, 78, 'Tesoy G. Juta', 'Son', 14, '2011-01-30', NULL, NULL),
(127, 79, 'Maureen Keth Geraldino', 'Daughter', 17, '2007-12-02', NULL, NULL),
(128, 80, 'Sarah Mae M. Espaldon', 'Daughter', 22, '2002-12-11', NULL, NULL),
(129, 80, 'Shanel M. Espaldon', 'Son', 23, '2002-06-18', NULL, NULL),
(130, 80, 'Shaina M. Espaldon', 'Daughter', 20, '2005-06-19', NULL, NULL),
(131, 80, 'Dave M. Espaldon', 'Spouse', 41, '1984-01-03', NULL, NULL),
(132, 81, 'Jayson C. Rosete', 'Son', 22, '2003-12-11', NULL, NULL),
(133, 81, 'Luzviminda Roseta', 'Spouse', 0, '0000-00-00', NULL, NULL),
(134, 81, 'Joshua Roseta', 'Son', 21, '2004-06-26', NULL, NULL),
(135, 82, 'Joan Gimoro Balansay', 'Daughter', 12, '2013-10-05', NULL, NULL),
(136, 82, 'Jonhn Michael G. Balansay', 'Son', 21, '2003-05-10', NULL, NULL),
(137, 82, 'Joana Joyce G. Balansay', 'Spouse', 41, '1984-12-05', NULL, NULL),
(138, 82, 'Janine Ziah G. Balansay', 'Daughter', 21, '2003-02-18', NULL, NULL),
(139, 83, 'Jonald Breboneria', 'Son', 26, '1999-11-18', NULL, NULL),
(140, 83, 'Jolan Breboneria', 'Son', 27, '1998-09-05', NULL, NULL),
(141, 84, 'Eldred Malinao Valdez', 'Spouse', 50, '1975-06-05', NULL, NULL),
(142, 84, 'Aldred C. Valdez', 'Son', 27, '1998-09-08', NULL, NULL),
(143, 84, 'Viancy Nicol C. Valdez', 'Daughter', 16, '2009-05-12', NULL, NULL),
(144, 84, 'Dwyth C. Valdez', 'Daughter', 22, '2003-10-17', NULL, NULL),
(145, 84, 'Zia C. Valdez', 'Daughter', 9, '2016-12-18', NULL, NULL),
(146, 85, 'Felisa M. Bautista', 'Daughter', 35, '1990-07-07', NULL, NULL),
(147, 85, 'Jason M. Bautista', 'Son', 23, '2002-12-11', NULL, NULL),
(148, 85, 'Justine B. Clamar', 'Son', 20, '2005-08-10', NULL, NULL),
(149, 86, 'Julien Daculan', 'Spouse', 50, '1975-03-22', NULL, NULL),
(150, 86, 'Blesie D. Malinao', 'Daughter', 25, '2000-10-23', NULL, NULL),
(151, 86, 'John Jacob D. Malinao', 'Son', 20, '2005-12-01', NULL, NULL),
(152, 87, 'Toby G. Malinao', 'Spouse', 60, '1965-11-10', NULL, NULL),
(153, 87, 'Jonald G. Malinao', 'Son', 35, '1990-02-11', NULL, NULL),
(154, 87, 'Francis G. Malinao', 'Daughter', 25, '2000-03-09', NULL, NULL),
(155, 87, 'Christian G. Malinao', 'Daughter', 20, '2005-08-07', NULL, NULL),
(156, 87, 'Maryjoy G. Malinao', 'Daughter', 14, '2011-04-20', NULL, NULL),
(157, 88, 'Renante Cabulit Gimoro', 'Spouse', 44, '1981-12-10', NULL, NULL),
(158, 88, 'Cristy Vargas Gimoro', 'Daughter', 25, '2000-12-05', NULL, NULL),
(159, 88, 'Regine Vargas Gimoro', 'Daughter', 26, '1999-07-22', NULL, NULL),
(160, 88, 'Jinerose Vargas Gimoro', 'Daughter', 23, '2002-02-04', NULL, NULL),
(161, 88, 'Rolesa Vargas Gimoro', 'Daughter', 25, '2000-01-03', NULL, NULL),
(162, 88, 'Mercy Joy Vargas Gimoro', 'Daughter', 27, '1998-04-10', NULL, NULL),
(163, 91, 'Rodelia P. Gimoro', 'Daughter', 22, '2002-12-11', NULL, NULL),
(164, 91, 'Roldan P. Gimoro', 'Son', 20, '2005-02-10', NULL, NULL),
(165, 91, 'Richelle P. Gimoro', 'Daughter', 8, '2017-01-02', NULL, NULL),
(166, 91, 'Rodel P. Gimoro', 'Spouse', 53, '1972-11-10', NULL, NULL),
(167, 92, 'Helan G. Panaligan', 'Spouse', 60, '1965-11-08', NULL, NULL),
(168, 93, 'Bernadette C. Gimoro', 'Spouse', 55, '1970-11-06', NULL, NULL),
(169, 93, 'Myrnalyn C. Gimoro', 'Daughter', 24, '2001-10-14', NULL, NULL),
(170, 93, 'Jasmine C. Gimoro', 'Daughter', 23, '2002-01-07', NULL, NULL),
(171, 93, 'Myrna C. Gimoro', 'Daughter', 20, '2005-12-19', NULL, NULL),
(172, 94, 'Caren G. Delariarte', 'Spouse', 38, '1987-05-20', NULL, NULL),
(173, 94, 'Ella Mae G. Delariarte', 'Daughter', 20, '2005-09-05', NULL, NULL),
(174, 94, 'Killy Niño G. Delariate', 'Daughter', 15, '2010-10-14', NULL, NULL),
(175, 96, 'Dianne G. Ringor', 'Daughter', 22, '2003-10-27', NULL, NULL),
(176, 96, 'Cathy Nichole G. Ringor', 'Daughter', 19, '2005-11-01', NULL, NULL),
(177, 97, 'Juvy V. Golpeo', 'Spouse', 45, '1980-09-06', NULL, NULL),
(178, 97, 'Jobelle V. Golpeo', 'Daughter', 21, '2004-03-10', NULL, NULL),
(179, 98, 'Wyanna Mae Malinao', 'Daughter', 23, '2003-06-03', NULL, NULL),
(180, 98, 'Rafael Raniel M. Glipeneo', 'Spouse', 57, '1968-07-27', NULL, NULL),
(181, 99, 'Albert G. Juta', 'Son', 26, '1990-12-05', NULL, NULL),
(182, 99, 'Aiko G. Juta', 'Daughter', 38, '1987-07-08', NULL, NULL),
(183, 99, 'Alfredo G. Juta', 'Son', 25, '2000-01-09', NULL, NULL),
(184, 99, 'Arvin G. Juta', 'Son', 47, '1978-09-26', NULL, NULL),
(185, 100, 'Ronald Alan B. Gimoro', 'Spouse', 41, '1984-09-24', NULL, NULL),
(186, 100, 'Myla G. Gimoro', 'Daughter', 17, '2008-12-10', NULL, NULL),
(187, 100, 'Meggy Angelo G. Gimoro', 'Son', 10, '2015-06-10', NULL, NULL),
(188, 100, 'Raniel G. Gimoro', 'Son', 21, '2004-01-15', NULL, NULL),
(189, 101, 'Sanjo D. Agnote', 'Son', 15, '2010-06-07', NULL, NULL),
(190, 101, 'Julie Mae D. Agnote', 'Daughter', 22, '2002-12-11', NULL, NULL),
(191, 101, 'Sarah Jane D. Agnote', 'Daughter', 19, '2005-08-26', NULL, NULL),
(192, 101, 'Jack D. Agnote', 'Son', 24, '2000-10-14', NULL, NULL),
(193, 101, 'Jomar Espaldon Agnote', 'Spouse', 35, '1990-04-10', NULL, NULL),
(194, 102, 'Adrian Miranda Breboneria', 'Son', 26, '1999-07-06', NULL, NULL),
(195, 102, 'Sheila Mae Breboneria', 'Daughter', 29, '1996-10-07', NULL, NULL),
(196, 102, 'Reymar Breboneria', 'Son', 23, '2002-12-11', NULL, NULL),
(197, 102, 'Gerald Breboneria', 'Son', 25, '2000-01-07', NULL, NULL),
(198, 102, 'Hezel Breboneria', 'Spouse', 47, '1978-10-12', NULL, NULL),
(199, 102, 'Sofia Breboneria', 'Daughter', 16, '2009-07-03', NULL, NULL),
(200, 103, 'Ronel G. Dellomas', 'Son', 38, '1987-11-21', NULL, NULL),
(201, 103, 'Cyrus D. Lacay', 'Son', 28, '1997-06-30', NULL, NULL),
(202, 104, 'Maryrose C. Espaldon', 'Spouse', 35, '1990-11-08', NULL, NULL),
(203, 104, 'Cris C. Espaldon', 'Son', 22, '2003-07-15', NULL, NULL),
(204, 104, 'Jimcarl C. Espaldon', 'Son', 18, '2007-01-01', NULL, NULL),
(205, 105, 'Marvin L. Geraldino', 'Son', 45, '1980-09-05', NULL, NULL),
(206, 105, 'Maricris L. Geraldino', 'Daughter', 47, '1978-07-18', NULL, NULL),
(207, 105, 'Martin L. Geraldino', 'Son', 35, '1990-04-08', NULL, NULL),
(208, 105, 'Raul Malinao Geraldino', 'Spouse', 69, '1956-01-08', NULL, NULL),
(209, 106, 'Marivic L. Geraldino', 'Daughter', 29, '1996-07-25', NULL, NULL),
(210, 106, 'Mary Ann L. Geraldino', 'Daughter', 24, '2001-08-28', NULL, NULL),
(211, 106, 'Jimna L. Geraldino', 'Spouse', 55, '1970-02-18', NULL, NULL),
(212, 107, 'Crishelle E. Villanueva', 'Spouse', 40, '1985-12-20', NULL, NULL),
(213, 107, 'Crisjay E. Villanueva', 'Daughter', 22, '2003-03-18', NULL, NULL),
(214, 107, 'Jihoo E. Villanueva', 'Son', 16, '2009-06-09', NULL, NULL),
(215, 108, 'Rosiel G. Espaldon', 'Daughter', 38, '1987-10-14', NULL, NULL),
(216, 108, 'Deanthristan G. Espaldon', 'Son', 25, '2001-09-05', NULL, NULL),
(217, 108, 'Clelsea G. Espaldon', 'Daughter', 23, '2002-07-18', NULL, NULL),
(218, 108, 'Ainsly G. Espaldon', 'Daughter', 20, '2005-11-10', NULL, NULL),
(219, 109, 'Roger C. Aranjues', 'Spouse', 47, '1978-12-20', NULL, NULL),
(220, 109, 'Kristine Nicole A. Galligues', 'Daughter', 24, '2000-09-19', NULL, NULL),
(221, 109, 'Eduardo Jr A. Galligues', 'Son', 23, '2002-06-17', NULL, NULL),
(222, 110, 'Jovelyn G. Prudenciado', 'Spouse', 37, '1990-07-23', NULL, NULL),
(223, 110, 'Sophia Mae P. Ovilla', 'Daughter', 21, '2004-12-01', NULL, NULL),
(224, 110, 'Carlyn Jane P. Ovilla', 'Daughter', 15, '2010-01-02', NULL, NULL),
(225, 111, 'Noralyn S. Arimado', 'Spouse', 45, '1980-12-26', NULL, NULL),
(226, 111, 'Scarleth A. Lazona', 'Daughter', 21, '2004-06-25', NULL, NULL),
(227, 111, 'Erich A. Lazona', 'Son', 17, '2008-02-01', NULL, NULL),
(228, 112, 'Marile D. David', 'Spouse', 55, '1970-09-30', NULL, NULL),
(229, 112, 'Maryrose D. David', 'Daughter', 34, '1991-08-10', NULL, NULL),
(230, 112, 'Brixton D. David', 'Son', 25, '2001-12-01', NULL, NULL),
(231, 113, 'Jobel V. Golde', 'Son', 23, '2002-01-04', NULL, NULL),
(232, 113, 'Jhermaine V. Golde', 'Daughter', 16, '2009-12-10', NULL, NULL),
(233, 113, 'Ramir G. Golde', 'Son', 14, '2011-06-08', NULL, NULL),
(234, 114, 'Romaldo G. Golde', 'Spouse', 41, '1984-04-04', NULL, NULL),
(235, 114, 'John Yuey B. Bermillo', 'Son', 24, '2000-04-04', NULL, NULL),
(236, 114, 'Rowela Mae B. Golde', 'Daughter', 22, '2003-08-10', NULL, NULL),
(237, 114, 'Dearviey B. Golde', 'Son', 22, '2002-12-11', NULL, NULL),
(238, 114, 'Dyrie B. Golde', 'Son', 21, '2004-03-10', NULL, NULL),
(239, 115, 'Maximino B. Villanueva', 'Son', 47, '1978-03-22', NULL, NULL),
(240, 115, 'Diana B. Villanueva', 'Daughter', 37, '1988-06-30', NULL, NULL),
(241, 116, 'Clarissa Breboneria Villanueva', 'Spouse', 30, '1995-11-03', NULL, NULL),
(242, 116, 'Alver Ace V. Laurente', 'Son', 6, '2019-06-10', NULL, NULL),
(243, 117, 'Jemrick V. Delos Santos', 'Spouse', 38, '1987-04-09', NULL, NULL),
(244, 118, 'Meann Gernan Golde', 'Spouse', 31, '1994-08-10', NULL, NULL),
(245, 118, 'Daneil Villanueva', 'Son', 20, '2005-07-29', NULL, NULL),
(246, 118, 'Ana luna Villanueva', 'Daughter', 13, '2013-12-08', NULL, NULL),
(247, 118, 'Darwin Villanueva', 'Son', 22, '2003-02-11', NULL, NULL),
(248, 119, 'Mercy Z. Villanueva', 'Spouse', 65, '1960-10-01', NULL, NULL),
(249, 119, 'Jakebyron Z. Villanueva', 'Son', 38, '1987-07-15', NULL, NULL),
(250, 119, 'Jullaine Z. Villanueva', 'Daughter', 28, '1997-01-26', NULL, NULL),
(251, 119, 'Matt Z. Villanueva', 'Son', 25, '2001-08-08', NULL, NULL),
(252, 119, 'Rever Z. Villanueva', 'Son', 22, '2003-11-02', NULL, NULL),
(253, 120, 'Sheryl B. Villanueva', 'Spouse', 38, '1987-04-03', NULL, NULL),
(254, 120, 'Rochell B. Villanueva', 'Daughter', 35, '1990-12-04', NULL, NULL),
(255, 120, 'Roxanne B. Villanueva', 'Daughter', 25, '2000-01-27', NULL, NULL),
(256, 120, 'Rizza B. Villanueva', 'Daughter', 23, '2002-03-13', NULL, NULL),
(257, 120, 'Rex B. Villanueva', 'Son', 20, '2005-05-01', NULL, NULL),
(258, 121, 'Shyril E. Villanueva', 'Daughter', 20, '2004-08-10', NULL, NULL),
(259, 121, 'Jenald E. Villanueva', 'Spouse', 43, '1982-02-10', NULL, NULL),
(260, 121, 'Shyro E. Villanueva', 'Daughter', 21, '2003-06-26', NULL, NULL),
(261, 121, 'Jenalyn E. Villanueva', 'Daughter', 15, '2010-07-02', NULL, NULL),
(262, 121, 'Sharlene E. Villanueva', 'Daughter', 23, '2002-09-19', NULL, NULL),
(263, 121, 'Jerold E. Villanueva', 'Son', 9, '2016-07-04', NULL, NULL),
(264, 121, 'Jhoros E. Villanueva', 'Son', 23, '2002-12-11', NULL, NULL),
(265, 124, 'Ashley A. Evasco', 'Spouse', 21, '2004-05-15', NULL, NULL),
(266, 124, 'Riley E. Geraldino', 'Son', 0, '2025-06-25', NULL, NULL),
(267, 125, 'Ramil Flores', 'Spouse', 29, '1996-12-25', NULL, NULL),
(268, 128, 'Samuel S. Culbengan', 'Son', 28, '1997-02-09', NULL, NULL),
(269, 128, 'Samuel C. Culbengan Sr', 'Spouse', 66, '1959-01-10', NULL, NULL),
(270, 128, 'Andro C. Osabel', 'Son', 24, '2001-10-19', NULL, NULL),
(271, 128, 'Jared C. Canale', 'Son', 21, '2003-09-08', NULL, NULL),
(272, 130, 'Roseta Pernito', 'Daughter', 21, '2004-06-16', NULL, NULL),
(273, 130, 'Novy Pernito', 'Daughter', 17, '2008-07-29', NULL, NULL),
(274, 131, 'Jim Bayoca', 'Son', 22, '2003-03-10', NULL, NULL),
(275, 131, 'Josh B. Bayoca', 'Son', 19, '2006-07-20', NULL, NULL),
(276, 132, 'Arlyn B. Gimao', 'Daughter', 22, '2003-06-19', NULL, NULL),
(277, 132, 'Gina Breboneria Gimao', 'Spouse', 47, '1978-12-11', NULL, NULL),
(278, 133, 'Luc Lejeune', 'Spouse', 46, '1979-12-05', NULL, NULL),
(279, 133, 'Jonalyn M. Malgapo', 'Daughter', 23, '2003-06-25', NULL, NULL),
(280, 133, 'Joselito M. Malgapo Jr', 'Daughter', 11, '2014-07-06', NULL, NULL),
(281, 133, 'Jennfer M. Malgapo', 'Daughter', 20, '2005-11-08', NULL, NULL),
(282, 134, 'Michelle G. Roseta', 'Spouse', 45, '1980-03-09', NULL, NULL),
(283, 134, 'Emmanuel G. Roseta', 'Son', 21, '2003-08-06', NULL, NULL),
(284, 135, 'Erlinda G. Gotis', 'Spouse', 46, '1979-01-01', NULL, NULL),
(285, 136, 'Marissa G. Gimen', 'Spouse', 27, '1998-03-02', NULL, NULL),
(286, 136, 'Meryjoy G. Gotis', 'Daughter', 14, '2012-06-15', NULL, NULL),
(287, 136, 'Althea Jhoy G. Gotis', 'Daughter', 6, '2019-07-05', NULL, NULL),
(288, 137, 'Elsie Paz', 'Spouse', 30, '1995-06-04', '2025-08-30 10:38:21', '2025-08-30 10:38:21'),
(289, 137, 'Prince Gian Gotis', 'Son', 21, '2004-03-03', '2025-08-30 10:38:21', '2025-08-30 10:38:21'),
(290, 137, 'Empres Gotis', 'Daughter', 22, '2003-05-21', '2025-08-30 10:38:21', '2025-08-30 10:38:21'),
(291, 138, 'Rodel Villiamor', 'Spouse', 51, '1973-09-22', '2025-08-30 10:42:39', '2025-08-30 10:42:39'),
(292, 138, 'Adrian Gotis', 'Son', 29, '1996-04-02', '2025-08-30 10:42:39', '2025-08-30 10:42:39'),
(293, 138, 'Adnon Gotis', 'Son', 27, '1999-05-05', '2025-08-30 10:42:39', '2025-08-30 10:42:39'),
(294, 138, 'Andrian Gotis', 'Son', 25, '2001-01-25', '2025-08-30 10:42:39', '2025-08-30 10:42:39'),
(295, 139, 'Mary Joy A. Estolonio', 'Spouse', 37, '1988-07-30', '2025-08-30 10:43:32', '2025-08-30 10:43:32'),
(296, 139, 'Jilian E. Birata', 'Daughter', 18, '2007-12-02', '2025-08-30 10:43:32', '2025-08-30 10:43:32'),
(297, 139, 'Crisanta E. Birata', 'Daughter', 17, '2008-05-01', '2025-08-30 10:43:32', '2025-08-30 10:43:32'),
(298, 139, 'Romel E. Birata', 'Son', 15, '2010-03-17', '2025-08-30 10:43:32', '2025-08-30 10:43:32'),
(299, 140, 'Aillen G. Gliponeo', 'Spouse', 37, '1988-11-25', '2025-08-30 10:44:10', '2025-08-30 10:44:10'),
(300, 140, 'Alexandra Masdo', 'Daughter', 18, '2007-05-05', '2025-08-30 10:44:10', '2025-08-30 10:44:10'),
(301, 140, 'Aldren Masdo', 'Son', 15, '2010-07-19', '2025-08-30 10:44:10', '2025-08-30 10:44:10'),
(302, 140, 'Alegrian Masdo', 'Son', 10, '2015-05-30', '2025-08-30 10:44:10', '2025-08-30 10:44:10'),
(303, 141, 'Denalyn V. Gimoro', 'Sibling', 21, '2003-10-01', '2025-08-30 10:45:28', '2025-08-30 10:45:28'),
(304, 142, 'Melva C. Delariarte', 'Daughter', 46, '1979-06-30', '2025-08-30 10:46:04', '2025-08-30 10:46:04'),
(305, 142, 'Joenard C. Delariarte', 'Son', 45, '1980-01-25', '2025-08-30 10:46:04', '2025-08-30 10:46:04'),
(306, 142, 'Jeric C. Delariarte', 'Son', 40, '1985-02-20', '2025-08-30 10:46:04', '2025-08-30 10:46:04'),
(307, 143, 'Joel R. Delariarte', 'Spouse', 65, '1960-11-29', '2025-08-30 10:46:47', '2025-08-30 10:46:47'),
(308, 144, 'Analyn B. Delos Reyes', 'Spouse', 41, '1984-05-02', '2025-08-30 10:47:20', '2025-08-30 10:47:20'),
(309, 144, 'Clarince D. Estolonio', 'Son', 20, '2005-10-20', '2025-08-30 10:47:20', '2025-08-30 10:47:20'),
(310, 144, 'Clarisse D. Estolonio', 'Daughter', 18, '2007-12-30', '2025-08-30 10:47:20', '2025-08-30 10:47:20'),
(311, 144, 'Nash D. Estolonio', 'Son', 19, '2006-04-24', '2025-08-30 10:47:20', '2025-08-30 10:47:20'),
(312, 145, 'Jonalyn G. Masdo', 'Daughter', 20, '2005-08-09', '2025-08-30 10:47:51', '2025-08-30 10:47:51'),
(313, 145, 'Pauline G. Masdo', 'Daughter', 18, '2007-11-14', '2025-08-30 10:47:51', '2025-08-30 10:47:51'),
(314, 146, 'Marivic F. Morilla', 'Spouse', 35, '1990-11-10', '2025-08-30 10:48:38', '2025-08-30 10:48:38'),
(315, 146, 'Jhon Mark Jamolin', 'Son', 16, '2009-02-14', '2025-08-30 10:48:38', '2025-08-30 10:48:38'),
(316, 147, 'Virginia P. Gotis', 'Spouse', 50, '1975-11-21', '2025-08-30 10:49:19', '2025-08-30 10:49:19'),
(317, 147, 'Azaril P. Gotis', 'Son', 30, '1995-05-05', '2025-08-30 10:49:19', '2025-08-30 10:49:19'),
(318, 147, 'Adamson P. Gotis', 'Son', 22, '2003-07-30', '2025-08-30 10:49:19', '2025-08-30 10:49:19'),
(319, 147, 'Gypsy P. Gotis', 'Son', 25, '2000-09-05', '2025-08-30 10:49:19', '2025-08-30 10:49:19'),
(320, 147, 'Roldan P. Gotis', 'Son', 20, '2005-08-29', '2025-08-30 10:49:19', '2025-08-30 10:49:19'),
(321, 147, 'Nathaniel P. Gotis', 'Son', 15, '2010-11-20', '2025-08-30 10:49:19', '2025-08-30 10:49:19'),
(322, 148, 'Jan Albert E. Gliban', 'Son', 30, '1995-01-26', '2025-08-30 10:51:04', '2025-08-30 10:51:04'),
(323, 148, 'Delia Z. Gliban', 'Daughter', 36, '1989-12-12', '2025-08-30 10:51:04', '2025-08-30 10:51:04'),
(324, 149, 'Irma G. Delariarte', 'Daughter', 25, '2000-06-03', '2025-08-30 10:51:53', '2025-08-30 10:51:53'),
(325, 149, 'Ernesto G. Delariarte', 'Son', 24, '2001-05-30', '2025-08-30 10:51:53', '2025-08-30 10:51:53'),
(326, 149, 'Erold G. Delariarte', 'Son', 20, '2005-07-20', '2025-08-30 10:51:53', '2025-08-30 10:51:53'),
(327, 150, 'Kenchin Tan', 'Sibling', 39, '1986-12-18', '2025-08-30 10:52:33', '2025-08-30 10:52:33'),
(328, 150, 'Ashlie Tan', 'Spouse', 38, '1987-04-25', '2025-08-30 10:52:33', '2025-08-30 10:52:33'),
(329, 150, 'Jakeun Tan', 'Son', 20, '2005-03-14', '2025-08-30 10:52:33', '2025-08-30 10:52:33'),
(330, 150, 'Missy Tan', 'Daughter', 18, '2007-07-29', '2025-08-30 10:52:33', '2025-08-30 10:52:33'),
(331, 151, 'CJ G. Gotis', 'Spouse', 45, '1975-04-30', '2025-08-30 10:53:07', '2025-08-30 10:53:07'),
(332, 151, 'Mereva G. Gotis', 'Daughter', 25, '2000-05-05', '2025-08-30 10:53:07', '2025-08-30 10:53:07'),
(333, 151, 'Ofra G. Gotis', 'Daughter', 22, '2003-07-20', '2025-08-30 10:53:07', '2025-08-30 10:53:07'),
(334, 152, 'Analyn Gliban', 'Spouse', 55, '1970-03-19', '2025-08-30 10:53:35', '2025-08-30 10:53:35'),
(335, 152, 'Ervin Gliban', 'Son', 30, '1995-05-30', '2025-08-30 10:53:35', '2025-08-30 10:53:35'),
(336, 152, 'Alyanna Gliban', 'Daughter', 28, '1997-06-03', '2025-08-30 10:53:35', '2025-08-30 10:53:35'),
(337, 153, 'Gerry Gliban', 'Son', 62, '1963-05-21', '2025-08-30 10:54:21', '2025-08-30 10:54:21'),
(338, 153, 'Precious Prinzessin Gliban', 'Daughter', 67, '1958-09-23', '2025-08-30 10:54:21', '2025-08-30 10:54:21'),
(339, 154, 'John Renald Hamor', 'Spouse', 55, '1970-04-01', '2025-08-30 10:54:49', '2025-08-30 10:54:49'),
(340, 154, 'Kryssa Mae Hamor', 'Daughter', 30, '1995-09-19', '2025-08-30 10:54:49', '2025-08-30 10:54:49'),
(341, 155, 'Joan B. Gliban', 'Spouse', 45, '1980-02-19', '2025-08-30 10:55:26', '2025-08-30 10:55:26'),
(342, 155, 'Trisha Mae Bautista', 'Daughter', 15, '2010-05-03', '2025-08-30 10:55:26', '2025-08-30 10:55:26'),
(343, 155, 'Trexie Mae B. Gliban', 'Daughter', 13, '2012-03-11', '2025-08-30 10:55:26', '2025-08-30 10:55:26'),
(344, 155, 'Trishtal Mae B. Gliban', 'Daughter', 13, '2012-03-11', '2025-08-30 10:55:26', '2025-08-30 10:55:26'),
(345, 156, 'Victiria G. Jamolin', 'Spouse', 60, '1965-06-01', '2025-08-30 10:55:56', '2025-08-30 10:55:56'),
(346, 156, 'Jegar G. Jamolin', 'Son', 30, '1995-05-18', '2025-08-30 10:55:56', '2025-08-30 10:55:56'),
(347, 157, 'Noime B. Estolonio', 'Spouse', 45, '1980-07-20', '2025-08-30 10:56:25', '2025-08-30 10:56:25'),
(348, 157, 'Angelo B. Estolonio', 'Son', 27, '1998-05-18', '2025-08-30 10:56:25', '2025-08-30 10:56:25'),
(349, 157, 'Ariane Mae B. Estolonio', 'Daughter', 22, '2003-05-18', '2025-08-30 10:56:25', '2025-08-30 10:56:25'),
(350, 158, 'Edloin G. Tancueco', 'Spouse', 45, '1980-07-18', '2025-08-30 10:57:00', '2025-08-30 10:57:00'),
(351, 158, 'Grant G. Tancueco', 'Son', 20, '2005-05-18', '2025-08-30 10:57:00', '2025-08-30 10:57:00'),
(352, 158, 'Lucas G. Tancueco', 'Son', 15, '2010-05-30', '2025-08-30 10:57:00', '2025-08-30 10:57:00'),
(353, 159, 'Eliza G. Gleban', 'Spouse', 45, '1980-04-18', '2025-08-30 10:57:29', '2025-08-30 10:57:29'),
(354, 159, 'Annaliza G. Gleban', 'Daughter', 25, '2000-12-18', '2025-08-30 10:57:29', '2025-08-30 10:57:29'),
(355, 159, 'Gunther G. Gleban', 'Son', 20, '2005-05-18', '2025-08-30 10:57:29', '2025-08-30 10:57:29'),
(356, 160, 'Joel R. Delariarte', 'Spouse', 65, '1960-05-01', '2025-08-30 10:58:33', '2025-08-30 10:58:33'),
(357, 161, 'Eva G. Baloloy', 'Spouse', 35, '1990-05-18', '2025-08-30 10:59:13', '2025-08-30 10:59:13'),
(358, 161, 'John Lloyd G. Baloloy', 'Son', 15, '2010-05-18', '2025-08-30 10:59:13', '2025-08-30 10:59:13'),
(359, 161, 'Alwin G. Baloloy', 'Son', 10, '2015-01-30', '2025-08-30 10:59:13', '2025-08-30 10:59:13'),
(360, 161, 'Shakkira G. Baloloy', 'Daughter', 9, '2016-02-18', '2025-08-30 10:59:13', '2025-08-30 10:59:13'),
(361, 162, 'Benita B. Gotis', 'Spouse', 67, '1958-05-30', '2025-08-30 10:59:53', '2025-08-30 10:59:53'),
(362, 162, 'Haydee B. Gotis', 'Daughter', 40, '1985-10-18', '2025-08-30 10:59:53', '2025-08-30 10:59:53'),
(363, 162, 'Noel B. Gotis', 'Son', 38, '1987-05-18', '2025-08-30 10:59:53', '2025-08-30 10:59:53'),
(364, 162, 'Jovelyn B. Gotis', 'Daughter', 35, '1990-05-04', '2025-08-30 10:59:53', '2025-08-30 10:59:53'),
(365, 163, 'Sharon B. Canale', 'Sibling', 40, '1985-05-18', '2025-08-30 11:00:41', '2025-08-30 11:00:41'),
(366, 163, 'Andrea B. Canale', 'Sibling', 35, '1990-06-08', '2025-08-30 11:00:41', '2025-08-30 11:00:41'),
(367, 163, 'Andro B. Canale', 'Son', 30, '1995-07-01', '2025-08-30 11:00:41', '2025-08-30 11:00:41'),
(368, 164, 'Rowena S. Elesterio', 'Spouse', 45, '1980-09-20', '2025-08-30 11:01:14', '2025-08-30 11:01:14'),
(369, 164, 'Dino E. Masdo', 'Son', 25, '2000-01-25', '2025-08-30 11:01:14', '2025-08-30 11:01:14'),
(370, 165, 'Jenny H. Hila', 'Spouse', 30, '1995-04-30', '2025-08-30 11:01:44', '2025-08-30 11:01:44'),
(371, 165, 'Jenniel H. Gotis', 'Daughter', 10, '2015-06-25', '2025-08-30 11:01:44', '2025-08-30 11:01:44'),
(372, 165, 'Zheilira Aikhaa H. Gotis', 'Daughter', 9, '2016-05-18', '2025-08-30 11:01:44', '2025-08-30 11:01:44'),
(373, 166, 'Eden A. Estolonio', 'Daughter', 40, '1985-07-19', '2025-08-30 11:02:32', '2025-08-30 11:02:32'),
(374, 166, 'Clenton A. Estolonio', 'Son', 39, '1986-11-03', '2025-08-30 11:02:32', '2025-08-30 11:02:32'),
(375, 166, 'Angel Vanness A. Estolonio', 'Daughter', 38, '1987-01-16', '2025-08-30 11:02:32', '2025-08-30 11:02:32'),
(376, 167, 'Nerissa A. Estolonio', 'Spouse', 70, '1955-10-18', '2025-08-30 11:03:07', '2025-08-30 11:03:07'),
(377, 168, 'Rolando C. Golpeo', 'Spouse', 55, '1970-07-10', '2025-08-30 22:53:29', '2025-08-30 22:53:29'),
(378, 168, 'Cherry V. Gotis', 'Daughter', 35, '1990-07-01', '2025-08-30 22:53:29', '2025-08-30 22:53:29'),
(379, 169, 'Louroes E. Agnote', 'Sibling', 48, '1977-06-11', '2025-08-30 22:54:24', '2025-08-30 22:54:24'),
(380, 169, 'Christine E. Agnote', 'Daughter', 30, '1995-08-10', '2025-08-30 22:54:24', '2025-08-30 22:54:24'),
(381, 169, 'Gloria H. Villanueva', 'Daughter', 29, '1996-07-10', '2025-08-30 22:54:24', '2025-08-30 22:54:24'),
(382, 170, 'Milagros A. Bautista', 'Spouse', 60, '1965-06-18', '2025-08-30 22:54:54', '2025-08-30 22:54:54'),
(383, 170, 'Rommel A. Bautista', 'Son', 35, '1990-07-14', '2025-08-30 22:54:54', '2025-08-30 22:54:54'),
(384, 170, 'Rumalyn A. Bautista', 'Daughter', 33, '1992-08-10', '2025-08-30 22:54:54', '2025-08-30 22:54:54'),
(385, 170, 'Rufa May A. Bautista', 'Daughter', 30, '1995-09-11', '2025-08-30 22:54:54', '2025-08-30 22:54:54'),
(386, 171, 'Jinkee Alig', 'Daughter', 35, '1990-07-09', '2025-08-30 22:55:57', '2025-08-30 22:55:57'),
(387, 171, 'Joshua Alig', 'Son', 34, '1991-07-17', '2025-08-30 22:55:57', '2025-08-30 22:55:57'),
(388, 171, 'Kenn Alig', 'Son', 33, '1992-07-11', '2025-08-30 22:55:57', '2025-08-30 22:55:57'),
(389, 171, 'Regie Alig', 'Son', 32, '1993-06-05', '2025-08-30 22:55:57', '2025-08-30 22:55:57'),
(390, 172, 'Sarina G. Delariarte', 'Spouse', 40, '1985-07-10', '2025-08-30 22:56:26', '2025-08-30 22:56:26'),
(391, 173, 'Shalymar Gimoro', 'Spouse', 45, '1980-07-10', '2025-08-30 22:57:07', '2025-08-30 22:57:07'),
(392, 173, 'Mary Ann G. Balida', 'Daughter', 25, '2000-06-10', '2025-08-30 22:57:07', '2025-08-30 22:57:07'),
(393, 173, 'Maricar G. Balida', 'Daughter', 22, '2003-07-10', '2025-08-30 22:57:07', '2025-08-30 22:57:07'),
(394, 174, 'Jhon Renz A. Estolonio', 'Sibling', 35, '1990-02-10', '2025-08-30 22:57:31', '2025-08-30 22:57:31'),
(395, 175, 'Arbie M. Juta', 'Spouse', 40, '1985-11-03', '2025-08-30 22:58:09', '2025-08-30 22:58:09'),
(396, 175, 'Arjean B. Juta', 'Sibling', 20, '2005-07-15', '2025-08-30 22:58:09', '2025-08-30 22:58:09'),
(397, 176, 'Jetecia Gimoro', 'Spouse', 70, '1955-12-10', '2025-08-30 22:58:45', '2025-08-30 22:58:45'),
(398, 176, 'Reymar Gimoro', 'Son', 45, '1980-01-10', '2025-08-30 22:58:45', '2025-08-30 22:58:45'),
(399, 176, 'Joery Gimoro', 'Son', 40, '1985-07-10', '2025-08-30 22:58:45', '2025-08-30 22:58:45'),
(400, 177, 'Jessica A. Gimoro', 'Spouse', 45, '1980-10-10', '2025-08-30 22:59:13', '2025-08-30 22:59:13'),
(401, 177, 'Ara Joy A. Gimoro', 'Daughter', 25, '2000-05-10', '2025-08-30 22:59:13', '2025-08-30 22:59:13'),
(402, 177, 'Arjhun A. Gimoro', 'Son', 24, '2001-08-10', '2025-08-30 22:59:13', '2025-08-30 22:59:13'),
(403, 179, 'Arles G. Gotis', 'Son', 60, '1965-06-10', '2025-08-30 22:59:53', '2025-08-30 22:59:53'),
(404, 179, 'Lysa G. Gotis', 'Daughter', 59, '1966-07-10', '2025-08-30 22:59:53', '2025-08-30 22:59:53'),
(405, 180, 'Rodolfo E. Juta', 'Son', 35, '1990-07-10', '2025-08-30 23:00:40', '2025-08-30 23:00:40'),
(406, 180, 'Mary Rose M. Juta', 'Sibling', 65, '1960-07-20', '2025-08-30 23:00:40', '2025-08-30 23:00:40'),
(407, 180, 'Nico M. Juta', 'Sibling', 64, '1961-07-30', '2025-08-30 23:00:40', '2025-08-30 23:00:40'),
(408, 180, 'Darrel M. Juta', 'Sibling', 62, '1963-07-01', '2025-08-30 23:00:40', '2025-08-30 23:00:40'),
(409, 180, 'Bobby M. Juta', 'Sibling', 61, '1964-07-25', '2025-08-30 23:00:40', '2025-08-30 23:00:40'),
(410, 181, 'Roberto P. Geraldino', 'Spouse', 55, '1970-08-10', '2025-08-30 23:01:23', '2025-08-30 23:01:23'),
(411, 181, 'Roxy D. Portento', 'Daughter', 30, '1995-09-25', '2025-08-30 23:01:23', '2025-08-30 23:01:23'),
(412, 181, 'Zam D. Geraldino', 'Daughter', 25, '2000-08-04', '2025-08-30 23:01:23', '2025-08-30 23:01:23'),
(413, 182, 'Sanoy B. Gotis', 'Spouse', 35, '1990-07-20', '2025-08-30 23:01:53', '2025-08-30 23:01:53'),
(414, 182, 'Cheska J. Gotis', 'Daughter', 8, '2017-05-03', '2025-08-30 23:01:53', '2025-08-30 23:01:53'),
(415, 182, 'Yohan J. Gotis', 'Son', 7, '2018-07-10', '2025-08-30 23:01:53', '2025-08-30 23:01:53'),
(416, 182, 'Jomarie J. Gotis', 'Son', 6, '2019-07-20', '2025-08-30 23:01:53', '2025-08-30 23:01:53'),
(417, 183, 'Nimfa B. Gotis', 'Sibling', 60, '1965-01-11', '2025-08-30 23:02:20', '2025-08-30 23:02:20'),
(418, 183, 'Domingo G. Gotis Jr.', 'Son', 35, '1990-06-15', '2025-08-30 23:02:20', '2025-08-30 23:02:20'),
(419, 184, 'Shiela G. Plazo', 'Sibling', 35, '1990-09-10', '2025-08-30 23:06:36', '2025-08-30 23:06:36'),
(420, 184, 'Princess Yesha G. Plazo', 'Sibling', 35, '1990-08-01', '2025-08-30 23:06:36', '2025-08-30 23:06:36'),
(421, 185, 'Carl Prince Gotis', 'Spouse', 35, '1990-07-10', '2025-08-30 23:07:05', '2025-08-30 23:07:05'),
(422, 186, 'Macmila B. Gimoro', 'Sibling', 35, '1990-07-10', '2025-08-30 23:07:38', '2025-08-30 23:07:38'),
(423, 186, 'Madelyn P. Gimoro', 'Sibling', 35, '1990-07-10', '2025-08-30 23:07:38', '2025-08-30 23:07:38'),
(424, 186, 'Micaella Mae P. Gimoro', 'Sibling', 35, '1990-07-10', '2025-08-30 23:07:38', '2025-08-30 23:07:38'),
(425, 186, 'Mark Jervis P. Gimoro', 'Sibling', 35, '1990-07-10', '2025-08-30 23:07:38', '2025-08-30 23:07:38'),
(426, 187, 'Sonny B. Gotis', 'Spouse', 35, '1990-04-18', '2025-08-30 23:08:25', '2025-08-30 23:08:25'),
(427, 187, 'Aizel Juta', 'Son', 10, '2015-07-03', '2025-08-30 23:08:25', '2025-08-30 23:08:25'),
(428, 187, 'Sedrick J. Gotis', 'Son', 10, '2015-07-03', '2025-08-30 23:08:25', '2025-08-30 23:08:25'),
(429, 187, 'Siyhan J. Gotis', 'Son', 10, '2015-07-03', '2025-08-30 23:08:25', '2025-08-30 23:08:25'),
(430, 188, 'Lilibeth R. Gotis', 'Spouse', 50, '1975-07-01', '2025-08-30 23:08:54', '2025-08-30 23:08:54'),
(431, 188, 'Charlemien R. Gotis', 'Daughter', 25, '2000-06-25', '2025-08-30 23:08:54', '2025-08-30 23:08:54'),
(432, 188, 'Belly Ray Cyrus R. Gotis', 'Son', 24, '2001-07-30', '2025-08-30 23:08:54', '2025-08-30 23:08:54'),
(433, 189, 'Romeo Geraldino', 'Son', 50, '1975-08-25', '2025-08-30 23:09:30', '2025-08-30 23:09:30'),
(434, 190, 'Rachel Ann Gliban', 'Daughter', 25, '2000-07-30', '2025-08-30 23:10:04', '2025-08-30 23:10:04'),
(435, 190, 'Rizzalyn May Gliban', 'Daughter', 24, '2001-05-03', '2025-08-30 23:10:04', '2025-08-30 23:10:04'),
(436, 190, 'Rolandoil Gliban', 'Son', 23, '2002-06-03', '2025-08-30 23:10:04', '2025-08-30 23:10:04'),
(437, 190, 'Roldan Gliban', 'Son', 22, '2003-07-03', '2025-08-30 23:10:04', '2025-08-30 23:10:04'),
(438, 191, 'Cristina G. Gotis', 'Spouse', 40, '1985-12-12', '2025-08-30 23:10:44', '2025-08-30 23:10:44'),
(439, 191, 'Andrew G. Gotis', 'Son', 20, '2005-04-03', '2025-08-30 23:10:44', '2025-08-30 23:10:44'),
(440, 191, 'Eugene G. Gotis', 'Daughter', 15, '2010-05-05', '2025-08-30 23:10:44', '2025-08-30 23:10:44'),
(441, 191, 'Wesley G. Gotis', 'Son', 14, '2016-07-13', '2025-08-30 23:10:44', '2025-08-30 23:10:44'),
(442, 192, 'Maria Cathrine Gimoro', 'Spouse', 45, '1980-04-01', '2025-08-30 23:11:20', '2025-08-30 23:11:20'),
(443, 192, 'Mike Clark G. Tolintino', 'Son', 25, '2000-07-13', '2025-08-30 23:11:20', '2025-08-30 23:11:20'),
(444, 192, 'Caint G. Tolintino', 'Son', 22, '2003-01-23', '2025-08-30 23:11:20', '2025-08-30 23:11:20'),
(445, 193, 'Mylly D. Demetrial', 'Spouse', 60, '1965-09-22', '2025-08-30 23:11:50', '2025-08-30 23:11:50'),
(446, 193, 'Nelly Gotis', 'Daughter', 35, '1990-05-11', '2025-08-30 23:11:50', '2025-08-30 23:11:50'),
(447, 193, 'Justine Culmatico', 'Son', 35, '1991-03-25', '2025-08-30 23:11:50', '2025-08-30 23:11:50'),
(448, 194, 'Harold G. Gimoro', 'Son', 35, '1990-07-03', '2025-08-30 23:12:30', '2025-08-30 23:12:30'),
(449, 194, 'Jesabelle B. Gimoro', 'Daughter', 34, '1991-01-16', '2025-08-30 23:12:30', '2025-08-30 23:12:30'),
(450, 194, 'Mike Francis Gimoro', 'Son', 33, '1992-07-11', '2025-08-30 23:12:30', '2025-08-30 23:12:30'),
(451, 195, 'Marily G. Gotis', 'Spouse', 65, '1955-04-11', '2025-08-30 23:13:03', '2025-08-30 23:13:03'),
(452, 195, 'Marvin G. Golpeo', 'Son', 35, '1990-07-14', '2025-08-30 23:13:03', '2025-08-30 23:13:03'),
(453, 196, 'Mary Grace V. Gimoro', 'Spouse', 35, '1990-07-31', '2025-08-31 00:14:06', '2025-08-31 00:14:06'),
(454, 196, 'Mary Rose G. Villanueva', 'Daughter', 15, '2010-06-28', '2025-08-31 00:14:06', '2025-08-31 00:14:06'),
(455, 197, 'Alex B. Gimoro', 'Spouse', 48, '1977-06-02', '2025-08-31 00:14:47', '2025-08-31 00:14:47'),
(456, 197, 'Emerson Mabanan', 'Son', 22, '2003-05-24', '2025-08-31 00:14:47', '2025-08-31 00:14:47'),
(457, 198, 'Myra T. Bitanguar', 'Spouse', 33, '1992-09-22', '2025-08-31 00:15:15', '2025-08-31 00:15:15'),
(458, 198, 'Michele B. Juta', 'Daughter', 13, '2012-06-13', '2025-08-31 00:15:15', '2025-08-31 00:15:15'),
(459, 199, 'Emelyn G. Juta', 'Spouse', 35, '1990-08-30', '2025-08-31 00:16:03', '2025-08-31 00:16:03'),
(460, 199, 'Messy G. Juta', 'Daughter', 15, '2010-11-13', '2025-08-31 00:16:03', '2025-08-31 00:16:03'),
(461, 199, 'Tesoy G. Juta', 'Son', 14, '2011-01-30', '2025-08-31 00:16:03', '2025-08-31 00:16:03');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_geronimorina76@gmail.com|127.0.0.1', 'i:1;', 1756530584),
('laravel_cache_geronimorina76@gmail.com|127.0.0.1:timer', 'i:1756530584;', 1756530584),
('laravel_cache_jobelgolde12@gmail.com|127.0.0.1', 'i:1;', 1755817143),
('laravel_cache_jobelgolde12@gmail.com|127.0.0.1:timer', 'i:1755817143;', 1755817143);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--

CREATE TABLE `contributions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `collector` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `purok` enum('purok1','purok2','purok3','purok4') DEFAULT NULL,
  `status` enum('paid','not_paid') DEFAULT 'not_paid',
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contributions`
--

INSERT INTO `contributions` (`id`, `member_id`, `collector`, `amount`, `payment_date`, `purok`, `status`, `updated_by`, `created_at`, `updated_at`) VALUES
(11, 113, 'Milanie  G.  Gimoro', 100.00, '2025-08-24 10:36:00', 'purok4', 'paid', 1, '2025-08-23 18:37:50', '2025-08-23 18:37:50'),
(14, 11, 'Emireta Gimoro', 100.00, '2025-08-24 00:00:00', 'purok1', 'paid', 11, '2025-08-23 19:57:47', '2025-08-23 19:57:47'),
(15, 12, 'Lorilyn P. Gimoro', 100.00, '2025-08-24 00:00:00', 'purok1', 'paid', 11, '2025-08-23 20:08:53', '2025-08-23 20:08:53'),
(16, 100, 'Milanie  G.  Gimoro', 100.00, '2025-08-25 00:00:00', 'purok4', 'paid', 11, '2025-08-24 16:21:46', '2025-08-24 16:21:46'),
(17, 10, 'Lorilyn P. Gimoro', 100.00, '2025-08-26 00:00:00', 'purok1', 'paid', 11, '2025-08-25 17:59:55', '2025-08-25 17:59:55'),
(18, 13, 'Milanie  G.  Gimoro', 100.00, '2025-08-30 00:00:00', 'purok1', 'paid', 11, '2025-08-29 20:58:48', '2025-08-29 20:58:48'),
(19, 14, 'Milanie  G.  Gimoro', 100.00, '2025-08-30 00:00:00', 'purok1', 'paid', 11, '2025-08-29 20:59:25', '2025-08-29 20:59:25'),
(20, 55, 'Emireta Gimoro', 100.00, '2025-09-04 19:41:00', 'purok4', 'paid', 1, '2025-09-04 03:41:45', '2025-09-04 03:41:45'),
(21, 59, 'Milanie  G.  Gimoro', 100.00, '2025-09-04 19:42:00', 'purok3', 'paid', 1, '2025-09-04 03:42:12', '2025-09-04 03:42:12');

-- --------------------------------------------------------

--
-- Table structure for table `death_reports`
--

CREATE TABLE `death_reports` (
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `reported_by` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `deceased_name` varchar(100) NOT NULL,
  `date_of_death` date NOT NULL,
  `report_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `registration_date` date NOT NULL,
  `purok` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `occupation` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `gender` enum('Male','Female','','') NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `first_name`, `last_name`, `address`, `contact_number`, `date_of_birth`, `registration_date`, `purok`, `age`, `middle_name`, `status`, `occupation`, `created_at`, `updated_at`, `gender`, `deleted_at`) VALUES
(10, 'Roberto', 'Arevalo', 'Bonga, Bulan, Sorsogon', '09776326447', '1986-12-30', '2025-07-19', 'Purok 1', 39, 'Globasa', 'active', 'Fishermen', NULL, NULL, 'Male', NULL),
(11, 'Alvin', 'Gotis', 'Bonga, Bulan, Sorsogon', '09776326447', '1977-05-10', '2025-07-19', 'Purok 1', 48, 'Relova', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(12, 'Ariel', 'Gotis', 'Bonga, Bulan, Sorsogon', '09776326447', '1975-04-30', '2025-07-19', 'Purok 1', 50, 'Gimoro', 'active', 'Tricycle Driver', NULL, NULL, 'Male', NULL),
(13, 'Jocelyn', 'Gimao', 'Bonga, Bulan, Sorsogon', '', '1952-05-15', '2025-07-19', 'Purok 1', 73, 'Rama', 'active', '', NULL, NULL, 'Female', NULL),
(14, 'Arnol', 'Gotis', 'Bonga, Bulan, Sorsogon', '09776326447', '1976-08-14', '2025-07-19', 'Purok 1', 49, 'Gimoro', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(15, 'Vicitacion', 'Gisalan', 'Bonga, Bulan, Sorsogon', '', '1950-09-21', '2025-07-19', 'Purok 1', 75, 'Gotis', 'active', '', NULL, NULL, 'Male', NULL),
(16, 'Michael', 'Gisalan', 'Bonga, Bulan, Sorsogon', '09776326447', '1972-01-21', '2025-07-19', 'Purok 1', 53, 'Gotis', 'active', 'Housewife', NULL, NULL, 'Male', NULL),
(17, 'Rene', 'Cid', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-11-24', '2025-07-20', 'Purok 1', 45, 'Gotis', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(18, 'Lily', 'Guna', 'Bonga, Bulan, Sorsogon', '09776326447', '1970-07-12', '2025-07-20', 'Purok 1', 55, 'Relova', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(19, 'Marlon', 'Guna', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-01', '2025-07-20', 'Purok 1', 45, 'Relova', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(20, 'Marilyn', 'Miranda', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-08-11', '2025-07-20', 'Purok 1', 45, 'Guna', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(21, 'Ricky', 'Miranda', 'Bonga, Bulan, Sorsogon', '', '1965-12-02', '2025-07-20', 'Purok 1', 60, 'Guna', 'active', '', NULL, NULL, 'Male', NULL),
(22, 'Lorna', 'Revilla', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-08-21', '2025-07-20', 'Purok 1', 45, 'Bermillo', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(23, 'Crispin', 'Guna', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-06-01', '2025-07-20', 'Purok 1', 45, 'Gotis', 'active', 'Fishermen', NULL, NULL, 'Male', NULL),
(24, 'Susan', 'Bermillo', 'Bonga, Bulan, Sorsogon', '09776326447', '1981-08-31', '2025-07-20', 'Purok 1', 44, 'Balansay', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(25, 'Joed Joy', 'Balansay', 'Bonga, Bulan, Sorsogon', '09776326447', '1975-04-21', '2025-07-20', 'Purok 1', 50, 'Rona', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(26, 'Raul', 'Geraldino', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-01-12', '2025-07-20', 'Purok 1', 45, 'Peralta', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(27, 'Edison', 'Gotis', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-07-01', '2025-07-20', 'Purok 1', 45, 'Bautista', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(28, 'Rico', 'Gotis', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-11-25', '2025-07-20', 'Purok 1', 45, 'Bautista', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(29, 'Hermenia', 'Gotis', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-01-19', '2025-07-20', 'Purok 1', 45, 'Bautista', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(30, 'Benigno', 'David', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-08-12', '2025-07-20', 'Purok 1', 45, 'Gobres', 'active', 'Tricycle Driver', NULL, NULL, 'Male', NULL),
(31, 'Mark', 'Zacarias', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-08-11', '2025-07-20', 'Purok 1', 45, 'Buizon', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(32, 'Mark', 'Zacarias', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-08-11', '2025-07-20', 'Purok 1', 45, 'Buizon', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(33, 'Fe', 'Zacarias', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-01-10', '2025-07-20', 'Purok 1', 45, 'Buizon', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(34, 'Jessica', 'Gutlay', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-11', '2025-07-20', 'Purok 1', 45, 'Tolete', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(35, 'Erdy', 'Canale', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-05-01', '2025-07-20', 'Purok 1', 45, 'Barsaga', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(36, 'Mary Ann', 'Canale', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-10-21', '2025-07-20', 'Purok 1', 45, 'Gotis', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(37, 'Elleazar', 'Gernale', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-07-13', '2025-07-20', 'Purok 1', 45, '', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(38, 'Jose', 'Vargas', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-05-30', '2025-07-20', 'Purok 1', 45, 'Molina', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(39, 'Rico', 'Golde', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-07-15', '2025-07-20', 'Purok 1', 45, 'Gernan', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(40, 'Rey', 'Golde', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-01-18', '2025-07-20', 'Purok 1', 45, 'Gernan', 'active', 'Fishermen', NULL, NULL, 'Male', NULL),
(41, 'Renan', 'Golde', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-01', '2025-07-20', 'Purok 1', 45, 'Gernan', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(42, 'Danilo', 'Almodiel', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-01', '2025-07-20', 'Purok 1', 45, 'Barsaga', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(43, 'Dennis', 'Vasque', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-01', '2025-07-20', 'Purok 1', 45, 'Vilamor', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(44, 'Marites', 'Golde', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-01', '2025-07-20', 'Purok 1', 45, 'Gernan', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(45, 'Federico', 'Malisa', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-01', '2025-07-20', 'Purok 1', 45, 'Peñaflor', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(46, 'Jennnelyn', 'Andaya', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-08-11', '2025-07-20', 'Purok 1', 45, 'Importante', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(47, 'Jenny Mae', 'Andaya', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-08-11', '2025-07-20', 'Purok 1', 45, 'Importante', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(48, 'Vidalito', 'Benig', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-01', '2025-07-20', 'Purok 1', 45, 'Banzuela', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(49, 'Nelia', 'Gestiada', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-08-11', '2025-07-20', 'Purok 1', 45, 'Benig', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(50, 'Arnel', 'Bermilllo', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-04', '2025-07-20', 'Purok 1', 45, 'Balansay', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(51, 'Jerome', 'Lagajino', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-07', '2025-07-20', 'Purok 1', 45, '', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(52, 'Amelita', 'Lacandasa', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-09', '2025-07-20', 'Purok 1', 45, 'Estolonio', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(53, 'Levy', 'Bermillo', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-02', '2025-07-20', 'Purok 1', 45, 'Balansay', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(54, 'Estillo', 'Buatis', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-11', '2025-07-20', 'Purok 1', 45, '', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(55, 'Adelaida', 'Banzuela', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-18', '2025-07-20', 'Purok 1', 45, '', 'active', 'Farmer', NULL, '2025-08-08 15:39:19', 'Female', NULL),
(56, 'Erlinda', 'Cid', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-10', '2025-07-20', 'Purok 1', 45, 'Gotis', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(57, 'Leonard', 'Nova', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-15', '2025-07-20', 'Purok 1', 45, 'Escuata', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(58, 'Marvin', 'Gelua', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-17', '2025-07-20', 'Purok 1', 45, 'Bitancor', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(59, 'Aldrin', 'Gimao', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-19', '2025-07-20', 'Purok 1', 45, 'Breboneria', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(60, 'Edison', 'Gotis', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-10', '2025-07-20', 'Purok 1', 45, 'B.', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(61, 'Ronilo', 'Amorado', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-16', '2025-07-20', 'Purok 1', 45, 'C.', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(62, 'Ronilo', 'Amorado', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-16', '2025-07-20', 'Purok 1', 45, 'C.', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(63, 'Josephine', 'Golde', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-14', '2025-07-20', 'Purok 1', 45, 'G.', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(64, 'Zacarias', 'Eupeña', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-30', '2025-07-20', 'Purok 1', 45, 'C.', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(65, 'Gregorio', 'Gisalan', 'Bonga, Bulan, Sorsogon', '09776326447', '1980-09-12', '2025-07-20', 'Purok 1', 45, 'G.', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(66, 'Lorelyn', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09125524485', '1988-04-16', '2025-07-31', 'Purok 1', 37, 'P.', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(67, 'Aireen', 'Juta', 'Bonga, Bulan, Sorsogon', '09125524485', '1990-06-12', '2025-07-31', 'Purok 1', 35, 'G.', 'active', 'Housewife', NULL, NULL, 'Female', NULL),
(68, 'Rommel', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1973-05-23', '2025-07-31', 'Purok 1', 52, 'G.', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(69, 'Elena', 'Geraldino', 'Bonga, Bulan, Sorsogon', '09125524485', '1947-06-11', '2025-07-31', 'Purok 1', 78, 'P.', 'active', '', NULL, NULL, 'Female', NULL),
(70, 'Robert', 'Gliban', 'Bonga, Bulan, Sorsogon', '09125524485', '1971-04-01', '2025-07-31', 'Purok 1', 54, 'G.', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(71, 'Arthur', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1982-09-01', '2025-07-31', 'Purok 1', 43, 'G.', 'active', 'Fishermen', NULL, NULL, 'Male', NULL),
(72, 'Nemrod', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1963-06-26', '2025-07-31', 'Purok 1', 62, 'H.', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(73, 'Apollonio', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09125524485', '1954-05-18', '2025-07-31', 'Purok 1', 71, 'M.', 'active', '', NULL, NULL, 'Male', NULL),
(74, 'Mario', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1957-10-08', '2025-07-31', 'Purok 1', 68, 'C.', 'active', '', NULL, NULL, 'Male', NULL),
(75, 'Lito', 'Villanueva', 'Bonga, Bulan, Sorsogon', '09125524485', '1990-08-01', '2025-07-31', 'Purok 1', 35, 'A.', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(76, 'Emerita', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09125524485', '1977-02-20', '2025-07-31', 'Purok 1', 48, 'M.', 'active', 'Sari-sari Store', NULL, NULL, 'Female', NULL),
(77, 'Michael', 'Juta', 'Bonga, Bulan, Sorsogon', '09125524485', '1992-10-28', '2025-07-31', 'Purok 2', 33, 'M.', 'active', 'Farmer', NULL, NULL, 'Female', NULL),
(78, 'Willy', 'Juta', 'Bonga, Bulan, Sorsogon', '09125524485', '1990-10-28', '2025-07-31', 'Purok 2', 35, 'M.', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(79, 'Mailyn', 'Geraldino', 'Bonga, Bulan, Sorsogon', '09916682157', '1989-01-03', '2025-07-31', 'Purok 4', 36, 'L.', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(80, 'Shiela', 'Masdo', 'Bonga, Bulan, Sorsogon', '09916682157', '1989-12-12', '2025-07-31', 'Purok 4', 39, 'G.', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(81, 'Esteban', 'Rosete', 'Bonga, Bulan, Sorsogon', '09916683157', '1943-12-10', '2025-07-31', 'Purok 4', 82, 'R.', 'active', 'Rice mill', NULL, NULL, 'Male', NULL),
(82, 'Juan', 'Balansay', 'Bonga, Bulan, Sorsogon', '09916683157', '1982-01-20', '2025-07-31', 'Purok 4', 43, 'Globasa', 'active', 'Tricycle driver', NULL, NULL, 'Male', NULL),
(83, 'Estila', 'Breboniria', 'Bonga, Bulan, Sorsogon', '09916682157', '1962-10-05', '2025-07-31', 'Purok 4', 63, '', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(84, 'Madel', 'Cid', 'Bonga, Bulan, Sorsogon', '09916682157', '1978-07-02', '2025-07-31', 'Purok 4', 47, 'Gotis', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(85, 'Jhonny', 'Bautista', 'Bonga, Bulan, Sorsogon', '09916682157', '1955-01-03', '2025-07-31', 'Purok 4', 70, 'Gotis', 'active', 'Welder', NULL, NULL, 'Male', NULL),
(86, 'Mark John', 'Malinao', 'Bonga, Bulan, Sorsogon', '09916682157', '1972-07-21', '2025-07-31', 'Purok 4', 53, 'Golpeo', 'active', 'Welder', NULL, NULL, 'Male', NULL),
(87, 'Vergie', 'Malinao', 'Bonga, Bulan, Sorsogon', '09916682157', '1967-12-10', '2025-07-31', 'Purok 4', 58, 'G.', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(88, 'Gigi', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09916682157', '1979-06-19', '2025-07-31', 'Purok 4', 46, 'Vargas', 'active', 'Construction worker', NULL, NULL, 'Male', NULL),
(89, 'Mario', 'Balansay', 'Bonga, Bulan, Sorsogon', '09916682157', '1954-09-19', '2025-07-31', 'Purok 4', 71, 'Globsa', 'active', 'Tricycle driver', NULL, NULL, 'Male', NULL),
(90, 'Niño', 'Magano', 'Bonga, Bulan, Sorsogon', '09916682157', '1987-07-30', '2025-07-31', 'Purok 4', 38, 'Dellomas', 'active', 'Tricycle driver', NULL, NULL, 'Male', NULL),
(91, 'Roosvelt', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09916682157', '1969-11-08', '2025-07-31', 'Purok 4', 55, 'Cabulit', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(92, 'Elmer', 'Panaligan', 'Bonga, Bulan, Sorsogon', '099166157', '1961-10-19', '2025-07-31', 'Purok 4', 64, 'Consigo', 'active', 'Welder', NULL, NULL, 'Male', NULL),
(93, 'Joefre', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09916682157', '1967-08-07', '2025-07-31', 'Purok 4', 58, 'Gaton', 'active', '', NULL, NULL, 'Male', NULL),
(94, 'Jomar', 'Delariarte', 'Bonga, Bulan, Sorsogon', '09916682157', '1985-04-21', '2025-07-31', 'Purok 4', 40, 'Breboneria', 'active', 'Tricycle driver', NULL, NULL, 'Male', NULL),
(95, 'Melanchie', 'Ringor', 'Bonga, Bulan, Sorsogon', '09916682157', '1958-07-18', '2025-07-31', 'Purok 4', 67, 'Dalinoc', 'active', 'Welder', NULL, NULL, 'Female', NULL),
(96, 'Catherine', 'Ringor', 'Bonga, Bulan, Sorsogon', '09916682157', '1982-05-26', '2025-07-31', 'Purok 4', 43, 'Gimoro', 'active', 'Houses wife', NULL, NULL, 'Female', NULL),
(97, 'Romaldo', 'Golpeo', 'Bonga, Bulan, Sorsogon', '09916682157', '1978-12-14', '2025-07-31', 'Purok 4', 47, 'Cabanza', 'active', 'Farmer', NULL, NULL, 'Male', NULL),
(98, 'Rina', 'Malinao', 'Bonga, Bulan, Sorsogon', '09916682157', '1972-05-14', '2025-07-31', 'Purok 4', 53, 'Gotis', 'active', 'House keeper', NULL, NULL, 'Female', NULL),
(99, 'Cynthia', 'Juta', 'Bonga, Bulan, Sorsogon', '09916682157', '1958-08-25', '2025-07-31', 'Purok 4', 67, 'Golpeo', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(100, 'Melanie', 'Gaurda', 'Bonga, Bulan, Sorsogon', '099182157', '1986-10-03', '2025-08-01', 'Purok 4', 39, 'Geraldino', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(101, 'Sarafe', 'Agnote', 'Bonga, Bulan, Sorsogon', '09916682157', '1992-05-07', '2025-08-01', 'Purok 4', 33, 'Dellomas', 'active', 'Welder', NULL, NULL, 'Female', NULL),
(102, 'Jesus', 'Breboneria', 'Bonga, Bulan, Sorsogon', '09916682157', '1973-11-15', '2025-08-01', 'Purok 4', 52, 'Balatukan', 'active', 'Pangingisda', NULL, NULL, 'Male', NULL),
(103, 'Domingo', 'Dellomas', 'Bonga, Bulan, Sorsogon', '09916682157', '1956-12-17', '2025-08-01', 'Purok 4', 69, 'Geocada', 'active', 'Tricycle driver', NULL, NULL, 'Male', NULL),
(104, 'Christopher', 'Espaldon', 'Bonga, Bulan, Sorsogon', '09916682157', '1984-12-14', '2025-08-01', 'Purok 4', 41, 'Malinao', 'active', 'Welder', NULL, NULL, 'Male', NULL),
(105, 'Salvacion', 'Geraldino', 'Bonga, Bulan, Sorsogon', '09916682157', '1958-05-08', '2025-08-01', 'Purok 4', 67, 'L.', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(106, 'Romoe', 'Malinao', 'Bonga, Bulan, Sorsogon', '09916682157', '1967-06-03', '2025-08-01', 'Purok 4', 58, 'Bonan', 'active', 'Pangigisda', NULL, NULL, 'Male', NULL),
(107, 'Jay', 'Villanueva', 'Bonga, Bulan, Sorsogon', '09916682157', '1986-12-04', '2025-08-01', 'Purok 4', 39, 'Breboneria', 'active', 'Welder', NULL, NULL, 'Male', NULL),
(108, 'Christer', 'Espaldon', 'Bonga, Bulan, Sorsogon', '09916682157', '1967-12-11', '2025-08-01', 'Purok 4', 58, 'Malinao', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(109, 'Manjelyn', 'Galligues', 'Bonga, Bulan, Sorsogon', '09916682157', '1980-11-11', '2025-08-01', 'Purok 4', 45, 'Aranjuez', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(110, 'Jonathan', 'Ovilla', 'Bonga, Bulan, Sorsogon', '09916682157', '1988-06-25', '2025-08-01', 'Purok 4', 37, 'Grieta', 'active', 'Rice mill', NULL, NULL, 'Male', NULL),
(111, 'Enrique', 'Lazona', 'Bonga, Bulan, Sorsogon', '09916682157', '1972-10-12', '2025-08-01', 'Purok 4', 53, 'Sola', 'active', 'None', NULL, NULL, 'Male', NULL),
(112, 'Raul', 'David', 'Bonga, Bulan, Sorsogon', '09916682157', '1965-05-08', '2025-08-01', 'Purok 4', 60, 'Sampiroy', 'active', 'Tupad', NULL, NULL, 'Male', NULL),
(113, 'Marjane', 'Golde', 'Bonga, Bulan, Sorsogon', '09916682157', '1984-10-30', '2025-08-01', 'Purok 4', 41, 'Valdez', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(114, 'Margie', 'Bermillo', 'Bonga, Bulan, Sorsogon', '09916682157', '1986-05-04', '2025-08-01', 'Purok 4', 39, 'Balansay', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(115, 'Judita', 'Villanueva', 'Bonga, Bulan, Sorsogon', '09916682157', '1959-05-08', '2025-08-01', 'Purok 4', 66, 'Breboneria', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(116, 'Albert', 'Laurente', 'Bonga, Bulan, Sorsogon', '09916682157', '1990-10-30', '2025-08-01', 'Purok 4', 35, 'Gimpez', 'active', 'Tupad', NULL, NULL, 'Male', NULL),
(117, 'May Ann', 'Delos Santos', 'Bonga, Bulan, Sorsogon', '09916682157', '1990-06-01', '2025-08-01', 'Purok 4', 35, 'Villanueva', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(118, 'Warren', 'Villanueva', 'Bonga, Bulan, Sorsogon', '09916682157', '1992-05-28', '2025-08-01', 'Purok 4', 33, 'Breboneria', 'active', 'Tricycle driver', NULL, NULL, 'Male', NULL),
(119, 'Marvin', 'Villanueva', 'Bonga, Bulan, Sorsogon', '09916682157', '1956-12-16', '2025-08-01', 'Purok 4', 69, 'Breboneria', 'active', 'Tricycle driver', NULL, NULL, 'Male', NULL),
(120, 'Rodel', 'Villanueva', 'Bonga, Bulan, Sorsogon', '09916682157', '1956-05-02', '2025-08-01', 'Purok 4', 69, 'Breboneria', 'active', 'Tricycle driver', NULL, NULL, 'Male', NULL),
(121, 'Shyril', 'Villanueva', 'Bonga, Bulan, Sorsogon', '09916682157', '1985-01-05', '2025-08-01', 'Purok 4', 40, 'Enesio', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(122, 'Peter John', 'Gliban', 'Bonga, Bulan, Sorsogon', '09916682157', '2000-02-21', '2025-08-01', 'Purok 4', 25, 'Forcadilla', 'active', 'Welder', NULL, NULL, 'Male', NULL),
(123, 'Antonio', 'Gisalan', 'Bonga, Bulan, Sorsogon', '09916682157', '1998-04-22', '2025-08-01', 'Purok 4', 27, 'Buenviaje', 'active', 'None', NULL, NULL, 'Male', NULL),
(124, 'Martin', 'Geraldino', 'Bonga, Bulan, Sorsogon', '09916682157', '2003-07-13', '2025-08-01', 'Purok 4', 22, 'Lebantino', 'active', 'Tupad', NULL, NULL, 'Male', NULL),
(125, 'Crisfa', 'Flores', 'Bonga, Bulan, Sorsogon', '09916682157', '1998-01-13', '2025-08-01', 'Purok 4', 27, 'Gimoro', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(126, 'Glenda', 'Fuller', 'Bonga, Bulan, Sorsogon', '09926682157', '1969-10-06', '2025-08-01', 'Purok 4', 56, '', 'active', 'House keeper', NULL, NULL, 'Female', NULL),
(127, 'Puti', 'Fuller', 'Bonga, Bulan, Sorsogon', '09916682157', '1993-09-11', '2025-08-01', 'Purok 4', 32, '', 'active', 'Pangigisda', NULL, NULL, 'Male', NULL),
(128, 'Edelyn', 'Culbengan', 'Bonga, Bulan, Sorsogon', '09916682157', '1962-09-13', '2025-08-01', 'Purok 4', 63, 'Canale', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(129, 'Emilda', 'Canale', 'Bonga, Bulan, Sorsogon', '09916682157', '1971-11-11', '2025-08-01', 'Purok 4', 54, 'Barsaga', 'active', 'House keeper', NULL, NULL, 'Female', NULL),
(130, 'Rosita', 'Pernito', 'Bonga, Bulan, Sorsogon', '09916682157', '1990-12-03', '2025-08-01', 'Purok 4', 35, '', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(131, 'Maricel', 'Bayoca', 'Bonga, Bulan, Sorsogon', '09916682157', '1972-04-05', '2025-08-01', 'Purok 4', 53, 'Bejerano', 'active', 'House wife', NULL, NULL, 'Female', NULL),
(132, 'Alberto', 'Gimao', 'Bonga, Bulan, Sorsogon', '09916682157', '1975-02-28', '2025-08-01', 'Purok 4', 50, 'G.', 'active', 'Tupad', NULL, NULL, 'Male', NULL),
(133, 'Helen', 'Malgapo', 'Bonga, Bulan, Sorsogon', '09916682157', '1980-03-23', '2025-08-01', 'Purok 4', 45, 'M.', 'active', 'Tupad', NULL, NULL, 'Female', NULL),
(134, 'Esteban Jr', 'Roseta', 'Bonga, Bulan, Sorsogon', '09916682157', '1978-12-05', '2025-08-01', 'Purok 4', 47, 'C.', 'active', 'Construction worker', NULL, NULL, 'Male', NULL),
(135, 'Ereneo', 'Gotis', 'Bonga, Bulan, Sorsogon', '09916682157', '1978-05-14', '2025-08-01', 'Purok 4', 48, 'Galido', 'active', 'Pangigisda', NULL, NULL, 'Male', NULL),
(136, 'Jomar', 'Gotis', 'Bonga, Bulan, Sorsogon', '09916682157', '1996-06-14', '2025-08-01', 'Purok 4', 29, 'Golpeo', 'active', 'Mini bus driver', NULL, NULL, 'Male', NULL),
(137, 'Melvin', 'Gotis', 'Bonga, Bulan, Sorsogon', '09150046884', '1994-10-22', '2025-08-30', 'Purok 2', 31, '', 'active', 'Tricycle Driver', '2025-08-30 10:38:21', '2025-08-30 10:38:21', 'Male', NULL),
(138, 'Beverly', 'Gotis', 'Bonga, Bulan, Sorsogon', '09150046884', '1974-08-05', '2025-08-30', 'Purok 2', 50, 'V.', 'active', 'Housewife', '2025-08-30 10:42:39', '2025-08-30 10:42:39', 'Female', NULL),
(139, 'Ronnel', 'Birata', 'Bonga, Bulan, Sorsogon', '09150046884', '1987-03-03', '2025-08-30', 'Purok 2', 38, 'D.', 'active', 'Welder', '2025-08-30 10:43:31', '2025-08-30 10:43:31', 'Male', NULL),
(140, 'Alegriano', 'Masdo', 'Bonga, Bulan, Sorsogon', '09150046884', '1986-12-30', '2025-08-30', 'Purok 2', 39, '', 'active', 'Fishermen', '2025-08-30 10:44:10', '2025-08-30 10:44:10', 'Male', NULL),
(141, 'Jeoffrey Cesar', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09150046884', '2000-04-30', '2025-08-30', 'Purok 2', 25, 'A.', 'active', 'Tricycle Driver', '2025-08-30 10:45:28', '2025-08-30 10:45:28', 'Male', NULL),
(142, 'Jesus', 'Delariarte', 'Bonga, Bulan, Sorsogon', '09150046884', '1958-12-25', '2025-08-30', 'Purok 2', 67, '', 'active', 'Fishermen', '2025-08-30 10:46:04', '2025-08-30 10:46:04', 'Male', NULL),
(143, 'Melda', 'Delariarte', 'Bonga, Bulan, Sorsogon', '09150046884', '1964-09-04', '2025-08-30', 'Purok 2', 61, '', 'active', 'Sari-sari Store', '2025-08-30 10:46:47', '2025-08-30 10:46:47', 'Female', NULL),
(144, 'Ernie', 'Estolonio', 'Bonga, Bulan, Sorsogon', '09150046884', '1979-06-22', '2025-08-30', 'Purok 2', 46, 'A.', 'active', 'Fishermen', '2025-08-30 10:47:20', '2025-08-30 10:47:20', 'Male', NULL),
(145, 'Follentino', 'Masdo', 'Bonga, Bulan, Sorsogon', '09150046884', '1984-07-14', '2025-08-30', 'Purok 2', 41, 'G.', 'active', 'Tricycle Driver', '2025-08-30 10:47:51', '2025-08-30 10:47:51', 'Male', NULL),
(146, 'Regan', 'Jamolin', 'Bonga, Bulan, Sorsogon', '09150046884', '1989-09-26', '2025-08-30', 'Purok 2', 36, 'G.', 'active', 'Fishermen', '2025-08-30 10:48:38', '2025-08-30 10:48:38', 'Male', NULL),
(147, 'Adam', 'Gotis', 'Bonga, Bulan, Sorsogon', '09150046884', '1971-03-03', '2025-08-30', 'Purok 2', 54, 'A.', 'active', 'Farmer', '2025-08-30 10:49:19', '2025-08-30 10:49:19', 'Male', NULL),
(148, 'Nestor', 'Gliban', 'Bonga, Bulan, Sorsogon', '09150046884', '1959-07-07', '2025-08-30', 'Purok 2', 66, 'M.', 'active', 'Farmer', '2025-08-30 10:51:04', '2025-08-30 10:51:04', 'Male', NULL),
(149, 'Erwin', 'Delariarte', 'Bonga, Bulan, Sorsogon', '09150046884', '1977-07-06', '2025-08-30', 'Purok 2', 48, 'G.', 'active', 'Farmer', '2025-08-30 10:51:53', '2025-08-30 10:51:53', 'Male', NULL),
(150, 'Rodel', 'Tan', 'Bonga, Bulan, Sorsogon', '09150046884', '1987-10-21', '2025-08-30', 'Purok 2', 38, '', 'active', 'Farmer', '2025-08-30 10:52:33', '2025-08-30 10:52:33', 'Male', NULL),
(151, 'Omega', 'Gotis', 'Bonga, Bulan, Sorsogon', '09150046884', '1978-07-30', '2025-08-30', 'Purok 2', 47, 'G.', 'active', 'Sari-sari Store', '2025-08-30 10:53:07', '2025-08-30 10:53:07', 'Female', NULL),
(152, 'Joven', 'Gliban', 'Bonga, Bulan, Sorsogon', '09150046884', '1967-01-24', '2025-08-30', 'Purok 2', 58, '', 'active', 'Farmer', '2025-08-30 10:53:35', '2025-08-30 10:53:35', 'Male', NULL),
(153, 'Dominga', 'Gliban', 'Bonga, Bulan, Sorsogon', '09150046884', '1937-12-08', '2025-08-30', 'Purok 2', 88, 'G.', 'active', 'Housewife', '2025-08-30 10:54:21', '2025-08-30 10:54:21', 'Female', NULL),
(154, 'Weryen', 'Hamor', 'Bonga, Bulan, Sorsogon', '09150046884', '1973-11-09', '2025-08-30', 'Purok 2', 52, 'G.', 'active', 'Housewife', '2025-08-30 10:54:48', '2025-08-30 10:54:48', 'Female', NULL),
(155, 'Jimso', 'Gliban', 'Bonga, Bulan, Sorsogon', '09150046884', '1976-08-09', '2025-08-30', 'Purok 2', 49, 'G.', 'active', 'Farmer', '2025-08-30 10:55:26', '2025-08-30 10:55:26', 'Male', NULL),
(156, 'Edgar', 'Jamolin', 'Bonga, Bulan, Sorsogon', '09150046884', '1962-03-21', '2025-08-30', 'Purok 2', 63, 'A.', 'active', 'Carpinter', '2025-08-30 10:55:56', '2025-08-30 10:55:56', 'Male', NULL),
(157, 'Edgar', 'Estolonio', 'Bonga, Bulan, Sorsogon', '09150046884', '1977-02-21', '2025-08-30', 'Purok 2', 48, 'A.', 'active', 'Farmer', '2025-08-30 10:56:25', '2025-08-30 10:56:25', 'Male', NULL),
(158, 'Shyra', 'Tancueco', 'Bonga, Bulan, Sorsogon', '09150046884', '1984-10-15', '2025-08-30', 'Purok 2', 41, 'G.', 'active', 'Sari-sari Store', '2025-08-30 10:57:00', '2025-08-30 10:57:00', 'Female', NULL),
(159, 'Gilbert', 'Gleban', 'Bonga, Bulan, Sorsogon', '09150046884', '1978-05-06', '2025-08-30', 'Purok 2', 47, 'Z.', 'active', 'Fishermen', '2025-08-30 10:57:28', '2025-08-30 10:57:28', 'Male', NULL),
(160, 'Melda', 'Delariarte', 'Bonga, Bulan, Sorsogon', '09150046884', '1964-09-04', '2025-08-30', 'Purok 2', 61, '', 'active', 'Housewife', '2025-08-30 10:58:33', '2025-08-30 10:58:33', 'Female', NULL),
(161, 'Alonso', 'Baloloy', 'Bonga, Bulan, Sorsogon', '09150046884', '1986-01-31', '2025-08-30', 'Purok 2', 39, 'G.', 'active', 'Farmer', '2025-08-30 10:59:13', '2025-08-30 10:59:13', 'Male', NULL),
(162, 'Gonzalo', 'Gotis', 'Bonga, Bulan, Sorsogon', '09150046884', '1955-03-21', '2025-08-30', 'Purok 2', 70, 'V.', 'active', '', '2025-08-30 10:59:53', '2025-08-30 10:59:53', 'Male', NULL),
(163, 'Andy', 'Canale', 'Bonga, Bulan, Sorsogon', '09150046884', '1983-01-05', '2025-08-30', 'Purok 2', 42, 'B.', 'active', 'Housewife', '2025-08-30 11:00:41', '2025-08-30 11:00:41', 'Female', NULL),
(164, 'Domingo', 'Masdo', 'Bonga, Bulan, Sorsogon', '0915046884', '1979-02-14', '2025-08-30', 'Purok 2', 46, 'G.', 'active', 'Farmer', '2025-08-30 11:01:14', '2025-08-30 11:01:14', 'Male', NULL),
(165, 'Nathaniel', 'Gotis', 'Bonga, Bulan, Sorsogon', '09150046884', '1997-06-19', '2025-08-30', 'Purok 2', 28, 'P.', 'active', 'Farmer', '2025-08-30 11:01:44', '2025-08-30 11:01:44', 'Male', NULL),
(166, 'Letty', 'Aguado', 'Bonga, Bulan, Sorsogon', '09150046884', '1954-11-19', '2025-08-30', 'Purok 2', 71, 'D.', 'active', '', '2025-08-30 11:02:32', '2025-08-30 11:02:32', 'Female', NULL),
(167, 'Eddic', 'Estolonio', 'Bonga, Bulan, Sorsogon', '09150046884', '1953-12-19', '2025-08-30', 'Purok 2', 72, 'H.', 'active', '', '2025-08-30 11:03:07', '2025-08-30 11:03:07', 'Male', NULL),
(168, 'Emelita', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1971-07-21', '2025-08-31', 'Purok 3', 54, 'V.', 'active', '', '2025-08-30 22:53:29', '2025-08-30 22:53:29', 'Female', NULL),
(169, 'Jason', 'Villanueva', 'Bonga, Bulan, Sorsogon', '09125524485', '1975-02-25', '2025-08-31', 'Purok 3', 50, 'H.', 'active', 'Farmer', '2025-08-30 22:54:24', '2025-08-30 22:54:24', 'Male', NULL),
(170, 'Romeo', 'Bautista', 'Bonga, Bulan, Sorsogon', '09125524485', '1962-06-15', '2025-08-31', 'Purok 3', 63, 'G.', 'active', '', '2025-08-30 22:54:54', '2025-08-30 22:54:54', 'Male', NULL),
(171, 'Nelly', 'Alig', 'Bonga, Bulan, Sorsogon', '09125524485', '1957-01-14', '2025-08-31', 'Purok 3', 68, 'S.', 'active', '', '2025-08-30 22:55:57', '2025-08-30 22:55:57', 'Female', NULL),
(172, 'Ernan', 'Delariarte', 'Bonga, Bulan, Sorsogon', '09125524485', '1980-09-30', '2025-08-31', 'Purok 3', 45, 'G.', 'active', 'Farmer', '2025-08-30 22:56:26', '2025-08-30 22:56:26', 'Male', NULL),
(173, 'Alvin', 'Balida', 'Bonga, Bulan, Sorsogon', '09125524485', '1977-01-08', '2025-08-31', 'Purok 3', 48, 'S.', 'active', 'Farmer', '2025-08-30 22:57:07', '2025-08-30 22:57:07', 'Male', NULL),
(174, 'Grenggo', 'Estolonio', 'Bonga, Bulan, Sorsogon', '09125524485', '1993-01-06', '2025-08-31', 'Purok 3', 31, 'A.', 'active', 'Farmer', '2025-08-30 22:57:31', '2025-08-30 22:57:31', 'Male', NULL),
(175, 'Jennifer', 'Juta', 'Bonga, Bulan, Sorsogon', '09125524485', '1987-11-22', '2025-08-31', 'Purok 3', 38, 'B.', 'active', 'Housewife', '2025-08-30 22:58:09', '2025-08-30 22:58:09', 'Female', NULL),
(176, 'Pedro', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09125524485', '1953-11-07', '2025-08-31', 'Purok 3', 72, '', 'active', 'Fishermen', '2025-08-30 22:58:45', '2025-08-30 22:58:45', 'Male', NULL),
(177, 'Argie', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09125524485', '1979-11-06', '2025-08-31', 'Purok 3', 46, '', 'active', 'Farmer', '2025-08-30 22:59:13', '2025-08-30 22:59:13', 'Male', NULL),
(178, 'Levy', 'Goyal', 'Bonga, Bulan, Sorsogon', '09125524485', '1962-05-12', '2025-08-31', 'Purok 3', 63, 'M.', 'active', '', '2025-08-30 22:59:29', '2025-08-30 22:59:29', 'Female', NULL),
(179, 'Anusalion', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1945-10-17', '2025-08-31', 'Purok 3', 80, 'G.', 'active', '', '2025-08-30 22:59:53', '2025-08-30 22:59:53', 'Female', NULL),
(180, 'Rosa', 'Juta', 'Bonga, Bulan, Sorsogon', '09125524485', '1962-08-08', '2025-08-31', 'Purok 3', 63, 'M.', 'active', '', '2025-08-30 23:00:40', '2025-08-30 23:00:40', 'Female', NULL),
(181, 'Rizalina', 'Decena', 'Bonga, Bulan, Sorsogon', '09125524485', '1972-06-19', '2025-08-31', 'Purok 3', 53, 'U.', 'active', 'House wife', '2025-08-30 23:01:23', '2025-08-30 23:01:23', 'Female', NULL),
(182, 'Marjorie', 'Juta', 'Bonga, Bulan, Sorsogon', '09125524485', '1997-11-07', '2025-08-31', 'Purok 3', 28, 'C.', 'active', 'Housewife', '2025-08-30 23:01:53', '2025-08-30 23:01:53', 'Female', NULL),
(183, 'Domingo', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1963-12-09', '2025-08-31', 'Purok 3', 62, 'G.', 'active', 'Farmer', '2025-08-30 23:02:20', '2025-08-30 23:02:20', 'Male', NULL),
(184, 'Ramon', 'Plazo', 'Bonga, Bulan, Sorsogon', '09125524485', '1988-04-16', '2025-08-31', 'Purok 3', 37, 'Z.', 'active', 'Farmer', '2025-08-30 23:06:36', '2025-08-30 23:06:36', 'Male', NULL),
(185, 'Celeste', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1993-04-27', '2025-08-31', 'Purok 3', 31, 'B.', 'active', 'Housewife', '2025-08-30 23:07:05', '2025-08-30 23:07:05', 'Female', NULL),
(186, 'Lorelyn', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09125524485', '1988-04-16', '2025-08-31', 'Purok 3', 37, 'P.', 'active', 'Housewife', '2025-08-30 23:07:38', '2025-08-30 23:07:38', 'Female', NULL),
(187, 'Aireen', 'Juta', 'Bonga, Bulan, Sorsogon', '09125524485', '1990-06-12', '2025-08-31', 'Purok 3', 35, 'G.', 'active', 'Housewife', '2025-08-30 23:08:25', '2025-08-30 23:08:25', 'Female', NULL),
(188, 'Rommel', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1973-05-23', '2025-08-31', 'Purok 3', 52, 'G.', 'active', 'Farmer', '2025-08-30 23:08:54', '2025-08-30 23:08:54', 'Male', NULL),
(189, 'Elena', 'Geraldino', 'Bonga, Bulan, Sorsogon', '09125524485', '1947-06-11', '2025-08-31', 'Purok 3', 78, 'P.', 'active', '', '2025-08-30 23:09:30', '2025-08-30 23:09:30', 'Female', NULL),
(190, 'Robert', 'Gliban', 'Bonga, Bulan, Sorsogon', '09125524485', '1971-04-01', '2025-08-31', 'Purok 3', 54, 'G.', 'active', 'Farmer', '2025-08-30 23:10:04', '2025-08-30 23:10:04', 'Male', NULL),
(191, 'Arthur', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1982-09-01', '2025-08-31', 'Purok 3', 43, 'G.', 'active', 'Fishermen', '2025-08-30 23:10:44', '2025-08-30 23:10:44', 'Male', NULL),
(192, 'Bobby', 'Tolintino', 'Bonga, Bulan, Sorsogon', '09125524485', '1978-03-17', '2025-08-31', 'Purok 3', 47, 'C.', 'active', 'Farmer', '2025-08-30 23:11:20', '2025-08-30 23:11:20', 'Male', NULL),
(193, 'Nemrod', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1963-06-26', '2025-08-31', 'Purok 3', 62, 'H.', 'active', 'Farmer', '2025-08-30 23:11:50', '2025-08-30 23:11:50', 'Male', NULL),
(194, 'Apollonio', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09125524485', '1954-05-18', '2025-08-31', 'Purok 3', 71, 'M.', 'active', '', '2025-08-30 23:12:30', '2025-08-30 23:12:30', 'Male', NULL),
(195, 'Mario', 'Gotis', 'Bonga, Bulan, Sorsogon', '09125524485', '1957-10-08', '2025-08-31', 'Purok 3', 68, 'C.', 'active', '', '2025-08-30 23:13:03', '2025-08-30 23:13:03', 'Male', NULL),
(196, 'Lito', 'Villanueva', 'Bonga, Bulan, Sorsogon', '09125524485', '1990-08-01', '2025-08-31', 'Purok 3', 35, 'A.', 'active', 'Farmer', '2025-08-31 00:14:06', '2025-08-31 00:14:06', 'Male', NULL),
(197, 'Emerita', 'Gimoro', 'Bonga, Bulan, Sorsogon', '09125524485', '1977-02-20', '2025-08-31', 'Purok 3', 48, 'M.', 'active', 'Sari-sari Store', '2025-08-31 00:14:47', '2025-08-31 00:14:47', 'Female', NULL),
(198, 'Michael', 'Juta', 'Bonga, Bulan, Sorsogon', '09125524485', '1992-10-28', '2025-08-31', 'Purok 3', 33, 'M.', 'active', 'Farmer', '2025-08-31 00:15:15', '2025-08-31 00:15:15', 'Female', NULL),
(199, 'Willy', 'Juta', 'Bonga, Bulan, Sorsogon', '09125524485', '1990-10-28', '2025-08-31', 'Purok 3', 35, 'M.', 'active', 'Farmer', '2025-08-31 00:16:03', '2025-08-31 00:16:03', 'Male', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_05_31_172902_create_members_table', 2),
(5, '2025_05_31_190536_create_members_table', 3),
(6, '2025_05_31_195352_create_beneficiary_table', 4),
(7, '2025_05_31_211307_create_beneficiary_table', 5),
(8, '2025_06_04_194905_create_contributions_table', 6),
(9, '2025_06_06_205059_create_officials_table', 7),
(10, '2025_06_07_002407_add_deleted_at_in_officials_table', 8),
(11, '2025_06_14_010642_add_softdelete_in_users_table', 9),
(12, '2025_06_14_070910_add_foreign_key_in_officialss_table', 10),
(13, '2025_06_14_072534_add_column_in_users_table', 11),
(14, '2025_06_14_080150_add_email_in_officials_table', 12),
(15, '2025_06_21_232407_create_death_reports_table', 13),
(16, '2025_06_21_232720_create_assistance_distribution_table', 13),
(17, '2025_06_26_000225_add_purok_in_users_table', 14),
(18, '2025_07_06_063448_create_sms_notification_saved_table', 15);

-- --------------------------------------------------------

--
-- Table structure for table `officials`
--

CREATE TABLE `officials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `term_start` date NOT NULL,
  `term_end` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `officials`
--

INSERT INTO `officials` (`id`, `user_id`, `name`, `email`, `position`, `term_start`, `term_end`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Maryjane v. Golde', NULL, 'secretary', '2025-06-06', '2027-06-06', 1, NULL, '2025-06-07 06:39:46', '2025-06-07 07:20:51'),
(2, NULL, 'Virgie G. Malinao', NULL, 'vise_president', '2025-06-06', '2027-06-06', 1, NULL, '2025-06-07 06:42:51', '2025-06-07 06:42:51'),
(4, NULL, 'dummy official', NULL, 'secretary', '2025-06-07', '2025-06-07', 1, '2025-06-07 21:53:01', '2025-06-07 21:52:48', '2025-06-07 21:53:01'),
(5, NULL, 'java', 'java@gmail.com', 'secretary', '2025-06-14', '2027-06-14', 1, NULL, '2025-06-14 00:05:15', '2025-06-14 00:05:15');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('jobelgolde43@gmail.com', '$2y$12$pBgKLF0i/FCClZmls81/mOE1i/QVsr3W6NT6IbzpEXF21fK2ynJIK', '2025-08-21 14:39:12');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4Br2Xdr6JH8Myv4KMZSQ76OFCrOKeLufVyo0gl2B', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQnlKaHdLSWRzNzlqMlFkazRBNVBNOWdzYUFjcDdJendQZmlneDZ6NyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTE7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Njg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jb2xsZWN0b3IvdG9nZ2xlLXB1cm9rLWFzLWNvbGxlY3Rvci9hbGwvcHVyb2s0Ijt9fQ==', 1757121920),
('had2TxW92pKsJAq0sjM8hq6R9tWpJXF2kcLTyjRQ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoic09PRFU0UUtBa3VzckxtYk5OMnNlQzlsSjVxdjdpVjhGcURpTGNJMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1757292089);

-- --------------------------------------------------------

--
-- Table structure for table `sms_notification_saved`
--

CREATE TABLE `sms_notification_saved` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sms_notification_saved`
--

INSERT INTO `sms_notification_saved` (`id`, `message`, `type`, `created_at`, `updated_at`) VALUES
(1, 'We regret to inform you that asd asd has passed away. Last night will be held on 2025-07-06. Collection for burial assistance starts on 2025-07-10.\'', 'deathReport', '2025-07-06 00:53:20', '2025-07-06 00:53:20'),
(2, 'We regret to inform you that asd asd_modified has passed away. Last night will be held on 2025-07-17. Collection for burial assistance starts on 2025-07-09.\'', 'deathReport', '2025-07-06 01:17:01', '2025-07-06 01:17:01'),
(3, 'Hello [boggart], thank you for paying your contribution. This message confirms your payment for the upcoming schedule.', 'scheduleContribution', '2025-07-06 10:02:21', '2025-07-06 10:02:21'),
(4, 'Hello, our records show you still have an unpaid balance for the contribution (Damayan). Please settle it at your earliest convenience. Thank you.', 'reminders', '2025-07-06 10:02:36', '2025-07-06 10:02:36'),
(5, 'Total money disbursed so far is ₱300.00. Thank you for your continuous support.', 'fundUpdates', '2025-07-06 10:09:24', '2025-07-06 10:09:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `purok` enum('1','2','3','4') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `deleted_at`, `purok`) VALUES
(1, 'Rina Geronimo', 'jobelgolde43@gmail.com', '2025-04-17 10:52:51', '$2y$12$u.SWz91uteDfsudVhf/c1OTZ0X.L8jjvex90QTP39vxVCax7A9mvu', 'admin', NULL, NULL, '2025-07-02 11:16:54', NULL, '1'),
(5, 'Jobelle', 'iamjobelle@gmail.com', NULL, '$2y$12$xlii9WcPc081pBhDVsHxPOdIJTpDSlD4ScZ2seouXwOBvBVOdr/Tq', 'collector', NULL, '2025-06-15 04:29:28', '2025-08-08 18:51:09', '2025-08-08 18:51:09', '1'),
(6, 'alder', 'clemente@gmail.com', NULL, '$2y$12$ZJCVd1sR7zPQNjRpw8LhO.HQFOXwyrDRDUzHAOsRKH3SB/bbAe/fq', 'collector', NULL, '2025-06-25 16:09:58', '2025-08-08 18:49:16', '2025-08-08 18:49:16', '2'),
(7, 'Milanie  G.  Gimoro', 'milanie_protectdamayan@gmail.org', NULL, '$2y$12$RDZkChx2n0d3VmpQevJ/1.zbBb.7Cc1VuEy.p47X7Fjz1hk/pzuhu', 'collector', NULL, '2025-08-08 19:20:47', '2025-08-08 19:20:47', NULL, '2'),
(8, 'Lorilyn P. Gimoro', 'lorilyn_protectdamayan@gmail.org', NULL, '$2y$12$NQYXDDou3R9DXN3cd1oEH.a0SuWRgZsMFdRfgK9tlpJs7W/YQE4Ta', 'collector', NULL, '2025-08-08 19:21:47', '2025-08-08 19:21:47', NULL, '3'),
(10, 'Emireta Gimoro', 'emerita_protectdamayan@gmail.org', NULL, '$2y$12$Lw2PyNCxBFR9bYOpHprBjux1z/ONmLixwFepGzavOFmG4g5vm1RWC', 'collector', NULL, '2025-08-08 19:22:55', '2025-08-08 19:22:55', NULL, '1'),
(11, 'Jiji V.  Gimoro', 'jiji_protectdamayan@gmail.org', NULL, '$2y$12$zDGtpeCYBQwRNe3CcjSkouL4YywEj2wmtV4fibi1OBBZzQFnSFpO6', 'collector', NULL, '2025-08-08 19:23:21', '2025-08-08 19:23:21', NULL, '4');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assistance_distribution`
--
ALTER TABLE `assistance_distribution`
  ADD PRIMARY KEY (`distribution_id`),
  ADD KEY `assistance_distribution_report_id_foreign` (`report_id`);

--
-- Indexes for table `beneficiary`
--
ALTER TABLE `beneficiary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beneficiary_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `contributions`
--
ALTER TABLE `contributions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contributions_member_id_foreign` (`member_id`),
  ADD KEY `contributions_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `death_reports`
--
ALTER TABLE `death_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `death_reports_reported_by_foreign` (`reported_by`),
  ADD KEY `death_reports_member_id_foreign` (`member_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `officials`
--
ALTER TABLE `officials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `officials_email_unique` (`email`),
  ADD KEY `officials_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sms_notification_saved`
--
ALTER TABLE `sms_notification_saved`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assistance_distribution`
--
ALTER TABLE `assistance_distribution`
  MODIFY `distribution_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beneficiary`
--
ALTER TABLE `beneficiary`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=462;

--
-- AUTO_INCREMENT for table `contributions`
--
ALTER TABLE `contributions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `death_reports`
--
ALTER TABLE `death_reports`
  MODIFY `report_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `officials`
--
ALTER TABLE `officials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sms_notification_saved`
--
ALTER TABLE `sms_notification_saved`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assistance_distribution`
--
ALTER TABLE `assistance_distribution`
  ADD CONSTRAINT `assistance_distribution_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `death_reports` (`report_id`) ON DELETE CASCADE;

--
-- Constraints for table `beneficiary`
--
ALTER TABLE `beneficiary`
  ADD CONSTRAINT `beneficiary_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contributions`
--
ALTER TABLE `contributions`
  ADD CONSTRAINT `contributions_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contributions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `death_reports`
--
ALTER TABLE `death_reports`
  ADD CONSTRAINT `death_reports_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `death_reports_reported_by_foreign` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `officials`
--
ALTER TABLE `officials`
  ADD CONSTRAINT `officials_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
