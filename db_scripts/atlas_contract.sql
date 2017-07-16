-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 04, 2017 at 05:22 AM
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
-- Table structure for table `atlas_contract`
--

CREATE TABLE `atlas_contract` (
  `contract_id` int(10) UNSIGNED NOT NULL,
  `client_id_fk` int(11) NOT NULL,
  `contract_jail_from` varchar(255) NOT NULL,
  `contract_person_name` varchar(255) NOT NULL,
  `contract_bond_sum` int(25) NOT NULL,
  `contract_date` date NOT NULL,
  `contract_bail_bonds` varchar(255) NOT NULL,
  `contract_cosigner` varchar(255) NOT NULL,
  `contract_current_address` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atlas_contract`
--
ALTER TABLE `atlas_contract`
  ADD PRIMARY KEY (`contract_id`),
  ADD UNIQUE KEY `client_id_fk` (`client_id_fk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atlas_contract`
--
ALTER TABLE `atlas_contract`
  MODIFY `contract_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
