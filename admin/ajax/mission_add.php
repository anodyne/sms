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

if(in_array("m_missions", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	
	$now = getdate();

?>

	<h2>Add New Mission</h2>
	<p>Use the fields below to provide details for your new mission. <strong class="yellow">Note:</strong> Mission images <strong>must</strong> be located in the <em>images/missionimages</em> directory. All you need to do is specify the image in that directory.</p>
	<br />

	<form method="post" action="">
		<table class="hud_guts">
			<tr>
				<td class="hudLabel">Title</td>
				<td></td>
				<td><input type="text" class="image" name="missionTitle" value="" /></td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			<tr>
				<td class="hudLabel">Image</td>
				<td></td>
				<td>
					<span class="fontSmall">images/missionimages/</span><br />
					<input type="text" class="image" name="missionImage" value="" />
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Order</td>
				<td></td>
				<td><input type="text" class="order" name="missionOrder" value="" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Status</td>
				<td></td>
				<td>
					<select name="missionStatus">
						<option value="upcoming">Upcoming Mission</option>
						<option value="current">Current Mission</option>
						<option value="completed">Completed Mission</option>
					</select>
				</td>
			</tr>
			
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Start Date</td>
				<td></td>
				<td><input type="text" class="date" name="missionStart" value="<?=dateFormat('sql', $now[0]);?>" /></td>
			</tr>
			<tr>
				<td class="hudLabel">End Date</td>
				<td></td>
				<td><input type="text" class="date" name="missionEnd" value="0000-00-00 00:00:00" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Description</td>
				<td></td>
				<td><textarea name="missionDesc" class="desc" rows="7"></textarea></td>
			</tr>
		</table>

		<br /><br />

		<div>
			<input type="hidden" name="action_type" value="create" />

			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>