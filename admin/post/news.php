<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/post/news.php
Purpose: Page to post a news item

System Version: 2.6.1
Last Modified: 2008-08-01 1348 EST
**/

/* access check */
if(in_array("p_news", $sessionAccess))
{
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "post";
	$query = FALSE;
	$result = FALSE;
	$today = getdate();
	
	if(isset($_GET['id']) && is_numeric($_GET['id'])) {
		$id = $_GET['id'];
	} else {
		$id = NULL;
	}
	
	if(isset($_POST['action_post_x']))
	{
		$getModerated = "SELECT crewid FROM sms_crew WHERE moderateNews = 'y'";
		$getModeratedResult = mysql_query( $getModerated );
		$modArray = array();
	
		while( $moderated = mysql_fetch_array( $getModeratedResult ) ) {
			extract( $moderated, EXTR_OVERWRITE );
	
			$modArray[] = $moderated[0];
		}
		/* end moderation check */
	
		if(count($modArray) > 0 && in_array($sessionCrewid, $modArray)) {
			$newsStatus = "pending";
		} elseif($sessionCrewid == "") {
			$newsStatus = "pending";
		} elseif($sessionCrewid == 0) {
			$newsStatus = "pending";
		} elseif($sessionCrewid > 0) {
			$newsStatus = "activated";
		} elseif($newsCat == 0 || $newsCat == "") {
			$newsStatus = "pending";
		}
	
		if(!isset($id))
		{
			$insert = "INSERT INTO sms_news (newsCat, newsAuthor, newsPosted, newsTitle, newsContent, newsStatus, newsPrivate) ";
			$insert.= "VALUES (%d, %d, %d, %s, %s, %s, %s)";
			
			$query = sprintf(
				$insert,
				escape_string($_POST['newsCat']),
				escape_string($sessionCrewid),
				escape_string($today[0]),
				escape_string($_POST['newsTitle']),
				escape_string($_POST['newsContent']),
				escape_string($newsStatus),
				escape_string($_POST['newsPrivate'])
			);
		}
		else
		{
			$update = "UPDATE sms_news SET newsCat = %d, newsPosted = %d, newsTitle = %s, newsContent = %s, newsStatus = %s, ";
			$update.= "newsPrivate = %s WHERE newsid = $id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string($_POST['newsCat']),
				escape_string($today[0]),
				escape_string($_POST['newsTitle']),
				escape_string($_POST['newsContent']),
				escape_string($newsStatus),
				escape_string($_POST['newsPrivate'])
			);
		}
		
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "post";
		
		/** EMAIL THE NEWS **/
		
		/* set the email author */
		$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankShortName ";
		$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$userFetch.= "WHERE crew.crewid = $sessionCrewid AND crew.rankid = rank.rankid LIMIT 1";
		$userFetchResult = mysql_query( $userFetch );
		
		while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
			extract( $userFetchArray, EXTR_OVERWRITE );
		
			$firstName = str_replace( "'", "", $firstName );
			$lastName = str_replace( "'", "", $lastName );
			
			$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";
	
		}
		
		foreach($_POST as $k => $v)
		{
			$$k = $v;
		}
		
		if(!is_numeric($newsCat))
		{
			$newsCat = NULL;
		}
		
		/* pull the category name */
		$getCategory = "SELECT catName FROM sms_news_categories WHERE catid = $newsCat LIMIT 1";
		$getCategoryResult = mysql_query($getCategory);
		$category = mysql_fetch_assoc($getCategoryResult);
		
		switch($newsStatus)
		{
			case 'activated':
				$to = getCrewEmails("emailNews");
				$subject = $emailSubject . " " . stripslashes($category['catName']) . " - " . stripslashes($newsTitle);
				$message = "A News Item Posted By " . printCrewNameEmail($sessionCrewid) . "\r\n\r\n";
				$message.= stripslashes($newsContent);
				break;
				
			case 'pending':
				$to = printCOEmail();
				$subject = $emailSubject . " " . stripslashes($category['catName']) . " - " . stripslashes($newsTitle) . " (Awaiting Approval)";
				$message = "A News Item Posted By " . printCrewNameEmail($sessionCrewid) . "\r\n\r\n";
				$message.= stripslashes($newsContent) . "\r\n\r\n";
				$message.= "Please log in to approve this news item.  " . $webLocation . "login.php?action=login";
				break;
		}
		
		/* send the email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
	}
	elseif(isset($_POST['action_save_x']))
	{
		if(!isset($id))
		{
			$insert = "INSERT INTO sms_news (newsCat, newsAuthor, newsPosted, newsTitle, newsContent, newsStatus, newsPrivate) ";
			$insert.= "VALUES (%d, %d, %d, %s, %s, %s, %s)";
			
			$query = sprintf(
				$insert,
				escape_string($_POST['newsCat']),
				escape_string($sessionCrewid),
				escape_string($today[0]),
				escape_string($_POST['newsTitle']),
				escape_string($_POST['newsContent']),
				escape_string('saved'),
				escape_string($_POST['newsPrivate'])
			);
		}
		else
		{
			$update = "UPDATE sms_news SET newsCat = %d, newsPosted = %d, newsTitle = %s, newsContent = %s, newsStatus = %s, ";
			$update.= "newsPrivate = %s WHERE newsid = $id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string($_POST['newsCat']),
				escape_string($today[0]),
				escape_string($_POST['newsTitle']),
				escape_string($_POST['newsContent']),
				escape_string('saved'),
				escape_string($_POST['newsPrivate'])
			);
		}
		
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "save";
	}
	elseif(isset($_POST['action_delete_x']))
	{
		$query = "DELETE FROM sms_news WHERE newsid = $id LIMIT 1";
		$result = mysql_query($query);
	
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "delete";
	}
	
?>
	
	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "news item", $action );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Post News Item</span><br /><br />
	
		<? if(!isset($id)) { ?>
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=news">
		<table>
			<tr>
				<td class="tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Category</b></td>
				<td>&nbsp;</td>
				<td>
					<select name="newsCat">
						<?
	
						/* do some logic to make sure that the system is pulling the right categories */
						if( in_array( "m_newscat1", $sessionAccess ) ) {
							$catLevel = 1;
						} if( in_array( "m_newscat2", $sessionAccess ) ) {
							$catLevel = 2;
						} if( in_array( "m_newscat3", $sessionAccess ) ) {
							$catLevel = 3;
						}
						
						$availableCats = "SELECT * FROM sms_news_categories WHERE catUserLevel <= '$catLevel' ";
						$availableCats.= "AND catVisible = 'y' ORDER BY catid ASC";
						$availableCatsResult = mysql_query( $availableCats );
						
						while( $available = mysql_fetch_assoc( $availableCatsResult ) ) {
							extract( $available, EXTR_OVERWRITE );
							
							echo "<option value='" . $catid . "'>" . $catName . "</option>";
							
						}
						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Title</b></td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="newsTitle" style="font-weight:bold;" length="100" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Privacy</b></td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="newsPrivateN" name="newsPrivate" value="n" checked="yes" /><label for="newsPrivateN">Public</label>
					<input type="radio" id="newsPrivateY" name="newsPrivate" value="y" /><label for="newsPrivateY">Private</label>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Content</b></td>
				<td>&nbsp;</td>
				<td><textarea name="newsContent" rows="15" class="desc"></textarea></td>
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
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/post.png\" name=\"action_post\" value=\"Post\" class=\"button\" onClick=\"javascript:return confirm('Are you sure you want to post this news item?')\" />" );
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
			$getNews = "SELECT * FROM sms_news WHERE newsid = $id LIMIT 1";
			$getNewsResults = mysql_query( $getNews );
			
			while( $fetchNews = mysql_fetch_array( $getNewsResults ) ) {
				extract( $fetchNews, EXTR_OVERWRITE );
			}
			
		?>
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=news&id=<?=$id;?>">
		<table>
			<tr>
				<td class="tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Category</b></td>
				<td>&nbsp;</td>
				<td>
					<select name="newsCat">
						<?
						
						/* do some logic to make sure that the system is pulling the right categories */
						if( in_array( "m_newscat1", $sessionAccess ) ) {
							$catLevel = 1;
						} if( in_array( "m_newscat2", $sessionAccess ) ) {
							$catLevel = 2;
						} if( in_array( "m_newscat3", $sessionAccess ) ) {
							$catLevel = 3;
						}
						
						$availableCats = "SELECT * FROM sms_news_categories WHERE catUserLevel <= '$catLevel' ";
						$availableCats.= "AND catVisible = 'y' ORDER BY catid ASC";
						$availableCatsResult = mysql_query( $availableCats );
						
						while( $available = mysql_fetch_assoc( $availableCatsResult ) ) {
							extract( $available, EXTR_OVERWRITE );
							
							echo "<option value='" . $catid . "'>" . $catName . "</option>";
							
						}
						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Title</b></td>
				<td>&nbsp;</td>
				<td><input type="text" class="name" name="newsTitle" style="font-weight:bold;" length="100" value="<?=print_input_text( $newsTitle );?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Privacy</b></td>
				<td>&nbsp;</td>
				<td>
					<input type="radio" id="newsPrivateN" name="newsPrivate" value="n" checked="yes" /><label for="newsPrivateN">Public</label>
					<input type="radio" id="newsPrivateY" name="newsPrivate" value="y"<? if( $newsPrivate == 'y' ) { echo " checked='yes'"; } ?> /><label for="newsPrivateY">Private</label>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel"><b>Content</b></td>
				<td>&nbsp;</td>
				<td><textarea name="newsContent" rows="10" class="desc"><?=stripslashes( $newsContent );?></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this saved news item?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/save.png" name="action_save" value="Save" class="button" />
					&nbsp;&nbsp;
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/post.png\" name=\"action_post\" value=\"Post\" class=\"button\" onClick=\"javascript:return confirm('Are you sure you want to post this saved news item?')\" />" );
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

<? } else { errorMessage( "news item posting" ); } ?>