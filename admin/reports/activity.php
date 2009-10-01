<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/reports/activity.php
Purpose: Page that shows the crew activity

System Version: 2.6.3
Last Modified: 2007-10-06 2239 EST
**/

/* access check */
if( in_array( "r_activity", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "reports";
	
	$today = getdate();
	
?>
	
	<div class="body">
		<span class="fontTitle">Crew Activity Report</span><br /><br />
		
			<?
	
			$rowCount = "0";
			$color1 = "rowColor1";
			$color2 = "rowColor2";
				
			?>
		
		<table>
			<tr>
				<td class="fontLarge"><b>Crew Member</b></td>
				<td align="center" class="fontLarge"><b>Last Login</b></td>
				<td align="center" class="fontLarge"><b>Last Post</b></td>
			</tr>
			<?
			
			$sql = "SELECT crew.firstName, crew.lastName, crew.lastLogin, crew.lastPost, crew.loa, rank.rankName ";
			$sql.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.rankid = rank.rankid AND crew.crewType = 'active' ";
			$sql.= "ORDER BY crew.rankid, crew.positionid ASC";
			$result = mysql_query($sql);
			
			while( $crew = mysql_fetch_assoc( $result ) ) {
				extract( $crew, EXTR_OVERWRITE );
				
				$rowColor = ( $rowCount % 2 ) ? $color1 : $color2;
			
			?>
			
			<tr class="<?=$rowColor;?>">
				<td>
					<?
					
					if( $loa == 0 ) {
						echo $rankName . " " . $firstName . " " . $lastName;
					} elseif( $loa == 1 ) {
						echo "<b class='red'>[LOA]</b> " . $rankName . " " . $firstName . " " . $lastName;
					} elseif( $loa == 2 ) {
						echo "<b class='orange'>[ELOA]</b> " . $rankName . " " . $firstName . " " . $lastName;
					}
					
					?>
				</td>
				<td align="center">
					<?
					
					if( empty( $lastLogin ) ) {
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
						} elseif( $lastLoginInDays >= 0 && $lastLoginInDays < 2 ) {
							echo "Within 24 Hours";
						}
						
					}
					
					?>
				</td>
				<td align="center">
					<?
					
					if( empty( $lastPost ) ) {
						echo "<span class='yellow'>No Posts Recorded</span>";
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
							
						} elseif( $lastPostInDays >= 0 && $lastPostInDays < 2 ) {
							echo "Within 24 Hours";
						}
						
					}
					
					?>
				</td>
			</tr>
			<? $rowCount++; } ?>
		</table>
	</div>
	
<? } else { errorMessage( "crew activity" ); } ?>