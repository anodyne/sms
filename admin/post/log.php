<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/post/log.php
Purpose: Page to post a personal log

System Version: 2.6.1
Last Modified: 2008-08-01 1348 EST
**/

/* access check */
if(in_array("p_log", $sessionAccess))
{
	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "post";
	$query = FALSE;
	$result = FALSE;
	$today = getdate();
	
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	else
	{
		$id = NULL;
	}

	if(isset($_POST['action_post_x']))
	{
		/** check to see if the user is moderated **/
		$getModerated = "SELECT crewid FROM sms_crew WHERE moderateLogs = 'y'";
		$getModeratedResult = mysql_query( $getModerated );
		$modArray = array();
	
		while( $moderated = mysql_fetch_array( $getModeratedResult ) ) {
			extract( $moderated, EXTR_OVERWRITE );
	
			$modArray[] = $moderated[0];
		}
		/** end moderation check **/
		
		if(count($modArray) > 0 && in_array($sessionCrewid, $modArray)) {
			$logStatus = "pending";
		} elseif($sessionCrewid == "") {
			$logStatus = "pending";
		} elseif($sessionCrewid == 0) {
			$logStatus = "pending";
		} elseif($sessionCrewid > 0) {
			$logStatus = "activated";
		}
		
		/* build the queries */
		if(!isset($id))
		{
			$insert = "INSERT INTO sms_personallogs (logAuthor, logTitle, logContent, logPosted, logStatus) VALUES (%d, %s, %s, %d, %s)";
			
			$query = sprintf(
				$insert,
				escape_string($sessionCrewid),
				escape_string($_POST['logTitle']),
				escape_string($_POST['logContent']),
				escape_string($today[0]),
				escape_string($logStatus)
			);
		}
		else
		{
			$update = "UPDATE sms_personallogs SET logTitle = %s, logContent = %s, logStatus = %s, logPosted = %d WHERE logid = $id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string($_POST['logTitle']),
				escape_string($_POST['logContent']),
				escape_string($logStatus),
				escape_string($today[0])
			);
		}
		
		$result = mysql_query($query);
		
		$action = "post";
		
		/* update the player's last post timestamp */
		$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() WHERE crewid = $sessionCrewid LIMIT 1";
		$updateTimestampResult = mysql_query($updateTimestamp);
		
		/*optimize the table */
		optimizeSQLTable( "sms_crew" );
		optimizeSQLTable( "sms_personallogs" );
		
		/** EMAIL THE LOG **/
		
		/* set the email author */
		$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankName, rank.rankShortName ";
		$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$userFetch.= "WHERE crew.crewid = $sessionCrewid AND crew.rankid = rank.rankid LIMIT 1";
		$userFetchResult = mysql_query( $userFetch );
		
		while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
			extract( $userFetchArray, EXTR_OVERWRITE );
		
			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";
			$name = $userFetchArray['rankName'] . " " . $userFetchArray['firstName'] . " " . $userFetchArray['lastName'];
	
		}
		
		foreach($_POST as $k => $v)
		{
			$$k = $v;
		}
		
		/* if the post has an activated status */
		switch($logStatus)
		{
			case 'activated':
				$to = getCrewEmails("emailLogs");
				$subject = $emailSubject . " " . $name . "'s Personal Log - " . stripslashes($logTitle);
				$message = stripslashes($logContent);
				break;
				
			case 'pending':
				$to = printCOEmail();
				$subject = $emailSubject . " " . $name . "'s Personal Log - " . stripslashes($logTitle) . " (Awaiting Approval)";
				$message = stripslashes($logContent) . "\r\n\r\n";
				$message.= "Please log in to approve this log.  " . $webLocation . "login.php?action=login";
				break;
		}
		
		/* send the email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	}
	elseif(isset($_POST['action_save_x']))
	{
		if(!isset($id))
		{
			$insert = "INSERT INTO sms_personallogs (logAuthor, logTitle, logContent, logPosted, logStatus) VALUES (%d, %s, %s, %d, %s)";
			
			$query = sprintf(
				$insert,
				escape_string($sessionCrewid),
				escape_string($_POST['logTitle']),
				escape_string($_POST['logContent']),
				escape_string($today[0]),
				escape_string('saved')
			);
		}
		else
		{
			$update = "UPDATE sms_personallogs SET logTitle = %s, logContent = %s, logStatus = %s, logPosted = %d WHERE logid = $id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string($_POST['logTitle']),
				escape_string($_POST['logContent']),
				escape_string('saved'),
				escape_string($today[0])
			);
		}
		
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "save";
	}
	elseif(isset($_POST['action_delete_x']))
	{
		$query = "DELETE FROM sms_personallogs WHERE logid = $id LIMIT 1";
		$result = mysql_query($query);
	
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "delete";
	}
	
?>
	
	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "personal log", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Post Personal Log</span><br /><br />
	
		<? if(!isset($id)) { ?>
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=log">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="logTitle" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td><textarea name="logContent" class="desc" rows="15"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" value="Save" class="button" />
					&nbsp;&nbsp;
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/post.png\" name=\"action_post\" value=\"Post\" class=\"button\" onClick=\"javascript:return confirm('Are you sure you want to post this personal log?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" class="button" value="Post" />
					</noscript>
				</td>
			</tr>
		</table>
		</form>
		
		<?
		
		}
		elseif(isset($id) && !isset($_POST['action_delete_x']))
		{
			$getLog = "SELECT * FROM sms_personallogs WHERE logid = $id LIMIT 1";
			$getLogResults = mysql_query($getLog);
			
			while($fetchLog = mysql_fetch_array($getLogResults)) {
				extract($fetchLog, EXTR_OVERWRITE);
			}
	
		?>
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=log&id=<?=$id;?>">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="logTitle" style="font-weight:bold;" length="100" value="<?=print_input_text( $logTitle );?>" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td><textarea name="logContent" class="desc" rows="15"><?=stripslashes( $logContent );?></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this saved personal log?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" class="button" value="Save" />
					&nbsp;&nbsp;
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/post.png\" name=\"action_post\" value=\"Post\" class=\"button\" onClick=\"javascript:return confirm('Are you sure you want to post this saved personal log?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" class="button" value="Post" />
					</noscript>
				</td>
			</tr>
		</table>
		</form>
	
		<? } elseif(isset($id) && isset($_POST['action_delete_x'])) { ?>
	
		Please return to the Control Panel to continue.
	
		<? } ?>
		
	</div>

<? } else { errorMessage( "personal log posting" ); } ?>