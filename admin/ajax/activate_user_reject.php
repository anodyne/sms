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
	$sessionAccess = false;
}

if( !is_array( $sessionAccess ) ) {
	$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
}

if(in_array("x_approve_users", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');

	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$getPendingCrew = "SELECT crewid, firstName, lastName, positionid, rankid ";
	$getPendingCrew.= "FROM sms_crew WHERE crewid = $id LIMIT 1";
	$getPendingCrewResult = mysql_query( $getPendingCrew );
	$pendingArray = mysql_fetch_assoc( $getPendingCrewResult );

?>
	<h2>Reject Crew Application &ndash; <? printText( $pendingArray['firstName'] . " " . $pendingArray['lastName'] );?></h2>
	<p>Please specify message you want to be sent to the player regarding their rejection.</p>
	<p>Rejection messages can now use wild cards for dynamic elements. For instance, using the <strong class="yellow">#position#</strong> wild card will insert the position they applied for into the email before it is sent. Available wild cards are: <strong>#ship#</strong>, <strong>#position#</strong>, and <strong>#player#</strong> (character&rsquo;s name).</p>
	
	<form method="post" action="">
		<table>
			<tr>
				<td class="tableCellLabel">Email Message</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="rejectMessage" class="narrowTable" rows="10"><?=stripslashes( $rejectMessage );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td>
					<input type="hidden" name="position" value="<?=$pendingArray['positionid'];?>" />
					<input type="hidden" name="action_id" value="<?=$pendingArray['crewid'];?>" />
					<input type="hidden" name="action_category" value="user" />
					<input type="hidden" name="action_type" value="reject" />
					
					<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Reject" />
				</td>
			</tr>
		</table>
	</form>

<?php } /* close the access check */ ?>