<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/253.php
Purpose: Update page to 2.5.4
Last Modified: 2008-04-24 1244 EST
**/

/* update the site permanent credits to make sure everyone has the right tou link */
mysql_query( "ALTER TABLE `sms_posts` CHANGE `postAuthor` `postAuthor` varchar(40) not null default ''" );

/* add the data for FirstLaunch */
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.5.4', '1190036700', 'This release increases the number of allowed JP authors from 6 to 8.', 'Increased allowed JP authors from 6 to 8' )" );

?>