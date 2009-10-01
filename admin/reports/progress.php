<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/reports/progress.php
Purpose: Page that shows the progress the sim has made

System Version: 2.6.1
Last Modified: 2008-08-16 1654 EST
**/

/* access check */
if( in_array( "r_progress", $sessionAccess ) ) {
	
	if(isset($_GET['period']) && is_numeric($_GET['period']))
	{
		$period = $_GET['period'];
	}
	else
	{
		$period = 3;
	}

	/* do some logic to determine the right period */
	switch($period)
	{
		case 3:
			$periodLong = 90;
			break;
		case 6:
			$periodLong = 180;
			break;
		case 9:
			$periodLong = 270;
			break;
		case 12:
			$periodLong = 365;
			break;
	}

	/* pull today's information */
	$today = getdate();

	/* get the target date */
	$targetDateSetup = $periodLong * ( 86400 );
	$targetDate = $today[0] - $targetDateSetup;

	/* format the date nicely */
	$targetDateFormatted = date( 'Y-m-d', $targetDate );

	/* pull the target date into an array */
	$targetDateArray = explode( "-", $targetDateFormatted );

	$todayNice = $today['year'] . "-" . ( $today['mon'] +1 ) . "-01 00:00:00";
	$targetNice = $targetDateArray['0'] . "-" . $targetDateArray['1'] . "-01 00:00:00";

	if( $jpCount == "n" ) {
	
		/* do a count query on the missionposts table */
		$totalPosts = "SELECT count( postid ) FROM sms_posts WHERE postPosted > '$targetDate' AND ";
		$totalPosts.= "postPosted < '$today[0]' AND postStatus = 'activated'";
		$totalPostsResult = mysql_query( $totalPosts );
		$totalPostsStats = mysql_fetch_array( $totalPostsResult );
		$postcount = $totalPostsStats['0'];
	
	} elseif( $jpCount == "y" ) {
		
		/* do a count query on the missionposts table */
		$totalPosts = "SELECT postAuthor FROM sms_posts WHERE postPosted > '$targetDate' AND ";
		$totalPosts.= "postPosted < '$today[0]' AND postStatus = 'activated'";
		$totalPostsResult = mysql_query( $totalPosts );
		$countRows = mysql_num_rows( $totalPostsResult );
		$authorArray = array();
		
		while( $counting = mysql_fetch_array( $totalPostsResult ) ) {
			extract( $counting, EXTR_OVERWRITE );
			
			/* explode the array from the query */
			$authorString = explode( ",", $postAuthor );
			
			/* count the number of elements */
			$arrayCount = count( $authorString );
			
			/* loop through the elements in the array and add them */
			/* to the end of the master array */
			for( $i = 0; $i < $arrayCount; $i++ ) {
				$authorArray[] = $authorString[$i];
			}
		}
		
		/* count the elements in the array */
		$postcount = count( $authorArray );

	}

	/* get the total logs in the given time period */
	$totalLogs = "SELECT count( logid ) FROM sms_personallogs WHERE logPosted > '$targetDate' AND ";
	$totalLogs.= "logPosted < '$today[0]' AND logStatus = 'activated'";
	$totalLogsResult = mysql_query( $totalLogs );
	$totalLogsStats = mysql_fetch_array( $totalLogsResult );

	/* get the total news items in the given time period */
	$totalNews = "SELECT count( newsid ) FROM sms_news WHERE newsPosted > '$targetDate' AND ";
	$totalNews.= "newsPosted < '$today[0]' AND newsStatus = 'activated'";
	$totalNewsResult = mysql_query( $totalNews );
	$totalNewsStats = mysql_fetch_array( $totalNewsResult );

?>

	<div class="body">
		<span class="fontTitle">Sim Progress</span><br /><br />
	
		<b>Select a time period:</b><br />
		<a href="<?=$webLocation;?>admin.php?page=reports&sub=progress&period=3">3 Months</a>
		&nbsp; &middot; &nbsp;
		<a href="<?=$webLocation;?>admin.php?page=reports&sub=progress&period=6">6 Months</a>
		&nbsp; &middot; &nbsp;
		<a href="<?=$webLocation;?>admin.php?page=reports&sub=progress&period=9">9 Months</a>
		&nbsp; &middot; &nbsp;
		<a href="<?=$webLocation;?>admin.php?page=reports&sub=progress&period=12">1 Year</a>
		<br /><br />
	
		<table>
			<tr>
				<td colspan="3" class="fontLarge"><b>Total Statistics</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Total Posts</td>
				<td></td>
				<td><?=$postcount;?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Total Logs</td>
				<td></td>
				<td><?=$totalLogsStats['0'];?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Total News Items</td>
				<td></td>
				<td><?=$totalNewsStats['0'];?></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<?
	
			for( $i = 0; $i <= $period; $i++ ) {
	
				/* month is equal to the target month PLUS $i */
				$month = $targetDateArray['1'] + $i;
	
				/* next month is equal to the target month PLUS $i PLUS 1 */
				$monthNext = $targetDateArray['1'] + $i + 1;
	
				/* reportYear is equal to the target year */
				$reportYear = $targetDateArray['0'];
	
				/* reportYearNext is equal to the target year PLUS 1 */
				$reportYearNext = $targetDateArray['0'] + 1;
	
				/* if the month we're dealing with is greater than */
				/* 12, subtract the month from 12 to get the correct */
				/* month number */
				if( $month > "12" ) {
					$monthNice = $month - 12;
					
					if( $monthNice < "10" ) {
						$monthNice = "0" . $monthNice;
					}
					
					$format1 = ( $reportYearNext ) . "-" . $monthNice;
				} else {
					$monthNice = $month;
	
					if( $monthNice < "10" ) {
						$monthNice = "0" . $monthNice;
					}
					
					$format1 = $reportYear . "-" . $monthNice;
				}
	
				/* if the next month we're dealing with is greater than */
				/* 12, subtract the month from 12 to get the correct */
				/* next month number */
				if( $monthNext > "12" ) {
					$monthNextNice = $monthNext - 12;
	
					if( $monthNextNice < "10" ) {
						$monthNextNice = "0" . $monthNextNice;
					}
					
					$format2 = ( $reportYearNext ) . "-" . $monthNextNice;
				} else {
					$monthNextNice = $monthNext;
					
					if( $monthNextNice < "10" ) {
						$monthNextNice = "0" . $monthNextNice;
					}
					
					$format2 = $reportYear . "-" . $monthNextNice;
				}
	
				/* do some logic to make sure the year is advance after december */
				if( $format2 == $reportYearNext . "-13" ) {
					$format2 = $reportYearNext + 1 . "-01";
					$yearNice = $reportYearNext + 1;
				}
				
				/* build the monthStart and monthEnd variables */
				$monthStartRaw = $format1 . "-01 00:00:00";
				$monthEndRaw = $format2 . "-01 00:00:00";
				
				$monthStart = strtotime( $monthStartRaw );
				$monthEnd = strtotime( $monthEndRaw );
	
			?>
			<tr>
				<td colspan="3" class="fontLarge"><b>
					<?
	
					switch ( $monthNice ) {
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
	
						echo " ";
	
						/* explode the month start format variable into an array */
						$yearArray = explode( "-", $format1 );
	
						/* echo out the year */
						echo $yearArray['0'];
	
						if( $jpCount == "n" ) {
		
							/* do a count query on the missionposts table */
							$totalPosts = "SELECT count( postid ) FROM sms_posts WHERE postPosted > '$monthStart' AND ";
							$totalPosts.= "postPosted < '$monthEnd' AND postStatus = 'activated'";
							$totalPostsResult = mysql_query( $totalPosts );
							$totalPostsStats = mysql_fetch_array( $totalPostsResult );
							$postcount = $totalPostsStats['0'];
						
						} elseif( $jpCount == "y" ) {
							
							/* do a count query on the missionposts table */
							$totalPosts = "SELECT postAuthor FROM sms_posts WHERE postPosted > '$monthStart' AND ";
							$totalPosts.= "postPosted < '$monthEnd' AND postStatus = 'activated'";
							$totalPostsResult = mysql_query( $totalPosts );
							$countRows = mysql_num_rows( $totalPostsResult );
							$authorArray = array();
							
							while( $counting = mysql_fetch_array( $totalPostsResult ) ) {
								extract( $counting, EXTR_OVERWRITE );
								
								/* explode the array from the query */
								$authorString = explode( ",", $postAuthor );
								
								/* count the number of elements */
								$arrayCount = count( $authorString );
								
								/* loop through the elements in the array and add them */
								/* to the end of the master array */
								for( $j = 0; $j < $arrayCount; $j++ ) {
									$authorArray[] = $authorString[$j];
								}
							}
							
							/* count the elements in the array */
							$postcount = count( $authorArray );
					
						}
					
						/* get the total logs in the given time period */
						$totalLogs = "SELECT count( logid ) FROM sms_personallogs WHERE logPosted BETWEEN ";
						$totalLogs.= "'$monthStart' AND '$monthEnd' AND logStatus = 'activated'";
						$totalLogsResult = mysql_query( $totalLogs );
						$totalLogsStats = mysql_fetch_array( $totalLogsResult );
						$logcount = $totalLogsStats['0'];
					
						/* get the total news items in the given time period */
						$totalNews = "SELECT count( newsid ) FROM sms_news WHERE newsPosted BETWEEN ";
						$totalNews.= "'$monthStart' AND '$monthEnd' AND newsStatus = 'activated'";
						$totalNewsResult = mysql_query( $totalNews );
						$totalNewsStats = mysql_fetch_array( $totalNewsResult );
						$newscount = $totalNewsStats['0'];
					
					?>
				</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Mission Posts</td>
				<td></td>
				<td><?=$postcount;?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Personal Logs</td>
				<td></td>
				<td><?=$logcount;?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">News Items</td>
				<td></td>
				<td><?=$newscount;?></td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<? } ?>
		</table>
		
	</div>
	
<? } else { errorMessage( "sim progress" ); } ?>