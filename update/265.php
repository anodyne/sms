<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/265.php
Purpose: Update to 2.6.6
Last Modified: 2008-12-08 1517 EST
**/

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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.6.6', '1228716000', 'This release adds two minor feature updates. The first is the way bio images are displayed and the second is the option to set moderation flags on activation. In addition, we have drastically improved the efficiency of building the next/previous links on the view post, log, and news pages as well as the mission logs page. Finally, to reduce the choppy loading of the manifest, we\'ve added a loading graphic which will stay in place until the manifest is fully loaded.', 'Updated bio image display to show a main picture and clicking the picture opens a gallery with all the character images;Added ability to set moderation flags at character activation;Improved efficiency of next/previous links on viewing post, log, and news pages;Improved efficiency of mission logs page;Added date posted or of last update in manage posts;Added loading image to the manifest')");

?>