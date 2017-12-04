
DROP TABLE IF EXISTS `results`;

CREATE TABLE `results` (
  `id` mediumint(128) NOT NULL AUTO_INCREMENT,
  `question-id`  mediumint(32) NOT NULL DEFAULT '0', 
  `query-id`  mediumint(64) NOT NULL DEFAULT '0',
  `method` ENUM('country','city','geoip','geocity','geonetspeed','geonetspeedcell','geoorg','geoisp','georegion','other') NOT NULL DEFAULT 'other',
  `value` mediumtext,
  `created` int(13) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
