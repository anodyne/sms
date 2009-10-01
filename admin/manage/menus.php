<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/menus.php
Purpose: Page to manage the menu items

System Version: 2.6.8
Last Modified: 2009-01-10 1640 EST
**/

/* access check */
if( in_array( "x_menu", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	$action_type = FALSE;
	$tab = 1;
	$sub_tab = 1;
	$sub_tab_a = 1;
	
	if(isset($_POST))
	{
		/* define the POST variables */
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		/* protecting against SQL injection */
		if(isset($action_id) && !is_numeric($action_id))
		{
			$action_id = FALSE;
			exit();
		}
		
		switch($action_type)
		{
			case 'create':
				
				$create = "INSERT INTO sms_menu_items (menuGroup, menuOrder, menuTitle, menuLinkType, ";
				$create.= "menuLink, menuMainSec, menuLogin, menuCat) ";
				$create.= "VALUES (%d, %d, %s, %s, %s, %s, %s, %s)";
				
				$query = sprintf(
					$create,
					escape_string($_POST['menuGroup']),
					escape_string($_POST['menuOrder']),
					escape_string($_POST['menuTitle']),
					escape_string($_POST['menuLinkType']),
					escape_string($_POST['menuLink']),
					escape_string($_POST['menuMainSec']),
					escape_string($_POST['menuLogin']),
					escape_string($_POST['menuCat'])
				);
				
				$result = mysql_query($query);
				
				/* optimize the table */
				optimizeSQLTable( "sms_menu_items" );
				
				/* set the action */
				$action = $action_type;
				
				break;
			case 'edit':
				
				$edit = "UPDATE sms_menu_items SET menuGroup = %d, menuOrder = %d, menuTitle = %s, ";
				$edit.= "menuLinkType = %s, menuLink = %s, menuAccess = %s, menuMainSec = %s, ";
				$edit.= "menuLogin = %s, menuCat = %s, menuAvailability = %s WHERE menuid = $action_id";
				
				$query = sprintf(
					$edit,
					escape_string($_POST['menuGroup']),
					escape_string($_POST['menuOrder']),
					escape_string($_POST['menuTitle']),
					escape_string($_POST['menuLinkType']),
					escape_string($_POST['menuLink']),
					escape_string($_POST['menuAccess']),
					escape_string($_POST['menuMainSec']),
					escape_string($_POST['menuLogin']),
					escape_string($_POST['menuCat']),
					escape_string($_POST['menuAvailability'])
				);
				
				$result = mysql_query($query);
				
				/* optimize the table */
				optimizeSQLTable( "sms_menu_items" );
				
				/* set the action */
				$action = "update";
				
				if(isset($_POST['action_tab']) && is_numeric($_POST['action_tab']))
				{
					$tab = $_POST['action_tab'];
				}
				
				if(isset($_POST['action_tab_sub']) && is_numeric($_POST['action_tab_sub']))
				{
					$sub_tab = $_POST['action_tab_sub'];
				}
				
				if(isset($_POST['action_tab_sub_a']) && is_numeric($_POST['action_tab_sub_a']))
				{
					$sub_tab_a = $_POST['action_tab_sub_a'];
				}
				
				break;
			case 'delete':
				
				$query = "DELETE FROM sms_menu_items WHERE menuid = $action_id";
				$result = mysql_query($query);
				
				/* optimize the table */
				optimizeSQLTable( "sms_menu_items" );
				
				/* set the action */
				$action = $action_type;
				
				if(isset($_POST['action_tab']) && is_numeric($_POST['action_tab']))
				{
					$tab = $_POST['action_tab'];
				}
				
				if(isset($_POST['action_tab_sub']) && is_numeric($_POST['action_tab_sub']))
				{
					$sub_tab = $_POST['action_tab_sub'];
				}
				
				if(isset($_POST['action_tab_sub_a']) && is_numeric($_POST['action_tab_sub_a']))
				{
					$sub_tab_a = $_POST['action_tab_sub_a'];
				}
				
				break;
		}
	}

$menus = array(
	'main' => array(),
	'general' => array(
		'main' => array(),
		'personnel' => array(),
		'ship' => array(),
		'simm' => array()
	),
	'admin' => array(
		'post' => array(),
		'manage' => array(),
		'reports' => array(),
		'user' => array()
	)
);

/** THE QUERIES **/

/* get the main menu items and dump them into the menu array */
$getMain = "SELECT * FROM sms_menu_items WHERE menuCat = 'main' ORDER BY menuGroup, menuOrder ASC";
$getMainResult = mysql_query( $getMain );

while($menuMain = mysql_fetch_assoc($getMainResult)) {
	extract($menuMain, EXTR_OVERWRITE);
	
	$menus['main'][] = array('id' => $menuid, 'title' => $menuTitle, 'link' => htmlentities($menuLink), 'display' => $menuAvailability);
	
}

/* get the general menu items and dump them into the menu array */
$getGeneral = "SELECT * FROM sms_menu_items WHERE menuCat = 'general' ORDER BY menuGroup, menuOrder ASC";
$getGeneralResult = mysql_query( $getGeneral );

while($menuGen = mysql_fetch_assoc($getGeneralResult)) {
	extract($menuGen, EXTR_OVERWRITE);
	
	$menus['general'][$menuMainSec][] = array('id' => $menuid, 'title' => $menuTitle, 'link' => htmlentities($menuLink), 'display' => $menuAvailability);
	
}

/* get the admin menu items and dump them into the menu array */
$getAdmin = "SELECT * FROM sms_menu_items WHERE menuCat = 'admin' ORDER BY menuGroup, menuOrder ASC";
$getAdminResult = mysql_query( $getAdmin );

while($menuAdmin = mysql_fetch_assoc($getAdminResult)) {
	extract($menuAdmin, EXTR_OVERWRITE);
	
	$menus['admin'][$menuMainSec][] = array('id' => $menuid, 'title' => $menuTitle, 'link' => htmlentities($menuLink), 'display' => $menuAvailability);
	
}

?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#container-1 > ul').tabs(<?php echo $tab;?>);
		$('#container-2 > ul').tabs(<?php echo $sub_tab;?>);
		$('#container-3 > ul').tabs(<?php echo $sub_tab_a;?>);
		
		$('.zebra tr:odd').addClass('alt');

		$("a[rel*=facebox]").click(function() {
			var id = $(this).attr("myID");
			var action = $(this).attr("myAction");

			$.facebox(function() {
				$.get('admin/ajax/menu_' + action + '.php?id=' + id, function(data) {
					$.facebox(data);
				});
			});
			return false;
		});
		
		$('a#toggle').click(function() {
			$('#notes').slideToggle('slow');
			return false;
		});
	});
</script>

<div class="body">
	
	<?
	
	$check = new QueryCheck;
	$check->checkQuery( $result, $query );
	
	if( !empty( $check->query ) ) {
		$check->message( "menu item", $action );
		$check->display();
	}
	
	?>
	
	<span class="fontTitle">Menu Management</span><br /><br />
	Use this page to edit the menus used throughout SMS. From these pages, you will be able to change anything about a menu item or delete the item entirely. <b class="red">Use extreme caution when editing menu items. Incorrect modification can cause you to not be able to access the menu items any more! Deletions cannot be undone.</b> Changes made to any menu item will affect that item across all skins in the system.<br /><br />
	
	<b class="fontMedium"><a href="#" class="add" myAction="add" rel="facebox">Add Menu Item &raquo;</a></b><br />
	
	<? if( $simmType == "starbase" || $usePosting == "n" || $useMissionNotes == "n" ) { ?>
	<br />
	<div class="update">
		<div class="notify-normal">
			<a href="#" id="toggle" class="fontNormal" style="float:right;margin-right:.5em;">Show/Hide</a>
			<span class="fontTitle">Notes</span>
		
			<div id="notes" style="display:none;">
			<br />
		
			Additional information about menu changes is available through Anodyne&rsquo;s <a href="http://docs.anodyne-productions.com/index.php?title=Changing_Menus_Around" target="_blank">
			online documentation</a>.<br /><br />
	
			<? if( $simmType == "starbase" ) { ?>
			Your simm type is set to STARBASE. Please make sure you make the following changes to your menus!
			<ul class="version">
				<li><b>Main Navigation</b>
					<ol>
						<li>Please turn <strong class="red">off</strong> THE SHIP and turn <strong class="green">on</strong> THE STARBASE</li>
					</ol>
				</li>
				<li><b>General Menus (The Ship)</b>
					<ol>
						<li>Please turn <strong class="red">off</strong> both SHIP HISTORY and SHIP TOUR and turn <strong class="green">on</strong> STARBASE HISTORY and STARBASE TOUR</li>
						<li>Please turn <strong class="green">on</strong> the menu item called DOCKED SHIPS</li>
						<li>Please turn <strong class="green">on</strong> the menu item called DOCKING REQUEST</li>
					</ol>
				</li>
				<li><b>Admin Menus (Manage)</b>
					<ol>
						<li>Please turn <strong class="green">on</strong> the menu item called DOCKED SHIPS</li>
						<li>Please turn <strong class="red">off</strong> the menu item called SHIP TOUR and turn <strong class="green">on</strong> STARBASE TOUR</li>
					</ol>
				</li>
			</ul>
			<? } if( $usePosting == "n" ) { ?>
			Your simm type is set to NOT use the SMS Posting system. Please make sure you make the following changes to your menus!
			<ul class="version">
				<li><b>General Menus (The Simm)</b>
					<ol>
						<li>If you are not keeping records on your SMS site you will need to remove the CURRENT MISSION link,
						the MISSION LOGS link, and the MISSION SUMMARIES link</li>
						<li>You may also want to change all the existing menu items in the Simm section to use group zero
						instead of group one</li>
					</ol>
				</li>
				<li><b>Admin Menus</b>
					<ol>
						<li>We do NOT advise removing any of the admin menu items! The advisable way is to change each
						user's access levels to NOT include the post menu. If the main post item is not available, the
						post menus will not display.</li>
					</ol>
				</li>
			</ul>
			<? } if( $useMissionNotes == "n" ) { ?>
			Your simm type is set to NOT use the Mission Notes system. Please make sure you make the following changes to your menus!
			<ul class="version">
				<li><b>Admin Menus</b>
					<ol>
						<li>We do NOT advise removing any of the admin menu items! The advisable way is to change each
						user's access levels to NOT include the mission notes item in both the Post menu as well as
						the Manage menu.</li>
					</ol>
				</li>
			</ul>
			<? } ?>
			</div>
		</div>
	</div>
	<? } ?>
	
	<div id="container-1">
		<ul>
			<li><a href="#one-a"><span>Main Navigation</span></a></li>
			<li><a href="#two-a"><span>General Menus</span></a></li>
			<li><a href="#three-a"><span>Admin Menus</span></a></li>
		</ul>
		
		<div id="one-a" class="ui-tabs-container ui-tabs-hide">
			The main navigation links are the links at the top of SMS that will take a user to the various sections of the site. By default, the only link that requires the user to be logged in is the Control Panel. In order to see changes to the main navigation menu, you may have to refresh the page after making changes.<br /><br />
			
			<table class="zebra" cellpadding="3" cellspacing="0">
				<thead>
					<tr class="fontMedium">
						<th width="30%">Title</th>
						<th width="50%">URL</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				
				<?php foreach($menus['main'] as $main_value) { ?>
				<tr class="fontNormal">
					<td width="30%">
						<?
						
						if( $main_value['display'] == "off" ) {
							echo "<strong class='red'>[ OFF ]</strong> &nbsp;&nbsp;";
						}
						
						printText($main_value['title']);
						
						?>
					</td>
					<td width="50%"><?=$main_value['link'];?></td>
					<td width="10%" align="center"><a href="#" class="delete" rel="facebox" myID="<?=$main_value['id'];?>" myAction="delete"><b>Delete</b></a></td>
					<td width="10%" align="center"><a href="#" class="edit" rel="facebox" myID="<?=$main_value['id'];?>" myAction="edit"><b>Edit</b></a></td>
				</tr>
				<?php } ?>
			</table>
		</div>
		
		<div id="two-a" class="ui-tabs-container ui-tabs-hide">
			Each major section of SMS has its own menu items. Use the sub navigation below to move through the various sections and make any changes you want.
			
			<div id="container-2">
				<ul>
					<li><a href="#one-b"><span>Main</span></a></li>
					<li><a href="#two-b"><span>Personnel</span></a></li>
					<li><a href="#three-b"><span>The Simm</span></a></li>
					<li><a href="#four-b"><span>The <?=ucfirst( $simmType );?></span></a></li>
				</ul>
				
				<div id="one-b" class="ui-tabs-container ui-tabs-hide">
					<table class="zebra" cellpadding="3" cellspacing="0">
						<thead>
							<tr class="fontMedium">
								<th width="30%">Title</th>
								<th width="50%">URL</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<?php foreach($menus['general']['main'] as $gen_main_v) { ?>
						<tr class="fontNormal">
							<td width="30%">
								<?
								
								if( $gen_main_v['display'] == "off" ) {
									echo "<strong class='red'>[ OFF ]</strong> &nbsp;&nbsp;";
								}
								
								printText($gen_main_v['title']);
								
								?>
							</td>
							<td width="50%"><?=$gen_main_v['link'];?></td>
							<td width="10%" align="center"><a href="#" class="delete" rel="facebox" myID="<?=$gen_main_v['id'];?>" myAction="delete"><b>Delete</b></a></td>
							<td width="10%" align="center"><a href="#" class="edit" rel="facebox" myID="<?=$gen_main_v['id'];?>" myAction="edit"><b>Edit</b></a></td>
						</tr>
						<?php } ?>
					</table>
				</div>
				
				<div id="two-b" class="ui-tabs-container ui-tabs-hide">
					<table class="zebra" cellpadding="3" cellspacing="0">
						<thead>
							<tr class="fontMedium">
								<th width="30%">Title</th>
								<th width="50%">URL</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<?php foreach($menus['general']['personnel'] as $gen_pers_v) { ?>
						<tr class="fontNormal">
							<td width="30%">
								<?
								
								if( $gen_pers_v['display'] == "off" ) {
									echo "<strong class='red'>[ OFF ]</strong> &nbsp;&nbsp;";
								}
								
								printText($gen_pers_v['title']);
								
								?>
							</td>
							<td width="50%"><?=$gen_pers_v['link'];?></td>
							<td width="10%" align="center"><a href="#" class="delete" rel="facebox" myID="<?=$gen_pers_v['id'];?>" myAction="delete"><b>Delete</b></a></td>
							<td width="10%" align="center"><a href="#" class="edit" rel="facebox" myID="<?=$gen_pers_v['id'];?>" myAction="edit"><b>Edit</b></a></td>
						</tr>
						<?php } ?>
					</table>
				</div>
				
				<div id="three-b" class="ui-tabs-container ui-tabs-hide">
					<table class="zebra" cellpadding="3" cellspacing="0">
						<thead>
							<tr class="fontMedium">
								<th width="30%">Title</th>
								<th width="50%">URL</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<?php foreach($menus['general']['simm'] as $gen_simm_v) { ?>
						<tr class="fontNormal">
							<td width="30%">
								<?
								
								if( $gen_simm_v['display'] == "off" ) {
									echo "<strong class='red'>[ OFF ]</strong> &nbsp;&nbsp;";
								}
								
								printText($gen_simm_v['title']);
								
								?>
							</td>
							<td width="50%"><?=$gen_simm_v['link'];?></td>
							<td width="10%" align="center"><a href="#" class="delete" rel="facebox" myID="<?=$gen_simm_v['id'];?>" myAction="delete"><b>Delete</b></a></td>
							<td width="10%" align="center"><a href="#" class="edit" rel="facebox" myID="<?=$gen_simm_v['id'];?>" myAction="edit"><b>Edit</b></a></td>
						</tr>
						<?php } ?>
					</table>
				</div>
				
				<div id="four-b" class="ui-tabs-container ui-tabs-hide">
					<table class="zebra" cellpadding="3" cellspacing="0">
						<thead>
							<tr class="fontMedium">
								<th width="30%">Title</th>
								<th width="50%">URL</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<?php foreach($menus['general']['ship'] as $gen_ship_v) { ?>
						<tr class="fontNormal">
							<td width="30%">
								<?
								
								if( $gen_ship_v['display'] == "off" ) {
									echo "<strong class='red'>[ OFF ]</strong> &nbsp;&nbsp;";
								}
								
								printText($gen_ship_v['title']);
								
								?>
							</td>
							<td width="50%"><?=$gen_ship_v['link'];?></td>
							<td width="10%" align="center"><a href="#" class="delete" rel="facebox" myID="<?=$gen_ship_v['id'];?>" myAction="delete"><b>Delete</b></a></td>
							<td width="10%" align="center"><a href="#" class="edit" rel="facebox" myID="<?=$gen_ship_v['id'];?>" myAction="edit"><b>Edit</b></a></td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
		
		<div id="three-a" class="ui-tabs-container ui-tabs-hide">
			The administration control panel of SMS is broken up in four distinct sections. Use the sub-navigation menu below to move between the sections and make any changes.
			
			<div id="container-3">
				<ul>
					<li><a href="#one-c"><span>Post</span></a></li>
					<li><a href="#two-c"><span>Manage</span></a></li>
					<li><a href="#three-c"><span>Reports</span></a></li>
					<li><a href="#four-c"><span>User</span></a></li>
				</ul>
				
				<div id="one-c" class="ui-tabs-container ui-tabs-hide">
					<table class="zebra" cellpadding="3" cellspacing="0">
						<thead>
							<tr class="fontMedium">
								<th width="30%">Title</th>
								<th width="50%">URL</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<?php foreach($menus['admin']['post'] as $admin_post_v) { ?>
						<tr class="fontNormal">
							<td width="30%">
								<?
								
								if( $admin_post_v['display'] == "off" ) {
									echo "<strong class='red'>[ OFF ]</strong> &nbsp;&nbsp;";
								}
								
								printText($admin_post_v['title']);
								
								?>
							</td>
							<td width="50%"><?=$admin_post_v['link'];?></td>
							<td width="10%" align="center"><a href="#" class="delete" rel="facebox" myID="<?=$admin_post_v['id'];?>" myAction="delete"><b>Delete</b></a></td>
							<td width="10%" align="center"><a href="#" class="edit" rel="facebox" myID="<?=$admin_post_v['id'];?>" myAction="edit"><b>Edit</b></a></td>
						</tr>
						<?php } ?>
					</table>
				</div>
				
				<div id="two-c" class="ui-tabs-container ui-tabs-hide">
					<b class='yellow'>There are 2 All NPC links to account for the various access levels associated with that feature. Do not delete either of those links or the feature, at certain access levels, will cease to function correctly!</b><br /><br />
					
					<table class="zebra" cellpadding="3" cellspacing="0">
						<thead>
							<tr class="fontMedium">
								<th width="30%">Title</th>
								<th width="50%">URL</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<?php foreach($menus['admin']['manage'] as $admin_manage_v) { ?>
						<tr class="fontNormal">
							<td width="30%">
								<?
								
								if( $admin_manage_v['display'] == "off" ) {
									echo "<strong class='red'>[ OFF ]</strong> &nbsp;&nbsp;";
								}
								
								printText($admin_manage_v['title']);
								
								?>
							</td>
							<td width="50%"><?=$admin_manage_v['link'];?></td>
							<td width="10%" align="center"><a href="#" class="delete" rel="facebox" myID="<?=$admin_manage_v['id'];?>" myAction="delete"><b>Delete</b></a></td>
							<td width="10%" align="center"><a href="#" class="edit" rel="facebox" myID="<?=$admin_manage_v['id'];?>" myAction="edit"><b>Edit</b></a></td>
						</tr>
						<?php } ?>
					</table>
				</div>
				
				<div id="three-c" class="ui-tabs-container ui-tabs-hide">
					<table class="zebra" cellpadding="3" cellspacing="0">
						<thead>
							<tr class="fontMedium">
								<th width="30%">Title</th>
								<th width="50%">URL</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<?php foreach($menus['admin']['reports'] as $admin_reports_v) { ?>
						<tr class="fontNormal">
							<td width="30%">
								<?
								
								if( $admin_reports_v['display'] == "off" ) {
									echo "<strong class='red'>[ OFF ]</strong> &nbsp;&nbsp;";
								}
								
								printText($admin_reports_v['title']);
								
								?>
							</td>
							<td width="50%"><?=$admin_reports_v['link'];?></td>
							<td width="10%" align="center"><a href="#" class="delete" rel="facebox" myID="<?=$admin_reports_v['id'];?>" myAction="delete"><b>Delete</b></a></td>
							<td width="10%" align="center"><a href="#" class="edit" rel="facebox" myID="<?=$admin_reports_v['id'];?>" myAction="edit"><b>Edit</b></a></td>
						</tr>
						<?php } ?>
					</table>
				</div>
				
				<div id="four-c" class="ui-tabs-container ui-tabs-hide">
					<b class='yellow'>There are 2 user account links and 3 biography links to account for the various access levels associated with those features. Do not delete any of those links or those features, at certain access levels, will cease to function correctly!</b><br /><br />
					
					<table class="zebra" cellpadding="3" cellspacing="0">
						<thead>
							<tr class="fontMedium">
								<th width="30%">Title</th>
								<th width="50%">URL</th>
								<th></th>
								<th></th>
							</tr>
						</thead>

						<?php foreach($menus['admin']['user'] as $admin_user_v) { ?>
						<tr class="fontNormal">
							<td width="30%">
								<?
								
								if( $admin_user_v['display'] == "off" ) {
									echo "<strong class='red'>[ OFF ]</strong> &nbsp;&nbsp;";
								}
								
								printText($admin_user_v['title']);
								
								?>
							</td>
							<td width="50%"><?=$admin_user_v['link'];?></td>
							<td width="10%" align="center"><a href="#" class="delete" rel="facebox" myID="<?=$admin_user_v['id'];?>" myAction="delete"><b>Delete</b></a></td>
							<td width="10%" align="center"><a href="#" class="edit" rel="facebox" myID="<?=$admin_user_v['id'];?>" myAction="edit"><b>Edit</b></a></td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	</div>

<? } else { errorMessage( "menu management" ); } ?>