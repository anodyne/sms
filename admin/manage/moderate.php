<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/moderate.php
Purpose: Page to show who is moderated and who's not

System Version: 2.6.0
Last Modified: 2008-04-22 1939 EST
**/

/* access check */
if(in_array("m_moderation", $sessionAccess))
{
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	$action_type = FALSE;
	
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
		
		if($action_type == 'moderate')
		{
			$update = "UPDATE sms_crew SET moderatePosts = %s, moderateLogs = %s, moderateNews = %s WHERE crewid = $action_id LIMIT 1";
			
			$query = sprintf(
				$update,
				escape_string($_POST['m_posts']),
				escape_string($_POST['m_logs']),
				escape_string($_POST['m_news'])
			);
			
			$result = mysql_query($query);
			
			/* optimize the table */
			optimizeSQLTable( "sms_crew" );
		}
	}
	
	/* dump the crew data into an array */
	$getCrew = "SELECT crewid, moderatePosts, moderateLogs, moderateNews FROM sms_crew ";
	$getCrew.= "WHERE crewType = 'active' ORDER BY positionid, rankid ASC";
	$getCrewResult = mysql_query($getCrew);
	$crew = array();

	while($crewMod = mysql_fetch_array($getCrewResult)) {
		extract( $crewMod, EXTR_OVERWRITE );
		
		$crew[$crewMod[0]] = array('posts' => $crewMod[1], 'logs' => $crewMod[2], 'news' => $crewMod[3]);
	}

?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.zebra tr:nth-child(odd)').addClass('alt');
			
			$("a[rel*=facebox]").click(function() {
				var id = $(this).attr("myID");

				jQuery.facebox(function() {
					jQuery.get('admin/ajax/moderate_edit.php?id=' + id, function(data) {
						jQuery.facebox(data);
					});
				});
				return false;
			});
		});
	</script>
	
	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery($result, $query);
				
		if(!empty($check->query))
		{
			$check->message("user moderation flags", "update");
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Crew Post Moderation</span><br /><br />
		From this page you can view and change the moderation levels for various players. A green icon means that the type of post (the column) is being moderated for that particular player, while nothing in the column means the type of post is not being moderated.<br /><br />
		Moderated posts will require approval before being sent out to the crew. If a player is moderated and they attempted to post a joint post, the joint post will require approval before it is sent out to the crew, even if the other members are not moderated. Unmoderated posts will be sent out without any need for activation. These values can also be changed from each user&rsquo;s account page.<br /><br />
	
		<table class="zebra" cellpadding="3" cellspacing="0">
			<tr class="fontMedium">
				<th>Crew Member</td>
				<th>Mission Posts</td>
				<th>Personal Logs</td>
				<th>News Items</td>
			</tr>
			
			<?php foreach($crew as $key => $value) { ?>
			<tr height="30">
				<td><? printCrewName( $key, "rank", "noLink" ); ?></td>
				<td align="center" valign="middle">
					<?php
					
					if($value['posts'] == "y")
					{
						echo "<img src='images/message-unread-icon.png' border='0' alt='Moderated' />";
					}
					else
					{
						echo "&nbsp;";
					}
					
					?>
				</td>
				<td align="center" valign="middle">
					<?php
					
					if($value['logs'] == "y")
					{
						echo "<img src='images/message-unread-icon.png' border='0' alt='Moderated' />";
					}
					else
					{
						echo "&nbsp;";
					}
					
					?>
				</td>
				<td align="center" valign="middle">
					<?php
					
					if($value['news'] == "y")
					{
						echo "<img src='images/message-unread-icon.png' border='0' alt='Moderated' />";
					}
					else
					{
						echo "&nbsp;";
					}
					
					?>
				</td>
				<td align="center">
					<a href="#" rel="facebox" myID="<?=$key;?>" class="edit"><strong>Edit Moderation</strong></a>
				</td>
			</tr>
			<?php } ?>
		</table>
			
	</div>
	
<? } else { errorMessage( "crew post moderation" ); } ?>