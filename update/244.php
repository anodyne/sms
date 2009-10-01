<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/244.php
Purpose: Update page to 2.5.0
Last Modified: 2008-04-24 1249 EST
**/

/* changes to the crew table */
mysql_query( "ALTER TABLE `sms_crew` ADD `accessPost` text not null" );
mysql_query( "ALTER TABLE `sms_crew` ADD `accessManage` text not null" );
mysql_query( "ALTER TABLE `sms_crew` ADD `accessReports` text not null" );
mysql_query( "ALTER TABLE `sms_crew` ADD `accessUser` text not null" );
mysql_query( "ALTER TABLE `sms_crew` ADD `accessOthers` text not null" );
mysql_query( "ALTER TABLE `sms_crew` CHANGE `leaveDate` `leaveDate` varchar(50) not null default ''" );
mysql_query( "ALTER TABLE `sms_crew` CHANGE `crewType` `crewType` varchar(50) not null default ''" );

/* update the crewType variable */
$getPlayers = "SELECT crewid, crewType, accessLevel FROM sms_crew WHERE crewType = 'player'";
$getPlayersResult = mysql_query( $getPlayers );

while( $getPlayersFetch = mysql_fetch_array( $getPlayersResult ) ) {
	extract( $getPlayersFetch, EXTR_OVERWRITE );
	
	if( $getPlayersFetch[2] >= 2 ) {
		$pType = "active";
	} elseif( $getPlayersFetch[2] == 1 ) {
		$pType = "inactive";
	} elseif( $getPlayersFetch[2] == 0 ) {
		$pType = "pending";
	}
	
	mysql_query( "UPDATE sms_crew SET crewType = '$pType' WHERE crewid = '$crewid' LIMIT 1" );
	
}

/* finally, update the crewType to its final form */
mysql_query( "ALTER TABLE `sms_crew` CHANGE `crewType` `crewType` enum( 'active', 'inactive', 'pending', 'npc' ) not null default 'active'" );

/* changes to the globals table */
mysql_query( "ALTER TABLE `sms_globals` ADD `showInfoMission` enum( 'y', 'n' ) not null default 'y'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `showInfoPosts` enum( 'y', 'n' ) not null default 'y'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `showInfoPositions` enum( 'y', 'n' ) not null default 'y'" );
mysql_query( "UPDATE sms_globals SET allowedSkins = 'default,cobalt,SMS_Lcars' WHERE globalid = '1'" );
mysql_query( "UPDATE sms_globals SET allowedRanks = 'default,dress' WHERE globalid = '1'" );
mysql_query( "UPDATE sms_globals SET skin = 'default' WHERE globalid = '1'" );
mysql_query( "UPDATE sms_globals SET rankSet = 'default' WHERE globalid = '1'" );

/* changes to the positions table */
mysql_query( "ALTER TABLE `sms_positions` ADD `positionMainPage` enum( 'y', 'n' ) not null default 'n'" );
mysql_query( "ALTER TABLE `sms_positions` CHANGE `positionType` `positionType` enum( 'senior', 'crew' ) not null default 'crew'" );
mysql_query( "ALTER TABLE `sms_positions` CHANGE `positionDisplay` `positionDisplay` varchar(1) not null default ''" );

/* update the positionDisplay variable */
$getPosDisplay = "SELECT positionid, positionDisplay FROM sms_positions";
$getPosDisplayResult = mysql_query( $getPosDisplay );

while( $getPosDisplayFetch = mysql_fetch_array( $getPosDisplayResult ) ) {
	extract( $getPosDisplayFetch, EXTR_OVERWRITE );
	
	/* don't let the query run if the positionDisplay stuff is already right */
	if( $positionDisplay == "0" || $positionDisplay == "1" ) {
	
		if( $positionDisplay == "0" ) {
			$display = "n";
		} if( $positionDisplay == "1" ) {
			$display = "y";
		}
		
		mysql_query( "UPDATE sms_positions SET positionDisplay = '$display' WHERE positionid = '$positionid' LIMIT 1" );
	
	}
	
}

/* finally, update the positionDisplay field to its final form */
mysql_query( "ALTER TABLE `sms_positions` CHANGE `positionDisplay` `positionDisplay` enum( 'y', 'n' ) not null default 'y'" );

/* update the permanent credits */
mysql_query( "UPDATE sms_messages SET sitePermanentCredits = 'Editing or removal of the following credits constitutes a material breach of the SMS Terms of Use outlined at the <a href=\"http://www.anodyne-productions.com/index.php?cat=main&page=legal\" target=\"_blank\">SMS ToU</a> page.\r\n\r\nSMS 2 uses the open source browser detection library <a href=\"http://sourceforge.net/projects/phpsniff/\" target=\"_blank\">phpSniff</a> to check for various versions of browsers for maximum compatibility.\r\n\r\nThe SMS 2 Update notification system uses <a href=\"http://magpierss.sourceforge.net/\" target=\"_blank\">MagpieRSS</a> to parse the necessary XML file. Magpie is distributed under the GPL license. Questions and suggestions about MagpieRSS should be sent to <i>magpierss-general@lists.sf.net</i>.\r\n\r\nSMS 2 uses icons from the open source <a href=\"http://tango.freedesktop.org/Tango_Icon_Gallery\" target=\"_blank\">Tango Icon Library</a>. The update icon used by SMS was created by David VanScott as a derivative of work done for the Tango Icon Library.\r\n\r\nThe rank sets (DS9 Era Duty Uniform Style #2 and DS9 Era Dress Uniform Style #2) used in SMS 2 were created by Kuro-chan of <a href=\"http://www.kuro-rpg.net\" target=\"_blank\">Kuro-RPG</a>. Please do not copy or modify the images in any way, simply contact Kuro-chan and he will see to your rank needs.\r\n\r\n<a href=\"http://www.kuro-rpg.net/\" target=\"_blank\"><img src=\"images/kurorpg-banner.jpg\" border=\"0\" alt=\"Kuro-RPG\" /></a>' WHERE messageid = 1" );

/* changes to the system table */
mysql_query( "ALTER TABLE `sms_system` ADD `sysuid` varchar( 20 ) not null default ''" );
mysql_query( "ALTER TABLE `sms_system` ADD `sysLaunchStatus` enum( 'y', 'n' ) not null default 'n'" );

/** setup the unique sim id **/
	
/* define the length */
$length = "20";

/* start with a blank password */
$string = "";

/* define possible characters */
$possible = "0123456789bcdfghjkmnpqrstvwxyz"; 

/* set up a counter */
$i = 0; 

/* add random characters to $password until $length is reached */
while( $i < $length ) { 

	/* pick a random character from the possible ones */
	$char = substr( $possible, mt_rand( 0, strlen( $possible )-1 ), 1 );
	
	/* we don't want this character if it's already in the password */
	if( !strstr( $string, $char ) ) { 
		$string .= $char;
		$i++;
	}

}
	
mysql_query( "UPDATE sms_system SET sysuid = '$string' WHERE sysid = '1' LIMIT 1" );

/* add the system versions table */
mysql_query( "CREATE TABLE `sms_system_versions` (
  `versionid` int(3) NOT NULL auto_increment,
  `version` varchar(50) NOT NULL default '',
  `versionDate` varchar(50) NOT NULL default '',
  `versionShortDesc` text NOT NULL,
  `versionDesc` text NOT NULL,
  PRIMARY KEY  (`versionid`)
) AUTO_INCREMENT=16 ;" );

mysql_query( "INSERT INTO `sms_system_versions` ( `versionid`, `version`, `versionDate`, `versionShortDesc`, `versionDesc` )
VALUES (1, '2.0.1', '1153890000', '', 'Fixed issues relating to bio and account management;Fixed bug with the join form;Fixed bug with personal log posting;Fixed issue with database field length issues on several variables;Fixed bio image reference issues that wouldn\'t allow users to specify an off-site location for their character image;Added minor functionality to the crew manifest, specs, and message items'),
(2, '2.0.2', '1155099600', '', 'Fixed issues relating to bio and account management (try #2);Fixed several issues associated with emails being sent out by the system;Added rank and position error checking for the crew listing in the admin control panel. List now provides an error message for users that have an invalid rank and/or position;Fixed manifest display bug;Fixed bug associated with whitespace and updating skins'),
(3, '2.1.0', '1158123600', '', 'Added database feature left out of the 2.0 release because of time constraints;Added tour feature'),
(4, '2.1.1', '1158382800', '', 'Fixed bug associated with excessive line breaks in database entries caused by the PHP nl2br() function that was being used;Added ability for COs to edit individual database items'),
(5, '2.2.0', '1159506000', '', 'Added confirmation prompts for users who have JavaScript turned on in their browser. When deleting an item, the system will prompt the user if they are sure they want to delete it;Added ability for COs to decide whether a JP counts as a single post or as as many posts as there are authors in the JP;Added a tag field to the mission posts to allow users to specify their post tags at the start. The email sent out will display the tag right at the top so another user knows right from the beginning whether or not their character is involved or tagged in the post;Added the ability for users to save posts, logs, and news items to come back to and keep working on;Fixed bug where XOs didn\'t have the right access permissions for creating and editing NPCs;Added ability to set activation and deactivation options for both players as well as NPCs;Fixed bug on the full manifest where positions that were turned off still showed up;Fixed image reference bug that had the tour section looking in the wrong place for tour images;Fixed bug where deleting a database item while it was selected would cause it to stay displayed in the browser despite not existing'),
(6, '2.2.1', '1159592400', '', 'Fixed bug where posts and logs had disappeared from both crew biographies as well as the admin control panel'),
(7, '2.3.0', '1163394000', '', 'Added ability for users to set the rank sets they see on the site when they\'re logged in;Improved rank management to include the ability to change other rank sets;Updated icons throughout the system;Added ability for COs to define an accept and reject message that they can edit when accepting or rejecting a player;Fixed bug where posts that were saved first wouldn\'t have an accurate timestamp;Fixed bug where systems that don\'t use SMS posting don\'t have access to posting news items;Fixed bug where simm statistics was in the menu even if the system doesn\'t use SMS posting;Fixed bug when department heads went to create an NPC, they couldn\'t create them at their own rank, just below;Fixed bug where rank select menus for department heads were cut off, not allowing them to select the top items;Added ability for COs to remove an award from a player through a GUI instead of a textbox;Fixed bug where NPCs with an invalid rank and/or position wouldn\'t show up in the NPC manifest (or full manifest). Like playing characters, if an NPC now has an invalid rank and/or position, it\'ll show an error at the bottom of the complete listing of NPCs in the control panel to allow someone to go in and fix the problem;Added links in a player\'s bio for COs to add or remove awards;Improved update notification. System will now check to make sure that both the files and the database are using the same version coming out of the the XML file and if they\'re not, display appropriate warning messages;The system will now email the authors of a JP whenever anyone saves it, notifying them that it\'s been changed;Fixed bug where posts that had been saved and then were posted wouldn\'t show any author info in the email;Fixed bug where posts and logs weren\'t ordered by date posted;Fixed bug when pending posts, logs, or news items were activated, they weren\'t mailed out to the crew;Fixed bug where, besides the from line, there was nowhere in a news item where the author was displayed;Added ability for COs to moderate posts, logs, or news from specific players;Updated layout of site globals page to make more sense;Fixed bug on accounts page for people without access to change a player\'s character type where both the $crewType and switch variables were being echoed out;Fixed bug where crew awards listing was trying to pull the small image from /images/awards instead of the large images from /images/awards/large;Improved efficiency on main control panel page by putting access level check before the system tries to check for pending items. If a user doesn\'t have level 4 access or higher, it won\'t even try to run the function now;Fixed issue where 2.2.0 and 2.2.1 didn\'t address changing the dockingStatus field in sms_starbase_docking'),
(8, '2.3.1', '1163653200', '', 'Fixed bug when posting PHP error involving in_array() was returned. This was caused when there were no users flagged for moderation;Fixed bug where JP authors\' latest post information wasn\'t updated when saving a JP then posting it;Fixed bug associated with a missing update piece from the 2.2.1 to 2.3.0 update. This bug only affected users who updated from 2.2.x to 2.3.0;Fixed minor bug in update notification where a wrong variable was being used and causing the version number not to be displayed out of the XML file'),
(9, '2.4.0', '1165122000', '', 'Added built-in deck listing feature;Added mission notes feature to allow COs to remind their crew of important things on the post and JP pages;Added ability for COs to change whether or not they use the mission notes feature;Changed add award feature to use graphical representations of the awards for adding instead of the select menu like before;Added a version history page;Added full list of users and their moderation status allowing for quick change in moderation status;Added ability for COs to allow XOs to receive crew applications and approve/deny them through the control panel;Fixed bug where simm page would try to print the task force even if the simm wasn\'t part of a task force;Changed link in update function to point to the new server;Fixed bug where news items weren\'t being emailed out;Fixed two style inconsistencies in tour management page;Added private messaging;Added tour summary;Added option to use a third image for each tour item;Added feature allowing COs to add a post, JP, log, or news item that a member of the crew has incorrectly posted or forgotten to post;Added sim progress feature that allows users to see how the sim has progressed over the last 3 months, 6 months, 9 months, and year;Added link from bio page to show all a user\'s posts and logs'),
(10, '2.4.1', '1165813200', '', 'Fixed SQL injection security holes by adding regular expression check to make sure that the GET variables being used were positive integers. If the check fails, the CO will be emailed with the offending user\'s IP address as well as the page they were trying to access and the exact time they attempted to access the page so the CO can forward that information on to the web host if necessary;Fixed incorrect link deck listing page when no decks are listed in the specifications page;Added Kuro-RPG banner on the credits page at his request'),
(11, '2.4.2', '1166850000', '', 'Fixed issue in update function where new SMS version wouldn\'t be displayed;Moved credits into the database and made them editable through the Site Messages panel;Fixed bug on bio page where the name wasn\'t being run through the printText() function to strip the SQL escaping slashes;Added non-posting departments to allow COs and XOs to create NPCs in departments where it isn\'t plausible to have a posting character;Added link to post entry for players who are logged in that will take them directly to the post mission entry page;Fixed call to undefined function error when editing mission notes;Fixed bug where email notification sent out after updating a JP wouldn\'t have a FROM field;Changed SMS to use the image name with mission images instead of the full path;Added ability to delete a saved post'),
(12, '2.4.3', '1167800400', '', 'Fixed JavaScript error when logging out;Fixed JavaScript bug with the Mission Notes hide/show feature;Added neuter and hemaphrodite to the gender options;Added player experience to the join form. This information will only be available in the email sent from the system and not stored in the database;Fixed bug where anyone who did a fresh install of 2.4.2 would not be able to access their globals and messages because of a typo in the install'),
(13, '2.4.4', '1169701200', '', 'Position grouping by department on the Add Character page;Gender and species added to NPC manifest;Bio page presentation cleanup;Deck listing page presentation cleanup;Departments sections in All NPCs list for access levels 4 and higher;HTML character printed after last department on department management page;Mission title wasn\'t being sent out in mission post emails;Mission log listing order - completed missions should be sorted by completion date descending;Email formatting bug in news item emails;Alternating row colors for the Crew Activity list, All NPCs, and All Characters;All Characters list ordering by department first;Editing an NPC\'s position through their bio would change the number of open positions for those positions (old and new);Some character pictures would break the SMS template on the bio page;If a player had a previous character with the same username and password, it\'d generally log them in as their old character;Email formatting bug in Award Nomination page;Changed Award Nomination and Change Status Request to have them email sent from the player nominating/requesting;Added User Stats page;Changed database to make it easier to track senior staff vs crew positions;Logic to make sure the apply link isn\'t show for an NPC occupying a position with no open slots;Added timestamp for when a playing character is deactivated;Updated styling on posting pages (bigger text boxes);Added a count of saved posts on the Post Count report;Post Count report wouldn\'t return the right results under some conditions;Sim statistics page wasn\'t obeying system\'s global preference for how to count posts and was including saved posts;Visual notification of saved JPs the user hasn\'t responded to;Leave date set on player deactivation;More logic in the printCO, printXO, printCOEmail, and printXOEmail functions to narrow down the results;Better layout on individual post management page;Ability to change a post\'s mission;Changing rank sets when spaces are between the values in the allowed ranks field;Fixed sim progress loops to accurately display the number of posts'),
(14, '2.4.4.1', '1170046800', '', 'Fixed positions table problem introduced in 2.4.4 (this release was only for future fresh installs, a patch fixed the issue for everyone else)'),
(15, '2.5.0', '1185317009', 'SMS 2.5 is a true milestone and one of the largest releases Anodyne has ever released.  This new version of SMS extends functionality across multiple planes, providing more control for COs with less effort.  A new user access system now allows COs to specify exactly which pages a certain player has access to and a new menu system means that you can now update menu items from within the SMS Control Panel.  On top of that, we\'ve patched dozens of bugs, fixed consistency issues, improved the system\'s overall efficiency, and made SMS smarter than ever before.', 'User access control system changed to let COs select exactly which pages a player has access to;Menus are now pulled from the database and managed through a page in the control panel;Moved alternating row colors to the stylesheet for skinners;Changed all system icons to use alpha-channel PNGs;Fixed JP author slot 3 issues;Added ability for COs to choose whether added posts are emailed out;Added option to select which mission the post is part of for the add post and add JP page;Fixed bug where updating a mission post through the mission posts listing would erase the mission info and cause an extract() error;Added tabs to pages with lots of content;Changed form submit buttons to use the image type instead of the native browser/OS widgets;Changed all timestamps from SQL format to UNIX format;Refreshed default theme;Added Cobalt theme;Added LCARS theme;Removed Alternate rank set to save space;Added phpSniff credits;Added page to display mission notes without having to go to the post pages;Added Top 5 Open Positions list on the main page that can be controlled through Site Globals;Improved site presentation options when it comes to the content on the main page - COs can now select which items they want to see;Added COs to acceptance/rejection emails;Widened text boxes throughout the system;Improved style consistency throughout the system;Changed news setup to allow a single news item to be viewed like a post or log (finally);Rewrote query execution check to be more efficient and smarter;Improved logic of activation page including the plurality based on the number of pending items in each category;Added First Launch feature that will give a CO a brief run-down of the updates to SMS when they first log in;Fixed manifest so that it won\'t show a link to apply for a position if there aren\'t any open slots;Standardized use of preview rank images;Added blank image to the root rank directory and changed system to use the blank image instead of looking for a specific rank image;Fixed manifest so that previous players and NPCs can hold two positions;Added graphical notification of player\'s LOA status on the main control panel;Install script will check to make sure the web location variable is in place, otherwise it won\'t let you continue to the next step;Crew activity report displays number of months now instead of just days;Added crew milestone report to show how long players have been aboard the sim;Fixed bug where user would not be notified if the update query failed because they tried to change their username, real name, or email without giving their current password;Changed password reset to display the new password instead of emailing it out because of problems with the emails not being sent out;Added unique sim identifier to make sure that SMS installations on the same domain don\'t cause problems for each other')" );

/* add the menu items table */
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
  PRIMARY KEY  (`menuid`)
) AUTO_INCREMENT=87 ;" );

mysql_query( "INSERT INTO `sms_menu_items` ( `menuid`, `menuGroup`, `menuOrder`, `menuTitle`, `menuLinkType`, `menuLink`, `menuAccess`, `menuMainSec`, `menuLogin`, `menuCat` )
VALUES (1, 0, 0, 'Main', 'onsite', 'index.php?page=main', '', '', 'n', 'main'),
(2, 0, 1, 'Personnel', 'onsite', 'index.php?page=manifest', '', '', 'n', 'main'),
(3, 0, 2, 'The Ship', 'onsite', 'index.php?page=ship', '', '', 'n', 'main'),
(4, 0, 3, 'The Simm', 'onsite', 'index.php?page=simm', '', '', 'n', 'main'),
(5, 0, 4, 'Database', 'onsite', 'index.php?page=database', '', '', 'n', 'main'),
(6, 0, 5, 'Control Panel', 'onsite', 'admin.php?page=main', '', '', 'y', 'main'),
(7, 0, 0, 'Simm News', 'onsite', 'index.php?page=news', '', 'main', 'n', 'general'),
(8, 0, 1, 'Site Credits', 'onsite', 'index.php?page=credits', '', 'main', 'n', 'general'),
(9, 0, 2, 'Contact Us', 'onsite', 'index.php?page=contact', '', 'main', 'n', 'general'),
(10, 0, 3, 'Join', 'onsite', 'index.php?page=join', '', 'main', 'n', 'general'),
(11, 0, 0, 'Crew Manifest', 'onsite', 'index.php?page=manifest', '', 'personnel', 'n', 'general'),
(12, 0, 1, 'NPC Manifest', 'onsite', 'index.php?page=manifest&disp=npcs', '', 'personnel', 'n', 'general'),
(13, 0, 2, 'Open Positions', 'onsite', 'index.php?page=manifest&disp=open', '', 'personnel', 'n', 'general'),
(14, 0, 3, 'Departed Crew', 'onsite', 'index.php?page=manifest&disp=past', '', 'personnel', 'n', 'general'),
(15, 0, 4, 'Chain of Command', 'onsite', 'index.php?page=coc', '', 'personnel', 'n', 'general'),
(16, 0, 5, 'Crew Awards', 'onsite', 'index.php?page=crewawards', '', 'personnel', 'n', 'general'),
(17, 0, 6, 'Join', 'onsite', 'index.php?page=join', '', 'personnel', 'n', 'general'),
(18, 0, 0, 'Ship History', 'onsite', 'index.php?page=history', '', 'ship', 'n', 'general'),
(19, 0, 1, 'Specifications', 'onsite', 'index.php?page=specifications', '', 'ship', 'n', 'general'),
(20, 0, 2, 'Ship Tour', 'onsite', 'index.php?page=tour', '', 'ship', 'n', 'general'),
(21, 0, 3, 'Deck Listing', 'onsite', 'index.php?page=decklisting', '', 'ship', 'n', 'general'),
(22, 0, 4, 'Departments', 'onsite', 'index.php?page=departments', '', 'ship', 'n', 'general'),
(23, 0, 5, 'Database', 'onsite', 'index.php?page=database', '', 'ship', 'n', 'general'),
(24, 0, 0, 'Current Mission', 'onsite', 'index.php?page=mission', '', 'simm', 'n', 'general'),
(25, 0, 1, 'Mission Logs', 'onsite', 'index.php?page=missions', '', 'simm', 'n', 'general'),
(26, 0, 2, 'Mission Summaries', 'onsite', 'index.php?page=summaries', '', 'simm', 'n', 'general'),
(27, 1, 0, 'Personal Log List', 'onsite', 'index.php?page=loglist', '', 'simm', 'n', 'general'),
(28, 1, 4, 'Crew Awards', 'onsite', 'index.php?page=crewawards', '', 'simm', 'n', 'general'),
(29, 1, 5, 'Simm Statistics', 'onsite', 'index.php?page=statistics', '', 'simm', 'n', 'general'),
(30, 1, 6, 'Simm Rules', 'onsite', 'index.php?page=rules', '', 'simm', 'n', 'general'),
(31, 1, 7, 'Database', 'onsite', 'index.php?page=database', '', 'simm', 'n', 'general'),
(32, 1, 8, 'Join', 'onsite', 'index.php?page=join', '', 'simm', 'n', 'general'),
(33, 0, 0, 'Write Mission Post', 'onsite', 'admin.php?page=post&sub=mission', 'p_mission', 'post', 'y', 'admin'),
(34, 0, 1, 'Write Joint Mission Post', 'onsite', 'admin.php?page=post&sub=jp', 'p_jp', 'post', 'y', 'admin'),
(35, 0, 2, 'Write Personal Log', 'onsite', 'admin.php?page=post&sub=log', 'p_log', 'post', 'y', 'admin'),
(36, 0, 3, 'Write News Item', 'onsite', 'admin.php?page=post&sub=news', 'p_news', 'post', 'y', 'admin'),
(37, 0, 4, 'Send Private Message', 'onsite', 'admin.php?page=post&sub=message', 'p_pm', 'post', 'y', 'admin'),
(38, 1, 0, 'Mission Notes', 'onsite', 'admin.php?page=post&sub=notes', 'p_missionnotes', 'post', 'y', 'admin'),
(39, 2, 0, 'Add Mission Post', 'onsite', 'admin.php?page=post&sub=addpost', 'p_addmission', 'post', 'y', 'admin'),
(40, 2, 1, 'Add Joint Mission Post', 'onsite', 'admin.php?page=post&sub=addjp', 'p_addjp', 'post', 'y', 'admin'),
(41, 2, 2, 'Add Personal Log', 'onsite', 'admin.php?page=post&sub=addlog', 'p_addlog', 'post', 'y', 'admin'),
(42, 2, 3, 'Add News Item', 'onsite', 'admin.php?page=post&sub=addnews', 'p_addnews', 'post', 'y', 'admin'),
(43, 0, 0, 'About SMS', 'onsite', 'admin.php?page=reports&sub=about', 'r_about', 'reports', 'y', 'admin'),
(44, 0, 1, 'Crew Activity', 'onsite', 'admin.php?page=reports&sub=activity', 'r_activity', 'reports', 'y', 'admin'),
(45, 0, 2, 'Post Count', 'onsite', 'admin.php?page=reports&sub=count', 'r_count', 'reports', 'y', 'admin'),
(46, 0, 3, 'Sim Progress', 'onsite', 'admin.php?page=reports&sub=progress', 'r_progress', 'reports', 'y', 'admin'),
(47, 0, 4, 'Strike List', 'onsite', 'admin.php?page=reports&sub=strikes', 'r_strikes', 'reports', 'y', 'admin'),
(48, 0, 5, 'Version History', 'onsite', 'admin.php?page=reports&sub=history', 'r_versions', 'reports', 'y', 'admin'),
(49, 0, 0, 'User Account', 'onsite', 'admin.php?page=user&sub=account', 'u_account1', 'user', 'y', 'admin'),
(50, 0, 0, 'User Account', 'onsite', 'admin.php?page=user&sub=account', 'u_account2', 'user', 'y', 'admin'),
(51, 0, 1, 'Biography', 'onsite', 'admin.php?page=user&sub=bio', 'u_bio1', 'user', 'y', 'admin'),
(52, 0, 1, 'Biography', 'onsite', 'admin.php?page=user&sub=bio', 'u_bio2', 'user', 'y', 'admin'),
(53, 0, 1, 'Biography', 'onsite', 'admin.php?page=user&sub=bio', 'u_bio3', 'user', 'y', 'admin'),
(54, 0, 2, 'Private Messages', 'onsite', 'admin.php?page=user&sub=inbox', 'u_inbox', 'user', 'y', 'admin'),
(55, 0, 3, 'Request Status Change', 'onsite', 'admin.php?page=user&sub=status', 'u_status', 'user', 'y', 'admin'),
(56, 0, 4, 'Site Options', 'onsite', 'admin.php?page=user&sub=site', 'u_options', 'user', 'y', 'admin'),
(57, 0, 5, 'Award Nominations', 'onsite', 'admin.php?page=user&sub=nominate', 'u_nominate', 'user', 'y', 'admin'),
(58, 0, 0, 'Site Globals', 'onsite', 'admin.php?page=manage&sub=globals', 'm_globals', 'manage', 'y', 'admin'),
(59, 0, 1, 'Site Messages', 'onsite', 'admin.php?page=manage&sub=messages', 'm_messages', 'manage', 'y', 'admin'),
(60, 0, 2, 'Specifications', 'onsite', 'admin.php?page=manage&sub=specifications', 'm_specs', 'manage', 'y', 'admin'),
(61, 0, 3, 'News Categories', 'onsite', 'admin.php?page=manage&sub=newscategories', 'm_newscat3', 'manage', 'y', 'admin'),
(62, 0, 4, 'User Access Levels', 'onsite', 'admin.php?page=manage&sub=access', 'x_access', 'manage', 'y', 'admin'),
(63, 1, 0, 'Mission Posts', 'onsite', 'admin.php?page=manage&sub=posts', 'm_posts', 'manage', 'y', 'admin'),
(64, 1, 1, 'Personal Logs', 'onsite', 'admin.php?page=manage&sub=logs', 'm_logs', 'manage', 'y', 'admin'),
(65, 1, 2, 'News Items', 'onsite', 'admin.php?page=manage&sub=news', 'm_news', 'manage', 'y', 'admin'),
(66, 1, 3, 'Mission Summaries', 'onsite', 'admin.php?page=manage&sub=summaries', 'm_missionsummaries', 'manage', 'y', 'admin'),
(67, 1, 4, 'Mission Notes', 'onsite', 'admin.php?page=manage&sub=missionnotes', 'm_missionnotes', 'manage', 'y', 'admin'),
(68, 2, 0, 'Create Character', 'onsite', 'admin.php?page=manage&sub=add', 'm_createcrew', 'manage', 'y', 'admin'),
(69, 2, 1, 'All NPCs', 'onsite', 'admin.php?page=manage&sub=npcs', 'm_npcs1', 'manage', 'y', 'admin'),
(70, 2, 1, 'All NPCs', 'onsite', 'admin.php?page=manage&sub=npcs', 'm_npcs2', 'manage', 'y', 'admin'),
(71, 2, 2, 'All Characters', 'onsite', 'admin.php?page=manage&sub=crew', 'm_crew', 'manage', 'y', 'admin'),
(72, 3, 0, 'Chain of Command', 'onsite', 'admin.php?page=manage&sub=coc', 'm_coc', 'manage', 'y', 'admin'),
(73, 3, 1, 'Give Crew Award', 'onsite', 'admin.php?page=manage&sub=addaward', 'm_giveaward', 'manage', 'y', 'admin'),
(74, 3, 2, 'Remove Crew Award', 'onsite', 'admin.php?page=manage&sub=removeaward', 'm_removeaward', 'manage', 'y', 'admin'),
(75, 3, 3, 'Strike Player', 'onsite', 'admin.php?page=manage&sub=strikes', 'm_strike', 'manage', 'y', 'admin'),
(76, 3, 4, 'User Post Moderation', 'onsite', 'admin.php?page=manage&sub=moderate', 'm_moderation', 'manage', 'y', 'admin'),
(77, 4, 0, 'Missions', 'onsite', 'admin.php?page=manage&sub=missions', 'm_missions', 'manage', 'y', 'admin'),
(78, 4, 1, 'Departments', 'onsite', 'admin.php?page=manage&sub=departments', 'm_departments', 'manage', 'y', 'admin'),
(79, 4, 2, 'Positions', 'onsite', 'admin.php?page=manage&sub=positions', 'm_positions', 'manage', 'y', 'admin'),
(80, 4, 3, 'Ranks', 'onsite', 'admin.php?page=manage&sub=ranks', 'm_ranks', 'manage', 'y', 'admin'),
(81, 4, 4, 'Awards', 'onsite', 'admin.php?page=manage&sub=awards', 'm_awards', 'manage', 'y', 'admin'),
(82, 4, 5, 'Database', 'onsite', 'admin.php?page=manage&sub=database', 'm_database', 'manage', 'y', 'admin'),
(83, 4, 6, 'Ship Tour', 'onsite', 'admin.php?page=manage&sub=tour', 'm_tour', 'manage', 'y', 'admin'),
(84, 4, 7, 'Deck Listing', 'onsite', 'admin.php?page=manage&sub=decklisting', 'm_decks', 'manage', 'y', 'admin'),
(85, 0, 6, 'Menu Items', 'onsite', 'admin.php?page=manage&sub=menugeneral', 'x_menu', 'manage', 'y', 'admin'),
(86, 0, 2, 'Crew Milestones', 'onsite', 'admin.php?page=reports&sub=milestones', 'r_milestones', 'reports', 'y', 'admin')" );

/* change the date fields to varchar */
mysql_query( "ALTER TABLE `sms_crew` CHANGE `joinDate` `joinDate` varchar(50) not null default ''" );
mysql_query( "ALTER TABLE `sms_crew` CHANGE `lastLogin` `lastLogin` varchar(50) not null default ''" );
mysql_query( "ALTER TABLE `sms_crew` CHANGE `lastPost` `lastPost` varchar(50) not null default ''" );
mysql_query( "ALTER TABLE `sms_personallogs` CHANGE `logPosted` `logPosted` varchar(50) not null default ''" );
mysql_query( "ALTER TABLE `sms_strikes` CHANGE `strikeDate` `strikeDate` varchar(50) not null default ''" );
mysql_query( "ALTER TABLE `sms_news` CHANGE `newsPosted` `newsPosted` varchar(50) not null default ''" );
mysql_query( "ALTER TABLE `sms_posts` CHANGE `postPosted` `postPosted` varchar(50) not null default ''" );
mysql_query( "ALTER TABLE `sms_missions` CHANGE `missionStart` `missionStart` varchar(50) not null default ''" );
mysql_query( "ALTER TABLE `sms_missions` CHANGE `missionEnd` `missionEnd` varchar(50) not null default ''" );
mysql_query( "ALTER TABLE `sms_privatemessages` CHANGE `pmDate` `pmDate` varchar(50) not null default ''" );

/**
	set up a multi-dimensional array for the timestamp update
	[x][0] => table's primary key
	[x][1] => field being updated
	[x][2] => table being updated
**/
$array = array(
	0 => array( 'crewid', 'joinDate', 'sms_crew' ),
	1 => array( 'crewid', 'leaveDate', 'sms_crew' ),
	2 => array( 'crewid', 'lastPost', 'sms_crew' ),
	3 => array( 'crewid', 'lastLogin', 'sms_crew' ),
	4 => array( 'strikeid', 'strikeDate', 'sms_strikes' ),
	5 => array( 'newsid', 'newsPosted', 'sms_news' ),
	6 => array( 'logid', 'logPosted', 'sms_personallogs' ),
	7 => array( 'postid', 'postPosted', 'sms_posts' ),
	8 => array( 'missionid', 'missionStart', 'sms_missions' ),
	9 => array( 'missionid', 'missionEnd', 'sms_missions' ),
	10 => array( 'pmid', 'pmDate', 'sms_privatemessages' )
);

/* loop through the array */
foreach( $array as $key => $value ) {

	/* pull in the info from the database */
	$getTime = "SELECT $value[0], $value[1] FROM $value[2] ORDER BY $value[0] ASC";
	$getTimeResult = mysql_query( $getTime );
	$getTimeCount = @mysql_num_rows( $getTimeResult );
	
	/* count the rows to avoid SQL errors */
	if( $getTimeCount >= 1 ) {
	
		/* loop through the results */
		while( $timeFetch = mysql_fetch_array( $getTimeResult ) ) {
			extract( $timeFetch, EXTR_OVERWRITE );
			
			/*
				make sure what the function is being fed is actually a
				SQL timestamp and not a UNIX timestamp 
			*/
			if( preg_match( "/^\d+$/", $timeFetch[1], $matches ) ) {} else {
			
				/* do some logic to make sure things are going to be updated correctly */
				if( $timeFetch[1] == "0000-00-00 00:00:00" || $timeFetch[1] == "-1" ) {
					$newTime = "";
				} else {
					$newTime = strtotime( $timeFetch[1] );
				}
				
				/* update the database */
				$update = "UPDATE $value[2] SET $value[1] = '$newTime' ";
				$update.= "WHERE $value[0] = '$timeFetch[0]' LIMIT 1";
				$updateResult = mysql_query( $update );
			
			}
		
		} /* close the while loop */
		
	} /* close the count check */
	
} /* close the foreach loop */

/* change the rankDisplay and prep it */
mysql_query( "ALTER TABLE `sms_ranks` CHANGE `rankDisplay` `rankDisplay` varchar(1) not null default ''" );

/* update the rankDisplay and rankName variables */
$getRankDisplay = "SELECT rankid, rankDisplay, rankName FROM sms_ranks";
$getRankDisplayResult = mysql_query( $getRankDisplay );

while( $getRankDisplayFetch = mysql_fetch_array( $getRankDisplayResult ) ) {
	extract( $getRankDisplayFetch, EXTR_OVERWRITE );
	
	if( $rankDisplay == "0" || $rankDisplay == "1" ) {
	
		/* set up the rank display change */
		if( $rankDisplay == "0" ) {
			$display = "n";
		} if( $rankDisplay == "1" ) {
			$display = "y";
		}
		
		mysql_query( "UPDATE sms_ranks SET rankDisplay = '$display' WHERE rankid = '$rankid' LIMIT 1" );
		
	}
	
	/* set up the rank name change */
	if( $rankName == "Lieutenant Junior Grade" ) {
		$newRank = "Lieutenant JG";
	} else {
		$newRank = $rankName;
	}
	
	/* run the query */
	mysql_query( "UPDATE sms_ranks SET rankName = '$newRank' WHERE rankid = '$rankid' LIMIT 1" );
	
}

/* change the rankDisplay */
mysql_query( "ALTER TABLE `sms_ranks` CHANGE `rankDisplay` `rankDisplay` enum( 'y', 'n' ) not null default 'y'" );

/* change the deptDisplay and prep it */
mysql_query( "ALTER TABLE `sms_departments` CHANGE `deptDisplay` `deptDisplay` varchar(1) not null default ''" );

/* update the positionDisplay variable */
$getDepDisplay = "SELECT deptid, deptDisplay FROM sms_departments";
$getDepDisplayResult = mysql_query( $getDepDisplay );

while( $getDepDisplayFetch = mysql_fetch_array( $getDepDisplayResult ) ) {
	extract( $getDepDisplayFetch, EXTR_OVERWRITE );
	
	if( $deptDisplay == "0" ) {
		$display = "n";
	} if( $deptDisplay == "1" ) {
		$display = "y";
	}
	
	mysql_query( "UPDATE sms_departments SET deptDisplay = '$display' WHERE deptid = '$deptid' LIMIT 1" );
	
}

/* change the deptDisplay */
mysql_query( "ALTER TABLE `sms_departments` CHANGE `deptDisplay` `deptDisplay` enum( 'y', 'n' ) not null default 'y'" );

/* change the missionStatus and prep it */
mysql_query( "ALTER TABLE `sms_missions` CHANGE `missionStatus` `missionStatus` varchar(50) not null default ''" );

/* update the missionStatus variable */
$getMisDisplay = "SELECT missionid, missionStatus FROM sms_missions";
$getMisDisplayResult = mysql_query( $getMisDisplay );

while( $getMisDisplayFetch = mysql_fetch_array( $getMisDisplayResult ) ) {
	extract( $getMisDisplayFetch, EXTR_OVERWRITE );
	
	if( $missionStatus == "0" ) {
		$display = "upcoming";
	} if( $missionStatus == "1" ) {
		$display = "current";
	} if( $missionStatus == "2" ) {
		$display = "completed";
	}
	
	mysql_query( "UPDATE sms_missions SET missionStatus = '$display' WHERE missionid = '$missionid' LIMIT 1" );
	
}

/* change the missionStatus field */
mysql_query( "ALTER TABLE `sms_missions` CHANGE `missionStatus` `missionStatus` enum( 'upcoming', 'current', 'completed' ) not null default 'upcoming'" );

/* update user access levels */
$getCrew = "SELECT crewid, accessLevel, displaySkin, displayRank FROM sms_crew WHERE accessLevel > '1' ORDER BY crewid ASC";
$getCrewResult = mysql_query( $getCrew );

while( $crewFetch = mysql_fetch_array( $getCrewResult ) ) {
	extract( $crewFetch, EXTR_OVERWRITE );

	if( $accessLevel == "5" ) {
		$levelsPost = "post,p_addjp,p_addnews,p_log,p_addlog,p_pm,p_mission,p_addmission,p_jp,p_news,p_missionnotes";
		$levelsManage = "manage,m_globals,m_messages,m_specs,m_posts,m_logs,m_news,m_missionsummaries,m_missionnotes,m_createcrew,m_crew,m_coc,m_npcs2,m_removeaward,m_strike,m_giveaward,m_missions,m_departments,m_moderation,m_ranks,m_awards,m_positions,m_tour,m_decks,m_database,m_newscat3,m_docking,m_catalogue";
		$levelsReports = "reports,r_about,r_count,r_strikes,r_activity,r_progress,r_versions,r_milestones";
		$levelsUser = "user,u_nominate,u_inbox,u_account2,u_status,u_options,u_bio3,u_stats,u_site";
		$levelsOther = "x_skindev,x_approve_users,x_approve_posts,x_approve_logs,x_approve_news,x_approve_docking,x_update,x_access,x_menu";
	} if( $accessLevel == "4" ) {
		$levelsPost = "post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes";
		$levelsManage = "manage,m_posts,m_logs,m_news,m_createcrew,m_npcs2,m_newscat2";
		$levelsReports = "reports,r_count,r_strikes,r_activity,r_progress,r_milestones";
		$levelsUser = "user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2,u_stats";
		$levelsOther = "x_approve_posts,x_approve_logs,x_approve_news";
	} if( $accessLevel == "3" ) {
		$levelsPost = "post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes";
		$levelsManage = "manage,m_createcrew,m_npcs1,m_newscat2";
		$levelsReports = "reports,r_count,r_strikes,r_activity,r_progress,r_milestones";
		$levelsUser = "user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2";
		$levelsOther = "";
	} if( $accessLevel == "2" ) {
		$levelsPost = "post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes";
		$levelsManage = "";
		$levelsReports = "reports,r_progress,r_milestones";
		$levelsUser = "user,u_account1,u_nominate,u_inbox,u_bio1,u_status,u_options";
		$levelsOther = "";
	}
	
	mysql_query( "UPDATE sms_crew SET accessPost = '$levelsPost', accessManage = '$levelsManage', accessReports = '$levelsReports', accessUser = '$levelsUser', accessOthers = '$levelsOther', displaySkin = 'default', displayRank = 'default' WHERE crewid = '$crewid' LIMIT 1" );
	
}

/* drop the unnecessary fields now */
mysql_query( "ALTER TABLE `sms_crew` DROP `accessLevel`" );
mysql_query( "ALTER TABLE `sms_globals` DROP `xoCrewApps`" );
mysql_query( "ALTER TABLE `sms_globals` DROP `showMainInfo`" );
mysql_query( "ALTER TABLE `sms_system` DROP `sysDocumentRoot`" );
mysql_query( "ALTER TABLE `sms_system` DROP `sysPHPSelf`" );
mysql_query( "ALTER TABLE `sms_system` DROP `sysServerName`" );
mysql_query( "ALTER TABLE `sms_system` DROP `sysServerAddr`" );

/* optimize the SQL tables */
optimizeSQLTable( "sms_crew" );
optimizeSQLTable( "sms_globals" );
optimizeSQLTable( "sms_system" );
optimizeSQLTable( "sms_missions" );
optimizeSQLTable( "sms_ranks" );
optimizeSQLTable( "sms_departments" );
optimizeSQLTable( "sms_positions" );
optimizeSQLTable( "sms_privatemessages" );
optimizeSQLTable( "sms_menu_items" );
optimizeSQLTable( "sms_posts" );
optimizeSQLTable( "sms_personallogs" );
optimizeSQLTable( "sms_news" );
optimizeSQLTable( "sms_messages" );
optimizeSQLTable( "sms_strikes" );

?>