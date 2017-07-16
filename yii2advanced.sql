-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2015 at 07:07 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yii2advanced`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_type`
--

CREATE TABLE IF NOT EXISTS `account_type` (
  `account_type_id` int(5) NOT NULL AUTO_INCREMENT,
  `accounttype` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `added_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`account_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `account_type`
--

INSERT INTO `account_type` (`account_type_id`, `accounttype`, `added_date`, `is_active`) VALUES
(1, 'Admin', '2011-06-03 20:52:24', 1),
(2, 'User', '2011-06-03 20:52:29', 1),
(3, 'Merchant', '2012-01-16 10:42:28', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `firstname`, `lastname`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `image`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mts', 'Admin', 'admin', 'mts123', '$2y$13$nfRgl250EMAm9zEpFHbEzO/zattTX6NTPThOi6iCKAwypaLiLE2Pa', 'mts', 'This is image', 'admin@yiidemo.com', 10, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`country_id`),
  KEY `IDX_COUNTRIES_NAME` (`country_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=281 ;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_id`, `country_name`, `is_active`) VALUES
(1, 'Afghanistan1', 1),
(2, 'Albania', 1),
(3, 'Algeria', 1),
(4, 'American Samoa', 1),
(5, 'Andorra', 1),
(6, 'Angola', 1),
(7, 'Anguilla', 1),
(8, 'Antarctica', 1),
(9, 'Antigua and Barbuda', 1),
(10, 'Argentina', 1),
(11, 'Armenia', 1),
(12, 'Aruba', 1),
(13, 'Australia', 1),
(14, 'Austria', 1),
(15, 'Azerbaijan', 1),
(16, 'Bahamas', 1),
(17, 'Bahrain', 1),
(18, 'Bangladesh', 1),
(19, 'Barbados', 1),
(20, 'Belarus', 1),
(21, 'Belgium', 1),
(22, 'Belize', 1),
(23, 'Benin', 1),
(24, 'Bermuda', 1),
(25, 'Bhutan', 1),
(26, 'Bolivia', 1),
(27, 'Bosnia and Herzegowina', 1),
(28, 'Botswana', 1),
(29, 'Bouvet Island', 1),
(30, 'Brazil', 1),
(31, 'British Indian Ocean Territory', 0),
(32, 'Brunei Darussalam', 0),
(33, 'Bulgaria', 0),
(34, 'Burkina Faso', 0),
(35, 'Burundi', 0),
(36, 'Cambodia', 0),
(37, 'Cameroon', 0),
(38, 'Canada', 0),
(39, 'Cape Verde', 0),
(40, 'Cayman Islands', 0),
(41, 'Central African Republic', 0),
(42, 'Chad', 0),
(43, 'Chile', 0),
(44, 'China', 0),
(45, 'Christmas Island', 0),
(46, 'Cocos (Keeling) Islands', 0),
(47, 'Colombia', 0),
(48, 'Comoros', 0),
(49, 'Congo', 0),
(50, 'Cook Islands', 0),
(51, 'Costa Rica', 0),
(52, 'Cote D''Ivoire', 0),
(53, 'Croatia', 0),
(54, 'Cuba', 0),
(55, 'Cyprus', 0),
(56, 'Czech Republic', 0),
(57, 'Denmark', 0),
(58, 'Djibouti', 0),
(59, 'Dominica', 0),
(60, 'Dominican Republic', 0),
(61, 'East Timor', 0),
(62, 'Ecuador', 0),
(63, 'Egypt', 0),
(64, 'El Salvador', 0),
(65, 'Equatorial Guinea', 0),
(66, 'Eritrea', 0),
(67, 'Estonia', 0),
(68, 'Ethiopia', 0),
(69, 'Falkland Islands (Malvinas)', 0),
(70, 'Faroe Islands', 0),
(71, 'Fiji', 0),
(72, 'Finland', 0),
(73, 'France', 0),
(74, 'France, Metropolitan', 0),
(75, 'French Guiana', 0),
(76, 'French Polynesia', 0),
(77, 'French Southern Territories', 0),
(78, 'Gabon', 0),
(79, 'Gambia', 0),
(80, 'Georgia', 0),
(81, 'Germany', 0),
(82, 'Ghana', 0),
(83, 'Gibraltar', 0),
(84, 'Greece', 0),
(85, 'Greenland', 0),
(86, 'Grenada', 0),
(87, 'Guadeloupe', 0),
(88, 'Guam', 0),
(89, 'Guatemala', 0),
(90, 'Guinea', 0),
(91, 'Guinea-bissau', 0),
(92, 'Guyana', 0),
(93, 'Haiti', 0),
(94, 'Heard and Mc Donald Islands', 0),
(95, 'Honduras', 0),
(96, 'Hong Kong', 0),
(97, 'Hungary', 0),
(98, 'Iceland', 0),
(99, 'India', 0),
(100, 'Indonesia', 0),
(101, 'Iran (Islamic Republic of)', 0),
(102, 'Iraq', 0),
(103, 'Ireland', 0),
(104, 'Israel', 0),
(105, 'Italy', 0),
(106, 'Jamaica', 0),
(107, 'Japan', 0),
(108, 'Jordan', 0),
(109, 'Kazakhstan', 0),
(110, 'Kenya', 0),
(111, 'Kiribati', 0),
(112, 'Korea, Democratic People''s Republic of', 0),
(113, 'Korea, Republic of', 0),
(114, 'Kuwait', 0),
(115, 'Kyrgyzstan', 0),
(116, 'Lao People''s Democratic Republic', 0),
(117, 'Latvia', 0),
(118, 'Lebanon', 0),
(119, 'Lesotho', 0),
(120, 'Liberia', 0),
(121, 'Libyan Arab Jamahiriya', 0),
(122, 'Liechtenstein', 0),
(123, 'Lithuania', 0),
(124, 'Luxembourg', 0),
(125, 'Macau', 0),
(126, 'Macedonia, The Former Yugoslav Republic of', 0),
(127, 'Madagascar', 0),
(128, 'Malawi', 0),
(129, 'Malaysia', 0),
(130, 'Maldives', 0),
(131, 'Mali', 0),
(132, 'Malta', 0),
(133, 'Marshall Islands', 0),
(134, 'Martinique', 0),
(135, 'Mauritania', 0),
(136, 'Mauritius', 0),
(137, 'Mayotte', 0),
(138, 'Mexico', 0),
(139, 'Micronesia, Federated States of', 0),
(140, 'Moldova, Republic of', 0),
(141, 'Monaco', 0),
(142, 'Mongolia', 0),
(143, 'Montserrat', 0),
(144, 'Morocco', 0),
(145, 'Mozambique', 0),
(146, 'Myanmar', 0),
(147, 'Namibia', 0),
(148, 'Nauru', 0),
(149, 'Nepal', 0),
(150, 'Netherlands', 0),
(151, 'Netherlands Antilles', 0),
(152, 'New Caledonia', 0),
(153, 'New Zealand', 0),
(154, 'Nicaragua', 0),
(155, 'Niger', 0),
(156, 'Nigeria', 0),
(157, 'Niue', 0),
(158, 'Norfolk Island', 0),
(159, 'Northern Mariana Islands', 0),
(160, 'Norway', 0),
(161, 'Oman', 0),
(162, 'Pakistan', 0),
(163, 'Palau', 0),
(164, 'Panama', 0),
(165, 'Papua New Guinea', 0),
(166, 'Paraguay', 0),
(167, 'Peru', 0),
(168, 'Philippines', 0),
(169, 'Pitcairn', 0),
(170, 'Poland', 0),
(171, 'Portugal', 0),
(172, 'Puerto Rico', 0),
(173, 'Qatar', 0),
(174, 'Reunion', 0),
(175, 'Romania', 0),
(176, 'Russian Federation', 0),
(177, 'Rwanda', 0),
(178, 'Saint Kitts and Nevis', 0),
(179, 'Saint Lucia', 0),
(180, 'Saint Vincent and the Grenadines', 0),
(181, 'Samoa', 0),
(182, 'San Marino', 0),
(183, 'Sao Tome and Principe', 0),
(184, 'Saudi Arabia', 0),
(185, 'Senegal', 0),
(186, 'Seychelles', 0),
(187, 'Sierra Leone', 0),
(188, 'Singapore', 0),
(189, 'Slovakia (Slovak Republic)', 0),
(190, 'Slovenia', 0),
(191, 'Solomon Islands', 0),
(192, 'Somalia', 0),
(193, 'South Africa', 0),
(194, 'South Georgia and the South Sandwich Islands', 0),
(195, 'Spain', 0),
(196, 'Sri Lanka', 0),
(197, 'St. Helena', 0),
(198, 'St. Pierre and Miquelon', 0),
(199, 'Sudan', 0),
(200, 'Suriname', 0),
(201, 'Svalbard and Jan Mayen Islands', 0),
(202, 'Swaziland', 0),
(203, 'Sweden', 0),
(204, 'Switzerland', 0),
(205, 'Syrian Arab Republic', 0),
(206, 'Taiwan', 0),
(207, 'Tajikistan', 0),
(208, 'Tanzania, United Republic of', 0),
(209, 'Thailand', 0),
(210, 'Togo', 0),
(211, 'Tokelau', 0),
(212, 'Tonga', 0),
(213, 'Trinidad and Tobago', 0),
(214, 'Tunisia', 0),
(215, 'Turkey', 0),
(216, 'Turkmenistan', 0),
(217, 'Turks and Caicos Islands', 0),
(218, 'Tuvalu', 0),
(219, 'Uganda', 0),
(220, 'Ukraine', 0),
(221, 'United Arab Emirates', 0),
(222, 'United Kingdom', 0),
(223, 'United States', 1),
(224, 'United States Minor Outlying Islands', 0),
(225, 'Uruguay', 0),
(226, 'Uzbekistan', 0),
(227, 'Vanuatu', 0),
(228, 'Vatican City State (Holy See)', 0),
(229, 'Venezuela', 0),
(230, 'Viet Nam', 0),
(231, 'Virgin Islands (British)', 0),
(232, 'Virgin Islands (U.S.)', 0),
(233, 'Wallis and Futuna Islands', 0),
(234, 'Western Sahara', 0),
(235, 'Yemen', 0),
(236, 'Yugoslavia', 0),
(237, 'Zaire', 0),
(238, 'Zambia', 0),
(239, 'Zimbabwe', 0),
(240, 'test - 0', 0),
(241, 'test - 1', 0),
(242, 'test - 2', 0),
(243, 'test - 3', 0),
(244, 'test - 4', 0),
(245, 'test - 5', 0),
(246, 'test - 6', 0),
(247, 'test - 7', 0),
(248, 'test - 8', 0),
(249, 'test - 9', 0),
(250, 'test - 10', 0),
(251, 'test - 11', 0),
(252, 'test - 12', 0),
(253, 'test - 13', 0),
(254, 'test - 14', 0),
(255, 'test - 15', 0),
(256, 'test - 16', 0),
(257, 'test - 17', 0),
(258, 'test - 18', 0),
(259, 'test - 19', 0),
(260, 'test - 0', 0),
(261, 'test - 1', 0),
(262, 'test - 2', 0),
(263, 'test - 3', 0),
(264, 'test - 4', 0),
(265, 'test - 5', 0),
(266, 'test - 6', 0),
(267, 'test - 7', 0),
(268, 'test - 8', 0),
(269, 'test - 9', 0),
(270, 'test - 10', 0),
(271, 'test - 11', 0),
(272, 'test - 12', 0),
(273, 'test - 13', 0),
(274, 'test - 14', 0),
(280, 'APCI', 1);

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1435570893),
('m130524_201442_init', 1435570905);

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE IF NOT EXISTS `state` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id_fk` int(11) NOT NULL,
  `state_code` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `state` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`state_id`),
  KEY `idx_zones_country_id` (`country_id_fk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=227 ;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`state_id`, `country_id_fk`, `state_code`, `state`, `is_active`) VALUES
(1, 223, 'AL', 'Alabama', 1),
(2, 223, 'AK', 'Alaska', 1),
(3, 223, 'AS', 'American Samoa', 1),
(4, 223, 'AZ', 'Arizona', 1),
(5, 223, 'AR', 'Arkansas', 1),
(6, 223, 'AF', 'Armed Forces Africa', 1),
(7, 223, 'AA', 'Armed Forces Americas', 1),
(8, 223, 'AC', 'Armed Forces Canada', 1),
(9, 223, 'AE', 'Armed Forces Europe', 1),
(10, 223, 'AM', 'Armed Forces Middle East', 1),
(11, 223, 'AP', 'Armed Forces Pacific', 1),
(12, 223, 'CA', 'California', 1),
(13, 223, 'CO', 'Colorado', 1),
(14, 223, 'CT', 'Connecticut', 1),
(15, 223, 'DE', 'Delaware', 1),
(16, 223, 'DC', 'District of Columbia', 1),
(17, 223, 'FM', 'Federated States Of Micronesia', 1),
(18, 223, 'FL', 'Florida', 1),
(19, 223, 'GA', 'Georgia', 1),
(20, 223, 'GU', 'Guam', 1),
(21, 223, 'HI', 'Hawaii', 1),
(22, 223, 'ID', 'Idaho', 1),
(23, 223, 'IL', 'Illinois', 1),
(24, 223, 'IN', 'Indiana', 1),
(25, 223, 'IA', 'Iowa', 1),
(26, 223, 'KS', 'Kansas', 1),
(27, 223, 'KY', 'Kentucky', 1),
(28, 223, 'LA', 'Louisiana', 1),
(29, 223, 'ME', 'Maine', 1),
(30, 223, 'MH', 'Marshall Islands', 1),
(31, 223, 'MD', 'Maryland', 1),
(32, 223, 'MA', 'Massachusetts', 1),
(33, 223, 'MI', 'Michigan', 1),
(34, 223, 'MN', 'Minnesota', 1),
(35, 223, 'MS', 'Mississippi', 1),
(36, 223, 'MO', 'Missouri', 1),
(37, 223, 'MT', 'Montana', 1),
(38, 223, 'NE', 'Nebraska', 1),
(39, 223, 'NV', 'Nevada', 1),
(40, 223, 'NH', 'New Hampshire', 1),
(41, 223, 'NJ', 'New Jersey', 1),
(42, 223, 'NM', 'New Mexico', 1),
(43, 223, 'NY', 'New York', 1),
(44, 223, 'NC', 'North Carolina', 1),
(45, 223, 'ND', 'North Dakota', 1),
(46, 223, 'MP', 'Northern Mariana Islands', 1),
(47, 223, 'OH', 'Ohio', 1),
(48, 223, 'OK', 'Oklahoma', 1),
(49, 223, 'OR', 'Oregon', 1),
(50, 223, 'PW', 'Palau', 1),
(51, 223, 'PA', 'Pennsylvania', 1),
(52, 223, 'PR', 'Puerto Rico', 1),
(53, 223, 'RI', 'Rhode Island', 1),
(54, 223, 'SC', 'South Carolina', 1),
(55, 223, 'SD', 'South Dakota', 1),
(56, 223, 'TN', 'Tennessee', 1),
(57, 223, 'TX', 'Texas', 1),
(58, 223, 'UT', 'Utah', 1),
(59, 223, 'VT', 'Vermont', 1),
(60, 223, 'VI', 'Virgin Islands', 1),
(61, 223, 'VA', 'Virginia', 1),
(62, 223, 'WA', 'Washington', 1),
(63, 223, 'WV', 'West Virginia', 1),
(64, 223, 'WI', 'Wisconsin', 1),
(65, 223, 'WY', 'Wyoming', 1),
(66, 38, 'AB', 'Alberta', 1),
(67, 38, 'BC', 'British Columbia', 1),
(68, 38, 'MB', 'Manitoba', 1),
(69, 38, 'NF', 'Newfoundland', 1),
(70, 38, 'NB', 'New Brunswick', 1),
(71, 38, 'NS', 'Nova Scotia', 1),
(72, 38, 'NT', 'Northwest Territories', 1),
(73, 38, 'NU', 'Nunavut', 1),
(74, 38, 'ON', 'Ontario', 1),
(75, 38, 'PE', 'Prince Edward Island', 1),
(76, 38, 'QC', 'Quebec', 1),
(77, 38, 'SK', 'Saskatchewan', 1),
(78, 38, 'YT', 'Yukon Territory', 1),
(79, 81, 'NDS', 'Niedersachsen', 1),
(80, 81, 'BAW', 'Baden-WÃ¼rttemberg', 1),
(81, 81, 'BAY', 'Bayern', 1),
(82, 81, 'BER', 'Berlin', 1),
(83, 81, 'BRG', 'Brandenburg', 1),
(84, 81, 'BRE', 'Bremen', 1),
(85, 81, 'HAM', 'Hamburg', 1),
(86, 81, 'HES', 'Hessen', 1),
(87, 81, 'MEC', 'Mecklenburg-Vorpommern', 1),
(88, 81, 'NRW', 'Nordrhein-Westfalen', 1),
(89, 81, 'RHE', 'Rheinland-Pfalz', 1),
(90, 81, 'SAR', 'Saarland', 1),
(91, 81, 'SAS', 'Sachsen', 1),
(92, 81, 'SAC', 'Sachsen-Anhalt', 1),
(93, 81, 'SCN', 'Schleswig-Holstein', 1),
(94, 81, 'THE', 'ThÃ¼ringen', 1),
(95, 14, 'WI', 'Wien', 1),
(96, 14, 'NO', 'NiederÃ¶sterreich', 1),
(97, 14, 'OO', 'OberÃ¶sterreich', 1),
(98, 14, 'SB', 'Salzburg', 1),
(99, 14, 'KN', 'KÃ¤rnten', 1),
(100, 14, 'ST', 'Steiermark', 1),
(101, 14, 'TI', 'Tirol', 1),
(102, 14, 'BL', 'Burgenland', 1),
(103, 14, 'VB', 'Voralberg', 1),
(104, 204, 'AG', 'Aargau', 1),
(105, 204, 'AI', 'Appenzell Innerrhoden', 1),
(106, 204, 'AR', 'Appenzell Ausserrhoden', 1),
(107, 204, 'BE', 'Bern', 1),
(108, 204, 'BL', 'Basel-Landschaft', 1),
(109, 204, 'BS', 'Basel-Stadt', 1),
(110, 204, 'FR', 'Freiburg', 1),
(111, 204, 'GE', 'Genf', 1),
(112, 204, 'GL', 'Glarus', 1),
(113, 204, 'JU', 'GraubÃ¼nden', 1),
(114, 204, 'JU', 'Jura', 1),
(115, 204, 'LU', 'Luzern', 1),
(116, 204, 'NE', 'Neuenburg', 1),
(117, 204, 'NW', 'Nidwalden', 1),
(118, 204, 'OW', 'Obwalden', 1),
(119, 204, 'SG', 'St. Gallen', 1),
(120, 204, 'SH', 'Schaffhausen', 1),
(121, 204, 'SO', 'Solothurn', 1),
(122, 204, 'SZ', 'Schwyz', 1),
(123, 204, 'TG', 'Thurgau', 1),
(124, 204, 'TI', 'Tessin', 1),
(125, 204, 'UR', 'Uri', 1),
(126, 204, 'VD', 'Waadt', 1),
(127, 204, 'VS', 'Wallis', 1),
(128, 204, 'ZG', 'Zug', 1),
(129, 204, 'ZH', 'ZÃ¼rich', 1),
(130, 195, 'A CoruÃ±a', 'A CoruÃ±a', 1),
(131, 195, 'Alava', 'Alava', 1),
(132, 195, 'Albacete', 'Albacete', 1),
(133, 195, 'Alicante', 'Alicante', 1),
(134, 195, 'Almeria', 'Almeria', 1),
(135, 195, 'Asturias', 'Asturias', 1),
(136, 195, 'Avila', 'Avila', 1),
(137, 195, 'Badajoz', 'Badajoz', 1),
(138, 195, 'Baleares', 'Baleares', 1),
(139, 195, 'Barcelona', 'Barcelona', 1),
(140, 195, 'Burgos', 'Burgos', 1),
(141, 195, 'Caceres', 'Caceres', 1),
(142, 195, 'Cadiz', 'Cadiz', 1),
(143, 195, 'Cantabria', 'Cantabria', 1),
(144, 195, 'Castellon', 'Castellon', 1),
(145, 195, 'Ceuta', 'Ceuta', 1),
(146, 195, 'Ciudad Real', 'Ciudad Real', 1),
(147, 195, 'Cordoba', 'Cordoba', 1),
(148, 195, 'Cuenca', 'Cuenca', 1),
(149, 195, 'Girona', 'Girona', 1),
(150, 195, 'Granada', 'Granada', 1),
(151, 195, 'Guadalajara', 'Guadalajara', 1),
(152, 195, 'Guipuzcoa', 'Guipuzcoa', 1),
(153, 195, 'Huelva', 'Huelva', 1),
(154, 195, 'Huesca', 'Huesca', 1),
(155, 195, 'Jaen', 'Jaen', 1),
(156, 195, 'La Rioja', 'La Rioja', 1),
(157, 195, 'Las Palmas', 'Las Palmas', 1),
(158, 195, 'Leon', 'Leon', 1),
(159, 195, 'Lleida', 'Lleida', 1),
(160, 195, 'Lugo', 'Lugo', 1),
(161, 195, 'Madrid', 'Madrid', 1),
(162, 195, 'Malaga', 'Malaga', 1),
(163, 195, 'Melilla', 'Melilla', 1),
(164, 195, 'Murcia', 'Murcia', 1),
(165, 195, 'Navarra', 'Navarra', 1),
(166, 195, 'Ourense', 'Ourense', 1),
(167, 195, 'Palencia', 'Palencia', 1),
(168, 195, 'Pontevedra', 'Pontevedra', 1),
(169, 195, 'Salamanca', 'Salamanca', 1),
(170, 195, 'Santa Cruz de Tenerife', 'Santa Cruz de Tenerife', 1),
(171, 195, 'Segovia', 'Segovia', 1),
(172, 195, 'Sevilla', 'Sevilla', 1),
(173, 195, 'Soria', 'Soria', 1),
(174, 195, 'Tarragona', 'Tarragona', 1),
(175, 195, 'Teruel', 'Teruel', 1),
(176, 195, 'Toledo', 'Toledo', 1),
(177, 195, 'Valencia', 'Valencia', 1),
(178, 195, 'Valladolid', 'Valladolid', 1),
(179, 195, 'Vizcaya', 'Vizcaya', 1),
(180, 195, 'Zamora', 'Zamora', 1),
(181, 195, 'Zaragoza', 'Zaragoza', 1),
(182, 99, 'NI', 'North India', 1),
(183, 99, 'SI', 'South India', 1),
(184, 99, 'EI', 'East India', 1),
(185, 99, 'CI', 'Central India', 1),
(226, 1, 'ss', 'AlabamaNw', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Jack', '1OLUM9lXDGZvtEtkSQz782n8VD3CViv_', '$2y$13$e4LPUxx5yP4tFf9uplsshet7R8eNW7TbOZp9fTwsF7jo9TeFS/YVe', NULL, 'mtstest1@gmail.com', 10, 1435586518, 1435586518);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
