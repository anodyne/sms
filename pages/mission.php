<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/mission.php
Purpose: Page to show the mission information based on an id from the URL

System Version: 2.6.7
Last Modified: 2008-12-11 0940 EST
**/

/* define the page class and vars */
$pageClass = "simm";

/* set the mission id if the id is numeric */
if( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) {
	$mid = $_GET['id'];
}

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* get mission id for individual mission display */
if( isset( $mid ) ) {

	/* pull all info based on mission id */
	$getMission = "SELECT * FROM sms_missions WHERE missionid = '$mid' LIMIT 1";
	$missions = mysql_query( $getMission );

} else {

	/* pull just the current mission given no id */
	$getMission = "SELECT * FROM sms_missions WHERE missionStatus = 'current'";
	$missions = mysql_query( $getMission );

}

/* pull the data and place into variables */
while( $missionInfo = mysql_fetch_array( $missions ) ) {
	extract( $missionInfo, EXTR_OVERWRITE );

	/* calculate the number of posts submitted for a mission */
	$getPosts = "SELECT * FROM sms_posts ";
	$getPosts.= "WHERE postMission = '$missionid' AND postStatus = 'activated'";
	$missionPosts = mysql_query ( $getPosts );
	
	/* count the rows */
	$numPosts = mysql_num_rows( $missionPosts );
				
	if( $numPosts == 0 ) {
		$numPosts = "N/A";
	} else {
		$numPosts = $numPosts;
	}
	
	/* pull only the last 5 posts to display on page */
	$getPosts = "SELECT * FROM sms_posts ";
	$getPosts.= "WHERE postMission = '$missionid' AND postStatus = 'activated' ORDER by postPosted DESC LIMIT 5";
	$missionPosts = mysql_query ( $getPosts );
	
	?>
	
	<div class="body">
	
	<span class="fontTitle"><? printText( $missionTitle ); ?></span><br />
	
	<?
	
	/* pull the image data and display if image is present */
	if ( !empty( $missionImage ) ) {
		echo "<br /><span><img src='" . $webLocation . "images/missionimages/" . $missionImage . "' /></span><br />";
	}
	
	?>
	
	<div style="padding: 1em 0 1em 0;"><? printText( $missionDesc ); ?></div><br />
	<span class="fontMedium"><b>Mission Specifics</b></span>
	
	<table>
	
		<? if( !empty( $missionStart ) ) { ?>
		<tr>
			<td class="tableCellLabel">Start Date</td>
			<td>&nbsp;</td>
			<td><?=dateFormat( "long", $missionStart );?></td>
		</tr>
		<? } ?>
		
		<? if( !empty( $missionEnd ) ) { ?>
		<tr>
			<td class="tableCellLabel">End Date</td>
			<td>&nbsp;</td>
			<td><?=dateFormat( "long", $missionEnd );?></td>
		</tr>
		<? } ?>
		
		<? if( $usePosting == "y" ) { ?>
		<tr>
			<td class="tableCellLabel">Total Posts</td>
			<td>&nbsp;</td>
			<td><?=$numPosts;?> [ <a href="<?=$webLocation;?>index.php?page=postlist&id=<?=$missionid;?>">View Posts</a> ]</td>
		</tr>
		<tr>
			<td class="tableCellLabel" valign="top">Recent Posts</td>
			<td>&nbsp;</td>
			<td>
				<table>
					<tr>
						<td class="fontNormal" align="center"><b>Date</b></td> 
						<td class="fontNormal" align="center"><b>Title</b></td> 
						<td class="fontNormal" align="center"><b>Author(s)</b></td> 
					</tr>
					
					<?
					
					while( $postInfo = mysql_fetch_array( $missionPosts ) ) {
						extract( $postInfo, EXTR_OVERWRITE );
						
						/* define title when no title was entered */
						if ( $postTitle == "" ) {
							$postTitle = "[ Untitled ]";
						}
					 
					?> 
					
					<tr>
						<td class="fontSmall" align="center"><?=dateFormat( "short", $postPosted );?></td> 
						<td class="fontSmall" align="center"><a href="<?=$webLocation;?>index.php?page=post&id=<?=$postid;?>"><?  printText($postTitle);?></a></td> 
						<td class="fontSmall" align="center"><? displayAuthors( $postid, "link" ); ?></a></td> 
					</tr>
					
					<? } ?>
					
				</table> <!--Close the recent posts table-->
			</td>
		</tr>
		<? } ?>
		
	</table> <!--Close the full page table-->
<? } ?>
</div><!--Close the body class div-->