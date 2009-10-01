<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/strikes.php
Purpose: Page to add and remove strikes against players

System Version: 2.6.0
Last Modified: 2008-04-19 1722 EST
**/

/* access check */
if( in_array( "m_strike", $sessionAccess ) ) {

	/* set the page class and variables */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	$today = getdate();
	
	if(isset($_POST['button_x']))
	{
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		if(!is_numeric($crew))
		{
			$crew = NULL;
		}
		
		$strikes = "SELECT strikes FROM sms_crew WHERE crewid = $crew LIMIT 1";
		$strikesResult = mysql_query( $strikes );
		$strikeVar = mysql_fetch_row( $strikesResult );
			
		/* do logic to figure out how to change the number of strikes */
		if($action == "add") {
			$strikesNew = ( $strikeVar['0'] + 1 );
		} elseif($action == "delete") {
			$strikesNew = ( $strikeVar['0'] - 1 );
		}
			
		$insert = "INSERT INTO sms_strikes (crewid, strikeDate, reason, number) VALUES (%d, %d, %s, %d)";
		
		$query = sprintf(
			$insert,
			escape_string($crew),
			escape_string($today[0]),
			escape_string($_POST['reason']),
			escape_string($strikesNew)
		);
		
		$result = mysql_query($query);
			
		/* update the user table to give the player the new number of strikes */
		$update = "UPDATE sms_crew SET strikes = %d WHERE crewid = $crew LIMIT 1";
		$query2 = sprintf($update, escape_string($strikesNew));
		$result2 = mysql_query($query2);
		
		/* optimize table */
		optimizeSQLTable( "sms_crew" );
		optimizeSQLTable( "sms_strikes" );
	}
	
?>
	
	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "strike", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Player Strikes</span><br /><br />
		
		Use this page to add and remove strikes from players. Once you've added or removed a strike,
		you can see the complete <a href="<?=$webLocation;?>admin.php?page=reports&sub=strikes">
		strike list</a>.<br /><br />
			
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=strikes">
		<table cellspacing="1">
			<tr>
				<td class="tableCellLabel">Add or Remove?</td>
				<td>&nbsp;</td>
				<td >
					<input type="radio" id="add" name="action" value="add" checked="yes" /> <label for="add">Add Strike</label>
					<input type="radio" id="remove" name="action" value="delete" /> <label for="remove">Remove Strike</label>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Crew Member</td>
				<td>&nbsp;</td>
				<td>
					<select name="crew">
			
						<?
						
						/* query the users database */
						$getCrew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
						$getCrew.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.crewType = 'active' ";
						$getCrew.= "AND crew.rankid = rank.rankid ORDER BY crew.rankid ASC";
						$getCrewResult = mysql_query( $getCrew );
						
						/* start looping through what the query returns */
						/* until it runs out of records */
						while( $fetchCrew = mysql_fetch_assoc( $getCrewResult ) ) {
							extract( $fetchCrew, EXTR_OVERWRITE );
						
						?>
					
						<option value="<?=$fetchCrew['crewid'];?>"><?=$rankName . " " . $firstName . " " . $lastName;?></option>
					
						<? } ?>
					
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">
					Please give the reason why this player is being given a strike
				</td>
				<td>&nbsp;</td>
				<td><textarea name="reason" rows="10" class="wideTextArea"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="button" value="Update" class="button" />
				</td>
			</tr>
		</table>
		</form>
	</div>
	
<? } else { errorMessage( "strike management" ); } ?>