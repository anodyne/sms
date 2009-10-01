<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/reports/milestones.php
Purpose: Page that shows the crew milestones

System Version: 2.5.2
Last Modified: 2007-08-02 1151 EST
**/

/* access check */
if( in_array( "r_milestones", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "reports";
	
	$today = getdate();
	
?>
	
	<div class="body">
		<span class="fontTitle">Crew Milestone Report</span><br /><br />
		
			<?
	
			$rowCount = "0";
			$color1 = "rowColor1";
			$color2 = "rowColor2";
				
			?>
		
		<table>
			<tr>
				<td class="fontLarge"><b>Crew Member</b></td>
				<td align="center" class="fontLarge"><b>Time Since Joining</b></td>
			</tr>
			<?
			
			$sql = "SELECT crew.firstName, crew.lastName, crew.joinDate, crew.lastPost, crew.loa, rank.rankName ";
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
					
					/* subtract today from the join date */
					$difference = $today[0] - $joinDate;
					
					/* find the number of days between now and the join date */
					$diffDay = $difference / 86400;
					
					/** FIND YEARS **/
					$yearsRaw = $diffDay / 365;
					
					if( $yearsRaw > 1 ) {
						if( floor( $yearsRaw ) == 1 ) {
							$case = "year";
						} else {
							$case = "years";
						}
						
						$years = floor( $yearsRaw ) . " " . $case . " ";
					} else {
						$years = "";
					}
					/** END FIND YEARS **/
					
					/* setup some new variables */
					$newDiffDay = floor( $yearsRaw ) * 365;
					$daysNewRaw = floor( $diffDay - $newDiffDay );
					
					/** FIND MONTHS **/
					$monthsRaw = $daysNewRaw / 30.416;
					
					if( $monthsRaw > 1 ) {
						if( floor( $monthsRaw ) == 1 ) {
							$caseMonths = "month";
						} else {
							$caseMonths = "months";
						}
						
						$months = floor( $monthsRaw ) . " " . $caseMonths . " ";
					} else {
						$months = "";
					}
					/** END FIND MONTHS **/
					
					/* setup some new variables */
					$newDiffDay2 = floor( $monthsRaw ) * 30.416;
					$daysNewRaw2 = floor( $daysNewRaw - $newDiffDay2 );
					
					if( floor( $daysNewRaw2 ) == 0 && $months == "" && $years == "" ) {
						$days = "Today";
					} elseif( floor( $daysNewRaw2 ) == 0 && ( !empty( $months ) || !empty( $years ) ) ) {
						$days = "";
					} elseif( floor( $daysNewRaw2 ) == 1 ) {
						$caseDays = "day";
						$days = floor( $daysNewRaw2 ) . " " . $caseDays . " ";
					} else {
						$caseDays = "days";
						$days = floor( $daysNewRaw2 ) . " " . $caseDays . " ";
					}
					
					/*
						if the join date is blank, display a nice message instead
						of the 37 years, etc. since the start of UNIX time
					*/
					if( $joinDate == "" || $joinDate == "-1" ) {
						echo "<span class='yellow'>No Data Available</span>";
					} elseif( $days == "Today" && $months == "" && $years == "" ) {
						echo $years . $months . $days;
					} else {
						echo $years . $months . $days . "ago";
					}
					
					?>
				</td>
			</tr>
			<? $rowCount++; } ?>
		</table>
	</div>
	
<? } else { errorMessage( "crew milestone" ); } ?>