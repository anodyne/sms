<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/post/addjp.php
Purpose: Page to add a joint post

System Version: 2.6.10
Last Modified: 2009-09-08 0843 EST
**/

/* access check */
if( in_array( "p_addjp", $sessionAccess ) ) {
	
	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "post";
	$result = FALSE;
	$query = FALSE;
	
	if(isset($_GET['id']))
	{
		if(is_numeric($_GET['id'])) {
			$id = $_GET['id'];
		} else {
			errorMessageIllegal( "add JP page" );
			exit();
		}
	}
	
	if(isset($_GET['number']))
	{
		if(is_numeric($_GET['number'])) {
			$number = $_GET['number'];
		} else {
			errorMessageIllegal( "add JP page" );
			exit();
		}
	}
	
	if(isset($_GET['delete']))
	{
		if(is_numeric($_GET['delete'])) {
			$delete = $_GET['delete'];
		} else {
			errorMessageIllegal( "add JP page" );
			exit();
		}
	}
	
	if(isset($_GET['add']))
	{
		if(is_numeric($_GET['add'])) {
			$add = $_GET['add'];
		} else {
			errorMessageIllegal( "add JP page" );
			exit();
		}
	}
	
	if(!isset($number)) {
		$number = 2;
	} elseif( $number > JP_AUTHORS ) {
		$number = JP_AUTHORS;
	}
	
	if(isset($_POST['action_x']))
	{
		$jpnumber = $_POST['jpNumber'];
		
		for ($n=1; $n<=$jpnumber; $n++)
		{
			$authors[] = $_POST['author' . $n];
		}
		
		/* make a string of the authors */
		$postAuthors = implode(',', $authors);
		
		$insert = "INSERT INTO sms_posts (postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, ";
		$insert.= "postStatus, postTag) VALUES (%s, %s, %s, %s, %s, UNIX_TIMESTAMP(), %d, %s, %s)";
		
		$query = sprintf(
			$insert,
			escape_string($postAuthors),
			escape_string($_POST['postTitle']),
			escape_string($_POST['postLocation']),
			escape_string($_POST['postTimeline']),
			escape_string($_POST['postContent']),
			escape_string($_POST['postMission']),
			escape_string('activated'),
			escape_string($_POST['postTag'])
		);
	
		$result = mysql_query($query);
	
		for($i=1; $i<=$number; $i++)
		{
			/* set the author var */
			$author = $_POST['author' . $i];
			
			if(!is_numeric($author)) {
				$author = NULL;
			}
	
			/* update the player's last post timestamp */
			$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() WHERE crewid = $author LIMIT 1";
			$updateTimestampResult = mysql_query( $updateTimestamp );
		}
	
		/* optimize the crew table */
		optimizeSQLTable( "sms_crew" );
		optimizeSQLTable( "sms_posts" );
		
		/* if the user wants to send the email out, do it */
		if(isset($_POST['sendEmail']))
		{
			foreach($_POST as $key => $value)
			{
				$$key = $value;
			}
			
			if(is_numeric($_POST['author1'])) {
				$emailAuthor = $_POST['author1'];
			}
			
			/* set the email author */
			$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankShortName ";
			$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
			$userFetch.= "WHERE crew.crewid = $emailAuthor AND crew.rankid = rank.rankid LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			
			while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
				extract( $userFetchArray, EXTR_OVERWRITE );
			}
			
			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";
			
			/* define the variables */
			$to = getCrewEmails("emailPosts");
			$subject = $emailSubject . " " . printMissionTitle( $postMission ) . " - " . $postTitle;
			$message = "A Post By " . displayEmailAuthors($postAuthors, 'noLink') . "\r\n";
			$message.= "Location: " . stripslashes($postLocation) . "\r\n";
			$message.= "Timeline: " . stripslashes($postTimeline) . "\r\n";
			$message.= "Tag: " . stripslashes($postTag) . "\r\n\r\n";
			$message.= stripslashes($postContent);
				
			/* send the email */
			mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
		}
	}
	
	?>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$('#participants').change(function(){
				var number = $(this).val();
				
				window.location = "<?php echo $webLocation;?>admin.php?page=post&sub=addjp&number=" + number;
			});
		});
	</script>
	
	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "joint post", "add" );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Add Joint Mission Entry</span><br /><br />
	
		This page should be used in the event that a member of the crew has accidentally posted incorrectly.  For instance, if a player has replied to one of the emails sent out to the system instead of logging in and posting, you can copy and paste the contents of their email into this form and put the entry into the system. For all other joint posts, please use the <a href="<?=$webLocation;?>admin.php?page=post&sub=jp"> Write Joint Post</a> page.<br /><br />
	
		<span class="fontNormal">
			<b>Select the number of participants:</b> &nbsp;
			
			<select id="participants">
				<option value="">Please Choose One</option>
				
				<?php for ($k=2; $k<=JP_AUTHORS; $k++): ?>
					<option value="<?php echo $k;?>"><?php echo $k;?> People</option>
				<?php endfor;?>
			</select>
		</span><br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=addjp">
		<table>
			<?
			
			$authorNum = 1;
			
			for( $i=1; $i<=$number; $i++ ) {
			
			?>
			
			<tr>
				<td class="narrowLabel tableCellLabel">
					<b>Author #<?=$i;?></b>
				</td>
				<td>&nbsp;</td>
				<td>
					<select name="author<?=$authorNum;?>">
					<?
					
					/* query the users database */
					$sql = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
					$sql.= "FROM sms_crew AS crew, sms_ranks AS rank ";
					$sql.= "WHERE crew.crewType = 'active' AND crew.rankid = rank.rankid ";
					$sql.= "ORDER BY crew.rankid ASC";
					$result = mysql_query( $sql );
					
					/*
						start looping through what the query returns
						until it runs out of records
					*/
					while( $myrow = mysql_fetch_array( $result ) ) {
						extract( $myrow, EXTR_OVERWRITE );
						
						/* $authorNumber = $author . $authorNum; */
						$authorNumber = $rankName . " " . $firstName . " " . $lastName;
						
						echo "<option value='" . $myrow['crewid'] . "'>" . $authorNumber . "</option>";
						
					}
					?>
					</select>
				</td>
			</tr>
			<? $authorNum = $authorNum + 1; } ?>
			
			<? if(!isset($number)) { ?>
			<input type="hidden" name="jpNumber" value="2" />
			<? } else { ?>
			<input type="hidden" name="jpNumber" value="<?=$number;?>" />
			<? } ?>
			
			<tr>
				<td class="narrowLabel tableCellLabel">Mission</td>
				<td>&nbsp;</td>
				<td class="fontNormal">
					<?
					
					$missionTitle = "SELECT missionid, missionTitle FROM sms_missions WHERE missionStatus = 'current' LIMIT 1";
					$missionTitleResult = mysql_query( $missionTitle );
					$missionCount = mysql_num_rows( $missionTitleResult );
					
					while( $titleArray = mysql_fetch_array( $missionTitleResult ) ) {
						extract( $titleArray, EXTR_OVERWRITE );
					}
					
					if( $missionCount == 0 ) {
						echo "<b>Please create a mission before posting!</b>";
					} else {
					
						$missions = "SELECT missionid, missionTitle, missionStatus FROM sms_missions WHERE ";
						$missions.= "missionStatus != 'upcoming'";
						$missionsResult = mysql_query( $missions );
						
						echo "<select name='postMission'>";
						
						while( $missionArray = mysql_fetch_array( $missionsResult ) ) {
							extract( $missionArray, EXTR_OVERWRITE );
							
							echo "<option value='" . $missionid . "'";
							if( $missionStatus == "current" ) { 
								echo " selected ";
							}
							echo ">";
							printText( $missionTitle );
							echo "</option>";
							
						}
						
						echo "</select>";
					
					}
					
					?>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
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
				<td class="narrowLabel tableCellLabel">Send Email?</td>
				<td>&nbsp;</td>
				<td><input type="checkbox" name="sendEmail" value="y" checked="checked" /></td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td><textarea name="postContent" class="desc" rows="15"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			
			<? if( $missionCount > "0" ) { ?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/add.png" name="action" class="button" value="Add" />
				</td>
			</tr>
			<? } ?>
		</table>
		</form>
	</div>
	
<? } else { errorMessage( "add joint post" ); } ?>