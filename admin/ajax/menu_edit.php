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

	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_menu_items WHERE menuid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );
	
	switch($pendingArray['menuCat'])
	{
		case 'main':
			$action_tab = 1;
			$action_tab_sub = 1;
			$action_tab_sub_a = 1;
			break;
		case 'general':
			$action_tab = 2;
			$action_tab_sub_a = 1;
			
			switch($pendingArray['menuMainSec'])
			{
				case 'main':
					$action_tab_sub = 1;
					break;
				case 'personnel':
					$action_tab_sub = 2;
					break;
				case 'ship':
					$action_tab_sub = 4;
					break;
				case 'simm':
					$action_tab_sub = 3;
					break;
			}
			
			break;
		case 'admin':
			$action_tab = 3;
			$action_tab_sub = 1;
			
			switch($pendingArray['menuMainSec'])
			{
				case 'post':
					$action_tab_sub_a = 1;
					break;
				case 'manage':
					$action_tab_sub_a = 2;
					break;
				case 'reports':
					$action_tab_sub_a = 4;
					break;
				case 'user':
					$action_tab_sub_a = 3;
					break;
			}
			
			break;
	}

?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('a#show_adv').click(function() {
				$('#advanced').toggle(75);
				return false;
			});
		});
	</script>
	
	<h2>Edit Menu Item</h2>
	<p>Use the fields below to edit the menu item. If you want more options (menu availability, section, category, authentication, or user access control), you can use the <strong>advanced options</strong> link below to show the remaining menu item options. <strong class='orange'>Use extreme caution when editing menu items. Incorrect modification can cause you to not be able to access the menu items any more!</strong></p>
	
	<hr size="1" width="100%" />
	
	<form method="post" action="">
		<table class="hud_guts">
			<tr>
				<td class="hudLabel">Title</td>
				<td></td>
				<td><input type="text" class="image" name="menuTitle" value="<?=$pendingArray['menuTitle'];?>" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Link URL</td>
				<td></td>
				<td><input type="text" class="image" name="menuLink" value="<?=$pendingArray['menuLink'];?>" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Link Type</td>
				<td></td>
				<td>
					<select name="menuLinkType">
						<option value="onsite"<? if( $pendingArray['menuLinkType'] == "onsite" ) { echo " selected"; } ?>>Onsite</option>
						<option value="offsite"<? if( $pendingArray['menuLinkType'] == "offsite" ) { echo " selected"; } ?>>Offsite</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Group</td>
				<td></td>
				<td><input type="text" class="order" name="menuGroup" size="3" value="<?=$pendingArray['menuGroup'];?>" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Order</td>
				<td></td>
				<td><input type="text" class="order" name="menuOrder" size="3" value="<?=$pendingArray['menuOrder'];?>" /></td>
			</tr>
		</table>
		
		<p><a href="#" id="show_adv" class="fontNormal"><strong>Advanced Options &raquo;</strong></a></p>
		
		<div id="advanced" style="display:none;">
			<table class="hud_guts">
				<tr>
					<td class="hudLabel">Status</td>
					<td></td>
					<td>
						<input type="radio" name="menuAvailability" id="maOn" value="on"<? if( $pendingArray['menuAvailability'] == "on" ) { echo " checked"; } ?>/><label for="maOn">On</label>
						<input type="radio" name="menuAvailability" id="maOff" value="off"<? if( $pendingArray['menuAvailability'] == "off" ) { echo " checked"; } ?>/><label for="maOff">Off</label>
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
							<option value="main"<? if( $pendingArray['menuCat'] == "main" ) { echo " selected"; } ?>>Main Navigation</option>
							<option value="general"<? if( $pendingArray['menuCat'] == "general" ) { echo " selected"; } ?>>General Menus</option>
							<option value="admin"<? if( $pendingArray['menuCat'] == "admin" ) { echo " selected"; } ?>>Admin Menus</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="hudLabel">Section</td>
					<td></td>
					<td>
						<select name="menuMainSec">
							<optgroup label="Main Navigation">
								<option value=""<? if( $pendingArray['menuMainSec'] == "" ) { echo " selected"; } ?>>Main Navigation</option>
							</optgroup>
							<optgroup label="General Menus">
								<option value="main"<? if( $pendingArray['menuMainSec'] == "main" ) { echo " selected"; } ?>>Main</option>
								<option value="personnel"<? if( $pendingArray['menuMainSec'] == "personnel" ) { echo " selected"; } ?>>Personnel</option>
								<option value="ship"<? if( $pendingArray['menuMainSec'] == "ship" ) { echo " selected"; } ?>><?=ucfirst( $simmType );?></option>
								<option value="simm"<? if( $pendingArray['menuMainSec'] == "simm" ) { echo " selected"; } ?>>Simm</option>
							</optgroup>
							<optgroup label="Admin Menus">
								<option value="post"<? if( $pendingArray['menuMainSec'] == "post" ) { echo " selected"; } ?>>Post</option>
								<option value="manage"<? if( $pendingArray['menuMainSec'] == "manage" ) { echo " selected"; } ?>>Manage</option>
								<option value="reports"<? if( $pendingArray['menuMainSec'] == "reports" ) { echo " selected"; } ?>>Reports</option>
								<option value="user"<? if( $pendingArray['menuMainSec'] == "user" ) { echo " selected"; } ?>>User</option>
							</optgroup>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="15"></td>
				</tr>
			
				<tr>
					<td class="hudLabel">Requires Login?</td>
					<td></td>
					<td>
						<input type="radio" id="menuLoginY" name="menuLogin" value="y" <? if( $pendingArray['menuLogin'] == "y" ) { echo "checked"; } ?>/><label for="menuLoginY">Yes</label>
						<input type="radio" id="menuLoginN" name="menuLogin" value="n" <? if( $pendingArray['menuLogin'] == "n" ) { echo "checked"; } ?>/><label for="menuLoginN">No</label>
					</td>
				</tr>
			
				<tr>
					<td colspan="3" height="15"></td>
				</tr>
				
				<?php if($pendingArray['menuCat'] == 'admin') {} else { ?>
				<tr>
					<td colspan="3" class="fontNormal yellow bold">Main navigation and general menus do not use the access control system. Changing this field for either category will not affect anything.</td>
				</tr>	
				<?php } ?>
				<tr>
					<td class="hudLabel">Access Code</td>
					<td></td>
					<td><input type="text" class="title" name="menuAccess" value="<?=$pendingArray['menuAccess'];?>" /></td>
				</tr>
			</table><br /><br />
		</div>
		
		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['menuid'];?>" />
			<input type="hidden" name="action_type" value="edit" />
			<input type="hidden" name="action_tab" value="<?=$action_tab;?>" />
			<input type="hidden" name="action_tab_sub" value="<?=$action_tab_sub;?>" />
			<input type="hidden" name="action_tab_sub_a" value="<?=$action_tab_sub_a;?>" />
			
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>
	</form>

<?php } /* close the referer check */ ?>