<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/functionsGlobal.php
Purpose: File that holds all the necessary global function files for JP author printing,
	database connection, and error catching
	
System Version: 2.6.10
Last Modified: 2009-09-07 2036 EST

Included Functions:
	displayAuthors( $missionID, $link )
	printCO()
	printXO()
	printCOEmail()
	printXOEmail()
	printPlayerPosition( $crewid, $position, $positionNumber )
	displayEmailAuthors( $authors, $link )
	printMissionTitle( $missionid )
	getCrewEmails( $type )
	printCrewNameEmail( $id )
	escape_string( $value )
	update_position( $position, $type )
**/

/*
|---------------------------------------------------------------
| DATABASE CONNECTION
|---------------------------------------------------------------
|
| This gets us our connection to the database using the database
| connection variables set in the variables file.
|
*/
include_once('variables.php');

/* make sure we're setting the right value for the database table */
if (isset($dbName))
{
	$database_table = $dbName;
}
elseif (isset($dbTable))
{
	$database_table = $dbTable;
}

$db = @mysql_connect($dbServer, $dbUser, $dbPassword) or die ("<b>" . $dbErrorMessage . "</b>");
mysql_select_db($database_table, $db) or die ("<b>Unable to select the appropriate database.  Please try again later.</b>");

/*
|---------------------------------------------------------------
| GLOBAL INFORMATION
|---------------------------------------------------------------
|
| We'll need to get all the globals and messages for use throughout
| the system. In addition, we'll get the system UID and version to
| use in the system check.
|
*/
$sms = "SELECT globals.*, messages.*, sys.sysuid, sys.sysVersion FROM sms_globals AS globals, sms_messages AS messages, ";
$sms.= "sms_system AS sys WHERE globals.globalid = '1' AND messages.messageid = '1' AND sys.sysid = '1'";
$smsResult = mysql_query($sms);

while($fetchSMS = mysql_fetch_assoc($smsResult)) {
	extract($fetchSMS, EXTR_OVERWRITE);
}

/*
|---------------------------------------------------------------
| SYSTEM INFORMATION
|---------------------------------------------------------------
|
| This is system's file version and the system UID. Do not change
| either of these variables! Doing so WILL cause problems within
| the system as a whole.
|
*/
$version = "2.6.10";
define('UID', $sysuid);

/*
|---------------------------------------------------------------
| SYSTEM CONSTANTS
|---------------------------------------------------------------
|
| These constants are used throughout the system for various things,
| though you may find they aren't used consistently throughout. That
| will be addressed more in Jefferson.
|
*/
define( 'WEBLOC', $webLocation );
define( 'VER_FILES', $version );
define( 'VER_DB', $sysVersion );
define( 'SHIP_NAME', $shipName );
define( 'SHIP_PREFIX', $shipPrefix );
define( 'SHIP_REG', $shipRegistry );
define( 'SIM_YEAR', $simmYear );

/*
|---------------------------------------------------------------
| GLOBAL FUNCTIONS
|---------------------------------------------------------------
|
| The title kinda lies, but moving things around at this stage in
| the game just would be more of a headache than it's worth. These
| functions are used in both the authenticated parts as well as the
| unauthenticated parts of SMS.
|
*/
/**
	JP Author Function
**/
function displayAuthors( $missionID, $link ) {

	$sql = "SELECT postAuthor FROM sms_posts WHERE postid = '$missionID' LIMIT 1";
	$result = mysql_query( $sql );
	$myrow = mysql_fetch_array( $result );
	$authorsString = FALSE;
	
	/* explode the string at the comma */
	$authors_raw = explode( ",", $myrow['0'] );
	
	/*
		start the loop based on whether there are key/value pairs
		and keep doing something until you run out of pairs
	*/
	foreach( $authors_raw as $key => $value ) {
		
		/* do the database query */
		$sql = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$sql.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$sql.= "WHERE crew.crewid = '$value' AND crew.rankid = rank.rankid";
		$result = mysql_query( $sql );
		
		/* Start pulling the array and populate the variables */
		while( $authorsStart = mysql_fetch_assoc( $result ) ) {
			extract( $authorsStart, EXTR_OVERWRITE );
			
			$authors = array();
			
			if( $link == "link" ) {
				$authors = array(
					"<a href='" . WEBLOC . "index.php?page=bio&crew=" . $authorsStart['crewid'] . "'>" . $rankName . " " . $firstName . " " . $lastName . "</a>"
				);
			} else {
				$authors = array(
					$rankName . " " . $firstName . " " . $lastName
				);
			}
			
			$authors_array[] = $authors[0];
			
			$authorsString = implode( " &amp; ", $authors_array );
			
		}	/* close the while loop */
	}	/* close the foreach loop */
	
	echo stripslashes( $authorsString );
		
}
/* END FUNCTION */

/**
	Print out the commanding officer
**/
function printCO($rank = 'long_rank') {
	switch($rank)
	{
		case 'long_rank':
			$rank_field = "rank.rankName";
			break;
		case 'short_rank':
			$rank_field = "rank.rankShortName";
			break;
	}
	
	$getCO = "SELECT crew.firstName, crew.lastName, $rank_field FROM sms_crew AS crew, sms_ranks AS rank ";
	$getCO.= "WHERE crew.positionid = 1 AND crew.crewType = 'active' AND crew.rankid = rank.rankid LIMIT 1";
	$getCOResult = mysql_query($getCO);
	$coFetch = mysql_fetch_array($getCOResult);
	
	return $coFetch[2] . " " . $coFetch[0] . " " . $coFetch[1];

}
/* END FUNCTION */

/**
	Print out the executive officer
**/
function printXO($rank = 'long_rank') {
	switch($rank)
	{
		case 'long_rank':
			$rank_field = "rank.rankName";
			break;
		case 'short_rank':
			$rank_field = "rank.rankShortName";
			break;
	}
	
	$getXO = "SELECT crew.firstName, crew.lastName, $rank_field FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.positionid = 2 AND crew.crewType = 'active' AND crew.rankid = rank.rankid LIMIT 1";
	$getXOResult = mysql_query( $getXO );
	$xoFetch = mysql_fetch_array( $getXOResult );
	
	return $xoFetch[2] . " " . $xoFetch[0] . " " . $xoFetch[1];

}
/* END FUNCTION */

/**
	Print out the commanding officer
**/
function printCOEmail() {
	
	$getCOEmail = "SELECT email FROM sms_crew WHERE positionid = '1' AND crewType = 'active' LIMIT 1";
	$getCOEmailResult = mysql_query( $getCOEmail );
	$email = mysql_fetch_array( $getCOEmailResult );
	
	return $email[0];

}
/* END FUNCTION */

/**
	Print out the executive officer
**/
function printXOEmail() {
	
	$getXOEmail = "SELECT email FROM sms_crew WHERE positionid = '2' AND crewType = 'active' LIMIT 1";
	$getXOEmailResult = mysql_query( $getXOEmail );
	$email = mysql_fetch_array( $getXOEmailResult );
	
	return $email[0];

}
/* END FUNCTION */

/**
	Print out the commanding officer
**/
function printPlayerPosition( $crewid, $position, $positionNumber ) {
	
	$getPosition = "SELECT position.positionName FROM sms_crew AS crew, sms_positions AS position ";
	$getPosition.= "WHERE crew.crewid = '$crewid' AND crew.positionid$positionNumber = position.positionid ";
	$getPositionResult = mysql_query( $getPosition );
	
	while( $position = mysql_fetch_array( $getPositionResult ) ) {
		extract( $position, EXTR_OVERWRITE );
	}
	
	echo stripslashes( $positionName );

}
/* END FUNCTION */

/**
	JP Author Function for emails
**/
function displayEmailAuthors( $authors, $link ) {

	/* explode the string at the comma */
	$authors_raw = explode( ",", $authors );
	
	/*
		start the loop based on whether there are key/value pairs
		and keep doing 'something' until you run out of pairs
	*/
	foreach( $authors_raw as $key => $value ) {
		
		/* do the database query */
		$sql = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$sql.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$sql.= "WHERE crew.crewid = '$value' AND crew.rankid = rank.rankid";
		$result = mysql_query( $sql );
		
		/* Start pulling the array and populate the variables */
		while( $authorsStart = mysql_fetch_assoc( $result ) ) {
			extract( $authorsStart, EXTR_OVERWRITE );
			
			$authors = array();
			
			if( $link == "link" ) {
				$authors = array(
					"<a href='" . WEBLOC . "index.php?page=bio&crew=" . $authorsStart['crewid'] . "'>" . $rankName . " " . $firstName . " " . $lastName . "</a>"
				);
			} else {
				$authors = array(
					$rankName . " " . $firstName . " " . $lastName
				);
			}
			
			$authors_array[] = $authors[0];
			
			$authorsString = implode( " & ", $authors_array );
			
		}	/* close the while loop */
	}	/* close the foreach loop */
	
	return $authorsString;
		
}
/* END FUNCTION */

/**
	Function to pull the mission title
**/
function printMissionTitle( $missionid ) {
	
	/* query the database to get the title */
	$getTitle = "SELECT missionTitle FROM sms_missions WHERE missionid = '$missionid' LIMIT 1";
	$getTitleResult = mysql_query( $getTitle );
	$fetchTitle = mysql_fetch_assoc( $getTitleResult );
	
	/* return the var */
	return $fetchTitle['missionTitle'];

}
/* END FUNCTION */

/**
	Function to pull the mission title
**/
function getCrewEmails( $type ) {
	
	$getEmails = "SELECT crewid, email FROM sms_crew WHERE $type = 'y' AND crewType = 'active' GROUP BY email";
	$getEmailsResult = mysql_query( $getEmails );
	$countEmails = mysql_num_rows( $getEmailsResult );
	
	$recipients = "";
	for( $j=0; $j < $countEmails; $j++ ) {
		$user = mysql_fetch_assoc( $getEmailsResult );
		$u_id = $user['crewid'];
		$u_email = $user['email'];
		if( empty( $recipients ) ) {
			$recipients = $u_email;
		} else {
			$recipients = $recipients . ", " . $u_email;
		}
	}
                        
	return $recipients;

}
/* END FUNCTION */

/**
	Admin function that will pull the user's first name, last name, rank, and rank image
**/
function printCrewNameEmail( $id ) {
	
	$nameFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
	$nameFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
	$nameFetch.= "WHERE crew.crewid = '$id' AND crew.rankid = rank.rankid LIMIT 1";
	$nameFetchResult = mysql_query( $nameFetch );
	
	while( $userFetchArray = mysql_fetch_array( $nameFetchResult ) ) {
		extract( $userFetchArray, EXTR_OVERWRITE );
	
		$name = $rankName . " " . $firstName . " " . $lastName;
		
	}
	
	return $name;
	
}
/** END FUNCTION **/

/**
	Function to scrub the SQL statements for injection
**/
function escape_string( $value )
{
	if( get_magic_quotes_gpc() )
	{
		$value = stripslashes( $value );
	}
	
	if( !is_numeric( $value ) )
	{
		$value = "'" . mysql_real_escape_string( $value ) . "'";
	}
	
	return $value;
	
}
/** END FUNCTION **/

/**
	Function to update the open positions number
**/
function update_position( $position, $type )
{
	$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
	$positionFetch.= "WHERE positionid = '$position' LIMIT 1";
	$positionFetchResult = mysql_query( $positionFetch );
	$positionX = mysql_fetch_row( $positionFetchResult );

	$open = $positionX[1];
	
	switch($type)
	{
		case 'give':
			$revised = ( $open - 1 );
			break;
		case 'take':
			$revised = ( $open + 1 );
			break;
	}

	$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
	$updatePosition.= "WHERE positionid = '$position' LIMIT 1";
	$updatePositionResult = mysql_query( $updatePosition );
}
/** END FUNCTION **/

?>