<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin.php
Purpose: The main file that pulls in the requested administration page

System Version: 2.6.3
Last Modified: 2008-10-05 0050 EST
**/

/* pull in the DB connection variables */
require_once('framework/dbconnect.php');

/* pulling a function from new library */
require_once('framework/session.name.php');

/* get system unique identifier */
$sysuid = get_system_uid();

/* rewrite master php.ini session.name */
ini_set('session.name', $sysuid);

/* start the session */
session_start();

/* pull in the DB connection variables */
require_once( 'framework/variables.php' );
require_once( 'framework/dbconnect.php' );

/* query the db for the system information */
$getVer = "SELECT sysVersion FROM sms_system WHERE sysid = 1";
$getVerResult = mysql_query( $getVer );
$updateVersion = mysql_fetch_array( $getVerResult );
	
/*
make sure the user is running 2.5, and if not, push them
to the install page to update from the earlier version
*/
if( $updateVersion[0] < "2.5.0" ) {
	header( 'Location: ' . $webLocation . 'install.php' );
} else {
	
	/* close the db connection to avoid any problems */
	mysql_close( $db );

	/* pull in the global functions file */
	require_once( 'framework/functionsGlobal.php' );
	require_once( 'framework/functionsAdmin.php' );
	require_once( 'framework/functionsUtility.php' );
	require_once( 'framework/classes/utility.php' );
	require_once( 'framework/classMenu.php' );
	require_once( 'framework/classes/check.php' );
	
	/* set the variables */
	$page = $_GET['page'];

	/* define the session variables */
	$sessionCrewid = $_SESSION['sessionCrewid'];
	$sessionAccessLevel = $_SESSION['sessionAccess'];
	$sessionDisplaySkin = $_SESSION['sessionDisplaySkin'];
	$sessionDisplayRank = $_SESSION['sessionDisplayRank'];
	
	/* define some path variables */
	define( 'path_userskin', $webLocation . 'skins/' . $sessionDisplaySkin . '/' );
	
	/* fixes a PHP warning with an undefined variable */
	if( !isset( $sessionAccess ) ) {
		$sessionAccess = "";
	}
	
	/*
		check to see if the session access variable is an array
		and if it isn't, explode the string
	*/
	if( !is_array( $sessionAccess ) ) {
		$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
	}
	
	/* if there is no page set, send them to the main page */
	if( !$page ) {
		$page = "main";
	}
	
	/* if the session is set, continue, otherwise, send them to the index page */
	if( isset( $sessionCrewid ) ) {
		
		/* grab the user's skin choice, otherwise, use the system default */
		if( isset( $sessionDisplaySkin ) ) {
			include_once( 'skins/' . $sessionDisplaySkin . '/header.php' );
		} else {
			include_once( 'skins/' . $skin . '/header.php' );
		}
		
		/* pull in the page referenced in the URL */
		include_once( 'admin/' . $page . '.php' );
		
		/* grab the user's skin choice, otherwise, use the system default */
		if( isset( $sessionDisplaySkin ) ) {
			include_once( 'skins/' . $sessionDisplaySkin . '/footer.php' );
		} else {
			include_once( 'skins/' . $skin . '/footer.php' );
		}
	
	} else {
		header( 'Location: ' . $webLocation . 'login.php?action=login&login=false&error=3' );
	}
	
}

?>