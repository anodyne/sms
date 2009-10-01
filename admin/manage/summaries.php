<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/summaries.php
Purpose: Page that moderates the various messages found throughout SMS

System Version: 2.6.7
Last Modified: 2008-12-11 0920 EST
**/

/* access check */
if( in_array( "m_missionsummaries", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_GET['t']) && is_numeric($_GET['t'])) {
		$tab = $_GET['t'];
	} else {
		$tab = 1;
	}
	
	/* if the POST action is update */
	if(isset($_POST['action_update_x']))
	{
		if(isset($_POST['missionid']) && is_numeric($_POST['missionid'])) {
			$missionid = $_POST['missionid'];
		} else {
			$missionid = NULL;
		}
		
		$update = "UPDATE sms_missions SET missionSummary = %s WHERE missionid = $missionid LIMIT 1";
		$query = sprintf($update, escape_string($_POST['missionSummary']));
		$result = mysql_query($query);
		
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
			'summary' => $missionSummary
		);
		
	}

?>

	<div class="body">
	
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "mission summary", "update" );
			$check->display();
		}
		
		?>
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('#container-1 > ul').tabs(<?php echo $tab; ?>);
			});
		</script>
		
		<span class="fontTitle">Manage Mission Summaries</span><br /><br />
		Mission summaries allow you to summarize your past and current missions so that new users can get a feel for what your crew has done in-character.  It&rsquo;s also a great way for players that enter during a mission or current players who have fallen behind to get caught up quickly.<br /><br />
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Current Mission(s)</span></a></li>
				<li><a href="#two"><span>Upcoming Missions (<?=count($mission_array['upcoming']);?>)</span></a></li>
				<li><a href="#three"><span>Completed Missions (<?=count($mission_array['completed']);?>)</span></a></li>
			</ul>
			
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<? if (count($mission_array['current']) < 1): ?>
					<strong class='orange fontMedium'>No current missions</strong>
				<? else: ?>
					<table>
						<?php foreach ($mission_array['current'] as $row): ?>
							
						<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=summaries&t=1">
						<tr>
							<td class="tableCellLabel">
								<? printText($row['title']);?>
								<input type="hidden" name="missionid" value="<?=$row['id'];?>" />
							</td>
							<td>&nbsp;</td>
							<td>
								<textarea name="missionSummary" rows="15" class="wideTextArea"><?=stripslashes( $row['summary']);?></textarea>
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
						</form>
					<?php endforeach; ?>
					</table>
				<?php endif; ?>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<? if (count($mission_array['upcoming']) < 1): ?>
					<strong class='orange fontMedium'>No upcoming missions</strong>
				<? else: ?>
					<table>
						<?php foreach ($mission_array['upcoming'] as $row): ?>
							
						<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=summaries&t=2">
						<tr>
							<td class="tableCellLabel">
								<? printText($row['title']);?>
								<input type="hidden" name="missionid" value="<?=$row['id'];?>" />
							</td>
							<td>&nbsp;</td>
							<td>
								<textarea name="missionSummary" rows="15" class="wideTextArea"><?=stripslashes( $row['summary']);?></textarea>
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
						</form>
					<?php endforeach; ?>
					</table>
				<?php endif; ?>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<? if (count($mission_array['completed']) < 1): ?>
					<strong class='orange fontMedium'>No completed missions</strong>
				<? else: ?>
					<table>
						<?php foreach ($mission_array['completed'] as $row): ?>
							
						<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=summaries&t=3">
						<tr>
							<td class="tableCellLabel">
								<? printText($row['title']);?>
								<input type="hidden" name="missionid" value="<?=$row['id'];?>" />
							</td>
							<td>&nbsp;</td>
							<td>
								<textarea name="missionSummary" rows="15" class="wideTextArea"><?=stripslashes( $row['summary']);?></textarea>
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
						</form>
					<?php endforeach; ?>
					</table>
				<?php endif; ?>
			</div>
		</div>
		
	</div>

<? } else { errorMessage( "mission summaries management" ); } ?>