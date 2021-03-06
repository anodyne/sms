<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/starbase.php
Purpose: Main page for the starbase section

System Version: 2.6.0
Last Modified: 2008-03-16 1407 EST
**/

/* define the page class and vars */
$pageClass = "ship";
$co = printCO();

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

?>

<div class="body">
	<span class="fontTitle">Welcome to the Starbase</span>
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
	<br /><br />
	
	<p>
		<b><? printText( $co ); ?><br />
		Commanding Officer, <? printText( $shipPrefix . " " . $shipName ); ?><br />
		<?

		if( $tfMember == "y" ) {
			echo $tfName . ", ";
		}
		
		echo $fleet;

		?></b>
	</p>
</div>