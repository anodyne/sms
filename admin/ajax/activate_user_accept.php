<?php

/* need to connect to the database */
require_once('../../framework/dbconnect.php');

/* pulling a function from new library */
require_once('../../framework/session.name.php');

/* get system unique identifier */
$sysuid = get_system_uid();

/* rewrite master php.ini session.name */
ini_set('session.name', $sysuid);

session_start();

if( !isset( $sessionAccess ) ) {
	$sessionAccess = "";
}

if( !is_array( $sessionAccess ) ) {
	$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
}

if(in_array("x_approve_users", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');

	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$getPendingCrew = "SELECT crewid, firstName, lastName, positionid, rankid ";
	$getPendingCrew.= "FROM sms_crew WHERE crewid = '$id' LIMIT 1";
	$getPendingCrewResult = mysql_query( $getPendingCrew );
	$pendingArray = mysql_fetch_assoc( $getPendingCrewResult );
	
	$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor FROM sms_ranks AS rank, ";
	$ranks.= "sms_departments AS dept WHERE dept.deptClass = rank.rankClass AND dept.deptDisplay = 'y' ";
	$ranks.= "AND rank.rankDisplay = 'y' GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
	$ranksResult = mysql_query( $ranks );
	
	$positions = "SELECT position.positionid, position.positionName, dept.deptName, ";
	$positions.= "dept.deptColor FROM sms_positions AS position, sms_departments AS dept ";
	$positions.= "WHERE position.positionOpen > '0' AND dept.deptDisplay = 'y' AND ";
	$positions.= "dept.deptid = position.positionDept AND dept.deptType = 'playing' ";
	$positions.= "ORDER BY dept.deptOrder, position.positionid ASC";
	$positionsResult = mysql_query( $positions );
	
	$currentPosition = "SELECT position.positionid, position.positionName, dept.deptName, ";
	$currentPosition.= "dept.deptColor FROM sms_positions AS position, sms_departments ";
	$currentPosition.= "AS dept WHERE position.positionid = '$pendingArray[positionid]' ";
	$currentPosition.= "AND position.positionDept = dept.deptid";
	$currentPositionResult = mysql_query( $currentPosition );
	$fetchCurrentPosition = mysql_fetch_assoc( $currentPositionResult );

?>
	<h2>Accept Crew Application &ndash; <? printText( $pendingArray['firstName'] . " " . $pendingArray['lastName'] );?></h2>
	<p>Please specify the position and rank you want <strong><? printText( $pendingArray['firstName'] . " " . $pendingArray['lastName'] );?></strong> to be accepted at. After that, please specify the message you want to be sent to the player regarding their acceptance.</p>
	<p>Acceptance messages can now use wild cards for dynamic elements. For instance, using the <strong class="yellow">#rank#</strong> wild card will insert the rank you give them into the email before it is sent. Available wild cards are: <strong>#ship#</strong>, <strong>#position#</strong>, <strong>#player#</strong> (character&rsquo;s name), and <strong>#rank#</strong>.</p>
	
	<form method="post" action="">
		<table>
			<tr>
				<td class="tableCellLabel">Position</td>
				<td>&nbsp;</td>
				<td>
					<select name="position">
					<?
			
					echo "<option value='" . $fetchCurrentPosition['positionid'] . "' style='color:#" . $fetchCurrentPosition['deptColor'] . ";background-color:#000;'>" . $fetchCurrentPosition['deptName'] . " - " . $fetchCurrentPosition['positionName'] . "</option>";
					
					while( $position = mysql_fetch_array( $positionsResult ) ) {
						extract( $position, EXTR_OVERWRITE );
				
						echo "<option value='" . $position['positionid'] . "' style='color:#" . $deptColor . ";background-color:#000;'>" . $position['deptName'] . " - " . $position['positionName'] . "</option>";
						
					}
					
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tableCellLabel">Rank</td>
				<td>&nbsp;</td>
				<td>
					<select name="rank">
					<?
			
					while( $rank = mysql_fetch_array( $ranksResult ) ) {
						extract( $rank, EXTR_OVERWRITE );
						
						if( $pendingArray['rankid'] == $rank['rankid'] ) {
							echo "<option value='" . $rankid . "' style='background:#000 url( images/ranks/default/" . $rankImage . " ) no-repeat 0 100%; height:50px; color:#" . $deptColor . ";' selected>" . $rankName . "</option>";
						} else {
							echo "<option value='" . $rankid . "' style='background:#000 url( images/ranks/default/" . $rankImage . " ) no-repeat 0 100%; height:50px; color:#" . $deptColor . ";'>" . $rankName . "</option>";
						}
						
					}
					
					?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="10"></td>
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
			
			<tr>
				<td colspan="3" height="10"></td>
			</tr>
			
			<tr>
				<td class="tableCellLabel">Email Message</td>
				<td>&nbsp;</td>
				<td>
					<textarea name="acceptMessage" class="narrowTable" rows="10"><?=stripslashes( $acceptMessage );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td>
					<input type="hidden" name="action_id" value="<?=$pendingArray['crewid'];?>" />
					<input type="hidden" name="action_category" value="user" />
					<input type="hidden" name="action_type" value="accept" />
					
					<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Approve" />
				</td>
			</tr>
		</table>
	</form>

<?php } /* close the access check */ ?>