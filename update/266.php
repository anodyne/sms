<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/266.php
Purpose: Update to 2.6.7
Last Modified: 2008-12-27 1345 EST
**/

/*
|---------------------------------------------------------------
| MENU ITEM
|---------------------------------------------------------------
|
| This update changes the menu item to read Current Mission(s)
|
*/
mysql_query("UPDATE sms_menu_items SET menuTitle = 'Current Mission(s)' WHERE menuTitle = 'Current Mission'");

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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.6.7', '1230408000', 'This release fixes bugs with rank management, next/previous links, a typo on the join page, and quotation marks in bio fields. In addition, this release adds the ability to run multiple missions simultaneously and adds award nomination information to the email that is sent out.', 'Fixed bug where rank management would only build department class menus for departments that were being displayed, causing issues for unused ranks that were updated;Fixed bug with next/previous links where they didn\'t respect when a log/post/news item was posted;Added the ability to run multiple missions simultaneously;Added more specific information to the award nomination emails (nominee, nominated by, award, and reason) so it isn\'t just a nondescript notice;Fixed bug where quotation marks couldn\'t be used in some bio fields;Fixed bug where SMS would still try to run the update check class even if an admin had set their update notification level to none;Fixed typo in a position description for fresh installs;Fixed the join agree page to say terms of use instead of disclaimer as the former is more accurate')");

?>