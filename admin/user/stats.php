<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/stats.php
Purpose: Page to display a user's statistics

System Version: 2.6.0
Last Modified: 2008-03-31 1230 EST
**/

/* set the page class */
$pageClass = "admin";
$subMenuClass = "user";

if(isset($_GET['crew']))
{
	if(is_numeric($_GET['crew'])) {
		$crew = $_GET['crew'];
	} else {
		errorMessageIllegal( "user bio page" );
		exit();
	}
}

/* access check */
if( $sessionCrewid == $crew || in_array( "u_stats", $sessionAccess ) ) {

	$getCrewType = "SELECT crewType FROM sms_crew WHERE crewid = $crew LIMIT 1";
	$getCrewTypeResult = mysql_query( $getCrewType );
	$getType = mysql_fetch_assoc( $getCrewTypeResult );
	
	$getCrew = "SELECT * FROM sms_crew WHERE crewid = $crew LIMIT 1";
	$getCrewResult = mysql_query( $getCrew );
	
	while( $fetchCrew = mysql_fetch_array( $getCrewResult ) ) {
		extract( $fetchCrew, EXTR_OVERWRITE );
		
		/* do a query on the missionposts table again, this time checking the author */
		$posts = "SELECT count( postid ) FROM sms_posts WHERE ( postAuthor LIKE '" . $crew . ",%' ";
		$posts.= "OR postAuthor LIKE '%," . $crew . "' OR postAuthor LIKE '%," . $crew . ",%' OR postAuthor = '" . $crew . "' ) AND postStatus = 'activated'";
		$postsResult = mysql_query( $posts );
		$postcountTotal = mysql_fetch_array( $postsResult );
		
		/* do a query on the missionposts table again, this time checking the author */
		$logs = "SELECT count( logid ) FROM sms_personallogs WHERE logAuthor = '" . $crew . "' AND logStatus = 'activated'";
		$logsResult = mysql_query( $logs );
		$logcountTotal = mysql_fetch_array( $logsResult );
		
		/* do a query on the missionposts table again, this time checking the author */
		$news = "SELECT count( newsid ) FROM sms_news WHERE newsAuthor = '" . $crew . "' AND newsStatus = 'activated'";
		$newsResult = mysql_query( $news );
		$newscountTotal = mysql_fetch_array( $newsResult );
		
		$today = getdate();

?>

	<div class="body">
		
		<? if( $getType['crewType'] == "npc" ) { ?>
		
		User stats cannot be retrieved for NPCs!
		
		<? } else { ?>
		
		<span class="fontTitle">User Stats - <? printCrewName( $crew, "rank", "noLink" ); ?></span>
		&nbsp;&nbsp;
		<? if( $fetchCrew['crewType'] == "pending" ) { ?><b class="yellow">[ Activation Pending ]</b><? } ?>
		
		<br /><br />
		
		<b class="fontLarge">Player Information</b><br /><br />
		<table class="narrowTable">
			<tr>
				<td class="tableCellLabel">Joined</td>
				<td></td>
				<td>
					<?
										
					if( empty( $joinDate ) || $joinDate == 0 ) {
						echo "<span class='orange'>No Data Available</span>";
					} else {
						
						$timeFromJoin = ( $today['0'] - $joinDate ) / 86400;
						$joinInDays = round( $timeFromJoin, 0 );
						
						if( $joinInDays >= 2 ) {
							if( $joinInDays > 30 ) {
								$months = round( ( $joinInDays / 30 ), 0 );
								
								if( $months == 1 ) {
									echo "1 Month Ago";
								} else {
									echo $months . " Months Ago";
								}
							} else {
								$joinInDays = round( $joinInDays, 0 );
								echo $joinInDays . " Days Ago";
							}
						} elseif( $joinInDays >= 1 && $joinInDays < 2 ) {
							echo "1 Day Ago";
						} elseif( $joinInDays < 1 ) {
							echo "Today";
						}
					
					?>
					<br />
					<font class="fontSmall"><?=dateFormat( "medium", $joinDate );?></font>
					<? } /* close the else */ ?>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Last Login</td>
				<td></td>
				<td>
					<?
					
					if( empty( $lastLogin ) || $lastLogin == 0 ) {
						echo "<span class='orange'>No Login Recorded</span>";
					} else {
						
						$timeFromLastLogin = ( $today['0'] - $lastLogin ) / 86400;
						$lastLoginInDays = round( $timeFromLastLogin, 0 );
						
						if( $lastLoginInDays >= 2 ) {
							if( $lastLoginInDays > 30 ) {
								$months = round( ( $lastLoginInDays / 30 ), 0 );
								
								if( $months == 1 ) {
									echo "1 Month Ago";
								} else {
									echo $months . " Months Ago";
								}
							} else {
								$lastLoginInDays = round( $lastLoginInDays, 0 );
								echo $lastLoginInDays . " Days Ago";
							}
						} elseif( $lastLoginInDays >= 1 && $lastLoginInDays < 2 ) {
							echo "1 Day Ago";
						} elseif( $lastLoginInDays < 1 ) {
							echo "Today";
						}
					
					?>
					<br />
					<font class="fontSmall"><?=dateFormat( "medium", $lastLogin );?></font>
					<? } /* close the else */ ?>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Last Post</td>
				<td></td>
				<td>
					<?
					
					if( empty( $lastPost ) || $lastPost == 0 ) {
						echo "<span class='orange'>No Posts Recorded</span>";
					} else {
						
						$timeFromLastPost = ( $today['0'] - $lastPost ) / 86400;
						$lastPostInDays = round( $timeFromLastPost, 1 );
						
						if( $lastPostInDays >= 2 ) {
							if( $lastPostInDays > $postCountDefault ) {
								echo "<span class='red'><b>";
							}
							
							if( $lastPostInDays > 30 ) {
								$months = round( ( $lastPostInDays / 30 ), 0 );
								
								if( $months == 1 ) {
									echo "1 Month Ago";
								} else {
									echo $months . " Months Ago";
								}
							} else {
								$lastPostInDays = round( $lastPostInDays, 0 );
								echo $lastPostInDays . " Days Ago";
							}
							
							if( $lastPostInDays > $postCountDefault ) {
								echo "</b></span>";
							}
						} elseif( $lastPostInDays >= 1 && $lastPostInDays < 2 ) {
							echo "1 Day Ago";
						} elseif( $lastPostInDays < 1 ) {
							echo "Today";
						}
					
					?>
					<br />
					<font class="fontSmall"><?=dateFormat( "medium", $lastPost );?></font>
					<? } /* close the else */ ?>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Receiving Posts by Email?</td>
				<td></td>
				<td>
					<?
					
					switch( $emailPosts ) {
						case "y":
							echo "Yes";
							break;
						case "n":
							echo "No";
							break;
					}
					
					?>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Receiving Logs by Email?</td>
				<td></td>
				<td>
					<?
					
					switch( $emailLogs ) {
						case "y":
							echo "Yes";
							break;
						case "n":
							echo "No";
							break;
					}
					
					?>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Receiving News by Email?</td>
				<td></td>
				<td>
					<?
					
					switch( $emailNews ) {
						case "y":
							echo "Yes";
							break;
						case "n":
							echo "No";
							break;
					}
					
					?>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Rank Set Choice</td>
				<td></td>
				<td><img src="<?=$webLocation;?>images/ranks/<?=$displayRank;?>/preview.png" border="0" alt="<?=ucfirst( $displayRank );?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Skin Choice</td>
				<td></td>
				<td><img src="<?=$webLocation;?>skins/<?=$displaySkin;?>/preview.jpg" border="0" alt="<?=ucfirst( $displaySkin );?>" /></td>
			</tr>
		</table>
		<br /><br />
		
		<b class="fontLarge">Milestones</b><br /><br />
		<?php
		
		if(empty($joinDate) || $joinDate == 0) {
			echo "<b class='fontMedium orange'>No Join Date On Record</b>";
		} else {
		
		?>
		<table class="narrowTable">
			<tr>
				<td class="tableCellLabel">3 Months</td>
				<td></td>
				<td>
					<?
					
					$threeRaw = 86400 * 90;
					$three = $joinDate + $threeRaw;
					
					echo dateFormat( "medium2", $three );
					
					?>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">6 Months</td>
				<td></td>
				<td>
					<?
					
					$sixRaw = 86400 * 180;
					$six = $joinDate + $sixRaw;
					
					echo dateFormat( "medium2", $six );
					
					?>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">1 Year</td>
				<td></td>
				<td>
					<?
					
					$year1Raw = 86400 * 365;
					$year1 = $joinDate + $year1Raw;
					
					echo dateFormat( "medium2", $year1 );
					
					?>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">2 Years</td>
				<td></td>
				<td>
					<?
					
					$year2Raw = 86400 * 730;
					$year2 = $joinDate + $year2Raw;
					
					echo dateFormat( "medium2", $year2 );
					
					?>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">5 Years</td>
				<td></td>
				<td>
					<?
					
					$year5Raw = 86400 * 1825;
					$year5 = $joinDate + $year5Raw;
					
					echo dateFormat( "medium2", $year5 );
					
					?>
				</td>
			</tr>
		</table>
		<br /><br />
		
		<?php } ?>
		
		<?
		
		if( $leaveDate != "" ) {
		
			/* subtract the leave date from the join date */
			$leaveDiff = $leaveDate - $joinDate;
			
			/* find the number of days between now and the join date */
			$leaveDiffDay = $leaveDiff / 86400;
			
		} else {
		
			/* subtract today from the join date */
			$leaveDiff = $today['0'] - $joinDate;
			
			/* find the number of days between now and the join date */
			$leaveDiffDay = $leaveDiff / 86400;
			
		}
		
		?>
		
		<b class="fontLarge">Posting Information</b><br /><br />
		<table class="narrowTable">
			<tr>
				<td class="tableCellLabel">Total Posts</td>
				<td></td>
				<td><?=$postcountTotal['0'];?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Avg Posts / Month</td>
				<td></td>
				<td><?=round( ( $postcountTotal['0'] / $leaveDiffDay ) * 30.416, 1 );?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Avg Posts / Week</td>
				<td></td>
				<td><?=round( ( $postcountTotal['0'] / $leaveDiffDay ) * 7, 1 );?></td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Total Logs</td>
				<td></td>
				<td><?=$logcountTotal['0'];?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Avg Logs / Month</td>
				<td></td>
				<td><?=round( ( $logcountTotal['0'] / $leaveDiffDay ) * 30.416, 1 );?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Avg Logs / Week</td>
				<td></td>
				<td><?=round( ( $logcountTotal['0'] / $leaveDiffDay ) * 7, 1 );?></td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Total News Items</td>
				<td></td>
				<td><?=$newscountTotal['0'];?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Avg Posts / Month</td>
				<td></td>
				<td><?=round( ( $newscountTotal['0'] / $leaveDiffDay ) * 30.416, 1 );?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Avg Posts / Week</td>
				<td></td>
				<td><?=round( ( $newscountTotal['0'] / $leaveDiffDay ) * 7, 1 );?></td>
			</tr>
		</table>
		<br /><br />
		
		<b class="fontLarge">Strike Information</b><br /><br />
		<table class="narrowTable">
			<tr>
				<td class="tableCellLabel">Total Active Strikes</td>
				<td></td>
				<td><?=$strikes;?></td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<?
			
			$getStrikes = "SELECT * FROM sms_strikes WHERE crewid = '$crew' ORDER BY strikeid DESC";
			$getStrikesResult = mysql_query( $getStrikes );
			
			while( $strikes = mysql_fetch_assoc( $getStrikesResult ) ) {
				extract( $strikes, EXTR_OVERWRITE );
			
			?>
			<tr>
				<td class="tableCellLabel"><?=dateFormat( "medium", $strikeDate );?></td>
				<td></td>
				<td><? printText( $reason ); ?></td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			<? } ?>
		</table>
			
		<? } } ?>
		
	</div>

<? } else { errorMessage( "this user's stats" ); } ?>