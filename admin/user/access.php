<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/access.php
Purpose: Page to display all of a user's access levels

System Version: 2.6.0
Last Modified: 2008-04-18 1803 EST
**/

if( isset( $_GET['crew'] ) && is_numeric( $_GET['crew'] ) )
{
	$crew = $_GET['crew'];
}
else
{
	errorMessageIllegal( "user access level page" );
	exit();
}

/* access check */
if( in_array( "x_access", $sessionAccess ) ) {
	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "user";

	$getCrewType = "SELECT crewType FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
	$getCrewTypeResult = mysql_query( $getCrewType );
	$getType = mysql_fetch_assoc( $getCrewTypeResult );
	
	$getCrew = "SELECT * FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
	$getCrewResult = mysql_query( $getCrew );
	
	while( $fetchCrew = mysql_fetch_array( $getCrewResult ) ) {
		extract( $fetchCrew, EXTR_OVERWRITE );
		
		/* set up an array of the different access sections */
		$arrayAccessGroups = array(
			0 => "post",
			1 => "manage",
			2 => "reports",
			3 => "user",
			4 => "other"
		);
		
		/* set up an array of the different access levels */
		$arrayAccessLevels = array(
			"post" => array(
				0 => array( "p_mission", "Write Mission Post" ),
				1 => array( "p_jp", "Write Joint Mission Post" ),
				2 => array( "p_log", "Write Personal Log" ),
				3 => array( "p_news", "Write News Item" ),
				4 => array( "p_pm", "Send Private Message" ),
				5 => array( "p_missionnotes", "Mission Notes" ),
				6 => array( "p_addmission", "Add Mission Post" ),
				7 => array( "p_addjp", "Add Joint Mission Post" ),
				8 => array( "p_addlog", "Add Personal Log" ),
				9 => array( "p_addnews", "Add News Item" )
			),
			"manage" => array(
				0 => array( "m_awards", "Awards" ),
				1 => array( "m_coc", "Chain of Command" ),
				2 => array( "m_crew", "All Characters" ),
				3 => array( "m_createcrew", "Add Character" ),
				4 => array( "m_database1", "Database-1" ),
				5 => array( "m_database2", "Database-2" ),
				6 => array( "m_decks", "Deck Listing" ),
				7 => array( "m_departments", "Departments" ),
				8 => array( "m_docking", "Starship Docking" ),
				9 => array( "m_giveaward", "Give Crew Award" ),
				10 => array( "m_missionnotes", "Mission Notes" ),
				11 => array( "m_posts1", "Mission Posts-1" ),
				12 => array( "m_posts2", "Mission Posts-2" ),
				13 => array( "m_missionsummaries", "Mission Summaries" ),
				14 => array( "m_missions", "Missions" ),
				15 => array( "m_newscat1", "News Category-1" ),
				16 => array( "m_newscat2", "News Category-2" ),
				17 => array( "m_newscat3", "News Category-3" ),
				18 => array( "m_news", "News Items" ),
				19 => array( "m_npcs1", "NPC-1" ),
				20 => array( "m_npcs2", "NPC-2" ),
				21 => array( "m_logs1", "Personal Logs-1" ),
				22 => array( "m_logs2", "Personal Logs-2" ),
				23 => array( "m_positions", "Positions" ),
				24 => array( "m_ranks", "Ranks" ),
				25 => array( "m_removeaward", "Remove Award" ),
				26 => array( "m_globals", "Site Globals" ),
				27 => array( "m_messages", "Site Messages" ),
				28 => array( "m_specs", "Specifications" ),
				29 => array( "m_strike", "Strikes" ),
				30 => array( "m_tour", "Tour" ),
				31 => array( "m_moderation", "User Moderation" )
			),
			"reports" => array(
				0 => array( "r_about", "About SMS" ),
				1 => array( "r_activity", "Crew Activity" ),
				2 => array( "r_count", "Post Count" ),
				3 => array( "r_milestones", "r_milestones" ),
				4 => array( "r_progress", "Sim Progress" ),
				5 => array( "r_strikes", "Strike List" ),
				6 => array( "r_versions", "Version History" )
			),
			"user" => array(
				0 => array( "u_account1", "User Account-1" ),
				1 => array( "u_account2", "User Account-2" ),
				2 => array( "u_bio1", "Biography-1" ),
				3 => array( "u_bio2", "Biography-2" ),
				4 => array( "u_bio3", "Biography-3" ),
				5 => array( "u_inbox", "Private Messages" ),
				6 => array( "u_status", "Status Change" ),
				7 => array( "u_site", "Site Options" ),
				8 => array( "u_nominate", "Award Nominations" )
			),
			"other" => array(
				0 => array( "x_access", "User Access Management" ),
				1 => array( "x_update", "Update SMS" ),
				2 => array( "x_approve_users", "Approve Users" ),
				3 => array( "x_approve_posts", "Approve Mission Posts" ),
				4 => array( "x_approve_logs", "Approve Personal Logs" ),
				5 => array( "x_approve_news", "Approve News Items" ),
				6 => array( "x_approve_docking", "Approve Docking Request" )
			)
		);
		
		/* explode the access fields into arrays */
		$postAccess = explode( ",", $accessPost );
		$manageAccess = explode( ",", $accessManage );
		$reportsAccess = explode( ",", $accessReports );
		$userAccess = explode( ",", $accessUser );
		$otherAccess = explode( ",", $accessOthers );

?>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$('.zebra tr:odd').addClass('alt');
			$('tr.noZebra').removeClass('alt');
		});
	</script>
	
	<div class="body">
		
		<? if( $getType['crewType'] == "npc" ) { ?>
		
		NPCs do not have access levels!
		
		<? } else { ?>
		
		<span class="fontTitle">User Access Levels - <? printCrewName( $crew, "rank", "noLink" ); ?></span>
		&nbsp;&nbsp;
		<?
		
		if( $fetchCrew['crewType'] == "pending" ) {
			echo "<b class='yellow'>[ Activation Pending ]</b>";
		}
		
		/*
			if the person is logged in and has level 5 access, display an icon
			that will take them to edit the entry
		*/
		if( isset( $sessionCrewid ) && in_array( "x_access", $sessionAccess ) ) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href='" . $webLocation . "admin.php?page=manage&sub=access&crew=" . $crew . "'>";
			echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
			echo "</a>";
		}
		
		?>
		
		<br /><br />
		
		<table class="zebra" cellspacing="0" cellpadding="3">
			<? foreach( $arrayAccessGroups as $key1 => $value1 ) { ?>
			<tr>
				<td class="fontLarge" align="right"><b><?=ucfirst( $value1 );?></b></td>
				<td width="20"></td>
				<td>
					<?
					
					if( $value1 == "other" ) {} else {
						if( in_array( $value1, ${$value1.'Access'} ) ) {
							echo "<img src='images/message-unread-icon.png' alt='' border='0' />";
						}
					}
					
					?>
				</td>
			</tr>
			
			<? foreach( $arrayAccessLevels[$value1] as $key2 => $value2 ) { ?>
			<tr>
				<td class="tableCellLabel"><?=( $value2[1] );?></td>
				<td width="20"></td>
				<td>
					<?
					
					if( in_array( $value2[0], ${$value1.'Access'} ) ) {
						echo "<img src='images/message-unread-icon.png' alt='' border='0' />";
					}
					
					?>
				</td>
			</tr>
			<? } ?>
			
			<tr class="noZebra">
				<td colspan="3" height="20"></td>
			</tr>
			<? } /* close the group foreach */ ?>
		</table>
			
		<? } } ?>
		
	</div>

<? } else { errorMessage( "this user's access level report" ); } ?>