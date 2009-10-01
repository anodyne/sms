<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/messages.php
Purpose: Page that moderates the various messages found throughout SMS

System Version: 2.6.0
Last Modified: 2008-05-31 1325 EST
**/

/* access check */
if( in_array( "m_messages", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;

	/* if the POST action is update */
	if(isset($_POST['action_update_x']))
	{
		$update = "UPDATE sms_messages SET welcomeMessage = %s, shipMessage = %s, simmMessage = %s, shipHistory = %s, ";
		$update.= "cpMessage = %s, joinDisclaimer = %s, samplePostQuestion = %s, rules = %s, acceptMessage = %s, ";
		$update.= "rejectMessage = %s, siteCredits = %s WHERE messageid = 1 LIMIT 1";
		
		$query = sprintf(
			$update,
			escape_string($_POST['welcomeMessage']),
			escape_string($_POST['shipMessage']),
			escape_string($_POST['simmMessage']),
			escape_string($_POST['shipHistory']),
			escape_string($_POST['cpMessage']),
			escape_string($_POST['joinDisclaimer']),
			escape_string($_POST['samplePostQuestion']),
			escape_string($_POST['rules']),
			escape_string($_POST['acceptMessage']),
			escape_string($_POST['rejectMessage']),
			escape_string($_POST['siteCredits'])
		);
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_messages" );
		
		/* this makes sure that once they hit update, the update is immediately seen */
		foreach($_POST as $k => $v)
		{
			$$k = stripslashes($v);
		}
	}
	
	/* strip the slashes from the vars */
	$welcomeMessage = stripslashes( $welcomeMessage );
	$shipMessage = stripslashes( $shipMessage );
	$simmMessage = stripslashes( $simmMessage );
	$shipHistory = stripslashes( $shipHistory );
	$cpMessage = stripslashes( $cpMessage );
	$joinDisclaimer = stripslashes( $joinDisclaimer );
	$samplePostQuestion = stripslashes( $samplePostQuestion );
	$rules = stripslashes( $rules );
	$acceptMessage = stripslashes( $acceptMessage );
	$rejectMessage = stripslashes( $rejectMessage );
	$siteCredits = stripslashes( $siteCredits );

?>

	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "site messages", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Site Messages</span>
			
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=messages">
			<table>
				<tr>
					<td class="tableCellLabel">Welcome Message</td>
					<td>&nbsp;</td>
					<td>
						<textarea name="welcomeMessage" class="desc" rows="10"><?=$welcomeMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Ship Message</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="shipMessage" class="desc" rows="10"><?=$shipMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Simm Message</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="simmMessage" class="desc" rows="10"><?=$simmMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Ship History</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="shipHistory" class="desc" rows="10"><?=$shipHistory;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Control Panel Welcome Message</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="cpMessage" class="desc" rows="10"><?=$cpMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Join Disclaimer</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="joinDisclaimer" class="desc" rows="10"><?=$joinDisclaimer;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel">
						Sample Post Question <? if( $useSamplePost == "n" ) { ?><br />
						<span class="fontSmall"><b class="yellow">You have chosen not to use a sample 
						post. If you would like to turn this feature on, please use the site globals panel to 
						do so.</b></span>
						<? } ?>
					</td>
					<td>&nbsp;</td>
					<td>
						<textarea name="samplePostQuestion" class="desc" rows="10"><?=$samplePostQuestion;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel"><b>Simm Rules</b></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="rules" class="desc" rows="10"><?=$rules;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Site Credits</td>
					<td>&nbsp;</td>
					<td>
						<textarea name="siteCredits" class="desc" rows="10"><?=$siteCredits;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td colspan="3" class="fontLarge"><b>Emails</b></td>
				</tr>
				<tr class="fontNormal">
					<td colspan="3">
						Define a form letter here that will be displayed when you accept or reject a player.
						Before accepting or rejecting the player, you will have the option of changing the 
						letter to be more specific to the situation.<br /><br />
						
						Acceptance and rejection messages now allow keywords to be placed in the messages.
						These keywords will be replaced out of the database before the message is sent. The
						following keywords are available for use in the acceptance and rejection messages:
						<br /><br />
						
						<span class="yellow">#player# - Player's Name<br />
						#ship# - Ship Name<br />
						#position# - Position<br />
						#rank# - Rank</span>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Player Acceptance Email</td>
					<td>&nbsp;</td>
					<td>
						<textarea name="acceptMessage" class="desc" rows="10"><?=$acceptMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Player Rejection Email</td>
					<td>&nbsp;</td>
					<td>
						<textarea name="rejectMessage" class="desc" rows="10"><?=$rejectMessage;?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td><input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" /></td>
				</tr>
			</table>
		</form>
	</div>

<? } else { errorMessage( "site messages management" ); } ?>