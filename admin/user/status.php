<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/status.php
Purpose: Page to request a change of status to LOA or ELOA

System Version: 2.6.1
Last Modified: 2008-08-04 0646 EST
**/

/* access check */
if( in_array( "u_status", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "user";
	$query = FALSE;
	$result = FALSE;

	if( isset($_POST['action_x']) )
	{
		$update = "UPDATE sms_crew SET loa = '%s' WHERE crewid = $sessionCrewid LIMIT 1";
		
		$query = sprintf(
			$update,
			escape_string($_POST['status'])
		);
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		switch($_POST['status'])
		{
			case 0:
				$newStatus = 'active';
				break;
			case 1:
				$newStatus = 'leave of absence';
				break;
			case 2:
				$newStatus = 'extended leave of absence';
				break;
		}
		
		/** EMAIL THE REQUEST **/
	
		/* set the email author */
		$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankShortName ";
		$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$userFetch.= "WHERE crew.crewid = '$sessionCrewid' AND crew.rankid = rank.rankid LIMIT 1";
		$userFetchResult = mysql_query( $userFetch );
		
		while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
			extract( $userFetchArray, EXTR_OVERWRITE );
		}
		
		$firstName = str_replace( "'", "", $firstName );
		$lastName = str_replace( "'", "", $lastName );
		
		$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";
		
		/* define the email variables */
		$to = printCOEmail() . ", " . printXOEmail();
		$subject = $emailSubject . " Status Change Request";
		$message = $_POST['crewMember'] . " has requested that their status be changed to $newStatus.

Duration: " . stripslashes( $_POST['duration'] ) . "
Reason: " . stripslashes( $_POST['reason'] );
		
		/* send the nomination email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	
	}

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "status", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Request Status Change</span><br /><br />
		
		Use this page to inform the CO and XO of your intention to go on LOA or ELOA. Once you
		submit the form, your status will be changed.<br /><br />
		
		<?
		
		$getInfo = "SELECT loa FROM sms_crew WHERE crewid = '$sessionCrewid' LIMIT 1";
		$getInfoResult = mysql_query( $getInfo );
		$info = mysql_fetch_assoc( $getInfoResult );
		
		?>
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=status">
		<table>
			<tr>
				<td class="tableCellLabel">Crew Member</td>
				<td>&nbsp;</td>
				<td>
					<? printCrewName( $sessionCrewid, "rank", "noLink" ); ?>
					<input type="hidden" name="crewMember" value="<? printCrewName( $sessionCrewid, 'rank', 'noLink' ); ?>" />
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Status</td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" name="status" value="0" <? if( $info['loa'] == "0" ) { echo "checked"; } ?>/> Active<br />
					<input type="radio" name="status" value="1" <? if( $info['loa'] == "1" ) { echo "checked"; } ?>/> Leave of Absence<br />
					<input type="radio" name="status" value="2" <? if( $info['loa'] == "2" ) { echo "checked"; } ?>/> Extended Leave of Absence
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">
					How long do you anticipate being on leave?
				</td>
				<td>&nbsp;</td>
				<td><textarea name="duration" class="desc" rows="3"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td class="tableCellLabel">
					Please give a brief reason why you need to take this leave.
				</td>
				<td>&nbsp;</td>
				<td><textarea name="reason" class="desc" rows="6"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action" value="Update" class="button" />
				</td>
			</tr>
		</table>
		</form>
		
	</div>

<? } else { errorMessage( "status change" ); } ?>