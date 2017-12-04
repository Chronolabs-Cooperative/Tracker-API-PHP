CREATE TABLE `emails` (
  `emails-key` varchar(44) NOT NULL,
  `uri-key` varchar(44) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `name` varchar(198) DEFAULT '',
  PRIMARY KEY (`emails-keys`),
  KEY `SEARCHING` (`uri-key`(22),`email`(23),`name`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `salts` (
  `salt-key` varchar(44) NOT NULL,
  `uri-key` varchar(44) DEFAULT '',
  `email-key` varchar(44) DEFAULT '',
  `variable` varchar(128) DEFAULT '',
  `fingerprint` varchar(32) DEFAULT '',
  `salt` tinytext,
  `created` int(12) DEFAULT '0',
  `retrieved` int(12) DEFAULT '0',
  `retrieves` int(12) DEFAULT '0',
  PRIMARY KEY (`salt-key`),
  KEY `MATCHER` (`fingerprint`(23),`email-key`(22),`uri-key`(22))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `uri` (
  `uri-key` varchar(44) NOT NULL DEFAULT '',
  `encounted` int(12) DEFAULT '0',
  `protocol` enum('https://','http://','httpx://') DEFAULT 'http://',
  `domain` varchar(255) DEFAULT '',
  `base` varchar(128) DEFAULT '',
  `strata` varchar(30) DEFAULT '',
  `fallout` varchar(3) DEFAULT '',
  `path` varchar(255) DEFAULT '/',
  `retrieves` int(8) DEFAULT '0',
  `salts` int(8) DEFAULT '0',
  PRIMARY KEY (`uri-key`),
  KEY `SEARCH` (`encounted`,`protocol`,`domain`(19),`base`(10),`strata`(5),`fallout`,`path`(11))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;