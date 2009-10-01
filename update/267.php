<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/267.php
Purpose: Update to 2.6.8
Last Modified: 2009-02-09 0906 EST
**/

/*
|---------------------------------------------------------------
| COMPONENT UPDATE
|---------------------------------------------------------------
*/
mysql_query("UPDATE sms_system_plugins SET pluginVersion = '1.2' WHERE plugin = 'Facebox' LIMIT 1");

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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.6.8', '1234400160', 'This release fixes bugs with saved mission posts, the Departments &amp; Positions page, and miscounting of bio images. In addition, we have re-written the modal window to remove images and use CSS styles almost exclusively for easier updates and better readability.', 'Fixed bug where saved mission posts and joint posts showed the missions in the dropdown twice;Fixed bug where departments and positions on the Departments and Positions page were not ordered by their respective orders, but by ID instead;Fixed bug where commas at the end of the character images string caused an additional image to be counted, and when displayed in the gallery, would appear as the full bio page;Fixed PHP notice where system UID wasn\'t set if the system\'s not installed;Fixed PHP notice on the installation center;Added note that having NPC-2 rights should also be accompanied by Bio-3 rights to the access pages;Removed images from rank dropdowns in Firefox to fix a bug with department heads having their dropdown menus cut off (Firefox bug);Upgraded Facebox to version 1.2;Updated the character add page to hide and show the username information based on what\'s clicked and what permissions the player has;Fixed styling bugs on install pages in IE7;Updated readme file to reflect updated server requirements')");

?>