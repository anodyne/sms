<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/missions.php
Purpose: Page that creates and moderates the missions

System Version: 2.6.7
Last Modified: 2008-12-11 0905 EST
**/

/* access check */
if( in_array( "m_missions", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_GET['s']) && is_numeric($_GET['s']))
	{
		$sec = $_GET['s'];
	}
	else
	{
		$sec = 1;
	}
	
	/* special function that preps the date for insertion */
	function prep_date($date)
	{
		if($date == "0000-00-00 00:00:00" || $date == "")
		{
			$date = "";
		}
		else
		{
			$date = strtotime($date);
		}
		
		return $date;
	}
	
	if(isset($_POST['action_type']) && $_POST['action_type'] == 'create') {
		
		$create = "INSERT INTO sms_missions ( missionOrder, missionTitle, missionDesc, missionStatus, missionStart, missionEnd, missionImage ) VALUES ( %d, %s, %s, %s, %d, %d, %s )";
		
		$start = prep_date($_POST['missionStart']);
		$end = prep_date($_POST['missionEnd']);
		
		$query = sprintf(
			$create,
			escape_string( $_POST['missionOrder'] ),
			escape_string( $_POST['missionTitle'] ),
			escape_string( $_POST['missionDesc'] ),
			escape_string( $_POST['missionStatus'] ),
			escape_string( $start ),
			escape_string( $end ),
			escape_string( $_POST['missionImage'] )
		);
		
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_missions" );
		
		$action = "create";
	
	} if(isset($_POST['action_update_x'])) {
		
		/* make sure the mission id is a number */
		if( is_numeric( $_POST['missionid'] ) )
		{
			$update = "UPDATE sms_missions SET missionOrder = %d, missionTitle = %s, missionDesc = %s, missionStatus = %s, ";
			$update.= "missionStart = %d, missionEnd = %d, missionImage = %s WHERE missionid = $_POST[missionid] LIMIT 1";
			
			$start = prep_date($_POST['missionStart']);
			$end = prep_date($_POST['missionEnd']);
			
			$query = sprintf(
				$update,
				escape_string( $_POST['missionOrder'] ),
				escape_string( $_POST['missionTitle'] ),
				escape_string( $_POST['missionDesc'] ),
				escape_string( $_POST['missionStatus'] ),
				escape_string( $start ),
				escape_string( $end ),
				escape_string( $_POST['missionImage'] )
			);
		
			$result = mysql_query( $query );
		
			/* optimize the table */
			optimizeSQLTable( "sms_missions" );
		
			$action = "update";
		}
	
	} if(isset($_POST['action_delete_x'])) {
		
		/* make sure the mission id is a number */
		if( is_numeric( $_POST['missionid'] ) )
		{
			/* do the query */
			$query = "DELETE FROM sms_missions WHERE missionid = $_POST[missionid] LIMIT 1";
			$result = mysql_query( $query );
		
			/* optimize the table */
			optimizeSQLTable( "sms_missions" );
		
			$action = "delete";
		}
	
	}
	
	$mission_array = array(
		'current' => array(),
		'completed' => array(),
		'upcoming' => array()
	);

	$missions = "SELECT * FROM sms_missions ORDER BY missionOrder DESC";
	$missionsResult = mysql_query( $missions );

	while( $notes = mysql_fetch_array( $missionsResult ) ) {
		extract( $notes, EXTR_OVERWRITE );

		$mission_array[$missionStatus][] = array(
			'id' => $missionid,
			'title' => $missionTitle,
			'order' => $missionOrder,
			'start' => $missionStart,
			'end' => $missionEnd,
			'desc' => $missionDesc,
			'image' => $missionImage,
			'status' => $missionStatus
		);

	}

?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#container-1 > ul').tabs(<?php echo $sec; ?>);
		
		$("a[rel*=facebox]").click(function() {
			jQuery.facebox(function() {
				jQuery.get('admin/ajax/mission_add.php', function(data) {
					jQuery.facebox(data);
				});
			});
			return false;
		});
	});
</script>

<div class="body">

	<?
	
	$check = new QueryCheck;
	$check->checkQuery( $result, $query );
			
	if( !empty( $check->query ) ) {
		$check->message( "mission", $action );
		$check->display();
	}
	
	?>
	
	<span class="fontTitle">Mission Management</span><br /><br />
	From here you can manage the missions your sim is participating in. You can as many missions simultaneously that you want simply by setting each mission to Current Mission. From the posting pages, players will be able to choose which mission their post is put into. Whether you use the posting system or not, mission management lets you provide pertinent mission information to your players. To create a mission, click the link below, or to update/delete a mission, use the forms below.<br /><br />
	
	<a href="#" rel="facebox" class="fontMedium add"><strong>Add New Mission &raquo;</strong></a>
	<br /><br />
	
	<div id="container-1">
		<ul>
			<li><a href="#one"><span>Current Mission(s)</span></a></li>
			<li><a href="#two"><span>Upcoming Missions (<?=count($mission_array['upcoming']);?>)</span></a></li>
			<li><a href="#three"><span>Completed Missions (<?=count($mission_array['completed']);?>)</span></a></li>
		</ul>

		<div id="one" class="ui-tabs-container ui-tabs-hide">
			<? if (count($mission_array['current']) < 1): ?>
				<span class='fontMedium orange bold'>No current missions</span>
			<? else: ?>
				<table cellpadding="0" cellspacing="3">
					<?php foreach($mission_array['current'] as $k1 => $v1) { ?>
					<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=missions&s=1">
					<tr>
						<td colspan="2" valign="top">
							<span class="fontNormal"><b>Title</b></span><br />
							<input type="text" class="image" name="missionTitle" value="<?=print_input_text( $v1['title'] );?>" />
						</td>
						<td valign="top">
							<span class="fontNormal"><b>Start Date</b></span><br />
							<input type="text" class="date" name="missionStart" value="<? if( empty( $v1['start'] ) ) { echo "0000-00-00 00:00:00"; } else { echo dateFormat( "sql", $v1['start'] ); } ?>" />
						</td>
						<td width="55%" rowspan="3" align="center" valign="top">
							<span class="fontNormal"><b>Description</b></span><br />
							<textarea name="missionDesc" class="desc" rows="7"><?=stripslashes( $v1['desc'] );?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" valign="bottom">
							<span class="fontNormal"><b>Image</b></span><br />
							<span class="fontSmall">images/missionimages/</span>
							<input type="text" class="image" name="missionImage" value="<?=$v1['image'];?>" maxlength="50" />
						</td>
					    <td valign="bottom">
					    	<span class="fontNormal"><b>End Date</b></span><br />
							<input type="text" class="date" name="missionEnd" value="<? if( empty( $v1['end'] ) ) { echo "0000-00-00 00:00:00"; } else { echo dateFormat( "sql", $v1['end'] ); } ?>" />
						</td>
					</tr>
					<tr>
						<td>
							<span class="fontNormal"><b>Order</b></span><br />
							<input type="text" class="color" name="missionOrder" value="<?=$v1['order'];?>" />
						</td>
					    <td>
					    	<span class="fontNormal"><b>Status</b></span><br />
							<select name="missionStatus">
								<option value="upcoming"<? if( $v1['status'] == "upcoming" ) { echo " selected"; } ?>>Upcoming Mission</option>
								<option value="current"<? if( $v1['status'] == "current" ) { echo " selected"; } ?>>Current Mission</option>
								<option value="completed"<? if( $v1['status'] == "completed" ) { echo " selected"; } ?>>Completed Mission</option>
							</select>
						</td>
					    <td></td>
					</tr>
					<tr>
						<td colspan="3"></td>
					    <td align="center" valign="top">
					    	<script type="text/javascript">
								document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this mission?')\" />" );
							</script>
							<noscript>
								<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
							</noscript>
							&nbsp;&nbsp;
							<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
							<input type="hidden" name="missionid" value="<?=$v1['id'];?>" />
						</td>
					</tr>
					<tr>
						<td colspan="4" height="25">&nbsp;</td>
					</tr>
					</form>
				<? } ?>
				</table>
			<? endif; ?>
		</div>
		
		<div id="two" class="ui-tabs-container ui-tabs-hide">
			<? if (count($mission_array['upcoming']) < 1): ?>
				<span class='fontMedium orange bold'>No upcoming missions</span>
			<? else: ?>
				<table cellpadding="0" cellspacing="3">
					<?php foreach($mission_array['upcoming'] as $k2 => $v2) { ?>
					<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=missions&s=2">
					<tr>
						<td colspan="2" valign="top">
							<span class="fontNormal"><b>Title</b></span><br />
							<input type="text" class="image" name="missionTitle" value="<?=print_input_text( $v2['title'] );?>" />
						</td>
						<td valign="top">
							<span class="fontNormal"><b>Start Date</b></span><br />
							<input type="text" class="date" name="missionStart" value="<? if( empty( $v2['start'] ) ) { echo "0000-00-00 00:00:00"; } else { echo dateFormat( "sql", $v2['start'] ); } ?>" />
						</td>
						<td width="55%" rowspan="3" align="center" valign="top">
							<span class="fontNormal"><b>Description</b></span><br />
							<textarea name="missionDesc" class="desc" rows="7"><?=stripslashes( $v2['desc'] );?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" valign="bottom">
							<span class="fontNormal"><b>Image</b></span><br />
							<span class="fontSmall">images/missionimages/</span>
							<input type="text" class="image" name="missionImage" value="<?=$v2['image'];?>" maxlength="50" />
						</td>
					    <td valign="bottom">
					    	<span class="fontNormal"><b>End Date</b></span><br />
							<input type="text" class="date" name="missionEnd" value="<? if( empty( $v2['end'] ) ) { echo "0000-00-00 00:00:00"; } else { echo dateFormat( "sql", $v2['end'] ); } ?>" />
						</td>
					</tr>
					<tr>
						<td>
							<span class="fontNormal"><b>Order</b></span><br />
							<input type="text" class="color" name="missionOrder" value="<?=$v2['order'];?>" />
						</td>
					    <td>
					    	<span class="fontNormal"><b>Status</b></span><br />
							<select name="missionStatus">
								<option value="upcoming"<? if( $v2['status'] == "upcoming" ) { echo " selected"; } ?>>Upcoming Mission</option>
								<option value="current"<? if( $v2['status'] == "current" ) { echo " selected"; } ?>>Current Mission</option>
								<option value="completed"<? if( $v2['status'] == "completed" ) { echo " selected"; } ?>>Completed Mission</option>
							</select>
						</td>
					    <td></td>
					</tr>
					<tr>
						<td colspan="3"></td>
					    <td align="center" valign="top">
					    	<script type="text/javascript">
								document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this mission?')\" />" );
							</script>
							<noscript>
								<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
							</noscript>
							&nbsp;&nbsp;
							<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
							<input type="hidden" name="missionid" value="<?=$v2['id'];?>" />
						</td>
					</tr>
					<tr>
						<td colspan="4" height="25">&nbsp;</td>
					</tr>
					</form>
				<? } ?>
				</table>
			<? endif; ?>
		</div>
		
		<div id="three" class="ui-tabs-container ui-tabs-hide">
			<? if (count($mission_array['completed']) < 1): ?>
				<span class='fontMedium orange bold'>No completed missions</span>
			<? else: ?>
				<table cellpadding="0" cellspacing="3">
					<?php foreach($mission_array['completed'] as $k3 => $v3) { ?>
					<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=missions&s=3">
					<tr>
						<td colspan="2" valign="top">
							<span class="fontNormal"><b>Title</b></span><br />
							<input type="text" class="image" name="missionTitle" value="<?=print_input_text( $v3['title'] );?>" />
						</td>
						<td valign="top">
							<span class="fontNormal"><b>Start Date</b></span><br />
							<input type="text" class="date" name="missionStart" value="<? if( empty( $v3['start'] ) ) { echo "0000-00-00 00:00:00"; } else { echo dateFormat( "sql", $v3['start'] ); } ?>" />
						</td>
						<td width="55%" rowspan="3" align="center" valign="top">
							<span class="fontNormal"><b>Description</b></span><br />
							<textarea name="missionDesc" class="desc" rows="7"><?=stripslashes( $v3['desc'] );?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2" valign="bottom">
							<span class="fontNormal"><b>Image</b></span><br />
							<span class="fontSmall">images/missionimages/</span>
							<input type="text" class="image" name="missionImage" value="<?=$v3['image'];?>" maxlength="50" />
						</td>
					    <td valign="bottom">
					    	<span class="fontNormal"><b>End Date</b></span><br />
							<input type="text" class="date" name="missionEnd" value="<? if( empty( $v3['end'] ) ) { echo "0000-00-00 00:00:00"; } else { echo dateFormat( "sql", $v3['end'] ); } ?>" />
						</td>
					</tr>
					<tr>
						<td>
							<span class="fontNormal"><b>Order</b></span><br />
							<input type="text" class="color" name="missionOrder" value="<?=$v3['order'];?>" />
						</td>
					    <td>
					    	<span class="fontNormal"><b>Status</b></span><br />
							<select name="missionStatus">
								<option value="upcoming"<? if( $v3['status'] == "upcoming" ) { echo " selected"; } ?>>Upcoming Mission</option>
								<option value="current"<? if( $v3['status'] == "current" ) { echo " selected"; } ?>>Current Mission</option>
								<option value="completed"<? if( $v3['status'] == "completed" ) { echo " selected"; } ?>>Completed Mission</option>
							</select>
						</td>
					    <td></td>
					</tr>
					<tr>
						<td colspan="3"></td>
					    <td align="center" valign="top">
					    	<script type="text/javascript">
								document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this mission?')\" />" );
							</script>
							<noscript>
								<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
							</noscript>
							&nbsp;&nbsp;
							<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
							<input type="hidden" name="missionid" value="<?=$v3['id'];?>" />
						</td>
					</tr>
					<tr>
						<td colspan="4" height="25">&nbsp;</td>
					</tr>
					</form>
				<? } ?>
				</table>
			<? endif; ?>
		</div>
		
	</div>
	
</div>

<? } else { errorMessage( "mission management" ); } ?>