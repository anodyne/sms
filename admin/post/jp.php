<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/post/jp.php
Purpose: Page to post a joint post

System Version: 2.6.10
Last Modified: 2009-09-08 0832 EST
**/

/* access check */
if(in_array("p_jp", $sessionAccess))
{
	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "post";
	$query = FALSE;
	$result = FALSE;
	$today = getdate();
	
	if(isset($_GET['id'])) {
		if(is_numeric($_GET['id'])) {
			$id = $_GET['id'];
		} else {
			errorMessageIllegal( "add JP page" );
			exit();
		}
	}
	
	if(isset($_GET['number'])) {
		if(is_numeric($_GET['number'])) {
			$number = $_GET['number'];
		} else {
			errorMessageIllegal( "add JP page" );
			exit();
		}
	}
	
	if(isset($_GET['delete'])) {
		if(is_numeric($_GET['delete'])) {
			$delete = $_GET['delete'];
		} else {
			errorMessageIllegal( "add JP page" );
			exit();
		}
	}
	
	if(isset($_GET['add'])) {
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
	
	if(isset($_POST['action_post_x']))
	{
		$author_count = $_POST['authorCount'];
		$authors_array = array();
		
		if(isset($id)) /* if the post has been saved and is now being posted */
		{
			for($a=0; $a<$author_count; $a++)
			{
				$authors_array[] = $_POST['postAuthor'.$a];
			}
		}
		else /* if the post is NOT saved (straight posting) */
		{
			$authors_array[] = $sessionCrewid;
			
			for($a=2; $a<=$number; $a++)
			{
				$authors_array[] = $_POST['author'.$a];
			}
		}
		
		/* make the array a string */
		$postAuthors = implode(',', $authors_array);
	
		/** check to see if the user is moderated **/
		$getModerated = "SELECT crewid FROM sms_crew WHERE moderatePosts = 'y'";
		$getModeratedResult = mysql_query( $getModerated );
		$modArray = array();
		$arrayModerate = array();
	
		while($moderated = mysql_fetch_array($getModeratedResult)) {
			extract( $moderated, EXTR_OVERWRITE );
	
			$modArray[] = $moderated[0];
		}
	
		/*
			loop through the authors and search for any of the items
			in the array of moderated users. if any are found, set the last
			key of the array to "y", otherwise, set it to "n"
		*/
		foreach($authors_array as $key => $value)
		{
			if(count($modArray) > 0 && in_array($value, $modArray)) {
				$arrayModerate[] = "y";
			} else {
				$arrayModerate[] = "n";
			}
		}
	
		/*
			if the array coming out of the foreach loop has a single key with
			the value of "y", set the post to pending, otherwise, go through
			with the standard post status checks
		*/
		if(count($modArray) > 0 && in_array("y", $arrayModerate)) {
			$postStatus = "pending";
		} else {
			if(($sessionCrewid == "") || ($sessionCrewid == 0)) {
				$postStatus = "pending";
			} elseif($sessionCrewid > 0) {
				$postStatus = "activated";
			} if($_POST['postMission'] == "") {
				$postStatus = "pending";
			}
		}
		/** end user moderation **/
		
		/* build the queries */
		if(!isset($id))
		{
			$insert = "INSERT INTO sms_posts (postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, ";
			$insert.= "postStatus, postTag) VALUES (%s, %s, %s, %s, %s, %d, %d, %s, %s)";
			
			$query = sprintf(
				$insert,
				escape_string($postAuthors),
				escape_string($_POST['postTitle']),
				escape_string($_POST['postLocation']),
				escape_string($_POST['postTimeline']),
				escape_string($_POST['postContent']),
				escape_string($today[0]),
				escape_string($_POST['postMission']),
				escape_string('activated'),
				escape_string($_POST['postTag'])
			);
		}
		else
		{
			$update = "UPDATE sms_posts SET postAuthor = %s, postTitle = %s, postLocation = %s, postTimeline = %s, postContent = %s, ";
			$update.= "postPosted = %d, postStatus = %s, postTag = %s, postMission = %d WHERE postid = $id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string($postAuthors),
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
		
		/* update the crew timestamps */
		foreach($authors_array as $k => $v)
		{
			$updateTimestamp = "UPDATE sms_crew SET lastPost = UNIX_TIMESTAMP() WHERE crewid = $v LIMIT 1";
			$updateTimestampResult = mysql_query($updateTimestamp);
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		optimizeSQLTable( "sms_crew" );
		
		$action = "post";
		
		/** EMAIL THE POST **/
		/* set the email author */
		$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankShortName ";
		$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$userFetch.= "WHERE crew.crewid = $sessionCrewid AND crew.rankid = rank.rankid LIMIT 1";
		$userFetchResult = mysql_query( $userFetch );
		
		while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
			extract( $userFetchArray, EXTR_OVERWRITE );
		}
		
		$firstName = str_replace( "'", "", $firstName );
		$lastName = str_replace( "'", "", $lastName );
		
		$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";
		
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		/* if the post has an activated status */
		switch($postStatus)
		{
			case 'activated':
				$to = getCrewEmails("emailPosts");
				$subject = $emailSubject . " " . printMissionTitle($postMission) . " - " . $postTitle;
				$message = "A Post By " . displayEmailAuthors($postAuthors, 'noLink') . "\r\n";
				$message.= "Location: " . stripslashes($postLocation) . "\r\n";
				$message.= "Timeline: " . stripslashes($postTimeline) . "\r\n";
				$message.= "Tag: " . stripslashes($postTag) . "\r\n\r\n";
				$message.= stripslashes($postContent);
				break;
				
			case 'pending':
				$to = printCOEmail();
				$subject = $emailSubject . " " . printMissionTitle($postMission) . " - " . $postTitle . " (Awaiting Approval)";
				$message = "A Post By " . displayEmailAuthors($postAuthors, 'noLink') . "\r\n";
				$message.= "Location: " . stripslashes($postLocation) . "\r\n";
				$message.= "Timeline: " . stripslashes($postTimeline) . "\r\n";
				$message.= "Tag: " . stripslashes($postTag) . "\r\n\r\n";
				$message.= stripslashes($postContent) . "\r\n\r\n";
				$message.= "Please log in to approve this post.  " . $webLocation . "login.php?action=login";
				break;
		}
		
		/* send the nomination email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	}
	elseif(isset($_POST['action_save_x']))
	{
		$author_count = $_POST['authorCount'];
		$authors_array = array();
		$authors_emails = array();
		
		if(isset($id)) /* if the post has been saved before and it's being edited */
		{
			for($a=0; $a<$author_count; $a++)
			{
				$authors_array[] = $_POST['postAuthor'.$a];
			}
		}
		else /* if the post is NOT saved already */
		{
			$authors_array[] = $sessionCrewid;
			
			for($a=2; $a<=$number; $a++)
			{
				$authors_array[] = $_POST['author'.$a];
			}
		}
		
		/* make the array a string */
		$postAuthors = implode(',', $authors_array);
		
		/* build the queries */
		if(!isset($id))
		{
			$insert = "INSERT INTO sms_posts (postAuthor, postTitle, postLocation, postTimeline, postContent, postPosted, postMission, ";
			$insert.= "postStatus, postTag, postSave) VALUES (%s, %s, %s, %s, %s, %d, %d, %s, %s, %d)";
			
			$query = sprintf(
				$insert,
				escape_string($postAuthors),
				escape_string($_POST['postTitle']),
				escape_string($_POST['postLocation']),
				escape_string($_POST['postTimeline']),
				escape_string($_POST['postContent']),
				escape_string($today[0]),
				escape_string($_POST['postMission']),
				escape_string('saved'),
				escape_string($_POST['postTag']),
				escape_string($sessionCrewid)
			);
		}
		else
		{
			$update = "UPDATE sms_posts SET postAuthor = %s, postTitle = %s, postLocation = %s, postTimeline = %s, postContent = %s, ";
			$update.= "postPosted = %d, postStatus = %s, postTag = %s, postSave = %d, postMission = %d WHERE postid = $id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string($postAuthors),
				escape_string($_POST['postTitle']),
				escape_string($_POST['postLocation']),
				escape_string($_POST['postTimeline']),
				escape_string($_POST['postContent']),
				escape_string($today[0]),
				escape_string('saved'),
				escape_string($_POST['postTag']),
				escape_string($sessionCrewid),
				escape_string($_POST['postMission'])
			);
		}
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "save";
		
		/*
			start the loop based on whether there are key/value pairs
			and keep doing 'something' until you run out of pairs
		*/
		foreach($authors_array as $key => $value)
		{
			if(is_numeric($value))
			{
				$getSelectEmails = "SELECT email FROM sms_crew WHERE emailPosts = 'y' AND crewid = $value";
				$getSelectEmailsResult = mysql_query($getSelectEmails);
			
				/* Start pulling the array and populate the variables */
				while($authorsEmails = mysql_fetch_array($getSelectEmailsResult)) {
					extract($authorsEmails, EXTR_OVERWRITE);
				
					$authors_emails[] = $authorsEmails[0];
				}
			}
		}
		
		/* put the email array into a string */
		$authors_email_string = implode(',', $authors_emails);
	
		/* set the email author */
		$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankShortName ";
		$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$userFetch.= "WHERE crew.crewid = $sessionCrewid AND crew.rankid = rank.rankid LIMIT 1";
		$userFetchResult = mysql_query($userFetch);
		
		while($userFetchArray = mysql_fetch_array($userFetchResult)) {
			extract($userFetchArray, EXTR_OVERWRITE);
		}
		
		$firstName = str_replace( "'", "", $firstName );
		$lastName = str_replace( "'", "", $lastName );
		
		foreach($_POST as $k => $v)
		{
			$$k = $v;
		}
		
		$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";
		
		/* define the variables */
		$to = $authors_email_string;
		$subject = $emailSubject . " " . printMissionTitle($postMission) . " - " . $postTitle . " (Saved Joint Post)";
		$message = "This email is to notify you that your joint post, " . stripslashes($postTitle) . ", has recently been updated.  Please log in to make any changes you want before it is posted.  The content of the new post is below.  This is an automatically generated email.  Please log in to continue working on this post: " . $webLocation . "login.php?action=login\r\n\r\n";
		$message.= "A Post By " . displayEmailAuthors($postAuthors, 'noLink') . "\r\n";
		$message.= "Location: " . stripslashes($postLocation) . "\r\n";
		$message.= "Timeline: " . stripslashes($postTimeline) . "\r\n";
		$message.= "Tag: " . stripslashes($postTag) . "\r\n\r\n";
		$message.= stripslashes($postContent);
	
		/* send the email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	}
	elseif(isset($delete))
	{
		$getAuthors = "SELECT postAuthor FROM sms_posts WHERE postid = $id LIMIT 1";
		$getAuthorsResult = mysql_query($getAuthors);
		
		while($authorAdjust = mysql_fetch_assoc($getAuthorsResult)) {
			extract($authorAdjust, EXTR_OVERWRITE);
		}
		
		/* create the new array */
		$authorArray = explode(",", $postAuthor);
		unset($authorArray[$delete]);
		$authorArray = array_values($authorArray);
		$newAuthors = implode(",", $authorArray);
		
		/* update the post */
		$query = "UPDATE sms_posts SET postAuthor = '$newAuthors' WHERE postid = $id LIMIT 1";
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
	
		$action = "remove";
	}
	elseif(isset($add))
	{
		$getAuthors = "SELECT postAuthor FROM sms_posts WHERE postid = $id LIMIT 1";
		$getAuthorsResult = mysql_query( $getAuthors );
		
		while( $authorAdjust = mysql_fetch_assoc( $getAuthorsResult ) ) {
			extract( $authorAdjust, EXTR_OVERWRITE );
		}
		
		/* create the new array */
		$authorArray = explode(",", $postAuthor);
		$authorArray[] = 0;
		$newAuthors = implode(",", $authorArray);
		
		/* update the post */
		$query = "UPDATE sms_posts SET postAuthor = '$newAuthors' WHERE postid = $id LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
	
		$action = "add";
	}
	elseif(isset($_POST['action_delete_x']))
	{
		$getAuthors = "SELECT postAuthor, postTitle FROM sms_posts WHERE postid = $id LIMIT 1";
		$getAuthorsResult = mysql_query($getAuthors);
		$authorFetch = mysql_fetch_array($getAuthorsResult);
	
		/* delete the JP */
		$query = "DELETE FROM sms_posts WHERE postid = $id LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "delete";
	
		/* send an email out to notify the people there have been changes made */
		/* build the author emails and explode the string at the comma */
		$rawAuthors = explode(",", $authorFetch[0]);
		$authors_array = array();
		
		/*
			start the loop based on whether there are key/value pairs
			and keep doing 'something' until you run out of pairs
		*/
		foreach($rawAuthors as $key => $value)
		{
			$getSelectEmails = "SELECT email FROM sms_crew WHERE crewid = $value";
			$getSelectEmailsResult = mysql_query( $getSelectEmails );
			
			/* Start pulling the array and populate the variables */
			while($authorsEmails = mysql_fetch_array($getSelectEmailsResult)) {
				extract($authorsEmails, EXTR_OVERWRITE);
				
				$authors_array[] = $authorsEmails[0];
			}
		}
		
		/* make the array a string */
		$authors_string = implode( ",", $authors_array );
	
		/* set the email author */
		$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankShortName ";
		$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$userFetch.= "WHERE crew.crewid = $sessionCrewid AND crew.rankid = rank.rankid LIMIT 1";
		$userFetchResult = mysql_query( $userFetch );
		
		while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
			extract( $userFetchArray, EXTR_OVERWRITE );
		}
		
		$firstName = str_replace( "'", "", $firstName );
		$lastName = str_replace( "'", "", $lastName );
		
		$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";
		
		/* define the variables */
		$to = $authors_string;
		$subject = $emailSubject . " Saved Post Deletion Notification";
		$message = "This email is to notify you that your joint post, " . stripslashes($authorFetch[1]) . ", has been deleted by " . displayEmailAuthors($sessionCrewid, 'noLink') . ".";
	
		/* send the email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	}
	
?>
	
	<div class="body">
		<?php
		
		/* set the type */
		if(isset($delete) || isset($add)) {
			$type = "joint post author";
		} else {
			$type = "joint mission post";
		}
		
		$check = new QueryCheck;
		$check->checkQuery($result, $query);
				
		if(!empty($check->query))
		{
			$check->message($type, $action);
			$check->display();
		}
		
		?>
	
		<? if($useMissionNotes == "y") { ?>
		
		<script type="text/javascript">
			$(document).ready(function() {
				$('a#toggle').click(function() {
					$('#notes').slideToggle('slow');
					return false;
				});
				
				$('#participants').change(function(){
					var number = $(this).val();
					
					window.location = "<?php echo $webLocation;?>admin.php?page=post&sub=jp&number=" + number;
				});
			});
		</script>
		
		<div class="update notify-normal">
			<a href="#" id="toggle" class="fontNormal" style="float:right;margin-right:.5em;">Show/Hide</a>
			<span class="fontTitle">Mission Notes</span>
			<div id="notes" style="display:none;clear:left;">
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
	
		<? if(!isset($id)) { ?>
		<span class="fontTitle">Post <?=$number;?>-Way Joint Mission Entry</span><br /><br />
		<span class="fontNormal">
			<b>Select the number of participants:</b> &nbsp;
			
			<select id="participants">
				<option value="">Please Choose One</option>
				
				<?php for ($k=2; $k<=JP_AUTHORS; $k++): ?>
					<option value="<?php echo $k;?>"><?php echo $k;?> People</option>
				<?php endfor;?>
			</select>
		</span><br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=jp&number=<?=$number;?>">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author #1</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<?
			
			$authorNum = 2;
			
			for( $i=1; $i<$number; $i++ ) {
			
			?>
			
			<tr>
				<td class="narrowLabel tableCellLabel">
					<b>Author #<?=$authorNum;?></b>
					<input type="hidden" name="authorCount" value="<?=$number;?>" />
				</td>
				<td>&nbsp;</td>
				<td>
					<select name="author<?=$authorNum;?>">
					<?
					
					/* query the users database */
					$sql = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
					$sql.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.crewType = 'active' ";
					$sql.= "AND crew.rankid = rank.rankid AND crew.crewid != '$sessionCrewid' ";
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
			<tr>
				<td colspan="3" height="10"></td>
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
				<td colspan="3" height="5"></td>
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
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" class="button" value="Save" />
					&nbsp;&nbsp;
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/post.png\" name=\"action_post\" value=\"Post\" class=\"button\" onClick=\"javascript:return confirm('Are you sure you want to post this joint mission entry?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" class="button" value="Post" />
					</noscript>
				</td>
			</tr>
			<? } ?>
		</table>
		</form>
	
		<? } elseif(isset($id) && !isset($_POST['action_delete_x'])) { ?>
		<span class="fontTitle">Edit Saved Joint Post</span><br /><br />
		<table cellpadding="2" cellspacing="2">
		<?
		
		$posts = "SELECT * FROM sms_posts WHERE postid = '$id' LIMIT 1";
		$postsResult = mysql_query( $posts );
		
		while( $postFetch = mysql_fetch_assoc( $postsResult ) ) {
			extract( $postFetch, EXTR_OVERWRITE );
		
		?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=jp&id=<?=$id;?>">
			<tr>
				<td class="narrowLabel tableCellLabel">Authors</td>
				<td>&nbsp;</td>
				<td>
					<? $authorCount = print_active_crew_select_menu( "post", $postAuthor, $postid, "post", "jp" ); ?>
					<input type="hidden" name="authorCount" value="<?=$authorCount;?>" />
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10">&nbsp;</td>
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
				<td>
					<input type="text" class="name" style="font-weight:bold;" maxlength="100" name="postTitle" value="<?=print_input_text( $postTitle );?>" />
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Location</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="name" style="font-weight:bold;" maxlength="100" name="postLocation" value="<?=print_input_text( $postLocation );?>" />
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Timeline</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="name" style="font-weight:bold;" maxlength="100" name="postTimeline" value="<?=print_input_text( $postTimeline );?>" />
				</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Tag</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="name" style="font-weight:bold;" maxlength="100" name="postTag" value="<?=print_input_text( $postTag );?>" />
				</td>
			</tr>
			<tr>
				<td colspan="3" height="5">&nbsp;</td>
			</tr>
			<tr>
				<td class="narrowLabel tableCellLabel">Content</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="postContent" class="desc" rows="15"><?=stripslashes( $postContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="hidden" name="postid" value="<?=$postid;?>" />
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this saved joint post?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" class="button" value="Save" />
					&nbsp;&nbsp;
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/post.png\" name=\"action_post\" value=\"Post\" class=\"button\" onClick=\"javascript:return confirm('Are you sure you want to post this saved joint mission entry?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/post.png" name="action_post" class="button" value="Post" />
					</noscript>
				</td>
			</tr>
			</form>
		<? } ?>
		</table>
		<? } elseif(isset($id) && isset($_POST['action_delete_x'])) { ?>
	
		Please return to the Control Panel to continue.
	
		<? } ?>
		
	</div>
	
<? } else { errorMessage( "mission posting" ); } ?>