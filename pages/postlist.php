<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/postlist.php
Purpose: Page to display all the posts of a specific mission

System Version: 2.6.0
Last Modified: 2008-02-25 1613 EST
**/

/* define the page class and vars */
$pageClass = "simm";

if(isset($_GET['id']) && is_numeric($_GET['id']))
{
	$mid = $_GET['id'];
}
else
{
	$mid = "";
}


/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* get mission id for individual mission display */
if( !empty($mid) ) {

/* pull all the posts to display on page */
$getPosts = "SELECT * FROM sms_posts ";
$getPosts.= "WHERE postMission = '$mid' AND postStatus = 'activated' ORDER by postPosted DESC";
$missionPosts = mysql_query ( $getPosts );

/* pull the mission title from sms_missions */
$getMission = "SELECT missionTitle, missionImage ";
$getMission.= "FROM sms_missions ";
$getMission.= "WHERE missionid = '$mid' LIMIT 1";
$missionInfo = mysql_query( $getMission );

while( $missionResult = mysql_fetch_array( $missionInfo ) ) {
	extract( $missionResult, EXTR_OVERWRITE ); 
}

?>

<div class="body">
	<span class="fontTitle"><i><? printText( $missionTitle ); ?></i> Post List</span><br />
	
	<?
	
		/* pull the image data and display if image is present */
		if ( $missionImage == "images/missionimages/" || $missionImage == "" ) {
			echo "";
		} else {
			echo "<br /><span><img src='" . $webLocation . "images/missionimages/" . $missionImage . "' /></span><br />";
		}
	
	?>
	
	<br />
	
	<table cellpadding="4" cellspacing="0">
		<tr class="fontMedium">
			<td width="25%"><b>Date</b></td> 
			<td width="25%" align="center"><b>Title</b></td> 
			<td width="50%" align="center"><b>Author(s)</b></td> 
		</tr>
	
		<?
		
		$rowCount = "0";
		$color1 = "rowColor1";
		$color2 = "rowColor2";
		
		while( $postInfo = mysql_fetch_array( $missionPosts ) ) {
			extract( $postInfo, EXTR_OVERWRITE );
			
			$rowColor = ( $rowCount % 2 ) ? $color1 : $color2;
			
			/* define title when no title was entered */
			if ( $postTitle == "" ) {
				$postTitle = "[ Untitled ]";
			}
		 
		?> 
	
		<tr class="<?=$rowColor;?> fontNormal">
			<td align="center"><?=dateFormat( "medium", $postPosted );?></td> 
			<td align="center"><a href="<?=$webLocation;?>index.php?page=post&id=<?=$postid;?>"><?  printText( $postTitle ); ?></a></td> 
			<td align="center"><? displayAuthors( $postid, "link"); ?></a></td> 
		</tr>
	
		<? $rowCount++; } ?>
	
	</table> 
	
<? } else { ?>

<div class="body">
	
	<span class="fontTitle">Error!</span><br /><br />
	Please specify a mission to view the posts for. If you believe you have received this message in
	error, please contact the system administrator.
	
<? } ?>
	
</div> <!-- close .body -->