<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/decklisting.php
Purpose: Page to display the deck listing

System Version: 2.6.0
Last Modified: 2008-02-25 1545 EST
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
<script type="text/javascript">
	$(document).ready(function() {
		$('.zebra tr:even').addClass('rowColor1');
	});
</script>

<div class="body">
	<span class="fontTitle">Deck Listing</span>
	<?

	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if( isset( $sessionCrewid ) && in_array( "m_decks", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=decklisting' class='image'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
		echo "</a>";
	}

	?>
	<br /><br />

	<table class="zebra" cellspacing="0" cellpadding="8">
		<?

		/* pull the number of decks the ship/starbase has from the db */
		$getDecks = "SELECT * FROM sms_tour_decks";
		$getDecksResult = mysql_query( $getDecks );

		/* loop through the results and fill the form */
		while( $deckFetch = mysql_fetch_assoc( $getDecksResult ) ) {
			extract( $deckFetch, EXTR_OVERWRITE );
		
		?>
		<tr>
			<td class="tableCellLabel" valign="top">Deck <?=$deckFetch['deckid'];?></td>
			<td>&nbsp;</td>
			<td><? printText( $deckFetch['deckContent'] ); ?></td>
		</tr>
		<? } ?>
	</table>
</div>