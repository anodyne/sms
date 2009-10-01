<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/230.php
Purpose: Update page to 2.3.1
Last Modified: 2008-04-24 1256 EST
**/

/* add the acceptMessage and rejectMessage fields to the messages database */
mysql_query( "ALTER TABLE `sms_messages` ADD `acceptMessage` TEXT NOT NULL" );
mysql_query( "ALTER TABLE `sms_messages` ADD `rejectMessage` TEXT NOT NULL" );

/* insert the data into the database */
mysql_query( "UPDATE sms_messages SET acceptMessage = 'Set your acceptance form letter here to update whenever you accept a new player. You can edit this message through the Site Messages panel.'" );
mysql_query( "UPDATE sms_messages SET rejectMessage = 'Set your rejection form letter here to update whenever you reject an applicant for your sim. You can edit this message through the Site Messages panel.'" );

?>