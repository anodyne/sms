<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/addpost.php
Purpose: Page to add a mission post

System Version: 2.6.0
Last Modified: 2008-04-29 1614 EST
**/

/* access check */
if( in_array( "p_addmission", $sessionAccess ) ) {
	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "post";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_POST['action_x']))
	{
		$today = getdate();
		
		$insert = "INSERT INTO sms_posts (postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, ";
		$insert.= "postStatus, postTag) VALUES (%d, %s, %s, %s, %s, %d, %d, %s, %s)";
		
		$query = sprintf(
			$insert,
			escape_string($_POST['postAuthor']),
			escape_string($_POST['postTitle']),
			escape_string($_POST['postLocation']),
			escape_string($_POST['postTimeline']),
			escape_string($_POST['postContent']),
			escape_string($today[0]),
			escape_string($_POST['postMission']),
			escape_string('activated'),
			escape_string($_POST['postTag'])
		);
	
		$result = mysql_query($query);
		
		/* update the last post for the author */
		if(isset($_POST['postAuthor']) && is_numeric($_POST['postAuthor']))
		{
			$postAuthor = $_POST['postAuthor'];
		}
		else
		{
			$postAuthor = NULL;
		}
		
		$update = "UPDATE sms_crew SET lastPost = %d WHERE crewid = $postAuthor LIMIT 1";
		
		$query2 = sprintf(
			$update,
			escape_string($today[0])
		);
		
		$result2 = mysql_query($query2);
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		optimizeSQLTable( "sms_crew" );
		
		/* if they sendEmail box is checked, send the email */
		if(isset($_POST['sendEmail']))
		{
			foreach($_POST as $key => $value)
			{
				$$key = $value;
			}
			
			if(!is_numeric($postAuthor)) {
				$postAuthor = NULL;
			}
			
			/* set the email author */
			$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankShortName ";
			$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
			$userFetch.= "WHERE crew.crewid = $postAuthor AND crew.rankid = rank.rankid LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			
			while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
				extract( $userFetchArray, EXTR_OVERWRITE );
			}
			
			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";
			
			/* define the variables */
			$to = getCrewEmails("emailPosts");
			$subject = $emailSubject . " " . printMissionTitle($postMission) . " - " . $postTitle;
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
	
	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery($result, $query);
				
		if(!empty($check->query))
		{
			$check->message("mission entry", "add");
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Add Mission Entry</span><br /><br />
	
		This page should be used in the event that a member of the crew has accidentally posted incorrectly.  For instance, if a player has replied to one of the emails sent out to the system instead of logging in and posting, you can copy and paste the contents of their email into this form and put the entry into the system. For all other mission posts, please use the <a href="<?=$webLocation;?>admin.php?page=post&sub=mission"> Write Mission Post</a> page.<br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=addpost">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td>
					<select name="postAuthor">
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
			
			<? if( $missionCount > 0 ) { ?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/add.png" name="action" value="Add" class="button" />
				</td>
			</tr>
			<? } ?>
		</table>
		</form>
	</div>
	
<? } else { errorMessage( "add mission entry" ); } ?>