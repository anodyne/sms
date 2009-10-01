<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/decklisting.php
Purpose: Page that moderates the ship deck listing page

System Version: 2.6.0
Last Modified: 2008-04-19 1836 EST
**/

/* access check */
if( in_array( "m_decks", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;

	/* check how many rows exist in the db */
	$checkRows = "SELECT * FROM sms_tour_decks";
	$checkRowsResult = mysql_query( $checkRows );
	$checkRowsCount = mysql_num_rows( $checkRowsResult );
	
	/* pull the number of decks the ship/starbase has from the db */
	$getDecks = "SELECT decks FROM sms_specs WHERE specid = 1 LIMIT 1";
	$getDecksResult = mysql_query( $getDecks );
	$deckCount = mysql_fetch_array( $getDecksResult );
	
	if($checkRowsCount == 0)
	{
		/*
			if there is a # of decks in the specs table, use that number to
			populate the decks table, otherwise, spit back an error
		*/
		if(!empty($deckCount[0]))
		{
			/* loop through that many times and create rows in the db */
			for($i=1; $i<=$deckCount[0]; $i++)
			{
				$insertDecks = "INSERT INTO sms_tour_decks ( deckid, deckContent ) VALUES ( '', '' )";
				$insertDecksResult = mysql_query( $insertDecks );
			}
		
			echo "<strong class='fontLarge'>Deck insert complete.  Please refresh the page.</strong>";
		}
		else
		{
			echo "<span class='fontLarge'><b>Error! Your specifications do not list the number of decks. In order to use the SMS deck listing feature, you must specify a number of decks in the specifications. Please go to the <a href='" . $webLocation . "admin.php?page=manage&sub=specifications'>specifications management page</a> and add the number of decks.</b></span>";
		}
	}
	else
	{
		if(isset($_POST['action_update_x']))
		{
			for($j=1; $j<=$deckCount[0]; $j++)
			{
				$update = "UPDATE sms_tour_decks SET deckContent = %s WHERE deckid = $j LIMIT 1";
				$query = sprintf($update, escape_string($_POST[$j.'_content']));
				$result = mysql_query( $query );
			}

			/* optimize the SQL table */
			optimizeSQLTable( "sms_tour_decks" );
			
			$action = "update";
		}
		elseif(isset($_POST['action_reset_x']))
		{
			$query = "TRUNCATE TABLE sms_tour_decks";
			$result = mysql_query($query);

			/* optimize the SQL table */
			optimizeSQLTable( "sms_tour_decks" );

			$action = "reset";
		}
	
?>
	
	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( "deck listing", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Deck Listing</span><br /><br />
		Use this feature to define the deck listing for your sim's vessel. The number of decks is determined by what is found in your specifications page. If you change your class of ship/starbase and need to reset your deck listing, use the button to the right.  <b class="red">WARNING:</b> you will lose all data in the deck listing by reseting it.<br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=decklisting">
		
			<div align="right">
				<script type="text/javascript">
					document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/reset.png\" name=\"action_reset\" value=\"Reset Deck Listing\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to reset your deck listing?')\" />" );
				</script>
				<noscript>
					<input type="image" src="<?=path_userskin;?>buttons/reset.png" name="action_reset" value="Reset Deck Listing" class="button" />
				</noscript>
			</div>
			<br />
			
			<table>
				<?
		
				for( $d = 1; $d <= $deckCount['0']; $d++ ) {
		
					/* pull the data for the specific deck */
					$getDeckData = "SELECT * FROM sms_tour_decks WHERE deckid = '$d' LIMIT 1";
					$getDeckDataResult = mysql_query( $getDeckData );
					$deck = mysql_fetch_assoc( $getDeckDataResult );
				
				?>
				<tr>
					<td class="tableCellLabel">Deck <?=$deck['deckid'];?></td>
					<td>&nbsp;</td>
					<td>
						<textarea name="<?=$d;?>_content" rows="5" class="wideTextArea"><?=stripslashes( $deck['deckContent'] );?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="5">&nbsp;</td>
				</tr>
				<? } ?>
				<tr>
					<td colspan="3" height="20">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" align="right">
						<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	
	<? } ?>
<? } else { errorMessage( "deck listing management" ); } ?>