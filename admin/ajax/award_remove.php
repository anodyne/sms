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

if(in_array("m_removeaward", $sessionAccess))
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
	$get = "SELECT awards FROM sms_crew WHERE crewid = $crew LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );
	
	$awards_raw = explode(";", $pendingArray['awards']);
	$awards_raw = array_reverse($awards_raw);
	
	foreach($awards_raw as $key => $value)
	{
		if($key == $award) {
			$awards_raw[$key] = explode(",", $value);
		} else {
			unset($awards_raw[$key]);
		}
	}
	
	$award_id = trim($awards_raw[$award][0]);
	
	$get2 = "SELECT * FROM sms_awards WHERE awardid = $award_id LIMIT 1";
	$get2R = mysql_query($get2);
	$fetch = mysql_fetch_assoc($get2R);
	
	if( file_exists( '../../images/awards/large/' . $fetch['awardImage'] ) ) {
		$image = $webLocation . 'images/awards/large/' . $fetch['awardImage'];
	} else {
		$image = $webLocation . 'images/awards/' . $fetch['awardImage'];
	}

?>

	<h2>Remove Award?</h2>
	<p>Are you sure you want to remove this award from <strong class="orange"><? printCrewName($crew, 'rank', 'noLink');?></strong>? This action cannot be undone!</p>
	
	<hr size="1" />
	
	<form method="post" action="admin.php?page=manage&sub=removeaward&crew=<?=$crew;?>">
		<h2><? printText( $fetch['awardName'] );?></h2>
		<h4>
			<img src="<?=$image;?>" alt="<?=$fetch['awardName'];?>" border="0" style="float:left; padding-right:10px;" />
			<? printText( $fetch['awardDesc'] );?>
		</h4>
		<div style="clear:both;"></div>
		
		<h3>Recipient: <? printCrewName( $crew, 'rank', 'noLink' );?></h3>
		<div class="overflow"><? printText($awards_raw[$award][2]);?></div>
		
		<p></p>
		
		<div>
			<input type="hidden" name="action_award" value="<?=$award;?>" />
			<input type="hidden" name="action_crew" value="<?=$crew;?>" />
			<input type="hidden" name="action_type" value="remove" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>