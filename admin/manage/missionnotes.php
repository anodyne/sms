<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/missionnotes.php
Purpose: Page that moderates the notes for each mission

System Version: 2.6.7
Last Modified: 2008-12-11 0911 EST
**/

/* access check */
if( in_array( "m_missionnotes", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$result = false;
	$query = false;
	
	if(isset($_GET['s']) && is_numeric($_GET['s']))
	{
		$sec = $_GET['s'];
	}
	else
	{
		$sec = 1;
	}
	
	if(isset($_POST['action_update_x']))
	{
		if(is_numeric($_POST['missionid']))
		{
			$id = $_POST['missionid'];
		}
		
		$update = "UPDATE sms_missions SET missionNotes = %s WHERE missionid = $id LIMIT 1";
		
		$query = sprintf(
			$update,
			escape_string($_POST['missionNotes'])
		);
		
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_missions" );
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
		'notes' => $missionNotes
	);
	
}

?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#container-1 > ul').tabs(<?php echo $sec; ?>);
		});
	</script>
	
	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "mission note", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Mission Notes</span><br /><br />
		Mission notes allow COs to give players important information before they post. Use it to outline part of a mission or to remind people of in-character assignments. Mission notes can be accessed from the mission posting pages (single or joint) as well as the Mission Notes link in the Post section of the menu.<br /><br />
		
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
					<table>
						<?php foreach($mission_array['current'] as $k1 => $v1) { ?>
						<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=missionnotes&s=1">
							<tr>
								<td class="tableCellLabel">
									<? printText( $v1['title'] );?>
									<input type="hidden" name="missionid" value="<?=$v1['id'];?>" />
								</td>
								<td>&nbsp;</td>
								<td>
									<textarea name="missionNotes" rows="10" class="wideTextArea"><?=stripslashes( $v1['notes'] );?></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td colspan="2"></td>
								<td align="right">
									<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
								</td>
							</tr>
							<tr>
								<td colspan="3" height="10"></td>
							</tr>
						</form>
						<?php } ?>
					</table>
				<? endif; ?>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<? if (count($mission_array['upcoming']) < 1): ?>
					<span class='fontMedium orange bold'>No upcoming missions</span>
				<? else: ?>
					<table>
						<?php foreach($mission_array['upcoming'] as $k3 => $v3) { ?>
						<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=missionnotes&s=3">
							<tr>
								<td class="tableCellLabel">
									<? printText( $v3['title'] );?>
									<input type="hidden" name="missionid" value="<?=$v3['id'];?>" />
								</td>
								<td>&nbsp;</td>
								<td>
									<textarea name="missionNotes" rows="10" class="wideTextArea"><?=stripslashes( $v3['notes'] );?></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td colspan="2"></td>
								<td align="right">
									<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
								</td>
							</tr>
							<tr>
								<td colspan="3" height="10"></td>
							</tr>
						</form>
						<?php } ?>
					</table>
				<? endif; ?>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<? if (count($mission_array['completed']) < 1): ?>
					<span class='fontMedium orange bold'>No completed missions</span>
				<? else: ?>
					<table>
						<?php foreach($mission_array['completed'] as $k2 => $v2) { ?>
						<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=missionnotes&s=2">
							<tr>
								<td class="tableCellLabel">
									<? printText( $v2['title'] );?>
									<input type="hidden" name="missionid" value="<?=$v2['id'];?>" />
								</td>
								<td>&nbsp;</td>
								<td>
									<textarea name="missionNotes" rows="10" class="wideTextArea"><?=stripslashes( $v2['notes'] );?></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td colspan="2"></td>
								<td align="right">
									<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
								</td>
							</tr>
							<tr>
								<td colspan="3" height="10"></td>
							</tr>
						</form>
						<?php } ?>
					</table>
				<? endif; ?>
			</div>
		</div>
		
	</div>

<? } else { errorMessage( "mission notes management" ); } ?>