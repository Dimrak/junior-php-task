-- Adminer 4.6.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `count_n`;
CREATE TABLE `count_n` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(50) COLLATE utf8_bin NOT NULL,
  `visitor` varchar(50) COLLATE utf8_bin NOT NULL,
  `cats` varchar(255) COLLATE utf8_bin NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
