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
	
	/* get the data */
	$get = "SELECT * FROM sms_crew WHERE crewid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );

?>

	<h2>Edit Player Moderation</h2>
	<p>From this page you can set the moderation status for your crew. Moderated posts will require approval before being sent out to the crew. If a player is moderated and they attempted to post a joint post, the joint post will require approval before it is sent out to the crew, even if the other members are not moderated. Unmoderated posts will be sent out without any need for activation.</p>
	
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
				<td class="hudLabel">Mission Posts</td>
				<td></td>
				<td>
					<select name="m_posts">
						<option value="n"<?php if($pendingArray['moderatePosts'] == 'n') { echo " selected"; } ?>>No Moderation</option>
						<option value="y"<?php if($pendingArray['moderatePosts'] == 'y') { echo " selected"; } ?>>Moderated</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="hudLabel">Personal Logs</td>
				<td></td>
				<td>
					<select name="m_logs">
						<option value="n"<?php if($pendingArray['moderateLogs'] == 'n') { echo " selected"; } ?>>No Moderation</option>
						<option value="y"<?php if($pendingArray['moderateLogs'] == 'y') { echo " selected"; } ?>>Moderated</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			<tr>
				<td class="hudLabel">News Items</td>
				<td></td>
				<td>
					<select name="m_news">
						<option value="n"<?php if($pendingArray['moderateNews'] == 'n') { echo " selected"; } ?>>No Moderation</option>
						<option value="y"<?php if($pendingArray['moderateNews'] == 'y') { echo " selected"; } ?>>Moderated</option>
					</select>
				</td>
			</tr>
		</table>
		<p></p>
		
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['crewid'];?>" />
			<input type="hidden" name="action_type" value="moderate" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>