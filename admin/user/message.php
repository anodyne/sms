<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/message.php
Purpose: Page that views a given private message

System Version: 2.6.0
Last Modified: 2008-04-06 2223 EST
**/

/* access check */
if( in_array( "u_inbox", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "user";
	
	if(isset($_GET['id']))
	{
		if(is_numeric($_GET['id'])) {
			$message = $_GET['id'];
		} else {
			errorMessageIllegal( "private message viewing page" );
			exit();
		}
	}
	
	$getMessages = "SELECT * FROM sms_privatemessages WHERE pmid = $message LIMIT 1";
	$getMessagesResult = mysql_query( $getMessages );

	/* loop through the results and fill the form */
	while( $msgFetch = mysql_fetch_assoc( $getMessagesResult ) ) {
		extract( $msgFetch, EXTR_OVERWRITE );
	}

	if( $sessionCrewid == $pmRecipient || $sessionCrewid == $pmAuthor ) {

		if( $pmStatus == "unread" && $sessionCrewid == $pmRecipient ) {
	
			/* if the PM status is unread, change it to read */
			$updateStatus = "UPDATE sms_privatemessages SET pmStatus = 'read' ";
			$updateStatus.= "WHERE pmid = $message LIMIT 1";
			$updateStatusResult = mysql_query( $updateStatus );
	
			/* optimize the table */
			optimizeSQLTable( "sms_privatemessages" );
	
		}
	
?>
	
	<div class="body">
		<span class="fontTitle">Private Message - 
		<?
		
		if( !empty( $pmSubject ) ) {
			printText( $pmSubject );
		} else {
			echo "<i>[ No Subject ]</i>";
		}
		
		?>
		</span><br /><br />
		
		<div class="fontNormal postDetails">
		<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=inbox&tab=3&id=<?=$pmAuthor;?>&reply=<?=$message;?>">
			<div align="center">
				<input type="image" src="<?=$webLocation;?>images/messages-reply.png" class="imageButton" /><br />
				<b>Private Message Details</b><br /><br />	
			</div>
			
			<table>
				<tr>
					<td class="tableCellLabel">Subject</td>
					<td>&nbsp;</td>
					<td>
						<?
						
						if( !empty( $pmSubject ) ) {
							printText( $pmSubject );
						} else {
							echo "<i>[ No Subject ]</i>";
						}
						
						?>
						<input type="hidden" name="replysubject" value="<?=$pmSubject;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">From</td>
					<td>&nbsp;</td>
					<td><? printCrewName( $pmAuthor, "rank", "noLink" ); ?></td>
				</tr>
				<tr>
					<td class="tableCellLabel">To</td>
					<td>&nbsp;</td>
					<td><? printCrewName( $pmRecipient, "rank", "noLink" ); ?></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Sent</td>
					<td>&nbsp;</td>
					<td><?=dateFormat( "medium", $pmDate );?></td>
				</tr>
			</table>
	
			<br />
			<div align="center">
				<a href="<?=$webLocation;?>admin.php?page=user&sub=inbox">
					<b>Back to Private Messages</b>
				</a>
			</div>
		</form>
		</div>
		
		<? printText( $pmContent ); ?>
		
	</div>
	
<? } } else { errorMessage( "private message" ); } ?>