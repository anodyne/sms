<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/250.php
Purpose: Update page to 2.5.3
Last Modified: 2008-04-24 1244 EST
**/

/* update the site permanent credits to make sure everyone has the right tou link */
mysql_query( "UPDATE sms_messages SET siteCreditsPermanent = 'Editing or removal of the following credits constitutes a material breach of the SMS Terms of Use outlined at the <a href=\"http://www.anodyne-productions.com/index.php?cat=main&page=legal\" target=\"_blank\">SMS ToU</a> page.\r\n\r\nSMS 2 uses the open source browser detection library <a href=\"http://sourceforge.net/projects/phpsniff/\" target=\"_blank\">phpSniff</a> to check for various versions of browsers for maximum compatibility.\r\n\r\nThe SMS 2 Update notification system uses <a href=\"http://magpierss.sourceforge.net/\" target=\"_blank\">MagpieRSS</a> to parse the necessary XML file. Magpie is distributed under the GPL license. Questions and suggestions about MagpieRSS should be sent to <i>magpierss-general@lists.sf.net</i>.\r\n\r\nSMS 2 uses icons from the open source <a href=\"http://tango.freedesktop.org/Tango_Icon_Gallery\" target=\"_blank\">Tango Icon Library</a>. The update icon used by SMS was created by David VanScott as a derivative of work done for the Tango Icon Library.\r\n\r\nThe rank sets (DS9 Era Duty Uniform Style #2 and DS9 Era Dress Uniform Style #2) used in SMS 2 were created by Kuro-chan of <a href=\"http://www.kuro-rpg.net\" target=\"_blank\">Kuro-RPG</a>. Please do not copy or modify the images in any way, simply contact Kuro-chan and he will see to your rank needs.\r\n\r\n<a href=\"http://www.kuro-rpg.net/\" target=\"_blank\"><img src=\"images/kurorpg-banner.jpg\" border=\"0\" alt=\"Kuro-RPG\" /></a>' WHERE messageid = 1" );

/* add the data for FirstLaunch */
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.5.3', '1187026200', 'This release patches several bugs related to player acceptance and rejection, display problems and account management.', 'Provided potential fix for skinners related to strange spacing in the Latest Posts section on the main page when moving paddings from anchors to list items;Fixed display bug with reply button on PM view page;Fixed bug where updating own account wouldn\'t work;Fixed bug where player being accepted or rejected wouldn\'t get an email;Fixed potential bug where player being accepted wouldn\'t be updated correctly' )" );

?>