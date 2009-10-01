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

if(in_array("m_departments", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');

?>

	<h2>Create New Department</h2>
	<p>Use the fields below to create a new department to use throughout the system. Once the department is created, you can tie positions and ranks to the department.</p>
	<br />

	<form method="post" action="">
		<table class="hud_guts" cellpadding="3">
			<tr>
				<td class="hudLabel">Name</td>
				<td></td>
				<td><input type="text" class="image" name="deptName" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Class</td>
				<td></td>
				<td><input type="text" class="order" name="deptClass" value="1" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Order</td>
				<td></td>
				<td><input type="text" class="order" name="deptOrder" value="1" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Color</td>
				<td></td>
				<td><input type="text" class="color" name="deptColor" value="ffffff" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Type</td>
				<td></td>
				<td>
					<select name="deptType">
						<option value="playing">Playing Dept</option>
						<option value="nonplaying">Non-Playing Dept</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="hudLabel">Display?</td>
				<td></td>
				<td>
					<select name="deptDisplay">
						<option value="y">Yes</option>
						<option value="n">No</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Description</td>
				<td></td>
				<td><textarea name="deptDesc" rows="5" class="desc"></textarea></td>
			</tr>
		</table>

		<div>
			<input type="hidden" name="action_type" value="create" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>