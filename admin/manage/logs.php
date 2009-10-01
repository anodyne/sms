<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/logs.php
Purpose: If there is an ID in the URL, the page will display the personal log
	with that ID number, otherwise, it'll display a list of the 25 most recent
	personal logs for moderation

System Version: 2.6.0
Last Modified: 2008-04-17 2259 EST
**/

/* access check */
if(in_array("m_logs1", $sessionAccess) || in_array("m_logs2", $sessionAccess))
{
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_GET['id'])) {
		if(is_numeric($_GET['id'])) {
			$id = $_GET['id'];
		} else {
			errorMessageIllegal( "personal log editing page" );
			exit();
		}
	}
	
	if(isset($_GET['remove'])) {
		if(is_numeric($_GET['remove'])) {
			$remove = $_GET['remove'];
		} else {
			errorMessageIllegal( "personal log editing page" );
			exit();
		}
	}
	
	if(isset($_POST['action_update_x']))
	{
		if(!in_array("m_logs2", $sessionAccess))
		{
			$update = "UPDATE sms_personallogs SET logTitle = %s, logContent = %s WHERE logid = $id";
			$query = sprintf(
				$update,
				escape_string($_POST['logTitle']),
				escape_string($_POST['logContent'])
			);
		}
		else
		{
			$update = "UPDATE sms_personallogs SET logAuthor = %d, logTitle = %s, logContent = %s, logStatus = %s WHERE logid = $id";
			$query = sprintf(
				$update,
				escape_string($_POST['logAuthor']),
				escape_string($_POST['logTitle']),
				escape_string($_POST['logContent']),
				escape_string($_POST['logStatus'])
			);
		}
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "update";
	}
	elseif(isset($_POST['action_delete_x']))
	{
		$query = "DELETE FROM sms_personallogs WHERE logid = $id LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "delete";
	}
	elseif(isset($remove))
	{
		$query = "DELETE FROM sms_personallogs WHERE logid = $remove LIMIT 1";
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_personallogs" );
		
		$action = "delete";
	}
	
	if(isset($id))
	{
	
?>
	<div class="body">
		<?php

		$check = new QueryCheck;
		$check->checkQuery( $result, $query );

		if( !empty( $check->query ) ) {
			$check->message( "personal log", $action );
			$check->display();
		}
		
		?>
		<span class="fontTitle">Manage Personal Log</span><br /><br />
		<?php if(in_array("m_logs2", $sessionAccess)) { ?>
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=logs"><strong class="fontMedium">&laquo; Back to Personal Logs</strong></a>
		<br /><br />
		<?php } ?>
		
		<?php if(!in_array("m_logs2", $sessionAccess)) { ?>
		<strong class="orange fontMedium">You are allowed to edit the title and content of your personal log. If you want to change the author or status, please contact the CO.</strong><br /><br />
		<?php } ?>
		
		<table cellpadding="2" cellspacing="2">
		<?
		
			$logs = "SELECT * FROM sms_personallogs WHERE logid = $id";
			$logsResult = mysql_query( $logs );
			
			while( $logFetch = mysql_fetch_assoc( $logsResult ) ) {
				extract( $logFetch, EXTR_OVERWRITE );
		
		?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=logs&id=<?=$id;?>">
			<tr>
				<td valign="middle" colspan="2">
					<b>Title</b><br />
					<input type="text" class="name" name="logTitle" maxlength="100" value="<?=print_input_text( $logTitle );?>" />
					<input type="hidden" name="logid" value="<?=$logid;?>" />
				</td>
			</tr>
			
			<?php if(in_array("m_logs2", $sessionAccess)) { ?>
			<tr>
				<td valign="middle">
					<b>Author</b><br />
					<?php print_active_crew_select_menu( "log", $logAuthor, $logid, "", "" ); ?>
				</td>
				<td valign="middle">
					<span class="fontNormal"><b>Status</b></span><br />
					<select name="logStatus">
						<option value="pending"<? if( $logStatus == "pending" ) { echo " selected"; } ?>>Pending</option>
						<option value="saved"<? if( $logStatus == "saved" ) { echo " selected"; } ?>>Saved</option>
						<option value="activated"<? if( $logStatus == "activated" ) { echo " selected"; } ?>>Activated</option>
					</select>
				</td>
			</tr>
			<?php } ?>
				
			<tr>
				<td colspan="2">
					<b>Content</b><br />
					<textarea name="logContent" class="wideTextArea" rows="15"><?=stripslashes( $logContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="20"></td>
			</tr>
			<tr>
				<td colspan="2" align="right">
					<input type="hidden" name="logid" value="<?=$logid;?>" />
	
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this personal log?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
	
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
				</td>
			</tr>
			</form>
		<? } ?>
		</table>
	</div>
	<?
		
	}
	elseif(!isset($id) && in_array("m_logs2", $sessionAccess))
	{
	
		$posts_array = array(
			'activated' => array(),
			'saved' => array(),
			'pending' => array()
		);
		
		$getPostsA = "SELECT logid, logTitle, logAuthor FROM sms_personallogs WHERE logStatus = 'activated' ORDER BY logPosted DESC LIMIT 25";
		$getPostsAR = mysql_query($getPostsA);
		
		$getPostsS = "SELECT logid, logTitle, logAuthor FROM sms_personallogs WHERE logStatus = 'saved' ORDER BY logPosted DESC";
		$getPostsSR = mysql_query($getPostsS);
		
		$getPostsP = "SELECT logid, logTitle, logAuthor FROM sms_personallogs WHERE logStatus = 'pending' ORDER BY logPosted DESC";
		$getPostsPR = mysql_query($getPostsP);
		
		while($fetch_a = mysql_fetch_array($getPostsAR)) {
			extract($fetch_a, EXTR_OVERWRITE);
			
			$posts_array['activated'][] = array('id' => $fetch_a[0], 'title' => $fetch_a[1], 'author' => $fetch_a[2]);
		}
		
		while($fetch_s = mysql_fetch_array($getPostsSR)) {
			extract($fetch_s, EXTR_OVERWRITE);
			
			$posts_array['saved'][] = array('id' => $fetch_s[0], 'title' => $fetch_s[1], 'author' => $fetch_s[2]);
		}
		
		while($fetch_p = mysql_fetch_array($getPostsPR)) {
			extract($fetch_p, EXTR_OVERWRITE);
			
			$posts_array['pending'][] = array('id' => $fetch_p[0], 'title' => $fetch_p[1], 'author' => $fetch_p[2]);
		}
	
	?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#container-1 > ul').tabs();
			$('.zebra tr:nth-child(even)').addClass('alt');
		});
	</script>
	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "personal log", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage Personal Logs</span><br /><br />
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Activated</span></a></li>
				<li><a href="#two"><span>Saved (<?php echo count($posts_array['saved']);?>)</span></a></li>
				<li><a href="#three"><span>Pending (<?php echo count($posts_array['pending']);?>)</span></a></li>
			</ul>
	
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<?
				
				if(count($posts_array['activated']) == 0)
				{
					echo "<strong class='fontLarge orange'>No activated personal logs found</strong>";
				}
				else
				{
				
				?>
				
				<table class="zebra" cellpadding="3" cellspacing="0">
					<tr class="fontMedium">
						<th width="34%">Title</th>
						<th width="44%">Author</th>
						<th width="2%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
					
					<?php foreach($posts_array['activated'] as $value_a) { ?>
					<tr class="fontNormal">
						<td><? printText($value_a['title']);?></td>
						<td><? printCrewName($value_a['author'], 'rank', 'link');?></td>
						<td></td>
						<td align="center"><a href="<?=$webLocation;?>index.php?page=log&id=<?=$value_a['id'];?>"><strong>View Log</strong></a></td>
						<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=logs&id=<?=$value_a['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>
					<?php } ?>
				</table>
				
				<? } ?>
			</div>
		
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<?
			
				if(count($posts_array['saved']) == 0)
				{
					echo "<strong class='fontLarge orange'>No saved personal logs found</strong>";
				}
				else
				{
			
				?>
				<table class="zebra" cellpadding="3" cellspacing="0">
					<tr class="fontMedium">
						<th width="34%">Title</th>
						<th width="44%">Author</th>
						<th width="2%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
				
					<?php foreach($posts_array['saved'] as $value_s) { ?>
					<tr class="fontNormal">
						<td><? printText($value_s['title']);?></td>
						<td><? printCrewName($value_s['author'], 'rank', 'link');?></td>
						<td></td>
						<td align="center"><a href="<?=$webLocation;?>index.php?page=log&id=<?=$value_s['id'];?>"><strong>View Log</strong></a></td>
						<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=logs&id=<?=$value_s['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>
					<?php } ?>
				</table>
				<?php } ?>
			</div>
		
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<?
			
				if(count($posts_array['pending']) == 0)
				{
					echo "<strong class='fontLarge orange'>No pending personal logs found</strong>";
				}
				else
				{
			
				?>
				<table class="zebra" cellpadding="3" cellspacing="0">
					<tr class="fontMedium">
						<th width="34%">Title</th>
						<th width="44%">Author</th>
						<th width="2%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
				
					<?php foreach($posts_array['pending'] as $value_p) { ?>
					<tr class="fontNormal">
						<td><? printText($value_p['title']);?></td>
						<td><? printCrewName($value_p['author'], 'rank', 'link');?></td>
						<td></td>
						<td align="center"><a href="<?=$webLocation;?>index.php?page=log&id=<?=$value_p['id'];?>"><strong>View Log</strong></a></td>
						<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=logs&id=<?=$value_p['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>
					<?php } ?>
				</table>
				<?php } ?>
			</div>
		</div> <!-- close #container-1 -->
	</div>
	
<?php

	}
	elseif(!isset($id) && !in_array("m_logs2", $sessionAccess))
	{
		echo "<div class='body'>";
		echo "<strong class='fontMedium orange'>You do not have permission to edit personal logs other than your own!</strong>";
		echo "</div>";
	}
}
else
{
	errorMessage( "personal log management" );
}

?>