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
-- Table structure for table `altas_applications`
--

CREATE TABLE `altas_applications` (
  `application_id` int(10) UNSIGNED NOT NULL,
  `client_id_fk` int(10) UNSIGNED NOT NULL,
  `defendant_name` varchar(255) NOT NULL,
  `defendant_us_citizen` varchar(10) NOT NULL,
  `defendant_dl` varchar(255) NOT NULL,
  `defendant_dob` date NOT NULL,
  `defendant_ssn` int(10) NOT NULL,
  `defendant_phone` int(25) NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `applicant_dob` date NOT NULL,
  `applicant_ssn` int(10) NOT NULL,
  `applicant_phone` int(25) NOT NULL,
  `applicant_current_address` varchar(255) NOT NULL,
  `applicant_current_city` varchar(255) NOT NULL,
  `applicant_current_state` varchar(255) NOT NULL,
  `applicant_current_zip_code` varchar(25) NOT NULL,
  `applicant_current_homeownership` varchar(10) NOT NULL,
  `applicant_current_monthly_payment` varchar(255) NOT NULL,
  `applicant_current_how_long` varchar(255) NOT NULL,
  `applicant_previous_address` varchar(255) NOT NULL,
  `applicant_previous_city` varchar(255) NOT NULL,
  `applicant_previous_state` varchar(255) NOT NULL,
  `applicant_previous_zip_code` varchar(25) NOT NULL,
  `applicant_previous_homeownership` varchar(10) NOT NULL,
  `applicant_previous_monthly_payment` varchar(255) NOT NULL,
  `applicant_previous_how_long` varchar(255) NOT NULL,
  `employment_current_employer` varchar(255) NOT NULL,
  `employment_employer_address` varchar(255) NOT NULL,
  `employment_how_long` varchar(255) NOT NULL,
  `employment_phone` varchar(25) DEFAULT NULL,
  `employment_email_address` varchar(255) NOT NULL,
  `employment_fax` int(25) DEFAULT NULL,
  `employment_city` varchar(255) NOT NULL,
  `employment_state` varchar(255) NOT NULL,
  `employment_zip_code` varchar(25) NOT NULL,
  `employment_position` varchar(255) NOT NULL,
  `employment_salary_type` varchar(20) NOT NULL,
  `employment_annual_income` int(30) NOT NULL,
  `personal_reference_name` varchar(255) NOT NULL,
  `personal_reference_address` varchar(255) NOT NULL,
  `personal_reference_city` varchar(255) NOT NULL,
  `personal_reference_state` varchar(255) NOT NULL,
  `personal_reference_zip_code` varchar(25) NOT NULL,
  `personal_reference_phone` varchar(25) NOT NULL,
  `personal_reference_relationship` varchar(255) NOT NULL,
  `references_name` varchar(255) NOT NULL,
  `references_address` varchar(255) NOT NULL,
  `references_phone` varchar(25) NOT NULL,
  `signature_date` date NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `altas_applications`
--
ALTER TABLE `altas_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD UNIQUE KEY `client_id_fk` (`client_id_fk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `altas_applications`
--
ALTER TABLE `altas_applications`
  MODIFY `application_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
