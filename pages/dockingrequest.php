<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/dockingrequest.php
Purpose: To display the form for ships to request docking permission at the starbase

System Version: 2.6.9
Last Modified: 2009-03-08 2304 EST
**/

/* check the simm type */
if($simmType == "starbase")
{
	/* define the page class and vars */
	$pageClass = "ship";
	$query = FALSE;
	$result = FALSE;
	
	/* pull in the menu */
	if( isset( $sessionCrewid ) ) {
		include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
		$skinChoice = $sessionDisplaySkin;
	} else {
		include_once( 'skins/' . $skin . '/menu.php' );
		$skinChoice = $skin;
	}
	
	/* determine if request form is being sumbitted */
	if(isset($_POST['action_x']))
	{
		$insert = "INSERT INTO sms_starbase_docking (dockingShipName, dockingShipRegistry, dockingShipClass, dockingShipURL, ";
		$insert.= "dockingShipCO, dockingShipCOEmail, dockingDuration, dockingDesc, dockingStatus) VALUES (%s, %s, %s, %s, %s, ";
		$insert.= "%s, %s, %s, %s)";
		
		$query = sprintf(
			$insert,
			escape_string($_POST['dockingShipName']),
			escape_string($_POST['dockingShipRegistry']),
			escape_string($_POST['dockingShipClass']),
			escape_string($_POST['dockingShipURL']),
			escape_string($_POST['dockingShipCO']),
			escape_string($_POST['dockingShipCOEmail']),
			escape_string($_POST['dockingDuration']),
			escape_string($_POST['dockingDesc']),
			escape_string('pending')
		);
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_starbase_docking" );
		
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		if(!empty($result))
		{
			/* email the ship CO */
			$subject1 = $emailSubject . " Docking Request";
			$to1 = $dockingShipCOEmail;
			$from1 = printCO() . " < " . printCOEmail() . " >";
			$message1 = $dockingShipCO . ", thank you for submitting a request to dock with " . $shipPrefix . " " . $shipName . ".  The CO has been sent a copy of your request and will be reviewing it shortly.  In the meantime, please feel free to browse our site (" . $webLocation . ") until the CO reviews your request.
	
This is an automatically generated message, please do not respond.";
		
			mail($to1, $subject1, $message1, "From: " . $from1 . "\nX-Mailer: PHP/" . phpversion());
			
			/* email the CO */
			$subject2 = $emailSubject . " Docking Request";
			$to2 = printCOEmail();
			$from2 = $dockingShipCO . " < " . $dockingShipCOEmail . " >";
			$message2 = "Greetings " . printCO() . ",
		
$dockingShipCO of the $dockingShipName has sent a request to dock with the $shipName.  To answer the Commanding Officer and approve or deny his request, please log in to your Control Panel.
	
" . $webLocation . "login.php?action=login";
		
			mail($to2, $subject2, $message2, "From: " . $from2 . "\nX-Mailer: PHP/" . phpversion());
		}
	}

?>

	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery($result, $query);
				
		if(!empty($check->query)) {
			$check->message("docking request", "submit");
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Docking Request Form</span><br /><br />
	
		<form method="post" action="<?=$webLocation;?>index.php?page=dockingrequest">
		<table>
			<tr>
				<td colspan="3" class="fontLarge"><b>Ship Information</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="dockingShipName" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Registry</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="dockingShipRegistry" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Class</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="dockingShipClass" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ship Website</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="image" name="dockingShipURL" />
					&nbsp;&nbsp;&nbsp;<span class="fontSmall orange">*Note: Please include the "http://" in your website*</span>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10">&nbsp;</td>
			</tr>
			
			<tr>
				<td colspan="3" class="fontLarge"><b>CO Information</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Commanding Officer</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="dockingShipCO" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Email</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="dockingShipCOEmail" /></td>
			</tr>
			<tr>
				<td colspan="3" height="10">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" class="fontLarge"><b>Docking Information</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Duration</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="dockingDuration" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Docking Purpose</td>
				<td>&nbsp;</td>
				<td><textarea name="dockingDesc" rows="5" class="desc"></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="25">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td><input type="image" src="<?=$webLocation;?>skins/<?=$skinChoice;?>/buttons/submit.png" name="action" class="button" value="Submit" /></td>
			</tr>
		</table>
		</form>
	</div>

<? } else { errorMessage( "ship docking request" ); } ?>