<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/268.php
Purpose: Update to 2.6.9
Last Modified: 2009-08-20 0841 EST
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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.6.9', '1250776800', 'This release fixes bugs with the docking request form, docked ship activation and database entry management.', 'Fixed typos in docking request email sent out to the starbase CO;Fixed bug with docked ship activation and rejection where the docking CO wouldn\'t be sent a copy of the acceptance or rejection email;Fixed location of Facebox loading graphic;Fixed bug in database management page where only entries with a display flag of yes would be shown instead of all entries;Fixed bug in database display page where departments with database use turned off still appeared;Updated the version check class to understand that SMS 3.0 is actually Nova 1.0;Updated the version check class to have the download link point to the main Anodyne site;Updated the version check class to point to a new XML file so Nova and SMS can both have version XML files with the same naming scheme')");

?>