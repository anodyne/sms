<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/269.php
Purpose: Update to 2.6.10
Last Modified: 2009-09-16 1938 EST
**/

/*
|---------------------------------------------------------------
| POST AUTHORS
|---------------------------------------------------------------
|
| Changing the database format of the postAuthor field
|
*/
mysql_query("ALTER TABLE `sms_posts` CHANGE `postAuthor` `postAuthor` text NOT NULL");

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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.6.10', '', 'This release fixes bugs with ...', 'Fixed bug on news page where selecting a category would narrow down news but the category listed next to each news item wouldn't be accurate;Fixed bug in menu class where setting a general menu item to require login wouldn't allow anyone to see the link, logged in or not;Fixed bug where mission posts wouldn't be deleted when the delete action was triggered from the manage posts page;Fixed bug where post tags couldn't be updated from the Edit Mission Post page)");

?>