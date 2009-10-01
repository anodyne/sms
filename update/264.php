<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/264.php
Purpose: Update to 2.6.5
Last Modified: 2008-11-21 0758 EST
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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.6.5', '1227308400', 'This release addresses an issue where the award nomination form would not work if a sim had awards with the categories of Out of Character or Both.', 'Fixed bug where Nominate button wouldn\'t appear on the nomination form for playing character if the only awards in the system had categories of Out of Character or Both')");

?>