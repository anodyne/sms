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

if(in_array("x_approve_news", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');

	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_database WHERE dbid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );

?>
	<h2>Delete Database Entry?</h2>
	<p>Are you sure you want to delete this database entry?  This action cannot be undone!</p>
	
	<hr size="1" width="100%" />
	
	<form method="post" action="">
		<h3><? printText( $pendingArray['dbTitle'] );?></h3>
		<p><? printText($pendingArray['dbDesc']);?></p>
		
		<?php if($pendingArray['dbType'] == "entry") { ?>
			<div class="overflow"><? printText( $pendingArray['dbContent'] );?></div>
		<?php
		
		} else {
			switch($pendingArray['dbType'])
			{
				case 'onsite':
					echo "On-site Entry: <a href='" . $webLocation . $pendingArray['dbURL'] . "'>" . $webLocation . $pendingArray['dbURL'] . "</a>";
					break;
				case 'offsite':
					echo "Off-site Entry: <a href=" . $pendingArray['dbURL'] . "' target='_blank'>" . $pendingArray['dbURL'] . "</a>";
					break;
			}
		}
		
		?>
		
		<p></p>
		
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['dbid'];?>" />
			<input type="hidden" name="action_type" value="delete" />
		
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Delete" />
		</div>
	</form>

<?php } /* close the referer check */ ?>