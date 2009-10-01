<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/256.php
Purpose: Update to 2.6.0
Last Modified: 2008-08-01 1509 EST
**/

/*
|---------------------------------------------------------------
| MISCELLANEOUS
|---------------------------------------------------------------
|
| This code finds out the version of MySQL so that the page can
| do some logic to avoid collation problems.
|
*/
$t = mysql_query("select version() as ve");
echo mysql_error();
$r = mysql_fetch_object( $t );

if( $r->ve >= 4 ) {
	$tail = "CHARACTER SET utf8";
} else {
	$tail = "";
}

/*
|---------------------------------------------------------------
| SYSTEM GLOBALS
|---------------------------------------------------------------
|
| These changes introduce a few new features to SMS, namely the ability
| to set the email subject, update notification, stardate display and
| manifest display defaults.
|
*/
mysql_query( "ALTER TABLE `sms_globals` ADD `emailSubject` varchar(75) not null default '[" . SHIP_PREFIX . " " . SHIP_NAME . "]'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `updateNotify` enum('all','major','none') not null default 'all'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `stardateDisplaySD` enum('y','n') not null default 'y'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `stardateDisplayDate` enum('y','n') not null default 'y'" );
mysql_query( "ALTER TABLE `sms_globals` ADD `manifest_defaults` text not null" );

/* set the manifest defaults based on what kind of manifest setup there is already */
$get1 = "SELECT manifestDisplay FROM sms_globals WHERE globalid = 1 LIMIT 1";
$getR1 = mysql_query($get1);
$fetch1 = mysql_fetch_array($getR1);

switch($fetch1[0])
{
	case 'full':
		$defaults = "$(\'tr.active\').show();,$(\'tr.npc\').show();,$(\'tr.open\').show();";
		break;
	case 'split':
		$defaults = "$(\'tr.active\').show();";
		break;
}

mysql_query( "UPDATE sms_globals SET manifest_defaults = '$defaults' WHERE globalid = 1" );

/* get rid of the manifest display field now */
mysql_query( "ALTER TABLE `sms_globals` DROP `manifestDisplay`" );
mysql_query( "ALTER TABLE `sms_globals` DROP `useArchive`" );


/*
|---------------------------------------------------------------
| SPECIFICATIONS
|---------------------------------------------------------------
|
| These changes fix a bug with the specs page where commas could
| not be used in the compliment numbers because of the fact that
| the database fields used INT fields instead of VARCHAR.
|
*/
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentEmergency` `complimentEmergency` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentOfficers` `complimentOfficers` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentEnlisted` `complimentEnlisted` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentMarines` `complimentMarines` varchar(20) NOT NULL default ''" );
mysql_query( "ALTER TABLE `sms_specs` CHANGE `complimentCivilians` `complimentCivilians` varchar(20) NOT NULL default ''" );


/*
|---------------------------------------------------------------
| RANKS
|---------------------------------------------------------------
|
| SMS now includes more rank information, including a short name
| that is now used in the emails sent out. In addition, we now
| include all the cadet ranks, but turn them off by default. This
| step will blow away the ranks in the database and rebuild the
| ranks database, then loop through the crew data and attempt to
| update the crew so their ranks are still accurate.
|
*/
$clear = "TRUNCATE TABLE sms_ranks";
$clearR = mysql_query($clear);

mysql_query( "ALTER TABLE `sms_ranks` ADD `rankShortName` varchar(32) not null default ''" );

require_once('update/ranks.php');

$getCrew = "SELECT * FROM sms_crew";
$getCrewR = mysql_query($getCrew);

while($fetchCrew = mysql_fetch_assoc($getCrewR)) {
	extract($fetchCrew, EXTR_OVERWRITE);
	
	if(array_key_exists($fetchCrew['rankid'], $old_ranks))
	{
		$new_rank = $old_ranks[$fetchCrew['rankid']];
		mysql_query("UPDATE sms_crew SET rankid = $new_rank WHERE crewid = $fetchCrew[crewid] LIMIT 1");
	}
}


/*
|---------------------------------------------------------------
| MENU ITEMS
|---------------------------------------------------------------
|
| The system changes mean we have to update where the private messages
| link points to. In addition, we have changed the name of the private
| messages inbox to be Inbox instead of just Private Messages. The menu
| management page has been consolidated into a single page and the link
| has been changed to reflec that. Finally we have to add the menu item
| for the new Default Access Levels feature.
|
*/
$getPMLink = "SELECT * FROM sms_menu_items WHERE menuAccess = 'p_pm' LIMIT 1";
$getPMLinkResult = mysql_query( $getPMLink );
$pmLink = mysql_fetch_assoc( $getPMLinkResult );
mysql_query( "UPDATE sms_menu_items SET menuLink = 'admin.php?page=user&sub=inbox&tab=3' WHERE menuid = '$pmLink[menuid]' LIMIT 1" );

$getInboxLink = "SELECT * FROM sms_menu_items WHERE menuAccess = 'u_inbox' LIMIT 1";
$getInboxLinkResult = mysql_query( $getInboxLink );
$inboxLink = mysql_fetch_assoc( $getInboxLinkResult );
mysql_query( "UPDATE sms_menu_items SET menuTitle = 'Inbox' WHERE menuid = '$inboxLink[menuid]' LIMIT 1" );

$getMenuLink = "SELECT * FROM sms_menu_items WHERE menuLink = 'admin.php?page=manage&sub=menugeneral' LIMIT 1";
$getMenuLinkResult = mysql_query( $getMenuLink );
$menuLink = mysql_fetch_assoc( $getMenuLinkResult );
mysql_query( "UPDATE sms_menu_items SET menuLink = 'admin.php?page=manage&sub=menus' WHERE menuid = '$menuLink[menuid]' LIMIT 1" );

$getDB = "SELECT * FROM sms_menu_items WHERE menuAccess = 'm_database' LIMIT 1";
$getDBResult = mysql_query( $getDB );
$dbLink = mysql_fetch_assoc( $getDBResult );
mysql_query( "UPDATE sms_menu_items SET menuAccess = 'm_database2' WHERE menuid = '$dbLink[menuid]' LIMIT 1" );

$getPosts = "SELECT * FROM sms_menu_items WHERE menuAccess = 'm_posts' LIMIT 1";
$getPostsResult = mysql_query( $getPosts );
$postLink = mysql_fetch_assoc( $getPostsResult );
mysql_query( "UPDATE sms_menu_items SET menuAccess = 'm_posts2' WHERE menuid = '$postLink[menuid]' LIMIT 1" );

$getLogs = "SELECT * FROM sms_menu_items WHERE menuAccess = 'm_logs' LIMIT 1";
$getLogsResult = mysql_query( $getLogs );
$logLink = mysql_fetch_assoc( $getLogsResult );
mysql_query( "UPDATE sms_menu_items SET menuAccess = 'm_logs2' WHERE menuid = '$logLink[menuid]' LIMIT 1" );

/* default access levels menu item */
mysql_query( "INSERT INTO sms_menu_items ( menuGroup, menuOrder, menuTitle, menuLinkType, menuLink, menuAccess, menuMainSec, menuLogin, menuCat, menuAvailability )
VALUES ( 0, 5, 'Default Access Levels', 'onsite', 'admin.php?page=manage&sub=accesslevels', 'x_access', 'manage', 'y', 'admin', 'y' ),
( 0, 5, 'Database', 'onsite', 'admin.php?page=manage&sub=database', 'm_database1', 'manage', 'y', 'admin', 'y' ),
(1, 0, 'Docking Request', 'onsite', 'index.php?page=dockingrequest', '', 'ship', 'n', 'general', 'n'),
(1, 1, 'Docked Ships', 'onsite', 'index.php?page=dockedships', '', 'ship', 'n', 'general', 'n'),
(4, 3, 'Docked Ships', 'onsite', 'admin.php?page=manage&sub=docking', 'm_docking', 'manage', 'y', 'admin', 'n'),
(0, 0, 'The Starbase', 'onsite', 'index.php?page=starbase', '', '', 'n', 'main', 'n'),
(0, 0, 'Starbase History', 'onsite', 'index.php?page=history', '', 'ship', 'n', 'general', 'n'),
(0, 2, 'Starbase Tour', 'onsite', 'index.php?page=tour', '', 'ship', 'n', 'general', 'n'),
(4, 1, 'Starbase Tour', 'onsite', 'admin.php?page=manage&sub=tour', 'm_tour', 'manage', 'y', 'admin', 'n')" );


/*
|---------------------------------------------------------------
| CREW
|---------------------------------------------------------------
|
| SMS now offers a personalized menu option that allows players to 
| set up 10 of their favorite or most used links, giving them quick
| access to them once they are logged in.
|
*/
/* add the user menu item preferences */
mysql_query("
	ALTER TABLE  `sms_crew` ADD  `menu1` VARCHAR(8) NOT NULL DEFAULT '57',
	ADD  `menu2` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu3` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu4` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu5` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu6` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu7` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu8` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu9` VARCHAR(8) NOT NULL DEFAULT '0',
	ADD  `menu10` VARCHAR(8) NOT NULL DEFAULT '0'
");

mysql_query( "ALTER TABLE `sms_crew` CHANGE `image` `image` text NOT NULL" );
mysql_query( "UPDATE `sms_crew` SET displaySkin = 'default'" );
mysql_query( "UPDATE `sms_crew` SET displayRank = 'default'" );


/*
|---------------------------------------------------------------
| ACCESS LEVELS
|---------------------------------------------------------------
|
| Default access levels can now be adjusted to make sure that an
| admin has lots of control of what new players are given.
|
*/
mysql_query( "CREATE TABLE `sms_accesslevels` (
  `id` tinyint(1) NOT NULL auto_increment,
  `post` text NOT NULL,
  `manage` text NOT NULL,
  `reports` text NOT NULL,
  `user` text NOT NULL,
  `other` text NOT NULL,
  PRIMARY KEY  (`id`)
) " . $tail . " ;" );

mysql_query( "INSERT INTO `sms_accesslevels` (`id`, `post`, `manage`, `reports`, `user`, `other`) 
VALUES (1, 'post,p_addjp,p_missionnotes,p_jp,p_addlog,p_pm,p_log,p_addmission,p_mission,p_addnews,p_news', 'manage,m_awards,m_logs2,m_coc,m_posts2,m_positions,m_crew,m_missions,m_ranks,m_createcrew,m_missionsummaries,m_removeaward,m_globals,m_database2,m_messages,m_decks,m_newscat3,m_specs,m_departments,m_news,m_strike,m_docking,m_tour,m_giveaward,m_npcs2,m_moderation,m_missionnotes', 'reports,r_about,r_count,r_strikes,r_activity,r_progress,r_versions,r_milestones', 'user,u_nominate,u_inbox,u_account2,u_status,u_options,u_bio3,u_stats,u_site', 'x_approve_docking,x_approve_posts,x_update,x_approve_logs,x_approve_users,x_access,x_approve_news,x_menu'),
(2, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'manage,m_logs2,m_posts2,m_createcrew,m_database1,m_newscat2,m_news,m_npcs2', 'reports,r_count,r_strikes,r_activity,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2,u_stats', 'x_approve_posts,x_approve_logs,x_approve_news'),
(3, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'manage,m_posts1,m_createcrew,m_database1,m_newscat2,m_npcs1,m_logs1', 'reports,r_count,r_strikes,r_activity,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2', ''),
(4, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'm_posts1,m_newscat1,m_logs1', 'reports,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_bio1,u_status,u_options', '');" );


/*
|---------------------------------------------------------------
| NEWS
|---------------------------------------------------------------
|
| Sometimes news items need to be seen just by the crew. Private
| news items make sure that people who are not logged in cannot
| see those items.
|
*/
mysql_query( "ALTER TABLE `sms_news` ADD `newsPrivate` ENUM('y', 'n') NOT NULL DEFAULT 'n'" );


/*
|---------------------------------------------------------------
| AWARDS
|---------------------------------------------------------------
|
| We are adding award categories to allow NPCs to be given in character
| awards as a CO sees fit. The categories also specify that playing
| characters can get in character and out of character awards. We
| have also added a feature that moves award nominations to a queue
| for a CO to review, and if they approve them, add them to a player
| record immediately.
|
*/
mysql_query( "ALTER TABLE `sms_awards` ADD `awardCat` enum('ic','ooc','both') not null default 'both'" );

$getAwards = "SELECT crewid, awards FROM sms_crew";
$getAwardsR = mysql_query($getAwards);

while($awardsFetch = mysql_fetch_array($getAwardsR)) {
	extract($awardsFetch, EXTR_OVERWRITE);
	
	$award = str_replace(',', ';', $awardsFetch[1]);
	mysql_query("UPDATE sms_crew SET awards = '$award' WHERE crewid = $awardsFetch[0]");
	
}

mysql_query( "CREATE TABLE `sms_awards_queue` (
  `id` int(6) NOT NULL auto_increment,
  `crew` int(6) NOT NULL default '0',
  `nominated` int(6) NOT NULL default '0',
  `award` int(6) NOT NULL default '0',
  `reason` text NOT NULL,
  `status` enum('accepted','pending','rejected') NOT NULL default 'pending',
  PRIMARY KEY  (`id`)
) " . $tail . " ;" );


/*
|---------------------------------------------------------------
| DATABASE
|---------------------------------------------------------------
|
| We have added departmental databases to the system requiring a few
| new fields in both the database table as well as the departments table.
|
*/
mysql_query( "ALTER TABLE `sms_database` ADD `dbDept` int(4) NOT NULL DEFAULT '0'" );
mysql_query( "ALTER TABLE `sms_departments` ADD `deptDatabaseUse` enum('y', 'n') NOT NULL DEFAULT 'y'" );


/*
|---------------------------------------------------------------
| SYSTEM PLUGINS
|---------------------------------------------------------------
|
| With the use of more system-wide plugins, we are adding a plugins
| section to the About SMS page that is fed from this table.
|
*/
mysql_query( "CREATE TABLE `sms_system_plugins` (
	`pid` int(4) NOT NULL auto_increment,
	`plugin` varchar(255) NOT NULL default '',
	`pluginVersion` varchar(15) NOT NULL default '',
	`pluginSite` varchar(200) NOT NULL default '',
	`pluginUse` text NOT NULL,
	`pluginFiles` text NOT NULL,
	PRIMARY KEY  (`pid`)
) " . $tail . " ;" );

mysql_query( "INSERT INTO sms_system_plugins ( pid, plugin, pluginVersion, pluginSite, pluginUse, pluginFiles ) 
VALUES ( '1', 'jQuery', '1.2.6', 'http://www.jquery.com/', 'Javascript library used throughout SMS', 'framework/js/jquery.js' ),
( '2', 'jQuery UI', '1.0', 'http://ui.jquery.com/', 'Tabs throughout the system', 'framework/js/ui.tabs.js;skins/[your skin]/style-ui.tabs.css' ),
( '3', 'clickMenu', '0.1.6', 'http://p.sohei.org/jquery-plugins/clickmenu/', 'Customizable user menu', 'framework/js/clickmenu.js;skins/[your skin]/style-clickmenu.css' ),
( '4', 'Link Scrubber', '1.0', 'http://www.crismancich.de/jquery/plugins/linkscrubber/', 'Remove dotted border around clicked links in Firefox', 'framework/js/linkscrubber.js' ),
( '5', 'Shadowbox', '1.0', 'http://mjijackson.com/shadowbox/', 'Lightbox functionality;Gallery function on tour pages', 'framework/js/shadowbox-jquery.js;framework/js/shadowbox.js;framework/css/shadowbox.css' ),
( '6', 'Facebox', '1.0', 'http://famspam.com/facebox', 'Modal dialogs throughout the system', 'framework/js/facebox.js;framework/css/facebox.css;images/facebox_b.png;images/facebox_bl.png;images/facebox_br.png;images/facebox_closelabel.gif;images/facebox_loading.gif;images/facebox_tl.png;images/facebox_tr.png' ),
( '7', 'Reflect jQuery', '1.0', 'http://plugins.jquery.com/project/reflect', 'Dynamic image reflection on tour pages', 'framework/js/reflect.js' )" );


/*
|---------------------------------------------------------------
| SYSTEM MESSAGES
|---------------------------------------------------------------
|
| Some major changes in SMS mean we have to update the permanent
| credits to reflect some of the new pieces that have been added.
|
*/
mysql_query("UPDATE sms_messages SET siteCreditsPermanent = 'Editing or removal of the following credits constitutes a material breach of the SMS Terms of Use outlined at the <a href=\"http://www.anodyne-productions.com/index.php?cat=sms&page=disclaimers\" target=\"_blank\">SMS Terms of Use</a> page.\r\n\r\nSMS 2 makes extensive use of the <a href=\"http://www.jquery.com\" target=\"_blank\">jQuery</a> Javascript library as well as multiple jQuery plugins. By default, SMS includes the <a href=\"http://ui.jquery.com/\" target=\"_blank\">jQuery UI</a>, <a href=\"http://p.sohei.org/jquery-plugins/clickmenu/\" target=\"_blank\">clickMenu</a>, <a href=\"http://www.crismancich.de/jquery/plugins/linkscrubber/\" target=\"_blank\">Link Scrubber</a>, <a href=\"http://mjijackson.com/shadowbox/\" target=\"_blank\">Shadowbox</a>, <a href=\"http://famspam.com/facebox\" target=\"_blank\">Facebox</a>, and <a href=\"http://plugins.jquery.com/project/reflect\" target=\"_blank\">Reflect jQuery</a>. More information about the versions and uses of the plugins can be obtained from the simm\'s webmaster.\r\n\r\nSMS 2 uses the open source browser detection library <a href=\"http://sourceforge.net/projects/phpsniff/\" target=\"_blank\">phpSniff</a> to check for various versions of browsers for maximum compatibility.\r\n\r\nThe SMS 2 Update notification system uses <a href=\"http://magpierss.sourceforge.net/\" target=\"_blank\">MagpieRSS</a> to parse the necessary XML file. Magpie is distributed under the GPL license. Questions and suggestions about MagpieRSS should be sent to <i>magpierss-general@lists.sf.net</i>.\r\n\r\nSMS 2 uses icons from the open source <a href=\"http://tango.freedesktop.org/Tango_Icon_Gallery\" target=\"_blank\">Tango Icon Library</a>.\r\n\r\nAdd and remove icons from the PI Diagona Pack created by <a href=\"http://pinvoke.com\" target=\"_blank\">Pinvoke.com</a>. Colorization by David VanScott.\r\n\r\nSMS 2 includes a stardate script developed by Phillip Sublett. Information on the script can be found at his site, <a href=\"http://TrekGuide.com/Stardates.htm\" target=\"_blank\">TrekGuide</a>.\r\n\r\nThe rank sets (DS9 Era Duty Uniform Style 2 and DS9 Era Dress Uniform Style 2) used in SMS 2 were created by Kuro-chan of <a href=\"http://www.kuro-rpg.net\" target=\"_blank\">Kuro-RPG</a>. Please do not copy or modify the images in any way, simply contact Kuro-chan and he will see to your rank needs.\r\n\r\n<a href=\"http://www.kuro-rpg.net/\" target=\"_blank\"><img src=\"images/kurorpg-banner.jpg\" border=\"0\" alt=\"Kuro-RPG\" /></a>' WHERE messageid = 1");


/*
|---------------------------------------------------------------
| SYSTEM VERSIONS
|---------------------------------------------------------------
|
| Now that SMS development is happening through SVN and SMS3 releases
| will happen off SVN, we are adding the SVN revision number to the
| release information. This will not mean much during SMS2, but will
| mean more in the future. Finally, we are adding the release information
| for this release.
|
*/
mysql_query( "ALTER TABLE `sms_system_versions` ADD `versionRev` int(5) NOT NULL AFTER `version`" );
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionRev`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.6.0', '604', '1217109600', 'This release is the final major update to SMS 2 and adds new features like user editing of posts, departmental databases, personalized menus, and a whole slew of smaller enhancements. Overall, this release is one of our largest and is an excellent capstone to SMS 2!', 'Private news items (can only be seen when logged in);Added the jQuery Javascript library;Tabs now use jQuery, meaning the content is available immediately after clicking;Added stardate script and the ability to turn it on and off (through site globals);Admins can now choose whether or not they want to be notified of SMS updates;Admins can now set the email subject lines (default is [Ship Name]);Acceptance and rejection messages now use wild card variables for dynamically inserting things like name, rank, positions, and ship name;If a query fails, it\'ll display an SMS Error Report that someone can copy and paste to the forums;Personalized menus;NPCs can now be given in character awards;Departmental databases;Completely new install process that\'s clearer and with much better instructions;Users (with proper permissions) can edit their own mission posts (except which mission the post is in and the post status);Users (with proper permissions) can edit their own personal logs (except the author and log status);Admins can now set the defaults for the various access levels (CO, XO, Department Head, Standard Player);Players can now put more than one image in their bio and they\'ll be displayed as a mini-gallery like the tour item pictures;SMS now automatically detects your web location variable during installation;Award nominations are now sent to a queue for approval by an admin;Completely rewrote activation page;Awards now include the award given, when it was given, and the reason;Completely new manifest page using jQuery;Completely new departments page using jQuery for toggling;Site options page now uses tabs to better organize everything;Combadge images on the manifest are now PNGs to match with any background color;Add and remove icons now have an off and over state;Added function for escaping strings before they\'re entered into the database;Added logic to check for the existence of the large award image and if it fails, fall back to the smaller version of the image;Notifications are now less obtrusive and provide more instant feedback;Made the pending checks smarter;Cleaned up the inbox including tabs (with unread count) and moving the compose PM into the inbox;Inbox now has a select all\/deselect all option;Added dynamic image reflection to the tour images;All images now have a class so that a skin can style images differently if they want;When viewing NPCs\' bios, it won\'t show the Posting Activity sections;SMS now uses some PHP constants to give functions access to things like web location and ship name;Awards now have categories of in character, out of character, and both;Error page when someone tries to go to a page that doesn\'t exist;Added a lightbox to the tour images to make a mini gallery;Replying to a private message will show the content of the message you\'re replying to below the compose box;A ton of commenting in the default skin to (hopefully) help out people who are trying to create their own skins;SMS now uses AXAH (Asynchronous XHTML and HTTP) for creating, editing, and deleting some items (menu items, departments, activations, ranks, positions, crew awards, tour items, missions, giving\/removing awards to crew, user post moderation, and docked ships);Consolidated menu management into a single page instead of two;Removed unused images;Adding system plugins to the About SMS page;Updated the framework structure;Default skin now uses some fancy dynamic location stuff so that the code could be moved to another directory and keep working;Default skin now uses UTF-8 for character encoding and English as the language by default;Version history page now uses tabs;Management pages for posts, logs, and news items now use tabs to separate activated, saved, and pending entries, plus it lists entries instead of providing \"mini editing\";Got rid of the confusing CLASS field in rank management, replacing it with a drop-down of the class groups to make it a little more self-explanatory;Re-wrote page for setting access levels for entire crew to make it better (it just plain sucked before);Gave the starbase-specific pages a little love to bring them more in line with the rest of the system;The bio page now displays awards from the most recent down instead of the oldest first;Rewrote the user moderation page to be more secure;Ranks now have short names (CAPT instead of Captain) that are used in emails to shorten the FROM field;Fixed bug where rank menus didn\'t respect the rankDisplay flag;Fixed bug where the system check class would try to write something to the main ACP even if there was nothing to write, causing an extra space;Fixed bug where crew compliment fields would only allow integers (commas would break things);Fixed bug where player stats page would throw back some weird data if there wasn\'t a properly formatted join date;Fixed bug where changing a playing character to something else (or vice versa) wouldn\'t affect the open positions;Fixed bug where contact page wouldn\'t send mail out;Fixed bug where the read more prompts in the control panel could break right in the middle;Fixed all the Apache warnings and errors SMS would dump into the server error logs (will make server admins VERY happy);Fixed bug where the crew awards page would, in some situations, print the award name twice;Fixed bug where someone with full XO privileges could create a CO character (thought without the CO access levels);Fixed bug where mission status wasn\'t inserted into the database;Fixed bug in the 2.4.4 update script where a semicolon was missing;Fixed bug where reset password form wouldn\'t send the email out to the appropriate person (or update it in the database either);Fixed bug where mission order wouldn\'t be updated;Fixed bug that would allow pending, inactive, and NPC characters to log in;Removed unused System Catalogues item from access levels management;Removed unused Skin Development item from access levels management;Removed unnecessary access checks on the admin subsection pages;Worked around Webkit bug where post edit icon wouldn\'t show up;Fixed bug where using the delete link for an individual news item (on the news item page) wouldn\'t do anything at all;Fixed join page bug where height, weight, physical description, personality overview, and real name weren\'t be inserted into the database;Fixed bug where admins could put a pound sign in the department color field and break the color system' )" );

?>