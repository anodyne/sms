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
	include_once('../../framework/functionsUtility.php');
	include_once('../../framework/functionsAdmin.php');
	
	if(isset($_GET['c']) && is_numeric($_GET['c']))
	{
		$crew = $_GET['c'];
	}
	
	if(isset($_GET['a']) && is_numeric($_GET['a']))
	{
		$award = $_GET['a'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_awards WHERE awardid = $award LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );
	
	if( file_exists( '../../images/awards/large/' . $pendingArray['awardImage'] ) ) {
		$image = $webLocation . 'images/awards/large/' . $pendingArray['awardImage'];
	} else {
		$image = $webLocation . 'images/awards/' . $pendingArray['awardImage'];
	}

?>

	<h2>Give Award</h2>
	<p>To give this award, please fill out the reason and hit OK. <strong>Please note:</strong> due to the way in which awards are stored in the database, you cannot use semicolons (;) or vertical bars (|) in your reason. Semicolons in the reason will be replaced with commas.</p>
	
	<hr size="1" />
	
	<form method="post" action="admin.php?page=manage&sub=addaward&crew=<?=$crew;?>">
		<h2><? printText( $pendingArray['awardName'] );?></h2>
		<h4>
			<img src="<?=$image;?>" alt="<?=$pendingArray['awardName'];?>" border="0" style="float:left; padding-right:10px;" />
			<? printText( $pendingArray['awardDesc'] );?>
		</h4>
		<div style="clear:both;"></div>
		
		<h3>Recipient: <? printCrewName( $crew, 'rank', 'noLink' );?></h3>
		
		<h3>Reason:</h3>
		<textarea name="reason" rows="12" class="desc"></textarea>
		
		<p></p>
		
		<div>
			<input type="hidden" name="action_award" value="<?=$award;?>" />
			<input type="hidden" name="action_crew" value="<?=$crew;?>" />
			<input type="hidden" name="action_type" value="give" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>