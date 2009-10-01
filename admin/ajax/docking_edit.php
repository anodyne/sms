<?php

/* need to connect to the database */
require_once('../../framework/dbconnect.php');

/* pulling a function from new library */
require_once('../../framework/session.name.php');

/* get system unique identifier */
$sysuid = get_system_uid();

/* rewrite master php.ini session.name */
ini_set('session.name', $sysuid);

session_start();

if( !isset( $sessionAccess ) ) {
	$sessionAccess = FALSE;
}

if( !is_array( $sessionAccess ) ) {
	$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
}

if(in_array("m_tour", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_starbase_docking WHERE dockid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );
	
	switch($pendingArray['dockingStatus'])
	{
		case 'activated':
			$tab_action = 1;
			break;
		case 'departed':
			$tab_action = 2;
			break;
		default:
			$tab_action = 1;
	}

?>

	<h2>Edit Docked Ship</h2>
	<p>Use the fields below to edit the docked ship.</p>
	<br />

	<form method="post" action="">
		<table class="hud_guts" cellpadding="3">
			<tr>
				<td class="hudLabel">Name</td>
				<td></td>
				<td><input type="text" class="image" name="dockingShipName" value="<?=$pendingArray['dockingShipName'];?>" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Registry</td>
				<td></td>
				<td><input type="text" class="image" name="dockingShipRegistry" value="<?=$pendingArray['dockingShipRegistry'];?>" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Class</td>
				<td></td>
				<td><input type="text" class="image" name="dockingShipClass" value="<?=$pendingArray['dockingShipClass'];?>" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Sim URL</td>
				<td></td>
				<td><input type="text" class="image" name="dockingShipURL" value="<?=$pendingArray['dockingShipURL'];?>" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Status</td>
				<td></td>
				<td>
					<select name="dockingStatus">
						<option value="activated"<? if($pendingArray['dockingStatus'] == "activated") { echo " selected"; } ?>>Activated</option>
						<option value="departed"<? if($pendingArray['dockingStatus'] == "departed") { echo " selected"; } ?>>Departed</option>
						<option value="pending"<? if($pendingArray['dockingStatus'] == "pending") { echo " selected"; } ?>>Pending</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">CO</td>
				<td></td>
				<td><input type="text" class="image" name="dockingShipCO" value="<?=$pendingArray['dockingShipCO'];?>" /></td>
			</tr>
			<tr>
				<td class="hudLabel">CO Email</td>
				<td></td>
				<td><input type="text" class="image" name="dockingShipCOEmail" value="<?=$pendingArray['dockingShipCOEmail'];?>" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Duration</td>
				<td></td>
				<td><textarea name="dockingDuration" rows="2" class="desc"><?=$pendingArray['dockingDuration'];?></textarea></td>
			</tr>
			<tr>
				<td class="hudLabel">Description</td>
				<td></td>
				<td><textarea name="dockingDesc" rows="8" class="desc"><?=$pendingArray['dockingDesc'];?></textarea></td>
			</tr>
		</table>

		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['dockid'];?>" />
			<input type="hidden" name="action_type" value="edit" />
			<input type="hidden" name="action_tab" value="<?=$tab_action;?>" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>