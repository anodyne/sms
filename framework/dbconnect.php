<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/dbconnect.php
Purpose: Database connection file
	
System Version: 2.6.1
Last Modified: 2008-07-27 1100 EST
**/

/* pull in the variables */
include_once( 'variables.php' );

/* make sure we're setting the right value for the database table */
if (isset($dbName))
{
	$database_table = $dbName;
}
elseif (isset($dbTable))
{
	$database_table = $dbTable;
}

/* database connection */
$db = @mysql_connect( $dbServer, $dbUser, $dbPassword ) or die ( "<b>" . $dbErrorMessage . "</b>" );
mysql_select_db( $database_table, $db ) or die ( "<b>Unable to select the appropriate database.  Please try again later.</b>" );

?>