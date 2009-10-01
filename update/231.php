<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/231.php
Purpose: Update page to 2.4.0
Last Modified: 2008-04-24 1255 EST
**/

/* add the site options preferences */
mysql_query( "ALTER TABLE `sms_crew` ADD `cpShowPosts` ENUM('y','n') NOT NULL DEFAULT 'y'" );
mysql_query( "ALTER TABLE `sms_crew` ADD `cpShowPostsNum` INT(3) NOT NULL DEFAULT '2'" );
mysql_query( "ALTER TABLE `sms_crew` ADD `cpShowLogs` ENUM('y','n') NOT NULL DEFAULT 'y'" );
mysql_query( "ALTER TABLE `sms_crew` ADD `cpShowLogsNum` INT(3) NOT NULL DEFAULT '2'" );
mysql_query( "ALTER TABLE `sms_crew` ADD `cpShowNews` ENUM('y','n') NOT NULL DEFAULT 'y'" );
mysql_query( "ALTER TABLE `sms_crew` ADD `cpShowNewsNum` INT(3) NOT NULL DEFAULT '2'" );

/* add the option to use mission notes or not */
mysql_query( "ALTER TABLE `sms_globals` ADD `useMissionNotes` ENUM('y','n') NOT NULL DEFAULT 'y'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `xoCrewApps` ENUM('y','n') NOT NULL DEFAULT 'n'" );

/* add the mission notes field in the misssions table */
mysql_query( "ALTER TABLE `sms_missions` ADD `missionNotes` TEXT NOT NULL" );

/* add the summary field in the tour table */
mysql_query( "ALTER TABLE `sms_tour` ADD `tourSummary` TEXT NOT NULL" );
mysql_query( "ALTER TABLE `sms_tour` ADD `tourPicture3` varchar(255) NOT NULL DEFAULT ''" );

/* create the tour decks table */
mysql_query( "CREATE TABLE `sms_tour_decks` (
  `deckid` int(4) NOT NULL auto_increment,
  `deckContent` text NOT NULL,
  PRIMARY KEY  (`deckid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;" );

/* create the private messages table */
mysql_query( "CREATE TABLE `sms_privatemessages` (
  `pmid` int(5) NOT NULL auto_increment,
  `pmRecipient` int(3) NOT NULL DEFAULT '0',
  `pmAuthor` int(3) NOT NULL DEFAULT '0',
  `pmSubject` varchar(100) NOT NULL default '',
	`pmContent` text NOT NULL,
	`pmDate` datetime NOT NULL default '0000-00-00 00:00:00',
	`pmStatus` enum( 'read','unread' ) default 'unread',
	`pmAuthorDisplay` enum( 'y','n' ) default 'y',
	`pmRecipientDisplay` enum( 'y','n' ) default 'y',
  PRIMARY KEY  (`pmid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;" );

?>