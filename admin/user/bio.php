<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/bio.php
Purpose: Page to display the requested bio

System Version: 2.6.7
Last Modified: 2008-12-17 0814 EST
**/

/* do some checking to make sure someone's not trying to do a SQL injection */
if(isset($_GET['crew']) && is_numeric($_GET['crew']))
{
	$crew = $_GET['crew'];
}
else {
	errorMessageIllegal("user bio page");
	exit();
}

/* get the crew type */
$getCrewType = "SELECT crewType FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
$getCrewTypeResult = mysql_query($getCrewType);
$getType = mysql_fetch_assoc($getCrewTypeResult);

/* access check */
if(
	($sessionCrewid == $crew) ||
	(in_array("u_bio2", $sessionAccess) && $getType['crewType'] == "npc") ||
	(in_array("u_bio3", $sessionAccess))
) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "user";
	$result = FALSE;
	$updateCrew = FALSE;
	
	if(isset($_POST['action_x']))
	{
		$action = $_POST['action_x'];
	}

	if(isset($action))
	{
		$update = "UPDATE sms_crew SET firstName = %s, middleName = %s, lastName = %s, rankid = %d, positionid = %d, positionid2 = %d, ";
		$update.= "gender = %s, species = %s, age = %d, image = %s, heightFeet = %d, heightInches = %d, weight = %d, eyeColor = %s, ";
		$update.= "hairColor = %s, physicalDesc = %s, personalityOverview = %s, strengths = %s, ambitions = %s, hobbies = %s, languages = %s, ";
		$update.= "father = %s, mother = %s, brothers = %s, sisters = %s, spouse = %s, children = %s, otherFamily = %s, history = %s, ";
		$update.= "serviceRecord = %s WHERE crewid = $crew LIMIT 1";
		
		$updateCrew = sprintf(
			$update,
			escape_string( $_POST['firstName'] ),
			escape_string( $_POST['middleName'] ),
			escape_string( $_POST['lastName'] ),
			escape_string( $_POST['rank'] ),
			escape_string( $_POST['position'] ),
			escape_string( $_POST['position2'] ),
			escape_string( $_POST['gender'] ),
			escape_string( $_POST['species'] ),
			escape_string( $_POST['age'] ),
			escape_string( $_POST['image'] ),
			escape_string( $_POST['heightFeet'] ),
			escape_string( $_POST['heightInches'] ),
			escape_string( $_POST['weight'] ),
			escape_string( $_POST['eyeColor'] ),
			escape_string( $_POST['hairColor'] ),
			escape_string( $_POST['physicalDesc'] ),
			escape_string( $_POST['personalityOverview'] ),
			escape_string( $_POST['strengths'] ),
			escape_string( $_POST['ambitions'] ),
			escape_string( $_POST['hobbies'] ),
			escape_string( $_POST['languages'] ),
			escape_string( $_POST['father'] ),
			escape_string( $_POST['mother'] ),
			escape_string( $_POST['brothers'] ),
			escape_string( $_POST['sisters'] ),
			escape_string( $_POST['spouse'] ),
			escape_string( $_POST['children'] ),
			escape_string( $_POST['otherFamily'] ),
			escape_string( $_POST['history'] ),
			escape_string( $_POST['serviceRecord'] )
		);
		
		$result = mysql_query( $updateCrew );
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
		
		/* set the variables */
		$position = $_POST['position'];
		$position2 = $_POST['position2'];
		$oldPosition = $_POST['oldPosition'];
		$oldPosition2 = $_POST['oldPosition2'];
		
		if( $getType['crewType'] == "active" || $getType['crewType'] == "inactive" ) {
		
			if( $oldPosition != $position && in_array( "u_bio3", $sessionAccess ) ) {
				
				/* update the position they're being given */
				update_position( $position, 'give' );
				update_position( $oldPosition, 'take' );
				
				/* get the position type from the database */
				$getPosType = "SELECT positionType FROM sms_positions WHERE positionid = '$position' LIMIT 1";
				$getPosTypeResult = mysql_query( $getPosType );
				$positionType = mysql_fetch_row( $getPosTypeResult );
				
				/* set the access levels accordingly */
				if( $positionType[0] == "senior" ) {
					$accessID = 3;
				} else {
					$accessID = 4;
				}
				
				/* pull the default access levels from the db */
				$getGroupLevels = "SELECT * FROM sms_accesslevels WHERE id = $accessID LIMIT 1";
				$getGroupLevelsResult = mysql_query( $getGroupLevels );
				$groups = mysql_fetch_array( $getGroupLevelsResult );
				
				$update = "UPDATE sms_crew SET accessPost = %s, accessManage = %s, accessReports = %s, accessUser = %s, accessOthers = %s ";
				$update.= "WHERE crewid = $crew LIMIT 1";
				
				$query = sprintf(
					$update,
					escape_string( $groups[1] ),
					escape_string( $groups[2] ),
					escape_string( $groups[3] ),
					escape_string( $groups[4] ),
					escape_string( $groups[5] )
				);
				
				$crewUpdateResult = mysql_query( $query );
				
				/* optimize the tables */
				optimizeSQLTable( "sms_crew" );
				optimizeSQLTable( "sms_positions" );
				
			} if( $oldPosition2 != $position2 && in_array( "u_bio3", $sessionAccess ) ) {
			
				/* update the position they're being given */
				update_position( $position2, 'give' );
				update_position( $oldPosition2, 'take' );
				
				/* optimize the table */
				optimizeSQLTable( "sms_positions" );
			
			}
		
		} /* close the crewType check */
		
	} /* close the check for the POST action */

$getCrew = "SELECT * FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
$getCrewResult = mysql_query( $getCrew );

while( $fetchCrew = mysql_fetch_array( $getCrewResult ) ) {
	extract( $fetchCrew, EXTR_OVERWRITE );

	$getRank = "SELECT rankName, rankImage FROM sms_ranks WHERE rankid = '$fetchCrew[rankid]'";
	$getRankResult = mysql_query( $getRank );
	$fetchRank = mysql_fetch_assoc( $getRankResult );
	
	if(in_array("u_bio3", $sessionAccess))
	{
		$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor FROM sms_ranks AS rank, ";
		$ranks.= "sms_departments AS dept WHERE dept.deptClass = rank.rankClass AND dept.deptDisplay = 'y' ";
		$ranks.= "AND rank.rankDisplay = 'y' GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
		$ranksResult = mysql_query($ranks);
		
		$positions = "SELECT position.positionid, position.positionName, dept.deptName, ";
		$positions.= "dept.deptColor FROM sms_positions AS position, sms_departments AS dept ";
		$positions.= "WHERE position.positionOpen > '0' AND dept.deptid = position.positionDept ";
		$positions.= "AND dept.deptDisplay = 'y' ORDER BY dept.deptOrder, position.positionid ASC";
		$position1Result = mysql_query($positions);
		$position2Result = mysql_query($positions);
		
		$user_class = FALSE;
	}
	elseif(in_array("u_bio2", $sessionAccess))
	{		
		$userDeptQuery = "SELECT crew.positionid, crew.rankid, position.positionDept, rank.rankOrder, crew.rankid FROM ";
		$userDeptQuery.= "sms_crew AS crew, sms_positions AS position, sms_ranks AS rank WHERE ";
		$userDeptQuery.= "crew.crewid = '$sessionCrewid' AND crew.positionid = position.positionid AND crew.rankid = rank.rankid LIMIT 1";
		$userDeptResult = mysql_query($userDeptQuery);
		$userDept = mysql_fetch_row($userDeptResult);
		
		$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor ";
		$ranks.= "FROM sms_ranks AS rank, sms_departments AS dept ";
		$ranks.= "WHERE dept.deptid = '$userDept[2]' AND dept.deptClass = rank.rankClass ";
		$ranks.= "AND rank.rankOrder >= '$userDept[3]' AND dept.deptDisplay = 'y' ";
		$ranks.= "AND rank.rankDisplay = 'y' GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
		$ranksResult = mysql_query($ranks);
		
		$positions = "SELECT position.positionid, position.positionName, dept.deptName, dept.deptColor ";
		$positions.= "FROM sms_positions AS position, sms_departments AS dept ";
		$positions.= "WHERE position.positionOpen > '0' AND position.positionDept = dept.deptid AND ";
		$positions.= "position.positionDept = '$userDept[2]' ORDER BY position.positionOrder ASC";
		$position1Result = mysql_query($positions);
		$position2Result = mysql_query($positions);
		
		$rankClass = "SELECT rankClass from sms_ranks WHERE rankid = $userDept[4] LIMIT 1";
		$rankClassR = mysql_query($rankClass);
		$rankRow = mysql_fetch_row($rankClassR);
		
		$user_class = $rankRow[0];
	}
	
	if( $fetchCrew['crewType'] == "npc" ) {
		$type = "NPC";
	} else {
		$type = "Character";
	}
	
	$getClass = "SELECT rankClass FROM sms_ranks WHERE rankid = $fetchCrew[rankid] LIMIT 1";
	$getClassR = mysql_query($getClass);
	$classFetch = mysql_fetch_row($getClassR);
	$char_class = $classFetch[0];

?>
<script type="text/javascript">
	$(document).ready(function() {
		$.facebox.settings.opacity = 0.85;
		
		$("a[rel*=facebox]").click(function() {
			jQuery.facebox(function() {
				jQuery.get('admin/ajax/bio_rank_change.php', function(data) {
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
		$check->checkQuery( $result, $updateCrew );
				
		if( !empty( $check->query ) ) {
			$check->message( "biography", "update" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage <?=$type;?> Biography</span>
		&nbsp;&nbsp;
		<? if( $fetchCrew['crewType'] == "pending" ) { ?><b class="yellow">[ Activation Pending ]</b><? } ?>
		
		<br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$crew;?>">
		<table>
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>Character Information</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">First Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image"  name="firstName" value="<?=print_input_text($firstName);?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Middle Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image"  name="middleName" value="<?=print_input_text($middleName);?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Last Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image"  name="lastName" value="<?=print_input_text($lastName);?>" /></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<?php
			
			if(
				(in_array("u_bio2", $sessionAccess) && $user_class == $char_class) ||
				in_array("u_bio3", $sessionAccess)
			) {
			
			?>
			<tr>
				<td class="tableCellLabel">Rank</td>
				<td>&nbsp;</td>
				<td>
					<select name="rank">
						<?
						
						while( $rank = mysql_fetch_array( $ranksResult ) ) {
							extract( $rank, EXTR_OVERWRITE );
							
							if( $fetchCrew['rankid'] == $rankid ) {
								echo "<option value='". $rankid ."' style='color:#". $deptColor . ";' selected>". $rankName ."</option>";
							} else {
								echo "<option value='". $rankid ."' style='color:#". $deptColor . ";'>". $rankName ."</option>";
							}
						}
						
						?>
					</select>
				</td>
			</tr>
			<? } else { ?>
			<tr>
				<td class="tableCellLabel">Rank</td>
				<td>&nbsp;</td>
				<td>
					<b><? printText( $fetchRank['rankName'] ); ?></b>
					<span class="fontSmall">
						<br /><a href="#" rel="facebox" myAction="why">Why can&rsquo;t I change this rank?</a>
					</span>
					<input type="hidden" name="rank" value="<?=$fetchCrew['rankid'];?>" />
				</td>
			</tr>
			<? } ?>
			
			<? if( in_array( "u_bio2", $sessionAccess ) || in_array( "u_bio3", $sessionAccess ) ) { ?>
			<tr>
				<td class="tableCellLabel">Position</td>
				<td>&nbsp;</td>
				<td>
					<select name="position">
					<?
					
					$currentPosition = "SELECT position.positionid, position.positionName, dept.deptName, dept.deptColor ";
					$currentPosition.= "FROM sms_positions AS position, sms_departments AS dept WHERE ";
					$currentPosition.= "position.positionid = '$fetchCrew[positionid]' AND position.positionDept = dept.deptid";
					$currentPositionResult = mysql_query( $currentPosition );
					$fetchCurrentPosition = mysql_fetch_assoc( $currentPositionResult );
					
					echo "<option value='" . $fetchCurrentPosition['positionid'] . "' style='color:#" . $fetchCurrentPosition['deptColor'] . "'>" . $fetchCurrentPosition['deptName'] . " - " . $fetchCurrentPosition['positionName'] . "</option>";
					
					while( $position = mysql_fetch_array( $position1Result ) ) {
						extract( $position, EXTR_OVERWRITE );
				
						echo "<option value='" . $positionid . "' style='color:#" . $deptColor . "'>" . $deptName . " - " . $positionName . "</option>";
						
					}
					
					?>
					</select>
					<input type="hidden" name="oldPosition" value="<?=$fetchCrew['positionid'];?>" />
				</td>
			</tr>
			<? } else { ?>
			<tr>
				<td class="tableCellLabel">Position</td>
				<td>&nbsp;</td>
				<td>
					<b><? printPlayerPosition( $fetchCrew['crewid'], $positionid, "" ); ?></b>
					<input type="hidden" name="position" value="<?=$positionid;?>" />
				</td>
			</tr>
			<? } ?>
			
			<? if( in_array( "u_bio3", $sessionAccess ) ) { ?>
			<tr>
				<td class="tableCellLabel">Second Position</td>
				<td>&nbsp;</td>
				<td>
					<select name="position2">
					<?
					
					$currentPosition = "SELECT position.positionid, position.positionName, dept.deptName, dept.deptColor ";
					$currentPosition.= "FROM sms_positions AS position, sms_departments AS dept WHERE ";
					$currentPosition.= "position.positionid = '$fetchCrew[positionid2]' AND position.positionDept = dept.deptid";
					$currentPositionResult = mysql_query( $currentPosition );
					$fetchCurrentPosition = mysql_fetch_assoc( $currentPositionResult );
					
					if( !empty( $fetchCrew['positionid2'] ) ) {
						echo "<option value='" . $fetchCurrentPosition['positionid'] . "' style='color:#" . $fetchCurrentPosition['deptColor'] . "'>" . $fetchCurrentPosition['deptName'] . " - " . $fetchCurrentPosition['positionName'] . "</option>";
					}
					
					echo "<option value='0'>No Position Specified</option>";
					
					while( $position2 = mysql_fetch_array( $position2Result ) ) {
						extract( $position2, EXTR_OVERWRITE );
				
						echo "<option value='" . $position2['positionid'] . "' style='color:#" . $deptColor . "'>" . $position2['deptName'] . " - " . $position2['positionName'] . "</option>";
						
					}
					
					?>
					</select>
					<input type="hidden" name="oldPosition2" value="<?=$fetchCrew['positionid2'];?>" />
				</td>
			</tr>
			<? } elseif( !empty( $positionid2 ) ) { ?>
			<tr>
				<td class="tableCellLabel">Second Position</td>
				<td>&nbsp;</td>
				<td>
					<? printPlayerPosition( $fetchCrew['crewid'], $positionid2, "2" ); ?>
					<input type="hidden" name="position2" value="<?=$positionid2;?>" />
				</td>
			</tr>
			<? } ?>
			
			<tr>
				<td class="tableCellLabel">Gender</td>
				<td>&nbsp;</td>
				<td>
					<select name="gender">
						<option value="Male" <? if( $gender == "Male" ) { echo "selected"; } ?>>Male</option>
						<option value="Female" <? if( $gender == "Female" ) { echo "selected"; } ?>>Female</option>
						<option value="Hermaphrodite" <? if( $gender == "selected" ) { echo "selected"; } ?>>Hermaphrodite</option>
						<option value="Neuter" <? if( $gender == "Neuter" ) { echo "selected"; } ?>>Neuter</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Species</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image"  name="species" value="<?=print_input_text($species);?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Age</td>
				<td>&nbsp;</td>
				<td><input type="text" class="order"  name="age" size="4" maxlength="3" value="<?=$age;?>" /></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="tableCellLabel">
					Images
				</td>
				<td>&nbsp;</td>
				<td>
					<strong class="fontSmall yellow">Separate images by commas. The first image listed will be used as the
						main bio image; the rest will be put in a gallery.</strong><br />
					<textarea name="image" class="desc" rows="3"><?=$image;?></textarea>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>Physical Appearance</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Height</td>
				<td>&nbsp;</td>
				<td>
					<input type="text" class="order" name="heightFeet" size="3" maxlength="2" value="<?=$heightFeet;?>" /> &prime;
					<input type="text" class="order" name="heightInches" size="3" maxlength="2" value="<?=$heightInches;?>" /> &Prime;
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Weight</td>
				<td>&nbsp;</td>
				<td><input type="text" class="text" name="weight" size="5" maxlength="4" value="<?=$weight;?>" /> lbs.</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Eye Color</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="eyeColor" value="<?=print_input_text($eyeColor);?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Hair Color</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="hairColor" value="<?=print_input_text($hairColor);?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Physical Description</td>
				<td>&nbsp;</td>
				<td><textarea name="physicalDesc" class="desc" rows="5"><?=$physicalDesc;?></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>Personality &amp; Traits</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">General Overview</td>
				<td>&nbsp;</td>
				<td><textarea name="personalityOverview" class="desc" rows="5"><?=$personalityOverview;?></textarea></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Strengths &amp; Weaknesses</td>
				<td>&nbsp;</td>
				<td><textarea name="strengths" class="desc" rows="5"><?=$strengths;?></textarea></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Ambitions</td>
				<td>&nbsp;</td>
				<td><textarea name="ambitions" class="desc" rows="5"><?=$ambitions;?></textarea></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Hobbies &amp; Interests</td>
				<td>&nbsp;</td>
				<td><textarea name="hobbies" class="desc" rows="5"><?=$hobbies;?></textarea></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Languages</td>
				<td>&nbsp;</td>
				<td><textarea name="languages" class="desc" rows="3"><?=$languages;?></textarea></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>Family</b></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Father</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="father" value="<?=$father;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Mother</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="mother" value="<?=$mother;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Brother(s)</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="brothers" value="<?=$brothers;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Sister(s)</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="sisters" value="<?=$sisters;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Spouse</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="spouse" value="<?=$spouse;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Children</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="children" value="<?=$children;?>" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Other Family</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="otherFamily" value="<?=$otherFamily;?>" /></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>History</b></td>
			</tr>
			<tr>
				<td colspan="3">
					<textarea name="history" rows="15" class="wideTextArea"><?=$history;?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td colspan="3" align="center" class="fontMedium"><b>Service Record</b></td>
			</tr>
			<tr>
				<td colspan="3">
					<textarea name="serviceRecord" rows="10" class="wideTextArea"><?=$serviceRecord;?></textarea>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="25"></td>
			</tr>
			<tr>
				<td colspan="3" align="right">
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action" class="button" value="Update" />
				</td>
			</tr>
		</table>
		
	</div>
	
<? } } else { errorMessage( "this user's bio management" ); } ?>