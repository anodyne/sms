<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: install/resource_structure.php
Purpose: Installation resource file with the database structure

System Version: 2.6.10
Last Modified: 2009-09-08 0804 EST
**/

/* query the database for the mysql version */
$t = mysql_query("select version() as ve");
echo mysql_error();
$r = mysql_fetch_object( $t );

/* if the server is running mysql 4 and higher, set the default character set */
if( $r->ve >= 4 ) {
	$tail = "CHARACTER SET utf8";
} else {
	$tail = "";
}

/* create the access levels table */
mysql_query( "CREATE TABLE `sms_accesslevels` (
  `id` tinyint(1) NOT NULL auto_increment,
  `post` text NOT NULL,
  `manage` text NOT NULL,
  `reports` text NOT NULL,
  `user` text NOT NULL,
  `other` text NOT NULL,
  PRIMARY KEY  (`id`)
) " . $tail . " ;" );

/* create the awards table */
mysql_query( "CREATE TABLE `sms_awards` (
  `awardid` int(4) NOT NULL auto_increment,
  `awardName` varchar(100) NOT NULL default '',
  `awardImage` varchar(50) NOT NULL default '',
  `awardOrder` int(3) NOT NULL default '0',
  `awardDesc` text NOT NULL,
  `awardCat` enum('ic','ooc','both') NOT NULL default 'both',
  PRIMARY KEY  (`awardid`)
) " . $tail . " ;" );

/* create the awards queue table */
mysql_query( "CREATE TABLE `sms_awards_queue` (
  `id` int(6) NOT NULL auto_increment,
  `crew` int(6) NOT NULL default '0',
  `nominated` int(6) NOT NULL default '0',
  `award` int(6) NOT NULL default '0',
  `reason` text NOT NULL,
  `status` enum('accepted','pending','rejected') NOT NULL default 'pending',
  PRIMARY KEY  (`id`)
) " . $tail . " ;" );

/* create the coc table */
mysql_query( "CREATE TABLE `sms_coc` (
  `cocid` int(1) NOT NULL auto_increment,
  `crewid` int(3) NOT NULL default '0',
  PRIMARY KEY  (`cocid`)
) " . $tail . " AUTO_INCREMENT=2 ;" );

/* create the crew table */
mysql_query( "CREATE TABLE `sms_crew` (
  `crewid` int(4) NOT NULL auto_increment,
  `username` varchar(16) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `crewType` enum('active','inactive','pending','npc') NOT NULL default 'active',
  `email` varchar(64) NOT NULL default '',
  `realName` varchar(32) NOT NULL default '',
  `displaySkin` varchar(32) NOT NULL default 'default',
  `displayRank` varchar(50) NOT NULL default 'default',
  `positionid` int(3) NOT NULL default '0',
  `positionid2` int(3) NOT NULL default '0',
  `rankid` int(3) NOT NULL default '0',
  `firstName` varchar(32) NOT NULL default '',
  `middleName` varchar(32) NOT NULL default '',
  `lastName` varchar(32) NOT NULL default '',
  `gender` enum('Male','Female','Hermaphrodite','Neuter') NOT NULL default 'Male',
  `species` varchar(32) NOT NULL default '',
  `aim` varchar(50) NOT NULL default '',
  `yim` varchar(50) NOT NULL default '',
  `msn` varchar(50) NOT NULL default '',
  `icq` varchar(50) NOT NULL default '',
  `heightFeet` int(2) NOT NULL default '0',
  `heightInches` int(2) NOT NULL default '0',
  `weight` int(4) NOT NULL default '0',
  `eyeColor` varchar(25) NOT NULL default '',
  `hairColor` varchar(25) NOT NULL default '',
  `age` int(4) NOT NULL default '0',
  `physicalDesc` text NOT NULL,
  `history` text NOT NULL,
  `personalityOverview` text NOT NULL,
  `strengths` text NOT NULL,
  `ambitions` text NOT NULL,
  `hobbies` text NOT NULL,
  `languages` varchar(100) NOT NULL default '',
  `serviceRecord` text NOT NULL,
  `father` varchar(100) NOT NULL default '',
  `mother` varchar(100) NOT NULL default '',
  `brothers` text NOT NULL,
  `sisters` text NOT NULL,
  `spouse` varchar(100) NOT NULL default '',
  `children` text NOT NULL,
  `otherFamily` text NOT NULL,
  `awards` text NOT NULL,
  `image` text NOT NULL,
  `contactInfo` enum('y','n') NOT NULL default 'y',
  `emailPosts` enum('y','n') NOT NULL default 'y',
  `emailLogs` enum('y','n') NOT NULL default 'y',
  `emailNews` enum('y','n') NOT NULL default 'y',
  `moderatePosts` enum('y','n') NOT NULL default 'n',
  `moderateLogs` enum('y','n') NOT NULL default 'n',
  `moderateNews` enum('y','n') NOT NULL default 'n',
  `cpShowPosts` enum('y','n') not null default 'y',
  `cpShowPostsNum` int(3) not null default '2',
  `cpShowLogs` enum('y','n') not null default 'y',
  `cpShowLogsNum` int(3) not null default '2',
  `cpShowNews` enum('y','n') not null default 'y',
  `cpShowNewsNum` int(3) not null default '2',
  `loa` enum('0','1','2') NOT NULL default '0',
  `strikes` int(1) NOT NULL default '0',
  `joinDate` varchar(50) NOT NULL default '',
  `leaveDate` varchar(50) NOT NULL default '',
  `lastLogin` varchar(50) NOT NULL default '',
  `lastPost` varchar(50) NOT NULL default '',
  `accessPost` text NOT NULL,
  `accessManage` text NOT NULL,
  `accessReports` text NOT NULL,
  `accessUser` text NOT NULL,
  `accessOthers` text NOT NULL,
  `menu1` varchar(8) NOT NULL DEFAULT '57',
  `menu2` varchar(8) NOT NULL DEFAULT '0',
  `menu3` varchar(8) NOT NULL DEFAULT '0',
  `menu4` varchar(8) NOT NULL DEFAULT '0',
  `menu5` varchar(8) NOT NULL DEFAULT '0',
  `menu6` varchar(8) NOT NULL DEFAULT '0',
  `menu7` varchar(8) NOT NULL DEFAULT '0',
  `menu8` varchar(8) NOT NULL DEFAULT '0',
  `menu9` varchar(8) NOT NULL DEFAULT '0',
  `menu10` varchar(8) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`crewid`)
) " . $tail . " ;" );

/* create the database table */
mysql_query( "CREATE TABLE `sms_database` (
	`dbid` int(4) NOT NULL auto_increment,
	`dbTitle` varchar(200) NOT NULL default '',
	`dbDesc` text NOT NULL,
	`dbContent` text NOT NULL,
	`dbType` enum('onsite','offsite','entry') NOT NULL default 'entry',
	`dbURL` varchar(255) NOT NULL default '',
	`dbOrder` int(4) NOT NULL default '0',
	`dbDisplay` enum('y','n') NOT NULL default 'y',
	`dbDept` int(4) NOT NULL default '0',
	PRIMARY KEY  (`dbid`)
	) " . $tail . " ;" );
	
/* create the department table */
mysql_query( "CREATE TABLE `sms_departments` (
  `deptid` int(3) NOT NULL auto_increment,
  `deptOrder` int(3) NOT NULL default '0',
  `deptClass` int(3) NOT NULL default '0',
  `deptName` varchar(32) NOT NULL default '',
  `deptDesc` text NOT NULL,
  `deptDisplay` enum('y','n') NOT NULL default 'y',
  `deptColor` varchar(6) NOT NULL default '',
  `deptType` enum('playing','nonplaying') not null default 'playing',
  `deptDatabaseUse` enum('y','n') NOT NULL default 'y',
  PRIMARY KEY  (`deptid`)
) " . $tail . " AUTO_INCREMENT=14 ;" );

/* create the globals table */
mysql_query( "CREATE TABLE `sms_globals` (
  `globalid` int(1) NOT NULL default '0',
  `shipPrefix` varchar(10) NOT NULL default '',
  `shipName` varchar(32) NOT NULL default '',
  `shipRegistry` varchar(16) NOT NULL default '',
  `skin` varchar(16) NOT NULL default '',
  `allowedSkins` text NOT NULL,
  `allowedRanks` text NOT NULL,
  `fleet` varchar(64) NOT NULL default '',
  `fleetURL` varchar(128) NOT NULL default '',
  `tfMember` enum('y','n') NOT NULL default 'y',
  `tfName` varchar(64) NOT NULL default '',
  `tfURL` varchar(128) NOT NULL default '',
  `tgMember` enum('y','n') NOT NULL default 'y',
  `tgName` varchar(64) NOT NULL default '',
  `tgURL` varchar(128) NOT NULL default '',
  `hasWebmaster` enum('y','n') NOT NULL default 'y',
  `webmasterName` varchar(128) NOT NULL default '',
  `webmasterEmail` varchar(64) NOT NULL default '',
  `showNews` enum('y','n') NOT NULL default 'y',
  `showNewsNum` int(2) NOT NULL default '5',
  `simmYear` varchar(4) NOT NULL default '2383',
  `rankSet` varchar(50) NOT NULL default 'default',
  `simmType` enum('ship','starbase') NOT NULL default 'ship',
  `postCountDefault` int(3) NOT NULL default '14',
  `manifest_defaults` text NOT NULL,
  `useSamplePost` enum('y','n') NOT NULL default 'y',
  `logList` int(4) NOT NULL default '25',
  `bioShowPosts` enum('y','n') NOT NULL default 'y',
  `bioShowLogs` enum('y','n') NOT NULL default 'y',
  `bioShowPostsNum` int(2) NOT NULL default '5',
  `bioShowLogsNum` int(2) NOT NULL default '5',
  `showInfoMission` enum('y','n') NOT NULL default 'y',
  `showInfoPosts` enum('y','n') NOT NULL default 'y',
  `showInfoPositions` enum('y','n') NOT NULL default 'y',
  `jpCount` enum('y','n') NOT NULL default 'y',
  `usePosting` enum('y','n') NOT NULL default 'y',
  `useMissionNotes` enum('y','n') NOT NULL default 'y',
  `updateNotify` enum('all','major','none') NOT NULL default 'all',
  `emailSubject` varchar(75) NOT NULL default '',
  `stardateDisplaySD` enum('y','n') NOT NULL default 'y',
  `stardateDisplayDate` enum('y','n') NOT NULL default 'y',
  PRIMARY KEY  (`globalid`)
) " . $tail . " ;" );

/* create the menu items table */
mysql_query( "CREATE TABLE `sms_menu_items` (
	`menuid` int(4) NOT NULL auto_increment,
	`menuGroup` int(3) NOT NULL,
	`menuOrder` int(3) NOT NULL,
	`menuTitle` varchar(200) NOT NULL,
	`menuLinkType` enum('onsite','offsite') NOT NULL default 'onsite',
	`menuLink` varchar(255) NOT NULL,
	`menuAccess` varchar(50) NOT NULL,
	`menuMainSec` varchar(200) NOT NULL,
	`menuLogin` enum('y','n') NOT NULL default 'n',
	`menuCat` enum('main','general','admin') NOT NULL default 'general',
	`menuAvailability` enum('on','off') NOT NULL default 'on',
	PRIMARY KEY  (`menuid`)
) " . $tail . " AUTO_INCREMENT=88;" );

/* create the messages table */
mysql_query( "CREATE TABLE `sms_messages` (
  `messageid` int(2) NOT NULL auto_increment,
  `welcomeMessage` text NOT NULL,
  `simmMessage` text NOT NULL,
  `shipMessage` text NOT NULL,
  `shipHistory` text NOT NULL,
  `cpMessage` text NOT NULL,
  `joinDisclaimer` text NOT NULL,
  `samplePostQuestion` text NOT NULL,
  `rules` text NOT NULL,
  `acceptMessage` text NOT NULL,
  `rejectMessage` text NOT NULL,
  `siteCreditsPermanent` text not null,
  `siteCredits` text not null,
  PRIMARY KEY  (`messageid`)
) " . $tail . " ;" );

/* create the missions table */
mysql_query( "CREATE TABLE `sms_missions` (
  `missionid` int(3) NOT NULL auto_increment,
  `missionOrder` int(3) NOT NULL default '0',
  `missionTitle` varchar(100) NOT NULL default '',
  `missionDesc` text NOT NULL,
  `missionSummary` text NOT NULL,
  `missionStatus` enum('current','upcoming','completed') NOT NULL default 'upcoming',
  `missionStart` varchar(50) NOT NULL default '',
  `missionEnd` varchar(50) NOT NULL default '',
  `missionImage` varchar(50) NOT NULL default 'images/missionimages/',
  `missionNotes` text NOT NULL,
  PRIMARY KEY  (`missionid`)
) " . $tail . " ;" );

/* create the news table */
mysql_query( "CREATE TABLE `sms_news` (
  `newsid` int(4) NOT NULL auto_increment,
  `newsCat` int(3) NOT NULL default '1',
  `newsAuthor` int(3) NOT NULL default '0',
  `newsPosted` varchar(50) NOT NULL default '',
  `newsTitle` varchar(100) NOT NULL default '',
  `newsContent` text NOT NULL,
  `newsStatus` enum( 'pending','saved','activated' ) NOT NULL default 'activated',
  `newsPrivate` enum( 'y', 'n' ) NOT NULL default 'n',
  PRIMARY KEY  (`newsid`)
) " . $tail . " ;" );

/* create the news category table */
mysql_query( "CREATE TABLE `sms_news_categories` (
  `catid` int(3) NOT NULL auto_increment,
  `catName` varchar(50) NOT NULL default '',
  `catUserLevel` int(2) NOT NULL default '0',
  `catVisible` enum('y','n') NOT NULL default 'y',
  PRIMARY KEY  (`catid`)
) " . $tail . " AUTO_INCREMENT=5 ;" );

/* create the personal logs table */
mysql_query( "CREATE TABLE `sms_personallogs` (
  `logid` int(4) NOT NULL auto_increment,
  `logAuthor` int(3) NOT NULL default '0',
  `logTitle` varchar(100) NOT NULL default '',
  `logContent` text NOT NULL,
  `logPosted` varchar(50) NOT NULL default '',
  `logStatus` enum( 'pending','saved','activated' ) NOT NULL default 'activated',
  PRIMARY KEY  (`logid`)
) " . $tail . " ;" );

/* create the positions table */
mysql_query( "CREATE TABLE `sms_positions` (
  `positionid` int(3) NOT NULL auto_increment,
  `positionOrder` int(3) NOT NULL default '0',
  `positionName` varchar(64) NOT NULL default '',
  `positionDesc` text NOT NULL,
  `positionDept` int(3) NOT NULL default '0',
  `positionType` enum( 'senior', 'crew' ) NOT NULL default 'crew',
  `positionOpen` int(2) NOT NULL default '1',
  `positionDisplay` enum('y','n') NOT NULL default 'y',
  `positionMainPage` enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (`positionid`)
) " . $tail . " AUTO_INCREMENT=69 ;" );

/* create the post table */
mysql_query( "CREATE TABLE `sms_posts` (
  `postid` int(4) NOT NULL auto_increment,
  `postAuthor` text NOT NULL,
  `postTitle` varchar(100) NOT NULL default '',
  `postLocation` varchar(100) NOT NULL default '',
  `postTimeline` varchar(100) NOT NULL default '',
  `postTag` varchar(255) NOT NULL default '',
  `postContent` text NOT NULL,
  `postPosted` varchar(50) NOT NULL default '',
  `postMission` int(3) NOT NULL default '0',
  `postStatus` enum( 'pending','saved','activated' ) NOT NULL default 'activated',
  `postSave` int(4) NOT NULL default '0',
  PRIMARY KEY  (`postid`)
) " . $tail . " ;" );

/* create the private messages table */
mysql_query( "CREATE TABLE `sms_privatemessages` (
	`pmid` int(5) NOT NULL auto_increment,
	`pmRecipient` int(3) NOT NULL DEFAULT '0',
	`pmAuthor` int(3) NOT NULL DEFAULT '0',
	`pmSubject` varchar(100) NOT NULL default '',
	`pmContent` text NOT NULL,
	`pmDate` varchar(50) NOT NULL default '',
	`pmStatus` enum( 'read','unread' ) default 'unread',
	`pmAuthorDisplay` enum( 'y','n' ) default 'y',
	`pmRecipientDisplay` enum( 'y','n' ) default 'y',
  PRIMARY KEY  (`pmid`)
) " . $tail . " ;" );

/* create the ranks table */
mysql_query( "CREATE TABLE `sms_ranks` (
  `rankid` int(3) NOT NULL auto_increment,
  `rankOrder` int(2) NOT NULL default '0',
  `rankName` varchar(32) NOT NULL default '',
  `rankShortName` varchar(32) NOT NULL default '',
  `rankImage` varchar(255) NOT NULL default '',
  `rankType` int(1) NOT NULL default '1',
  `rankDisplay` enum('y','n') NOT NULL default 'y',
  `rankClass` int(3) NOT NULL default '0',
  PRIMARY KEY  (`rankid`)
) " . $tail . " AUTO_INCREMENT=213 ;" );

/* create the specs table */
mysql_query( "CREATE TABLE `sms_specs` (
  `specid` int(1) NOT NULL default '1',
  `shipClass` varchar(50) NOT NULL default '',
  `shipRole` varchar(80) NOT NULL default '',
  `duration` int(3) NOT NULL default '0',
  `durationUnit` varchar(16) NOT NULL default 'Years',
  `refit` int(3) NOT NULL default '0',
  `refitUnit` varchar(16) NOT NULL default 'Years',
  `resupply` int(3) NOT NULL default '0',
  `resupplyUnit` varchar(16) NOT NULL default 'Years',
  `length` int(5) NOT NULL default '0',
  `height` int(5) NOT NULL default '0',
  `width` int(5) NOT NULL default '0',
  `decks` int(5) NOT NULL default '0',
  `complimentEmergency` varchar(20) NOT NULL default '',
  `complimentOfficers` varchar(20) NOT NULL default '',
  `complimentEnlisted` varchar(20) NOT NULL default '',
  `complimentMarines` varchar(20) NOT NULL default '',
  `complimentCivilians` varchar(20) NOT NULL default '',
  `warpCruise` varchar(8) NOT NULL default '',
  `warpMaxCruise` varchar(8) NOT NULL default '',
  `warpEmergency` varchar(8) NOT NULL default '',
  `warpMaxTime` varchar(20) NOT NULL default '',
  `warpEmergencyTime` varchar(20) NOT NULL default '',
  `phasers` text NOT NULL,
  `torpedoLaunchers` text NOT NULL,
  `torpedoCompliment` text NOT NULL,
  `defensive` text NOT NULL,
  `shields` text NOT NULL,
  `shuttlebays` int(3) NOT NULL default '0',
  `hasShuttles` enum('y','n') NOT NULL default 'y',
  `hasRunabouts` enum('y','n') NOT NULL default 'y',
  `hasFighters` enum('y','n') NOT NULL default 'y',
  `hasTransports` enum('y','n') NOT NULL default 'y',
  `shuttles` text NOT NULL,
  `runabouts` text NOT NULL,
  `fighters` text NOT NULL,
  `transports` text NOT NULL,
  PRIMARY KEY  (`specid`)
) " . $tail . " ;" );

/* create the starbase docking table */
mysql_query( "CREATE TABLE `sms_starbase_docking` (
  `dockid` int(5) NOT NULL auto_increment,
  `dockingShipName` varchar(100) NOT NULL default '',
  `dockingShipRegistry` varchar(32) NOT NULL default '',
  `dockingShipClass` varchar(50) NOT NULL default '',
  `dockingShipURL` varchar(128) NOT NULL default '',
  `dockingShipCO` varchar(128) NOT NULL default '',
  `dockingShipCOEmail` varchar(50) NOT NULL default '',
  `dockingDuration` varchar(50) NOT NULL default '',
  `dockingDesc` text NOT NULL,
  `dockingStatus` enum( 'pending','activated','departed' ) NOT NULL default 'activated',
  PRIMARY KEY  (`dockid`)
) " . $tail . " ;" );

/* create the strikes table */
mysql_query( "CREATE TABLE `sms_strikes` (
  `strikeid` int(4) NOT NULL auto_increment,
  `crewid` int(3) NOT NULL default '0',
  `strikeDate` varchar(50) NOT NULL default '',
  `reason` text NOT NULL,
  `number` int(3) NOT NULL default '0',
  PRIMARY KEY  (`strikeid`)
) " . $tail . " ;" );

/* add the system table */
mysql_query( "CREATE TABLE `sms_system` (
  `sysid` int(2) NOT NULL auto_increment,
  `sysuid` varchar(20) NOT NULL default '',
  `sysVersion` varchar(10) NOT NULL default '',
  `sysBaseVersion` varchar(10) NOT NULL default '',
  `sysIncrementVersion` varchar(10) NOT NULL default '',
  `sysLaunchStatus` enum('y','n') NOT NULL default 'n',
  PRIMARY KEY  (`sysid`)
) " . $tail . " ;" );

/* add the system plugins table */
mysql_query( "CREATE TABLE `sms_system_plugins` (
	`pid` int(4) NOT NULL auto_increment,
	`plugin` varchar(255) NOT NULL default '',
	`pluginVersion` varchar(15) NOT NULL default '',
	`pluginSite` varchar(200) NOT NULL default '',
	`pluginUse` text NOT NULL,
	`pluginFiles` text NOT NULL,
	PRIMARY KEY  (`pid`)
) " . $tail . " ;" );

/* add the system versions table */
mysql_query( "CREATE TABLE `sms_system_versions` (
	`versionid` int(3) NOT NULL auto_increment,
	`version` varchar(50) NOT NULL default '',
	`versionRev` int(5) NOT NULL default '0',
	`versionDate` varchar(50) NOT NULL default '',
	`versionShortDesc` text NOT NULL,
	`versionDesc` text NOT NULL,
	PRIMARY KEY  (`versionid`)
) " . $tail . " ;" );

/* add the tour table */
mysql_query( "CREATE TABLE `sms_tour` (
	`tourid` int( 4 ) NOT NULL auto_increment,
	`tourName` varchar( 100 ) NOT NULL default '',
	`tourLocation` varchar( 100 ) NOT NULL default '',
	`tourPicture1` varchar( 255 ) NOT NULL default '',
	`tourPicture2` varchar( 255 ) NOT NULL default '',
	`tourPicture3` varchar( 255 ) NOT NULL default '',
	`tourDesc` text NOT NULL,
	`tourSummary` text NOT NULL,
	`tourOrder` int( 4 ) NOT NULL default '0',
	`tourDisplay` enum( 'y','n' ) NOT NULL default 'y',
	PRIMARY KEY  (`tourid`)
) " . $tail . " ;" );

/* add the deck listing table */
mysql_query( "CREATE TABLE `sms_tour_decks` (
	`deckid` int(4) NOT NULL auto_increment,
	`deckContent` text,
  PRIMARY KEY  (`deckid`)
) " . $tail . " ;" );

?>