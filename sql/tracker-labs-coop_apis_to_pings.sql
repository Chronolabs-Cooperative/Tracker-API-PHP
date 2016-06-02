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
-- Table structure for table `apis_to_pings`
--

DROP TABLE IF EXISTS `apis_to_pings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apis_to_pings` (
  `source-apiid` varchar(32) NOT NULL,
  `target-apiid` varchar(32) NOT NULL,
  `time-zone` varchar(100) NOT NULL,
  `recieved` float(24,14) NOT NULL DEFAULT '0.00000000000000',
  `sent` float(24,14) NOT NULL DEFAULT '0.00000000000000',
  `delay` float(24,14) NOT NULL DEFAULT '0.00000000000000',
  `average` float(24,14) NOT NULL DEFAULT '0.00000000000000',
  `test` tinyint(4) NOT NULL,
  `tests` tinyint(4) NOT NULL,
  `year` tinyint(4) NOT NULL,
  `month` tinyint(2) NOT NULL,
  `day` tinyint(2) NOT NULL,
  `hour` tinyint(2) NOT NULL,
  `minute` tinyint(2) NOT NULL,
  `second` tinyint(2) NOT NULL,
  `stamp` float(24,14) NOT NULL DEFAULT '0.00000000000000',
  `delete` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`source-apiid`,`target-apiid`,`test`,`tests`,`month`,`year`,`day`,`hour`,`minute`,`second`,`time-zone`)
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

-- Dump completed on 2016-01-15  5:32:19
