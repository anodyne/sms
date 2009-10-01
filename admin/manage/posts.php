<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/posts.php
Purpose: Page that moderates the mission posts

System Version: 2.6.10
Last Modified: 2009-09-16 1936 EST
**/

/* access check */
if(in_array("m_posts1", $sessionAccess) || in_array("m_posts2", $sessionAccess))
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
			errorMessageIllegal( "post management page" );
			exit();
		}
	}
	
	if(isset($_GET['remove'])) {
		if(is_numeric($_GET['remove'])) {
			$remove = $_GET['remove'];
		} else {
			errorMessageIllegal( "post management page" );
			exit();
		}
	}
	
	if(isset($_GET['delete'])) {
		if(is_numeric($_GET['delete'])) {
			$delete = $_GET['delete'];
		} else {
			errorMessageIllegal( "post management page" );
			exit();
		}
	}
	
	if(isset($_GET['add'])) {
		if(is_numeric($_GET['add'])) {
			$add = $_GET['add'];
		} else {
			errorMessageIllegal( "post management page" );
			exit();
		}
	}
	
	if(isset($_POST['authorCount'])) {
		$count = $_POST['authorCount'];
	} else {
		$count = FALSE;
	}
	
	$authors_array = array();
	
	for($i = 0; $i < JP_AUTHORS; $i++)
	{
		if(isset($_POST['postAuthor' . $i])) {
			$authors_array[] = $_POST['postAuthor' . $i];
		}
	}
	
	if(count($authors_array) > 0) {
		$postAuthor = implode(',', $authors_array);
	} else {
		$postAuthor = FALSE;
	}
	
	if(isset($_POST['action_update_x']))
	{
		if(isset($_POST['postid']) && is_numeric($_POST['postid'])) {
			$postid = $_POST['postid'];
		} else {
			$postid = FALSE;
		}
		
		if(!in_array("m_posts2", $sessionAccess))
		{
			$update = "UPDATE sms_posts SET postTitle = %s, postLocation = %s, postTimeline = %s, ";
			$update.= "postAuthor = %s, postTag = %s, postContent = %s WHERE postid = $postid LIMIT 1";
		
			$query = sprintf(
				$update,
				escape_string($_POST['postTitle']),
				escape_string($_POST['postLocation']),
				escape_string($_POST['postTimeline']),
				escape_string($postAuthor),
				escape_string($_POST['postTag']),
				escape_string($_POST['postContent'])
			);
		}
		else
		{
			$update = "UPDATE sms_posts SET postTitle = %s, postLocation = %s, postTimeline = %s, ";
			$update.= "postAuthor = %s, postContent = %s, postStatus = %s, postMission = %d, postTag = %s ";
			$update.= "WHERE postid = $postid LIMIT 1";
		
			$query = sprintf(
				$update,
				escape_string($_POST['postTitle']),
				escape_string($_POST['postLocation']),
				escape_string($_POST['postTimeline']),
				escape_string($postAuthor),
				escape_string($_POST['postContent']),
				escape_string($_POST['postStatus']),
				escape_string($_POST['postMission']),
				escape_string($_POST['postTag'])
			);
		}
		
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "update";
	}
	elseif(isset( $_POST['action_delete_x']))
	{
		$query = "DELETE FROM sms_posts WHERE postid = $id LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "delete";
	}
	elseif(isset($remove))
	{
		$query = "DELETE FROM sms_posts WHERE postid = '$remove' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "delete";
	}
	elseif(isset($delete))
	{
		if(isset($_GET))
		{
			if(is_numeric($_GET['postid'])) {
				$postid = $_GET['postid'];
			} else {
				$postid = FALSE;
			}
			
			if(is_numeric($_GET['delete'])) {
				$arrayid = $_GET['delete'];
			} else {
				$arrayid = FALSE;
			}
		}
		
		/* pull the authors for the specific post */
		$getAuthors = "SELECT postAuthor FROM sms_posts WHERE postid = '$postid' LIMIT 1";
		$getAuthorsResult = mysql_query( $getAuthors );
		
		while( $authorAdjust = mysql_fetch_assoc( $getAuthorsResult ) ) {
			extract( $authorAdjust, EXTR_OVERWRITE );
		}
		
		/* create the new array */
		$authorArray = explode( ",", $postAuthor );
		unset( $authorArray[$arrayid] );
		$authorArray = array_values( $authorArray );
		$newAuthors = implode( ",", $authorArray );
		
		/* update the post */
		$query = "UPDATE sms_posts SET postAuthor = '$newAuthors' WHERE postid = '$postid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "remove";
	}
	elseif(isset($add))
	{
		if(isset($_GET))
		{
			if(is_numeric($_GET['postid'])) {
				$postid = $_GET['postid'];
			} else {
				$postid = FALSE;
			}
			
			if(is_numeric($_GET['add'])) {
				$arrayid = $_GET['add'];
			} else {
				$arrayid = FALSE;
			}
		}
		
		/* pull the authors for the specific post */
		$getAuthors = "SELECT postAuthor FROM sms_posts WHERE postid = $postid LIMIT 1";
		$getAuthorsResult = mysql_query( $getAuthors );
		
		while( $authorAdjust = mysql_fetch_assoc( $getAuthorsResult ) ) {
			extract( $authorAdjust, EXTR_OVERWRITE );
		}
		
		/* create the new array */
		$authorArray = explode( ",", $postAuthor );
		$authorArray[] = 0;
		$newAuthors = implode( ",", $authorArray );
		
		/* update the post */
		$query = "UPDATE sms_posts SET postAuthor = '$newAuthors' WHERE postid = '$postid' LIMIT 1";
		$result = mysql_query( $query );
		
		/* optimize the table */
		optimizeSQLTable( "sms_posts" );
		
		$action = "add";
	}
	
	/* if there's an id in the URL, proceed */
	if(isset($id))
	{
		$posts = "SELECT * FROM sms_posts WHERE postid = $id LIMIT 1";
		$postsResult = mysql_query($posts);
		$fetch = mysql_fetch_assoc($postsResult);
		$tempAuthors = explode(",", $fetch['postAuthor']);
		
		if(
			in_array("m_posts2", $sessionAccess) ||
			(!in_array("m_posts2", $sessionAccess) && in_array($sessionCrewid, $tempAuthors))
		) {
			
?>

	<div class="body">
	
		<?php
		
		/* do logic to make sure the object is right */
		if(isset($add) || isset($delete))
		{
			$object = "author";
		}
		elseif(isset($_POST['action_update_x']) || isset($_POST['action_delete_x']) || isset($remove))
		{
			$object = "post";
		}
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( $object, $action );
			$check->display();
		}
		
		?>
	
		<span class="fontTitle">Manage Mission Post</span><br /><br />
		
		<?php if(in_array("m_posts2", $sessionAccess)) { ?>
		<a href="<?=$webLocation;?>admin.php?page=manage&sub=posts"><strong class="fontMedium">&laquo; Back to Mission Posts</strong></a>
		<br /><br />
		<?php } ?>
		
		<table cellpadding="0" cellspacing="3">
			<form method="post" action="<?=$webLocation;?>admin.php?page=manage&sub=posts&id=<?=$id;?>">
			<tr>
				<td>
					<b>Post Title</b><br />
					<input type="text" class="name" maxlength="100" name="postTitle" value="<?=print_input_text( $fetch['postTitle'] );?>" />
				</td>
				<td>
					<b>Location</b><br />
					<input type="text" class="name" maxlength="100" name="postLocation" value="<?=print_input_text( $fetch['postLocation'] );?>" />
				</td>
			</tr>
			<tr>
				<td>
					<b>Tag</b><br />
					<input type="text" class="name" maxlength="100" name="postTag" value="<?=print_input_text( $fetch['postTag'] );?>" />
				</td>
				<td>
					<b>Timeline</b><br />
					<input type="text" class="name" maxlength="100" name="postTimeline" value="<?=print_input_text( $fetch['postTimeline'] );?>" />
				</td>
			</tr>
			<tr>
				<td valign="top" rowspan="2">
					<b>Author</b><br />
					<? $authorCount = print_active_crew_select_menu( "post", $fetch['postAuthor'], $fetch['postid'], "manage", "posts" ); ?>
					<input type="hidden" name="authorCount" value="<?=$authorCount;?>" />
				</td>
				<td>
					<?php if(in_array("m_posts2", $sessionAccess)) { ?>
					<b>Status</b><br />
					<select name="postStatus">
						<option value="pending"<? if( $fetch['postStatus'] == "pending" ) { echo " selected"; } ?>>Pending</option>
						<option value="saved"<? if( $fetch['postStatus'] == "saved" ) { echo " selected"; } ?>>Saved</option>
						<option value="activated"<? if( $fetch['postStatus'] == "activated" ) { echo " selected"; } ?>>Activated</option>
					</select>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td>
					<b>Mission</b><br />
					
					<?php if(in_array("m_posts2", $sessionAccess)) { ?>
					<select name="postMission">
						<?php
	
						$getMissions = "SELECT * FROM sms_missions WHERE missionStatus != 'upcoming' ORDER BY missionOrder DESC";
						$getMissionsResult = mysql_query( $getMissions );
	
						while($misFetch = mysql_fetch_assoc($getMissionsResult)) {
							extract($misFetch, EXTR_OVERWRITE);
	
						?>
	
						<option value="<?=$missionid;?>"<? if( $fetch['postMission'] == $missionid ) { echo " selected"; } ?>><? printText( $missionTitle ); ?></option>
	
						<?php } ?>
					</select>
					<?php
					
					} else {
						$getMissions = "SELECT * FROM sms_missions WHERE missionStatus = 'current' LIMIT 1";
						$getMissionsResult = mysql_query($getMissions);
						$fetchMission = mysql_fetch_assoc($getMissionsResult);
						
						echo "<em>";
						printText($fetchMission['missionTitle']);
						echo "</em>";
					}
						
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<b>Content</b><br />
					<textarea rows="15" name="postContent" class="wideTextArea"><?=stripslashes( $fetch['postContent'] );?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="15"></td>
			</tr>
			<tr>
				<td colspan="2" valign="top" align="right">
					<input type="hidden" name="postid" value="<?=$fetch['postid'];?>" />
	
					<script type="text/javascript">
						document.write( "<input type=\"image\" src=\"<?=path_userskin;?>buttons/delete.png\" name=\"action_delete\" value=\"Delete\" class=\"button\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this mission post?')\" />" );
					</script>
					<noscript>
						<input type="image" src="<?=path_userskin;?>buttons/delete.png" name="action_delete" value="Delete" class="button" />
					</noscript>
	
					&nbsp;&nbsp;
					<input type="image" src="<?=path_userskin;?>buttons/update.png" name="action_update" class="button" value="Update" />
				</td>
			</tr>
			</form>
		</table>
	</div>
	
	<?
	
		} /* closes the check for the author */
		elseif(!in_array("m_posts2", $sessionAccess) && !in_array($sessionCrewid, $tempAuthors))
		{
			echo "<div class='body'>";
			echo "<strong class='fontMedium orange'>You only have permission to edit your own posts!</strong>";
			echo "</div>";
		}
	}
	elseif(!isset($id) && in_array("m_posts2", $sessionAccess))
	{
	
		$posts_array = array(
			'activated' => array(),
			'saved' => array(),
			'pending' => array()
		);
		
		$getPostsA = "SELECT postid, postTitle, postPosted FROM sms_posts WHERE postStatus = 'activated' ORDER BY postPosted DESC LIMIT 20";
		$getPostsAR = mysql_query($getPostsA);
		
		$getPostsS = "SELECT postid, postTitle, postPosted FROM sms_posts WHERE postStatus = 'saved' ORDER BY postPosted DESC";
		$getPostsSR = mysql_query($getPostsS);
		
		$getPostsP = "SELECT postid, postTitle, postPosted FROM sms_posts WHERE postStatus = 'pending' ORDER BY postPosted DESC";
		$getPostsPR = mysql_query($getPostsP);
		
		while($fetch_a = mysql_fetch_array($getPostsAR)) {
			extract($fetch_a, EXTR_OVERWRITE);
			
			$posts_array['activated'][] = array(
				'id' => $fetch_a[0],
				'title' => $fetch_a[1],
				'date' => dateFormat('medium2', $fetch_a[2])
			);
		}
		
		while($fetch_s = mysql_fetch_array($getPostsSR)) {
			extract($fetch_s, EXTR_OVERWRITE);
			
			$posts_array['saved'][] = array(
				'id' => $fetch_s[0],
				'title' => $fetch_s[1],
				'date' => dateFormat('medium2', $fetch_s[2])
			);
		}
		
		while($fetch_p = mysql_fetch_array($getPostsPR)) {
			extract($fetch_p, EXTR_OVERWRITE);
			
			$posts_array['pending'][] = array(
				'id' => $fetch_p[0],
				'title' => $fetch_p[1],
				'date' => dateFormat('medium2', $fetch_p[2])
			);
		}
	
	?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#container-1 > ul').tabs();
			$('.zebra tr:nth-child(even)').addClass('alt');
		});
	</script>
	
	<div class="body">
		<span class="fontTitle">Manage Mission Posts</span><br /><br />
		
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
					echo "<strong class='fontLarge orange'>No activated posts found</strong>";
				}
				else
				{
				
				?>
				
				<table class="zebra" cellpadding="3" cellspacing="0">
					<tr class="fontMedium">
						<th width="34%">Title</th>
						<th width="44%">Author(s)</th>
						<th width="2%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
					
					<?php foreach($posts_array['activated'] as $value_a) { ?>
					<tr class="fontNormal">
						<td>
							<strong><? printText($value_a['title']);?></strong><br />
							<span class="fontNormal" style="color:#888;"><?=$value_a['date'];?></span>
						</td>
						<td><? displayAuthors($value_a['id'], 'rank', 'link');?></td>
						<td></td>
						<td align="center"><a href="<?=$webLocation;?>index.php?page=post&id=<?=$value_a['id'];?>"><strong>View Post</strong></a></td>
						<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=posts&id=<?=$value_a['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>
					<?php } ?>
				</table>
				
				<? } ?>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<?
				
				if(count($posts_array['saved']) == 0)
				{
					echo "<strong class='fontLarge orange'>No saved posts found</strong>";
				}
				else
				{
				
				?>
				<table class="zebra" cellpadding="3" cellspacing="0">
					<tr class="fontMedium">
						<th width="34%">Title</th>
						<th width="44%">Author(s)</th>
						<th width="2%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
					
					<?php foreach($posts_array['saved'] as $value_s) { ?>
					<tr class="fontNormal">
						<td>
							<strong><? printText($value_s['title']);?></strong><br />
							<span class="fontNormal" style="color:#888;"><?=$value_s['date'];?></span>
						</td>
						<td><? displayAuthors($value_s['id'], 'rank', 'link');?></td>
						<td></td>
						<td align="center"><a href="<?=$webLocation;?>index.php?page=post&id=<?=$value_s['id'];?>"><strong>View Post</strong></a></td>
						<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=posts&id=<?=$value_s['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>
					<?php } ?>
				</table>
				<?php } ?>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<?
				
				if(count($posts_array['pending']) == 0)
				{
					echo "<strong class='fontLarge orange'>No pending posts found</strong>";
				}
				else
				{
				
				?>
				<table class="zebra" cellpadding="3" cellspacing="0">
					<tr class="fontMedium">
						<th width="34%">Title</th>
						<th width="44%">Author(s)</th>
						<th width="2%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
					
					<?php foreach($posts_array['pending'] as $value_p) { ?>
					<tr class="fontNormal">
						<td>
							<strong><? printText($value_p['title']);?></strong><br />
							<span class="fontNormal" style="color:#888;"><?=$value_p['date'];?></span>
						</td>
						<td><? displayAuthors($value_p['id'], 'rank', 'link');?></td>
						<td></td>
						<td align="center"><a href="<?=$webLocation;?>index.php?page=post&id=<?=$value_p['id'];?>"><strong>View Post</strong></a></td>
						<td align="center"><a href="<?=$webLocation;?>admin.php?page=manage&sub=posts&id=<?=$value_p['id'];?>" class="edit"><strong>Edit</strong></a></td>
					</tr>
					<?php } ?>
				</table>
				<?php } ?>
			</div>
		</div>
		
	</div>

<?php

	}
	elseif(!isset($id) && !in_array("m_posts2", $sessionAccess))
	{
		echo "<div class='body'>";
		echo "<strong class='fontMedium orange'>You do not have permission to edit posts other than your own!</strong>";
		echo "</div>";
	}
}
else
{
	errorMessage( "post management" );
}

?>