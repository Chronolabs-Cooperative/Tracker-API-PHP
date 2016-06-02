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
-- Table structure for table `peers`
--

DROP TABLE IF EXISTS `peers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peers` (
  `id` bigint(60) unsigned zerofill NOT NULL,
  `torrentid` bigint(30) unsigned zerofill NOT NULL DEFAULT '000000000000000000000000000000',
  `trackerid` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
  `apiid` varchar(32) NOT NULL DEFAULT '',
  `ipid` varchar(32) NOT NULL DEFAULT '',
  `agentid` varchar(32) NOT NULL DEFAULT '',
  `peerhash` varchar(44) NOT NULL DEFAULT '',
  `peerid` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `port` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uploaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `downloaded` bigint(20) unsigned NOT NULL DEFAULT '0',
  `left` bigint(20) unsigned NOT NULL DEFAULT '0',
  `numwant` bigint(20) unsigned NOT NULL DEFAULT '0',
  `key` varchar(20) NOT NULL DEFAULT '',
  `compact` enum('yes','no') NOT NULL DEFAULT 'yes',
  `supportcrypto` enum('yes','no') NOT NULL DEFAULT 'yes',
  `event` varchar(30) NOT NULL DEFAULT '',
  `seeder` enum('yes','no') NOT NULL DEFAULT 'no',
  `started` int(12) NOT NULL,
  `lastaction` int(12) NOT NULL,
  `connectable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `finished` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `torrent_peer_id` (`torrentid`,`peerid`),
  KEY `torrent` (`torrentid`),
  KEY `torrent_seeder` (`torrentid`,`seeder`),
  KEY `last_action` (`lastaction`),
  KEY `connectable` (`connectable`),
  KEY `torrent_connect` (`torrentid`,`connectable`)
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

-- Dump completed on 2016-01-15  5:32:18
