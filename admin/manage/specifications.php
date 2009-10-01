<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/specifications.php
Purpose: Page that moderates the specs

System Version: 2.6.0
Last Modified: 2008-04-19 1748 EST
**/

/* access check */
if( in_array( "m_specs", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_POST['action_update_x']))
	{
		$update = "UPDATE sms_specs SET shipClass = %s, shipRole = %s, duration = %d, durationUnit = %s, refit = %d, refitUnit = %s, ";
		$update.= "resupply = %d, resupplyUnit = %s, length = %d, width = %d, height = %d, decks = %d, complimentEmergency = %s, ";
		$update.= "complimentOfficers = %s, complimentEnlisted = %s, complimentMarines = %s, complimentCivilians = %s, warpCruise = %s, ";
		$update.= "warpMaxCruise = %s, warpEmergency = %s, warpMaxTime = %s, warpEmergencyTime = %s, phasers = %s, torpedoLaunchers = %s, ";
		$update.= "torpedoCompliment = %s, defensive = %s, shields = %s, shuttlebays = %s, hasShuttles = %s, hasRunabouts = %s, ";
		$update.= "hasFighters = %s, shuttles = %s, runabouts = %s, fighters = %s, hasTransports = %s, transports = %s WHERE specid = 1 LIMIT 1";
		
		$query = sprintf(
			$update,
			escape_string($_POST['shipClass']),
			escape_string($_POST['shipRole']),
			escape_string($_POST['duration']),
			escape_string($_POST['durationUnit']),
			escape_string($_POST['refit']),
			escape_string($_POST['refitUnit']),
			escape_string($_POST['resupply']),
			escape_string($_POST['resupplyUnit']),
			escape_string($_POST['length']),
			escape_string($_POST['width']),
			escape_string($_POST['height']),
			escape_string($_POST['decks']),
			escape_string($_POST['complimentEmergency']),
			escape_string($_POST['complimentOfficers']),
			escape_string($_POST['complimentEnlisted']),
			escape_string($_POST['complimentMarines']),
			escape_string($_POST['complimentCivilians']),
			escape_string($_POST['warpCruise']),
			escape_string($_POST['warpMaxCruise']),
			escape_string($_POST['warpEmergency']),
			escape_string($_POST['warpMaxTime']),
			escape_string($_POST['warpEmergencyTime']),
			escape_string($_POST['phasers']),
			escape_string($_POST['torpedoLaunchers']),
			escape_string($_POST['torpedoCompliment']),
			escape_string($_POST['defensive']),
			escape_string($_POST['shields']),
			escape_string($_POST['shuttlebays']),
			escape_string($_POST['hasShuttles']),
			escape_string($_POST['hasRunabouts']),
			escape_string($_POST['hasFighters']),
			escape_string($_POST['shuttles']),
			escape_string($_POST['runabouts']),
			escape_string($_POST['fighters']),
			escape_string($_POST['hasTransports']),
			escape_string($_POST['transports'])
		);
		
		$result = mysql_query($query);
		
		/* optimize table */
		optimizeSQLTable( "sms_specs" );
	}
	
	$getSpecs = "SELECT * FROM sms_specs WHERE specid = 1";
	$getSpecsResult = mysql_query( $getSpecs );
	
	while( $specFetch = mysql_fetch_array( $getSpecsResult ) ) {
		extract( $specFetch, EXTR_OVERWRITE );
	}

?>

	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "specifications", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle"><?=ucwords( $simmType );?> Specifications</span>
			
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=specifications">
			<table>
				<tr>
					<td colspan="3" height="15"></td>
				</tr>
				<tr>
					<td colspan="3" class="fontMedium"><b>Ship Information</b></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Class</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="shipClass" maxlength="50" value="<?=$shipClass;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Role</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="shipRole" maxlength="80" value="<?=$shipRole;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Expected Duration</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="duration" size="3" maxlength="3" value="<?=$duration;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Duration Unit<br />(Years, Months, etc.)</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="durationUnit" maxlength="80" value="<?=$durationUnit;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Time Before Refit</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="refit" size="3" maxlength="3" value="<?=$refit;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Refit Unit<br />(Years, Months, etc.)</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="refitUnit" maxlength="80" value="<?=$refitUnit;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Time Before Resupply</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="resupply" size="3" maxlength="3" value="<?=$resupply;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Resupply Unit<br />(Years, Months, etc.)</td>
					<td>&nbsp;</td>
					<td>
						<input type="text" class="text" name="resupplyUnit" maxlength="80" value="<?=$resupplyUnit;?>" />
					</td>
				</tr>
				
				<tr>
					<td colspan="3" height="15">
				</tr>
				
				<tr>
					<td colspan="3" class="fontMedium"><b>Dimensions</b></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Length</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="length" size="5" maxlength="5" value="<?=$length;?>" /> Meters</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Width</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="width" size="5" maxlength="5" value="<?=$width;?>" /> Meters</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Height</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="height" size="5" maxlength="5" value="<?=$height;?>" /> Meters</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Decks</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="decks" size="5" maxlength="5" value="<?=$decks;?>" /></td>
				</tr>
				
				<tr>
					<td colspan="3" height="15">
				</tr>
				
				<tr>
					<td colspan="3" class="fontMedium">
						<b>Personnel Information</b><br />
						<span class="fontSmall">Leave blank if your simm doesn't have the specific type of personnel</span>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Officers</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="complimentOfficers" size="6" maxlength="6" value="<?=$complimentOfficers;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Enlisted Officers</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="complimentEnlisted" size="6" maxlength="6" value="<?=$complimentEnlisted;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Marines</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="complimentMarines" size="6" maxlength="6" value="<?=$complimentMarines;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Civilians</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="complimentCivilians" size="6" maxlength="6" value="<?=$complimentCivilians;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Emergency Capacity</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="complimentEmergency" size="8" maxlength="8" value="<?=$complimentEmergency;?>" /></td>
				</tr>
				
				<tr>
					<td colspan="3" height="15">
				</tr>
				
				<tr>
					<td colspan="3" class="fontMedium"><b>Speed Information</b></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Standard Velocity</td>
					<td>&nbsp;</td>
					<td>Warp <input type="text" class="text" name="warpCruise" size="8" maxlength="8" value="<?=$warpCruise;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Maximum Velocity</td>
					<td>&nbsp;</td>
					<td>
						Warp <input type="text" class="text" name="warpMaxCruise" size="8" maxlength="8" value="<?=$warpMaxCruise;?>" />
						<input type="text" class="text" name="warpMaxTime" maxlength="20" value="<?=$warpMaxTime;?>" />
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Emergency Velocity</td>
					<td>&nbsp;</td>
					<td>
						Warp <input type="text" class="text" name="warpEmergency" size="8" maxlength="8" value="<?=$warpEmergency;?>" />
						<input type="text" class="text" name="warpEmergencyTime" maxlength="20" value="<?=$warpEmergencyTime;?>" />
					</td>
				</tr>
				
				<tr>
					<td colspan="3" height="15">
				</tr>
				
				<tr>
					<td colspan="3" class="fontMedium"><b>Offensive &amp; Defensive Systems</b></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Phasers</td>
					<td>&nbsp;</td>
					<td><textarea name="phasers" rows="5" class="textArea"><?=stripslashes( $phasers );?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Torpedo Launchers</td>
					<td>&nbsp;</td>
					<td><textarea name="torpedoLaunchers" rows="5" class="textArea"><?=stripslashes( $torpedoLaunchers );?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Torpedo Compliment</td>
					<td>&nbsp;</td>
					<td><textarea name="torpedoCompliment" rows="5" class="textArea"><?=stripslashes( $torpedoCompliment );?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Defensive Systems</td>
					<td>&nbsp;</td>
					<td><textarea name="defensive" rows="5" class="textArea"><?=stripslashes( $defensive );?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Shields</td>
					<td>&nbsp;</td>
					<td><textarea name="shields" rows="5" class="textArea"><?=stripslashes( $shields );?></textarea></td>
				</tr>
				
				<tr>
					<td colspan="3" height="15">
				</tr>
				
				<tr>
					<td colspan="3" class="fontMedium"><b>Auxiliary Craft</b></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Shuttlebays</td>
					<td>&nbsp;</td>
					<td><input type="text" class="text" name="shuttlebays" size="3" maxlength="3" value="<?=$shuttlebays;?>" /></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Has Shuttles?</td>
					<td>&nbsp;</td>
					<td>
						<input type="radio" id="shutY" name="hasShuttles" value="y" <? if( $hasShuttles == "y" ) { echo "checked"; } ?>/> <label for="shutY">Yes</label>
						<input type="radio" id="shutN" name="hasShuttles" value="n" <? if( $hasShuttles == "n" ) { echo "checked"; } ?>/> <label for="shutN">No</label>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Shuttles</td>
					<td>&nbsp;</td>
					<td><textarea name="shuttles" rows="5" class="textArea"><?=$shuttles;?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Has Runabouts?</td>
					<td>&nbsp;</td>
					<td>
						<input type="radio" id="runY" name="hasRunabouts" value="y" <? if( $hasRunabouts == "y" ) { echo "checked"; } ?>/> <label for="runY">Yes</label>
						<input type="radio" id="runN" name="hasRunabouts" value="n" <? if( $hasRunabouts == "n" ) { echo "checked"; } ?>/> <label for="runN">No</label>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Runabouts</td>
					<td>&nbsp;</td>
					<td><textarea name="runabouts" rows="5" class="textArea"><?=$runabouts;?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Has Fighters?</td>
					<td>&nbsp;</td>
					<td>
						<input type="radio" id="fightersY" name="hasFighters" value="y" <? if( $hasFighters == "y" ) { echo "checked"; } ?>/> <label for="fightersY">Yes</label>
						<input type="radio" id="fightersN" name="hasFighters" value="n" <? if( $hasFighters == "n" ) { echo "checked"; } ?>/> <label for="fightersN">No</label>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Fighters</td>
					<td>&nbsp;</td>
					<td><textarea name="fighters" rows="5" class="textArea"><?=$fighters;?></textarea></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Ship Has Transports?</td>
					<td>&nbsp;</td>
					<td>
						<input type="radio" id="tranY" name="hasTransports" value="y" <? if( $hasTransports == "y" ) { echo "checked"; } ?>/> <label for="tranY">Yes</label>
						<input type="radio" id="tranN" name="hasTransports" value="n" <? if( $hasTransports == "n" ) { echo "checked"; } ?>/> <label for="tranN">No</label>
					</td>
				</tr>
				<tr>
					<td class="tableCellLabel">Transports</td>
					<td>&nbsp;</td>
					<td><textarea name="transports" rows="5" class="textArea"><?=$transports;?></textarea></td>
				</tr>
				
				<tr>
					<td colspan="3" height="25"></td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td><input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" /></td>
				</tr>
			</table>
		</form>
	</div>
	
<? } else { errorMessage( "specifications management" ); } ?>