<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/ship.php
Purpose: Main page to display the ship welcome

System Version: 2.6.0
Last Modified: 2007-10-10 1015 EST
**/

/* define the page class */
$pageClass = "ship";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

?>

<div class="body">
	<span class="fontTitle"><? printText( $shipPrefix . " " . $shipName . " " . $shipRegistry ); ?></span>
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
	
	<? printText( $shipMessage ); ?>
</div>