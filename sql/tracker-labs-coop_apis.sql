CREATE DATABASE  IF NOT EXISTS `tracker-labs-coop` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `tracker-labs-coop`;
-- MySQL dump 10.13  Distrib 5.6.27, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tracker-labs-coop
-- ------------------------------------------------------
-- Server version	5.6.27-0ubuntu1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `apis`
--

DROP TABLE IF EXISTS `apis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apis` (
  `id` varchar(32) NOT NULL,
  `api-url` varchar(200) NOT NULL,
  `polinating` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `major` tinyint(4) NOT NULL DEFAULT '1',
  `minor` tinyint(4) NOT NULL DEFAULT '0',
  `revision` tinyint(4) NOT NULL DEFAULT '1',
  `callback` varchar(200) NOT NULL,
  `tracker` varchar(200) NOT NULL,
  `secret` tinytext NOT NULL,
  `agentid` varchar(32) NOT NULL DEFAULT '',
  `calls-recieved` mediumint(24) NOT NULL DEFAULT '0',
  `calls-sent` mediumint(24) NOT NULL DEFAULT '0',
  `kbytes-recieved` mediumint(24) NOT NULL DEFAULT '0',
  `kbytes-sent` mediumint(24) NOT NULL DEFAULT '0',
  `torrents-recieved` mediumint(24) NOT NULL DEFAULT '0',
  `torrents-sent` mediumint(24) NOT NULL DEFAULT '0',
  `peers-recieved` mediumint(24) NOT NULL DEFAULT '0',
  `peers-sent` mediumint(24) NOT NULL DEFAULT '0',
  `questions` mediumint(24) NOT NULL DEFAULT '0',
  `failures` mediumint(24) NOT NULL DEFAULT '0',
  `heard` int(12) NOT NULL DEFAULT '0',
  `down` int(12) NOT NULL DEFAULT '0',
  `failed` int(12) NOT NULL DEFAULT '0',
  `created` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`api-url`,`polinating`,`callback`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-01-15  5:32:15
