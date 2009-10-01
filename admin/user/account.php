<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/account.php
Purpose: Page with the account settings for a user

System Version: 2.6.1
Last Modified: 2008-08-16 1739 EST
**/

/* set the page class */
$pageClass = "admin";
$subMenuClass = "user";
$result = FALSE;
$updateAcct = FALSE;

/* set the POST action */
if( isset( $_POST['action_x'] ) )
{
	$action = $_POST['action_x'];
}

/* make sure the CREW variable is a number */
if( isset( $_GET['crew'] ) && is_numeric( $_GET['crew'] ) ) {
	$crew = $_GET['crew'];
} else {
	errorMessageIllegal( "crew account page" );
	exit();
}

/* access check */
if(
	( $sessionCrewid == $crew && in_array( "u_account1", $sessionAccess ) ) ||
	( in_array( "u_account2", $sessionAccess ) )
) {
	
	if( isset( $action ) ) {
		
		/* pull the current password hash */
		$getPassword = "SELECT password, username, realName, email FROM sms_crew WHERE crewid = '$_GET[crew]' LIMIT 1";
		$getPasswordResult = mysql_query( $getPassword );
		$fetchPassword = mysql_fetch_array( $getPasswordResult );
		
		if( isset( $_POST['currentPassword'] ) ) {
			
			if( $_POST['currentPassword'] == "" ) {
				/*
				if the current password is empty, check to make sure they're
				not trying to update username, real name, or email, otherwise
				run the update query
				*/
				if( $_POST['username'] != $fetchPassword[1] ) {
					$updateAcct = "foo username";
					$result = "";
				} elseif( $_POST['realName'] != $fetchPassword[2] ) {
					$updateAcct = "foo real name";
					$result = "";
				} elseif( $_POST['email'] != $fetchPassword[3] ) {
					$updateAcct = "foo email";
					$result = "";
				} else {
					$update = "UPDATE sms_crew SET contactInfo = %s, emailPosts = %s, emailLogs = %s, emailNews = %s, aim = %s, ";
					$update.= "msn = %s, yim = %s, icq = %s, loa = '%s', crewType = %s, moderatePosts = %s, moderateLogs = %s, ";
					$update.= "moderateNews = %s WHERE crewid = $crew LIMIT 1";
					
					$updateAcct = sprintf(
						$update,
						escape_string( $_POST['contactInfo'] ),
						escape_string( $_POST['emailPosts'] ),
						escape_string( $_POST['emailLogs'] ),
						escape_string( $_POST['emailNews'] ),
						escape_string( $_POST['aim'] ),
						escape_string( $_POST['msn'] ),
						escape_string( $_POST['yim'] ),
						escape_string( $_POST['icq'] ),
						escape_string( $_POST['loa'] ),
						escape_string( $_POST['crewType'] ),
						escape_string( $_POST['moderatePosts'] ),
						escape_string( $_POST['moderateLogs'] ),
						escape_string( $_POST['moderateNews'] )
					);
					
					$result = mysql_query( $updateAcct );
				}
			} elseif( $_POST['currentPassword'] > "" ) {
			
				if( md5( $_POST['currentPassword'] ) == $fetchPassword[0] ) {
					/*
					if what the user provided matches what's in the database, then
					either change their password (if they want that) or update their
					personal information
					*/
					if( isset( $_POST['changePass'] ) ) {
						$update = "UPDATE sms_crew SET username = %s, password = %s, contactInfo = %s, realName = %s, email = %s, ";
						$update.= "emailPosts = %s, emailLogs = %s, emailNews = %s, aim = %s, msn = %s, yim = %s, icq = %s, loa = '%s', ";
						$update.= "crewType = %s, moderatePosts = %s, moderateLogs = %s, moderateNews = %s WHERE crewid = $crew LIMIT 1";

						$updateAcct = sprintf(
							$update,
							escape_string( $_POST['username'] ),
							escape_string( md5( $_POST['newPassword'] ) ),
							escape_string( $_POST['contactInfo'] ),
							escape_string( $_POST['realName'] ),
							escape_string( $_POST['email'] ),
							escape_string( $_POST['emailPosts'] ),
							escape_string( $_POST['emailLogs'] ),
							escape_string( $_POST['emailNews'] ),
							escape_string( $_POST['aim'] ),
							escape_string( $_POST['msn'] ),
							escape_string( $_POST['yim'] ),
							escape_string( $_POST['icq'] ),
							escape_string( $_POST['loa'] ),
							escape_string( $_POST['crewType'] ),
							escape_string( $_POST['moderatePosts'] ),
							escape_string( $_POST['moderateLogs'] ),
							escape_string( $_POST['moderateNews'] )
						);
							
						if( $_POST['newPassword'] == $_POST['passwordConfirm'] ) {
							$result = mysql_query( $updateAcct );
						} else {
							$result = "";
						}
					} elseif( !isset( $_POST['changePass'] ) ) {
						$update = "UPDATE sms_crew SET username = %s, contactInfo = %s, realName = %s, email = %s, emailPosts = %s, ";
						$update.= "emailLogs = %s, emailNews = %s, aim = %s, msn = %s, yim = %s, icq = %s, loa = '%s', crewType = %s, ";
						$update.= "moderatePosts = %s, moderateLogs = %s, moderateNews = %s WHERE crewid = $crew LIMIT 1";

						$updateAcct = sprintf(
							$update,
							escape_string( $_POST['username'] ),
							escape_string( $_POST['contactInfo'] ),
							escape_string( $_POST['realName'] ),
							escape_string( $_POST['email'] ),
							escape_string( $_POST['emailPosts'] ),
							escape_string( $_POST['emailLogs'] ),
							escape_string( $_POST['emailNews'] ),
							escape_string( $_POST['aim'] ),
							escape_string( $_POST['msn'] ),
							escape_string( $_POST['yim'] ),
							escape_string( $_POST['icq'] ),
							escape_string( $_POST['loa'] ),
							escape_string( $_POST['crewType'] ),
							escape_string( $_POST['moderatePosts'] ),
							escape_string( $_POST['moderateLogs'] ),
							escape_string( $_POST['moderateNews'] )
						);
						
						$result = mysql_query( $updateAcct );
					}
				} else {
					/* if what the user provided doesn't match what's in the database, then error out */
					$updateAcct = "Bad password!";
					$result = "";
				}
				
			}
			
		} else {
			/* if someone is trying to update another person's account, then do it */
			$update = "UPDATE sms_crew SET contactInfo = %s, emailPosts = %s, emailLogs = %s, emailNews = %s, aim = %s, msn = %s, ";
			$update.= "yim = %s, icq = %s, loa = '%s', crewType = %s, moderatePosts = %s, moderateLogs = %s, moderateNews = %s ";
			$update.= "WHERE crewid = $crew LIMIT 1";

			$updateAcct = sprintf(
				$update,
				escape_string( $_POST['contactInfo'] ),
				escape_string( $_POST['emailPosts'] ),
				escape_string( $_POST['emailLogs'] ),
				escape_string( $_POST['emailNews'] ),
				escape_string( $_POST['aim'] ),
				escape_string( $_POST['msn'] ),
				escape_string( $_POST['yim'] ),
				escape_string( $_POST['icq'] ),
				escape_string( $_POST['loa'] ),
				escape_string( $_POST['crewType'] ),
				escape_string( $_POST['moderatePosts'] ),
				escape_string( $_POST['moderateLogs'] ),
				escape_string( $_POST['moderateNews'] )
			);
			
			$result = mysql_query( $updateAcct );
		}
		
		/* make sure that positions get adjusted accordingly */
		$type_array = array('active', 'inactive', 'pending', 'npc');
		
		if( in_array($_POST['crewType'], $type_array) && in_array($_POST['oldCrewType'], $type_array))
		{
			/* define the variables */
			$crewType = $_POST['crewType'];
			$oldCrewType = $_POST['oldCrewType'];
			
			if($crewType != $oldCrewType)
			{
				/* get their positions */
				$getPos = "SELECT positionid, positionid2 FROM sms_crew WHERE crewid = $crew LIMIT 1";
				$getPosR = mysql_query($getPos);
				$positions = mysql_fetch_array($getPosR);
				
				if($oldCrewType == 'active')
				{
					update_position( $positions[0], 'take' );
					
					if(count($positions) > 1)
					{
						update_position( $positions[1], 'take' );
					}
				}
				
				if($crewType == 'active')
				{
					update_position( $positions[0], 'give' );
					
					if(count($positions) > 1)
					{
						update_position( $positions[1], 'give' );
					}
				}
				
				/* optimize the table */
				optimizeSQLTable( "sms_positions" );
				
			} /* close the not equal check */
		} /* close the array check */
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
	
	} /* close if action is set */
	
	$accountInfo = "SELECT username, password, loa, realName, email, aim, yim, msn, icq, contactInfo, emailPosts, emailLogs, ";
	$accountInfo.= "emailNews, crewType, moderatePosts, moderateLogs, moderateNews FROM sms_crew WHERE crewid = $crew LIMIT 1";
	$accountInfoResult = mysql_query( $accountInfo );
	
	while( $account = mysql_fetch_assoc( $accountInfoResult ) ) {
		extract( $account, EXTR_OVERWRITE );
	
?>
	
	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $updateAcct );
				
		if( !empty( $check->query ) ) {
			$check->message( "account", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">User Account for <? printCrewName( $crew, "noRank", "noLink" ); ?></span>
		&nbsp;&nbsp;
		<font class="fontNormal">
			[ <a href="<?=$webLocation;?>admin.php?page=user&sub=stats&crew=<?=$crew;?>">User Statistics &raquo;</a> ]
		</font>
		<br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=account&crew=<?=$_GET['crew'];?>">
		
		<? if( $sessionCrewid == $crew ) { ?>
		<div class="update">
			<div class="notify-normal">
			<table>
				<tr>
					<td colspan="3" class="fontLarge"><b>Personal Information</b></td>
				</tr>
				<tr>
					<td colspan="3" class="yellow">
						You must provide your current password if you wish to change your password, alter your 
						e-mail address, alter your username, or alter your real name.
					</td>
				</tr>
				
				<tr>
					<td colspan="3" height="10"></td>
				</tr>
				
				<tr>
					<td class="tableCellLabel">Current Password</td>
					<td>&nbsp;</td>
					<td><input type="password" class="image" name="currentPassword" maxlength="32" /></td>
				</tr>
				
				<tr>
					<td colspan="3" height="15"></td>
				</tr>
				
				<tr>
					<td class="tableCellLabel">Change Password?</td>
					<td>&nbsp;</td>
					<td><input type="checkbox" name="changePass" value="y" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">New Password</td>
					<td>&nbsp;</td>
					<td><input type="password" class="image" name="newPassword" maxlength="32" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Confirm Password</td>
					<td>&nbsp;</td>
					<td><input type="password" class="image" name="passwordConfirm" maxlength="32" /></td>
				</tr>
				
				<tr>
					<td colspan="3" height="15"></td>
				</tr>
				
				<tr>
					<td class="tableCellLabel">User Name</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="image" name="username" maxlength="16" value="<?=print_input_text( $account['username'] );?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Email Address</td>
					<td>&nbsp;</td>
					<td><input type="text" class="image" name="email" maxlength="64" value="<?=$account['email'];?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Real Name</td>
					<td>&nbsp;</td>
					<td><input type="text" class="image" name="realName" maxlength="32" value="<?=print_input_text( $account['realName'] );?>" /></td>
				</tr>
			</table>
			</div>
		</div>
		<br /><br />
		<? } /* close the section intended just if the user is trying to update their account */ ?>
		
		<table>
			
			<? if( in_array( "u_account2", $sessionAccess ) && $sessionCrewid != $crew ) { ?>
			<tr>
				<td class="tableCellLabel">Real Name</td>
				<td>&nbsp;</td>
				<td><? printText( $account['realName'] ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Email Address</td>
				<td>&nbsp;</td>
				<td><?=$account['email'];?></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<? } ?>
			
			<tr>
				<td class="tableCellLabel"><b>Show Contact Information?</b></td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="contactInfoY" name="contactInfo" value="y" <? if( $account['contactInfo'] == "y" ) { echo "checked"; } ?> /> <label for="contactInfoY">Yes</label>
					<input type="radio" id="contactInfoN" name="contactInfo" value="n" <? if( $account['contactInfo'] == "n" ) { echo "checked"; } ?> /> <label for="contactInfoN">No</label>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Get Mission Posts By Email?</b></td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="emailPostsY" name="emailPosts" value="y" <? if( $account['emailPosts'] == "y" ) { echo "checked"; } ?> /> <label for="emailPostsY">Yes</label>
					<input type="radio" id="emailPostsN" name="emailPosts" value="n" <? if( $account['emailPosts'] == "n" ) { echo "checked"; } ?> /> <label for="emailPostsN">No</label>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Get Personal Logs By Email?</b></td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="emailLogsY" name="emailLogs" value="y" <? if( $account['emailLogs'] == "y" ) { echo "checked"; } ?> /> <label for="emailLogsY">Yes</label>
					<input type="radio" id="emailLogsN" name="emailLogs" value="n" <? if( $account['emailLogs'] == "n" ) { echo "checked"; } ?> /> <label for="emailLogsN">No</label>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Get News Items By Email?</b></td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="emailNewsY" name="emailNews" value="y" <? if( $account['emailNews'] == "y" ) { echo "checked"; } ?> /> <label for="emailNewsY">Yes</label>
					<input type="radio" id="emailNewsN" name="emailNews" value="n" <? if( $account['emailNews'] == "n" ) { echo "checked"; } ?> /> <label for="emailNewsN">No</label>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="tableCellLabel" style="color: #f6c731;"><b>AIM</b></td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="aim" maxlength="32" value="<?=print_input_text( $account['aim'] );?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel" style="color: #005ca6;"><b>MSN</b></td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="msn" maxlength="32" value="<?=print_input_text( $account['msn'] );?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel" style="color: #cf181e;"><b>Yahoo!</b></td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="yim" maxlength="32" value="<?=print_input_text( $account['yim'] );?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel" style="color: #18a218;"><b>ICQ</b></td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="icq" maxlength="32" value="<?=print_input_text( $account['icq'] );?>" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="tableCellLabel"><b>Player Status</b></td>
				<td>&nbsp;</td>
				<td valign="middle">
					<? if( in_array( "u_account2", $sessionAccess ) ) { ?>
					
					<select name="loa">
						<option value="0" <? if( $account['loa'] == "0" ) { echo "selected"; } ?>>Active</option>
						<option value="1" <? if( $account['loa'] == "1" ) { echo "selected"; } ?>>Leave of Absence</option>
						<option value="2" <? if( $account['loa'] == "2" ) { echo "selected"; } ?>>Extended Leave of Absence</option>
					</select>
					
					<?
					
					} elseif( in_array( "u_account1", $sessionAccess ) ) {
						echo "<input type='hidden' name='loa' value='" . $account['loa'] . "' />";
						
						if( $account['loa'] == "0" ) {
							echo "Active";
						} elseif( $account['loa'] == "1" ) {
							echo "On Leave of Absence";
						} elseif( $account['loa'] == "2" ) {
							echo "On Extended Leave of Absence";
						} if( $sessionCrewid == $crew ) {
							echo "&nbsp;&nbsp; [ <a href='" . $webLocation . "admin.php?page=user&sub=status'>Request Change of Status</a> ]";
						}
					} 
					
					?>
				</td>
			</tr>
	
			<? if( in_array( "u_account2", $sessionAccess ) ) { ?>
			<tr>
				<td class="tableCellLabel">Character Type</td>
				<td>&nbsp;</td>
				<td>
					<select name="crewType">
						<option value="active" <? if( $crewType == "active" ) { echo "selected"; } ?>>Active Player</option>
						<option value="inactive" <? if( $crewType == "inactive" ) { echo "selected"; } ?>>Inactive Player</option>
						<option value="npc" <? if( $crewType == "npc" ) { echo "selected"; } ?>>Non-Playing Character</option>
						<option value="pending" <? if( $crewType == "pending" ) { echo "selected"; } ?>>Pending Player</option>
					</select>
					<input type="hidden" name="oldCrewType" value="<?=$crewType;?>" />
				</td>
			</tr>
			<? } else { ?>
			<tr>
				<td class="tableCellLabel">Character Type</td>
				<td>&nbsp;</td>
				<td>
					<?
					
					switch( $crewType ) {
						case "active":
							echo "Active Player";
							break;
						case "inactive":
							echo "Inactive Player";
							break;
						case "npc":
							echo "Non-Playing Character";
							break;
						case "pending":
							echo "Pending Player";
							break;
					}
	
					?>
					<input type="hidden" name="crewType" value="<?=$crewType;?>" />
				</td>
			</tr>
			<? } ?>
	
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<? if( in_array( "u_account2", $sessionAccess ) ) { ?>
			<tr>
				<td class="tableCellLabel">Moderate Posts?</td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="modPostsY" name="moderatePosts" value="y"<? if( $moderatePosts == "y" ) { echo " checked"; } ?> /> <label for="modPostsY">Yes</label>
					<input type="radio" id="modPostsN" name="moderatePosts" value="n"<? if( $moderatePosts == "n" ) { echo " checked"; } ?> /> <label for="modPostsN">No</label>
				</td>
			</tr>
			<? } else { ?>
				<input type="hidden" name="moderatePosts" value="<?=$moderatePosts;?>" />
			<? } ?>
	
			<? if( in_array( "u_account2", $sessionAccess ) ) { ?>
			<tr>
				<td class="tableCellLabel">Moderate Logs?</td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="modLogsY" name="moderateLogs" value="y"<? if( $moderateLogs == "y" ) { echo " checked"; } ?> /> <label for="modLogsY">Yes</label>
					<input type="radio" id="modLogsN" name="moderateLogs" value="n"<? if( $moderateLogs == "n" ) { echo " checked"; } ?> /> <label for="modLogsN">No</label>
				</td>
			</tr>
			<? } else { ?>
				<input type="hidden" name="moderateLogs" value="<?=$moderateLogs;?>" />
			<? } ?>
	
			<? if( in_array( "u_account2", $sessionAccess ) ) { ?>
			<tr>
				<td class="tableCellLabel">Moderate News?</td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="modNewsY" name="moderateNews" value="y"<? if( $moderateNews == "y" ) { echo " checked"; } ?> /> <label for="modNewsY">Yes</label>
					<input type="radio" id="modNewsN" name="moderateNews" value="n"<? if( $moderateNews == "n" ) { echo " checked"; } ?> /> <label for="modNewsN">No</label>
				</td>
			</tr>
			<? } else { ?>
				<input type="hidden" name="moderateNews" value="<?=$moderateNews;?>" />
			<? } ?>
			
			<tr>
				<td colspan="3" height="25"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td><input type="image" src="<?=path_userskin;?>buttons/update.png" name="action" class="button" value="Update" /></td>
			</tr>
		</table>
		</form>
		<? } ?>
	</div>
	
<? } else { errorMessage( "user account" ); } ?>