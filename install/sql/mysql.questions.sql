
DROP TABLE IF EXISTS `questions`;

CREATE TABLE `questions` (
  `id` mediumint(32) NOT NULL AUTO_INCREMENT,
  `session` varchar(128) NOT NULL DEFAULT '',
  `callback` varchar(300) NOT NULL DEFAULT '',
  `ipv4` int(4) NOT NULL DEFAULT '0',
  `ipv6` int(4) NOT NULL DEFAULT '0',
  `netbios` int(4) NOT NULL DEFAULT '0',
  `methods`  tinytext,
  `created` int(13) NOT NULL DEFAULT '0',
  `finish` int(13) NOT NULL DEFAULT '0',
  `called` int(13) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
