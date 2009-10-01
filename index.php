<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: index.php
Purpose: The main file that pulls in the requested page

System Version: 2.6.3
Last Modified: 2008-10-05 0034 EST
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

/* fixing error reporting issues */
error_reporting(0);
ini_set('display_errors', 0);

/* query the db for the system information */
$getVer = "SELECT sysVersion FROM sms_system WHERE sysid = 1";
$getVerResult = mysql_query( $getVer );
	
if( !empty( $getVerResult ) ) {
	$updateVersion = mysql_fetch_array( $getVerResult );
}

/*
make sure the user is running 2.5, and if not, push them
to the install page to update from the earlier version
*/
if( $updateVersion[0] < "2.5.0" || empty($dbUser) ) {
	header( 'Location: install.php' );
	exit;
} else {
	
	/* close the db connection to avoid any problems */
	mysql_close($db);

	/* pull in the global functions file */
	require_once('framework/functionsGlobal.php');
	require_once('framework/functionsAdmin.php');
	require_once('framework/functionsUtility.php');
	require_once('framework/classes/utility.php');
	require_once('framework/classMenu.php');

	/* get the referenced page from the URL */
	if( isset( $_GET['page'] ) ) {
		$page = $_GET['page'];
	} else {
		$page = "main";
	}
	
	/* if there's a session set, define the session variables */
	if( isset( $_SESSION['sessionCrewid'] ) ) {
		$sessionCrewid = $_SESSION['sessionCrewid'];
		$sessionDisplaySkin = $_SESSION['sessionDisplaySkin'];
		$sessionDisplayRank = $_SESSION['sessionDisplayRank'];
	}
	
	/* fixes a PHP warning with an undefined variable */
	if( !isset( $sessionAccess ) ) {
		$sessionAccess = "";
	}
	
	/* if the sessionAccess isn't an array, make it one */
	if( !is_array($sessionAccess) && isset($_SESSION['sessionAccess']) ) {
		$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
	}
	
	/* grab the user's skin choice, otherwise, use the system default */
	if( isset( $sessionDisplaySkin ) ) {
		include_once( 'skins/' . $sessionDisplaySkin . '/header.php' );
	} else {
		include_once( 'skins/' . $skin . '/header.php' );
	}
	
	/* pull in the page referenced in the URL */
	if( file_exists( 'pages/' . $page . '.php' ) ) {	
		include_once( 'pages/' . $page . '.php' );
	} else {
		include_once( 'pages/error.php' );
	}
	
	/* grab the user's skin choice, otherwise, use the system default */
	if( isset( $sessionDisplaySkin ) ) {
		include_once( 'skins/' . $sessionDisplaySkin . '/footer.php' );
	} else {
		include_once( 'skins/' . $skin . '/footer.php' );
	}

}

?>