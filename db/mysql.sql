
#ALTER TABLE `prefix_monit_school_textbook` ADD COLUMN `discmiid` integer unsigned NOT NULL default 0 AFTER `discegeid`;
#ALTER TABLE `prefix_monit_school_gia_dates` ADD COLUMN `discmiid` integer unsigned NOT NULL default 0 AFTER `discegeid`;
#ALTER TABLE `prefix_monit_staff` ADD COLUMN `listmiids` VARCHAR(255) NOT NULL AFTER `listegeids`;
#ALTER TABLE `prefix_monit_school_pupil_card` ADD COLUMN `listmiids` VARCHAR(255) NOT NULL DEFAULT '0' AFTER `listegeids`;
#ALTER TABLE `prefix_monit_school_class` ADD COLUMN `listmiids` VARCHAR(255) NOT NULL DEFAULT '0' AFTER `timeadded`;

DROP TABLE IF EXISTS `prefix_monit_school_discipline_mi`;
CREATE TABLE `prefix_monit_school_discipline_mi` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `yearid` int(10) unsigned NOT NULL default '0',
  `codepredmet` int(10) unsigned NOT NULL default '0',
  `parallelnum` tinyint unsigned NOT NULL default '0',
  `name` varchar(30) NOT NULL,
  `textbookcatids` varchar(45) default '',
  `timepublish` int(10) unsigned NOT NULL default '0',
  `timeload` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `prefix_monit_school_umk`;
CREATE TABLE `prefix_monit_school_umk` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `yearid` int(10) unsigned NOT NULL default '0',
  `schoolid` int(10) unsigned NOT NULL default '0',
  `classid` int(10) unsigned NOT NULL default '0',
  `discmiid` int(10) unsigned NOT NULL default '0',
  `hours` tinyint default '0',
  `leveledu` tinyint default '0',
  `textbookid` int(10) unsigned default '0',  
  PRIMARY KEY  (`id`),
  KEY `discmiid` (`discmiid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prefix_monit_mi_results`;
CREATE TABLE `prefix_monit_mi_results` (
  `id` int(10) NOT NULL auto_increment,
  `yearid` int(10) unsigned NOT NULL default '0',
  `rayonid` int(10) unsigned NOT NULL default '0',
  `schoolid` int(10) unsigned NOT NULL default '0',
  `classid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `pp` varchar(10) NOT NULL default '',
  `audit` int(11) NOT NULL default '0',
  `codepredmet` int(11) NOT NULL default '0',
  `variant` int(11) NOT NULL default '0',
  `sidea` varchar(255) NOT NULL default '',
  `sideb` varchar(255) NOT NULL default '',
  `sidec` varchar(255) NOT NULL default '',
  `ball` int(11) NOT NULL default '0',
  `ocenka` int(11) NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '1234567890',
  PRIMARY KEY  (`id`),
  KEY `schoolid` (`schoolid`),
  KEY `classid` (`classid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

