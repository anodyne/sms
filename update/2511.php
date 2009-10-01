<?php

/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: update/2511.php
Purpose: Update page to 2.5.2
Last Modified: 2008-04-24 1246 EST
**/

/* add the on/off switch for the menu items */
mysql_query( "ALTER TABLE `sms_menu_items` ADD `menuAvailability` enum( 'on', 'off' ) default 'on' not null" );

/**
	set up a multi-dimensional array for the timestamp update
	[x][0] => table's primary key
	[x][1] => field being updated
	[x][2] => table being updated
**/
$array = array(
	0 => array( 'crewid', 'joinDate', 'sms_crew' )
);

/* loop through the array */
foreach( $array as $key => $value ) {

	/* pull in the info from the database */
	$getTime = "SELECT $value[0], $value[1] FROM $value[2] ORDER BY $value[0] ASC";
	$getTimeResult = mysql_query( $getTime );
	$getTimeCount = @mysql_num_rows( $getTimeResult );
	
	/* count the rows to avoid SQL errors */
	if( $getTimeCount >= 1 ) {
	
		/* loop through the results */
		while( $timeFetch = mysql_fetch_array( $getTimeResult ) ) {
			extract( $timeFetch, EXTR_OVERWRITE );
			
			/*
				make sure what the function is being fed is actually a
				SQL timestamp and not a UNIX timestamp 
			*/
			if( preg_match( "/^\d+$/", $timeFetch[1], $matches ) ) {} else {
			
				/* do some logic to make sure things are going to be updated correctly */
				if( $timeFetch[1] == "0000-00-00 00:00:00" || $timeFetch[1] == "-1" ) {
					$newTime = "";
				} else {
					$newTime = strtotime( $timeFetch[1] );
				}
				
				/* update the database */
				$update = "UPDATE $value[2] SET $value[1] = '$newTime' ";
				$update.= "WHERE $value[0] = '$timeFetch[0]' LIMIT 1";
				$updateResult = mysql_query( $update );
			
			}
		
		} /* close the while loop */
		
	} /* close the count check */
	
} /* close the foreach loop */

/* add the data for FirstLaunch */
mysql_query( "INSERT INTO sms_system_versions ( `version`, `versionDate`, `versionShortDesc`, `versionDesc` ) VALUES ( '2.5.2', '1186709400', 'This release patches several outstanding bugs in SMS as well as enhancing existing features with additional functionality.', 'Fixed inconsistencies in granting of permissions across the system;Fixed bug where saving a joint post with 4 participants would overwrite the third author with a blank variable;Added more notes to the user access control sections;Removed inactive crew list from the user access control listing since inactive players shouldn\'t have access;Changed tab text in site globals to prevent from wrapping to a new line at 1024x768 resolutions;Fixed incorrect display of dates on user statistics page;Fixed bug where when changing a user\'s position, the open count wouldn\'t increment;Added automatic access level change when moving between senior and crew positions;Added visual notification of whether menu items are ON or OFF;Added page to add/remove a given access level for the entire crew at the same time;Added page that gives full listing of a given user\'s access;Added user access report link to the full crew listing by the stats link;Added display of second position (if applicable) to the active crew list;When the SMS posting system is turned on or off, the system will take actions to make sure the people are either stripped of or given posting access;Added logic to installation that will detect the MySQL version and add the UTF8 character set if the version is 4.0 and higher;Fixed install bug where if the variables file was written the webLocation variable would be empty;Fixed bug where deck listing textareas would show HTML break tags after updating;Fixed bug where join page set wrong timestamp;Added nice message if the join date is empty instead of the 37 years, etc.;Fixed bug on milestones report where time wouldn\'t display if it was 1 day or less;Updated logic on milestones report to display date in a nicer fashion;Improved display for dates less than 1 day on the activity report;Added on/off switch control to each menu item;Fixed bug on login where error message would extend across entire screen;Reactivated emailing of password on password reset;Added visual separation between items that need a password to be changed and those that don\'t on the edit account page;Removed username from being listed on the edit acount page unless the person viewing it is the owner of the account;Fixed bug where dates wouldn\'t display by recent posts and logs;Fixed account bug where admin couldn\'t change active status of a player from the edit account page;Fixed bug where private messages weren\'t being sent or received' )" );

?>