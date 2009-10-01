<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/ranks.php
Purpose: Page that moderates the ranks

System Version: 2.6.7
Last Modified: 2008-12-09 2211 EST
**/

/* access check */
if( in_array( "m_ranks", $sessionAccess ) ) {

	/* set the page class and variables */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$result = FALSE;
	$query = FALSE;
	
	/* create an array of allowed ranks */
	$allowedRanksArray = explode( ",", $allowedRanks );
	
	/* set the rank variable */
	if(isset($_GET['rank']) && is_numeric($_GET['rank'])) {
		$rank = $_GET['rank'];
	} else {
		$rank = 1;
	}
	
	/* set the rank set variable */
	if(isset($_GET['set']) && in_array($_GET['set'], $allowedRanksArray)) {
		$set = $_GET['set'];
	} else {
		$set = $rankSet;
	}
	
	/* if the POST action is update */
	if(isset($_POST['action_update_x']))
	{
		if(isset($_POST['rankid']) && is_numeric($_POST['rankid']))
		{
			$rankid = $_POST['rankid'];
		}
		else
		{
			$rankid = NULL;
		}
		
		$update = "UPDATE sms_ranks SET rankOrder = %d, rankName = %s, rankImage = %s, rankDisplay = %s, rankClass = %d, ";
		$update.= "rankShortName = %s WHERE rankid = $rankid LIMIT 1";
		
		$query = sprintf(
			$update,
			escape_string($_POST['rankOrder']),
			escape_string($_POST['rankName']),
			escape_string($_POST['rankImage']),
			escape_string($_POST['rankDisplay']),
			escape_string($_POST['rankClass']),
			escape_string($_POST['rankShortName'])
		);
		
		$result = mysql_query($query);
		
		/* optimize table */
		optimizeSQLTable( "sms_ranks" );
		
		$action = "update";
	}
	elseif(isset($_POST['action_type']) && $_POST['action_type'] == "create")
	{	
		$insert = "INSERT INTO sms_ranks (rankOrder, rankName, rankShortName, rankImage, rankDisplay, rankClass) ";
		$insert.= "VALUES(%d, %s, %s, %s, %s, %d)";
		
		$query = sprintf(
			$insert,
			escape_string($_POST['rankOrder']),
			escape_string($_POST['rankName']),
			escape_string($_POST['rankShortName']),
			escape_string($_POST['rankImage']),
			escape_string($_POST['rankDisplay']),
			escape_string($_POST['rankClass'])
		);
		
		$result = mysql_query($query);
		
		/* optimize table */
		optimizeSQLTable("sms_ranks");
		
		$action = "create";
	}
	elseif(isset($_POST['action_delete_x']))
	{
		if(isset($_POST['rankid']) && is_numeric($_POST['rankid']))
		{
			$rankid = $_POST['rankid'];
		}
		else
		{
			$rankid = NULL;
		}
		
		/* do the delete query */
		$query = "DELETE FROM sms_ranks WHERE rankid = $rankid LIMIT 1";
		$result = mysql_query($query);
		
		/* optimize table */
		optimizeSQLTable( "sms_ranks" );
		
		$action = "delete";
	}

?>
<script type="text/javascript">
	$(document).ready(function() {
		$("a[rel*=facebox]").click(function() {
			var action = $(this).attr("myAction");
			
			jQuery.facebox(function() {
				jQuery.get('admin/ajax/rank_' + action + '.php', function(data) {
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
			$check->message( "rank", $action );
			$check->display();
		}
		
		?>
		<span class="fontTitle">Rank Management</span><br /><br />
		Use the fields below to manage the ranks used by SMS. <strong class="yellow">Please note</strong> that all the rank sets use the same data from the database, the only thing that changes are the images. You cannot edit a certain rank set to be different from another. If you want to create a new rank item, use the link below.<br /><br />
		
		<a href="#" rel="facebox" myAction="add" class="add fontMedium"><strong>Create New Rank &raquo;</strong></a><br /><br />
		
		<p><span class="fontSmall">Click on the rank image to view that rank set</span><br /><br />
		<?php foreach($allowedRanksArray as $key => $value) { ?>
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=ranks&set=<?=trim( $value );?>" class="image">
				<img src="<?=$webLocation;?>images/ranks/<?=trim( $value );?>/preview.png" border="0" alt="" />
			</a>
		<?php } ?></p>
		
		<p><span class="fontSmall">Click on the rank image to view and edit that <b>color set</b></span><br /><br />
		<?php
	
		/* get the rank classes from the database */
		$getRankClasses = "SELECT rankClass, rankImage FROM sms_ranks WHERE rankImage LIKE '%-blank.png' ";
		$getRankClasses.= "GROUP BY rankClass ORDER BY rankClass ASC";
		$getRankClassesResult = mysql_query( $getRankClasses );
	
		/* loop through the spit out the rank links */
		while( $classFetch = mysql_fetch_array( $getRankClassesResult ) ) {
			extract( $classFetch, EXTR_OVERWRITE );
	
		?>
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=ranks&set=<?=trim( $set );?>&rank=<?=$rankClass;?>" class="image">
				<img src="<?=$webLocation;?>images/ranks/<?=trim( $set );?>/<?=$rankImage;?>" border="0" alt="Rank Class <?=$rankClass;?>" />
			</a>
		<?php } ?></p>
		
		<table cellpadding="0" cellspacing="3" border="0">
			<?php
			
			/* pull the ranks from the database */
			$getRanks = "SELECT * FROM sms_ranks WHERE rankClass = $rank ORDER BY rankOrder ASC";
			$getRanksResult = mysql_query( $getRanks );
			
			/* loop through the results and fill the form */
			while( $rankFetch = mysql_fetch_assoc( $getRanksResult ) ) {
				extract( $rankFetch, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=ranks&set=<?=$set;?>&rank=<?=$rank;?>">
			<tr>
				<td colspan="2" width="165">
					<span class="fontNormal"><b>Class</b></span><br />
					<select name="rankClass">
						<?php
						
						$get = "SELECT deptClass, deptName, deptColor FROM sms_departments ";
						$get.= "GROUP BY deptClass ORDER BY deptOrder ASC";
						$getR = mysql_query($get);
						
						while($fetch = mysql_fetch_array($getR)) {
							extract($fetch, EXTR_OVERWRITE);
							
							if($fetch[0] == $rankClass)
							{
								$selected = "selected";
							}
							else
							{
								$selected = FALSE;
							}
							
							echo "<option value='" . $fetch[0] . "' style='color:#" . $fetch[2] . ";' " . $selected . ">" . $fetch[1] . "</option>";
						}
						
						?>
					</select>
				</td>
				<td>
					<span class="fontNormal"><b>Rank</b></span><br />
					<input type="text" class="name" name="rankName" value="<?=print_input_text( $rankName );?>" />
				</td>
				<td rowspan="2" width="150" align="center" valign="middle">
					<img src="<?=$webLocation . 'images/ranks/' . trim( $set ) . '/' . $rankImage;?>" alt="<?=$rankName;?>" border="0" />
				</td>
				<td rowspan="2" align="center" valign="middle">
					<input type="hidden" name="rankid" value="<?=$rankid;?>" />
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" /><br />
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this rank?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="rankOrder" maxlength="3" value="<?=$rankOrder;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Display?</b></span><br />
					<select name="rankDisplay">
						<option value="y"<? if( $rankDisplay == "y" ) { echo " selected"; } ?>>Yes</option>
						<option value="n"<? if( $rankDisplay == "n" ) { echo " selected"; } ?>>No</option>
					</select>
				</td>
				<td>
					<span class="fontNormal"><b>Image</b></span><br />
					<span class="fontSmall">images/ranks/<?=trim( $set );?>/</span><input type="text" class="image" name="rankImage" value="<?=$rankImage;?>" />
				</td>
			</tr>
			<tr>
				<td colspan="5">
					<span class="fontNormal"><strong>Short Name</strong></span><br />
					<input type="text" class="date" name="rankShortName" value="<?=$rankShortName;?>" />
				</td>
			</tr>
			<tr>
				<td colspan="5" height="25"></td>
			</tr>
			</form>
			<? } /* close the rank while loop */ ?>
	  </table>
	</div>

<? } else { errorMessage( "rank management" ); } ?>