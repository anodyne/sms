<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/accessall.php
Purpose: Page to display all of a user's access levels

System Version: 2.6.4
Last Modified: 2008-11-14 0826 EST
**/

/* set the page class */
$pageClass = "admin";
$subMenuClass = "user";
$query = FALSE;
$result = FALSE;

/* access check */
if(in_array("x_access", $sessionAccess))
{
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
			3 => array( "r_milestones", "Crew Milestones" ),
			4 => array( "r_progress", "Sim Progress" ),
			5 => array( "r_security", "Security Report" ),
			6 => array( "r_strikes", "Strike List" ),
			7 => array( "r_versions", "Version History" )
		),
		"user" => array(
			0 => array( "u_account1", "User Account 1" ),
			1 => array( "u_account2", "User Account 2" ),
			2 => array( "u_bio1", "Biography 1" ),
			3 => array( "u_bio2", "Biography 2" ),
			4 => array( "u_bio3", "Biography 3" ),
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
	
	if(isset($_POST['action_add_x']) || isset($_POST['action_remove_x']))
	{
		$post = $_POST;
		
		/* building the variables we'll need */
		$location = strpos($post['access'], "_");
		$location_offset = $location +1;
		$type = substr($post['access'], 0, $location);
		$value = $post['access'];
		$value = substr_replace($value, '', 0, $location_offset);
		
		switch($type)
		{
			case 'post':
				$field = 'accessPost';
				break;
			case 'manage':
				$field = 'accessManage';
				break;
			case 'reports':
				$field = 'accessReports';
				break;
			case 'user':
				$field = 'accessUser';
				break;
			case 'other':
				$field = 'accessOthers';
				break;
		}
		
		/* get the active crew */
		$get = "SELECT * FROM sms_crew WHERE crewType = 'active'";
		$getR = mysql_query($get);
		
		/* loop through the results */
		while($fetch = mysql_fetch_assoc($getR)) {
			extract($fetch, EXTR_OVERWRITE);
			
			/* take the user's access field ($field) and put it into an array */
			$array = explode(',', $$field);
			
			/* find the key of the one we're trying to add if it exists */
			$key_num = array_search($value, $array);
			
			/* if the item isn't in the array */
			if($key_num === FALSE)
			{
				if(isset($_POST['action_add_x']))
				{
					$array[] = $value;
				}
				elseif(isset($_POST['action_remove_x']))
				{
					/* don't do anything */
				}
			}
			else
			{
				if(isset($_POST['action_add_x']))
				{
					/* don't do anything */
				}
				elseif(isset($_POST['action_remove_x']))
				{
					unset($array[$key_num]);
					array_values($array);
				}
			}
			
			/* make sure there are no empty array values */
			foreach ($array as $k => $v)
			{
				if (empty($v))
				{
					unset($array[$k]);
					array_values($array);
				}
			}
			
			/* implode the array into a string */
			$string = implode(',', $array);
			
			/* update each crew member */
			$query = "UPDATE sms_crew SET $field = '$string' WHERE crewid = '$crewid'";
			$result = mysql_query($query);
			
			/* set the appropriate action */
			if( isset($_POST['action_add_x']) ) {
				$action = "add";
			} elseif( isset($_POST['action_remove_x']) ) {
				$action = "remove";
			}
		}
	}

?>

	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery($result, $query);
				
		if(!empty($check->query))
		{
			$check->message("crew access levels", $action);
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Crew Access Levels</span><br /><br />
		From here you can add or remove an access level for every member of the active crew. Just select an access level from the drop down and whether you want to add or remove that access level. <strong class="yellow">Use great caution when adding or removing access for the entire crew!</strong> The same rules/notes that apply for access levels also apply here. Please see the notes associated with the access levels for more information.<br /><br />
		
		<p>&nbsp;</p>
		
		<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=accessall">
			<select name="access">
				<?php foreach($arrayAccessGroups as $k1 => $v1) { ?>
				<optgroup label="<?php echo ucfirst($v1);?>">
					<?php foreach($arrayAccessLevels[$v1] as $k2 => $v2) { ?>
					<option value="<?=$v1;?>_<?=$v2[0];?>"><?php echo $v2[1];?></option>
					<?php } ?>
				</optgroup>
				<?php } ?>
			</select>
			<br /><br />
			
			<input type="image" src="<?=path_userskin;?>buttons/remove.png" name="action_remove" value="Remove" class="button" />
			&nbsp;&nbsp;
			<input type="image" src="<?=path_userskin;?>buttons/add.png" name="action_add" value="Add" class="button" />
		</form>
		
	</div>

<? } else { errorMessage( "crew access level management" ); } ?>