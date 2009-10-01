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

if(in_array("m_positions", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	
	$get = "SELECT * FROM sms_departments WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
	$getR = mysql_query($get);

?>

	<h2>Create New Position</h2>
	<p>Use the fields below to create a new position to be used throughout the system. Once the position is created, you can assign players to that position.</p>
	<br />

	<form method="post" action="">
		<table class="hud_guts" cellpadding="3">
			<tr>
				<td class="hudLabel">Name</td>
				<td></td>
				<td><input type="text" class="image" name="positionName" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Order</td>
				<td></td>
				<td><input type="text" class="order" name="positionOrder" value="1" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Open Slots</td>
				<td></td>
				<td><input type="text" class="order" name="positionOpen" value="1" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Display?</td>
				<td></td>
				<td>
					<select name="positionDisplay">
						<option value="y">Yes</option>
						<option value="n">No</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Department</td>
				<td></td>
				<td>
					<select name="positionDept">
						<?php
						
						while($fetch = mysql_fetch_assoc($getR)) {
							extract($fetch, EXTR_OVERWRITE);
							
							echo "<option value='" . $deptid . "'>" . $deptName . "</option>";
							
						}
						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="hudLabel">Type</td>
				<td></td>
				<td>
					<select name="positionType">
						<option value="senior">Senior Staff</option>
						<option value="crew">Crew</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td class="hudLabel">Description</td>
				<td></td>
				<td><textarea name="positionDesc" rows="5" class="desc"></textarea></td>
			</tr>
		</table>

		<div>
			<input type="hidden" name="action_type" value="create" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>