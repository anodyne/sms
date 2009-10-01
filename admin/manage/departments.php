<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/departments.php
Purpose: Page that moderates the simm departments

System Version: 2.6.0
Last Modified: 2008-06-01 1052 EST
**/

/* access check */
if( in_array( "m_departments", $sessionAccess ) ) {

	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_POST['action_update_x']))
	{
		if(isset($_POST['deptid']) && is_numeric($_POST['deptid']))
		{
			$deptid = $_POST['deptid'];
		}
		else
		{
			$deptid = FALSE;
			exit();
		}
		
		if(isset($_POST['deptColor']))
		{
			$color = $_POST['deptColor'];
		}
		
		if($color{0} == '#')
		{
			$color = str_replace('#', '', $color);
		}
		
		$update = "UPDATE sms_departments SET deptName = %s, deptClass = %d, deptOrder = %d, deptColor = '%s', deptDisplay = %s, ";
		$update.= "deptDesc = %s, deptType = %s WHERE deptid = $deptid LIMIT 1";
		
		$query = sprintf(
			$update,
			escape_string($_POST['deptName']),
			escape_string($_POST['deptClass']),
			escape_string($_POST['deptOrder']),
			$color,
			escape_string($_POST['deptDisplay']),
			escape_string($_POST['deptDesc']),
			escape_string($_POST['deptType'])
		);
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_departments" );
		
		$object = "department";
		$action = "update";
	}
	elseif(isset($_POST['action_type']) && $_POST['action_type'] == "create")
	{	
		$insert = "INSERT INTO sms_departments (deptName, deptClass, deptOrder, deptColor, deptDisplay, deptDesc, deptType) ";
		$insert.= "VALUES (%s, %d, %d, '%s', %s, %s, %s)";
		
		if(isset($_POST['deptColor']))
		{
			$color = $_POST['deptColor'];
		}
		
		if($color{0} == '#')
		{
			$color = str_replace('#', '', $color);
		}
		
		$query = sprintf(
			$insert,
			escape_string($_POST['deptName']),
			escape_string($_POST['deptClass']),
			escape_string($_POST['deptOrder']),
			$color,
			escape_string($_POST['deptDisplay']),
			escape_string($_POST['deptDesc']),
			escape_string($_POST['deptType'])
		);
		
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_departments" );
		
		$object = "department";
		$action = "create";
	}
	elseif(isset($_POST['action_type']) && $_POST['action_type'] == "database")
	{
		foreach($_POST as $a => $b)
		{
			/* only use the items that start with DEPT_ */
			if(substr($a, 0, 5) == "dept_")
			{
				$id = substr_replace($a, '', 0, 5);
				$value = $b;
				
				/* if the values are what we expect, do the query */
				if(is_numeric($id) && ($value == "y" || $value == "n"))
				{
					$query = "UPDATE sms_departments SET deptDatabaseUse = '$value' WHERE deptid = $id";
					$result = mysql_query($query);
				}
			}
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_departments" );
		
		$object = "departmental database access";
		$action = "update";
	}
	elseif(isset($_POST['action_delete_x']))
	{
		if(isset($_POST['deptid']) && is_numeric($_POST['deptid']))
		{
			$deptid = $_POST['deptid'];
		}
		else
		{
			$deptid = FALSE;
			exit();
		}
		
		/* do the delete query */
		$query = "DELETE FROM sms_departments WHERE deptid = $deptid LIMIT 1";
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_departments" );
		
		$object = "department";
		$action = "delete";
	}

?>
<script type="text/javascript">
	$(document).ready(function() {
		$("a[rel*=facebox]").click(function() {
			var action = $(this).attr("myAction");
			
			jQuery.facebox(function() {
				jQuery.get('admin/ajax/department_' + action + '.php', function(data) {
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
			$check->message( $object, $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Department Management</span><br /><br />
		Use the fields below to manage your departments. You can add a new department using the appropriate link below, or, you can manage which departments have access to departmental databases from the appropriate link. <strong class="yellow">Important</strong> &ndash; if you want to create a new rank, you will need to create a new department before you attempt to create the rank. (You can also use an existing department as well.)<br /><br />
		
		<a href="#" rel="facebox" class="fontMedium add" myAction="add"><strong>Add New Department &raquo;</strong></a><br />
		<a href="#" rel="facebox" class="fontMedium add" myAction="database"><strong>Update Departmental Database Access &raquo;</strong></a><br /><br />
	
		<table>
			<?
			
			/* get the departments from the database */
			$getDepartments = "SELECT * FROM sms_departments ORDER BY deptOrder ASC";
			$getDepartmentsResult = mysql_query( $getDepartments );
			
			/* loop through the results and fill the form */
			while( $deptFetch = mysql_fetch_assoc( $getDepartmentsResult ) ) {
				extract( $deptFetch, EXTR_OVERWRITE );
			
			?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=departments">
			<tr>
				<td colspan="5" valign="top">
					<span class="fontNormal"><b>Department</b></span><br />
					<input type="text" class="name" name="deptName" value="<?=print_input_text( $deptName );?>" />
				</td>
				<td width="75%" rowspan="2" align="center" valign="top">
					<span class="fontNormal"><b>Description</b></span><br />
					<textarea name="deptDesc" rows="4" class="desc"><?=stripslashes( $deptDesc );?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<span class="fontNormal"><b>Class</b></span><br />
					<input type="text" class="class" name="deptClass" maxlength="3" value="<?=$deptClass;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Order</b></span><br />
					<input type="text" class="order" name="deptOrder" maxlength="3" value="<?=$deptOrder;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Color</b></span><br />
                    <input type="text" class="color" style="color:#<?=$deptColor;?>" name="deptColor" maxlength="7" value="<?=$deptColor;?>" />
				</td>
				<td>
					<span class="fontNormal"><b>Dept Type</b></span><br />
                    <select name="deptType">
                    	<option value="playing"<? if( $deptType == "playing" ) { echo " selected"; } ?>>Playing Dept</option>
                    	<option value="nonplaying"<? if( $deptType == "nonplaying" ) { echo " selected"; } ?>>Non-Playing Dept</option>
                    </select>
				</td>
			    <td>
			    	<span class="fontNormal"><b>Display?</b></span><br />
                    <select name="deptDisplay">
                    	<option value="y"<? if( $deptDisplay == "y" ) { echo " selected"; } ?>>Yes</option>
                    	<option value="n"<? if( $deptDisplay == "n" ) { echo " selected"; } ?>>No</option>
                    </select>
				</td>
			</tr>
			<tr>
				<td colspan="5" align="right">&nbsp;</td>
	            <td align="center">
	            	<input type="hidden" name="deptid" value="<?=$deptid;?>" />
                    <script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this department?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" />
				</td>
			</tr>
			<tr>
				<td colspan="6" height="25">&nbsp;</td>
			</tr>
			</form>
			<? } ?>
		</table>
	</div>

<? } else { errorMessage( "department management" ); } ?>