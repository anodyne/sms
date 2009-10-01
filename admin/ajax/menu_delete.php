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

if(in_array("x_menu", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsAdmin.php');
	include_once('../../framework/functionsUtility.php');

	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_menu_items WHERE menuid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );
	
	switch($pendingArray['menuCat'])
	{
		case 'main':
			$action_tab = 1;
			$section = "Main Navigation";
			break;
		case 'general':
			$action_tab = 2;
			$section = "General System menus";
			$action_tab_sub_a = 1;
			
			switch($pendingArray['menuMainSec'])
			{
				case 'main':
					$action_tab_sub = 1;
					break;
				case 'personnel':
					$action_tab_sub = 2;
					break;
				case 'ship':
					$action_tab_sub = 4;
					break;
				case 'simm':
					$action_tab_sub = 3;
					break;
			}
			
			break;
		case 'admin':
			$action_tab = 3;
			$section = "Administration System menus";
			$action_tab_sub = 1;
			
			switch($pendingArray['menuMainSec'])
			{
				case 'post':
					$action_tab_sub_a = 1;
					break;
				case 'manage':
					$action_tab_sub_a = 2;
					break;
				case 'reports':
					$action_tab_sub_a = 4;
					break;
				case 'user':
					$action_tab_sub_a = 3;
					break;
			}
			
			break;
	}

?>
	<h2>Delete Menu Item?</h2>
	<p>Are you sure you want to delete the <strong class="orange"><? printText($pendingArray['menuTitle']);?></strong> menu item from the <?=$section;?>?  This action cannot be undone and could cause problems with SMS! If you are unsure, we recommend that you simply turn the menu item&rsquo;s availability to OFF.</p>
	
	<form method="post" action="">
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['menuid'];?>" />
			<input type="hidden" name="action_type" value="delete" />
			<input type="hidden" name="action_tab" value="<?=$action_tab;?>" />
			<input type="hidden" name="action_tab_sub" value="<?=$action_tab_sub;?>" />
			<input type="hidden" name="action_tab_sub_a" value="<?=$action_tab_sub_a;?>" />
			
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>