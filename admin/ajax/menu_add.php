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

?>
	<h2>Add Menu Item</h2>
	<p>Use the fields below to provide details for your new menu item. Please note that creating admin items that require unique access controls is not supported in this version of SMS!</p>
	<br />
	
	<form method="post" action="">
		<table class="hud_guts">
			<tr>
				<td class="hudLabel">Title</td>
				<td></td>
				<td><input type="text" class="image" name="menuTitle" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Link</td>
				<td></td>
				<td><input type="text" class="image" name="menuLink" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Link Type</td>
				<td></td>
				<td>
					<select name="menuLinkType">
						<option value="onsite">Onsite</option>
						<option value="offsite">Offsite</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Category</td>
				<td></td>
				<td>
					<select name="menuCat">
						<option value="main">Main Navigation</option>
						<option value="general">General Menus</option>
						<option value="admin">Admin Menus</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="hudLabel">Section</td>
				<td></td>
				<td>
					<select name="menuMainSec">
						<optgroup label="Main Navigation">
							<option value="">Main Navigation</option>
						</optgroup>
						<optgroup label="General Menus">
							<option value="main">Main</option>
							<option value="personnel">Personnel</option>
							<option value="ship"><?=ucfirst( $simmType );?></option>
							<option value="simm">Simm</option>
						</optgroup>
						<optgroup label="Admin Menus">
							<option value="post">Post</option>
							<option value="manage">Manage</option>
							<option value="reports">Reports</option>
							<option value="user">User</option>
						</optgroup>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Group</td>
				<td></td>
				<td><input type="text" class="order" name="menuGroup" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Order</td>
				<td></td>
				<td><input type="text" class="order" name="menuOrder" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Requires Login?</td>
				<td></td>
				<td>
					<input type="radio" id="menuLoginY" name="menuLogin" value="y" /><label for="menuLoginY">Yes</label>
					<input type="radio" id="menuLoginN" name="menuLogin" value="n" checked="y" /><label for="menuLoginN">No</label>
				</td>
			</tr>
		</table>
		
		<br /><br />
		
		<div>
			<input type="hidden" name="action_type" value="create" />
			
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>