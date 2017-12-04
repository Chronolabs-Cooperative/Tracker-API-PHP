
DROP TABLE IF EXISTS `queries`;

CREATE TABLE `queries` (
  `id` mediumint(64) NOT NULL AUTO_INCREMENT,
  `question-id`  mediumint(32) NOT NULL DEFAULT '0', 
  `type` ENUM('ipv4','ipv6','realm') NOT NULL DEFAULT 'ipv4',
  `value` varchar(196) NOT NULL DEFAULT '',
  `methods` tinytext,
  `todo` int(4) NOT NULL DEFAULT '0',
  `created` int(13) NOT NULL DEFAULT '0',
  `queried` int(13) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
