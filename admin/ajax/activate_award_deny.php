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

if(in_array("m_giveaward", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsAdmin.php');
	include_once('../../framework/functionsUtility.php');

	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT q.*, a.* FROM sms_awards_queue AS q, sms_awards AS a ";
	$get.= "WHERE q.id = $id AND q.award = a.awardid LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );
	
	if( file_exists( '../../images/awards/large/' . $pendingArray['awardImage'] ) ) {
		$image = $webLocation . 'images/awards/large/' . $pendingArray['awardImage'];
	} else {
		$image = $webLocation . 'images/awards/' . $pendingArray['awardImage'];
	}

?>
	<h2>Deny Award Nomination?</h2>
	<p>Are you sure you want to deny this award nomination?  Once denied, the award will be removed from the queue and can only be re-added to the queue by nominating again.</p>
	
	<hr size="1" width="100%" />
	
	<form method="post" action="">
		<h2><? printText( $pendingArray['awardName'] );?></h2>
		<h4>
			<img src="<?=$image;?>" alt="<?=$pendingArray['awardName'];?>" border="0" style="float:left; padding-right:10px;" />
			<? printText( $pendingArray['awardDesc'] );?>
		</h4>
		<div style="clear:both;"></div>
		<p>
			<strong>Recipient:</strong> <? printCrewName( $pendingArray['nominated'], 'rank', 'noLink' );?><br />
			<strong>Nominated By:</strong> <? printCrewName( $pendingArray['crew'], 'rank', 'noLink' );?>
		</p>
		
		<div class="overflow"><? printText( $pendingArray['reason'] );?></div>
		
		<p></p>
		
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['id'];?>" />
			<input type="hidden" name="action_category" value="award" />
			<input type="hidden" name="action_type" value="reject" />
		
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>