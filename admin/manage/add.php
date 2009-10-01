<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/add.php
Purpose: Page to add a player or NPC

System Version: 2.6.8
Last Modified: 2009-01-12 1142 EST
**/

/* access check */
if(in_array("m_createcrew", $sessionAccess))
{
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	$today = getdate();
	
	if(isset($_POST['action_create_x']))
	{
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		if($crewType == 'npc' && (in_array("m_npcs1", $sessionAccess ) || in_array("m_npcs2", $sessionAccess)))
		{
			$insert = "INSERT INTO sms_crew (crewType, firstName, middleName, lastName, gender, species, rankid, positionid) ";
			$insert.= "VALUES (%s, %s, %s, %s, %s, %s, %d, %d)";
			
			$query = sprintf(
				$insert,
				escape_string('npc'),
				escape_string($_POST['firstName']),
				escape_string($_POST['middleName']),
				escape_string($_POST['lastName']),
				escape_string($_POST['gender']),
				escape_string($_POST['species']),
				escape_string($_POST['rank']),
				escape_string($_POST['position'])
			);
			
			$result = mysql_query($query);
			
			/* optimize the table */
			optimizeSQLTable( "sms_crew" );
			
			$type = 'non-playing character';
		}
		elseif($crewType == "active" && in_array("m_crew", $sessionAccess))
		{
			if($password == $confirmPassword)
			{
				if(!is_numeric($position)) {
					$position = NULL;
				}
				
				/* get the position type from the database */
				$getPosType = "SELECT positionType FROM sms_positions WHERE positionid = $position LIMIT 1";
				$getPosTypeResult = mysql_query($getPosType);
				$positionType = mysql_fetch_row($getPosTypeResult);
			
				/* set the access levels accordingly */
				if($positionType[0] == "senior") {
					$accessID = 3;
				} else {
					$accessID = 4;
				}
			
				/* pull the default access levels from the db */
				$getGroupLevels = "SELECT * FROM sms_accesslevels WHERE id = $accessID LIMIT 1";
				$getGroupLevelsResult = mysql_query($getGroupLevels);
				$groups = mysql_fetch_array($getGroupLevelsResult);
				
				$insert = "INSERT INTO sms_crew (crewType, username, password, email, firstName, middleName, lastName, gender, ";
				$insert.= "species, rankid, positionid, joinDate, accessPost, accessManage, accessReports, accessUser, ";
				$insert.= "accessOthers, moderatePosts, moderateLogs, moderateNews) ";
				$insert.= "VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s)";
				
				$query = sprintf(
					$insert,
					escape_string('active'),
					escape_string($_POST['username']),
					escape_string(md5($_POST['password'])),
					escape_string($_POST['email']),
					escape_string($_POST['firstName']),
					escape_string($_POST['middleName']),
					escape_string($_POST['lastName']),
					escape_string($_POST['gender']),
					escape_string($_POST['species']),
					escape_string($_POST['rank']),
					escape_string($_POST['position']),
					escape_string($today[0]),
					escape_string($groups[1]),
					escape_string($groups[2]),
					escape_string($groups[3]),
					escape_string($groups[4]),
					escape_string($groups[5]),
					escape_string($_POST['moderatePosts']),
					escape_string($_POST['moderateLogs']),
					escape_string($_POST['moderateNews'])
				);
		
				$result = mysql_query($query);
				
				update_position($position, 'give');
			
				/* optimize the table */
				optimizeSQLTable( "sms_crew" );
				optimizeSQLTable( "sms_positions" );
				
				$type = 'character';
			
				/** EMAIL THE PLAYER **/

				/* define the variables */
				$to = $email . ", " . printCOEmail();
				$from = printCO('short_rank') . " < " . printCOEmail() . " >";
				$subject = $emailSubject . " New Character Created";
				$message = "This is an automatic email to notify you that your new character has been created.  Please log in to the site (" . $webLocation . ") using the username and password below to update your biography.  If you have any questions, please contact the CO.

USERNAME: " . $_POST['username'] . "
PASSWORD: " . $_POST['password'] . "";
		
				/* send the email */
				mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
			}
			else
			{
				$result = FALSE;
			}
		
		}
	}

?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#crewTypeN').click(function(){
				$('#pc').hide();
			});
			
			$('#crewTypeP').click(function(){
				$('#pc').show();
			});
		});
	</script>
	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery($result, $query);
		
		if(!empty($check->query))
		{
			$check->message($type, "create");
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Add Crew</span><br /><br />
	
		<? if( in_array( "m_npcs1", $sessionAccess ) ) { ?>
		Department Heads are permitted to create NPCs for their own department and at ranks lower than their own.  If you want an NPC to hold a rank equal to or higher than your own, please contact the CO or XO.  Additionally, you can assign an NPC to any open position.  If you have questions or problems, please contact the CO or XO.
		
		<? } elseif( in_array( "m_npcs2", $sessionAccess ) ) { ?>
		Commanding Officers and Executive Officers are permitted to create NPCs for any department and at any rank.  Additionally, COs can assign an NPC to any open position in any department. COs are also the only members of the crew authorized to create new playing characters.  New playing characters that are created will still need to be approved through the Control Panel before the player associated with the character can log in and begin simming.
		<? } ?><br /><br />
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=add">
		
		<strong>Character Type</strong><br />
		<input type="radio" id="crewTypeP" name="crewType" value="active" <? if( !in_array( "m_crew", $sessionAccess ) ) { echo "disabled"; } else { echo "checked"; } ?>/> <label for="crewTypeP">Playing Character</label>
		<input type="radio" id="crewTypeN" name="crewType" value="npc" <? if( !in_array( "m_crew", $sessionAccess ) && ( in_array( "m_npcs1", $sessionAccess ) || in_array( "m_npcs2", $sessionAccess ) ) ) { echo " checked"; } ?> /> <label for="crewTypeN">Non-Playing Character</label>
		
		<? if( in_array( "m_crew", $sessionAccess ) ) { ?>
			<div id="pc">
				<table>
					
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Username</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image"  name="username" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Password</td>
						<td>&nbsp;</td>
						<td><input type="password" class="image" name="password" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Confirm Password</td>
						<td>&nbsp;</td>
						<td><input type="password" class="image" name="confirmPassword" /></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Email Address</td>
						<td>&nbsp;</td>
						<td><input type="text" class="image" name="email" /></td>
					</tr>
					
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td class="tableCellLabel">Moderate Posts?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" name="moderatePosts" id="posts_y" value="y" /> <label for="posts_y">Yes</label>
							<input type="radio" name="moderatePosts" id="posts_n" value="n" checked /> <label for="posts_n">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Moderate Logs?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" name="moderateLogs" id="logs_y" value="y" /> <label for="logs_y">Yes</label>
							<input type="radio" name="moderateLogs" id="logs_n" value="n" checked /> <label for="logs_n">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Moderate News?</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" name="moderateNews" id="news_y" value="y" /> <label for="news_y">Yes</label>
							<input type="radio" name="moderateNews" id="news_n" value="n" checked /> <label for="news_n">No</label>
						</td>
					</tr>
				</table>
			</div>
		<? } ?>
		
		<br /><br />
		<table>
			<tr>
				<td class="tableCellLabel">First Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="firstName" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Middle Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="middleName" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Last Name</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="lastName" /></td>
			</tr>
			<tr>
				<td class="tableCellLabel">Gender</td>
				<td>&nbsp;</td>
				<td>
					<select name="gender">
						<option value="Male">Male</option>
						<option value="Female">Female</option>
						<option value="Hermaphrodite">Hermaphrodite</option>
						<option value="Neuter">Neuter</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Species</td>
				<td>&nbsp;</td>
				<td><input type="text" class="image" name="species" /></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<?
			
			if( in_array( "m_npcs2", $sessionAccess ) ) {
				$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor FROM sms_ranks AS rank, ";
				$ranks.= "sms_departments AS dept WHERE dept.deptClass = rank.rankClass AND dept.deptDisplay = 'y' ";
				$ranks.= "AND rank.rankDisplay = 'y' GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
				$ranksResult = mysql_query( $ranks );
				
				$positions = "SELECT position.positionid, position.positionName, dept.deptName, ";
				$positions.= "dept.deptColor FROM sms_positions AS position, sms_departments AS dept ";
				$positions.= "WHERE position.positionOpen > '0' AND dept.deptid = position.positionDept ";
				$positions.= "AND dept.deptDisplay = 'y' ORDER BY position.positionDept, position.positionid ASC";
				$positionsResult = mysql_query( $positions );
				
			} elseif( in_array( "m_npcs1", $sessionAccess ) ) {
			
				$userDeptQuery = "SELECT crew.positionid, crew.rankid, position.positionDept, rank.rankOrder FROM ";
				$userDeptQuery.= "sms_crew AS crew, sms_positions AS position, sms_ranks AS rank WHERE ";
				$userDeptQuery.= "crew.crewid = '$sessionCrewid' AND crew.positionid = position.positionid AND crew.rankid = rank.rankid LIMIT 1";
				$userDeptResult = mysql_query( $userDeptQuery );
				$userDept = mysql_fetch_row( $userDeptResult );
				
				$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor ";
				$ranks.= "FROM sms_ranks AS rank, sms_departments AS dept ";
				$ranks.= "WHERE dept.deptid = '$userDept[2]' AND dept.deptClass = rank.rankClass ";
				$ranks.= "AND rank.rankOrder >= '$userDept[3]' AND dept.deptDisplay = 'y' ";
				$ranks.= "AND rank.rankDisplay = 'y' GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
				$ranksResult = mysql_query( $ranks );
				
				$positions = "SELECT position.positionid, position.positionName, dept.deptName, dept.deptColor ";
				$positions.= "FROM sms_positions AS position, sms_departments AS dept ";
				$positions.= "WHERE position.positionOpen > '0' AND position.positionDept = dept.deptid AND ";
				$positions.= "position.positionDept = '$userDept[2]' ORDER BY positionOrder ASC";
				$positionsResult = mysql_query( $positions );
				
			}
			
			?>
			<tr>
				<td class="tableCellLabel">Rank</td>
				<td>&nbsp;</td>
				<td>
					<select name="rank">
						<?
						
						while($rank = mysql_fetch_assoc($ranksResult)) {
							extract($rank, EXTR_OVERWRITE);
							
							echo "<option value='" . $rank['rankid'] . "' style='background:#000; color:#" . $rank['deptColor'] . ";'>" . $rank['rankName'] . "</option>";
						
						}
						
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Position</td>
				<td>&nbsp;</td>
				<td>
					<select name="position">
					<?
					
					while( $position = mysql_fetch_assoc( $positionsResult ) ) {
						extract( $position, EXTR_OVERWRITE );
				
						echo "<option value='" . $position['positionid'] . "' style='color:#" . $position['deptColor'] . ";'>" . $position['deptName'] . " - " . $position['positionName'] . "</option>";
						
					}
					
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="25"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td><input type="image" src="<?=path_userskin;?>buttons/create.png" name="action_create" class="button" value="Create" /></td>
			</tr>
		</table>
		</form>
	</div>
	
<? } else { errorMessage( "add character" ); } ?>