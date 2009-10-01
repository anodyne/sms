<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/post/addnews.php
Purpose: Page to add a news item

System Version: 2.6.0
Last Modified: 2008-04-29 1633 EST
**/

/* access check */
if(in_array("p_addnews", $sessionAccess))
{
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "post";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_POST['action_x']))
	{
		$today = getdate();
		
		$insert = "INSERT INTO sms_news (newsCat, newsAuthor, newsPosted, newsTitle, newsContent, newsStatus, newsPrivate) ";
		$insert.= "VALUES (%d, %d, %d, %s, %s, %s, %s)";
		
		$query = sprintf(
			$insert,
			escape_string($_POST['newsCat']),
			escape_string($_POST['newsAuthor']),
			escape_string($today[0]),
			escape_string($_POST['newsTitle']),
			escape_string($_POST['newsContent']),
			escape_string('activated'),
			escape_string($_POST['newsPrivate'])
		);
	
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		/* if the user wants the email sent out, do it */
		if(isset($_POST['sendEmail']))
		{
			foreach($_POST as $key => $value)
			{
				$$key = $value;
			}
			
			if(!is_numeric($newsAuthor)) {
				$newsAuthor = NULL;
			}
			
			if(!is_numeric($newsCat)) {
				$newsCat = NULL;
			}
			
			/* set the email author */
			$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, rank.rankShortName ";
			$userFetch.= "FROM sms_crew AS crew, sms_ranks AS rank ";
			$userFetch.= "WHERE crew.crewid = $newsAuthor AND crew.rankid = rank.rankid LIMIT 1";
			$userFetchResult = mysql_query( $userFetch );
			
			while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
				extract( $userFetchArray, EXTR_OVERWRITE );
			
				$firstName = str_replace( "'", "", $firstName );
				$lastName = str_replace( "'", "", $lastName );
				
				$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";
		
			}
			
			/* pull the category name */
			$getCategory = "SELECT catName FROM sms_news_categories WHERE catid = $newsCat LIMIT 1";
			$getCategoryResult = mysql_query( $getCategory );
			$category = mysql_fetch_assoc( $getCategoryResult );
		
			/* define the variables */
			$to = getCrewEmails("emailNews");
			$subject = $emailSubject . " " . stripslashes( $category['catName'] ) . " - " . stripslashes( $newsTitle );
			$message = "A News Item Posted By " . printCrewNameEmail( $newsAuthor ) . "\r\n\r\n";
			$message.= stripslashes($newsContent);
				
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
			$check->message("news item", "add");
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Add News Item</span><br /><br />
	
		This page should be used in the event that a member of the crew has accidentally posted incorrectly.  For instance, if a player has replied to one of the emails sent out to the system instead of logging in and posting, you can copy and paste the contents of their email into this form and put the entry into the system. For all other news items, please use the <a href="<?=$webLocation;?>admin.php?page=post&sub=news"> Write News Item</a> page.<br /><br />
	
		<form method="post" action="<?=$webLocation;?>admin.php?page=post&sub=addnews">
		<table>
			<tr>
				<td class="narrowLabel tableCellLabel">Author</td>
				<td>&nbsp;</td>
				<td>
					<select name="newsAuthor">
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
				<td class="narrowLabel tableCellLabel">Category</td>
				<td>&nbsp;</td>
				<td>
					<select name="newsCat">
						<?

						if( in_array( "m_newscat1", $sessionAccess ) ) {
							$userCatAccess = 1;
						} if( in_array( "m_newscat2", $sessionAccess ) ) {
							$userCatAccess = 2;
						} if( in_array( "m_newscat3", $sessionAccess ) ) {
							$userCatAccess = 3;
						} 
						
						$availableCats = "SELECT * FROM sms_news_categories WHERE ";
						$availableCats.= "catUserLevel <= '$userCatAccess' AND catVisible = 'y' ";
						$availableCats.= "ORDER BY catid ASC";
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
				<td class="narrowLabel tableCellLabel">Title</td>
				<td>&nbsp;</td>
				<td><input type="text" class="name"  name="newsTitle" style="font-weight:bold;" length="100" /></td>
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
				<td><textarea name="newsContent" class="desc" rows="15"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>
					<input type="image" src="<?=path_userskin;?>buttons/add.png" name="action" value="Add" class="button" />
				</td>
			</tr>
		</table>
		</form>
	</div>

<? } else { errorMessage( "add news item" ); } ?>