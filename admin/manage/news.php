<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/news.php
Purpose: If there is an ID in the URL, it will pull the corresponding news item
	for editing, otherwise, it'll just show a list of the last 4 news items for
	moderation

System Version: 2.6.0
Last Modified: 2008-04-17 2306 EST
**/

/* access check */
if(in_array("m_news", $sessionAccess))
{
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_GET['id']) && is_numeric($_GET['id'])) {
		$id = $_GET['id'];
	} else {
		$id = NULL;
	}
	
	if(isset($_GET['remove']) && is_numeric($_GET['remove'])) {
		$remove = $_GET['remove'];
	} else {
		$remove = NULL;
	}
	
	if(isset($_POST['action_update_x']))
	{
		$update = "UPDATE sms_news SET newsCat = %d, newsAuthor = %d, newsTitle = %s, newsContent = %s, newsStatus = %s, ";
		$update.= "newsPrivate = %s WHERE newsid = $id LIMIT 1";
		
		$query = sprintf(
			$update,
			escape_string($_POST['newsCat']),
			escape_string($_POST['newsAuthor']),
			escape_string($_POST['newsTitle']),
			escape_string($_POST['newsContent']),
			escape_string($_POST['newsStatus']),
			escape_string($_POST['newsPrivate'])
		);
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "update";
	}
	elseif(isset($_POST['action_delete_x']))
	{
		$query = "DELETE FROM sms_news WHERE newsid = $id LIMIT 1";
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "delete";
	}
	elseif(isset($remove))
	{
		$query = "DELETE FROM sms_news WHERE newsid = $remove LIMIT 1";
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_news" );
		
		$action = "delete";
	}
	
	if(isset($id))
	{

?>

	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "news item", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage News Item</span><br /><br />
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=news" class="fontMedium"><strong>&laquo; Back to News Items</strong></a><br /><br />
		
		<table cellpadding="2" cellspacing="2">
		<?
		
			$news = "SELECT * FROM sms_news WHERE newsid = '$id'";
			$newsResult = mysql_query( $news );
			
			while( $newsFetch = mysql_fetch_assoc( $newsResult ) ) {
				extract( $newsFetch, EXTR_OVERWRITE );
		
		?>
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=news&id=<?=$id;?>">
			<tr>
				<td>
					<b>Title</b><br />
					<input type="text" class="name" name="newsTitle" maxlength="100" value="<?=print_input_text( $newsTitle );?>" />
				</td>
				<td colspan="2">
					<b>Category</b><br />
					<select name="newsCat">
					<?
					
					$cats = "SELECT * FROM sms_news_categories ORDER BY catid ASC";
					$catsResult = mysql_query( $cats );
					
					while( $catFetch = mysql_fetch_assoc( $catsResult ) ) {
						extract( $catFetch, EXTR_OVERWRITE );
							
						if( $newsCat == $catid ) {
							echo "<option value='$newsCat' selected>" . stripslashes( $catName ) . "</option>";
						} else {
							echo "<option value='$catid'>" . stripslashes( $catName ) . "</option>";
						}
					}
					
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b>Author</b><br />
					<? print_active_crew_select_menu( "news", $newsAuthor, "", "", "" ); ?>
				</td>
				<td>
					<b>Status</b><br />
					<select name="newsStatus">
						<option value="pending"<? if( $newsStatus == "pending" ) { echo " selected"; } ?>>Pending</option>
						<option value="saved"<? if( $newsStatus == "saved" ) { echo " selected"; } ?>>Saved</option>
						<option value="activated"<? if( $newsStatus == "activated" ) { echo " selected"; } ?>>Activated</option>
					</select>
				</td>
				<td>
					<b>Privacy Status</b><br />
					<select name="newsPrivate">
						<option value="n"<? if( $newsPrivate == "n" ) { echo " selected"; } ?>>Public</option>
						<option value="y"<? if( $newsPrivate == "y" ) { echo " selected"; } ?>>Private</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<b>Content</b><br />
					<textarea name="newsContent" class="wideTextArea" rows="15"><?=stripslashes( $newsContent );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3" align="right">
					<input type="hidden" name="newsid" value="<?=$newsid;?>" />
	
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this news item?')\" />" );
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
	elseif(!isset($id))
	{
	
		$posts_array = array(
			'activated' => array(),
			'saved' => array(),
			'pending' => array()
		);

		$getPostsA = "SELECT newsid, newsTitle, newsAuthor FROM sms_news WHERE newsStatus = 'activated' ORDER BY newsPosted DESC LIMIT 25";
		$getPostsAR = mysql_query($getPostsA);

		$getPostsS = "SELECT newsid, newsTitle, newsAuthor FROM sms_news WHERE newsStatus = 'saved' ORDER BY newsPosted DESC";
		$getPostsSR = mysql_query($getPostsS);

		$getPostsP = "SELECT newsid, newsTitle, newsAuthor FROM sms_news WHERE newsStatus = 'pending' ORDER BY newsPosted DESC";
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
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "news item", $action );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Manage News Items</span><br /><br />
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
					echo "<strong class='fontLarge orange'>No activated news items found</strong>";
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
						<td align="center"><a href="<?=$webLocation;?>index.php?page=news&id=<?=$value_a['id'];?>"><strong>View News</strong></a></td>
						<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=news&id=<?=$value_a['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>
					<?php } ?>
				</table>
				
				<? } ?>
			</div>
		
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<?
			
				if(count($posts_array['saved']) == 0)
				{
					echo "<strong class='fontLarge orange'>No saved news items found</strong>";
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
						<td align="center"><a href="<?=$webLocation;?>index.php?page=news&id=<?=$value_s['id'];?>"><strong>View News</strong></a></td>
						<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=news&id=<?=$value_s['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>
					<?php } ?>
				</table>
				<?php } ?>
			</div>
		
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<?
			
				if(count($posts_array['pending']) == 0)
				{
					echo "<strong class='fontLarge orange'>No pending news items found</strong>";
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
						<td align="center"><a href="<?=$webLocation;?>index.php?page=news&id=<?=$value_p['id'];?>"><strong>View News</strong></a></td>
						<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=news&id=<?=$value_p['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>
					<?php } ?>
				</table>
				<?php } ?>
			</div>
		</div> <!-- close #container-1 -->
	</div>
	
<? } } else { errorMessage( "news item moderation" ); } ?>