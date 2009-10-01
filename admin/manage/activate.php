<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/activate.php
Purpose: Page to manage pending users, posts, logs, and docking requests

System Version: 2.6.9
Last Modified: 2009-03-08 2303 EST
**/

/* access check */
if(
	in_array( "x_approve_users", $sessionAccess ) ||
	in_array( "x_approve_posts", $sessionAccess ) ||
	in_array( "x_approve_logs", $sessionAccess ) ||
	in_array( "x_approve_news", $sessionAccess ) ||
	in_array( "x_approve_docking", $sessionAccess ) ||
	in_array( "m_giveaward", $sessionAccess )
) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action_category = FALSE;
	$result = FALSE;
	$query = FALSE;
	
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
		
		if($action_category == 'user' && in_array('x_approve_users', $sessionAccess))
		{
			switch($action_type)
			{
				case 'accept':
					
					/* get the position type from the database */
					$getPosType = "SELECT positionType FROM sms_positions WHERE positionid = '$position' LIMIT 1";
					$getPosTypeResult = mysql_query( $getPosType );
					$positionType = mysql_fetch_row( $getPosTypeResult );

					/* set the access levels accordingly */
					if( $positionType[0] == "senior" ) {
						$accessID = 3;
					} else {
						$accessID = 4;
					}

					/* pull the default access levels from the db */
					$getGroupLevels = "SELECT * FROM sms_accesslevels WHERE id = $accessID LIMIT 1";
					$getGroupLevelsResult = mysql_query( $getGroupLevels );
					$groups = mysql_fetch_array( $getGroupLevelsResult );
					
					$update = "UPDATE sms_crew SET positionid = %d, crewType = %s, accessPost = %s, ";
					$update.= "accessManage = %s, accessReports = %s, accessUser = %s, accessOthers = %s, ";
					$update.= "rankid = %d, leaveDate = %s, moderatePosts = %s, moderateLogs = %s, moderateNews = %s ";
					$update.= "WHERE crewid = $action_id LIMIT 1";
					
					$query = sprintf(
						$update,
						escape_string( $position ),
						escape_string( 'active' ),
						escape_string( $groups[1] ),
						escape_string( $groups[2] ),
						escape_string( $groups[3] ),
						escape_string( $groups[4] ),
						escape_string( $groups[5] ),
						escape_string( $rank ),
						escape_string( '' ),
						escape_string( $moderatePosts ),
						escape_string( $moderateLogs ),
						escape_string( $moderateNews )
					);

					$result = mysql_query( $query );

					/* update the position they're being given */
					update_position( $position, 'give' );

					/** EMAIL THE APPROVAL **/

					/* set the email author */
					$userFetch = "SELECT email FROM sms_crew WHERE crewid = '$action_id' LIMIT 1";
					$userFetchResult = mysql_query( $userFetch );
					$userEmail = mysql_fetch_row( $userFetchResult );

					/* define the variables */
					$to = $userEmail[0] . ", " . printCOEmail();
					$from = printCO('short_rank') . " < " . printCOEmail() . " >";
					$subject = $emailSubject . " Your Application";

					/* new instance of the replacement class */
					$message = new MessageReplace;
					$message->message = $acceptMessage;
					$message->shipName = $shipPrefix . " " . $shipName;
					$message->player = $action_id;
					$message->rank = $_POST['rank'];
					$message->position = $_POST['position'];
					$message->setArray();
					$accept = stripslashes($message->changeMessage());

					/* send the email */
					mail( $to, $subject, $accept, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
					
					/* optimize the tables */
					optimizeSQLTable( "sms_crew" );
					optimizeSQLTable( "sms_positions" );
					
					break;
				case 'reject':
					
					/** EMAIL THE REJECTION **/

					/* set the email author */
					$userFetch = "SELECT email FROM sms_crew WHERE crewid = $action_id LIMIT 1";
					$userFetchResult = mysql_query( $userFetch );
					$userEmail = mysql_fetch_row( $userFetchResult );

					/* define the variables */
					$to = $userEmail[0] . ", " . printCOEmail();
					$from = printCO('short_rank') . " < " . printCOEmail() . " >";
					$subject = $emailSubject . " Your Application";

					/* new instance of the replacement class */
					$message = new MessageReplace;
					$message->message = $rejectMessage;
					$message->shipName = $shipPrefix . " " . $shipName;
					$message->player = $action_id;
					$message->position = $_POST['position'];
					$message->setArray();
					$reject = stripslashes($message->changeMessage());

					/* send the email */
					mail( $to, $subject, $reject, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
					
					/* delete the record from the db */
					$query = "DELETE FROM sms_crew WHERE crewid = $action_id LIMIT 1";
					$result = mysql_query( $query );
					
					/* optimize the tables */
					optimizeSQLTable( "sms_crew" );
					
					break;
			}
		}
		if($action_category == 'post' && in_array('x_approve_posts', $sessionAccess))
		{
			switch($action_type)
			{
				case 'activate':
					
					$query = "UPDATE sms_posts SET postStatus = 'activated' WHERE postid = $action_id LIMIT 1";
					$result = mysql_query( $query );

					 /* optimize the table */
					optimizeSQLTable( "sms_posts" );

					/** EMAIL THE POST **/

					$getPostContents = "SELECT * FROM sms_posts WHERE postid = $action_id LIMIT 1";
					$getPostContentsResult = mysql_query( $getPostContents );
					$fetchPost = mysql_fetch_assoc( $getPostContentsResult );

					/* set the email author */
					$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, ";
					$userFetch.= "rank.rankShortName FROM sms_crew AS crew, sms_ranks AS rank WHERE ";
					$userFetch.= "crew.crewid = '$fetchPost[postAuthor]' AND crew.rankid = rank.rankid LIMIT 1";
					$userFetchResult = mysql_query( $userFetch );

					while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
						extract( $userFetchArray, EXTR_OVERWRITE );
					}

					$firstName = str_replace( "'", "", $firstName );
					$lastName = str_replace( "'", "", $lastName );

					$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";

					/* define the variables */
					$to = getCrewEmails( "emailPosts" );
					$subject = $emailSubject . " " . printMissionTitle( $fetchPost['postMission'] ) . " - " . $fetchPost['postTitle'];
					$message = "A Post By " . displayEmailAuthors( $fetchPost['postAuthor'], 'noLink' ) . "\r\n";
					$message.= "Location: " . stripslashes($fetchPost['postLocation']) . "\r\n";
					$message.= "Timeline: " . stripslashes($fetchPost['postTimeline']) . "\r\n";
					$message.= "Tag: " . stripslashes($fetchPost['postTag']) . "\r\n\r\n";
					$message.= stripslashes($fetchPost['postContent']);

					/* send the email */
					mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
					
					break;
				case 'delete':
					
					$query = "DELETE FROM sms_posts WHERE postid = $action_id LIMIT 1";
					$result = mysql_query( $query );

					/* optimize the table */
					optimizeSQLTable( "sms_posts" );
					
					break;
			}
		}
		if($action_category == 'log' && in_array('x_approve_logs', $sessionAccess))
		{
			switch($action_type)
			{
				case 'activate':
				
					$query = "UPDATE sms_personallogs SET logStatus = 'activated' WHERE logid = $action_id LIMIT 1";
					$result = mysql_query( $query );

					/* optimize the table */
					optimizeSQLTable( "sms_personallogs" );

					/** EMAIL THE LOG **/

					$getLogContents = "SELECT * FROM sms_personallogs WHERE logid = $action_id LIMIT 1";
					$getLogContentsResult = mysql_query( $getLogContents );
					$fetchLog = mysql_fetch_assoc( $getLogContentsResult );

					/* set the email author */
					$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, ";
					$userFetch.= "rank.rankShortName, rank.rankName FROM sms_crew AS crew, sms_ranks AS rank WHERE ";
					$userFetch.= "crew.crewid = '$fetchLog[logAuthor]' AND crew.rankid = rank.rankid LIMIT 1";
					$userFetchResult = mysql_query( $userFetch );

					while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
						extract( $userFetchArray, EXTR_OVERWRITE );
					}

					$firstName = str_replace( "'", "", $firstName );
					$lastName = str_replace( "'", "", $lastName );

					$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";
					$name = $rankName . " " . $firstName . " " . $lastName;

					/* define the variables */
					$to = getCrewEmails( "emailLogs" );
					$subject = $emailSubject . " " . $name . "'s Personal Log - " . stripslashes( $fetchLog['logTitle'] );
					$message = stripslashes( $fetchLog['logContent'] );

					/* send the email */
					mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
					
					break;
				case 'delete':
					
					$query = "DELETE FROM sms_personallogs WHERE logid = $action_id LIMIT 1";
					$result = mysql_query( $query );

					/* optimize the table */
					optimizeSQLTable( "sms_personallogs" );
					
					break;
			}
		}
		if($action_category == 'news' && in_array('x_approve_news', $sessionAccess))
		{
			switch($action_type)
			{
				case 'activate':
					
					$query = "UPDATE sms_news SET newsStatus = 'activated' WHERE newsid = $action_id LIMIT 1";
					$result = mysql_query( $query );

					/* optimize the table */
					optimizeSQLTable( "sms_news" );

					/** EMAIL THE NEWS **/

					$getNewsContents = "SELECT * FROM sms_news WHERE newsid = $action_id LIMIT 1";
					$getNewsContentsResult = mysql_query( $getNewsContents );
					$fetchNews = mysql_fetch_assoc( $getNewsContentsResult );

					/* pull the category name */
					$getCategory = "SELECT catName FROM sms_news_categories WHERE catid = '$fetchNews[newsCat]' LIMIT 1";
					$getCategoryResult = mysql_query( $getCategory );
					$category = mysql_fetch_assoc( $getCategoryResult );

					/* set the email author */
					$userFetch = "SELECT crew.crewid, crew.firstName, crew.lastName, crew.email, ";
					$userFetch.= "rank.rankShortName FROM sms_crew AS crew, sms_ranks AS rank WHERE ";
					$userFetch.= "crew.crewid = '$fetchNews[newsAuthor]' AND crew.rankid = rank.rankid LIMIT 1";
					$userFetchResult = mysql_query( $userFetch );

					while( $userFetchArray = mysql_fetch_array( $userFetchResult ) ) {
						extract( $userFetchArray, EXTR_OVERWRITE );
					}

					$firstName = str_replace( "'", "", $firstName );
					$lastName = str_replace( "'", "", $lastName );

					$from = $rankShortName . " " . $firstName . " " . $lastName . " < " . $email . " >";

					/* define the variables */
					$to = getCrewEmails( "emailNews" );
					$subject = $emailSubject . " " . stripslashes( $category['catName'] ) . " - " . stripslashes( $fetchNews['newsTitle'] );
					$message = "A News Item Posted By " . printCrewNameEmail( $fetchNews['newsAuthor'] ) . "\r\n\r\n";
					$message.= stripslashes( $fetchNews['newsContent'] );

					/* send the email */
					mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
					
					break;
				case 'delete':
					
					$query = "DELETE FROM sms_news WHERE newsid = $action_id LIMIT 1";
					$result = mysql_query( $query );

					/* optimize the table */
					optimizeSQLTable( "sms_news" );
					
					break;
			}
		}
		if($action_category == 'award' && in_array('m_giveaward', $sessionAccess))
		{
			switch($action_type)
			{
				case 'accept':
					
					/* set the status to accepted */
					$query1 = "UPDATE sms_awards_queue SET status = 'accepted' WHERE id = $action_id";
					$result1 = mysql_query($query1);
					
					/* get the data */
					$get = "SELECT q.*, c.awards FROM sms_awards_queue AS q, sms_crew AS c ";
					$get.= "WHERE q.id = $action_id AND c.crewid = q.nominated LIMIT 1";
					$getR = mysql_query($get);
					$fetch = mysql_fetch_assoc($getR);
					
					/* don't explode the array if there's nothing there to start with */
					if(!empty($fetch['awards']))
					{
						$awards_array = explode(";", $fetch['awards']);
					}

					/* get the date info from PHP */
					$now = getdate();
					
					/* make sure there are no semicolons in the reason */
					$reason = str_replace(";", ",", $fetch['reason']);

					/* build the new award entry */
					$awards_array[] = $fetch['award'] . "|" . $now[0] . "|" . $reason;

					/* put the string back together */
					$awards_string = implode(";", $awards_array);
					
					/* build the update query */
					$update = "UPDATE sms_crew SET awards = %s WHERE crewid = $fetch[nominated]";
					
					/* insert the values into the query */
					$query = sprintf(
						$update,
						escape_string($awards_string)
					);
					
					/* run the query */
					$result = mysql_query($query);
					
					/* optimize the tables */
					optimizeSQLTable( "sms_awards_queue" );
					optimizeSQLTable( "sms_crew" );
					
					break;
				case 'reject':
					
					$query = "UPDATE sms_awards_queue SET status = 'rejected' WHERE id = $action_id";
					$result = mysql_query($query);
					
					/* optimize the table */
					optimizeSQLTable("sms_awards_queue");
					
					break;
			}
		}
		if($action_category == 'docking' && in_array('x_approve_docking', $sessionAccess))
		{
			switch($action_type)
			{
				case 'approve':
					
					$query = "UPDATE sms_starbase_docking SET dockingStatus = 'activated' WHERE dockid = $action_id LIMIT 1";
					$result = mysql_query( $query );

					/* optimize the table */
					optimizeSQLTable( "sms_starbase_docking" );

					/** EMAIL THE APPROVAL **/

					/* set the email author */
					$emailFetch = "SELECT dockingShipCOEmail FROM sms_starbase_docking WHERE dockid = $action_id LIMIT 1";
					$emailFetchResult = mysql_query($emailFetch);
					$coEmail = mysql_fetch_assoc($emailFetchResult);

					/* define the variables */
					$to = $coEmail['dockingShipCOEmail'] . ", " . printCOEmail();
					$from = printCO('short_rank') . " < " . printCOEmail() . " >";
					$subject = $emailSubject . " Your Docking Request";
					$message = "Thank you for submitting a request to dock with the " . $shipPrefix . " " . $shipName . ".  After reviewing your application, we are pleased to inform you that your request to dock with our starbase has been approved!

The CO of the station will be in contact with you shortly.  Thank you for interest in docking with us.";

					/* send the email */
					mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
					
					break;
				case 'deny':
					
					$query = "DELETE FROM sms_starbase_docking WHERE dockid = $action_id LIMIT 1";
					$result = mysql_query( $query );

					/* optimize the table */
					optimizeSQLTable( "sms_stabase_docking" );

					/** EMAIL THE DENIAL **/

					/* set the email author */
					$emailFetch = "SELECT dockingShipCOEmail FROM sms_starbase_docking WHERE dockid = $action_id LIMIT 1";
					$emailFetchResult = mysql_query($emailFetch);
					$coEmail = mysql_fetch_assoc($emailFetchResult);

					/* define the variables */
					$to = $coEmail['dockingShipCOEmail'] . ", " . printCOEmail();
					$from = printCO('short_rank') . " < " . printCOEmail() . " >";
					$subject = $emailSubject . " Your Docking Request";
					$message = "Thank you for submitting a request to dock with the " . $shipPrefix . " " . $shipName . ".  After reviewing your application, we regret to inform you that your request to dock with our starbase has been denied.  There can be many reasons for this.  If you would like clarification, please contact the CO.";

					/* send the email */
					mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );
					
					break;
			}
		}
	}

	/* get pending users */
	$getPendingUsers = "SELECT crew.crewid, crew.firstName, crew.lastName, position.positionName ";
	$getPendingUsers.= "FROM sms_crew AS crew, sms_positions AS position WHERE ";
	$getPendingUsers.= "crew.positionid = position.positionid AND crew.crewType = 'pending'";
	$getPendingUsersResult = mysql_query( $getPendingUsers );
	$countPendingUsers = mysql_num_rows( $getPendingUsersResult );
	
	/* get pending mission posts */
	$getPendingPosts = "SELECT postid, postTitle FROM sms_posts WHERE postStatus = 'pending'";
	$getPendingPostsResult = mysql_query( $getPendingPosts );
	$countPendingPosts = mysql_num_rows( $getPendingPostsResult );
	
	/* get pending personal logs */
	$getPendingLogs = "SELECT logid, logTitle FROM sms_personallogs WHERE logStatus = 'pending'";
	$getPendingLogsResult = mysql_query( $getPendingLogs );
	$countPendingLogs = mysql_num_rows( $getPendingLogsResult );
	
	/* get pending news items */
	$getPendingNews = "SELECT newsid, newsTitle FROM sms_news WHERE newsStatus = 'pending'";
	$getPendingNewsResult = mysql_query( $getPendingNews );
	$countPendingNews = mysql_num_rows( $getPendingNewsResult );
	
	/* get pending awards */
	$getPendingAwards = "SELECT * FROM sms_awards_queue WHERE status = 'pending'";
	$getPendingAwardsResult = mysql_query( $getPendingAwards );
	$countPendingAwards = mysql_num_rows( $getPendingAwardsResult );
	
	if($simmType == "starbase")
	{
		/* get pending docking requests */
		$getPendingDocking = "SELECT * FROM sms_starbase_docking WHERE dockingStatus = 'pending'";
		$getPendingDockingResult = mysql_query( $getPendingDocking );
		$countPendingDocking = mysql_num_rows( $getPendingDockingResult );
		
		if($action_category == "docking")
		{
			$action_category = "docking request";
		}
	}
	
	if($countPendingUsers > 0) {
		$start = 1;
	} elseif($countPendingPosts > 0) {
		$start = 2;
	} elseif($countPendingLogs > 0) {
		$start = 3;
	} elseif($countPendingNews > 0) {
		$start = 4;
	} elseif($countPendingAwards > 0) {
		$start = 5;
	} elseif($simmType == "starbase" && $countPendingDocking > 0) {
		$start = 6;
	} else {
		$start = 1;
	}

?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#container-1 > ul').tabs(<?php echo $start; ?>);
		$('.zebra tr:odd').addClass('alt');
		
		$("a[rel*=facebox]").click(function() {
			var id = $(this).attr("myID");
			var type = $(this).attr("myType");
			var action = $(this).attr("myAction");
			
			jQuery.facebox(function() {
				jQuery.get('admin/ajax/activate_' + type + "_" + action + '.php?id=' + id, function(data) {
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
	
	if(!empty($check->query)) {
		$check->message($action_category, $action_type);
		$check->display();
	}
	
	?>
	<span class="fontTitle">Manage Pending Items</span><br /><br />

	<div id="container-1">
		<ul>
			<li><a href="#one"><span>Users (<?=$countPendingUsers;?>)</span></a></li>
			<li><a href="#two"><span>Mission Posts (<?=$countPendingPosts;?>)</span></a></li>
			<li><a href="#three"><span>Personal Logs (<?=$countPendingLogs;?>)</span></a></li>
			<li><a href="#four"><span>News Items (<?=$countPendingNews;?>)</span></a></li>
			<li><a href="#five"><span>Awards (<?=$countPendingAwards;?>)</span></a></li>
			<?php if($simmType == "starbase") { ?><li><a href="#six"><span>Docking Requests (<?=$countPendingDocking;?>)</span></a></li><?php } ?>
		</ul>
		
		<!-- users -->
		<div id="one" class="ui-tabs-container ui-tabs-hide">
			<?php if( $countPendingUsers < 1 ) { ?>
				<b class="fontMedium orange">No pending users found</b>
			<?php } else { ?>
			<b class="fontLarge">Pending Users</b><br /><br />
			<table class="zebra" cellpadding="3" cellspacing="0">
				<thead>
					<tr class="fontMedium">
						<th width="35%">Name</th>
						<th width="35%">Position</th>
						<th width="10%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
				</thead>
				
				<?php
				
				/* loop through the results and fill the form */
				while( $pendingUser = mysql_fetch_assoc( $getPendingUsersResult ) ) {
					extract( $pendingUser, EXTR_OVERWRITE );
				
				?>
				<tr class="fontNormal">
					<td><? printText( $pendingUser['firstName'] . " " . $pendingUser['lastName'] ); ?></td>
					<td><? printText( $pendingUser['positionName'] ); ?></td>
					<td align="center"><a href="<?=$webLocation;?>index.php?page=bio&crew=<?=$pendingUser['crewid'];?>"><b>View Bio</b></a></td>
					<td align="center"><a href="#" class="delete" rel="facebox" myID="<?=$pendingUser['crewid'];?>" myType="user" myAction="reject"><b>Reject</b></a></td>
					<td align="center"><a href="#" class="add" rel="facebox" myID="<?=$pendingUser['crewid'];?>" myType="user" myAction="accept"><b>Accept</b></a></td>
				</tr>
				<?php } ?>
				
			</table>
			<?php } /* close counting */ ?>
		</div>
		
		<!-- posts -->
		<div id="two" class="ui-tabs-container ui-tabs-hide">
			<?php if( $countPendingPosts < 1 ) { ?>
				<b class="fontMedium orange">No pending mission posts found</b>
			<?php } else { ?>
			<b class="fontLarge">Pending Mission Posts</b><br /><br />
			<table class="zebra" cellpadding="3" cellspacing="0">
				<thead>
					<tr class="fontMedium">
						<th width="35%">Title</th>
						<th width="35%">Author</th>
						<th width="10%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
				</thead>
				
				<?php
				
				/* loop through the results and fill the form */
				while( $pendingPosts = mysql_fetch_assoc( $getPendingPostsResult ) ) {
					extract( $pendingPosts, EXTR_OVERWRITE );
				
				?>
				<tr class="fontNormal">
					<td><? printText( $pendingPosts['postTitle'] ); ?></td>
					<td><? displayAuthors( $pendingPosts['postid'], 'noLink' ); ?></td>
					<td align="center"><a href="<?=$webLocation;?>index.php?page=post&id=<?=$pendingPosts['postid'];?>"><b>View Post</b></a></td>
					<td align="center"><a href="#" class="delete" rel="facebox" myID="<?=$pendingPosts['postid'];?>" myType="post" myAction="delete"><b>Delete</b></a></td>
					<td align="center"><a href="#" class="add" rel="facebox" myID="<?=$pendingPosts['postid'];?>" myType="post" myAction="activate"><b>Activate</b></a></td>
				</tr>
				<?php } ?>
				
			</table>
			<?php } /* close counting */ ?>
		</div>
		
		<!-- personal logs -->
		<div id="three" class="ui-tabs-container ui-tabs-hide">
			<?php if( $countPendingLogs < 1 ) { ?>
				<b class="fontMedium orange">No pending personal logs found</b>
			<?php } else { ?>
			<b class="fontLarge">Pending Personal Logs</b><br /><br />
			<table class="zebra" cellpadding="3" cellspacing="0">
				<thead>
					<tr class="fontMedium">
						<th width="35%">Title</th>
						<th width="35%">Author</th>
						<th width="10%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
				</thead>
				
				<?php
				
				/* loop through the results and fill the form */
				while( $pendingLogs = mysql_fetch_assoc( $getPendingLogsResult ) ) {
					extract( $pendingLogs, EXTR_OVERWRITE );
				
				?>
				<tr class="fontNormal">
					<td><? printText( $pendingLogs['logTitle'] ); ?></td>
					<td><? printCrewName( $pendingLogs['logid'], 'rank', 'noLink' ); ?></td>
					<td align="center"><a href="<?=$webLocation;?>index.php?page=log&id=<?=$pendingLogs['logid'];?>"><b>View Log</b></a></td>
					<td align="center"><a href="#" class="delete" rel="facebox" myID="<?=$pendingLogs['logid'];?>" myType="log" myAction="delete"><b>Delete</b></a></td>
					<td align="center"><a href="#" class="add" rel="facebox" myID="<?=$pendingLogs['logid'];?>" myType="log" myAction="activate"><b>Activate</b></a></td>
				</tr>
				<?php } ?>
				
			</table>
			<?php } /* close counting */ ?>
		</div>
		
		<!-- news items -->
		<div id="four" class="ui-tabs-container ui-tabs-hide">
			<?php if( $countPendingNews < 1 ) { ?>
				<b class="fontMedium orange">No pending news items found</b>
			<?php } else { ?>
			<b class="fontLarge">Pending News Items</b><br /><br />
			<table class="zebra" cellpadding="3" cellspacing="0">
				<thead>
					<tr class="fontMedium">
						<th width="35%">Title</th>
						<th width="35%">Author</th>
						<th width="10%"></th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
				</thead>
				
				<?php
				
				/* loop through the results and fill the form */
				while( $pendingNews = mysql_fetch_assoc( $getPendingNewsResult ) ) {
					extract( $pendingNews, EXTR_OVERWRITE );
				
				?>
				<tr class="fontNormal">
					<td><? printText( $pendingNews['newsTitle'] ); ?></td>
					<td><? printCrewName( $pendingNews['newsAuthor'], 'rank', 'noLink' ); ?></td>
					<td align="center"><a href="<?=$webLocation;?>index.php?page=news&id=<?=$pendingNews['newsid'];?>"><b>View News</b></a></td>
					<td align="center"><a href="#" class="delete" rel="facebox" myID="<?=$pendingNews['newsid'];?>" myType="news" myAction="delete"><b>Delete</b></a></td>
					<td align="center"><a href="#" class="add" rel="facebox" myID="<?=$pendingNews['newsid'];?>" myType="news" myAction="activate"><b>Activate</b></a></td>
				</tr>
				<?php } ?>
				
			</table>
			<?php } /* close counting */ ?>
		</div>
		
		<!-- award nominations -->
		<div id="five" class="ui-tabs-container ui-tabs-hide">
			<?php if( $countPendingAwards < 1 ) { ?>
				<b class="fontMedium orange">No pending award nominations found</b>
			<?php } else { ?>
			<b class="fontLarge">Pending Award Nominations</b><br /><br />
			<table class="zebra" cellpadding="3" cellspacing="0">
				<thead>
					<tr class="fontMedium">
						<th width="30%">Award</th>
						<th width="25%">Recipient</th>
						<th width="25%">Nominated By</th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
				</thead>
				
				<?php
				
				/* loop through the results and fill the form */
				while( $pendingAwards = mysql_fetch_assoc( $getPendingAwardsResult ) ) {
					extract( $pendingAwards, EXTR_OVERWRITE );
					
					$getA = "SELECT * FROM sms_awards WHERE awardid = $pendingAwards[award] LIMIT 1";
					$getAResult = mysql_query($getA);
					$award = mysql_fetch_assoc($getAResult);
				
				?>
				<tr class="fontNormal">
					<td><? printText( $award['awardName'] ); ?></td>
					<td><? printCrewName( $pendingAwards['nominated'], "rank", "noLink" ); ?></td>
					<td><? printCrewName( $pendingAwards['crew'], "rank", "noLink" ); ?></td>
					<td align="center"><a href="#" class="delete" rel="facebox" myID="<?=$pendingAwards['id'];?>" myType="award" myAction="deny"><b>Deny</b></a></td>
					<td align="center">
						<? if (!empty($award)): ?>
							<a href="#" class="add" rel="facebox" myID="<?=$pendingAwards['id'];?>" myType="award" myAction="approve"><b>Approve</b></a>
						<? endif; ?>
					</td>
				</tr>
				<?php } ?>
				
			</table>
			<?php } /* close counting */ ?>
		</div>
		
		<?php if($simmType == 'starbase') { ?>
		<!-- docking requests -->
		<div id="six" class="ui-tabs-container ui-tabs-hide">
			<?php if( $countPendingDocking < 1 ) { ?>
				<b class="fontMedium orange">No pending docking requests found</b>
			<?php } else { ?>
			<b class="fontLarge">Pending Docking Requests</b><br /><br />
			<table class="zebra" cellpadding="3" cellspacing="0">
				<thead>
					<tr class="fontMedium">
						<th width="30%">Ship Name</th>
						<th width="25%">Ship CO</th>
						<th width="25%">Ship Site</th>
						<th width="10%"></th>
						<th width="10%"></th>
					</tr>
				</thead>
				
				<?php
				
				/* loop through the results and fill the form */
				while( $pendingDocking = mysql_fetch_assoc( $getPendingDockingResult ) ) {
					extract( $pendingDocking, EXTR_OVERWRITE );
				
				?>
				<tr class="fontNormal">
					<td><? printText( $pendingDocking['dockingShipName'] . " " . $pendingDocking['dockingShipRegistry'] ); ?></td>
					<td><? printText( $pendingDocking['dockingShipCO'] ); ?></td>
					<td><a href="<?=$pendingDocking['dockingShipURL'];?>" target="_blank"><?=$pendingDocking['dockingShipURL'];?></a></td>
					<td align="center"><a href="#" class="delete" rel="facebox" myID="<?=$pendingDocking['dockid'];?>" myType="docking" myAction="deny"><b>Deny</b></a></td>
					<td align="center"><a href="#" class="add" rel="facebox" myID="<?=$pendingDocking['dockid'];?>" myType="docking" myAction="approve"><b>Approve</b></a></td>
				</tr>
				<?php } ?>
				
			</table>
			<?php } /* close counting */ ?>
		</div>
		<?php } ?>
	</div>

</div>

<?php } else { errorMessage( "activation" ); } ?>