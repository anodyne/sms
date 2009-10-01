<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/error.php
Purpose: Error display page

System Version: 2.6.0
Last Modified: 2007-11-07 1542 EST
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
	<span class="fontTitle">Error!</span><br /><br />
	You have requested a page that does not exist. Please use your browser&rsquo;s
	back button and try again.
</div>