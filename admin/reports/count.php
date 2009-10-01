<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/reports/count.php
Purpose: Page that shows recent post counts for the sim

System Version: 2.5.0
Last Modified: 2007-06-10 1843 EST
**/

/* access check */
if( in_array( "r_count", $sessionAccess ) ) {
	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "reports";
	
	/**
		Do not edit below this line unless you are highly
		knowledgeable with PHP date functions.  Modification
		of this may inadvertantly break the count function.
	**/
	
	/* get today's date */
	$today = getdate();
		
	/* create variables to be used in the query */
	$monthStartRaw = $today['year'] . "-" . $today['mon'] . "-01 00:00:00";
	$monthEndRaw = $today['year'] . "-" . ( $today['mon']+1 ) . "-01 00:00:00";
	
	/* do some logic to make sure it doesn't break at the end of each month */
	if( $monthEndRaw == $today['year'] . "-13-01 00:00:00" ) {
		$monthEndRaw = ( $today['year'] +1 ) . "-01-01 00:00:00";
	}
	
	/* convert month start and end to a timestamp */
	$monthStart = strtotime( $monthStartRaw );
	$monthEnd = strtotime( $monthEndRaw );
	
	/* take the number of days and multiply it times */
	/* the number of seconds in those days */
	$daysInSeconds = $postCountDefault * ( 86400 );
	
	/* take the number of seconds and subtract it from */
	/* the current date's UNIX timestamp */
	$daysPrior = $today[0] - ( $daysInSeconds );
	
	/* take the UNIX timestamp and convert it into something */
	/* that SQL can use in the query */
	$dateShift = dateFormat( "sql", $daysPrior );
	
	/* format today's date for SQL */
	$todayDate = dateFormat( "sql", $today[0] );
	
?>
	
	<div class="body">
		<span class="fontTitle">Post Count Report</span><br /><br />
	
		<?
		
		if( $jpCount == "n" ) {
		
			/* do a count query on the missionposts table */
			$posts = "SELECT count( postid ) FROM sms_posts WHERE postPosted > '$monthStart' ";
			$posts.= "AND postPosted < '$monthEnd' AND postStatus = 'activated'";
			$postsResult = mysql_query( $posts );
			$postcount = mysql_fetch_array( $postsResult );
			
			/* do a count query on the personallogs table */
			$logs = "SELECT count( logid ) FROM sms_personallogs WHERE logPosted > '$monthStart' ";
			$logs.= "AND logPosted < '$monthEnd' AND logStatus = 'activated'";
			$logsResult = mysql_query( $logs );
			$logcount = mysql_fetch_array( $logsResult );
			
			echo "<b>Total Mission Posts For the Month of " . $today['month'] . "</b>: " . $postcount['0'] . "<br />";
			echo "<b>Total Personal Logs For the Month of " . $today['month'] . "</b>: " . $logcount['0'] . "<br />";
			echo "<b>Total Posts For the Month of " . $today['month'] . "</b>: " . ( $postcount['0'] + $logcount['0'] );
		
		} elseif( $jpCount == "y" ) {
			/* do a count query on the missionposts table */
			$posts = "SELECT postAuthor FROM sms_posts WHERE postPosted > '$monthStart' ";
			$posts.= "AND postPosted < '$monthEnd' AND postStatus = 'activated'";
			$postsResult = mysql_query( $posts );
			$countRows = mysql_num_rows( $postsResult );
			$authorArray = array();
			
			while( $counting = mysql_fetch_array( $postsResult ) ) {
				extract( $counting, EXTR_OVERWRITE );
				
				/* explode the array from the query */
				$authorString = explode( ",", $postAuthor );
				
				/* count the number of elements */
				$arrayCount = count( $authorString );
				
				/* loop through the elements in the array and add them */
				/* to the end of the master array */
				for( $i=0; $i<$arrayCount; $i++ ) {
					$authorArray[] = $authorString[$i];
				}
			}
			
			/* count the elements in the array */
			$postcount = count( $authorArray );
			
			/* do a count query on the personallogs table */
			$logs = "SELECT count( logid ) FROM sms_personallogs WHERE logPosted > '$monthStart' ";
			$logs.= "AND logPosted < '$monthEnd' AND logStatus = 'activated'";
			$logsResult = mysql_query( $logs );
			$logcount = mysql_fetch_array( $logsResult );
			
			echo "<b>Total Mission Posts For the Month of " . $today['month'] . "</b>: " . $postcount . "<br />";
			echo "<b>Total Personal Logs For the Month of " . $today['month'] . "</b>: " . $logcount['0'] . "<br />";
			echo "<b>Total Posts For the Month of " . $today['month'] . "</b>: " . ( $postcount + $logcount['0'] );
			
		}
		
		/* do a count query on the missionposts table for saved posts */
		$savedPosts = "SELECT count( postid ) FROM sms_posts WHERE postPosted > '$monthStart' ";
		$savedPosts.= "AND postPosted < '$monthEnd' AND postStatus = 'saved'";
		$savedPostsResult = mysql_query( $savedPosts );
		$savedPostCount = mysql_fetch_array( $savedPostsResult );
		
		/* do a count query on the personallogs table for saved logs */
		$savedLogs = "SELECT count( logid ) FROM sms_personallogs WHERE logPosted > '$monthStart' ";
		$savedLogs.= "AND logPosted < '$monthEnd' AND logStatus = 'saved'";
		$savedLogsResult = mysql_query( $savedLogs );
		$savedLogCount = mysql_fetch_array( $savedLogsResult );
		
		echo "<br /><br />";
		echo "<b>Total Saved Posts &amp; Logs</b>: " . ( $savedPostCount['0'] + $savedLogCount['0'] );
		
		?>
		
		<br /><br /><br />
		
		<span class="fontLarge"><b>Individual Crew Counts</b></span><br /><br />
		<table cellspacing="0" cellpadding="0">
			<tr class="fontMedium">
				<td><b>Crew Member</b></td>
				<td colspan="2" align="center"><b>Mission Posts</b></td>
				<td colspan="2" align="center"><b>Personal Logs</b></td>
				<td colspan="2" align="center"><b>Totals</b></td>
			</tr>
			<tr class="fontSmall">
				<td>&nbsp;</td>
				<td align="center"><i><?=$postCountDefault;?> Days</i></td>
				<td align="center"><i><?=$today['month'];?></i></td>
				<td align="center"><i><?=$postCountDefault;?> Days</i></td>
				<td align="center"><i><?=$today['month'];?></i></td>
				<td align="center"><i><?=$postCountDefault;?> Days</i></td>
				<td align="center"><i><?=$today['month'];?></i></td>
			</tr>
			<?php
			
			/* query the users table for firstname, lastname, and id */
			$users = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.loa, crew.strikes, ";
			$users.= "rank.rankName FROM sms_crew AS crew, sms_ranks AS rank WHERE ";
			$users.= "crew.crewType = 'active' AND crew.rankid = rank.rankid ORDER BY ";
			$users.= "crew.rankid, crew.positionid ASC";
			$usersResult = mysql_query( $users );
			
			$rowCount = "0";
			$color1 = "rowColor1";
			$color2 = "rowColor2";
			
			/* loop through them and create an array to feed the following two queries */
			while( $usercount = mysql_fetch_array( $usersResult ) ) {
				extract( $usercount, EXTR_OVERWRITE );
				
				$rowColor = ($rowCount % 2) ? $color1 : $color2;
			
			/* do a query on the missionposts table again, this time checking the author */
			$posts2 = "SELECT count( postid ) FROM sms_posts WHERE ( postAuthor LIKE '" . $crewid . ",%' OR ";
			$posts2.= "postAuthor LIKE '%," . $crewid . "' OR postAuthor LIKE '%," . $crewid . ",%' OR postAuthor = '" . $crewid . "' ) ";
			$posts2.= "AND postPosted > '$monthStart' AND postPosted < '$monthEnd' AND postStatus = 'activated'";
			$postsSingleResult = mysql_query( $posts2 );
			$postcountSingle = mysql_fetch_array( $postsSingleResult );
			
			/* do a query on the personallogs table again, this time checking the author */
			$logs2 = "SELECT count( logid ) FROM sms_personallogs WHERE logAuthor = '" . $crewid . "' AND logPosted > '$monthStart' ";
			$logs2.= "AND logPosted < '$monthEnd' AND logStatus = 'activated'";
			$logsSingleResult = mysql_query( $logs2 );
			$logcountSingle = mysql_fetch_array( $logsSingleResult );
			
			/* do a query on the missionposts table to find the number of posts in the defined time */
			$postsTimeDefined = "SELECT count( postid ) FROM sms_posts WHERE ( postAuthor LIKE '" . $crewid . ",%' OR ";
			$postsTimeDefined.= "postAuthor LIKE '%," . $crewid . "' OR postAuthor LIKE '%," . $crewid . ",%' OR postAuthor = '" . $crewid . "' ) ";
			$postsTimeDefined.= "AND postPosted > '$daysPrior' AND postPosted < '$todayDate' AND postStatus = 'activated'";
			$postsTimeDefinedResult = mysql_query( $postsTimeDefined );
			$postcountWeeks = mysql_fetch_array( $postsTimeDefinedResult );
			
			/* do a query on the missionposts table to find the number of logs in the defined time */
			$logsTimeDefined = "SELECT count( logid ) FROM sms_personallogs WHERE logAuthor = '" . $crewid . "' ";
			$logsTimeDefined.= "AND logPosted > '$daysPrior' AND logPosted < '$todayDate' AND logStatus = 'activated'";
			$logsTimeDefinedResult = mysql_query( $logsTimeDefined );
			$logcountWeeks = mysql_fetch_array( $logsTimeDefinedResult );
			
			echo "<tr class='" . $rowColor . "'>";
			
			$totalCount = $postcountWeeks[0] + $logcountWeeks[0];
			
			/* if the user is on LOA, display their name and post counts in RED */
			if( $usercount['loa'] == "1" ) {
				$prefix = "<b class='red'>[LOA]</b> ";
				$color = "red";
			} if( $usercount['loa'] == "2" ) {
				$prefix = "<b class='orange'>[ELOA]</b> ";
				$color = "orange";
			} if( $usercount['loa'] == "0" ) {
				if( $totalCount == 0 ) {
					$color = "yellow";
				} elseif( $totalCount >= 1 ) {
					$color = "";
				}
				$prefix = "";
			}
			
			?>
				<td>
					<? printText( $prefix . $rankName . " " . $firstName . " " . $lastName ); ?>
				</td>
				<td align="center" class="<?=$color;?>"><?=$postcountWeeks[0];?></td>
				<td align="center" class="countTableBorder"><?=$postcountSingle['0'];?></td>
				<td align="center" class="<?=$color;?>"><?=$logcountWeeks['0'];?></td>
				<td align="center" class="countTableBorder"><?=$logcountSingle['0'];?></td>
				<td align="center" class="<?=$color;?>"><b><?=$postcountWeeks['0'] + $logcountWeeks['0'];?></b></td>
				<td align="center"><b><?=$postcountSingle['0'] + $logcountSingle['0'];?></b></td>
			</tr>
			<?php $rowCount++; } ?>
		</table>
		<br /><br />
		
		<span class="fontLarge"><b>Total Posts</b></span><br />
		<span class="fontNormal"><i>Includes Previous Players</i></span>
		<br /><br />
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><b>Crew Member</b></td>
				<td align="center"><b>Mission Posts</b></td>
				<td align="center"><b>Personal Logs</b></td>
				<td align="center"><b>Totals</b></td>
			</tr>
		<?
	
			/* query the users table for firstname, lastname, and id */
			$usersTotal = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.crewType, ";
			$usersTotal.= "crew.loa, rank.rankName FROM sms_crew AS crew, sms_ranks AS rank ";
			$usersTotal.= "WHERE crew.rankid = rank.rankid AND ( crew.crewType = 'active' OR crew.crewType = 'inactive' )";
			$usersTotal.= "ORDER BY crew.rankid, crew.positionid ASC";
			$usersTotalResult = mysql_query( $usersTotal );
			
			$rowCountAll = "0";
			$color1 = "rowColor1";
			$color2 = "rowColor2";
			
			/* loop through them and create an array to feed the following two queries */
			while ( $usercountTotal = mysql_fetch_array( $usersTotalResult ) ) {
				extract( $usercountTotal, EXTR_OVERWRITE );
				
				$rowColor = ($rowCountAll % 2) ? $color1 : $color2;
			
			/* do a query on the missionposts table again, this time checking the author */
			$posts3 = "SELECT count( postid ) FROM sms_posts WHERE ( postAuthor LIKE '" . $crewid . ",%' ";
			$posts3.= "OR postAuthor LIKE '%," . $crewid . "' OR postAuthor LIKE '%," . $crewid . ",%' OR postAuthor = '" . $crewid . "' ) AND postStatus = 'activated'";
			$posts3Result = mysql_query( $posts3 );
			$postcountTotal = mysql_fetch_array( $posts3Result );
			
			/* do a query on the missionposts table again, this time checking the author */
			$logs3 = "SELECT count( logid ) FROM sms_personallogs WHERE logAuthor = '" . $crewid . "' AND logStatus = 'activated'";
			$logs3Result = mysql_query( $logs3 );
			$logcountTotal = mysql_fetch_array( $logs3Result );
	
			if( $usercountTotal['crewType'] == "inactive" ) {
				echo "<tr class='green " . $rowColor . "'>";
			} if( $usercountTotal['crewType'] == "active" ) {
				echo "<tr class='" . $rowColor . "'>";
				if( $usercountTotal['loa'] == "1" ) {
					$prefix = "<b class='red'>[LOA]</b> ";
					$color = "red";
				} if( $usercountTotal['loa'] == "2" ) {
					$prefix = "<b class='orange'>[ELOA]</b> ";
					$color = "red";
				} if( $usercountTotal['loa'] == "0" ) {
					$prefix = "";
					$color = "";
				}
			}
	
		?>
	
				<td><? printText( $prefix . $rankName . " " . $firstName . " " . $lastName ); ?></td>
				<td align="center" class="countTableBorder"><?=$postcountTotal['0'];?></td>
				<td align="center" class="countTableBorder"><?=$logcountTotal['0'];?></td>
				<td align="center"><?=$postcountTotal['0'] + $logcountTotal['0'];?></td>
			</tr>
		<? $rowCountAll++; } ?>
		</table>
	</div>

<? } else { errorMessage( "post count" ); } ?>