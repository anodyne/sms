<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/241.php
Purpose: Update page to 2.4.2
Last Modified: 2008-04-24 1253 EST
**/

/* add the type of display option for departments */
mysql_query( "ALTER TABLE `sms_departments` ADD `deptType` enum( 'playing', 'nonplaying' ) not null default 'playing'" );

/* add both credits fields */
mysql_query( "ALTER TABLE `sms_messages` ADD `siteCreditsPermanent` text not null" );
mysql_query( "ALTER TABLE `sms_messages` ADD `siteCredits` text not null" );

/* insert data into the two new fields */
mysql_query( "UPDATE sms_messages SET siteCredits = 'Please define your site credits in the Site Messages page...' WHERE messageid = '1' LIMIT 1" );
mysql_query( "UPDATE sms_messages SET siteCreditsPermanent = 'The SMS 2 Update notification system uses <a href=\"http://magpierss.sourceforge.net/\" target=\"_blank\">MagpieRSS</a> to parse the necessary XML file. Magpie is distributed under the GPL license. Questions and suggestions about MagpieRSS should be sent to <i>magpierss-general@lists.sf.net</i>.\r\n\r\nSMS 2 uses icons from the open source <a href=\"http://tango.freedesktop.org/Tango_Icon_Gallery\" target=\"_blank\">Tango Icon Library</a>. The update icon used by SMS was created by David VanScott as a derivative of work done for the Tango Icon Library.\r\n\r\nThe rank sets (DS9 Era Duty Uniform Style #2, DS9 Era Dress Uniform Style #2, and DS9 Alternate Style #1) used in SMS 2 were created by Kuro-chan of <a href=\"http://www.kuro-rpg.com\" target=\"_blank\">Kuro-RPG</a>. Please do not copy or modify the images in any way, simply contact Kuro-chan and he will see to your rank needs.\r\n\r\n<a href=\"http://www.kuro-rpg.com/\" target=\"_blank\"><img src=\"images/kurorpg-banner.jpg\" border=\"0\" alt=\"Kuro-RPG\" /></a>' WHERE messageid = '1' LIMIT 1" );

?>