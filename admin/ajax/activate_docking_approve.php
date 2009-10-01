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

if(in_array("x_approve_docking", $sessionAccess))
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

?>
	<h2>Approve Pending Docking Request?</h2>
	<p>Are you sure you want to approve this docking request?  Once approved, the CO of the docking ship will be notified of their approval.</p>
	
	<hr size="1" width="100%" />
	
	<form method="post" action="">
		<h3><? printText( $pendingArray['dockingShipName'] . " " . $pendingArray['dockingShipRegistry'] );?></h3>
		<p>
			<strong>Ship Class:</strong> <? printText($pendingArray['dockingShipClass']);?><br />
			<strong>Website:</strong> <a href="<?=$pendingArray['dockingShipURL'];?>" target="_blank"><?=$pendingArray['dockingShipURL'];?></a><br /><br />
			
			<strong>Commanding Officer:</strong> <? printText($pendingArray['dockingShipCO']);?><br />
			<strong>Email:</strong> <? printText($pendingArray['dockingShipCOEmail']);?><br /><br />
			
			<strong>Docking Duration:</strong> <? printText($pendingArray['dockingShipDuration']);?>
		</p>
		
		<div class="overflow"><? printText( $pendingArray['dockingDesc'] );?></div>
		
		<p></p>
		
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['dockid'];?>" />
			<input type="hidden" name="action_category" value="docking" />
			<input type="hidden" name="action_type" value="approve" />
			
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>