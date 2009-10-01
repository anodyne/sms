<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/post/mission.php
Purpose: Page to post a mission entry

System Version: 2.6.8
Last Modified: 2008-12-28 2126 EST
**/

/* access check */
if(in_array("p_mission", $sessionAccess))
{	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "post";
	$query = FALSE;
	$result = FALSE;
	$today = getdate();
	
	/* check to make sure the id is legit */
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
		$getModerated = "SELECT crewid FROM sms_crew WHERE moderatePosts = 'y'";
		$getModeratedResult = mysql_query( $getModerated );
		$modArray = array();
	
		while( $moderated = mysql_fetch_array( $getModeratedResult ) ) {
			extract( $moderated, EXTR_OVERWRITE );
	
			$modArray[] = $moderated['0'];
		}
		/** end moderation check **/
		
		if(count($modArray) > 0 && in_array($sessionCrewid, $modArray)) {
			$postStatus = "pending";
		} elseif($sessionCrewid == "" || $sessionCrewid == 0) {
			$postStatus = "pending";
		} elseif($sessionCrewid > 0) {
			$postStatus = "activated";
		} if($_POST['postMission'] == "") {
			$postStatus = "pending";
		}
	
		if(!isset($id))
		{
			$insert = "INSERT INTO sms_posts (postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, ";
			$insert.= "postStatus, postTag) VALUES (%d, %s, %s, %s, %s, %d, %d, %s, %s)";
			
			$query = sprintf(
				$insert,
				escape_string($sessionCrewid),
				escape_string($_POST['postTitle']),
				escape_string($_POST['postLocation']),
				escape_string($_POST['postTimeline']),
				escape_string($_POST['postContent']),
				escape_string($today[0]),
				escape_string($_POST['postMission']),
				escape_string($postStatus),
				escape_string($_POST['postTag'])
			);
		}
		else
		{
			$update = "UPDATE sms_posts SET postTitle = %s, postLocation = %s, postTimeline = %s, postContent = %s, postPosted = %d, ";
			$update.= "postStatus = %s, postTag = %s, postMission = %d WHERE postid = $id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string($_POST['postTitle']),
				escape_string($_POST['postLocation']),
				escape_string($_POST['postTimeline']),
				escape_string($_POST['postContent']),
				escape_string($today[0]),
				escape_string($postStatus),
				escape_string($_POST['postTag']),
				escape_string($_POST['postMission'])
			);
		}
	
		$result = mysql_query($query);
		
		$action = "post";
		
		/* update the player's last post time stamp */
		$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() WHERE crewid = $sessionCrewid LIMIT 1";
		$updateTimestampResult = mysql_query($updateTimestamp);
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		optimizeSQLTable( "sms_posts" );
		
		/** EMAIL THE POST **/
		
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
		
		foreach($_POST as $k => $v)
		{
			$$k = $v;
		}
		
		switch($postStatus)
		{
			case 'activated':
				$to = getCrewEmails( "emailPosts" );
				$subject = $emailSubject . " " . printMissionTitle($postMission) . " - " . $postTitle;
				$message = "A Post By " . displayEmailAuthors($sessionCrewid, 'noLink') . "\r\n";
				$message.= "Location: " . stripslashes($postLocation) . "\r\n";
				$message.= "Timeline: " . stripslashes($postTimeline) . "\r\n";
				$message.= "Tag: " . stripslashes($postTag) . "\r\n\r\n";
				$message.= stripslashes($postContent);
				break;
				
			case 'pending':
				$to = printCOEmail();
				$subject = $emailSubject . " " . printMissionTitle($postMission) . " - " . $postTitle . " (Awaiting Approval)";
				$message = "A Post By " . displayEmailAuthors($sessionCrewid, 'noLink') . "\r\n";
				$message.= "Location: " . stripslashes($postLocation) . "\r\n";
				$message.= "Timeline: " . stripslashes($postTimeline) . "\r\n";
				$message.= "Tag: " . stripslashes($postTag) . "\r\n\r\n";
				$message.= stripslashes($postContent) . "\r\n\r\n";
				$message.= "Please log in to approve this post.  " . $webLocation . "login.php?action=login";
				break;
		}
		
		/* send the email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	}
	elseif(isset($_POST['action_save_x']))
	{
		if(!isset($id))
		{
			$insert = "INSERT INTO sms_posts (postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, ";
			$insert.= "postStatus, postTag) VALUES (%d, %s, %s, %s, %s, %d, %d, %s, %s)";
			
			$query = sprintf(
				$insert,
				escape_string($sessionCrewid),
				escape_string($_POST['postTitle']),
				escape_string($_POST['postLocation']),
				escape_string($_POST['postTimeline']),
				escape_string($_POST['postContent']),
				escape_string($today[0]),
				escape_string($_POST['postMission']),
				escape_string('saved'),
				escape_string($_POST['postTag'])
			);
		}
		else
		{
			$update = "UPDATE sms_posts SET postTitle = %s, postLocation = %s, postTimeline = %s, postContent = %s, postPosted = %d, ";
			$update.= "postStatus = %s, postTag = %s, postMission = %d WHERE postid = $id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string($_POST['postTitle']),
				escape_string($_POST['postLocation']),
				escape_string($_POST['postTimeline']),
				escape_string($_POST['postContent']),
				escape_string($today[0]),
				escape_string('saved'),
				escape_string($_POST['postTag']),
				escape_string($_POST['postMission'])
			);
		}
	
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "save";
	}
	elseif(isset($_POST['action_delete_x']))
	{
		$query = "DELETE FROM sms_posts WHERE postid = $id LIMIT 1";
		$result = mysql_query($query);
	
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "delete";
	}
	
?>
	
	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "mission entry", $action );
			$check->display();
		}
		
		if( $useMissionNotes == "y" && !isset($_POST['action_delete_x']))
		{
		
		?>
			
		<script type="text/javascript">
			$(document).ready(function() {
				$('a#toggle').click(function() {
					$('#notes').slideToggle('slow');
					return false;
				});
			});
		</script>

		<div class="update notify-normal">
			<a href="#" id="toggle" class="fontNormal" style="float:right;margin-right:.5em;">Show/Hide</a>
			
			<span class="fontTitle">Mission Notes</span>
			<div id="notes" style="display:none;">
				<br />
				<?
	
				$getNotes = "SELECT missionid, missionTitle, missionNotes FROM sms_missions ";
				$getNotes.= "WHERE missionStatus = 'current'";
				$getNotesResult = mysql_query($getNotes);
				
				while($notes = mysql_fetch_assoc($getNotesResult)) {
					extract($notes, EXTR_OVERWRITE);
					
					$missions[] = array(
						'id' => $missionid,
						'title' => $missionTitle,
						'notes' => $missionNotes
					);
				}
				
				if (count($missions) == 1)
				{
					printText($missions[0]['notes']);
				}
				else
				{
					foreach ($missions as $row)
					{
						echo "<span class='fontMedium bold'>". $row['title'] ."</span><br />";
						if ($row['notes'] == '')
						{
							echo '<span class="orange">No mission notes</span>';
						}
						else
						{
							printText($row['notes']);
						}
						echo "<br /><br />";
					}
				}
	
				?>
			</div>
		</div><br />
		<? } ?>
	
		<span class="fontTitle">Post Mission Entry</span><br /><br />
	
		<? if(!isset($id)) { ?>
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=mission">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Mission</td>
				<td>&nbsp;</td>
				<td class="fontNormal">
					<?
					
					$missionTitle = "SELECT missionid, missionTitle FROM sms_missions WHERE missionStatus = 'current'";
					$missionTitleResult = mysql_query($missionTitle);
					$missions = FALSE;
					
					while($titleArray = mysql_fetch_array($missionTitleResult)) {
						extract($titleArray, EXTR_OVERWRITE);
						
						$missions[] = array(
							'id' => $missionid,
							'title' => $missionTitle
						);
					}
					
					if (count($missions) == 0 ):
						echo "<b>You must <a href='" . $webLocation . "admin.php?page=manage&sub=missions'>create a mission</a> before posting!</b>";
					elseif (count($missions) > 1):
						echo "<select name='postMission'>";
						foreach ($missions as $k => $v):
							echo "<option value='". $v['id'] ."'>". $v['title'] ."</option>";
						endforeach;
						echo "</select>";
					else:
						echo "<a href='". $webLocation ."index.php?page=mission&id=". $missions[0]['id'] ."'>";
						printText($missions[0]['title']);
						echo "</a>";
						echo "<input type='hidden' name='postMission' value='". $missions[0]['id'] ."' />";
					endif;
					
					?>
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTitle" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Location</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postLocation" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Timeline</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTimeline" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Tag</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTag" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td><textarea name="postContent" class="desc" rows="15"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			
			<? if( count($missions) > 0 ) { ?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" value="Save" class="button" />
					&nbsp;&nbsp;
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/post.png\" name=\"action_post\" value=\"Post\" class=\"button\" onClick=\"javascript:return confirm('Are you sure you want to post this mission entry?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" class="button" value="Post" />
					</noscript>
				</td>
			</tr>
			<? } ?>
		</table>
		</form>
		
		<?
	
		}
		elseif(isset($id) && !isset($_POST['action_delete_x']))
		{
			$getPost = "SELECT * FROM sms_posts WHERE postid = $id LIMIT 1";
			$getPostResults = mysql_query( $getPost );
			
			while( $fetchPost = mysql_fetch_array( $getPostResults ) ) {
				extract( $fetchPost, EXTR_OVERWRITE );
			}
	
		?>
	
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=mission&id=<?=$id;?>">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? displayAuthors( $postid, "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Mission</td>
				<td>&nbsp;</td>
				<td class="fontNormal">
					<?
					
					$missionTitle = "SELECT missionid, missionTitle FROM sms_missions WHERE missionStatus = 'current'";
					$missionTitleResult = mysql_query($missionTitle);
					$missions = FALSE;
					
					while($titleArray = mysql_fetch_array($missionTitleResult)) {
						extract($titleArray, EXTR_OVERWRITE);
						
						$missions[] = array(
							'id' => $missionid,
							'title' => $missionTitle
						);
					}
					
					if (count($missions) == 0 ):
						echo "<b>You must <a href='" . $webLocation . "admin.php?page=manage&sub=missions'>create a mission</a> before posting!</b>";
					elseif (count($missions) > 1):
						echo "<select name='postMission'>";
						foreach ($missions as $k => $v):
							if ($postMission == $v['id']):
								$selected = ' selected';
							else:
								$selected = '';
							endif;
							
							echo "<option value='". $v['id'] ."'". $selected .">". $v['title'] ."</option>";
						endforeach;
						echo "</select>";
					else:
						echo "<a href='". $webLocation ."index.php?page=mission&id=". $postMission ."'>";
						echo printMissionTitle($postMission);
						echo "</a>";
						echo "<input type='hidden' name='postMission' value='". $postMission ."' />";
					endif;
					
					?>
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTitle" style="font-weight:bold;" length="100" value="<?=print_input_text( $postTitle );?>" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Location</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postLocation" style="font-weight:bold;" length="100" value="<?=print_input_text( $postLocation );?>" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Timeline</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTimeline" style="font-weight:bold;" length="100" value="<?=print_input_text( $postTimeline );?>" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Tag</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="postTag" style="font-weight:bold;" length="100" value="<?=print_input_text( $postTag );?>" /></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td><textarea name="postContent" class="desc" rows="15"><?=stripslashes( $postContent );?></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			
			<? if( count($missions) > 0 && $sessionCrewid == $postAuthor && $postStatus == "saved" ) { ?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this saved post?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" value="Save" class="button" />
					&nbsp;&nbsp;
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/post.png\" name=\"action_post\" value=\"Post\" class=\"button\" onClick=\"javascript:return confirm('Are you sure you want to post this saved mission entry?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" class="button" value="Post" />
					</noscript>
				</td>
			</tr>
			<? } ?>
		</table>
		</form>
		
		<? } elseif(isset($id) && isset($_POST['action_delete_x'])) { ?>
	
		Please return to the Control Panel to continue.
	
		<? } ?>
		
	</div>

<? } else { errorMessage( "mission posting" ); } ?>