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

if(in_array("m_awards", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');

?>

	<h2>Create New Award</h2>
	<p>Use the fields below to create a new award to be given to playing characters and/or non-playing characters. Once the award is created, you can give it to characters and NPCs alike. <strong class="yellow">Note:</strong> only in character awards can be given to NPCs. Playing characters can get all awards. When creating an award, we recommend having two images, one small image (that will be displayed in the character bio) and a larger image (that will be displayed in the list of awards). When you create the award, just type the name of the image <em class="orange">(i.e. award.jpg)</em> and not the full path.</p>
	<br />

	<form method="post" action="">
		<table class="hud_guts" cellpadding="3">
			<tr>
				<td class="hudLabel">Name</td>
				<td></td>
				<td><input type="text" class="image" name="awardName" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Image</td>
				<td></td>
				<td><input type="text" class="image" name="awardImage" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Order</td>
				<td></td>
				<td><input type="text" class="order" name="awardOrder" value="1" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Category</td>
				<td></td>
				<td>
					<select name="awardCat">
						<option value="ic">In Character</option>
						<option value="ooc">Out of Character</option>
						<option value="both">Both</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Description</td>
				<td></td>
				<td><textarea name="awardDesc" rows="5" class="desc"></textarea></td>
			</tr>
		</table>

		<div>
			<input type="hidden" name="action_type" value="create" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>