<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/newscategories.php
Purpose: Page that moderates the news categories

System Version: 2.6.0
Last Modified: 2008-04-22 1909 EST
**/

/* access check */
if(in_array("m_newscat3", $sessionAccess))
{
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_POST['action_update_x']))
	{
		if(isset($_POST['catid']) && is_numeric($_POST['catid'])) {
			$catid = $_POST['catid'];
		} else {
			$catid = NULL;
		}
		
		$update = "UPDATE sms_news_categories SET catName = %s, catUserLevel = %d, catVisible = %s WHERE catid = $catid LIMIT 1";
		
		$query = sprintf(
			$update,
			escape_string($_POST['catName']),
			escape_string($_POST['catUserLevel']),
			escape_string($_POST['catVisible'])
		);
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_news_categories" );
		
		$action = "update";
	}
	elseif(isset($_POST['action_create_x']))
	{
		$insert = "INSERT INTO sms_news_categories (catName, catUserLevel, catVisible) VALUES (%s, %d, %s)";
		
		$query = sprintf(
			$insert,
			escape_string($_POST['catName']),
			escape_string($_POST['catUserLevel']),
			escape_string($_POST['catVisible'])
		);
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_news_categories" );
		
		$action = "create";
	}
	elseif(isset($_POST['action_delete_x']))
	{
		if(isset($_POST['catid']) && is_numeric($_POST['catid'])) {
			$catid = $_POST['catid'];
		} else {
			$catid = NULL;
		}
		
		$query = "DELETE FROM sms_news_categories WHERE catid = $catid LIMIT 1";
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_news_categories" );
		
		$action = "delete";
	}

?>

	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery($result, $query);
		
		if(!empty($check->query))
		{
			$check->message("news category", $action);
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Create Site News Category</span><br /><br />
		
		<table cellpadding="2" cellspacing="2">
			<form method="post" action="admin.php?page=manage&sub=newscategories">
			<tr>
				<td valign="middle">
					<b>Category Name</b><br />
					<input type="text" class="name" name="catName" maxlength="50" />
				</td>
				<td valign="middle">
					<b>Required Access Level</b><br />
					<select name="catUserLevel">
						<option value="1">General User</option>
						<option value="2">Power User</option>
						<option value="3">Admin</option>
					</select>
				</td>
				<td valign="middle">
					<b>Show Category?</b><br />
					<input type="radio" id="visY" name="catVisible" value="y" checked="yes" /> <label for="visY">Yes</label>
					<input type="radio" id="visN" name="catVisible" value="n" /> <label for="visN">No</label>
				</td>
				<td valign="middle" align="right" style="width:125px;">
					<input type="image" src="<?=path_userskin;?>buttons/create.png" class="button" name="action_create" value="Create" />
				</td>
			</tr>
			</form>
		</table>
		
		<br /><br />
			
		<span class="fontTitle">Manage Site News Categories</span><br /><br />
			
		<table cellpadding="2" cellspacing="2">
		<?
			
		/* pull the categories from the db */
		$newsCategories = "SELECT * FROM sms_news_categories ORDER BY catid ASC";
		$newsCategoriesResult = mysql_query( $newsCategories );
		
		/* loop through the results and fill in the form */
		while( $categories = mysql_fetch_assoc( $newsCategoriesResult ) ) {
			extract( $categories, EXTR_OVERWRITE );
		
		?>
			<form method="post" action="admin.php?page=manage&sub=newscategories">
			<tr>
				<td valign="middle">
					<b>Category Name</b><br />
					<input type="text" class="name" name="catName" maxlength="50" value="<?=print_input_text( $catName );?>" />
				</td>
				<td valign="middle">
					<b>Required Access Level</b><br />
					<select name="catUserLevel">
						<option value="1" <? if( $catUserLevel == 1 ) { echo "selected"; } ?>>General User</option>
						<option value="2" <? if( $catUserLevel == 2 ) { echo "selected"; } ?>>Power User</option>
						<option value="3" <? if( $catUserLevel == 3 ) { echo "selected"; } ?>>Admin</option>
					</select>
				</td>
				<td valign="middle">
					<b>Show Category?</b><br />
					<input type="radio" id="visibleY" name="catVisible" value="y" <? if( $catVisible == "y" ) { echo "checked"; } ?>/> <label for="visibleY">Yes</label>
					<input type="radio" id="visibleN" name="catVisible" value="n" <? if( $catVisible == "n" ) { echo "checked"; } ?>/> <label for="visibleN">No</label>
				</td>
				<td valign="middle" align="right">
					<input type="hidden" name="catid" value="<?=$catid;?>" />
					<input type="image" src="<?=path_userskin;?>buttons/update.png" class="button" name="action_update" value="Update" /><br />
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this news category?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" class="button" name="action_delete" value="Delete" />
					</noscript>
				</td>
			</tr>
			<tr>
				<td colspan="4" height="25"></td>
			</tr>
			</form>
		<? } /* close the $categories while loop */ ?>
		</table>
	</div>
	
<? } else { errorMessage( "news category management" ); } ?>