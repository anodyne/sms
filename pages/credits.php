<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: pages/credits.php
Purpose: Page to list the various credits for SMS 2

System Version: 2.5.0
Last Modified: 2007-04-05 2348 EST
**/

/* define the page class */
$pageClass = "main";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

?>

<div class="body">
	<span class="fontTitle">Site Credits</span>
	<?
	
	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if( isset( $sessionCrewid ) && in_array( "m_messages", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=messages' class='image'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
		echo "</a>";
	}
	
	?>
	
	<br /><br />
	
	<?
	
	/*
		do not remove this ... many people have worked on projects that are
		used within SMS ... please respect that
	*/
	printText( $siteCreditsPermanent );
	
	?>

	<br /><br /><hr size="1" /><br />

	<? printText( $siteCredits ); ?>
	
</div> <!-- close the div id content tag -->