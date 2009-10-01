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

if(in_array("m_npcs2", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	include_once('../../framework/functionsAdmin.php');
	
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT c.*, p.* FROM sms_crew AS c, sms_positions AS p WHERE c.positionid = p.positionid AND c.crewid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );

?>

	<h2>Activate Non-Playing Character?</h2>
	<p>Are you sure you want to activate the NPC <strong class="orange"><? printCrewName($pendingArray['crewid'], 'rank', 'noLink');?></strong> to become a playing character? Once activated, the player you assign to the character will be able to login to SMS and participate in the game as this character.</p>
	
	<hr size="1" width="100%" />

	<form method="post" action="">
		<table class="hud_guts">
			<tr>
				<td class="hudLabel">Character</td>
				<td></td>
				<td><? printCrewName( $pendingArray['crewid'], 'rank', 'noLink' );?></td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			<tr>
				<td class="hudLabel">Position</td>
				<td></td>
				<td><? printText( $pendingArray['positionName'] );?></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td class="hudLabel">Username</td>
				<td></td>
				<td><input type="text" class="image" name="username" maxlength="32" value="<?=$pendingArray['username'];?>" /></td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			<tr>
				<td class="hudLabel">Password</td>
				<td>&nbsp;</td>
				<td><input type="password" class="image" name="password" maxlength="32" /></td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			<tr>
				<td class="hudLabel">Email Address</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="email" maxlength="64" value="<?=$pendingArray['email'];?>" /></td>
			</tr>
		</table>
		<p></p>
		
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['crewid'];?>" />
			<input type="hidden" name="action_type" value="activate" />
			<input type="hidden" name="position1" value="<?=$pendingArray['positionid'];?>" />
			<input type="hidden" name="position2" value="<?=$pendingArray['positionid2'];?>" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>