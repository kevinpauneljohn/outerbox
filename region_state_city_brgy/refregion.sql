-- Database Manager 4.2.5 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `refregion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `psgcCode` varchar(255) DEFAULT NULL,
  `regDesc` text,
  `regCode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `refregion` (`id`, `psgcCode`, `regDesc`, `regCode`) VALUES
(1,	'010000000',	'REGION I (ILOCOS REGION)',	'01'),
(2,	'020000000',	'REGION II (CAGAYAN VALLEY)',	'02'),
(3,	'030000000',	'REGION III (CENTRAL LUZON)',	'03'),
(4,	'040000000',	'REGION IV-A (CALABARZON)',	'04'),
(5,	'170000000',	'REGION IV-B (MIMAROPA)',	'17'),
(6,	'050000000',	'REGION V (BICOL REGION)',	'05'),
(7,	'060000000',	'REGION VI (WESTERN VISAYAS)',	'06'),
(8,	'070000000',	'REGION VII (CENTRAL VISAYAS)',	'07'),
(9,	'080000000',	'REGION VIII (EASTERN VISAYAS)',	'08'),
(10,	'090000000',	'REGION IX (ZAMBOANGA PENINSULA)',	'09'),
(11,	'100000000',	'REGION X (NORTHERN MINDANAO)',	'10'),
(12,	'110000000',	'REGION XI (DAVAO REGION)',	'11'),
(13,	'120000000',	'REGION XII (SOCCSKSARGEN)',	'12'),
(14,	'130000000',	'NATIONAL CAPITAL REGION (NCR)',	'13'),
(15,	'140000000',	'CORDILLERA ADMINISTRATIVE REGION (CAR)',	'14'),
(16,	'150000000',	'AUTONOMOUS REGION IN MUSLIM MINDANAO (ARMM)',	'15'),
(17,	'160000000',	'REGION XIII (Caraga)',	'16');

-- 2019-12-02 13:44:32
