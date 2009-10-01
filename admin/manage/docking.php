<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/docking.php
Purpose: Page to manage the docked ships at a starbase

System Version: 2.6.0
Last Modified: 2008-04-18 1959 EST
**/

/* access check */
if(in_array("m_docking", $sessionAccess))
{
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	$action_type = FALSE;
	$tab = 1;
	
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
			case 'edit':
				
				$update = "UPDATE sms_starbase_docking SET dockingShipName = %s, dockingShipRegistry = %s, dockingShipClass = %s, ";
				$update.= "dockingShipURL = %s, dockingShipCO = %s, dockingShipCOEmail = %s, dockingDuration = %s, dockingDesc = %s, ";
				$update.= "dockingStatus = %s WHERE dockid = $action_id LIMIT 1";
				
				$query = sprintf(
					$update,
					escape_string($_POST['dockingShipName']),
					escape_string($_POST['dockingShipRegistry']),
					escape_string($_POST['dockingShipClass']),
					escape_string($_POST['dockingShipURL']),
					escape_string($_POST['dockingShipCO']),
					escape_string($_POST['dockingShipCOEmail']),
					escape_string($_POST['dockingDuration']),
					escape_string($_POST['dockingDesc']),
					escape_string($_POST['dockingStatus'])
				);
				
				$result = mysql_query($query);
				
				$action = "update";
				
				if(isset($_POST['action_tab']) && is_numeric($_POST['action_tab']))
				{
					$tab = $_POST['action_tab'];
				}
				
				break;
			case 'delete':
				
				$query = "DELETE FROM sms_starbase_docking WHERE dockid = $action_id LIMIT 1";
				$result = mysql_query($query);
				
				$action = "delete";
				
				if(isset($_POST['action_tab']) && is_numeric($_POST['action_tab']))
				{
					$tab = $_POST['action_tab'];
				}
				
				break;
		}
		
		/* optimize the table */
		optimizeSQLTable("sms_starbase_docking");
	}
	
	$ships = array(
		'activated' => array(),
		'departed' => array()
	);
	
	$getShips = "SELECT * FROM sms_starbase_docking WHERE dockingStatus != 'pending'";
	$getShipsR = mysql_query($getShips);
	
	while($shipFetch = mysql_fetch_assoc($getShipsR)) {
		extract($shipFetch, EXTR_OVERWRITE);
		
		$ships[$dockingStatus][] = array(
			'id' => $dockid,
			'name' => $dockingShipName,
			'registry' => $dockingShipRegistry,
			'class' => $dockingShipClass,
			'co' => $dockingShipCO,
			'co_email' => $dockingShipCOEmail
		);
	}

?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#container-1 > ul').tabs(<?php echo $tab;?>);
			$('.zebra tr:nth-child(even)').addClass('alt');
		
			$("a[rel*=facebox]").click(function() {
				var action = $(this).attr("myAction");
				var id = $(this).attr("myID");
			
				jQuery.facebox(function() {
					jQuery.get('admin/ajax/docking_' + action + '.php?id=' + id, function(data) {
						jQuery.facebox(data);
					});
				});
				return false;
			});
		});
	</script>

	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery($result,$query);
				
		if(!empty($check->query)) {
			$check->message("ship", $action);
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Docked Ship Management</span><br /><br />
		From here you can edit any ships that are currently docked or have previously docked with your starbase. You cannot edit or activate pending docking requests from this page, you must use the <a href="<?=$webLocation;?>admin.php?page=manage&sub=activate">activation page</a> to do so.<br /><br />
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Docked Ships (<?php echo count($ships['activated']);?>)</span></a></li>
				<li><a href="#two"><span>Departed Ships (<?php echo count($ships['departed']);?>)</span></a></li>
			</ul>

			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				if(count($ships['activated']) == 0)
				{
					echo "<strong class='orange fontMedium'>No docked ships found</strong>";
				}
				else
				{
				
				?>
				
				<table class="zebra" cellpadding="3" cellspacing="0">
					<tr class="fontMedium">
						<th>Ship Name</th>
						<th>Ship CO</th>
						<th width="10%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
					<?php foreach($ships['activated'] as $k1 => $v1) { ?>
					<tr height="40">
						<td>
							<?php printText($v1['name'] . " " . $v1['registry']);?><br />
							<span class="fontSmall">Class: <?php printText($v1['class']);?></span>
						</td>
						<td>
							<?php printText($v1['co']);?><br />
							<span class="fontSmall"><?php printText($v1['co_email']);?></span>
						</td>
						<td align="center">
							<a href="<?=$webLocation;?>index.php?page=dockedships&ship=<?=$v1['id'];?>"><strong>View</strong></a>
						</td>
						<td align="center">
							<a href="#" rel="facebox" myAction="delete" myID="<?=$v1['id'];?>" class="delete"><strong>Delete</strong></a></td>
						<td align="center">
							<a href="#" rel="facebox" myAction="edit" myID="<?=$v1['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>	
					<?php } ?>
				</table>
					
				<?php } ?>
			</div>
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				if(count($ships['departed']) == 0)
				{
					echo "<strong class='orange fontMedium'>No departed docked ships found</strong>";
				}
				else
				{
				
				?>
				
				<table class="zebra" cellpadding="3" cellspacing="0">
					<tr class="fontMedium">
						<th>Ship Name</th>
						<th>Ship CO</th>
						<th width="10%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
					<?php foreach($ships['departed'] as $k2 => $v2) { ?>
					<tr height="40">
						<td>
							<?php printText($v2['name'] . " " . $v2['registry']);?><br />
							<span class="fontSmall">Class: <?php printText($v2['class']);?></span>
						</td>
						<td>
							<?php printText($v2['co']);?><br />
							<span class="fontSmall"><?php printText($v2['co_email']);?></span>
						</td>
						<td align="center">
							<a href="<?=$webLocation;?>index.php?page=dockedships&ship=<?=$v2['id'];?>"><strong>View</strong></a>
						</td>
						<td align="center">
							<a href="#" rel="facebox" myAction="delete" myID="<?=$v2['id'];?>" class="delete"><strong>Delete</strong></a></td>
						<td align="center">
							<a href="#" rel="facebox" myAction="edit" myID="<?=$v2['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>	
					<?php } ?>
				</table>
					
				<?php } ?>
			</div>
		</div>
		
	</div>
	
<? } else { errorMessage( "docked ship management" ); } ?>