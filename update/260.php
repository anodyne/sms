<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/260.php
Purpose: Update to 2.6.1
Last Modified: 2008-08-17 1635 EST
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
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionRev`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.6.1', '641', '1219005900', 'This release addresses several bugs and issues with the initial release of SMS 2.6, including bugs while updating and several smaller issues related to emails sent out by the system and creating new ranks.', 'Fixed bug where system version wouldn\'t be passed to the update script if it wasn\'t in the URL (caused all the issues with improper updates);Fixed bug where XO wasn\'t printed on the contact page;Fixed bug where after doing an update, the system would throw an error about being unable to select the appropriate database;Made the database password box during install a password field instead of a text field;Fixed several bugs with the reset password form;Fixed a potential security issue with the reset password form;Fixed bug where after doing an update, the system would display without a skin;Fixed bug where the wrong author is listed in the activate news items tab;Fixed error message display about wrong data type when not logged in;Fixed bug in single author mission entry email;Fixed bug where newly created ranks in a new department weren\'t put where they should be;Fixed bug where using quotes in a field that displayed data from the database would cause the data to disappear;Fixed illegal operation emails being sent out;Fixed bug where users couldn\'t be put on LOA;Fixed bug where player application email was wrong;Fixed bug where contact form wouldn\'t use user-defined subject;Added more instructions to the awards pages to highlight the fact that NPCs can only receive in-character awards;Fixed bug where some preferences weren\'t updating properly;Fixed IE7 bug where Site Options weren\'t updated;Updated instructions for adding a rank' )" );

?>