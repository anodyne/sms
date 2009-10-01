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
	include_once('../../framework/functionsAdmin.php');
	include_once('../../framework/functionsUtility.php');

	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_news WHERE newsid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );

?>
	<h2>Delete Pending News Item?</h2>
	<p>Are you sure you want to delete this news item?  This action cannot be undone!</p>
	
	<hr size="1" width="100%" />
	
	<form method="post" action="">
		<h3><? printText( $pendingArray['newsTitle'] );?></h3>
		<h4>By <? printCrewName( $pendingArray['newsAuthor'], 'rank', 'noLink' );?></h4>
		
		<div class="overflow"><? printText( $pendingArray['newsContent'] );?></div>
		
		<p></p>
		
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['newsid'];?>" />
			<input type="hidden" name="action_category" value="news" />
			<input type="hidden" name="action_type" value="delete" />
		
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Delete" />
		</div>
	</form>

<?php } /* close the referer check */ ?>