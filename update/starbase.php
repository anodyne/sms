<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/starbase.php
Purpose: Update page to change the system over to use starbase menus
Last Modified: 2007-07-22 1949 EST
**/

/* update the globals so the simm is a starbase */
mysql_query( "UPDATE sms_globals SET simmType = 'starbase' WHERE globalid = '1' LIMIT 1" );

/* change the main menu text from ship to starbase */
mysql_query( "UPDATE sms_menu_items SET menuTitle = 'The Starbase', menuLink = 'index.php?page=starbase' WHERE menuid = '3' LIMIT 1" );

/* change the tour and history from ship to starbase */
mysql_query( "UPDATE sms_menu_items SET menuTitle = 'Starbase Tour' WHERE menuid = '20' LIMIT 1" );
mysql_query( "UPDATE sms_menu_items SET menuTitle = 'Starbase History', WHERE menuid = '18' LIMIT 1" );

/* add the docked ships and docking request menu items */
mysql_query( "INSERT INTO sms_menu_items ( menuGroup, menuOrder, menuTitle, menuLink, menuMainSec, menuCat ) VALUES ( '1', '1', 'Docked Ships', 'index.php?page=dockedships', 'ship', 'general' ) " );
mysql_query( "INSERT INTO sms_menu_items ( menuGroup, menuOrder, menuTitle, menuLink, menuMainSec, menuCat ) VALUES ( '1', '0', 'Docking Request', 'index.php?page=dockingrequest', 'ship', 'general' ) " );

/* add the docking management link to the admin menu */
mysql_query( "INSERT INTO sms_menu_items ( menuGroup, menuOrder, menuTitle, menuLink, menuMainSec, menuCat, menuAccess, menuLogin ) VALUES ( '4', '8', 'Docked Ships', 'admin.php?page=manage&sub=docking', 'manage', 'admin', 'm_docking', 'y' ) " );

?>