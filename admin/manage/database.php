<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/database.php
Purpose: Page that moderates the database entries

System Version: 2.6.9
Last Modified: 2009-05-14 0743 EST
**/

/* access check */
if( in_array( "m_database1", $sessionAccess ) || in_array( "m_database2", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	$action_type = FALSE;
	$myDept = FALSE;
	
	if(isset($_POST))
	{
		/* define the POST variables */
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		/* protecting against SQL injection */
		if(isset($action_id) && !is_numeric($action_id))
		{
			$action_id = FALSE;
			exit();
		}
		
		switch($action_type)
		{
			case 'create':
				
				$create = "INSERT INTO sms_database (dbTitle, dbType, dbDesc, dbOrder, dbDisplay, dbURL, dbContent, dbDept) ";
				$create.= "VALUES (%s, %s, %s, %d, %s, %s, %s, %d)";

				$query = sprintf(
					$create,
					escape_string($_POST['dbTitle']),
					escape_string($_POST['dbType']),
					escape_string($_POST['dbDesc']),
					escape_string($_POST['dbOrder']),
					escape_string($_POST['dbDisplay']),
					escape_string($_POST['dbURL']),
					escape_string($_POST['dbContent']),
					escape_string($_POST['dbDept'])
				);

				$result = mysql_query( $query );
				
				break;
			case 'update':
				
				$update = "UPDATE sms_database SET dbTitle = %s, dbOrder = %d, dbDisplay = %s, dbURL = %s, dbDesc = %s, dbContent = %s, ";
				$update.= "dbType = %s, dbDept = %d WHERE dbid = $action_id LIMIT 1";

				$query = sprintf(
					$update,
					escape_string($_POST['dbTitle']),
					escape_string($_POST['dbOrder']),
					escape_string($_POST['dbDisplay']),
					escape_string($_POST['dbURL']),
					escape_string($_POST['dbDesc']),
					escape_string($_POST['dbContent']),
					escape_string($_POST['dbType']),
					escape_string($_POST['dbDept'])
				);

				$result = mysql_query( $query );
				
				break;
			case 'delete':
				
				$query = "DELETE FROM sms_database WHERE dbid = $action_id LIMIT 1";
				$result = mysql_query($query);
				
				break;
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_database" );
	}

	/* set up the database array */
	$database = array(0 => array());
	$departments = array();

	/* pull all the applicable departments */
	$depts = "SELECT * FROM sms_departments WHERE deptDatabaseUse = 'y' ORDER BY deptORDER ASC";
	$deptsR = mysql_query($depts);

	/* set up the department sections */
	while($deptFetch = mysql_fetch_assoc($deptsR)) {
		extract($deptFetch, EXTR_OVERWRITE);
	
		$database[$deptid] = array();
		$departments[] = $deptid;
	}

	/* pull global entries */
	$entries = "SELECT * FROM sms_database WHERE dbDept = 0 ORDER BY dbOrder ASC";
	$entriesR = mysql_query($entries);
	
	/* fill in the array */
	while($entryFetch = mysql_fetch_assoc($entriesR)) {
		extract($entryFetch, EXTR_OVERWRITE);
	
		$database[0][] = array('id' => $dbid, 'title' => $dbTitle, 'type' => $dbType, 'url' => $dbURL, 'order' => $dbOrder);
	}
	
	/* pull all the entries */
	$entries = "SELECT db.* FROM sms_database AS db, sms_departments AS d WHERE ";
	$entries.= "db.dbDept = d.deptid AND d.deptDatabaseUse = 'y'";
	$entriesR = mysql_query($entries);
	
	/* fill in the array */
	while($entryFetch = mysql_fetch_assoc($entriesR)) {
		extract($entryFetch, EXTR_OVERWRITE);
	
		$database[$dbDept][] = array('id' => $dbid, 'title' => $dbTitle, 'type' => $dbType, 'url' => $dbURL, 'order' => $dbOrder);
	}

	/* if they have level 1 database access, get their department id */
	if(!in_array("m_database2", $sessionAccess))
	{
		$depts = "SELECT position.positionDept FROM sms_crew AS crew, sms_positions AS position, sms_departments AS dept WHERE ";
		$depts.= "crew.crewid = '$sessionCrewid' AND crew.positionid = position.positionid LIMIT 1";
		$deptsR = mysql_query($depts);
		$deptFetch = mysql_fetch_row($deptsR);
	
		$myDept = $deptFetch[0];
		$arrayKeys = array_keys($database);
	}

	/* scrub the array for empty sets */
	foreach($database as $a => $b)
	{
		if(count($b) == 0)
		{
			unset($database[$a]);
		}
	
		if(!in_array("m_database2", $sessionAccess) && $a != $myDept)
		{
			unset($database[$a]);
		}
	}

?>
<script type="text/javascript">
	$(document).ready(function() {
		$("a[rel*=facebox]").click(function() {
			var id = $(this).attr("myID");
			var action = $(this).attr("myAction");
			
			jQuery.facebox(function() {
				jQuery.get('admin/ajax/database_' + action + '.php?id=' + id, function(data) {
					jQuery.facebox(data);
				});
			});
			return false;
		});
		
		$('.zebra tr:nth-child(even)').addClass('alt');
	});
</script>

<div class="body">

	<?php
	
	$check = new QueryCheck;
	$check->checkQuery( $result, $query );
			
	if( !empty( $check->query ) ) {
		$check->message( "database entry", $action_type );
		$check->display();
	}
	
	?>

	<span class="fontTitle">Database Entry Management</span><br /><br />
	
	The database feature in SMS 2 allows for the creation of an easy-to-manage list of important links, both on-site and off-site, as well as the option to create a database entry for those things that don&rsquo;t require a complete new page created.
	
	<?php if(in_array("m_database2", $sessionAccess)) { ?>
	<strong class="yellow">Note:</strong> admins can give and take access to the database feature through the Department management page.
	<?php } ?>
	<br /><br />
	
	If you want to create an entry that uses extensive HTML or any PHP, please create a new SMS page and use an on-site URL forwarding entry.  The database feature will display basic HTML, but does not support extensive use of HTML code in the entry.  For off-site URL forwarding entries, give the full URL (e.g. http://www.something.com/), for on-site URL forwarding entries only give what comes after the location of SMS (e.g. index.php?page=manifest).  For reference, your web location is: <b><?=$webLocation;?></b><br /><br />
	
	<?php if( (!in_array("m_database2", $sessionAccess) && in_array($myDept, $arrayKeys)) || in_array("m_database2", $sessionAccess)) { ?>
	<a href="#" rel="facebox" myAction="add" class="fontMedium add"><strong>Create New Database Entry &raquo;</strong></a>
	<br /><br />
	<?php } ?>
	
	<?php
	
	if(!in_array("m_database2", $sessionAccess) && !in_array($myDept, $departments))
	{
		echo "<strong class='fontMedium orange'>Your department does not have permission to use the SMS database feature. Please contact the CO to gain access to the departmental database feature.</strong>";
	}
	else
	{
	
		foreach($database as $k1 => $v1)
		{
			$getD = "SELECT deptName, deptColor FROM sms_departments WHERE deptid = $k1";
			$getDResult = mysql_query($getD);
			$deptX = mysql_fetch_row($getDResult);
		
			/* if it is not the 0 entry, set the dept name and color accordingly */
			if($k1 > 0)
			{
				$d_name = $deptX[0];
				$d_color = $deptX[1];
			}
			else
			{
				$d_name = "Global Entries";
				$d_color = "ffffff";
			}
	
		?>
		<table class="zebra" cellpadding="3" cellspacing="0">
			<tr class="table_head">
				<td colspan="4">
					<strong class="fontMedium" style="color:#<?=$d_color;?>;"><?=$d_name;?></strong>
				</td>
			</tr>
		<?php
	
			foreach($database[$k1] as $k2 => $v2)
			{
	
		?>
			<tr class="fontNormal">
				<td><? printText( $v2['title'] ); ?></td>
				<td align="center" width="10%">
					<?php
				
					switch($v2['type'])
					{
						case 'entry':
							echo "<strong><a href='" . $webLocation . "index.php?page=database&entry=" . $v2['id'] . "'>";
							break;
						case 'onsite':
							echo "<strong><a href='" . $webLocation . $dbURL . "'>";
							break;
						case 'offsite':
							echo "<strong><a href='" . $dbURL . "'>";
							break;
					}
				
					echo "View</a></strong>";
				
					?>
				</td>
				<td align="center" width="10%">
					<strong><a href="#" rel="facebox" myAction="edit" myID="<?=$v2['id'];?>" class="edit">Edit</a></strong>
				</td>
				<td align="center" width="10%">
					<strong><a href="#" rel="facebox" myAction="delete" myID="<?=$v2['id'];?>" class="delete">Delete</a></strong>
				</td>
			</tr>
		<?php } ?>
	
		</table><br />	
		<?php } ?>
	<?php } ?>
	
</div>

<? } else { errorMessage( "database management" ); } ?>