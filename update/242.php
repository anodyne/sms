<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/242.php
Purpose: Update page to 2.4.3
Last Modified: 2008-04-24 1251 EST
**/

/* change the gender field */
mysql_query( "ALTER TABLE `sms_crew` CHANGE `gender` `gender` enum( 'Male', 'Female', 'Hermaphrodite', 'Neuter' ) not null default 'Male'" );

/* fix the site credits field */
mysql_query( "ALTER TABLE `sms_messages` CHANGE `siteCredts` `siteCredits` text not null" );

?>