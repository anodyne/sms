<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/255.php
Purpose: Update page to 2.5.6
Last Modified: 2008-04-24 1258 EST
**/

/* add the data for FirstLaunch */
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.5.6', '1202230800', 'This release fixes an annoying issue where spammers trying to access un-authenticated pages produced an email claiming SQL injection.', 'Removed email to CO when an illegal operation is attempted (99% of these attempts are in fact spammers, not a malicious hacking attempt)' )" );

?>