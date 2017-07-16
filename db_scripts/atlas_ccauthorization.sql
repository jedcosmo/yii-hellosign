-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 11, 2017 at 05:43 AM
-- Server version: 5.5.55-0ubuntu0.14.04.1
-- PHP Version: 7.0.19-1+deb.sury.org~trusty+2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gmpdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `atlas_ccauthorization`
--

CREATE TABLE `atlas_ccauthorization` (
  `ccauthorization_id` int(11) NOT NULL,
  `client_id_fk` int(11) NOT NULL,
  `ccauthorization_premiunm_amount` int(25) NOT NULL,
  `ccauthorization_premiunm_amount_text` varchar(255) NOT NULL,
  `ccauthorization_security_code` varchar(25) NOT NULL,
  `ccauthorization_card_type` varchar(25) NOT NULL,
  `ccauthorization_card_name` varchar(255) NOT NULL,
  `ccauthorization_card_number` varchar(255) NOT NULL,
  `ccauthorization_card_expiration` date NOT NULL,
  `ccauthorization_billing_address` varchar(255) NOT NULL,
  `ccauthorization_billing_city` varchar(255) NOT NULL,
  `ccauthorization_state` varchar(255) NOT NULL,
  `ccauthorization_zip_code` varchar(255) NOT NULL,
  `ccauthorization_date_signed` date NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atlas_ccauthorization`
--
ALTER TABLE `atlas_ccauthorization`
  ADD PRIMARY KEY (`ccauthorization_id`),
  ADD UNIQUE KEY `client_id_fk` (`client_id_fk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atlas_ccauthorization`
--
ALTER TABLE `atlas_ccauthorization`
  MODIFY `ccauthorization_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
