-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2024 at 07:10 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `t-man`
--

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `ime` varchar(20) NOT NULL,
  `opis` varchar(256) NOT NULL,
  `koncan` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `ime`, `opis`, `koncan`) VALUES
(48, 'Projekt 1', 'Projekt 1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `projectsuser`
--

CREATE TABLE `projectsuser` (
  `id` int(11) NOT NULL,
  `id_projekta` int(11) NOT NULL,
  `id_userja` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projectsuser`
--

INSERT INTO `projectsuser` (`id`, `id_projekta`, `id_userja`) VALUES
(40, 48, 94);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `id_projekta` int(11) NOT NULL,
  `ime` varchar(20) NOT NULL,
  `opis` varchar(256) NOT NULL,
  `zac_cas` timestamp NOT NULL DEFAULT current_timestamp(),
  `kon_cas` timestamp NULL DEFAULT NULL,
  `koncano` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `id_projekta`, `ime`, `opis`, `zac_cas`, `kon_cas`, `koncano`) VALUES
(28, 48, 'Naloga 1', 'Naloga 1', '2024-06-16 17:04:31', '2024-06-23 17:04:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `vzdevek` varchar(20) NOT NULL,
  `geslo` varchar(255) NOT NULL,
  `ime` varchar(20) NOT NULL,
  `priimek` varchar(20) NOT NULL,
  `mail` varchar(40) NOT NULL,
  `admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `vzdevek`, `geslo`, `ime`, `priimek`, `mail`, `admin`) VALUES
(94, 'Admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', 'Admin', 'admin@mail.com', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projectsuser`
--
ALTER TABLE `projectsuser`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_project` (`id_projekta`),
  ADD KEY `id_userja` (`id_userja`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_projekta` (`id_projekta`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `projectsuser`
--
ALTER TABLE `projectsuser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projectsuser`
--
ALTER TABLE `projectsuser`
  ADD CONSTRAINT `id_project` FOREIGN KEY (`id_projekta`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `id_userja` FOREIGN KEY (`id_userja`) REFERENCES `user` (`id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `id_projekta` FOREIGN KEY (`id_projekta`) REFERENCES `projects` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
