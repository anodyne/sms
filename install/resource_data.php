<?php
/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: install/resource_data.php
Purpose: Installation resource file that contains the database data

System Version: 2.6.9
Last Modified: 2009-08-20 0841 EST
**/

/* insert data into the access levels table */
mysql_query( "INSERT INTO `sms_accesslevels` (`id`, `post`, `manage`, `reports`, `user`, `other`) 
VALUES (1, 'post,p_addjp,p_missionnotes,p_jp,p_addlog,p_pm,p_log,p_addmission,p_mission,p_addnews,p_news', 'manage,m_awards,m_logs2,m_coc,m_posts2,m_positions,m_crew,m_missions,m_ranks,m_createcrew,m_missionsummaries,m_removeaward,m_globals,m_database2,m_messages,m_decks,m_newscat3,m_specs,m_departments,m_news,m_strike,m_docking,m_tour,m_giveaward,m_npcs2,m_moderation,m_missionnotes', 'reports,r_about,r_count,r_strikes,r_activity,r_progress,r_versions,r_milestones', 'user,u_nominate,u_inbox,u_account2,u_status,u_options,u_bio3,u_stats,u_site', 'x_approve_docking,x_approve_posts,x_update,x_approve_logs,x_approve_users,x_access,x_approve_news,x_menu'),
(2, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'manage,m_logs2,m_posts2,m_createcrew,m_database1,m_newscat2,m_news,m_npcs2', 'reports,r_count,r_strikes,r_activity,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio3,u_stats', 'x_approve_posts,x_approve_logs,x_approve_news'),
(3, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'manage,m_posts1,m_createcrew,m_database1,m_newscat2,m_npcs1,m_logs1', 'reports,r_count,r_strikes,r_activity,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio2', ''),
(4, 'post,p_log,p_pm,p_mission,p_jp,p_news,p_missionnotes', 'm_posts1,m_newscat1,m_logs1', 'reports,r_progress,r_milestones', 'user,u_account1,u_nominate,u_inbox,u_bio1,u_status,u_options', '');" );

/* insert data into the coc table */
mysql_query( "INSERT INTO `sms_coc` (`cocid`, `crewid`) VALUES (1, 1);" );

/* insert data into the department table */
mysql_query( "INSERT INTO `sms_departments` (`deptid`, `deptOrder`, `deptClass`, `deptName`, `deptDesc`, `deptDisplay`, `deptColor`, `deptType`) 
VALUES (1, 1, 1, 'Command', 'The Command department is ultimately responsible for the ship and its crew, and those within the department are responsible for commanding the vessel and representing the interests of Starfleet.', 'y', '9c2c2c', 'playing'),
(2, 2, 1, 'Flight Control', 'Responsible for the navigation and flight control of a vessel and its auxiliary craft, the Flight Control department includes pilots trained in both starship and auxiliary craft piloting. Note that the Flight Control department does not include Fighter pilots.', 'y', '9c2c2c', 'playing'),
(3, 3, 1, 'Strategic Operations', 'The Strategic Operations department acts as an advisory to the command staff, as well as a resource of knowledge and information concerning hostile races in the operational zone of the ship, as well as combat strategies and other such things.', 'y', '9c2c2c', 'playing'),
(4, 4, 2, 'Security & Tactical', 'Merging the responsibilities of ship to ship and personnel combat into a single department, the security & tactical department is responsible for the tactical readiness of the vessel and the security of the ship.', 'y', 'c08429', 'playing'),
(5, 5, 2, 'Operations', 'The operations department is responsible for keeping ship systems functioning properly, rerouting power, bypassing relays, and doing whatever else is necessary to keep the ship operating at peak efficiency.', 'y', 'c08429', 'playing'),
(6, 6, 2, 'Engineering', 'The engineering department has the enormous task of keeping the ship working; they are responsible for making repairs, fixing problems, and making sure that the ship is ready for anything.', 'y', 'c08429', 'playing'),
(7, 7, 3, 'Science', 'From sensor readings to figuring out a way to enter the strange spacial anomaly, the science department is responsible for recording data, testing new ideas out, and making discoveries.', 'y', '008080', 'playing'),
(8, 8, 3, 'Medical & Counseling', 'The medical & counseling department is responsible for the mental and physical health of the crew, from running annual physicals to combatting a strange plague that is afflicting the crew to helping a crew member deal with the loss of a loved one.', 'y', '008080', 'playing'),
(9, 9, 4, 'Intelligence', 'The Intelligence department is responsible for gathering and providing intelligence as it becomes possible during a mission; during covert missions, the intelligence department also takes a more active role, providing the necessary classified and other information.', 'y', '666666', 'playing'),
(10, 10, 5, 'Diplomatic Detachment', 'Responsible for representing the Federation and its interest, members of the Diplomatic Corps are members of the civilian branch of the Federation.', 'y', '800080', 'playing'),
(11, 11, 6, 'Marine Detachment', 'When the standard security detail is not enough, marines come in and clean up; the marine detachment is a powerful tactical addition to any ship, responsible for partaking in personal combat, from sniping to melee.', 'y', '008000', 'playing'),
(12, 12, 7, 'Starfighter Wing', 'The best pilots in Starfleet, they are responsible for piloting the starfighters in ship to ship battles, as well as providing escort for shuttles, and runabouts.', 'y', '406ceb', 'playing'),
(13, 13, 8, 'Civilian Affairs', 'Civilians play an important role in Starfleet. Many civilian specialists across a number of fields work on occasion with Starfleet personnel as a Mission Specialist. In other cases, extra ship and station duties, such as running the ship''s lounge, are outsourced to a civilian contract.', 'y', 'ffffff', 'playing');" );

/* insert data into the globals table */
mysql_query( "INSERT INTO `sms_globals` (`globalid`, `shipPrefix`, `shipName`, `shipRegistry`, `skin`, `allowedSkins`, `allowedRanks`, `fleet`, `fleetURL`, `tfMember`, `tfName`, `tfURL`, `tgMember`, `tgName`, `tgURL`, `hasWebmaster`, `webmasterName`, `webmasterEmail`, `showNews`, `showNewsNum`, `simmYear`, `rankSet`, `simmType`, `postCountDefault`, `manifest_defaults`, `useSamplePost`, `logList`, `bioShowPosts`, `bioShowLogs`, `bioShowPostsNum`, `bioShowLogsNum`, `jpCount`, `usePosting`, `useMissionNotes` ) 
VALUES (1, '', '', '', 'default', 'default,cobalt', 'default,dress', '', '', 'n', '', '', 'n', '', '', 'n', '', '', 'y', 3, '2384', 'default', 'ship', '14', '$(\'tr.active\').show();,$(\'tr.npc\').show();', 'y', 20, 'y', 'y', 5, 5, 'y', 'y', 'y' );" );

/* populate the menu items table */
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
(24, 0, 0, 'Current Mission(s)', 'onsite', 'index.php?page=mission', '', 'simm', 'n', 'general'),
(25, 0, 1, 'Mission Logs', 'onsite', 'index.php?page=missions', '', 'simm', 'n', 'general'),
(26, 0, 2, 'Mission Summaries', 'onsite', 'index.php?page=summaries', '', 'simm', 'n', 'general'),
(27, 1, 0, 'Personal Log List', 'onsite', 'index.php?page=loglist', '', 'simm', 'n', 'general'),
(28, 1, 1, 'Crew Awards', 'onsite', 'index.php?page=crewawards', '', 'simm', 'n', 'general'),
(29, 1, 2, 'Simm Statistics', 'onsite', 'index.php?page=statistics', '', 'simm', 'n', 'general'),
(30, 1, 3, 'Simm Rules', 'onsite', 'index.php?page=rules', '', 'simm', 'n', 'general'),
(31, 1, 4, 'Database', 'onsite', 'index.php?page=database', '', 'simm', 'n', 'general'),
(32, 1, 5, 'Join', 'onsite', 'index.php?page=join', '', 'simm', 'n', 'general'),
(33, 0, 0, 'Write Mission Post', 'onsite', 'admin.php?page=post&sub=mission', 'p_mission', 'post', 'y', 'admin'),
(34, 0, 1, 'Write Joint Mission Post', 'onsite', 'admin.php?page=post&sub=jp', 'p_jp', 'post', 'y', 'admin'),
(35, 0, 2, 'Write Personal Log', 'onsite', 'admin.php?page=post&sub=log', 'p_log', 'post', 'y', 'admin'),
(36, 0, 3, 'Write News Item', 'onsite', 'admin.php?page=post&sub=news', 'p_news', 'post', 'y', 'admin'),
(37, 0, 4, 'Send Private Message', 'onsite', 'admin.php?page=user&sub=inbox&tab=3', 'p_pm', 'post', 'y', 'admin'),
(38, 1, 0, 'Mission Notes', 'onsite', 'admin.php?page=post&sub=notes', 'p_missionnotes', 'post', 'y', 'admin'),
(39, 2, 0, 'Add Mission Post', 'onsite', 'admin.php?page=post&sub=addpost', 'p_addmission', 'post', 'y', 'admin'),
(40, 2, 1, 'Add Joint Mission Post', 'onsite', 'admin.php?page=post&sub=addjp', 'p_addjp', 'post', 'y', 'admin'),
(41, 2, 2, 'Add Personal Log', 'onsite', 'admin.php?page=post&sub=addlog', 'p_addlog', 'post', 'y', 'admin'),
(42, 2, 3, 'Add News Item', 'onsite', 'admin.php?page=post&sub=addnews', 'p_addnews', 'post', 'y', 'admin'),
(43, 0, 0, 'About SMS', 'onsite', 'admin.php?page=reports&sub=about', 'r_about', 'reports', 'y', 'admin'),
(44, 0, 1, 'Crew Activity', 'onsite', 'admin.php?page=reports&sub=activity', 'r_activity', 'reports', 'y', 'admin'),
(45, 0, 2, 'Crew Milestones', 'onsite', 'admin.php?page=reports&sub=milestones', 'r_milestones', 'reports', 'y', 'admin'),
(46, 0, 3, 'Post Count', 'onsite', 'admin.php?page=reports&sub=count', 'r_count', 'reports', 'y', 'admin'),
(47, 0, 4, 'Sim Progress', 'onsite', 'admin.php?page=reports&sub=progress', 'r_progress', 'reports', 'y', 'admin'),
(48, 0, 5, 'Strike List', 'onsite', 'admin.php?page=reports&sub=strikes', 'r_strikes', 'reports', 'y', 'admin'),
(49, 0, 6, 'Version History', 'onsite', 'admin.php?page=reports&sub=history', 'r_versions', 'reports', 'y', 'admin'),
(50, 0, 0, 'User Account', 'onsite', 'admin.php?page=user&sub=account', 'u_account1', 'user', 'y', 'admin'),
(51, 0, 0, 'User Account', 'onsite', 'admin.php?page=user&sub=account', 'u_account2', 'user', 'y', 'admin'),
(52, 0, 1, 'Biography', 'onsite', 'admin.php?page=user&sub=bio', 'u_bio1', 'user', 'y', 'admin'),
(53, 0, 1, 'Biography', 'onsite', 'admin.php?page=user&sub=bio', 'u_bio2', 'user', 'y', 'admin'),
(54, 0, 1, 'Biography', 'onsite', 'admin.php?page=user&sub=bio', 'u_bio3', 'user', 'y', 'admin'),
(55, 0, 2, 'Inbox', 'onsite', 'admin.php?page=user&sub=inbox', 'u_inbox', 'user', 'y', 'admin'),
(56, 0, 3, 'Request Status Change', 'onsite', 'admin.php?page=user&sub=status', 'u_status', 'user', 'y', 'admin'),
(57, 0, 4, 'Site Options', 'onsite', 'admin.php?page=user&sub=site', 'u_options', 'user', 'y', 'admin'),
(58, 0, 5, 'Award Nominations', 'onsite', 'admin.php?page=user&sub=nominate', 'u_nominate', 'user', 'y', 'admin'),
(59, 0, 0, 'Site Globals', 'onsite', 'admin.php?page=manage&sub=globals', 'm_globals', 'manage', 'y', 'admin'),
(60, 0, 1, 'Site Messages', 'onsite', 'admin.php?page=manage&sub=messages', 'm_messages', 'manage', 'y', 'admin'),
(61, 0, 2, 'User Access Levels', 'onsite', 'admin.php?page=manage&sub=access', 'x_access', 'manage', 'y', 'admin'),
(62, 0, 3, 'Default Access Levels', 'onsite', 'admin.php?page=manage&sub=accesslevels', 'x_access', 'manage', 'y', 'admin'),
(63, 0, 4, 'Menu Items', 'onsite', 'admin.php?page=manage&sub=menus', 'x_menu', 'manage', 'y', 'admin'),
(64, 0, 5, 'Database', 'onsite', 'admin.php?page=manage&sub=database', 'm_database1', 'manage', 'y', 'admin'),
(65, 0, 5, 'Database', 'onsite', 'admin.php?page=manage&sub=database', 'm_database2', 'manage', 'y', 'admin'),
(66, 1, 0, 'Missions', 'onsite', 'admin.php?page=manage&sub=missions', 'm_missions', 'manage', 'y', 'admin'),
(67, 1, 1, 'Mission Notes', 'onsite', 'admin.php?page=manage&sub=missionnotes', 'm_missionnotes', 'manage', 'y', 'admin'),
(68, 1, 2, 'Mission Summaries', 'onsite', 'admin.php?page=manage&sub=summaries', 'm_missionsummaries', 'manage', 'y', 'admin'),
(69, 1, 3, 'Mission Posts', 'onsite', 'admin.php?page=manage&sub=posts', 'm_posts2', 'manage', 'y', 'admin'),
(70, 1, 4, 'Personal Logs', 'onsite', 'admin.php?page=manage&sub=logs', 'm_logs2', 'manage', 'y', 'admin'),
(71, 1, 5, 'News Items', 'onsite', 'admin.php?page=manage&sub=news', 'm_news', 'manage', 'y', 'admin'),
(72, 1, 6, 'News Categories', 'onsite', 'admin.php?page=manage&sub=newscategories', 'm_newscat3', 'manage', 'y', 'admin'),
(73, 2, 0, 'Create Character/NPC', 'onsite', 'admin.php?page=manage&sub=add', 'm_createcrew', 'manage', 'y', 'admin'),
(74, 2, 1, 'All Playing Characters', 'onsite', 'admin.php?page=manage&sub=crew', 'm_crew', 'manage', 'y', 'admin'),
(75, 2, 2, 'All NPCs', 'onsite', 'admin.php?page=manage&sub=npcs', 'm_npcs1', 'manage', 'y', 'admin'),
(76, 2, 2, 'All NPCs', 'onsite', 'admin.php?page=manage&sub=npcs', 'm_npcs2', 'manage', 'y', 'admin'),
(77, 2, 3, 'Chain of Command', 'onsite', 'admin.php?page=manage&sub=coc', 'm_coc', 'manage', 'y', 'admin'),
(78, 2, 4, 'Strikes', 'onsite', 'admin.php?page=manage&sub=strikes', 'm_strike', 'manage', 'y', 'admin'),
(79, 2, 5, 'User Post Moderation', 'onsite', 'admin.php?page=manage&sub=moderate', 'm_moderation', 'manage', 'y', 'admin'),
(80, 3, 0, 'Crew Awards', 'onsite', 'admin.php?page=manage&sub=awards', 'm_awards', 'manage', 'y', 'admin'),
(81, 3, 1, 'Give Award', 'onsite', 'admin.php?page=manage&sub=addaward', 'm_giveaward', 'manage', 'y', 'admin'),
(82, 3, 2, 'Remove Award', 'onsite', 'admin.php?page=manage&sub=removeaward', 'm_removeaward', 'manage', 'y', 'admin'),
(83, 4, 0, 'Specifications', 'onsite', 'admin.php?page=manage&sub=specifications', 'm_specs', 'manage', 'y', 'admin'),
(84, 4, 1, 'Ship Tour', 'onsite', 'admin.php?page=manage&sub=tour', 'm_tour', 'manage', 'y', 'admin'),
(85, 4, 2, 'Deck Listing', 'onsite', 'admin.php?page=manage&sub=decklisting', 'm_decks', 'manage', 'y', 'admin'),
(86, 5, 0, 'Departments', 'onsite', 'admin.php?page=manage&sub=departments', 'm_departments', 'manage', 'y', 'admin'),
(87, 5, 1, 'Positions', 'onsite', 'admin.php?page=manage&sub=positions', 'm_positions', 'manage', 'y', 'admin'),
(88, 5, 2, 'Ranks', 'onsite', 'admin.php?page=manage&sub=ranks', 'm_ranks', 'manage', 'y', 'admin'),

(89, 1, 0, 'Docking Request', 'onsite', 'index.php?page=dockingrequest', '', 'ship', 'n', 'general'),
(90, 1, 1, 'Docked Ships', 'onsite', 'index.php?page=dockedships', '', 'ship', 'n', 'general'),
(91, 4, 3, 'Docked Ships', 'onsite', 'admin.php?page=manage&sub=docking', 'm_docking', 'manage', 'y', 'admin'),
(92, 0, 0, 'The Starbase', 'onsite', 'index.php?page=starbase', '', '', 'n', 'main'),
(93, 0, 0, 'Starbase History', 'onsite', 'index.php?page=history', '', 'ship', 'n', 'general'),
(94, 0, 2, 'Starbase Tour', 'onsite', 'index.php?page=tour', '', 'ship', 'n', 'general'),
(95, 4, 1, 'Starbase Tour', 'onsite', 'admin.php?page=manage&sub=tour', 'm_tour', 'manage', 'y', 'admin')" );

/* insert data into the messages table */
mysql_query( "INSERT INTO `sms_messages` (`messageid`, `welcomeMessage`, `simmMessage`, `shipMessage`, `shipHistory`, `cpMessage`, `joinDisclaimer`, `samplePostQuestion`, `rules`, `acceptMessage`, `rejectMessage`, `siteCreditsPermanent`, `siteCredits` ) 
VALUES (1, 'Define your welcome message through the site messages panel...', 'Define your simm\'s welcome message through the site messages section of the Control Panel.', 'Define your ship\'s welcome message through the site messages section of the Control Panel.', 'Define your ship/starbase\'s history through the site messages section of the Control Panel.', 'Define the control panel welcome message through the site messages section of the Control Panel.', 'Members are expected to follow the rules and regulations of both the sim and fleet at all times, both in character and out of character. By continuing, you affirm that you will sim in a proper and adequate manner. Members who choose to make ultra short posts, post very infrequently, or post posts with explicit content (above PG-13) will be removed immediately, and by continuing, you agree to this. In addition, in compliance with the Children\'s Online Privacy Protection Act of 1998 (COPPA), we do not accept players under the age of 13.  Any players found to be under the age of 13 will be immediately removed without question.  By agreeing to these terms, you are also saying that you are above the age of 13.', 'Define your sample post question/scenario through the site messages section of the Control Panel.', 'Define your ship rules through the site messages section of the Control Panel.', 'Thank you for your interest in our sim. I would like to officially welcome you aboard as the newest member of our crew!', 'Thank you for your interest in our sim. Unfortunately, at this time, I cannot offer you a position onboard our sim.', 'Editing or removal of the following credits constitutes a material breach of the SMS Terms of Use outlined at the <a href=\"http://www.anodyne-productions.com/index.php?cat=sms&page=disclaimers\" target=\"_blank\">SMS Terms of Use</a> page.\r\n\r\nSMS 2 makes extensive use of the <a href=\"http://www.jquery.com\" target=\"_blank\">jQuery</a> Javascript library as well as multiple jQuery plugins. By default, SMS includes the <a href=\"http://ui.jquery.com/\" target=\"_blank\">jQuery UI</a>, <a href=\"http://p.sohei.org/jquery-plugins/clickmenu/\" target=\"_blank\">clickMenu</a>, <a href=\"http://www.crismancich.de/jquery/plugins/linkscrubber/\" target=\"_blank\">Link Scrubber</a>, <a href=\"http://mjijackson.com/shadowbox/\" target=\"_blank\">Shadowbox</a>, <a href=\"http://famspam.com/facebox\" target=\"_blank\">Facebox</a>, and <a href=\"http://plugins.jquery.com/project/reflect\" target=\"_blank\">Reflect jQuery</a>. More information about the versions and uses of the plugins can be obtained from the simm\'s webmaster.\r\n\r\nSMS 2 uses the open source browser detection library <a href=\"http://sourceforge.net/projects/phpsniff/\" target=\"_blank\">phpSniff</a> to check for various versions of browsers for maximum compatibility.\r\n\r\nThe SMS 2 Update notification system uses <a href=\"http://magpierss.sourceforge.net/\" target=\"_blank\">MagpieRSS</a> to parse the necessary XML file. Magpie is distributed under the GPL license. Questions and suggestions about MagpieRSS should be sent to <i>magpierss-general@lists.sf.net</i>.\r\n\r\nSMS 2 uses icons from the open source <a href=\"http://tango.freedesktop.org/Tango_Icon_Gallery\" target=\"_blank\">Tango Icon Library</a>.\r\n\r\nAdd and remove icons from the PI Diagona Pack created by <a href=\"http://pinvoke.com\" target=\"_blank\">Pinvoke.com</a>. Colorization by David VanScott.\r\n\r\nSMS 2 includes a stardate script developed by Phillip Sublett. Information on the script can be found at his site, <a href=\"http://TrekGuide.com/Stardates.htm\" target=\"_blank\">TrekGuide</a>.\r\n\r\nThe rank sets (DS9 Era Duty Uniform Style 2 and DS9 Era Dress Uniform Style 2) used in SMS 2 were created by Kuro-chan of <a href=\"http://www.kuro-rpg.net\" target=\"_blank\">Kuro-RPG</a>. Please do not copy or modify the images in any way, simply contact Kuro-chan and he will see to your rank needs.\r\n\r\n<a href=\"http://www.kuro-rpg.net/\" target=\"_blank\"><img src=\"images/kurorpg-banner.jpg\" border=\"0\" alt=\"Kuro-RPG\" /></a>', 'Please define your site credits in the Site Messages page...');" );

/* insert data into the news category table */
mysql_query( "INSERT INTO `sms_news_categories` (`catid`, `catName`, `catUserLevel`, `catVisible`) 
VALUES (1, 'General News', 1, 'y'),
(2, 'Simm Announcement', 2, 'y'),
(3, 'Website Update', 3, 'y'),
(4, 'Out of Character', 1, 'y');" );

/* insert the positions data */
mysql_query( "INSERT INTO `sms_positions` (`positionid`, `positionOrder`, `positionName`, `positionDesc`, `positionDept`, `positionType`, `positionOpen` ) 
VALUES (1, 0, 'Commanding Officer', 'Ultimately responsible for the ship and crew, the Commanding Officer is the most senior officer aboard a vessel. S/he is responsible for carrying out the orders of Starfleet, and for representing both Starfleet and the Federation.', 1, 'senior', 1),
(2, 1, 'Executive Officer', 'The liaison between captain and crew, the Executive Officer acts as the disciplinarian, personnel manager, advisor to the captain, and much more. S/he is also one of only two officers, along with the Chief Medical Officer, that can remove a Commanding Officer from duty.', 1, 'senior', 1),
(3, 2, 'Second Officer', 'At times the XO must assume command of a Starship or base, when this happens the XO needs the help of another officer to assume his/her role as XO. The second officer is not a stand alone position, but a role given to the highest ranked and trusted officer aboard. When required the Second Officer will assume the role of XO, or if needed CO, and performs their duties as listed, for as long as required.', 1, 'crew', 1),
(4, 10, 'Chief of the Boat', 'The seniormost Chief Petty Officer (including Senior and Master Chiefs), regardless of rating, is designated by the Commanding Officer as the Chief of the Boat (for vessels) or Command Chief (for starbases). In addition to his or her departmental responsibilities, the COB/CC performs the following duties: serves as a liaison between the Commanding Officer (or Executive Officer) and the enlisted crewmen; ensures enlisted crews understand Command policies; advises the Commanding Officer and Executive Officer regarding enlisted morale, and evaluates the quality of noncommissioned officer leadership, management, and supervisory training.\r\n\r\nThe COB/CC works with the other department heads, Chiefs, supervisors, and crewmen to insure discipline is equitably maintained, and the welfare, morale, and health needs of the enlisted personnel are met. The COB/CC is qualified to temporarily act as Commanding or Executive Officer if so ordered. ', 1, 'crew', 1),
(5, 15, 'Mission Advisor', 'Advises the Commanding Officer and Executive Officer on mission-specific areas of importance. Many times, the Mission Advisor knows just as much about the mission as the CO and XO do, if not even more. He or she also performs mission-specific tasks, and can take on any roles that a mission requires him or her to do. Concurrently holds another position, except in rare circumstances.', 1, 'crew', 1),
(6, 0, 'Chief Flight Control Officer', 'Originally known as helm, or Flight Control Officer, CONN incorporates two job, Navigation and flight control. A Flight Control Officer must always be present on the bridge of a starship. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. The Chief Flight Control Officer is the senior most CONN Officer aboard, serving as a Senior Officer, and chief of the personnel under him/her.', 2, 'senior', 1),
(7, 1, 'Assistant Chief Flight Control Officer', 'Originally known as helm, or Flight Control Officer, CONN incorporates two job, Navigation and flight control. A Flight Control Officer must always be present on the bridge of a starship. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. The Assistant Chief Flight Control Officer is the second senior most CONN Officer aboard and reports directly to the Chief Flight Control Officer.', 2, 'crew', 1),
(8, 5, 'Flight Control Officer', 'Originally know as helm, or Flight Control Officer, CONN incorporates two job, navigation and flight control. A Flight Control Officer must always be present on the bridge of a starship, and every vessel has a number of Flight Control Officers to allow shift rotations. S/he plots courses, supervises the computers piloting, corrects any flight deviations and pilots the ship manually when needed. Flight Control Officers report to the Chief Flight Control Officer.', 2, 'crew', 5),
(9, 10, 'Shuttle/Runabout Pilot', 'Responsible for piloting the various auxiliary craft (besides fighters), these pilots are responsible for transporting their passengers safely to and from locations that are inaccessible via the transporter.', 2, 'crew', 4),
(10, 0, 'Chief Strategic Operations Officer', 'The Chief Strategic Operations Officer is responsible for coordinating all Starfleet and allied assets in within their designated area of space, as well as tactical analysis (in the absence of a dedicated tactical department) and intelligence gathering (in the absence of a dedicated intelligence department).', 3, 'senior', 1),
(11, 1, 'Assistant Chief Strategic Operations Officer', 'The Assistant Chief Strategic Operations Officer is the second ranked officer in the Strategic Operations department. He or she answers to the Chief Strategic Operations Officer. He or she is responsible for coordinating Starfleet and allied assets within a designated area of space, as well as tactical analysis and intelligence gathering.', 3, 'crew', 1),
(12, 5, 'Strategic Operations Officer', 'The Strategic Operations Officer is part of the Strategic Operations department. He or she answers to the Chief Strategic Operations Officer. He or she is responsible for coordinating Starfleet and allied assets within a designated area of space, as well as tactical analysis and intelligence gathering.', 3, 'crew', 1),
(13, 0, 'Chief Security/Tactical Officer', 'The Chief Security Officer is called Chief of Security. Her/his duty is to ensure the safety of ship and crew. Some take it as their personal duty to protect the Commanding Officer/Executive Officer on away teams. She/he is also responsible for people under arrest and the safety of guests, liked or not.  S/he also is a department head and a member of the senior staff, responsible for all the crew members in her/his department and duty rosters. Security could be called the 24th century police force.\r\n\r\nThe Chief of Security role can also be combined with the Chief Tactical Officer position. ', 4, 'senior', 1),
(14, 1, 'Assistant Chief Security/Tactical Officer', 'The Assistant Chief Security Officer is sometimes called Deputy of Security. S/he assists the Chief of Security in the daily work; in issues regarding Security and any administrative matters.  If required the Deputy must be able to take command of the Security department. ', 4, 'crew', 1),
(15, 5, 'Security Officer', 'There are several Security Officers aboard each vessel. They are assigned to their duties by the Chief of Security and his/her Deputy and mostly guard sensitive areas, protect people, patrol, and handle other threats to the Federation.', 4, 'crew', 1),
(16, 10, 'Tactical Officer', 'The Tactical Officers are the vessels gunmen. They assist the Chief Tactical Officer by running and maintaining the numerous weapons systems aboard the ship/starbase, and analysis and tactical planning of current missions. Very often Tactical Officers are also trained in ground combat and small unit tactics.', 4, 'crew', 1),
(17, 15, 'Security Investigations Officer', 'The Security Investigations Officer is an Enlisted Officer. S/He fulfills the role of a special investigator or detective when dealing with Starfleet matters aboard ship or on a planet. Coordinates with the Chief Security Officer on all investigations as needed. The Security Investigations Officer reports to the Chief of Security.', 4, 'crew', 1),
(18, 20, 'Brig Officer', 'The Brig Officer is a Security Officer who has chosen to specialize in a specific role. S/he guards the brig and its cells. But there are other duties associated with this post as well. S/he is responsible for any prisoner transport, and the questioning of prisoners. Often Brig Officers have a good knowledge of forcefield technology, and are experts in escaping such confinements.', 4, 'crew', 1),
(19, 25, 'Master-At-Arms', 'The Master-at-Arms trains and supervises Security crewmen in departmental operations, repairs, and protocols; maintains duty assignments for all Security personnel; supervises weapons locker access and firearm deployment; and is qualified to temporarily act as Chief of Security if so ordered. The Master-at-Arms reports to the Chief of Security.', 4, 'crew', 1),
(20, 0, 'Chief Operations Officer', 'The Chief Operations Officer has the primary responsibility of ensuring that ship functions, such as the use of the lateral sensor array, do not interfere with one and another. S/he must prioritize resource allocations, so that the most critical activities can have every chance of success. If so required, s/he can curtail shipboard functions if s/he thinks they will interfere with the ship''s current mission or routine operations.\r\n\r\nThe Chief Operations Officer oversees the Operations department, and is a member of the senior staff. ', 5, 'senior', 1),
(21, 1, 'Assistant Chief Operations Officer', 'The Chief Operations Officer cannot man the bridge at all times. Extra personnel are needed to relive and maintain ship operations. The Operations Officers are thus assistants to the Chief, fulfilling his/her duties when required, and assuming the Operations consoles if required at any time.\r\n\r\nThe Assistant Chief Operations Officer is the second-in-command of the Operations Department, and can assume the role of Chief Operations Officer on a temporary or permanent basis if so needed. ', 5, 'crew', 1),
(22, 5, 'Operations Officer', 'The Chief Operations Officer cannot man the bridge at all times. Extra personnel are needed to relive and maintain ship operations. The Operations Officers are thus assistants to the Chief, fulfilling his/her duties when required, and assuming the Operations consoles if required at any time.\r\n\r\nThe Operations Officer reports to the Chief Operations Officer.', 5, 'crew', 1),
(23, 10, 'Quartermaster', 'Replicator usage can allow the fabrication of nearly any critical mission part, but large-scale replication is not considered energy-efficient except in emergency situations. However, in such situations, power usage is strictly limited, so it is unwise to depend upon the availability of replicated spare parts.\r\n\r\nThus a ship/facility must maintain a significant stock of spare parts in inventory at all times. The Quartermaster is the person responsible for the requesting of parts from Starfleet and maintaining the stock and inventory of all spare parts. All request for supplies are passed to the Quartermaster, who check and send the final request to the XO for final approval. A good Quartermaster is never caught short on supplies.\r\n\r\nThe Quartermaster trains and supervises crewmen in Bridge operations, repairs, and protocols and sets the agenda for instruction in general ship and starbase operations for the Boatswain''s Mate; maintains the ship''s log, the ship''s clock, and watch and duty assignments for all Bridge personnel; may assume any Bridge (i.e. CONN) or Operations role (i.e. transporter) as required; and is qualified to temporarily act as Commanding or Executive Officer if so ordered.\r\n\r\nQuartermasters ensure that all officers and crew perform their duties consistent with Starfleet directives. The Quartermaster reports to the Executive Officer.', 5, 'crew', 1),
(24, 12, 'Boatswain', 'Each vessel and base has one Warrant Officer (or Chief Warrant Officer) who holds the position of Boatswain. The Boatswain (pronounced and also written \"Bosun\" or \"Bos\'n\") trains and supervises personnel (including both the ship\'s company or base personnel as well as passengers or vessels) in general ship and base operations, repairs, and protocols; maintains duty assignments for all Operations personnel; sets the agenda for instruction in general ship and base operations; supervises auxiliary and utility service personnel and daily ship or base maintenance; coordinates all personnel cross-trained in damage control operations and supervises damage control and emergency operations; may assume any Bridge or Operations role as required; and is qualified to temporarily act at Operations if so ordered.\r\n\r\nThe Boatswain reports to the Chief Operations Officer.', 5, 'crew', 1),
(25, 0, 'Chief Engineering Officer', 'The Chief Engineer is responsible for the condition of all systems and equipment on board a Starfleet ship or facility. S/he oversees maintenance, repairs and upgrades of all equipment. S/he is also responsible for the many repairs teams during crisis situations.\r\n\r\nThe Chief Engineer is not only the department head but also a senior officer, responsible for all the crew members in her/his department and maintenance of the duty rosters.', 6, 'senior', 1),
(26, 1, 'Assistant Chief Engineering Officer', 'The Assistant Chief Engineer assists the Chief Engineer in the daily work; in issues regarding mechanical, administrative matters and co-ordinating repairs with other departments.\r\n\r\nIf so required, the Assistant Chief Engineer must be able to take over as Chief Engineer, and thus must be versed in current information regarding the ship or facility. ', 6, 'crew', 1),
(27, 5, 'Engineering Officer', 'There are several non-specialized engineers aboard of each vessel. They are assigned to their duties by the Chief Engineer and his Assistant, performing a number of different tasks as required, i.e. general maintenance and repair. Generally, engineers as assigned to more specialized engineering person to assist in there work is so requested by the specialized engineer.', 6, 'crew', 1),
(28, 10, 'Communications Specialist', 'The Communications Specialist is a specialized engineer. Communication aboard a ship or facility takes two basic forms, voice and data. Both are handled by the onboard computer system and dedicated hardware. The vastness and complexity of this system requires a dedicated team to maintain the system.\r\n\r\nThe Communications Specialist is the officer in charge of this team, which is made up from NCO personnel, assigned to the team by the Assistant and Chief Engineer. The Communications Specialist reports to the Asst. and Chief Engineer.', 6, 'crew', 1),
(29, 15, 'Computer Systems Specialist', 'The Computer Systems Specialist is a specialized Engineer. The new generation of Computer systems are highly developed. This system needs much maintenance and the Computer Systems Specialist was introduced to relieve the Science Officer, whose duty this was in the very early days.\r\n\r\nA small team is assigned to the Computer Systems Specialist, which is made up from NCO personnel assigned by the Assistant and Chief Engineer. The Computer Systems Specialist reports to the Assistant and Chief Engineer. ', 6, 'crew', 1),
(30, 20, 'Damage Control Specialist', 'The Damage Control Specialist is a specialized Engineer. The Damage Control Specialist controls all damage control aboard the ship when it gets damaged in battle. S/he oversees all damage repair aboard the ship, and coordinates repair teams on the smaller jobs so the Chief Engineer can worry about other matters.\r\n\r\nA small team is assigned to the Damage Control Specialist which is made up from NCO personnel assigned by the Assistant and Chief Engineer. The Damage Control Specialist reports to the Assistant and Chief Engineer. ', 6, 'crew', 1),
(31, 25, 'Matter/Energy Systems Specialist', 'The Matter / Energy Systems Specialist is a specialized Engineer. All aspect of matter energy transfers with the sole exception of the warp drive systems are handled by the Matter/Energy Systems Specialist. Such areas involved are transporter and replicator systems. The Matter/Energy Systems Specialist is the Officer in charge of a small team, which is made up from NCO personnel, assigned by the Assistant and Chief Engineer. The Matter/Energy Systems Specialist reports to the Assistant and Chief Engineer.', 6, 'crew', 1),
(32, 30, 'Propulsion Specialist', 'Specializing in impulse and warp propulsion, these specialists are often specific to even a single class of ship due to the complexity of warp and impulse systems.', 6, 'crew', 1),
(33, 35, 'Structural/Environmental Systems Specialist', 'The Structural and Environmental Systems Specialist is a specialised Engineer. From a small ship/facility to a large one, all requires constant monitoring. The hull, bulkheads, walls, Jeffrey''s tubes, turbolifts, structural integrity field, internal dampening field, and environmental systems are all monitored and maintained by this officer and his/her team.\r\n\r\nThe team assigned to the Structural and Environmental Systems Specialist is made up from NCO personnel, assigned by the Assistant and Chief Engineer. The Structural and Environmental Systems Specialist reports to the Asst and Chief Engineer. ', 6, 'crew', 1),
(34, 40, 'Transporter Chief', 'The Transporter Chief is responsible for all transports to and from other ships and any planetary bodies. When transporting is not going on, the Transporter Chief is responsible for keeping the transporters running at peak efficiency.\r\n\r\nThe team assigned to the Transporter Chief is made up from NCO personnel, assigned by the Assistant and Chief Engineer. The Transporter Chief reports to the Assistant and Chief Engineer. ', 6, 'crew', 1),
(35, 0, 'Chief Science Officer', 'The Chief Science Officer is responsible for all the scientific data the ship/facility collects, and the distribution of such data to specific section within the department for analysis. S/he is also responsible with providing the ship''s captain with scientific information needed for command decisions.\r\n\r\nS/he also is a department head and a member of the Senior Staff and responsible for all the crew members in her/his department and duty rosters.', 7, 'senior', 1),
(36, 1, 'Assistant Chief Science Officer', 'The Assistant Chief Science Officer assists Chief Science Officer in all areas, such as administration, and analysis of scientific data. The Assistant often take part in specific analysis of important data along with the Chief Science Officer, however spends most time overseeing current project and their section heads.', 7, 'crew', 1),
(37, 5, 'Science Officer', 'There are several general Science Officers aboard each vessel. They are assigned to their duties by the Chief Science Officer and his Assistant. Assignments include work for the Specialized Section heads, as well as duties for work being carried out by the Chief and Assistant.', 7, 'crew', 1),
(38, 10, 'Alien Archaeologist/Anthropologist', 'Specialized Science Officer in charge of the Alien Culture Section. This role involves the study of all newly discovered alien species and life forms, from the long dead to thriving. There knowledge also involves current known alien species. Has close ties to the Historian.\r\n\r\nAnswers to the Chief Science Officer and Assistant Chief Science Officer. ', 7, 'crew', 1),
(39, 15, 'Biologist', 'Specialized Science Officer in charge of the Biology Section. This role entails the study of biology, botany, zoology and many more Life Sciences. On larger ships there many be a number of Science Officers within this section, under the lead of the Biologist.', 7, 'crew', 1),
(40, 20, 'Language Specialist', 'Specialized Communications Officer in charge of the Linguistics section. This role involves the study of new and old languages and text in an attempt to better understand and interpret their meaning.\r\n\r\nAnswers to the Chief and Assistant Chief Communications Officer. ', 7, 'crew', 1),
(41, 25, 'Stellar Cartographer', 'Specialized Science Officer in charge of the Stellar Cartography bay. This role entails the mapping of all spatial phenomenon, and the implications of such phenomenon. Has close ties with the Physicist and Astrometrics Officer.', 7, 'crew', 1),
(42, 0, 'Chief Medical Officer', 'The Chief Medical Officer is responsible for the physical health of the entire crew, but does more than patch up injured crew members. His/her function is to ensure that they do not get sick or injured to begin with, and to this end monitors their health and conditioning with regular check ups. If necessary, the Chief Medical Officer can remove anyone from duty, even a Commanding Officer. Besides this s/he is available to provide medical advice to any individual who requests it.\r\n\r\nAdditionally the Chief is also responsible for all aspect of the medical deck, such as the Medical labs, Surgical suites and Dentistry labs.\r\n\r\nS/he also is a department head and a member of the Senior Staff and responsible for all the crew members in her/his department and duty rosters. ', 8, 'senior', 1),
(43, 1, 'Chief Counselor', 'Because of their training in psychology, technically the ship''s/facility''s Counselor is considered part of Starfleet medical. The Counselor is responsible both for advising the Commanding Officer in dealing with other people and races, and in helping crew members with personal, psychological, and emotional problems.\r\n\r\nThe Chief Counselor is considered a member of the Senior Staff. S/he is responsible for the crew in his/her department. The Chief Counselor is the Counselor with the highest rank and most experience. ', 8, 'senior', 1),
(44, 2, 'Assistant Chief Medical Officer', 'A starship or facility has numerous personnel aboard, and thus the Chief Medical Officer cannot be expect to do all the work required. The Asst. Chief Medical Officer assists Chief in all areas, such as administration, and application of medical care.', 8, 'crew', 1),
(45, 5, 'Medical Officer', 'Medical Officer undertake the majority of the work aboard the ship/facility, examining the crew, and administering medical care under the instruction of the Chief Medical Officer and Assistant Chief Medical Officer also run the other Medical areas not directly overseen by the Chief Medical Officer.', 8, 'crew', 1),
(46, 10, 'Counselor', 'Because of their training in psychology, technically the ship''s/facility''s Counselor is considered part of Starfleet medical. The Counselor is responsible both for advising the Commanding Officer in dealing with other people and races, and in helping crew members with personal, psychological, and emotional problems.', 8, 'crew', 1),
(47, 15, 'Nurse', 'Nurses are trained in basic medical care, and are capable of dealing with less serious medical cases. In more serious matters the nurse assist the medical officer in the examination and administration of medical care, be this injecting required drugs, or simply assuring the injured party that they will be ok. The Nurses also maintain the medical wards, overseeing the patients and ensuring they are receiving medication and care as instructed by the Medical Officer.', 8, 'crew', 2),
(48, 20, 'Morale Officer', 'Responsible for keeping the morale of the crew high. Delivers regular reports on morale to the Executive Officer. The Morale Officer plans activities that will keep the crew''s morale and demeanor up. If any crew member is having problems, the Morale Officer can assist that crew member.', 8, 'crew', 1),
(49, 0, 'Chief Intelligence Officer', 'Responsible for managing the intelligence department in its various facets, the Chief Intelligence officer often assists the Strategic Operations officer with information gathering and analysis, and then acts as a channel of information to the CO and bridge crew during combat situations.', 9, 'senior', 1),
(50, 1, 'Assistant Chief Intelligence Officer', 'Responsible for aiding the Chief Intelligence Officer in managing the intelligence department in its various facets, often assisting the Strategic Operations officer with information gathering and analysis.', 9, 'crew', 1),
(51, 5, 'Intelligence Officer', 'Responsible for gathering intelligence, an Intelligence officer has the patience to read through a database for hours on end, and the cunning to coax information from an unwilling giver. S/he must provide this information to the Chief Intelligence officer as it becomes needed.', 9, 'crew', 2),
(52, 10, 'Infiltration Specialist', 'The Infiltration Specialist is trained the arts of covert operations and infiltration. They are trained to get into and out of enemy installations, territory, etc. Once in, they can gather intel, or if needed plant explosives, and even in times of war capture of enemy personnel. The Infiltration Specialist reports to the Chief Intelligence Officer.', 9, 'crew', 1),
(53, 15, 'Encryption Specialist', 'This NCO takes submitted Intelligence reports and runs them through algorithms, checking for keywords that denote mistyped classification and then puts the report into crypto form and sends them through the proper channels of communication to either on board ship consoles or off board to who ever needs to receive it. The Encryption Specialist reports to the Chief Intelligence Officer.', 9, 'crew', 1),
(54, 0, 'Chief Diplomatic Officer', 'The Diplomatic Officer of each vessel/base must be familiar with a variety of areas: history, religion, politics, economics, and military, and understand how they affect potential threats. A wide range of operations can occur in response to these areas and threats. These operations occur within three general states of being: peacetime competition, conflict and war.\r\n\r\nS/he must be equally flexible and demonstrate initiative, agility, depth, synchronization, and improvisation to provide responsive legal services to his/her Commanding Officer as well a diplomatic advise on current status of an Alien Species both aligned and non aligned to the Federation.\r\n\r\nThe Chief Diplomatic Officer is in charge of the Diplomatic Corps Detachment. He or she oversees the operation of it, as well as makes sure everything in that department is carried out according to Starfleet Regulations. ', 10, 'senior', 1),
(55, 1, 'Assistant Chief Diplomatic Officer', 'The Diplomatic Officer of each vessel/base must be familiar with a variety of areas: history, religion, politics, economics, and military, and understand how they affect potential threats. A wide range of operations can occur in response to these areas and threats. These operations occur within three general states of being: peacetime competition, conflict and war.\r\n\r\nS/he must be equally flexible and demonstrate initiative, agility, depth, synchronization, and improvisation to provide responsive legal services to his/her Commanding Officer aiding in official functions as prescribed by protocol, performing administrative duties, and other tasks as directed by the Chief Diplomatic Officer, as well a diplomatic advise on current status of an Alien Species both aligned and non aligned to the Federation.\r\n\r\nThe Assistant Chief Diplomatic Officer is the second-in-command of the Diplomatic Corps Detachment. If necessary, he or she can take the place of the Chief Diplomatic Officer on a temporary or permanent basis.', 10, 'crew', 1),
(56, 5, 'Diplomatic Officer', 'The Diplomatic Officer of each vessel/base must be familiar with a variety of areas: history, religion, politics, economics, and military, and understand how they affect potential threats. A wide range of operations can occur in response to these areas and threats. These operations occur within three general states of being: peacetime competition, conflict and war.\r\n\r\nS/he must be equally flexible and demonstrate initiative, agility, depth, synchronization, and improvisation to provide responsive legal services to his/her Commanding Officer aiding in official functions as prescribed by protocol, performing administrative duties, and other tasks as directed by the Chief Diplomatic Officer and/or Assistant Chief Diplomatic Officer as well a diplomatic advice on current status of an Alien Species both aligned and non aligned to the Federation. ', 10, 'crew', 1),
(57, 10, 'Diplomatic Corpsman', 'The Diplomatic Corpsman is a special position reserved for enlisted officers who wish to study diplomacy, and aid the department in its mission. Their duties consist of, but are not limited to, aiding Diplomatic Officers and Diplomat''s Aide in the construction of various legal documents, researching diplomatic archives, attending and aiding in the preparation for diplomatic functions, and other tasks as prescribed by the Chief Diplomatic Officer and/or Assistant Chief Diplomatic Officer. These individuals are qualified to undertake some of the responsibilities of a Diplomatic Officer, as their training are far less in-depth. They are, however, able to, and adequately trained to function as a paralegal when such services are required by a vessel/base''s crew.', 10, 'crew', 1),
(58, 15, 'Diplomat''s Aide', 'S/he responds to the Ship/Base''s Chief Diplomatic Officer, and is required to be able to stand in and run the Diplomatic Department as required should the Chief Diplomatic Officer be absent for any reason.\r\n\r\nThe Aide must therefore be versed in all Diplomatic information regarding the current status of the Federation and its aligned and non aligned neighbours.', 10, 'crew', 1),
(59, 0, 'Marine Commanding Officer', 'The Marine CO is responsible for all the Marine personnel assigned to the ship/facility. S/he is in required to take command of any special ground operations and lease such actions with security. The Marines could be called the 24th century commandos.\r\n\r\nThe CO can range from a Second Lieutenant on a small ship to a Lieutenant Colonel on a large facility or colony. Charged with the training, condition and tactical leadership of the Marine compliment, they are a member of the senior staff.\r\n\r\nAnswers to the Commanding Officer of the ship/facility. ', 11, 'senior', 1),
(60, 1, 'Marine Executive Officer', 'The Executive Officer of the Marines, works like any Asst. Department head, removing some of the work load from the Marine CO and if the need arises taking on the role of Marine CO. S/he oversees the regular duties of the Marines, from regular drills to equipment training, assignment and supply request to the ship/facilities Materials Officer.\r\n\r\nAnswers to the Marine Commanding Officer.', 11, 'crew', 1),
(61, 5, 'First Sergeant', 'The First Sergeant is the highest ranked Enlisted marine. S/He is in charge of all of the marine enlisted affairs in the detachment. They assist the Company or Detachment Commander as their Executive Officer would. They act as a bridge, closing the gap between the NCO''s and the Officers.\r\n\r\nAnswers To Marine Commanding Officer.', 11, 'crew', 1),
(62, 10, 'Marine', 'Serving within a squad, the marine is trained in a variety of means of combat, from melee to ranged projectile to sniping.', 11, 'crew', 99),
(63, 0, 'Wing Commander', 'Commander of all the squadrons within the wing.', 12, 'senior', 1),
(64, 1, 'Wing Executive Officer', 'The first officer of the Wing.', 12, 'crew', 1),
(65, 5, 'Squadron Leader', 'Leader of a starfighter squadron.', 12, 'crew', 1),
(66, 10, 'Squadron Pilot', 'A pilot in the starfighter squadron', 12, 'crew', 1),
(67, 0, 'Chef', 'Responsible for preparing all meals served in the Mess Hall and for the food during any diplomatic functions that may be held onboard.', 13, 'crew', 1),
(68, 1, 'Other', '', 13, 'crew', 1);" );

/* insert the ranks data */
mysql_query( "INSERT INTO `sms_ranks` (`rankid`, `rankOrder`, `rankName`, `rankImage`, `rankShortName`, `rankType`, `rankDisplay`, `rankClass`) 
VALUES (1, 1, 'Fleet Admiral', 'Starfleet/r-a5.png', 'FADM', 1, 'y', 1),
(2, 1, 'Fleet Admiral', 'Starfleet/y-a5.png', 'FADM', 1, 'y', 2),
(3, 1, 'Fleet Admiral', 'Starfleet/t-a5.png', 'FADM', 1, 'y', 3),
(4, 1, 'Fleet Admiral', 'Starfleet/s-a5.png', 'FADM', 1, 'y', 4),
(5, 1, 'Fleet Admiral', 'Starfleet/v-a5.png', 'FADM', 1, 'y', 5),
(6, 1, 'Field Marshal', 'Marine/g-a5.png', 'FMSL', 1, 'y', 6),
(7, 1, 'Fleet Admiral', 'Starfleet/c-a5.png', 'FADM', 1, 'y', 7),

(8, 2, 'Admiral', 'Starfleet/r-a4.png', 'ADM', 1, 'y', 1),
(9, 2, 'Admiral', 'Starfleet/y-a4.png', 'ADM', 1, 'y', 2),
(10, 2, 'Admiral', 'Starfleet/t-a4.png', 'ADM', 1, 'y', 3),
(11, 2, 'Admiral', 'Starfleet/s-a4.png', 'ADM', 1, 'y', 4),
(12, 2, 'Admiral', 'Starfleet/v-a4.png', 'ADM', 1, 'y', 5),
(13, 2, 'General', 'Marine/g-a4.png', 'GEN', 1, 'y', 6),
(14, 2, 'Admiral', 'Starfleet/c-a4.png', 'ADM', 1, 'y', 7),

(15, 3, 'Vice Admiral', 'Starfleet/r-a3.png', 'VADM', 1, 'y', 1),
(16, 3, 'Vice Admiral', 'Starfleet/y-a3.png', 'VADM', 1, 'y', 2),
(17, 3, 'Vice Admiral', 'Starfleet/t-a3.png', 'VADM', 1, 'y', 3),
(18, 3, 'Vice Admiral', 'Starfleet/s-a3.png', 'VADM', 1, 'y', 4),
(19, 3, 'Vice Admiral', 'Starfleet/v-a3.png', 'VADM', 1, 'y', 5),
(20, 3, 'Lieutenant General', 'Marine/g-a3.png', 'LTGEN', 1, 'y', 6),
(21, 3, 'Vice Admiral', 'Starfleet/c-a3.png', 'VADM', 1, 'y', 7),

(22, 4, 'Rear Admiral', 'Starfleet/r-a2.png', 'RADM', 1, 'y', 1),
(23, 4, 'Rear Admiral', 'Starfleet/y-a2.png', 'RADM', 1, 'y', 2),
(24, 4, 'Rear Admiral', 'Starfleet/t-a2.png', 'RADM', 1, 'y', 3),
(25, 4, 'Rear Admiral', 'Starfleet/s-a2.png', 'RADM', 1, 'y', 4),
(26, 4, 'Rear Admiral', 'Starfleet/v-a2.png', 'RADM', 1, 'y', 5),
(27, 4, 'Major General', 'Marine/g-a2.png', 'MAJGEN', 1, 'y', 6),
(28, 4, 'Rear Admiral', 'Starfleet/c-a2.png', 'RADM', 1, 'y', 7),

(29, 5, 'Commodore', 'Starfleet/r-a1.png', 'COM', 1, 'y', 1),
(30, 5, 'Commodore', 'Starfleet/y-a1.png', 'COM', 1, 'y', 2),
(31, 5, 'Commodore', 'Starfleet/t-a1.png', 'COM', 1, 'y', 3),
(32, 5, 'Commodore', 'Starfleet/s-a1.png', 'COM', 1, 'y', 4),
(33, 5, 'Commodore', 'Starfleet/v-a1.png', 'COM', 1, 'y', 5),
(34, 5, 'Brigadier General', 'Marine/g-a1.png', 'BGEN', 1, 'y', 6),
(35, 5, 'Commodore', 'Starfleet/c-a1.png', 'COM', 1, 'y', 7),

(36, 6, 'Captain', 'Starfleet/r-o6.png', 'CAPT', 1, 'y', 1),
(37, 6, 'Captain', 'Starfleet/y-o6.png', 'CAPT', 1, 'y', 2),
(38, 6, 'Captain', 'Starfleet/t-o6.png', 'CAPT', 1, 'y', 3),
(39, 6, 'Captain', 'Starfleet/s-o6.png', 'CAPT', 1, 'y', 4),
(40, 6, 'Captain', 'Starfleet/v-o6.png', 'CAPT', 1, 'y', 5),
(41, 6, 'Colonel', 'Marine/g-o6.png', 'COL', 1, 'y', 6),
(42, 6, 'Captain', 'Starfleet/c-o6.png', 'CAPT', 1, 'y', 7),

(43, 7, 'Commander', 'Starfleet/r-o5.png', 'CMDR', 1, 'y', 1),
(44, 7, 'Commander', 'Starfleet/y-o5.png', 'CMDR', 1, 'y', 2),
(45, 7, 'Commander', 'Starfleet/t-o5.png', 'CMDR', 1, 'y', 3),
(46, 7, 'Commander', 'Starfleet/s-o5.png', 'CMDR', 1, 'y', 4),
(47, 7, 'Commander', 'Starfleet/v-o5.png', 'CMDR', 1, 'y', 5),
(48, 7, 'Lieutenant Colonel', 'Marine/g-o5.png', 'LTCOL', 1, 'y', 6),
(49, 7, 'Commander', 'Starfleet/c-o5.png', 'CMDR', 1, 'y', 7),

(50, 8, 'Lieutenant Commander', 'Starfleet/r-o4.png', 'LTCMDR', 1, 'y', 1),
(51, 8, 'Lieutenant Commander', 'Starfleet/y-o4.png', 'LTCMDR', 1, 'y', 2),
(52, 8, 'Lieutenant Commander', 'Starfleet/t-o4.png', 'LTCMDR', 1, 'y', 3),
(53, 8, 'Lieutenant Commander', 'Starfleet/s-o4.png', 'LTCMDR', 1, 'y', 4),
(54, 8, 'Lieutenant Commander', 'Starfleet/v-o4.png', 'LTCMDR', 1, 'y', 5),
(55, 8, 'Major', 'Marine/g-o4.png', 'MAJ', 1, 'y', 6),
(56, 8, 'Lieutenant Commander', 'Starfleet/c-o4.png', 'LTCMDR', 1, 'y', 7),

(57, 9, 'Lieutenant', 'Starfleet/r-o3.png', 'LT', 1, 'y', 1),
(58, 9, 'Lieutenant', 'Starfleet/y-o3.png', 'LT', 1, 'y', 2),
(59, 9, 'Lieutenant', 'Starfleet/t-o3.png', 'LT', 1, 'y', 3),
(60, 9, 'Lieutenant', 'Starfleet/s-o3.png', 'LT', 1, 'y', 4),
(61, 9, 'Lieutenant', 'Starfleet/v-o3.png', 'LT', 1, 'y', 5),
(62, 9, 'Marine Captain', 'Marine/g-o3.png', 'CAPT', 1, 'y', 6),
(63, 9, 'Lieutenant', 'Starfleet/c-o3.png', 'LT', 1, 'y', 7),

(64, 10, 'Lieutenant JG', 'Starfleet/r-o2.png', 'LT(JG)', 1, 'y', 1),
(65, 10, 'Lieutenant JG', 'Starfleet/y-o2.png', 'LT(JG)', 1, 'y', 2),
(66, 10, 'Lieutenant JG', 'Starfleet/t-o2.png', 'LT(JG)', 1, 'y', 3),
(67, 10, 'Lieutenant JG', 'Starfleet/s-o2.png', 'LT(JG)', 1, 'y', 4),
(68, 10, 'Lieutenant JG', 'Starfleet/v-o2.png', 'LT(JG)', 1, 'y', 5),
(69, 10, '1st Lieutenant', 'Marine/g-o2.png', '1LT', 1, 'y', 6),
(70, 10, 'Lieutenant JG', 'Starfleet/c-o2.png', 'LT(JG)', 1, 'y', 7),

(71, 11, 'Ensign', 'Starfleet/r-o1.png', 'ENS', 1, 'y', 1),
(72, 11, 'Ensign', 'Starfleet/y-o1.png', 'ENS', 1, 'y', 2),
(73, 11, 'Ensign', 'Starfleet/t-o1.png', 'ENS', 1, 'y', 3),
(74, 11, 'Ensign', 'Starfleet/s-o1.png', 'ENS', 1, 'y', 4),
(75, 11, 'Ensign', 'Starfleet/v-o1.png', 'ENS', 1, 'y', 5),
(76, 11, '2nd Lieutenant', 'Marine/g-o1.png', '2LT', 1, 'y', 6),
(77, 11, 'Ensign', 'Starfleet/c-o1.png', 'ENS', 1, 'y', 7),

(78, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/r-w4.png', 'CWO1', 1, 'y', 1),
(79, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/y-w4.png', 'CWO1', 1, 'y', 2),
(80, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/t-w4.png', 'CWO1', 1, 'y', 3),
(81, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/s-w4.png', 'CWO1', 1, 'y', 4),
(82, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/v-w4.png', 'CWO1', 1, 'y', 5),
(83, 12, 'Chief Warrant Officer 1st Class', 'Marine/g-w4.png', 'CWO1', 1, 'y', 6),
(84, 12, 'Chief Warrant Officer 1st Class', 'Starfleet/c-w4.png', 'CWO1', 1, 'y', 7),

(85, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/r-w3.png', 'CWO2', 1, 'y', 1),
(86, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/y-w3.png', 'CWO2', 1, 'y', 2),
(87, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/t-w3.png', 'CWO2', 1, 'y', 3),
(88, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/s-w3.png', 'CWO2', 1, 'y', 4),
(89, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/v-w3.png', 'CWO2', 1, 'y', 5),
(90, 13, 'Chief Warrant Officer 2nd Class', 'Marine/g-w3.png', 'CWO2', 1, 'y', 6),
(91, 13, 'Chief Warrant Officer 2nd Class', 'Starfleet/c-w3.png', 'CWO2', 1, 'y', 7),

(92, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/r-w2.png', 'CWO3', 1, 'y', 1),
(93, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/y-w2.png', 'CWO3', 1, 'y', 2),
(94, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/t-w2.png', 'CWO3', 1, 'y', 3),
(95, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/s-w2.png', 'CWO3', 1, 'y', 4),
(96, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/v-w2.png', 'CWO3', 1, 'y', 5),
(97, 14, 'Chief Warrant Officer 3rd Class', 'Marine/g-w2.png', 'CWO3', 1, 'y', 6),
(98, 14, 'Chief Warrant Officer 3rd Class', 'Starfleet/c-w2.png', 'CWO3', 1, 'y', 7),

(99, 15, 'Warrant Officer', 'Starfleet/r-w1.png', 'WO', 1, 'y', 1),
(100, 15, 'Warrant Officer', 'Starfleet/y-w1.png', 'WO', 1, 'y', 2),
(101, 15, 'Warrant Officer', 'Starfleet/t-w1.png', 'WO', 1, 'y', 3),
(102, 15, 'Warrant Officer', 'Starfleet/s-w1.png', 'WO', 1, 'y', 4),
(103, 15, 'Warrant Officer', 'Starfleet/v-w1.png', 'WO', 1, 'y', 5),
(104, 15, 'Warrant Officer', 'Marine/g-w1.png', 'WO', 1, 'y', 6),
(105, 15, 'Warrant Officer', 'Starfleet/c-w1.png', 'WO', 1, 'y', 7),

(106, 16, 'Master Chief Petty Officer', 'Starfleet/r-e9.png', 'MCPO', 1, 'y', 1),
(107, 16, 'Master Chief Petty Officer', 'Starfleet/y-e9.png', 'MCPO', 1, 'y', 2),
(108, 16, 'Master Chief Petty Officer', 'Starfleet/t-e9.png', 'MCPO', 1, 'y', 3),
(109, 16, 'Master Chief Petty Officer', 'Starfleet/s-e9.png', 'MCPO', 1, 'y', 4),
(110, 16, 'Master Chief Petty Officer', 'Starfleet/v-e9.png', 'MCPO', 1, 'y', 5),
(111, 16, 'Sergeant Major', 'Marine/g-e9.png', 'SGTMAJ', 1, 'y', 6),
(112, 16, 'Master Chief Petty Officer', 'Starfleet/c-e9.png', 'MCPO', 1, 'y', 7),

(113, 17, 'Senior Chief Petty Officer', 'Starfleet/r-e8.png', 'SCPO', 1, 'y', 1),
(114, 17, 'Senior Chief Petty Officer', 'Starfleet/y-e8.png', 'SCPO', 1, 'y', 2),
(115, 17, 'Senior Chief Petty Officer', 'Starfleet/t-e8.png', 'SCPO', 1, 'y', 3),
(116, 17, 'Senior Chief Petty Officer', 'Starfleet/s-e8.png', 'SCPO', 1, 'y', 4),
(117, 17, 'Senior Chief Petty Officer', 'Starfleet/v-e8.png', 'SCPO', 1, 'y', 5),
(118, 17, 'Master Sergeant', 'Marine/g-e8.png', 'MSGT', 1, 'y', 6),
(119, 17, 'Senior Chief Petty Officer', 'Starfleet/c-e8.png', 'SCPO', 1, 'y', 7),

(120, 18, 'Chief Petty Officer', 'Starfleet/r-e7.png', 'CPO', 1, 'y', 1),
(121, 18, 'Chief Petty Officer', 'Starfleet/y-e7.png', 'CPO', 1, 'y', 2),
(122, 18, 'Chief Petty Officer', 'Starfleet/t-e7.png', 'CPO', 1, 'y', 3),
(123, 18, 'Chief Petty Officer', 'Starfleet/s-e7.png', 'CPO', 1, 'y', 4),
(124, 18, 'Chief Petty Officer', 'Starfleet/v-e7.png', 'CPO', 1, 'y', 5),
(125, 18, 'Gunnery Sergeant', 'Marine/g-e7.png', 'GYSGT', 1, 'y', 6),
(126, 18, 'Chief Petty Officer', 'Starfleet/c-e7.png', 'CPO', 1, 'y', 7),

(127, 19, 'Petty Officer 1st Class', 'Starfleet/r-e6.png', 'PO1', 1, 'y', 1),
(128, 19, 'Petty Officer 1st Class', 'Starfleet/y-e6.png', 'PO1', 1, 'y', 2),
(129, 19, 'Petty Officer 1st Class', 'Starfleet/t-e6.png', 'PO1', 1, 'y', 3),
(130, 19, 'Petty Officer 1st Class', 'Starfleet/s-e6.png', 'PO1', 1, 'y', 4),
(131, 19, 'Petty Officer 1st Class', 'Starfleet/v-e6.png', 'PO1', 1, 'y', 5),
(132, 19, 'Staff Sergeant', 'Marine/g-e6.png', 'SSGT', 1, 'y', 6),
(133, 19, 'Petty Officer 1st Class', 'Starfleet/c-e6.png', 'PO1', 1, 'y', 7),

(134, 20, 'Petty Officer 2nd Class', 'Starfleet/r-e5.png', 'PO2', 1, 'y', 1),
(135, 20, 'Petty Officer 2nd Class', 'Starfleet/y-e5.png', 'PO2', 1, 'y', 2),
(136, 20, 'Petty Officer 2nd Class', 'Starfleet/t-e5.png', 'PO2', 1, 'y', 3),
(137, 20, 'Petty Officer 2nd Class', 'Starfleet/s-e5.png', 'PO2', 1, 'y', 4),
(138, 20, 'Petty Officer 2nd Class', 'Starfleet/v-e5.png', 'PO2', 1, 'y', 5),
(139, 20, 'Sergeant', 'Marine/g-e5.png', 'SGT', 1, 'y', 6),
(140, 20, 'Petty Officer 2nd Class', 'Starfleet/c-e5.png', 'PO2', 1, 'y', 7),

(141, 21, 'Petty Officer 3rd Class', 'Starfleet/r-e4.png', 'PO3', 1, 'y', 1),
(142, 21, 'Petty Officer 3rd Class', 'Starfleet/y-e4.png', 'PO3', 1, 'y', 2),
(143, 21, 'Petty Officer 3rd Class', 'Starfleet/t-e4.png', 'PO3', 1, 'y', 3),
(144, 21, 'Petty Officer 3rd Class', 'Starfleet/s-e4.png', 'PO3', 1, 'y', 4),
(145, 21, 'Petty Officer 3rd Class', 'Starfleet/v-e4.png', 'PO3', 1, 'y', 5),
(146, 21, 'Corporal', 'Marine/g-e4.png', 'CPL', 1, 'y', 6),
(147, 21, 'Petty Officer 3rd Class', 'Starfleet/c-e4.png', 'PO3', 1, 'y', 7),

(148, 22, 'Crewman', 'Starfleet/r-e3.png', 'CN', 1, 'y', 1),
(149, 22, 'Crewman', 'Starfleet/y-e3.png', 'CN', 1, 'y', 2),
(150, 22, 'Crewman', 'Starfleet/t-e3.png', 'CN', 1, 'y', 3),
(151, 22, 'Crewman', 'Starfleet/s-e3.png', 'CN', 1, 'y', 4),
(152, 22, 'Crewman', 'Starfleet/v-e3.png', 'CN', 1, 'y', 5),
(153, 22, 'Lance Corporal', 'Marine/g-e3.png', 'LCPL', 1, 'y', 6),
(154, 22, 'Crewman', 'Starfleet/c-e3.png', 'CN', 1, 'y', 7),

(155, 23, 'Crewman Apprentice', 'Starfleet/r-e2.png', 'CA', 1, 'y', 1),
(156, 23, 'Crewman Apprentice', 'Starfleet/y-e2.png', 'CA', 1, 'y', 2),
(157, 23, 'Crewman Apprentice', 'Starfleet/t-e2.png', 'CA', 1, 'y', 3),
(158, 23, 'Crewman Apprentice', 'Starfleet/s-e2.png', 'CA', 1, 'y', 4),
(159, 23, 'Crewman Apprentice', 'Starfleet/v-e2.png', 'CA', 1, 'y', 5),
(160, 23, 'Private 1st Class', 'Marine/g-e2.png', 'PFC', 1, 'y', 6),
(161, 23, 'Crewman Apprentice', 'Starfleet/c-e2.png', 'CA', 1, 'y', 7),

(162, 24, 'Crewman Recruit', 'Starfleet/r-e1.png', 'CR', 1, 'y', 1),
(163, 24, 'Crewman Recruit', 'Starfleet/y-e1.png', 'CR', 1, 'y', 2),
(164, 24, 'Crewman Recruit', 'Starfleet/t-e1.png', 'CR', 1, 'y', 3),
(165, 24, 'Crewman Recruit', 'Starfleet/s-e1.png', 'CR', 1, 'y', 4),
(166, 24, 'Crewman Recruit', 'Starfleet/v-e1.png', 'CR', 1, 'y', 5),
(167, 24, 'Private', 'Marine/g-e1.png', 'PVT', 1, 'y', 6),
(168, 24, 'Crewman Recruit', 'Starfleet/c-e1.png', 'CR', 1, 'y', 7),

(169, 25, 'Cadet Senior Grade', 'Starfleet/r-c4.png', 'CTSR', 1, 'n', 1),
(170, 25, 'Cadet Senior Grade', 'Starfleet/y-c4.png', 'CTSR', 1, 'n', 2),
(171, 25, 'Cadet Senior Grade', 'Starfleet/t-c4.png', 'CTSR', 1, 'n', 3),
(172, 25, 'Cadet Senior Grade', 'Starfleet/s-c4.png', 'CTSR', 1, 'n', 4),
(173, 25, 'Cadet Senior Grade', 'Starfleet/v-c4.png', 'CTSR', 1, 'n', 5),
(174, 25, 'Cadet Senior Grade', 'Marine/g-c4.png', 'CTSR', 1, 'n', 6),
(175, 25, 'Cadet Senior Grade', 'Starfleet/c-c4.png', 'CTSR', 1, 'n', 7),

(176, 26, 'Cadet Junior Grade', 'Starfleet/r-c3.png', 'CTJR', 1, 'n', 1),
(177, 26, 'Cadet Junior Grade', 'Starfleet/y-c3.png', 'CTJR', 1, 'n', 2),
(178, 26, 'Cadet Junior Grade', 'Starfleet/t-c3.png', 'CTJR', 1, 'n', 3),
(179, 26, 'Cadet Junior Grade', 'Starfleet/s-c3.png', 'CTJR', 1, 'n', 4),
(180, 26, 'Cadet Junior Grade', 'Starfleet/v-c3.png', 'CTJR', 1, 'n', 5),
(181, 26, 'Cadet Junior Grade', 'Marine/g-c3.png', 'CTJR', 1, 'n', 6),
(182, 26, 'Cadet Junior Grade', 'Starfleet/c-c3.png', 'CTJR', 1, 'n', 7),

(183, 27, 'Cadet Sophomore Grade', 'Starfleet/r-c2.png', 'CTSO', 1, 'n', 1),
(184, 27, 'Cadet Sophomore Grade', 'Starfleet/y-c2.png', 'CTSO', 1, 'n', 2),
(185, 27, 'Cadet Sophomore Grade', 'Starfleet/t-c2.png', 'CTSO', 1, 'n', 3),
(186, 27, 'Cadet Sophomore Grade', 'Starfleet/s-c2.png', 'CTSO', 1, 'n', 4),
(187, 27, 'Cadet Sophomore Grade', 'Starfleet/v-c2.png', 'CTSO', 1, 'n', 5),
(188, 27, 'Cadet Sophomore Grade', 'Marine/g-c2.png', 'CTSO', 1, 'n', 6),
(189, 27, 'Cadet Sophomore Grade', 'Starfleet/c-c2.png', 'CTSO', 1, 'n', 7),

(190, 28, 'Cadet Freshman Grade', 'Starfleet/r-c1.png', 'CTFR', 1, 'n', 1),
(191, 28, 'Cadet Freshman Grade', 'Starfleet/y-c1.png', 'CTFR', 1, 'n', 2),
(192, 28, 'Cadet Freshman Grade', 'Starfleet/t-c1.png', 'CTFR', 1, 'n', 3),
(193, 28, 'Cadet Freshman Grade', 'Starfleet/s-c1.png', 'CTFR', 1, 'n', 4),
(194, 28, 'Cadet Freshman Grade', 'Starfleet/v-c1.png', 'CTFR', 1, 'n', 5),
(195, 28, 'Cadet Freshman Grade', 'Marine/g-c1.png', 'CTFR', 1, 'n', 6),
(196, 28, 'Cadet Freshman Grade', 'Starfleet/c-c1.png', 'CTFR', 1, 'n', 7),

(197, 29, 'Trainee', 'Starfleet/r-c0.png', 'TRN', 1, 'n', 1),
(198, 29, 'Trainee', 'Starfleet/y-c0.png', 'TRN', 1, 'n', 2),
(199, 29, 'Trainee', 'Starfleet/t-c0.png', 'TRN', 1, 'n', 3),
(200, 29, 'Trainee', 'Starfleet/s-c0.png', 'TRN', 1, 'n', 4),
(201, 29, 'Trainee', 'Starfleet/v-c0.png', 'TRN', 1, 'n', 5),
(202, 29, 'Trainee', 'Marine/g-c0.png', 'TRN', 1, 'n', 6),
(203, 29, 'Trainee', 'Starfleet/c-c0.png', 'TRN', 1, 'n', 7),

(204, 30, '', 'Starfleet/r-blank.png', '', 1, 'y', 1),
(205, 30, '', 'Starfleet/y-blank.png', '', 1, 'y', 2),
(206, 30, '', 'Starfleet/t-blank.png', '', 1, 'y', 3),
(207, 30, '', 'Starfleet/s-blank.png', '', 1, 'y', 4),
(208, 30, '', 'Starfleet/v-blank.png', '', 1, 'y', 5),
(209, 30, '', 'Marine/g-blank.png', '', 1, 'y', 6),
(210, 30, '', 'Starfleet/c-blank.png', '', 1, 'y', 7),

(211, 1, '', 'Starfleet/w-blank.png', '', 1, 'y', 8),
(212, 2, '', 'Starfleet/b-blank.png', '', 1, 'y', 8);" );

/* insert the specs data */
mysql_query( "INSERT INTO `sms_specs` (`specid`, `shipClass`, `shipRole`, `duration`, `durationUnit`, `refit`, `refitUnit`, `resupply`, `resupplyUnit`, `length`, `height`, `width`, `decks`, `complimentEmergency`, `complimentOfficers`, `complimentEnlisted`, `complimentMarines`, `complimentCivilians`, `warpCruise`, `warpMaxCruise`, `warpEmergency`, `warpMaxTime`, `warpEmergencyTime`, `phasers`, `torpedoLaunchers`, `torpedoCompliment`, `defensive`, `shields`, `shuttlebays`, `hasShuttles`, `hasRunabouts`, `hasFighters`, `hasTransports`, `shuttles`, `runabouts`, `fighters`, `transports`) 
VALUES (1, '', '', '', 'Years', '', 'Years', '', 'Years', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 'y', 'y', 'y', 'y', '', '', '', '');" );

/** setup the unique sim id **/

/* define the length */
$length = 20;

/* start with a blank password */
$string = "";

/* define possible characters */
$possible = "0123456789bcdfghjkmnpqrstvwxyz"; 

/* set up a counter */
$i = 0; 

/* add random characters to $password until $length is reached */
while($i < $length)
{ 
	/* pick a random character from the possible ones */
	$char = substr( $possible, mt_rand( 0, strlen( $possible )-1 ), 1 );
	
	/* we don't want this character if it's already in the password */
	if(!strstr($string, $char))
	{ 
		$string .= $char;
		$i++;
	}
}

/* populate the system table with data */
mysql_query( "INSERT INTO sms_system ( sysid, sysuid, sysVersion, sysBaseVersion, sysIncrementVersion ) VALUES ( '1', '$string', '2.6.9', '2.6', '.9' )" );

/* populate the system plugins with data */
mysql_query( "INSERT INTO sms_system_plugins ( pid, plugin, pluginVersion, pluginSite, pluginUse, pluginFiles ) 
VALUES ( '1', 'jQuery', '1.2.6', 'http://www.jquery.com/', 'Javascript library used throughout SMS', 'framework/js/jquery.js' ),
( '2', 'jQuery UI', '1.0', 'http://ui.jquery.com/', 'Tabs throughout the system', 'framework/js/ui.tabs.js;skins/[your skin]/style-ui.tabs.css' ),
( '3', 'clickMenu', '0.1.6', 'http://p.sohei.org/jquery-plugins/clickmenu/', 'Customizable user menu', 'framework/js/clickmenu.js;skins/[your skin]/style-clickmenu.css' ),
( '4', 'Link Scrubber', '1.0', 'http://www.crismancich.de/jquery/plugins/linkscrubber/', 'Remove dotted border around clicked links in Firefox', 'framework/js/linkscrubber.js' ),
( '5', 'Shadowbox', '1.0', 'http://mjijackson.com/shadowbox/', 'Lightbox functionality;Gallery function on tour pages', 'framework/js/shadowbox-jquery.js;framework/js/shadowbox.js;framework/css/shadowbox.css' ),
( '6', 'Facebox', '1.2', 'http://famspam.com/facebox', 'Modal dialogs throughout the system', 'framework/js/facebox.js;framework/css/facebox.css;images/hud_close.png;images/hud_loading.gif' ),
( '7', 'Reflect jQuery', '1.0', 'http://plugins.jquery.com/project/reflect', 'Dynamic image reflection on tour pages', 'framework/js/reflect.js' )" );

/* populate the versions table with data */
mysql_query( "INSERT INTO `sms_system_versions` (`version`, `versionDate`, `versionShortDesc`, `versionDesc`)
VALUES ('2.0.1', '1153890000', '', 'Fixed issues relating to bio and account management;Fixed bug with the join form;Fixed bug with personal log posting;Fixed issue with database field length issues on several variables;Fixed bio image reference issues that wouldn\'t allow users to specify an off-site location for their character image;Added minor functionality to the crew manifest, specs, and message items'),
('2.0.2', '1155099600', '', 'Fixed issues relating to bio and account management (try 2);Fixed several issues associated with emails being sent out by the system;Added rank and position error checking for the crew listing in the admin control panel. List now provides an error message for users that have an invalid rank and/or position;Fixed manifest display bug;Fixed bug associated with whitespace and updating skins'),
('2.1.0', '1158123600', '', 'Added database feature left out of the 2.0 release because of time constraints;Added tour feature'),
('2.1.1', '1158382800', '', 'Fixed bug associated with excessive line breaks in database entries caused by the PHP nl2br() function that was being used;Added ability for COs to edit individual database items'),
('2.2.0', '1159506000', '', 'Added confirmation prompts for users who have JavaScript turned on in their browser. When deleting an item, the system will prompt the user if they are sure they want to delete it;Added ability for COs to decide whether a JP counts as a single post or as as many posts as there are authors in the JP;Added a tag field to the mission posts to allow users to specify their post tags at the start. The email sent out will display the tag right at the top so another user knows right from the beginning whether or not their character is involved or tagged in the post;Added the ability for users to save posts, logs, and news items to come back to and keep working on;Fixed bug where XOs didn\'t have the right access permissions for creating and editing NPCs;Added ability to set activation and deactivation options for both players as well as NPCs;Fixed bug on the full manifest where positions that were turned off still showed up;Fixed image reference bug that had the tour section looking in the wrong place for tour images;Fixed bug where deleting a database item while it was selected would cause it to stay displayed in the browser despite not existing'),
('2.2.1', '1159592400', '', 'Fixed bug where posts and logs had disappeared from both crew biographies as well as the admin control panel'),
('2.3.0', '1163394000', '', 'Added ability for users to set the rank sets they see on the site when they\'re logged in;Improved rank management to include the ability to change other rank sets;Updated icons throughout the system;Added ability for COs to define an accept and reject message that they can edit when accepting or rejecting a player;Fixed bug where posts that were saved first wouldn\'t have an accurate timestamp;Fixed bug where systems that don\'t use SMS posting don\'t have access to posting news items;Fixed bug where simm statistics was in the menu even if the system doesn\'t use SMS posting;Fixed bug when department heads went to create an NPC, they couldn\'t create them at their own rank, just below;Fixed bug where rank select menus for department heads were cut off, not allowing them to select the top items;Added ability for COs to remove an award from a player through a GUI instead of a textbox;Fixed bug where NPCs with an invalid rank and/or position wouldn\'t show up in the NPC manifest (or full manifest). Like playing characters, if an NPC now has an invalid rank and/or position, it\'ll show an error at the bottom of the complete listing of NPCs in the control panel to allow someone to go in and fix the problem;Added links in a player\'s bio for COs to add or remove awards;Improved update notification. System will now check to make sure that both the files and the database are using the same version coming out of the the XML file and if they\'re not, display appropriate warning messages;The system will now email the authors of a JP whenever anyone saves it, notifying them that it\'s been changed;Fixed bug where posts that had been saved and then were posted wouldn\'t show any author info in the email;Fixed bug where posts and logs weren\'t ordered by date posted;Fixed bug when pending posts, logs, or news items were activated, they weren\'t mailed out to the crew;Fixed bug where, besides the from line, there was nowhere in a news item where the author was displayed;Added ability for COs to moderate posts, logs, or news from specific players;Updated layout of site globals page to make more sense;Fixed bug on accounts page for people without access to change a player\'s character type where both the crew type and switch variables were being echoed out;Fixed bug where crew awards listing was trying to pull the small image from /images/awards instead of the large images from /images/awards/large;Improved efficiency on main control panel page by putting access level check before the system tries to check for pending items. If a user doesn\'t have level 4 access or higher, it won\'t even try to run the function now;Fixed issue where 2.2.0 and 2.2.1 didn\'t address changing the dockingStatus field in sms_starbase_docking'),
('2.3.1', '1163653200', '', 'Fixed bug when posting PHP error involving in_array() was returned. This was caused when there were no users flagged for moderation;Fixed bug where JP authors\' latest post information wasn\'t updated when saving a JP then posting it;Fixed bug associated with a missing update piece from the 2.2.1 to 2.3.0 update. This bug only affected users who updated from 2.2.x to 2.3.0;Fixed minor bug in update notification where a wrong variable was being used and causing the version number not to be displayed out of the XML file'),
('2.4.0', '1165122000', '', 'Added built-in deck listing feature;Added mission notes feature to allow COs to remind their crew of important things on the post and JP pages;Added ability for COs to change whether or not they use the mission notes feature;Changed add award feature to use graphical representations of the awards for adding instead of the select menu like before;Added a version history page;Added full list of users and their moderation status allowing for quick change in moderation status;Added ability for COs to allow XOs to receive crew applications and approve/deny them through the control panel;Fixed bug where simm page would try to print the task force even if the simm wasn\'t part of a task force;Changed link in update function to point to the new server;Fixed bug where news items weren\'t being emailed out;Fixed two style inconsistencies in tour management page;Added private messaging;Added tour summary;Added option to use a third image for each tour item;Added feature allowing COs to add a post, JP, log, or news item that a member of the crew has incorrectly posted or forgotten to post;Added sim progress feature that allows users to see how the sim has progressed over the last 3 months, 6 months, 9 months, and year;Added link from bio page to show all a user\'s posts and logs'),
('2.4.1', '1165813200', '', 'Fixed SQL injection security holes by adding regular expression check to make sure that the GET variables being used were positive integers. If the check fails, the CO will be emailed with the offending user\'s IP address as well as the page they were trying to access and the exact time they attempted to access the page so the CO can forward that information on to the web host if necessary;Fixed incorrect link deck listing page when no decks are listed in the specifications page;Added Kuro-RPG banner on the credits page at his request'),
('2.4.2', '1166850000', '', 'Fixed issue in update function where new SMS version wouldn\'t be displayed;Moved credits into the database and made them editable through the Site Messages panel;Fixed bug on bio page where the name wasn\'t being run through the printText() function to strip the SQL escaping slashes;Added non-posting departments to allow COs and XOs to create NPCs in departments where it isn\'t plausible to have a posting character;Added link to post entry for players who are logged in that will take them directly to the post mission entry page;Fixed call to undefined function error when editing mission notes;Fixed bug where email notification sent out after updating a JP wouldn\'t have a FROM field;Changed SMS to use the image name with mission images instead of the full path;Added ability to delete a saved post'),
('2.4.3', '1167800400', '', 'Fixed JavaScript error when logging out;Fixed JavaScript bug with the Mission Notes hide/show feature;Added neuter and hemaphrodite to the gender options;Added player experience to the join form. This information will only be available in the email sent from the system and not stored in the database;Fixed bug where anyone who did a fresh install of 2.4.2 would not be able to access their globals and messages because of a typo in the install'),
('2.4.4', '1169701200', '', 'Position grouping by department on the Add Character page;Gender and species added to NPC manifest;Bio page presentation cleanup;Deck listing page presentation cleanup;Departments sections in All NPCs list for access levels 4 and higher;HTML character printed after last department on department management page;Mission title wasn\'t being sent out in mission post emails;Mission log listing order - completed missions should be sorted by completion date descending;Email formatting bug in news item emails;Alternating row colors for the Crew Activity list, All NPCs, and All Characters;All Characters list ordering by department first;Editing an NPC\'s position through their bio would change the number of open positions for those positions (old and new);Some character pictures would break the SMS template on the bio page;If a player had a previous character with the same username and password, it\'d generally log them in as their old character;Email formatting bug in Award Nomination page;Changed Award Nomination and Change Status Request to have them email sent from the player nominating/requesting;Added User Stats page;Changed database to make it easier to track senior staff vs crew positions;Logic to make sure the apply link isn\'t show for an NPC occupying a position with no open slots;Added timestamp for when a playing character is deactivated;Updated styling on posting pages (bigger text boxes);Added a count of saved posts on the Post Count report;Post Count report wouldn\'t return the right results under some conditions;Sim statistics page wasn\'t obeying system\'s global preference for how to count posts and was including saved posts;Visual notification of saved JPs the user hasn\'t responded to;Leave date set on player deactivation;More logic in the printCO, printXO, printCOEmail, and printXOEmail functions to narrow down the results;Better layout on individual post management page;Ability to change a post\'s mission;Changing rank sets when spaces are between the values in the allowed ranks field;Fixed sim progress loops to accurately display the number of posts'),
('2.4.4.1', '1170046800', '', 'Fixed positions table problem introduced in 2.4.4 (this release was only for future fresh installs, a patch fixed the issue for everyone else)'),
('2.5.0', '1185317009', 'SMS 2.5 is a true milestone and one of the largest releases Anodyne has ever released.  This new version of SMS extends functionality across multiple planes, providing more control for COs with less effort.  A new user access system now allows COs to specify exactly which pages a certain player has access to and a new menu system means that you can now update menu items from within the SMS Control Panel.  On top of that, we\'ve patched dozens of bugs, fixed consistency issues, improved the system\'s overall efficiency, and made SMS smarter than ever before.', 'User access control system changed to let COs select exactly which pages a player has access to;Menus are now pulled from the database and managed through a page in the control panel;Moved alternating row colors to the stylesheet for skinners;Changed all system icons to use alpha-channel PNGs;Fixed JP author slot 3 issues;Added ability for COs to choose whether added posts are emailed out;Added option to select which mission the post is part of for the add post and add JP page;Fixed bug where updating a mission post through the mission posts listing would erase the mission info and cause an extract() error;Added tabs to pages with lots of content;Changed form submit buttons to use the image type instead of the native browser/OS widgets;Changed all timestamps from SQL format to UNIX format;Refreshed default theme;Added Cobalt theme;Added LCARS theme;Removed Alternate rank set to save space;Added phpSniff credits;Added page to display mission notes without having to go to the post pages;Added Top 5 Open Positions list on the main page that can be controlled through Site Globals;Improved site presentation options when it comes to the content on the main page - COs can now select which items they want to see;Added COs to acceptance/rejection emails;Widened text boxes throughout the system;Improved style consistency throughout the system;Changed news setup to allow a single news item to be viewed like a post or log (finally);Rewrote query execution check to be more efficient and smarter;Improved logic of activation page including the plurality based on the number of pending items in each category;Added First Launch feature that will give a CO a brief run-down of the updates to SMS when they first log in;Fixed manifest so that it won\'t show a link to apply for a position if there aren\'t any open slots;Standardized use of preview rank images;Added blank image to the root rank directory and changed system to use the blank image instead of looking for a specific rank image;Fixed manifest so that previous players and NPCs can hold two positions;Added graphical notification of player\'s LOA status on the main control panel;Install script will check to make sure the web location variable is in place, otherwise it won\'t let you continue to the next step;Crew activity report displays number of months now instead of just days;Added crew milestone report to show how long players have been aboard the sim;Fixed bug where user would not be notified if the update query failed because they tried to change their username, real name, or email without giving their current password;Changed password reset to display the new password instead of emailing it out because of problems with the emails not being sent out;Added unique sim identifier to make sure that SMS installations on the same domain don\'t cause problems for each other'),
('2.5.1', '1185447600', 'This release fixes several bugs not caught during beta testing, including a bug where IE users could not activate new crew members. In addition, there have been some corrections to the install files as well as the join page.', 'Install: fixed typo in permanent credits insert;Install: changed positionDisplay update to make sure it isn\'t trying to update something it shouldn\'t;Install: changed rankDisplay update to make sure it isn\'t trying to update something it shouldn\'t;Fixed bug where IE users couldn\'t activate or reject players;Fixed bug where incorrect timestamp format was used when activating a new player;Update: added smart checking to make sure a timestamp isn\'t trying to be updated if it\'s already a timestamp; Fixed bug where simm statistics weren\'t showing at all;Improved the email and logic code on the join form regarding whether XOs get emails or not'),
('2.5.1.1', '1185746400', 'This release fixes a bug where SMS wouldn\'t allow players to be accepted or rejected.', 'Fixed bug where players couldn\'t be accepted or rejected'),
('2.5.2', '1186578000', 'This release patches several outstanding bugs in SMS as well as enhancing existing features with additional functionality.', 'Added page to add/remove a given access level for the entire crew at the same time;Added page that gives full listing of a given user\'s access;Added user access report link to the full crew listing by the stats link;Added display of second position (if applicable) to the active crew list;When the SMS posting system is turned on or off, the system will take actions to make sure the people are either stripped of or given posting access;Added character set and collation to install (hopefully this will fix problems people were having);Fixed bug where if the variables file was written the webLocation variable would be empty;Fixed bug where textareas would show HTML break tags after updating;Fixed bug where join page set wrong timestamp;Added nice message if the join date is empty instead of the 37 years, etc.;Fixed bug where time wouldn\'t display if it was 1 day or less;Updated logic to display date in a nicer fashion;Improved display for dates less than 1 day;Added on/off switch control to each menu item;Fixed bug where error message on login page would extend across entire screen;Reactivated emailing of password;Added visual separation between items that need a password to be changed and those that don\'t in the account managemment page;Removed username from being listed on the account management page unless the person viewing it is the owner of the account;Fixed bug where dates wouldn\'t display by recent posts and logs;Fixed account bug where admin couldn\'t change active status of a player;Fixed bug where saving a joint post with 4 participants would overwrite the third author with a blank variable'),
('2.5.3', '1187026200', 'This release patches several bugs related to player acceptance and rejection, display problems and account management.', 'Provided potential fix for skinners related to strange spacing in the Latest Posts section on the main page when moving paddings from anchors to list items;Fixed display bug with reply button on PM view page;Fixed bug where updating own account wouldn\'t work;Fixed bug where player being accepted or rejected wouldn\'t get an email;Fixed potential bug where player being accepted wouldn\'t be updated correctly'),
('2.5.4', '1190036700', 'This release increases the number of allowed JP authors from 6 to 8.', 'Increased allowed JP authors from 6 to 8'),
('2.5.5', '1194444000', 'This release fixes a critical security issue and patches a bug with default standard player access levels.', 'Fixed critical security issue;Fixed bug where newly created standard players don\'t have the right permissions for sending news items'),
('2.5.6', '1202230800', 'This release fixes an annoying issue where spammers trying to access un-authenticated pages produced an email claiming SQL injection.', 'Removed email to CO when an illegal operation is attempted (99% of these attempts are in fact spammers, not a malicious hacking attempt)'),
('2.6.0', '1217109600', 'This release is the final major update to SMS 2 and adds new features like user editing of posts, departmental databases, personalized menus, and a whole slew of smaller enhancements. Overall, this release is one of our largest and is an excellent capstone to SMS 2!', 'Private news items (can only be seen when logged in);Added the jQuery Javascript library;Tabs now use jQuery, meaning the content is available immediately after clicking;Added stardate script and the ability to turn it on and off (through site globals);Admins can now choose whether or not they want to be notified of SMS updates;Admins can now set the email subject lines (default is [Ship Name]);Acceptance and rejection messages now use wild card variables for dynamically inserting things like name, rank, positions, and ship name;If a query fails, it\'ll display an SMS Error Report that someone can copy and paste to the forums;Personalized menus;NPCs can now be given in character awards;Departmental databases;Completely new install process that\'s clearer and with much better instructions;Users (with proper permissions) can edit their own mission posts (except which mission the post is in and the post status);Users (with proper permissions) can edit their own personal logs (except the author and log status);Admins can now set the defaults for the various access levels (CO, XO, Department Head, Standard Player);Players can now put more than one image in their bio and they\'ll be displayed as a mini-gallery like the tour item pictures;SMS now automatically detects your web location variable during installation;Award nominations are now sent to a queue for approval by an admin;Completely rewrote activation page;Awards now include the award given, when it was given, and the reason;Completely new manifest page using jQuery;Completely new departments page using jQuery for toggling;Site options page now uses tabs to better organize everything;Combadge images on the manifest are now PNGs to match with any background color;Add and remove icons now have an off and over state;Added function for escaping strings before they\'re entered into the database;Added logic to check for the existence of the large award image and if it fails, fall back to the smaller version of the image;Notifications are now less obtrusive and provide more instant feedback;Made the pending checks smarter;Cleaned up the inbox including tabs (with unread count) and moving the compose PM into the inbox;Inbox now has a select all\/deselect all option;Added dynamic image reflection to the tour images;All images now have a class so that a skin can style images differently if they want;When viewing NPCs\' bios, it won\'t show the Posting Activity sections;SMS now uses some PHP constants to give functions access to things like web location and ship name;Awards now have categories of in character, out of character, and both;Error page when someone tries to go to a page that doesn\'t exist;Added a lightbox to the tour images to make a mini gallery;Replying to a private message will show the content of the message you\'re replying to below the compose box;A ton of commenting in the default skin to (hopefully) help out people who are trying to create their own skins;SMS now uses AXAH (Asynchronous XHTML and HTTP) for creating, editing, and deleting some items (menu items, departments, activations, ranks, positions, crew awards, tour items, missions, giving\/removing awards to crew, user post moderation, and docked ships);Consolidated menu management into a single page instead of two;Removed unused images;Adding system plugins to the About SMS page;Updated the framework structure;Default skin now uses some fancy dynamic location stuff so that the code could be moved to another directory and keep working;Default skin now uses UTF-8 for character encoding and English as the language by default;Version history page now uses tabs;Management pages for posts, logs, and news items now use tabs to separate activated, saved, and pending entries, plus it lists entries instead of providing \"mini editing\";Got rid of the confusing CLASS field in rank management, replacing it with a drop-down of the class groups to make it a little more self-explanatory;Re-wrote page for setting access levels for entire crew to make it better (it just plain sucked before);Gave the starbase-specific pages a little love to bring them more in line with the rest of the system;The bio page now displays awards from the most recent down instead of the oldest first;Rewrote the user moderation page to be more secure;Ranks now have short names (CAPT instead of Captain) that are used in emails to shorten the FROM field;Fixed bug where rank menus didn\'t respect the rankDisplay flag;Fixed bug where the system check class would try to write something to the main ACP even if there was nothing to write, causing an extra space;Fixed bug where crew compliment fields would only allow integers (commas would break things);Fixed bug where player stats page would throw back some weird data if there wasn\'t a properly formatted join date;Fixed bug where changing a playing character to something else (or vice versa) wouldn\'t affect the open positions;Fixed bug where contact page wouldn\'t send mail out;Fixed bug where the read more prompts in the control panel could break right in the middle;Fixed all the Apache warnings and errors SMS would dump into the server error logs (will make server admins VERY happy);Fixed bug where the crew awards page would, in some situations, print the award name twice;Fixed bug where someone with full XO privileges could create a CO character (thought without the CO access levels);Fixed bug where mission status wasn\'t inserted into the database;Fixed bug in the 2.4.4 update script where a semicolon was missing;Fixed bug where reset password form wouldn\'t send the email out to the appropriate person (or update it in the database either);Fixed bug where mission order wouldn\'t be updated;Fixed bug that would allow pending, inactive, and NPC characters to log in;Removed unused System Catalogues item from access levels management;Removed unused Skin Development item from access levels management;Removed unnecessary access checks on the admin subsection pages;Worked around Webkit bug where post edit icon wouldn\'t show up;Fixed bug where using the delete link for an individual news item (on the news item page) wouldn\'t do anything at all;Fixed join page bug where height, weight, physical description, personality overview, and real name weren\'t be inserted into the database;Fixed bug where admins could put a pound sign in the department color field and break the color system'),
('2.6.1', '1219005900', 'This release addresses several bugs and issues with the initial release of SMS 2.6, including bugs while updating and several smaller issues related to emails sent out by the system and creating new ranks.', 'Fixed bug where system version wouldn\'t be passed to the update script if it wasn\'t in the URL (caused all the issues with improper updates);Fixed bug where XO wasn\'t printed on the contact page;Fixed bug where after doing an update, the system would throw an error about being unable to select the appropriate database;Made the database password box during install a password field instead of a text field;Fixed several bugs with the reset password form;Fixed a potential security issue with the reset password form;Fixed bug where after doing an update, the system would display without a skin;Fixed bug where the wrong author is listed in the activate news items tab;Fixed error message display about wrong data type when not logged in;Fixed bug in single author mission entry email;Fixed bug where newly created ranks in a new department weren\'t put where they should be;Fixed bug where using quotes in a field that displayed data from the database would cause the data to disappear;Fixed illegal operation emails being sent out;Fixed bug where users couldn\'t be put on LOA;Fixed bug where player application email was wrong;Fixed bug where contact form wouldn\'t use user-defined subject;Added more instructions to the awards pages to highlight the fact that NPCs can only receive in-character awards;Fixed bug where some preferences weren\'t updating properly;Fixed IE7 bug where Site Options weren\'t updated;Updated instructions for adding a rank'),
('2.6.2', '1220155200', 'This release addresses several bugs and issues with SMS 2.6, including issues with the reset password form, acceptance and rejection emails, the top open positions list, and award nominations.', 'Fixed bug where top positions couldn\'t be updated;Disabled NPC award nomination tab if there are no in-character awards present;Fixed bug where view link in database management always points to the last database entry;Fixed bug where reset password wouldn\'t send email out and would print new password on the screen;Fixed bug where HTML breaks were shown in acceptance and rejection emails;Added alt text to combadge images in the event the combadge image is missing, people can still get to the bios;Added changed files list to the zip archive again;Fixed bug where players with XO level access couldn\'t accurately update NPCs with a higher rank than their own'),
('2.6.3', '1223848800', 'This release addresses issues related to award nominations, access problems with SMS systems on the same domain, email issues, and other bug fixes.', 'Updated skin location code to work better on Windows machines (local and servers);Fixed display issues with character images and tour images in Firefox 3;Updated reflection script to version 1.9;Fixed issue with fresh install which made the system think it was running version 2.6.0;Fixed issue with commas not being able to be used in award reasons (semicolons can still not be used);Fixed confusing issue where crew activity report said Today when it was actually within the last 24 hours;Fixed bug where reset password would reset a password and try to update a password and send an email even if you didn\'t put anything in the fields;Fixed bug where \"login to update this joint post\" message was missing from joint post saved notification emails;Fixed issue where two SMS installations on the same domain would cause weird access issues (thanks to Jon Matterson for letting us use his MOD to fix this issue);Updated default rank set to use DS9 rank set using alpha channel transparencies, allowing the ranks to look good on any background color;Fixed bug in ship skin that would cause it to break when switching directories'),
('2.6.4', '1227222000', 'This release addresses issues related to nominating crew when no awards are present, display of links and tabs when events tied to those links/tabs are not legitimate, adding and removing certain access levels, and a major issue with fresh installs where the write process fails.', 'Fixed spacing in rank drop down menus to avoid text and graphic overlap;Fixed bug where players could nominate another player for an award, even if no awards exist;Fixed a potential bug where a player could manually get to the NPC tab and submit a nomination even if the tab was disabled;Removed approve link in Approve Award Nomination list if there is no award associated with that nomination (and award with an id of 0);Fixed bug where web location variable wouldn\'t be written to the proper DIV in the event the file write failed;Fixed issue where SMS violated mod_security rule 340077 on some servers;Fixed bug where excess data was being inserted into the access fields;Fixed bug where adding an access level from the Other section for the entire crew would fail;Fixed bug where leading comma would be added to a user\'s access levels if they didn\'t have anything in that field to start with;Tweaked rank short names for updates and fresh installs'),
('2.6.5', '1227308400', 'This release addresses an issue where the award nomination form would not work if a sim had awards with the categories of Out of Character or Both.', 'Fixed bug where Nominate button wouldn\'t appear on the nomination form for playing character if the only awards in the system had categories of Out of Character or Both'),
('2.6.6', '1228716000', 'This release adds two minor feature updates. The first is the way bio images are displayed and the second is the option to set moderation flags on activation. In addition, we have drastically improved the efficiency of building the next/previous links on the view post, log, and news pages as well as the mission logs page. Finally, to reduce the choppy loading of the manifest, we\'ve added a loading graphic which will stay in place until the manifest is fully loaded.', 'Updated bio image display to show a main picture and clicking the picture opens a gallery with all the character images;Added ability to set moderation flags at character activation;Improved efficiency of next/previous links on viewing post, log, and news pages;Improved efficiency of mission logs page;Added date posted or of last update in manage posts;Added loading image to the manifest'),
('2.6.7', '1230408000', 'This release fixes bugs with rank management, next/previous links, a typo on the join page, and quotation marks in bio fields. In addition, this release adds the ability to run multiple missions simultaneously and adds award nomination information to the email that is sent out.', 'Fixed bug where rank management would only build department class menus for departments that were being displayed, causing issues for unused ranks that were updated;Fixed bug with next/previous links where they didn\'t respect when a log/post/news item was posted;Added the ability to run multiple missions simultaneously;Added more specific information to the award nomination emails (nominee, nominated by, award, and reason) so it isn\'t just a nondescript notice;Fixed bug where quotation marks couldn\'t be used in some bio fields;Fixed bug where SMS would still try to run the update check class even if an admin had set their update notification level to none;Fixed typo in a position description for fresh installs;Fixed the join agree page to say terms of use instead of disclaimer as the former is more accurate'),
('2.6.8', '1234400160', 'This release fixes bugs with saved mission posts, the Departments &amp; Positions page, and miscounting of bio images. In addition, we have re-written the modal window to remove images and use CSS styles almost exclusively for easier updates and better readability.', 'Fixed bug where saved mission posts and joint posts showed the missions in the dropdown twice;Fixed bug where departments and positions on the Departments and Positions page were not ordered by their respective orders, but by ID instead;Fixed bug where commas at the end of the character images string caused an additional image to be counted, and when displayed in the gallery, would appear as the full bio page;Fixed PHP notice where system UID wasn\'t set if the system\'s not installed;Fixed PHP notice on the installation center;Added note that having NPC-2 rights should also be accompanied by Bio-3 rights to the access pages;Removed images from rank dropdowns in Firefox to fix a bug with department heads having their dropdown menus cut off (Firefox bug);Upgraded Facebox to version 1.2;Updated the character add page to hide and show the username information based on what\'s clicked and what permissions the player has'),
('2.6.9', '1250776800', 'This release fixes bugs with the docking request form, docked ship activation and database entry management.', 'Fixed typos in docking request email sent out to the starbase CO;Fixed bug with docked ship activation and rejection where the docking CO wouldn\'t be sent a copy of the acceptance or rejection email;Fixed location of the Facebox loading graphic;Fixed bug in database management page where only entries with a display flag of yes would be shown instead of all entries;Fixed bug in database display page where departments with database use turned off still appeared;Updated the version check class to understand that SMS 3.0 is actually Nova 1.0;Updated the version check class to have the download link point to the main Anodyne site;Updated the version check class to point to a new XML file so Nova and SMS can both have version XML files with the same naming scheme')");

?>