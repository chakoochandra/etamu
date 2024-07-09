-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.24-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for tamuku
CREATE DATABASE IF NOT EXISTS `tamuku` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `tamuku`;

-- Dumping structure for table tamuku.ci_sessions
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`,`ip_address`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table tamuku.ci_sessions: ~0 rows (approximately)
DELETE FROM `ci_sessions`;
/*!40000 ALTER TABLE `ci_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ci_sessions` ENABLE KEYS */;

-- Dumping structure for table tamuku.configs
CREATE TABLE IF NOT EXISTS `configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) DEFAULT NULL,
  `value` varchar(250) DEFAULT NULL,
  `category` tinyint(4) DEFAULT NULL,
  `note` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- Dumping data for table tamuku.configs: ~10 rows (approximately)
DELETE FROM `configs`;
/*!40000 ALTER TABLE `configs` DISABLE KEYS */;
INSERT INTO `configs` (`id`, `key`, `value`, `category`, `note`) VALUES
	(1, 'APP_VERSION', '1.0', 5, 'string. versi aplikasi'),
	(2, 'APP_NAME', 'Buku Tamu Digital', 5, 'string. nama aplikasi'),
	(3, 'APP_SHORT_NAME', 'eTamu', 5, 'string. nama pendek aplikasi. [BACKUP - NANTI DIHAPUS]'),
	(4, 'SATKER_NAME', 'Kemenag Kabupaten ABC', 1, 'string. nama satker'),
	(5, 'SATKER_ADDRESS', 'Jalan ABC No. 123', 1, 'string. nama satker'),
	(6, 'DIALOGWA_API_URL', 'https://dialogwa.id/api', 2, 'string. url api dialogwa.id'),
	(7, 'DIALOGWA_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY1ZjNiMjIyZWY1MmJjMzc4MDYxM2U1OSIsInVzZXJuYW1lIjoiY2hhbmRyYSIsImlhdCI6MTcxNzc0Nzc4NywiZXhwIjo0ODczNTA3Nzg3fQ.KIqEs7rELJzVj2hk6WJqCiYy0T0Mz7G5vbiy4gFLRQ0', 2, 'string. token dialogwa.id'),
	(8, 'DIALOGWA_SESSION', 'demo', 2, 'string. sesi online dialogwa.id'),
	(9, 'WA_TEST_TARGET_ETAMU', NULL, 2, 'string. nomor WA untuk tes penerima notifikasi'),
	(10, 'SEND_NOTIFICATION', '1', NULL, 'tinyint. 0: tidak kirim notifikasi, 1: kirim notifikasi ');
/*!40000 ALTER TABLE `configs` ENABLE KEYS */;

-- Dumping structure for table tamuku.guests
CREATE TABLE IF NOT EXISTS `guests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_to_meet` int(11) NOT NULL,
  `visit_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(100) NOT NULL,
  `gender` char(1) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `organization` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `message` text,
  `guest_count` tinyint(1) NOT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '0 : tidak bertemu, 1 : bertemu',
  `photo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_guests_persons` (`person_to_meet`),
  CONSTRAINT `FK_guests_persons` FOREIGN KEY (`person_to_meet`) REFERENCES `persons` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table tamuku.guests: ~0 rows (approximately)
DELETE FROM `guests`;
/*!40000 ALTER TABLE `guests` DISABLE KEYS */;
/*!40000 ALTER TABLE `guests` ENABLE KEYS */;

-- Dumping structure for table tamuku.persons
CREATE TABLE IF NOT EXISTS `persons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` tinyint(2) NOT NULL DEFAULT '0',
  `person` varchar(100) NOT NULL COMMENT 'yang dituju, bisa jabatan atau nama',
  `gender` enum('L','P') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table tamuku.persons: ~0 rows (approximately)
DELETE FROM `persons`;
/*!40000 ALTER TABLE `persons` DISABLE KEYS */;
INSERT INTO `persons` (`id`, `order`, `person`, `gender`, `phone`) VALUES
	(1, 1, 'Kepala Kemenag', 'L', '089122345678'),
	(2, 2, 'Kepala Sub Bagian Tata Usaha', 'P', '0811111111'),
	(3, 10, 'Fulanah binti Fulan', 'P', '08222222221');
/*!40000 ALTER TABLE `persons` ENABLE KEYS */;

-- Dumping structure for table tamuku.trans_message_whatsapp
CREATE TABLE IF NOT EXISTS `trans_message_whatsapp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sent_time` datetime NOT NULL,
  `sent_by` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `type` varchar(50) NOT NULL,
  `callback` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `note` varchar(100) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table tamuku.trans_message_whatsapp: ~0 rows (approximately)
DELETE FROM `trans_message_whatsapp`;
/*!40000 ALTER TABLE `trans_message_whatsapp` DISABLE KEYS */;
/*!40000 ALTER TABLE `trans_message_whatsapp` ENABLE KEYS */;

-- Dumping structure for table tamuku.tref_menu
CREATE TABLE IF NOT EXISTS `tref_menu` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  `parent` tinyint(2) DEFAULT NULL,
  `icon` varchar(25) DEFAULT NULL,
  `iconClass` varchar(25) DEFAULT NULL,
  `menuClass` varchar(25) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `order` tinyint(2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table tamuku.tref_menu: ~3 rows (approximately)
DELETE FROM `tref_menu`;
/*!40000 ALTER TABLE `tref_menu` DISABLE KEYS */;
INSERT INTO `tref_menu` (`id`, `label`, `parent`, `icon`, `iconClass`, `menuClass`, `url`, `order`, `status`) VALUES
	(1, 'Form Tamu', NULL, 'pencil-square-o', NULL, NULL, 'etamu', 1, 1),
	(2, 'Riwayat Tamu', NULL, 'address-book', NULL, NULL, 'etamu/history', 2, 1),
	(3, 'Riwayat Notifikasi', NULL, 'whatsapp', NULL, NULL, 'whatsapp', 3, 1),
	(4, 'Daftar Pejabat', NULL, 'users', NULL, NULL, 'etamu/person', 4, 1);
/*!40000 ALTER TABLE `tref_menu` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
