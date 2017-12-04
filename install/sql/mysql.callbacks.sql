

DROP TABLE IF EXISTS `callbacks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callbacks` (
  `id` mediumint(200) NOT NULL AUTO_INCREMENT,
  `uri` varchar(300) NOT NULL DEFAULT '',
  `timeout` int(4) NOT NULL DEFAULT '0',
  `connection` int(4) NOT NULL DEFAULT '0',
  `data` mediumtext NOT NULL,
  `queries` mediumtext NOT NULL,
  `fails` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
