<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/crew.php
Purpose: Page to display the active, inactive, and pending crew on the sim

System Version: 2.6.0
Last Modified: 2008-04-23 1802 EST
**/

/* access check */
if( in_array( "m_crew", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	$action_type = FALSE;
	
	if(isset($_GET['id']) && is_numeric($_GET['id'])) {
		$actionid = $_GET['id'];
	} else {
		$actionid = NULL;
	}
	
	if(isset($_GET['action'])) {
		$action = $_GET['action'];
	}
	
	if(isset($_POST['action_tab']) && is_numeric($_POST['action_tab'])) {
		$tab = $_POST['action_tab'];
	} else {
		$tab = 1;
	}
	
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
		
		if($action_type == 'deactivate')
		{
			$levelsPost = "";
			$levelsManage = "";
			$levelsReports = "";
			$levelsUser = "";
			$levelsOther = "";
			$today = getdate();
			
			$update = "UPDATE sms_crew SET crewType = %s, accessPost = %s, accessManage = %s, accessReports = %s, ";
			$update.= "accessUser = %s, accessOthers = %s, leaveDate = %d WHERE crewid = $action_id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string($_POST['type']),
				escape_string($levelsPost),
				escape_string($levelsManage),
				escape_string($levelsReports),
				escape_string($levelsUser),
				escape_string($levelsOther),
				escape_string($today[0])
			);

			$result = mysql_query($query);

			/* optimize the table */
			optimizeSQLTable( "sms_crew" );

			/* set the action */
			$action = $action_type;
			
			/* get the user's old position */
			$getPos = "SELECT positionid, positionid2 FROM sms_crew WHERE crewid = $action_id LIMIT 1";
			$getPosResult = mysql_query($getPos);
			$oldPosition = mysql_fetch_array($getPosResult);
			
			/* update the position they're being given */
			update_position($oldPosition[0], 'take');
			
			if(!empty($oldPosition[1]))
			{
				update_position($oldPosition[1], 'take');
			}
			
			/* optimize the table */
			optimizeSQLTable( "sms_positions" );
		}
		if($action_type == 'activate')
		{
			/* pull the default access levels from the db */
			$getGroupLevels = "SELECT * FROM sms_accesslevels WHERE id = 4 LIMIT 1";
			$getGroupLevelsResult = mysql_query( $getGroupLevels );
			$groups = mysql_fetch_array( $getGroupLevelsResult );
			
			$update = "UPDATE sms_crew SET crewType = %s, accessPost = %s, accessManage = %s, accessReports = %s, ";
			$update.= "accessUser = %s, accessOthers = %s WHERE crewid = $action_id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string('active'),
				escape_string($groups[1]),
				escape_string($groups[2]),
				escape_string($groups[3]),
				escape_string($groups[4]),
				escape_string($groups[5])
			);

			$result = mysql_query($query);

			/* optimize the table */
			optimizeSQLTable( "sms_crew" );

			/* set the action */
			$action = $action_type;
			
			/* get the user's old position */
			$getPos = "SELECT positionid, positionid2 FROM sms_crew WHERE crewid = $action_id LIMIT 1";
			$getPosResult = mysql_query($getPos);
			$oldPosition = mysql_fetch_array($getPosResult);
			
			/* update the position they're being given */
			update_position($oldPosition[0], 'give');
			
			if(!empty($oldPosition[1]))
			{
				update_position($oldPosition[1], 'give');
			}
			
			/* optimize the table */
			optimizeSQLTable( "sms_positions" );
		}
		if($action_type == 'delete')
		{
			$get = "SELECT * FROM sms_crew WHERE crewid = $action_id LIMIT 1";
			$getR = mysql_query($get);
			$fetch = mysql_fetch_assoc($getR);
			
			/* if the crew member is active, make sure to adjust the available positions */
			if($fetch['crewType'] == 'active')
			{
				update_position($fetch['positionid'], 'take');

				if(!empty($fetch['positionid2']))
				{
					update_position($fetch['positionid2'], 'take');
				}
			}
			
			$query = "DELETE FROM sms_crew WHERE crewid = $action_id LIMIT 1";
			$result = mysql_query($query);
			
			$action = $action_type;
			
			/* optimize the table */
			optimizeSQLTable( "sms_positions" );
			optimizeSQLTable( "sms_crew" );
		}
	}

	/* build an array of all the positions to check for invalid ones */
	$posArray = "SELECT p.positionid, p.positionName, d.deptColor FROM sms_positions AS p, sms_departments AS d ";
	$posArray.= "WHERE p.positionDept = d.deptid ORDER BY p.positionid ASC";
	$posArrayResult = mysql_query( $posArray );
	$pos_array = array();

	while($myrow = mysql_fetch_array($posArrayResult)) {
		$pos_array[$myrow[0]] = array($myrow[1], $myrow[2]);
	}
	
	/* build an array with all the crew in it */
	$crew = array(
		'pending' => array(),
		'active' => array(),
		'inactive' => array()
	);
	
	$get = "SELECT crewid, crewType, rankid, positionid, positionid2 FROM sms_crew ";
	$get.= "WHERE crewType != 'npc' ORDER BY positionid, rankid ASC";
	$getR = mysql_query($get);
	
	while($fetch = mysql_fetch_array($getR)) {
		extract($fetch, EXTR_OVERWRITE);
		
		$crew[$fetch[1]][] = array(
			'id' => $fetch[0],
			'rank' => $fetch[2],
			'position1' => $fetch[3],
			'position2' => $fetch[4]
		);
	}
	
?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#container-1 > ul').tabs(<?=$tab;?>);
			$('.zebra tr:nth-child(odd)').addClass('alt');
			
			$("a[rel*=facebox]").click(function() {
				var action = $(this).attr("myAction");
				var id = $(this).attr("myID");
				var tab = $(this).attr("myTab");

				jQuery.facebox(function() {
					jQuery.get('admin/ajax/crew_' + action + '.php?id=' + id + '&t=' + tab, function(data) {
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
		$check->checkQuery( $result, $query );
				
		if(!empty($check->query))
		{
			$check->message("player", $action);
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Playing Characters</span>
		<p>From this page, you can select any of the playing characters that exist . You can edit their bios, promote (or demote) them to another position or rank. Additionally, if need be, you can deactivate the character if the player has retired or been removed.  By deactivating a character, you will be given a choice of whether they should be sent to the Departed Crew Manifest or made an NPC.  <strong class="yellow">Please note</strong> that pending characters cannot be accepted from this page, you must use the <a href="<?=$webLocation;?>admin.php?page=manage&sub=activate">activation page</a>.</p>
		
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=add" class="add fontMedium"><strong>Add a Character &raquo;</strong></a>
		<br /><br />
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Active Crew (<?php echo count($crew['active']);?>)</span></a></li>
				<li><a href="#two"><span>Inactive Crew (<?php echo count($crew['inactive']);?>)</span></a></li>
				<li><a href="#three"><span>Pending Crew (<?php echo count($crew['pending']);?>)</span></a></li>
			</ul>
			
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<table class="zebra" cellpadding="3" cellspacing="0">
					<?php foreach($crew['active'] as $key_a => $value_a) { ?>

					<tr height="40">
						<td width="50%">
							<b><? printCrewName($value_a['id'], 'rank', 'noLink');?></b><br />
							<?php

							$key1 = array_key_exists($value_a['position1'], $pos_array);

							if(!empty($value_a['position2']))
							{
								$key2 = array_key_exists($value_a['position2'], $pos_array);
							}

							/* check to see if the first position is legit */
							if($key1 !== FALSE)
							{
								echo "<span class='fontNormal' style='color: #" . $pos_array[$value_a['position1']][1] . ";'>";
								printText($pos_array[$value_a['position1']][0]);
								echo "</span>";
							}
							else
							{
								echo "<strong class='fontNormal red'>[ Invalid Position ]</strong>";
							}

							/* check to see if the second position is legit */
							if(!empty($value_a['position2']))
							{
								if($key2 !== FALSE)
								{
									echo "<span class='fontNormal'> &amp; </span>";
									echo "<span class='fontNormal' style='color: #" . $pos_array[$value_a['position2']][1] . ";'>";
									printText($pos_array[$value_a['position2']][0]);
									echo "</span>";
								}
								else
								{
									echo "<strong class='fontNormal red'>[ Invalid Position ]</strong>";
								}
							}

							?>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$value_a['id'];?>" class="edit"><b>Edit Bio</b></a>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="<?=$webLocation;?>admin.php?page=user&sub=account&crew=<?=$value_a['id'];?>" class="edit"><b>Edit Account</b></a>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="<?=$webLocation;?>admin.php?page=user&sub=stats&crew=<?=$value_a['id'];?>"><strong>Stats</strong></a> &middot;
							<a href="<?=$webLocation;?>admin.php?page=user&sub=access&crew=<?=$value_a['id'];?>"><strong>Access</strong></a>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="#" rel="facebox" myAction="deactivate" myID="<?=$value_a['id'];?>" myTab="1" class="delete"><b>Deactivate</b></a>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="#" rel="facebox" myAction="delete" myID="<?=$value_a['id'];?>" myTab="1" class="delete"><b>Delete</b></a>
						</td>
					</tr>

					<?php } ?>
				</table>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				if(count($crew['inactive']) == 0)
				{
					echo "<strong class='orange fontLarge'>No inactive characters found</strong>";
				}
				else
				{
				
				?>
				<table class="zebra" cellpadding="3" cellspacing="0">
					<?php foreach($crew['inactive'] as $key_i => $value_i) { ?>

					<tr height="40">
						<td width="50%">
							<b><? printCrewName($value_i['id'], 'rank', 'noLink');?></b><br />
							<?php

							$key1 = array_key_exists($value_i['position1'], $pos_array);

							if(!empty($value_i['position2']))
							{
								$key2 = array_key_exists($value_i['position2'], $pos_array);
							}

							/* check to see if the first position is legit */
							if($key1 !== FALSE)
							{
								echo "<span class='fontNormal' style='color: #" . $pos_array[$value_i['position1']][1] . ";'>";
								printText($pos_array[$value_i['position1']][0]);
								echo "</span>";
							}
							else
							{
								echo "<strong class='fontNormal red'>[ Invalid Position ]</strong>";
							}

							/* check to see if the second position is legit */
							if(!empty($value_i['position2']))
							{
								if($key2 !== FALSE)
								{
									echo "<span class='fontNormal'> &amp; </span>";
									echo "<span class='fontNormal' style='color: #" . $pos_array[$value_i['position2']][1] . ";'>";
									printText($pos_array[$value_i['position2']][0]);
									echo "</span>";
								}
								else
								{
									echo "<strong class='fontNormal red'>[ Invalid Position ]</strong>";
								}
							}

							?>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$value_i['id'];?>" class="edit"><b>Edit Bio</b></a>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="<?=$webLocation;?>admin.php?page=user&sub=account&crew=<?=$value_i['id'];?>" class="edit"><b>Edit Account</b></a>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="<?=$webLocation;?>admin.php?page=user&sub=stats&crew=<?=$value_i['id'];?>"><strong>Stats</strong></a> &middot;
							<a href="<?=$webLocation;?>admin.php?page=user&sub=access&crew=<?=$value_i['id'];?>"><strong>Access</strong></a>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="#" rel="facebox" myAction="activate" myID="<?=$value_i['id'];?>" myTab="2" class="add"><b>Activate</b></a>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="#" rel="facebox" myAction="delete" myID="<?=$value_i['id'];?>" myTab="2" class="delete"><b>Delete</b></a>
						</td>
					</tr>

					<?php } ?>
				</table>
				<?php } ?>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				if(count($crew['pending']) == 0)
				{
					echo "<strong class='orange fontLarge'>No pending characters found</strong>";
				}
				else
				{
				
				?>
				<table class="zebra" cellpadding="3" cellspacing="0">
					<?php foreach($crew['pending'] as $key_p => $value_p) { ?>

					<tr height="40">
						<td width="60%">
							<b><? printCrewName($value_p['id'], 'noRank', 'noLink', 'pending');?></b><br />
							<span class="fontNormal">Unassigned</span>
						</td>
						<td width="10%"></td>
						<td width="10%" align="center" class="fontNormal">
							<a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$value_p['id'];?>" class="edit"><b>Edit Bio</b></a>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="<?=$webLocation;?>admin.php?page=user&sub=account&crew=<?=$value_p['id'];?>" class="edit"><b>Edit Account</b></a>
						</td>
						<td width="10%" align="center" class="fontNormal">
							<a href="#" rel="facebox" myAction="delete" myID="<?=$value_p['id'];?>" myTab="3" class="delete"><b>Delete</b></a>
						</td>
					</tr>

					<?php } ?>
				</table>
				<?php } ?>
			</div>
		</div>
		
	</div>
	
<?php } else { errorMessage( "crew management" ); } ?>