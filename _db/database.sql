-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.25 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             12.5.0.6677
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table whatsphant.chat
CREATE TABLE IF NOT EXISTS `chat` (
  `idChat` int(11) NOT NULL AUTO_INCREMENT,
  `FK_idUser1` int(11) DEFAULT NULL,
  `FK_idUser2` int(11) DEFAULT NULL,
  `FK_idLastMessage` int(11) DEFAULT NULL,
  PRIMARY KEY (`idChat`),
  KEY `FK_idUser1` (`FK_idUser1`),
  KEY `FK_idUser2` (`FK_idUser2`),
  KEY `FK_idLastMessage` (`FK_idLastMessage`),
  CONSTRAINT `FK_idLastMessage` FOREIGN KEY (`FK_idLastMessage`) REFERENCES `message` (`idMessage`),
  CONSTRAINT `FK_idUser1` FOREIGN KEY (`FK_idUser1`) REFERENCES `user` (`idUser`),
  CONSTRAINT `FK_idUser2` FOREIGN KEY (`FK_idUser2`) REFERENCES `user` (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table whatsphant.chat: ~0 rows (approximately)

-- Dumping structure for table whatsphant.message
CREATE TABLE IF NOT EXISTS `message` (
  `idMessage` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL,
  `FK_idChat` int(11) DEFAULT NULL,
  `FK_idUser` int(11) DEFAULT NULL,
  `isActive` tinyint(3) unsigned DEFAULT '1',
  `updatedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createdOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idMessage`),
  KEY `FK_idChat` (`FK_idChat`),
  KEY `FK_idUser` (`FK_idUser`),
  CONSTRAINT `FK_idChat` FOREIGN KEY (`FK_idChat`) REFERENCES `chat` (`idChat`),
  CONSTRAINT `FK_idUser` FOREIGN KEY (`FK_idUser`) REFERENCES `user` (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table whatsphant.message: ~0 rows (approximately)

-- Dumping structure for table whatsphant.user
CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `issuedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expirationTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table whatsphant.user: ~1 rows (approximately)
INSERT INTO `user` (`idUser`, `email`, `name`, `password`, `issuedAt`, `expirationTime`, `isAdmin`, `isActive`) VALUES
	(1, 'admin', 'admin', '0c7540eb7e65b553ec1ba6b20de79608', '2023-08-28 20:32:44', '2023-08-28 20:52:44', 1, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
