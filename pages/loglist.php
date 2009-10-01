<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: Nathan Wharry [ mail@herschwolf.net ]
File: pages/log.php
Purpose: To display all the personal logs for all users

System Version: 2.5.0
Last Modified: 2007-04-23 1200 EST
**/

/* define the page class */
$pageClass = "simm";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* pull all the posts to display on page */
$getlogs = "SELECT * FROM sms_personallogs ";
$getlogs.= "WHERE logStatus = 'activated' ORDER by logPosted DESC LIMIT $logList";
$personallogs = mysql_query ( $getlogs );

?>

<div class="body">
	<span class="fontTitle">Personal Logs for the <? printText( $shipPrefix . " " . $shipName ); ?></span><br />
	Displaying the last <?=$logList;?> personal log entries...<br /><br />
	
	<table cellspacing="0" cellpadding="4">
		<tr class="fontMedium">
			<td align="center" width="35%"><b>Date</b></td> 
			<td align="center" width="35%"><b>Title</b></td> 
			<td align="center" width="30%"><b>Author</b></td> 
		</tr>
		
		<?
		
		$rowCount = "0";
		$color1 = "rowColor1";
		$color2 = "rowColor2";
		
		while( $loginfo = mysql_fetch_array( $personallogs ) ) {
			extract( $loginfo, EXTR_OVERWRITE );
			
			$rowColor = ( $rowCount % 2 ) ? $color1 : $color2;
			
			/* define title when no title was entered */
			if ( $logTitle == "" ) {
				$logTitle = "[ Untitled ]";
			}
		 
		?> 
		
		<tr class="<?=$rowColor;?> fontNormal">
			<td align="center"><?=dateFormat( "medium", $logPosted );?></td> 
			<td align="center"><a href="<?=$webLocation;?>index.php?page=log&id=<?=$logid;?>"><? printText( $logTitle );?></a></td> 
			<td align="center"><? printCrewName( $loginfo['logAuthor'], "rank", "link" ); ?></a></td> 
		</tr>
		
		<? $rowCount++; } ?>
	</table> 

</div>