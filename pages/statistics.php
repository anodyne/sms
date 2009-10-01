<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/statistics.php
Purpose: To display the monthly statistics for the simm

System Version: 2.5.0
Last Modified: 2007-07-23 0926 EST
**/

/* define the page class */
$pageClass = "simm";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* get today's date */
$today = getdate();

/** PREVIOUS MONTH **/

/* create variables to be used in the query */
$previousMonth = $today['mon'] -1;
$previousYear = $today['year'];

if( $previousMonth == "0" ) {
	$previousMonth = "12";
} if( $previousMonth == "12" ) {
	$previousYear = $today['year'] -1;
}

/* create variables to be used in the query for previous month */
$previousmonth_start = $previousYear . "-" . $previousMonth . "-01 00:00:00";
$previousmonth_end = $previousYear . "-" . ($previousMonth +1) . "-01 00:00:00";

/* do some logic to make sure it doesn't break at the end of each month for previous month */
if( $previousmonth_end == $previousYear . "-13-01 00:00:00" ) {
	$previousmonth_end = ($previousYear +1) . "-01-01 00:00:00";
}

/** CURRENT MONTH **/

/* create variables to be used in the query */
$month_start = $today['year'] . "-" . $today['mon'] . "-01 00:00:00";
$month_end = $today['year'] . "-" . ($today['mon']+1) . "-01 00:00:00";

/* do some logic to make sure it doesn't break at the end of each month */
if($month_end == $today['year'] . "-13-01 00:00:00") {
	$month_end = ($today['year'] +1) . "-01-01 00:00:00";
}

/* unix timestamps */
$previousmonthStartU = strtotime( $previousmonth_start );
$previousmonthEndU = strtotime( $previousmonth_end );
$monthStartU = strtotime( $month_start );
$monthEndU = strtotime( $month_end );

/** QUERY DATABASE **/

/** Previous Month **/

if( $jpCount == "n" ) {
	
	/* do a count query on the sms_posts table for previous month */
	$previousposts = "SELECT count(postid) ";
	$previousposts.= "FROM sms_posts ";
	$previousposts.= "WHERE postPosted > '$previousmonthStartU' ";
	$previousposts.= "AND postPosted < '$previousmonthEndU' AND postStatus = 'activated'";
	$result_previousposts = mysql_query( $previousposts );
	$previouspostcount = mysql_fetch_array( $result_previousposts );
	$previousPosts = $previouspostcount['0'];
	
} elseif( $jpCount == "y" ) {

	/* do a count query on the sms_posts table for previous month */
	$previousposts = "SELECT postAuthor FROM sms_posts WHERE postPosted > '$previousmonthStartU' ";
	$previousposts.= "AND postPosted < '$previousmonthEndU' AND postStatus = 'activated'";
	$result_previousposts = mysql_query( $previousposts );
	$countRows = mysql_num_rows( $result_previousposts );
	$authorArray = array();
	
	while( $counting = mysql_fetch_array( $result_previousposts ) ) {
		extract( $counting, EXTR_OVERWRITE );
		
		/* explode the array from the query */
		$authorString = explode( ",", $postAuthor );
		
		/* count the number of elements */
		$arrayCount = count( $authorString );
		
		/*
			loop through the elements in the array and add them
			to the end of the master array
		*/
		for( $i=0; $i<$arrayCount; $i++ ) {
			$authorArray[] = $authorString[$i];
		}
	}
	
	/* count the elements in the array */
	$previousPosts = count( $authorArray );
	
}

/* do a count query on the sms_personallogs table for previous month */
$previouslogs = "SELECT count( logid ) ";
$previouslogs.= "FROM sms_personallogs ";
$previouslogs.= "WHERE logPosted > '$previousmonthStartU' ";
$previouslogs.= "AND logPosted < '$previousmonthEndU' AND logStatus = 'activated'";
$result_previouslogs = mysql_query( $previouslogs );
$previouslogcount = mysql_fetch_array( $result_previouslogs );

/* do a count query on the sms_crew table for previous month */
$previousactiveCrew = "SELECT count( crewid ) ";
$previousactiveCrew.= "FROM sms_crew ";
$previousactiveCrew.= "WHERE ( joinDate < '$monthStartU' AND crewType = 'active' ) ";
$previousactiveCrew.= "OR ( leaveDate > '$previousmonthStartU' AND leaveDate < '$previousmonthEndU' AND crewType = 'inactive' )";
$previousactiveCrewResult = mysql_query( $previousactiveCrew );
$previousactiveCrewArray = mysql_fetch_array( $previousactiveCrewResult );

/** Current Month **/

if( $jpCount == "n" ) {
	
	/* do a count query on the sms_posts table */
	$posts = "SELECT count( postid ) FROM sms_posts WHERE postPosted > '$monthStartU' ";
	$posts.= "AND postPosted < '$monthEndU' AND postStatus = 'activated'";
	$result_posts = mysql_query( $posts );
	$postcount = mysql_fetch_array( $result_posts );
	$currentPosts = $postcount['0'];
	
} elseif( $jpCount == "y" ) {

	/* do a count query on the sms_posts table */
	$posts = "SELECT postAuthor FROM sms_posts WHERE postPosted > '$monthStartU' ";
	$posts.= "AND postPosted < '$monthEndU' AND postStatus = 'activated'";
	$result_posts = mysql_query( $posts );
	$countRows = mysql_num_rows( $result_posts );
	$authorArray = array();
	
	while( $counting = mysql_fetch_array( $result_posts ) ) {
		extract( $counting, EXTR_OVERWRITE );
		
		/* explode the array from the query */
		$authorString = explode( ",", $postAuthor );
		
		/* count the number of elements */
		$arrayCount = count( $authorString );
		
		/*
			loop through the elements in the array and add them
			to the end of the master array
		*/
		for( $i=0; $i<$arrayCount; $i++ ) {
			$authorArray[] = $authorString[$i];
		}
	}
	
	/* count the elements in the array */
	$currentPosts = count( $authorArray );
	
}

/* do a count query on the sms_personallogs table */
$logs = "SELECT count( logid ) FROM sms_personallogs WHERE logPosted > '$monthStartU' ";
$logs.= "AND logPosted < '$monthEndU' AND logStatus = 'activated'";
$result_logs = mysql_query( $logs );
$logcount = mysql_fetch_array( $result_logs );

/* do a count query on the sms_crew table */
$activeCrew = "SELECT count( crewid ) FROM sms_crew WHERE crewType = 'active'";
$activeCrewResult = mysql_query( $activeCrew );
$activeCrewArray = mysql_fetch_array( $activeCrewResult );

/* set the variables for previous month */
$previousCrew = $previousactiveCrewArray['0'];
$previousLogs = $previouslogcount['0'];
$previousTotalPosts = $previousPosts + $previousLogs;

/* set the variables for the current month */
$currentCrew = $activeCrewArray['0'];
$currentLogs = $logcount['0'];
$currentTotalPosts = $currentPosts + $currentLogs;

/* do the math for the current pace */
if($today['mon'] == "1") {
	$monthDays = "31";
} elseif($today['mon'] == "2") {
	$monthDays = "28";
} elseif($today['mon'] == "3") {
	$monthDays = "31";
} elseif($today['mon'] == "4") {
	$monthDays = "30";
} elseif($today['mon'] == "5") {
	$monthDays = "31";
} elseif($today['mon'] == "6") {
	$monthDays = "30";
} elseif($today['mon'] == "7") {
	$monthDays = "31";
} elseif($today['mon'] == "8") {
	$monthDays = "31";
} elseif($today['mon'] == "9") {
	$monthDays = "30";
} elseif($today['mon'] == "10") {
	$monthDays = "31";
} elseif($today['mon'] == "11") {
	$monthDays = "30";
} elseif($today['mon'] == "12") {
	$monthDays = "31";
}

$pacePosts = round( ( ( $currentPosts ) / ( $today['mday'] ) ) *$monthDays, 2 );
$paceLogs = round( ( ( $currentLogs ) / ( $today['mday'] ) ) * $monthDays, 2 );
$paceTotalPosts = round( ( ( $currentTotalPosts ) / ( $today['mday'] ) ) * $monthDays, 2 );

/* set variables for current months average */
$currentAverageMission = round( ( $currentPosts ) / ( $currentCrew ), 2 );
$currentAverageLogs = round( ( $currentLogs ) / ( $currentCrew ), 2 );
$currentAveragePosts = round( ( $currentTotalPosts ) / ( $currentCrew ), 2 );

/* set variables for previous month and if statments for starting simms */
if ( $previousCrew == "0" ) {

	$previousAverageMission = 0;
	$previousAverageLogs = 0;
	$previousAveragePosts = 0;

} else { 

	$previousAverageMission = round( ( $previousPosts ) / ( $previousCrew ), 2 );
	$previousAverageLogs = round( ( $previousLogs ) / ( $previousCrew ), 2 );
	$previousAveragePosts = round( ( $previousTotalPosts ) / ( $previousCrew ), 2 );
}

?>

<div class="body">
	<span class="fontTitle">Simm Statistics</span><br /><br />
	
	The numbers below represent the posting frequency for last month and this month
	as well as projections for the end of the current month and averages for each category.
	If you have any questions, please contact the CO.  Statistics for the previous month are 
	released on the first day of the new month and immediately following the submission 
	of the monthly Commanding Officer's report.<br /><br />
	
	<div align="center">
		<table cellspacing="2" cellpadding="2">
			<tr>
				<td align="right" width="25%"><b> <?=$today['month'] . " " . $today['year'];?>   </b></td>
				<td width="50%">&nbsp;</td>
				<td align="left" width="25%"><b>
					<?
					switch ( $previousMonth ) {
					case 1:
						echo "January";
						break;
					case 2:
						echo "February";
						break;
					case 3:
						echo "March";
						break;
					 case 4:
						echo "April";
						break;
					 case 5:
						echo "May";
						break;
					 case 6:
						echo "June";
						break;
					 case 7:
						echo "July";
						break;
					 case 8:
						echo "August";
						break;
					 case 9:
						echo "September";
						break;
					 case 10:
						echo "October";
						break;
					 case 11:
						echo "November";
						break;
					 case 12:
						echo "December";
						break;
					}
					
					echo " " . $previousYear;
					?>  </b>
				</td>
			</tr>
			<tr>
				<td align="right"><?=$currentCrew;?></td>
				<td align="center">Total Crew</td>
				<td align="left"><?=$previousCrew;?></td>
			</tr>
			<tr>
				<td align="right"><?=$currentPosts;?></td>
				<td align="center">Total Mission Posts</td>
				<td align="left"><?=$previousPosts;?></td>
			</tr>
			<tr>
				<td align="right"><?=$currentLogs;?></td>
				<td align="center">Total Personal Logs</td>
				<td align="left"><?=$previousLogs;?></td>
			</tr>
			<tr>
				<td align="right"><?=$currentTotalPosts;?></td>
				<td align="center">Total Posts (Mission &amp; Logs)</td>
				<td align="left"><?=$previousTotalPosts;?></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="3" valign="bottom" align="center">
					<b>Averages</b> <font class="fontSmall">&dagger;</font>
				</td>
			</tr>
			<tr>
				<td align="right"><?=$currentAverageMission;?></td>
				<td align="center">Average Mission Posts Per Player</td>
				<td align="left"><?=$previousAverageMission;?></td>
			</tr>
			<tr>
				<td align="right"><?=$currentAverageLogs;?></td>
				<td align="center">Average Personal Logs Per Player</td>
				<td align="left"><?=$previousAverageLogs;?></td>
			</tr>
			<tr>
				<td align="right"><?=$currentAveragePosts;?></td>
				<td align="center">Average Posts (Mission &amp; Log) Per Player</td>
				<td align="left"><?=$previousAveragePosts;?></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="3" valign="bottom" align="center">
					<b>Current Pace</b> <font class="fontSmall">&Dagger;</font>
				</td>
			</tr>
			<tr>
				<td align="right"><?=$pacePosts;?></td>
				<td align="center">Current Mission Post Pace</td>
				<td align="left">&nbsp;</td>
			</tr>
			<tr>
				<td align="right"><?=$paceLogs;?></td>
				<td align="center">Current Personal Log Pace</td>
				<td align="left">&nbsp;</td>
			</tr>
			<tr>
				<td align="right"><?=$paceTotalPosts;?></td>
				<td align="center">Current Total Post Pace</td>
				<td align="left">&nbsp;</td>
			</tr>
		</table>
	</div>
		
		<br /><br />

		<span class="fontNormal">
			<i>&dagger; Averages are calculated by taking the number of posts in the month and dividing by the 
			number of players.  For the current month, the averages are calculated the same but will appear
			smaller until the end of the month.  End of the month averages will look 	similar to previous month average.<br /><br />
			&Dagger; Pace is determined by dividing the number of posts in a month by the number of elapsed
			days, then multiplying by the number of days in a given month.  Actual end of the month 
			numbers may vary.</i>
		</span>

</div>