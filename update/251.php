<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/251.php
Purpose: Update page to 2.5.1.1
Last Modified: 2008-04-24 1247 EST
**/

/* add the data for FirstLaunch */
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ('2.5.1.1', '1185746400', 'This release fixes a bug where SMS wouldn\'t allow players to be accepted or rejected.', 'Fixed bug where players couldn\'t be accepted or rejected')" );

?>