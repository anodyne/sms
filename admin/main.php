<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/main.php
Purpose: Main page of the administrative control panel

System Version: 2.6.7
Last Modified: 2008-12-17 0823 EST
**/

/* define the page class */
$pageClass = "admin";

/* fetch the user's information for their display preferences */
$userFetch = "SELECT rank.rankImage, rank.rankName, crew.positionid, crew.positionid2, crew.cpShowPosts, ";
$userFetch.= "crew.cpShowPostsNum, crew.cpShowLogs, crew.cpShowLogsNum, crew.cpShowNews, ";
$userFetch.= "crew.cpShowNewsNum, crew.loa FROM sms_crew AS crew, ";
$userFetch.= "sms_ranks AS rank, sms_positions AS position WHERE crew.crewid = '$sessionCrewid' ";
$userFetch.= "AND crew.rankid = rank.rankid AND crew.positionid = position.positionid";
$userFetchResult = mysql_query($userFetch);

while( $user = mysql_fetch_array( $userFetchResult ) ) {
	extract( $user, EXTR_OVERWRITE );
}

/* pull in the menu */
include_once( 'skins/'. $sessionDisplaySkin .'/menu.php' );

/* set the rank variable */
if( isset( $sessionCrewid ) ) {
	$rankSet = $sessionDisplayRank;
} else {
	$rankSet = $rankSet;
}

?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#container-1 > ul').tabs();
	});
</script>
	
<div class="body">
	<?
	
	/* if the user has the right permissions, check for the latest version of SMS */
	if( in_array( "x_update", $sessionAccess ) ) {
	
		/* check the launch status */
		$launch = new FirstLaunch;
		$launch->checkStatus();
		
		/* if the system hasn't been launched since the update */
		if( $launch->status == "n" ) {
			$launch->gather();
			$launch->display();
			$launch->update();
		}
		
	}
	
	$system_check = new SystemCheck;
	$system_check->access = $sessionAccess;
	
	/* if the admin wants to be notified, run the check */
	if(in_array("x_update", $sessionAccess) && $updateNotify != 'none')
	{
		$system_check->version( $updateNotify );
	}
	
	/* check for pending items */
	if(
		in_array( "x_approve_users", $sessionAccess ) ||
		in_array( "x_approve_posts", $sessionAccess ) ||
		in_array( "x_approve_logs", $sessionAccess ) ||
		in_array( "x_approve_news", $sessionAccess ) ||
		in_array( "x_approve_docking", $sessionAccess ) ||
		in_array( "m_giveaward", $sessionAccess )
	) {
		$system_check->pendings();
	}
	
	/* check for unread messages */
	if( in_array( "u_inbox", $sessionAccess ) ) {
		$system_check->messages( $sessionCrewid );
	}
	
	/* if the sim uses the posting system, show the saved entries */
	if( $usePosting == "y" &&
		( in_array( "p_mission", $sessionAccess ) ||
		in_array( "p_log", $sessionAccess ) ||
		in_array( "p_news", $sessionAccess ) ||
		in_array( "p_jp", $sessionAccess ) )
	) {
		$system_check->saved( $sessionCrewid, $sessionAccess );
	}
	
	/* output the checks */
	$system_check->output();
	
	?>
	
	<span class="fontTitle"><b><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></b></span>
	<img src="<?=$webLocation;?>images/ranks/<?=$rankSet;?>/<?=$rankImage;?>" alt="<?=$rankName;?>" /><br />
	<span class="fontMedium">
		<?
		
		/* spit out the player position for position #1 */
		printPlayerPosition( $sessionCrewid, $positionid, "" );
		
		/* if there's a second position, spit that out as well */
		if( !empty( $positionid2 ) ) {
			echo " &amp; ";
			printPlayerPosition( $sessionCrewid, $positionid2, "2" );
		}
		
		/* finally, print the ship name */
		printText( ", " . $shipPrefix . " " . $shipName );
		
		if( $loa == "1" || $loa == "2" ) {
			echo "<br />";
			switch( $loa ) {
				case "1":
					echo "<b class='yellow'>[ On Leave of Absence ]</b>";
					break;
				case "2":
					echo "<b class='orange'>[ On Extended Leave of Absence ]</b>";
					break;
			}
		}
		
		?>
	</span>
	
	<p><? printText( $cpMessage ); ?></p>
	<p>Use these links to get started:

	<ul class="list-dark">
		<? if( $usePosting == "y" ) { ?>

		<? if( in_array( "p_mission", $sessionAccess ) ) { ?>
		<li><a href="<?=$webLocation;?>admin.php?page=post&sub=mission">Write a mission entry</a></li>
		<li><a href="<?=$webLocation;?>admin.php?page=post&sub=jp">Start a joint post</a></li>
		<? } ?>

		<? if( in_array( "p_log", $sessionAccess ) ) { ?>
		<li><a href="<?=$webLocation;?>admin.php?page=post&sub=log">Write a personal log</a></li>
		<? } ?>
	
		<? } /* close the check for if they're using the posting features */ ?>

		<? if( in_array( "u_account1", $sessionAccess ) || in_array( "u_account2", $sessionAccess ) ) { ?>
		<li><a href="<?=$webLocation;?>admin.php?page=user&sub=account&crew=<?=$sessionCrewid;?>">Update your account information or change your password</a></li>
		<? } ?>

		<? if( in_array( "u_bio1", $sessionAccess ) || in_array( "u_bio2", $sessionAccess ) || in_array( "u_bio3", $sessionAccess ) ) { ?>
		<li><a href="<?=$webLocation;?>admin.php?page=user&sub=bio&crew=<?=$sessionCrewid;?>">Update your character's biography</a></li>
		<? } ?>

		<? if( in_array( "u_options", $sessionAccess ) ) { ?>
		<li><a href="<?=$webLocation;?>admin.php?page=user&sub=site">Change your site options</a></li>
		<li><a href="<?=$webLocation;?>admin.php?page=user&sub=site&sec=2">Change your personalized menu</a></li>
		<? } ?>
	</ul></p>
	
	<?php
	
	if( $usePosting == "y" ) {
		if( $cpShowPosts == "y" ) {
			
			/* query the db to get all the posts data */
			$getPosts = "SELECT post.*, mission.missionid, mission.missionTitle ";
			$getPosts.= "FROM sms_posts AS post, sms_missions AS mission ";
			$getPosts.= "WHERE post.postMission = mission.missionid AND ";
			$getPosts.= "post.postStatus = 'activated' ORDER BY post.postPosted DESC LIMIT $cpShowPostsNum";
			$getPostsResult = mysql_query( $getPosts );
			$countPosts = mysql_num_rows( $getPostsResult );
				
		} if( $cpShowLogs == "y" ) {
		
			/* query the db to get all the personal logs data */
			$getLogs = "SELECT * FROM sms_personallogs WHERE logStatus = 'activated' ";
			$getLogs.= "ORDER BY logPosted DESC LIMIT $cpShowLogsNum";
			$getLogsResult = mysql_query( $getLogs );
			$countLogs = mysql_num_rows( $getLogsResult );
			
		}
	}
	
	if( $cpShowNews == "y" ) {
		/* query the db to get all the news data */
		$getNewsItems = "SELECT news.*, cat.* FROM sms_news AS news, sms_news_categories AS cat ";
		$getNewsItems.= "WHERE news.newsCat = cat.catid AND news.newsStatus = 'activated' ";
		$getNewsItems.= "ORDER BY newsPosted DESC LIMIT $cpShowNewsNum";
		$getNewsItemsResult = mysql_query( $getNewsItems );
		$countNews = mysql_num_rows( $getNewsItemsResult );
	}
	
	/* create an array of the cpShow vars */
	$cpOptionsArray = array( "posts" => $cpShowPosts, "logs" => $cpShowLogs, "news" => $cpShowNews );

	/* if any of them are yes, show the tab container */
	if( in_array( "y", $cpOptionsArray ) ) {

	?>
	
	<div id="container-1">
		<ul>
			<?php if( $cpShowPosts == "y" ) { ?><li><a href="#one"><span>Recent Posts</span></a></li><?php } ?>
			<?php if( $cpShowLogs == "y" ) { ?><li><a href="#two"><span>Recent Logs</span></a></li><?php } ?>
			<?php if( $cpShowNews == "y" ) { ?><li><a href="#three"><span>Recent News</span></a></li><?php } ?>
		</ul>
		
		<?php if( $usePosting == "y" && $cpShowPosts == "y" ) { ?>
		<div id="one" class="ui-tabs-container ui-tabs-hide">
			<?php
	
			if( $countPosts == 0 ) {
				echo "<span class='fontMedium'><b>No Posts Recorded</b></span>";
			} else {
			
				/* loop through everything until you run out of results */
				while( $posts = mysql_fetch_array( $getPostsResult ) ) {
					extract( $posts, EXTR_OVERWRITE );
					
					$length = 50; /* The number of words you want */
					$words = explode(' ', $postContent); /* Creates an array of words */
					$words = array_slice($words, 0, $length); /* Slices the array */
					$text = implode(' ', $words); /* Grabs only the specified number of words */
					$tempAuthors = explode(",", $postAuthor);
				
			?>
				
				<span class="fontMedium">
					<b><? printText( $postTitle ); ?></b>
					
					<?php
					
					if(
						in_array("m_posts2", $sessionAccess) ||
						(in_array("m_posts1", $sessionAccess) && in_array($sessionCrewid, $tempAuthors))
					) {
					
					?>
					&nbsp;
					<a href="<?=WEBLOC;?>admin.php?page=manage&sub=posts&id=<?=$postid;?>" class="image">
						<img src="<?=$webLocation;?>images/edit.png" alt="[ Edit ]" border="0" />
					</a>
					<?php } ?>
				</span><br />
				
				<span class="fontSmall">
					<?php
					
					echo "Posted by ";
					displayAuthors( $posts['postid'], "link" );
					echo " on " . dateFormat( "long", $postPosted );
					
					?>
					<br />
					
					Mission: <a href="<?=$webLocation;?>index.php?page=mission&id=<?=$missionid;?>"><? printText( $missionTitle ); ?></a>
				</span><br />
				<div style="padding: .5em 0em 2em 1em;">
					<?php
					
					printText( $text );
					echo " ... <nobr>[ <a href='" . $webLocation . "index.php?page=post&id=" . $postid . "'>Read More &raquo;</a> ]</nobr>";
					
					?>
				</div>
			
			<?php
			
				} /* close the first while loop */
			} /* close the count check */
			
			?>
		</div>
		<?php } ?>
		
		<?php if( $usePosting == "y" && $cpShowLogs == "y" ) { ?>
		<div id="two" class="ui-tabs-container ui-tabs-hide">
			<?php
	
			if( $countLogs == 0 ) {
				echo "<span class='fontMedium'><b>No Personal Logs Recorded</b></span>";
			} else {
			
				/* loop through everything until you run out of results */
				while( $logs = mysql_fetch_assoc( $getLogsResult ) ) {
					extract( $logs, EXTR_OVERWRITE );
					
					$length = 50; /* The number of words you want */
					$words = explode( ' ', $logContent ); /* Creates an array of words */
					$words = array_slice( $words, 0, $length ); /* Slices the array */
					$text = implode( ' ', $words ); /* Grabs only the specified number of words */
					
			?>
				
				<span class="fontMedium">
					<b><?php printText( $logTitle ); ?></b>
					
					<?php
					
					if(
						in_array("m_logs2", $sessionAccess) ||
						(in_array("m_logs1", $sessionAccess) && $sessionCrewid == $logs['logAuthor'])
					) {
						
					?>
					&nbsp;
					<a href="<? WEBLOC;?>admin.php?page=manage&sub=logs&id=<?=$logid;?>" class="image">
						<img src="<?=$webLocation;?>images/edit.png" alt="[ Edit ]" border="0" />
					</a>
					<?php } ?>
					
				</span><br />
				<span class="fontSmall">
					Posted by <? printCrewName( $logs['logAuthor'], "rank", "link" ); ?> on
					<?php echo dateFormat( "long", $logPosted ); ?>
				</span><br />
				<div style="padding: .5em 0 2em 1em;">
					<?php
					
					printText( $text );
					echo " ... <nobr>[ <a href='" . $webLocation . "index.php?page=log&id=" . $logid . "'>Read More &raquo;</a> ]</nobr>";
					
					?>
				</div>
			
			<?php
			
				} /* close the while loop */
			} /* close the count check */
			
			?>
		</div>
		<? } ?>
		
		<?php if( $cpShowNews == "y" ) { ?>
		<div id="three" class="ui-tabs-container ui-tabs-hide">
			<?php
	
			if( $countNews == 0 ) {
				echo "<span class='fontMedium'><b>No News Items Recorded</b></span>";
			} else {
			
				/* loop through everything until you run out of results */
				while( $newsItems = mysql_fetch_array( $getNewsItemsResult ) ) {
					extract( $newsItems, EXTR_OVERWRITE );
					
					$length = 50; /* The number of words you want */
					$words = explode( ' ', $newsContent ); /* Creates an array of words */
					$words = array_slice( $words, 0, $length ); /* Slices the array */
					$text = implode( ' ', $words ); /* Grabs only the specified number of words */
					
					
			?>
				
				<span class="fontMedium">
					<b><?php printText( $newsTitle );?></b>
					
					<?php if( in_array( "m_news", $sessionAccess ) ) { ?>
					&nbsp;
					<a href="<?=$webLocation;?>admin.php?page=manage&sub=news&id=<?=$newsid;?>" class="image">
						<img src="<?=$webLocation;?>images/edit.png" alt="[ Edit ]" border="0" />
					</a>
					<?php } ?>
					
				</span><br />
				<span class="fontSmall">
					Posted by <?php printCrewName( $newsItems['newsAuthor'], "rank", "link" ); ?> on <?php echo dateFormat( "long", $newsPosted );?><br />
					Category: <?php printText( $catName ); ?>
				</span><br />
				<div style="padding: .5em 0em 2em 1em;">
					<?php
					
					printText( $text );
					echo " ... <nobr>[ <a href='" . $webLocation . "index.php?page=news&id=" . $newsid . "'>Read More &raquo;</a> ]</nobr>";
					
					?>
				</div>
			
			<?php
			
				} /* close the while loop */
			} /* close the count check */
			
			?>
		</div>
		<?php } ?>
		
	</div>
	
	<?php } /* close the logic that determines if users want to see stuff on the CP */ ?>
</div>