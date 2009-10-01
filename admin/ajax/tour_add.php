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

if(in_array("m_tour", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');

?>

	<h2>Add New Tour Item</h2>
	<p>Use the fields below to create a new tour item. Once created, the tour item will be displayed in the Tour section of the site. If you want to add images to your tour item, they must be stored on your server and be put in the <em class="orange">images/tour</em> directory. Simply reference the name and extension of the image.</p>
	<br />

	<form method="post" action="">
		<table class="hud_guts" cellpadding="3">
			<tr>
				<td class="hudLabel">Name</td>
				<td></td>
				<td><input type="text" class="image" name="tourName" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Location</td>
				<td></td>
				<td><input type="text" class="image" name="tourLocation" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Picture #1</td>
				<td></td>
				<td><input type="text" class="image" name="tourPicture1" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Picture #2</td>
				<td></td>
				<td><input type="text" class="image" name="tourPicture2" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Picture #3</td>
				<td></td>
				<td><input type="text" class="image" name="tourPicture3" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Order</td>
				<td></td>
				<td><input type="text" class="order" name="tourOrder" value="1" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Display?</td>
				<td></td>
				<td>
					<select name="tourDisplay">
						<option value="y">Yes</option>
						<option value="n">No</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Summary</td>
				<td></td>
				<td><textarea name="tourSummary" rows="2" class="desc"></textarea></td>
			</tr>
			<tr>
				<td class="hudLabel">Description</td>
				<td></td>
				<td><textarea name="tourDesc" rows="12" class="desc"></textarea></td>
			</tr>
		</table>

		<div>
			<input type="hidden" name="action_type" value="add" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>