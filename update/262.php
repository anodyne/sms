<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/262.php
Purpose: Update to 2.6.3
Last Modified: 2008-10-12 1655 EST
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
mysql_query("INSERT INTO sms_system_versions ( `version`, `versionRev`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.6.3', '688', '1223848800', 'This release addresses issues related to award nominations, access problems with SMS systems on the same domain, email issues, and other bug fixes.', 'Updated skin location code to work better on Windows machines (local and servers);Fixed display issues with character images and tour images in Firefox 3;Updated reflection script to version 1.9;Fixed issue with fresh install which made the system think it was running version 2.6.0;Fixed issue with commas not being able to be used in award reasons (semicolons can still not be used);Fixed confusing issue where crew activity report said Today when it was actually within the last 24 hours;Fixed bug where reset password would reset a password and try to update a password and send an email even if you didn\'t put anything in the fields;Fixed bug where \"login to update this joint post\" message was missing from joint post saved notification emails;Fixed issue where two SMS installations on the same domain would cause weird access issues (thanks to Jon Matterson for letting us use his MOD to fix this issue);Updated default rank set to use DS9 rank set using alpha channel transparencies, allowing the ranks to look good on any background color;Fixed bug in ship skin that would cause it to break when switching directories' )");

?>