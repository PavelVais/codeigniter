-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2014 at 01:01 PM
-- Server version: 5.6.17
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zacivpohybucz1`
--

-- --------------------------------------------------------

--
-- Table structure for table `errors`
--

CREATE TABLE IF NOT EXISTS `errors` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `url` varchar(200) NOT NULL,
  `class` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `file` varchar(200) DEFAULT NULL,
  `line` int(5) DEFAULT '0',
  `detail` text,
  `viewed` tinyint(1) DEFAULT '0',
  `ip` varchar(40) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `user_id` int(9) unsigned DEFAULT NULL,
  `user_agent` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
