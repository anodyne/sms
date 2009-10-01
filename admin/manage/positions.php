<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/positions.php
Purpose: Page that moderates the positions

System Version: 2.6.0
Last Modified: 2008-04-18 1437 EST
**/

/* access check */
if(in_array("m_positions", $sessionAccess))
{
	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	$positionid = FALSE;
	
	if(isset($_GET['dept']) && is_numeric($_GET['dept']))
	{
		$dept = $_GET['dept'];
	}
	else
	{
		$dept = 1;
	}
	
	if(isset($_POST['action_update_x']))
	{
		if(isset($_POST['positionid']) && is_numeric($_POST['positionid']))
		{
			$positionid = $_POST['positionid'];
		}
		
		$update = "UPDATE sms_positions SET positionName = %s, positionDept = %d, positionOrder = %d, positionDesc = %s, ";
		$update.= "positionOpen = %d, positionType = %s, positionDisplay = %s WHERE positionid = $positionid LIMIT 1";
		
		$query = sprintf(
			$update,
			escape_string($_POST['positionName']),
			escape_string($_POST['positionDept']),
			escape_string($_POST['positionOrder']),
			escape_string($_POST['positionDesc']),
			escape_string($_POST['positionOpen']),
			escape_string($_POST['positionType']),
			escape_string($_POST['positionDisplay'])
		);
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_positions" );
		
		$action = "update";
	}
	elseif(isset($_POST['action_type']) && $_POST['action_type'] == "create")
	{
		$insert = "INSERT INTO sms_positions (positionName, positionDept, positionOrder, positionDesc, positionOpen, ";
		$insert.= "positionType, positionDisplay) VALUES (%s, %d, %d, %s, %d, %s, %s)";
		
		$query = sprintf(
			$insert,
			escape_string($_POST['positionName']),
			escape_string($_POST['positionDept']),
			escape_string($_POST['positionOrder']),
			escape_string($_POST['positionDesc']),
			escape_string($_POST['positionOpen']),
			escape_string($_POST['positionType']),
			escape_string($_POST['positionDisplay'])
		);
		
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_positions" );
		
		$action = "create";
	}
	elseif(isset($_POST['action_delete_x']))
	{
		if(isset($_POST['positionid']) && is_numeric($_POST['positionid']))
		{
			$positionid = $_POST['positionid'];
		}
		
		$query = "DELETE FROM sms_positions WHERE positionid = $positionid LIMIT 1";
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_positions" );
		
		$action = "delete";
	}
	
	/* grab the departments for the menu */
	$getDepts = "SELECT deptid, deptName FROM sms_departments ORDER BY deptOrder ASC";
	$getDeptsResult = mysql_query( $getDepts );
	
	/* count the departments */
	$countDepts = mysql_num_rows( $getDeptsResult );
	$countDeptsFinal = $countDepts - 1;
	
?>
<script type="text/javascript">
	$(document).ready(function() {
		$("a[rel*=facebox]").click(function() {
			var action = $(this).attr("myAction");
			
			jQuery.facebox(function() {
				jQuery.get('admin/ajax/position_' + action + '.php', function(data) {
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
				
		if( !empty( $check->query ) ) {
			$check->message( "position", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Position Management</span><br /><br />
		Use the fields below to manage the positions used by SMS. You can create new positions and tie them to a department by using the link below to add a new position.
		
		<? if( $dept == 1 ) { ?>
		<strong class="yellow">Please Note:</strong> Several functions that control emails in SMS use position ids 1 and 2 for the Commanding Officer and Executive Officer respectively. Because of this, you cannot delete the first two positions. If you change either of these positions to have different id numbers, some of the email functions will not work properly.
		<? } ?><br /><br />
		
		<a href="#" rel="facebox" myAction="add" class="add fontMedium"><strong>Add New Position &raquo;</strong></a><br /><br />
		
		<div class="update notify-normal fontNormal">
			<strong class="orange">Click on the department name to view and edit the positions</strong><br /><br />
			<?
			
			/* loop through the departments */
			while( $deptFetch = mysql_fetch_array( $getDeptsResult ) ) {
				extract( $deptFetch, EXTR_OVERWRITE );
				
				/*
					create a multi-dimensional array of the data
					
					[x] => Array
					[x][deptid] => 1
					[x][deptName] => Command
				*/
				$depts[] = array( "deptid" => $deptFetch[0], "deptName" => $deptFetch[1] );
				
			}
			
			/* loop through the array */
			foreach( $depts as $key => $value ) {
			
				echo "<a href='" . $webLocation . "admin.php?page=manage&sub=positions&dept=" . $value['deptid'] . "'>";
				
				/*
					if it's the last element of the array, just close the HREF
					otherwise, put a middot between the array values
				*/
				if( $key >= $countDeptsFinal ) {
					echo $value['deptName'] . "</a>";
				} else {
					echo $value['deptName'] . "</a> &nbsp; &middot; &nbsp; ";
				}
			
			}
			
			?><br /><br />
		</div><br />
	
		<?php
		
		$fetchName = "SELECT deptName FROM sms_departments WHERE deptid = $dept LIMIT 1";
		$fetchResult = mysql_query( $fetchName );
		$department = mysql_fetch_assoc( $fetchResult );
		
		?>
		
		<span class="fontTitle"><?=$department['deptName'];?> Positions</span><br /><br />
	
		<table cellpadding="0" cellspacing="3">
			<?
			
			$getPositions = "SELECT * FROM sms_positions WHERE positionDept = '$dept' ORDER BY positionOrder ASC";
			$getPositionsResult = mysql_query( $getPositions );
		
			while( $positionFetch = mysql_fetch_assoc( $getPositionsResult ) ) {
				extract( $positionFetch, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=positions&dept=<?=$dept;?>">
			<tr>
				<td colspan="3">
					<span class="fontNormal"><b>Position</b></span><br />
					<input type="text" class="name" name="positionName" value="<?=print_input_text( $positionName );?>" />
				</td>
				<td width="5" rowspan="3" align="center" valign="top">&nbsp;</td>
				<td width="80%" rowspan="3" align="center" valign="top">
					<span class="fontNormal"><b>Description</b></span><br />
					<textarea name="positionDesc" rows="5" class="desc"><?=stripslashes( $positionDesc );?></textarea>
					<br />
					
					<? if( $positionid == "1" || $positionid == "2" ) { } else { ?>
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this position?')\" />" );
					</script>
                    <noscript>
                    	<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
                    </noscript>
                    &nbsp;&nbsp;
                    <? } ?>
                    <input type="hidden" name="positionid" value="<?=$positionid;?>" />
                    <input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="positionOrder" maxlength="3" value="<?=$positionOrder;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Type</b></span><br />
					<select name="positionType">
						<option value="crew"<? if( $positionType == "crew" ) { echo " selected"; } ?>>Crew</option>
						<option value="senior"<? if( $positionType == "senior" ) { echo " selected"; } ?>>Senior Staff</option>
					</select>
				</td>
				<td>
					<span class="fontNormal"><b>Display?</b></span><br />
					<select name="positionDisplay">
						<option value="y" <? if( $positionDisplay == "y" ) { echo " selected"; } ?>>Yes</option>
						<option value="n" <? if( $positionDisplay == "n" ) { echo " selected"; } ?>>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<span class="fontNormal"><b>Open</b></span><br />
					<input type="text" class="order" maxlength="3" name="positionOpen" value="<?=$positionOpen;?>" />
				</td>
				<td colspan="2" valign="top"><span class="fontNormal"><b>Department</b></span><br />
					<select name="positionDept">
                    <?
					
					$getDepts = "SELECT deptid, deptName, deptColor FROM sms_departments ";
					$getDepts.= "WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
					$getDeptsResult = mysql_query( $getDepts );
		
					while( $deptFetch = mysql_fetch_array( $getDeptsResult ) ) {
						extract( $deptFetch, EXTR_OVERWRITE );
					
					?>
                    <option value="<?=$deptid;?>" style="color:#<?=$deptColor;?>;" <? if( $positionDept == $deptid ) { echo "selected"; } ?>>
                      <?=$deptName;?>
                    </option>
					<? } /* close the loop building the select menu */ ?>
					</select>
				</td>
			</tr>
			<tr>
				<td height="25" colspan="4">&nbsp;</td>
			</tr>
			</form>
			<? } ?>
		</table>
	</div>
	
<? } else { errorMessage( "positions management" ); } ?>