-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 11, 2017 at 07:12 AM
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
-- Table structure for table `atlas_signature_request`
--

CREATE TABLE `atlas_signature_request` (
  `request_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `event_type` varchar(255) NOT NULL,
  `signature_id` varchar(255) NOT NULL,
  `signature_request_id` varchar(255) NOT NULL,
  `signature_url` varchar(255) DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `date_signed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `atlas_signature_request`
--
ALTER TABLE `atlas_signature_request`
  ADD PRIMARY KEY (`request_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `atlas_signature_request`
--
ALTER TABLE `atlas_signature_request`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
