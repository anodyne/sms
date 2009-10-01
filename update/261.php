<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/261.php
Purpose: Update to 2.6.2
Last Modified: 2008-08-30 2357 EST
**/

/*
|---------------------------------------------------------------
| ACCESS LEVELS
|---------------------------------------------------------------
|
| This update makes sure that players with XO level access can
| accurately update NPCs with higher ranks than their own.
|
*/
mysql_query("UPDATE sms_accesslevels SET user = 'user,u_account1,u_nominate,u_inbox,u_status,u_options,u_bio3,u_stats' WHERE id = 2 LIMIT 1");

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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionRev`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.6.2', '664', '1220155200', 'This release addresses several bugs and issues with SMS 2.6, including issues with the reset password form, acceptance and rejection emails, the top open positions list, and award nominations.', 'Fixed bug where top positions couldn\'t be updated;Disabled NPC award nomination tab if there are no in-character awards present;Fixed bug where view link in database management always points to the last database entry;Fixed bug where reset password wouldn\'t send email out and would print new password on the screen;Fixed bug where HTML breaks were shown in acceptance and rejection emails;Added alt text to combadge images in the event the combadge image is missing, people can still get to the bios;Added changed files list to the zip archive again;Fixed bug where players with XO level access couldn\'t accurately update NPCs with a higher rank than their own' )");

?>