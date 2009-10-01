<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/coc.php
Purpose: Page to change the order of the chain of command

System Version: 2.6.0
Last Modified: 2008-04-19 1713 EST
**/

/* access check */
if( in_array( "m_coc", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$sql = FALSE;
	$result = FALSE;
	
	if(isset($_GET['action']) && ($_GET['action'] == "create" || $_GET['action'] == "delete")) {
		$action = $_GET['action'];
	}
	
	if(isset($_POST['action_update_x']))
	{
		$cocid = $_POST['cocid'];
		$userid = $_POST['crewid'];
		
		if(!is_numeric($cocid)) {
			$cocid = NULL;
		}
		
		if(!is_numeric($userid)) {
			$userid = NULL;
		}
		
		/* do the SQL Update query */
		$sql = "UPDATE sms_coc SET crewid = $userid WHERE cocid = $cocid LIMIT 1";
		$result = mysql_query($sql);
		
		/* optimize the table */
		optimizeSQLTable( "sms_coc" );
		
		$action = "update";
	}
	
	if(isset($action) && $action == "create")
	{
		$sql = "INSERT INTO sms_coc (cocid, crewid) VALUES ('', '0')";
		$result = mysql_query($sql);
		
		/* optimize the table */
		optimizeSQLTable( "sms_coc" );
	}
	if(isset($action) && $action == "delete")
	{
		$getLastId = "SELECT cocid FROM sms_coc ORDER BY cocid DESC LIMIT 1";
		$getLastIdResult = mysql_query($getLastId);
		$lastID = mysql_fetch_assoc($getLastIdResult);
		
		$sql = "DELETE FROM sms_coc WHERE cocid = $lastID[cocid] LIMIT 1";
		$result = mysql_query($sql);
		
		/* optimize the table */
		optimizeSQLTable( "sms_coc" );
	}

?>

	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $sql );
		
		if( !empty( $check->query ) ) {
			$check->message( "chain of command", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage the Chain of Command</span><br /><br />
		
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=coc&action=create" class="add fontMedium"><strong>Add CoC Position &raquo;</strong></a>
		<br />
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=coc&action=delete" class="delete fontMedium"><strong>Remove Last CoC Position &raquo;</strong></a>
		<br /><br />
		
		<table cellspacing="1">
	
		<?
		
		/* pull the CoC from the database */
		$coc = "SELECT * FROM sms_coc ORDER BY cocid ASC";
		$cocResult = mysql_query($coc);
		
		/* set the i variable */
		$i = 1;
		
		/* Start pulling the array and populate the variables */
		while ($cocList = mysql_fetch_array($cocResult)) {
			extract($cocList, EXTR_OVERWRITE);
		
		?>
			
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=coc">
			<tr>
				<td valign="middle" align="center" width="30%"><b>CoC Position #<?=$i;?></b></td>
				<td width="30%">
					<input type="hidden" name="cocid" value="<?=$cocList[0];?>" />
					<select name="crewid">
					
					<?
					
					/* pull the crew from the database */
					$crew = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName FROM ";
					$crew.= "sms_crew AS crew, sms_ranks AS rank WHERE crew.rankid = rank.rankid AND ";
					$crew.= "crew.crewType = 'active' ORDER BY crew.rankid ASC";
					$crewResult = mysql_query($crew);
					
					/* populate the form */
					while ($crewList = mysql_fetch_array($crewResult)) {
						extract($crewList, EXTR_OVERWRITE);
						
						if( $cocList['1'] == $crewid ) {
							echo "<option value='$cocList[1]' selected>$rankName $firstName $lastName</option>";
						} else {
							echo "<option value='$crewid'>$rankName $firstName $lastName</option>";
						}
					
					}
					
					?>
					</select>
				</td>
				<td>&nbsp;</td>
				<td width="30%" valign="middle">
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
				</td>
			</form>
			</tr>
			<tr>
				<td colspan="4" height="10"></td>
			</tr>
			<? $i = $i+1; } ?>
	</table>
	</div>

<? } else { errorMessage( "chain of command management" ); } ?>