<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/npcs.php
Purpose: Page to manage the NPCs on the simm

System Version: 2.6.0
Last Modified: 2008-04-23 1811 EST
**/

/* access check */
if(in_array("m_npcs1", $sessionAccess) || in_array("m_npcs2", $sessionAccess))
{
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	$action_type = FALSE;
	
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
		
		if($action_type == 'activate' && in_array("m_npcs2", $sessionAccess))
		{
			if(isset($_POST['position1']) && is_numeric($_POST['position1'])) {
				$position1 = $_POST['position1'];
			} else {
				$position1 = NULL;
			}
			
			if(isset($_POST['position2']) && is_numeric($_POST['position2'])) {
				$position2 = $_POST['position2'];
			} else {
				$position2 = NULL;
			}
			
			update_position($position1, 'give');

			if(!empty($position2))
			{
				update_position($position2, 'give');
			}
			
			/* pull the default access levels from the db */
			$getGroupLevels = "SELECT * FROM sms_accesslevels WHERE id = 4 LIMIT 1";
			$getGroupLevelsResult = mysql_query( $getGroupLevels );
			$groups = mysql_fetch_array( $getGroupLevelsResult );
			
			$update = "UPDATE sms_crew SET crewType = %s, accessPost = %s, accessManage = %s, accessReports = %s, ";
			$update.= "accessUser = %s, accessOthers = %s, username = %s, password = %s, email = %s WHERE crewid = $action_id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string('active'),
				escape_string($groups[1]),
				escape_string($groups[2]),
				escape_string($groups[3]),
				escape_string($groups[4]),
				escape_string($groups[5]),
				escape_string($_POST['username']),
				escape_string(md5($_POST['password'])),
				escape_string($_POST['email'])
			);

			$result = mysql_query($query);

			/* optimize the table */
			optimizeSQLTable( "sms_crew" );
			optimizeSQLTable( "sms_positions" );
		}
		if($action_type == 'delete')
		{
			$query = "DELETE FROM sms_crew WHERE crewid = $action_id LIMIT 1";
			$result = mysql_query($query);
			
			/* optimize the table */
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
	
?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("a[rel*=facebox]").click(function() {
				var action = $(this).attr("myAction");
				var id = $(this).attr("myID");

				jQuery.facebox(function() {
					jQuery.get('admin/ajax/npc_' + action + '.php?id=' + id, function(data) {
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
		$check->checkQuery($result, $query);
		
		if(!empty($check->query))
		{
			$check->message("non-playing character", $action_type);
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Non-Playing Characters</span>
		<p>From this page, you can select any of the NPCs that exist in your own department. You can edit their bios, promote (or demote) them to another position or rank (below your own). If you want to move an NPC from your own department to another department, please contact the CO or XO. In addition, you can also add your own NPCs for your department.
		
		<? if( in_array( "m_npcs2", $sessionAccess ) ) { ?>
			If you would like to make an NPC a playing character, simply activate them.  You will then be able to edit their account.
		<? } ?><br /><br />
		
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=add" class="add fontLarge"><strong>Add Non-Playing Character &raquo;</strong></a>
		</p>
		
		<table cellpadding="3" cellspacing="0">
		
		<?php
	
		if( in_array( "m_npcs2", $sessionAccess ) ) {
			
			$departments = "SELECT * FROM sms_departments WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
			$deptResults = mysql_query( $departments );
			
			/* pull the data out of the department query */
			while ( $dept = mysql_fetch_array( $deptResults ) ) {
				extract( $dept, EXTR_OVERWRITE );
					
		?>
				
			<tr>
				<td colspan="5" height="5"></td>
			</tr>
			<tr>
				<td colspan="5">
					<font color="#<?=$deptColor;?>">
						<b><? printText( $deptName ); ?></b>
					</font>
				</td>
			</tr>
				
			<?php
			
			$npcs = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.rankid, crew.positionid FROM sms_crew AS crew, ";
			$npcs.= "sms_positions AS position, sms_departments AS dept WHERE crew.crewType = 'npc' AND crew.positionid = position.positionid ";
			$npcs.= "AND position.positionDept = dept.deptid AND dept.deptid = '$dept[0]' ORDER BY crew.positionid, crew.rankid ASC";
			$npcsResult = mysql_query($npcs);
			$npcCount = mysql_num_rows($npcsResult);
			
			$rowCount = 0;
			$color1 = "rowColor2";
			$color2 = "rowColor1";
				
			while($npc = mysql_fetch_assoc($npcsResult)) {
				extract($npc, EXTR_OVERWRITE);
						
				$rowColor = ($rowCount % 2) ? $color1 : $color2;
			
			?>
				
			<tr class="fontNormal <?=$rowColor;?>" height="25">
				<td width="40%">
					<strong><? printCrewName($npc['crewid'], 'rank', 'noLink');?></strong>
				</td>
				<td width="30%">
				<?php
				
				$key1 = array_key_exists($npc['positionid'], $pos_array);
				
				if($key1 !== FALSE)
				{
					echo "<span style='color: #" . $pos_array[$npc['positionid']][1] . ";'>";
					printText($pos_array[$npc['positionid']][0]);
					echo "</span>";
				}
				else
				{
					echo "<strong class='red'>[ Invalid Position ]</strong>";
				}
				
				?>
				
				<td width="10%" align="center"><a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$npc['crewid'];?>" class="edit"><strong>Edit</strong></a></td>
				<td width="10%" align="center"><a href="#" rel="facebox" myAction="activate" myID="<?=$npc['crewid'];?>" class="add"><strong>Activate</strong></a></td>
				<td width="10%" align="center">
					<a href="#" rel="facebox" myAction="delete" myID="<?=$npc['crewid'];?>" class="delete"><strong>Delete</strong></a>
				</td>
			</tr>
			
			<?php
		
				$rowCount++;
				
			}
			
			} /* close the NPC position loop */
		
		} /* close the access check */
		if( in_array( "m_npcs1", $sessionAccess ) && !in_array( "m_npcs2", $sessionAccess ) )
		{  
			$userDeptQuery = "SELECT crew.positionid, position.positionDept ";
			$userDeptQuery.= "FROM sms_crew AS crew, sms_positions AS position ";
			$userDeptQuery.= "WHERE crew.crewid = '$sessionCrewid' AND ";
			$userDeptQuery.= "crew.positionid = position.positionid LIMIT 1";
			$userDeptResult = mysql_query( $userDeptQuery );
			$userDept = mysql_fetch_row( $userDeptResult );
							
			$npcs = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.rankid, ";
			$npcs.= "crew.positionid FROM sms_crew AS crew, sms_positions AS position, ";
			$npcs.= "sms_departments AS dept WHERE crew.crewType = 'npc' AND ";
			$npcs.= "position.positionDept = dept.deptid AND ";
			$npcs.= "crew.positionid = position.positionid AND position.positionDept = '$userDept[1]'";
			$npcsResult = mysql_query( $npcs );
			$npcCount = mysql_num_rows( $npcsResult );
											
		}
		
		$rowCount = 0;
		$color1 = "rowColor2";
		$color2 = "rowColor1";
		
		/* make sure that a nasty SQL error doesn't get thrown back if there aren't any results */
		if( $npcCount == 0 && in_array( "m_npcs1", $sessionAccess ) ) {
		
			echo "<tr class='fontNormal'>";
				echo "<td colspan='4'>";
					echo "<b>There are no NPCs to moderate in this department! You can create one by using the link above.</b>";
				echo "</td>";
			echo "</tr>";
			
		} else {
		
		while( $npc = mysql_fetch_assoc( $npcsResult ) ) {
			extract( $npc, EXTR_OVERWRITE );
			
			$rowColor = ($rowCount % 2) ? $color1 : $color2;
			
		?>
		
		<tr class="fontNormal <?=$rowColor;?>" height="25">
			<td width="40%">
				<strong><? printCrewName($npc['crewid'], 'rank', 'noLink');?></strong>
			</td>
			<td width="40%">
			<?php
			
			$key1 = array_key_exists($npc['positionid'], $pos_array);
			
			if($key1 !== FALSE)
			{
				echo "<span style='color: #" . $pos_array[$npc['positionid']][1] . ";'>";
				printText($pos_array[$npc['positionid']][0]);
				echo "</span>";
			}
			else
			{
				echo "<strong class='red'>[ Invalid Position ]</strong>";
			}
			
			?>
			</td>
			
			<td width="10%" align="center"><a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$npc['crewid'];?>" class="edit"><strong>Edit</strong></a></td>
			<td width="10%" align="center">
				<a href="#" rel="facebox" myAction="delete" myID="<?=$npc['crewid'];?>" class="delete"><strong>Delete</strong></a>
			</td>
		</tr>
		
		<? $rowCount++; } } ?>
		
		</table>
		
	</div>
	
<? } else { errorMessage( "NPC management" ); } ?>