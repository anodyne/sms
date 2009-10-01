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

	<h2>Delete Docked Ship?</h2>
	<p>Are you sure you want to delete the <strong class="orange"><? printText($pendingArray['dockingShipName']);?></strong> from your docked ships?  This action cannot be undone!</p>
	
	<hr size="1" width="100%" />

	<h3><? printText( $pendingArray['dockingShipName'] . " " . $pendingArray['dockingShipRegistry'] );?></h3>
	<h4><? printText( $pendingArray['dockingShipCO'] );?></h4>
	
	<div class="overflow">
		<strong>Duration:</strong> <? printText($pendingArray['dockingDuration']);?></strong><br /><br />
		<? printText( $pendingArray['dockingDesc'] );?>
	</div>
		
	<form method="post" action="">
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['dockid'];?>" />
			<input type="hidden" name="action_type" value="delete" />
			<input type="hidden" name="action_tab" value="<?=$tab_action;?>" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>