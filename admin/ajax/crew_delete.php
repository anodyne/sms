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

if(in_array("m_crew", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	include_once('../../framework/functionsAdmin.php');
	
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	if(isset($_GET['t']) && is_numeric($_GET['t']))
	{
		$t = $_GET['t'];
	}
	
	/* get the data */
	$get = "SELECT c.*, p.* FROM sms_crew AS c, sms_positions AS p WHERE c.positionid = p.positionid AND c.crewid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );

?>

	<h2>Delete Character?</h2>
	<p>Are you sure you want to delete <strong class="orange"><? printCrewName($pendingArray['crewid'], 'rank', 'noLink');?></strong>? This action cannot be undone!</p>
	
	<hr size="1" width="100%" />

	<form method="post" action="">
		<table class="hud_guts">
			<tr>
				<td class="hudLabel">Character</td>
				<td></td>
				<td><? printCrewName( $pendingArray['crewid'], 'rank', 'noLink' );?></td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="hudLabel">Position</td>
				<td></td>
				<td><? printText( $pendingArray['positionName'] );?></td>
			</tr>
		</table>
		<p></p>
		
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['crewid'];?>" />
			<input type="hidden" name="action_type" value="delete" />
			<input type="hidden" name="action_tab" value="<?=$t;?>" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>