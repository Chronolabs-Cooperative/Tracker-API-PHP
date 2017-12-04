
# Table structure for table `uploads`
#

CREATE TABLE uploads (
  upid mediumint(255) unsigned NOT NULL auto_increment,
  uid mediumint(255) unsigned NOT NULL default '0',
  ip varchar(60) NOT NULL default '',
  referee varchar(128) NOT NULL default '',
  callback tinytext,
  filename varchar(60) NOT NULL default '',
  path varchar(255) NOT NULL default '',
  email varchar(60) NOT NULL default '',
  md5 varchar(32) NOT NULL default '',
  timezone varchar(150) NOT NULL default '',
  uploaded int(10) unsigned NOT NULL default '0',
  lastread int(10) unsigned NOT NULL default '0',
  oversize tinyint(1) unsigned NOT NULL default '0',
  active tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (upid),
  KEY md5 (md5),
  KEY activelastread (active,lastread),
  KEY uploadedlastread (uploaded,lastread)
) ENGINE=INNODB;
