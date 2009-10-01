<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/functionsAdmin.php
Purpose: List of functions specific to the administration control panel

System Version: 2.6.10
Last Modified: 2009-09-08 0827 EST

Included Functions:
	printCrewName( $crewid, $rank, $link )
	checkPendings( $accessLevel, $simmType )
	errorMessage( $page )
	checkSMSVersion( $version )
	checkSavedPosts( $crew, $access )
	aboutSMS( $version )
	checkUnreadMessages( $crew )
	errorMessageIllegal( $page, $crewid, $expected, $actual )
	accessControls( $webLocation, $skin )
	print_active_crew_select_menu( $type, $author, $id, $section, $sub )
**/

define('JP_AUTHORS', 8);

/**
	Admin function that will pull the user's first name, last name, rank, and rank image
**/
function printCrewName($crewid, $rank, $link, $type = "active")
{
	if($type == 'active')
	{
		$userFetch = "SELECT c.crewid, c.firstName, c.lastName, r.rankName FROM sms_crew AS c, sms_ranks AS r ";
		$userFetch.= "WHERE c.crewid = '$crewid' AND c.rankid = r.rankid LIMIT 1";
		$userFetchResult = mysql_query($userFetch);
		$fetch = mysql_fetch_assoc($userFetchResult);
	
		$name = array(
			'rank' => $fetch['rankName'],
			'first_name' => $fetch['firstName'],
			'last_name' => $fetch['lastName']
		);
	}
	elseif($type == 'pending')
	{
		$userFetch = "SELECT crewid, firstName, lastName FROM sms_crew WHERE crewid = '$crewid' LIMIT 1";
		$userFetchResult = mysql_query($userFetch);
		$fetch = mysql_fetch_assoc($userFetchResult);
	
		$name = array(
			'first_name' => $fetch['firstName'],
			'last_name' => $fetch['lastName']
		);
	}
	
	foreach($name as $key => $value)
	{
		if(empty($value))
		{
			unset($name[$key]);
		}
	}
	
	if($rank == "noRank")
	{
		unset($name['rank']);
		$name = implode(' ', $name);
		
		if($link == "noLink")
		{
			echo stripslashes($name);
		}
		elseif($link == "link")
		{
			echo "<a href='" . WEBLOC . "index.php?page=bio&crew=" . $fetch['crewid'] . "'>";
			echo stripslashes($name);
			echo "</a>";
		}
	}
	elseif($rank == "rank")
	{
		$name = implode(' ', $name);
		
		if($link == "noLink")
		{
			echo stripslashes($name);
		}
		elseif($link == "link")
		{
			echo "<a href='" . WEBLOC . "index.php?page=bio&crew=" . $fetch['crewid'] . "'>";
			echo stripslashes($name);
			echo "</a>";
		}
	}
}
/** END FUNCTION **/

/**
	Admin function that checks pendings and displays a message
**/
function checkPendings( $accessLevel, $simmType ) {
	
	if( in_array( "x_approve_users", $accessLevel ) ) {
		/* check for pending users */
		$checkUsers = "SELECT crewid FROM sms_crew WHERE crewType = 'pending'";
		$checkUsersResult = mysql_query( $checkUsers);
		$pendingUsers = mysql_num_rows( $checkUsersResult );
	}
	
	if( in_array( "x_approve_posts", $accessLevel ) ) {
		/* check for pending posts */
		$checkPosts = "SELECT postid FROM sms_posts WHERE postStatus = 'pending'";
		$checkPostsResult = mysql_query( $checkPosts );
		$pendingPosts = mysql_num_rows( $checkPostsResult );
	}
	
	if( in_array( "x_approve_logs", $accessLevel ) ) {
		/* check for pending logs */
		$checkLogs = "SELECT logid FROM sms_personallogs WHERE logStatus = 'pending'";
		$checkLogsResult = mysql_query( $checkLogs );
		$pendingLogs = mysql_num_rows( $checkLogsResult );
	}
	
	if( in_array( "x_approve_news", $accessLevel ) ) {
		/* check for pending news items */
		$checkNews = "SELECT newsid FROM sms_news WHERE newsStatus = 'pending'";
		$checkNewsResult = mysql_query( $checkNews );
		$pendingNews = mysql_num_rows( $checkNewsResult );
	}
	
	/* add up all the row counts */
	if( $simmType == "starbase" && in_array( "x_approve_docking", $accessLevel ) ) {
	
		/* check for pending docking requests */
		$checkDockings = "SELECT dockid FROM sms_starbase_docking WHERE dockingStatus = 'pending'";
		$checkDockingsResult = mysql_query( $checkDockings );
		$pendingDockings = mysql_num_rows( $checkDockingsResult );
		
		/* compile the pending count */
		$pendingCount = ( $pendingUsers + $pendingPosts + $pendingLogs + $pendingNews + $pendingDockings );
		
	} else {
	
		/* compile the pending count */
		$pendingCount = ( $pendingUsers + $pendingPosts + $pendingLogs + $pendingNews );
		
	}
	
	/* do some logic to make sure the notification is using the right verb tenses */
	if( $pendingCount == 1 ) {
		$pendings = "is 1 item";
	} elseif( $pendingCount > 1 ) {
		$pendings = "are " . $pendingCount . " items";
	}
	
	if( $simmType == "starbase" ) {
		/* create an array and populate with the counts */
		$pendingArray = array();

		/* figure out what should and shouldn't be in the array */
		if( in_array( "x_approve_users", $accessLevel ) ) {
			$pendingArray['users'] = $pendingUsers;
		} if( in_array( "x_approve_posts", $accessLevel ) ) {
			$pendingArray['posts'] = $pendingPosts;
		} if( in_array( "x_approve_logs", $accessLevel ) ) {
			$pendingArray['logs'] = $pendingLogs;
		} if( in_array( "x_approve_news", $accessLevel ) ) {
			$pendingArray['news'] = $pendingNews;
		} if( in_array( "x_approve_docking", $accessLevel ) ) {
			$pendingArray['docking'] = $pendingDockings;
		}
		
	} else {
		$pendingArray = array();
		
		/* figure out what should and shouldn't be in the array */
		if( in_array( "x_approve_users", $accessLevel ) && ( $pendingUsers > 0 ) ) {
			$pendingArray[] = array( "users", $pendingUsers );
		} if( in_array( "x_approve_posts", $accessLevel ) && ( $pendingPosts > 0 ) ) {
			$pendingArray[] = array( "posts", $pendingPosts );
		} if( in_array( "x_approve_logs", $accessLevel ) && ( $pendingLogs > 0 ) ) {
			$pendingArray[] = array( "logs", $pendingLogs );
		} if( in_array( "x_approve_news", $accessLevel ) && ( $pendingNews > 0 ) ) {
			$pendingArray[] = array( "news items", $pendingNews );
		}
		
	}
	
	/* display the message if one of the 4 have pendings */
	if(
		( $pendingUsers > 0 && in_array( "x_approve_users", $accessLevel ) ) ||
		( $pendingPosts > 0 && in_array( "x_approve_posts", $accessLevel ) ) ||
		( $pendingLogs > 0 && in_array( "x_approve_logs", $accessLevel ) ) ||
		( $pendingNews > 0 && in_array( "x_approve_news", $accessLevel ) ) ||
		( $pendingDockings > 0 && in_array( "x_approve_docking", $accessLevel ) )
	) {
		
		echo "<div class='update'>";
		echo "<img src='" . WEBLOC . "images/warning-large.png' border='0' alt='warning' style='float:left; padding: 0 6px 0 0;' />";
		echo "<span class='fontTitle'>Pending Items</span><br /><br />";
		echo "There " . $pendings . " [ ";
		
		/* get a count of the number of items in the array and subtract one to give us a pointer to the last key */
		$keyCount = count( $pendingArray ) - 1;
		
		/* loop through the array and act on each key */
		foreach( $pendingArray as $key => $value ) {
			
			/* if it's the last key of the array, display the AND */
			if( $key == $keyCount ) {
				
				echo " &amp; ";
				
				/* make sure the docking stuff is set up right */
				if( $value[0] == "docking" ) {
					$value[0] = "docking requests";
				}
				
				/* do some logic to make sure the plurality of the word is right */
				if( $value[1] == 1 ) {
					$value[0] = substr_replace( $value[0], '', -1 );
				}
				
				echo $value[1] . " " . $value[0];
				
			} else {
				
				if( $key == 0 ) {} else {
					echo ", ";
				}
			
				/* make sure the docking stuff is set up right */
				if( $value[0] == "docking" ) {
					$value[0] = "docking requests";
				}
				
				/* do some logic to make sure the plurality of the word is right */
				if( $value[1] == 1 ) {
					$value[0] = substr_replace( $value[0], '', -1 );
				}
				
				echo $value[1] . " " . $value[0];
				
			}
			
		}
		
		 echo " ] awaiting moderation. Please go to the <a href='" . WEBLOC . "admin.php?page=manage&sub=activate'>activation page</a> to view activation options.";
		
		echo "</div>";
		echo "<br />";
	} else {
		echo "<br />";
	}
	
}

/** END FUNCTION **/

/**
	Display error message if someone doesn't have the right access level
**/
function errorMessage( $page ) {

	echo "<div class='body'>";
		echo "<span class='fontTitle'>Error!</span><br /><br />";
		echo "You do not have permission to access the " . $page . " page. If you believe you have received this message in error, please contact the site administrator.";
	echo "</div>";

}
/* END FUNCTION */

/**
	Function that checks the XML file on the Anodyne site to make sure the user
	has the latest version of SMS running.  WARNING: modying this function may
	cause the version checking feature to fail. Modify this code at your own risk.
**/
function checkSMSVersion( $version ) {

	/* get the version out of the database */
	$getDBVersion = "SELECT sysVersion FROM sms_system WHERE sysid = '1' LIMIT 1";
	$getDBVersionResult = mysql_query( $getDBVersion );
	$dbVersion = mysql_fetch_array( $getDBVersionResult );
	
	/* pull in the main fetch file */
	require_once( 'framework/rss/rss_fetch.inc' );
	
	/* set the url of the XML file */
	/* DO NOT CHANGE THIS URL! Doing so will break the version checking function! */
	$url = "http://www.anodyne-productions.com/feeds/version.xml";
	$rss = fetch_rss( $url );
	
	/* define the variables coming out of the XML file */
	foreach( $rss->items as $item ) {
		$rssVersion = $item['version'];
		$notes = $item['notes'];
	}
	
	/* if the version the user has and the version from the XML file are different, display the notice */
	if( $version < $rssVersion && $dbVersion['sysVersion'] < $rssVersion ) {
		echo "<div class='update'>";
		
		echo "<img src='" . WEBLOC . "images/feed.png' border='0' alt='' style='float:left; padding: 0 12px 0 0;' />";
		echo "<span class='fontTitle'>SMS Update Available</span><br /><br />";
		echo "SMS " . $rssVersion . " is now available.<br /><br />";
		
		echo $notes;
		echo "<br /><br />";
		echo "Go to the <a href='http://www.anodyne-productions.com/index.php?cat=sms&page=downloads' target='_blank'>Anodyne SMS Site</a> to download this update.";
		
		echo "</div>";
		echo "<br />";
	} if( $dbVersion['sysVersion'] > $version && $dbVersion['sysVersion'] == $rssVersion ) {
		echo "<div class='update'>";
		
		echo "<img src='" . WEBLOC . "images/warning-large.png' border='0' alt='' style='float:left; padding: 0 12px 0 0;' />";
		echo "<span class='fontTitle'>SMS Update Warning</span><br /><br />";
		echo "Your database is running SMS version " . $dbVersion['sysVersion'] . ", however, your SMS files are running version " . $version . " and need to be updated. Please upload the correct files before continuing. If you do not update your files and database, the new version of SMS will not work correctly!<br /><br />";
		
		echo "</div>";
		echo "<br />";
	} if( $version > $dbVersion['sysVersion'] && $version == $rssVersion ) {

		/* format the version right for the URL pass */
		$urlVersion = str_replace( ".", "", $dbVersion['sysVersion'] );

		/* do some logic to make sure that the urlVersion var is right */
		if( $urlVersion == "20" || $urlVersion == "21" || $urlVersion == "22" || $urlVersion == "23" || $urlVersion == "24" || $urlVersion == "25" || $urlVersion == "26" ) {
			$urlVersion = $urlVersion . "0";
		}
		
		echo "<div class='update'>";
		
		echo "<img src='" . WEBLOC . "images/warning-large.png' border='0' alt='' style='float:left; padding: 0 12px 0 0;' />";
		echo "<span class='fontTitle'>SMS Update Warning</span><br /><br />";
		echo "Your SMS files are running SMS version " . $version . ", however, your database is running version " . $dbVersion['sysVersion'] . " and needs to be updated. Please use the link below to finish your update. If you do not update your files and database, the new version of SMS will not work correctly!<br /><br />";
		echo "<a href='" . WEBLOC . "update.php?version=" . $urlVersion . "'>Update SMS Database</a>";
		
		echo "</div>";
		echo "<br />";
	}

}
/* END FUNCTION */

/**
	Function that checks to see if there are any saved posts by the author
**/
function checkSavedPosts( $crew, $access ) {
	
	if( in_array( "p_mission", $access ) ) {
		/* count the posts */
		$countPosts = "SELECT postid, postTitle FROM sms_posts WHERE postAuthor = '$crew' AND postStatus = 'saved'";
		$countPostsResult = mysql_query( $countPosts );
		$countPostsFinal = mysql_num_rows( $countPostsResult );
	}
	
	if( in_array( "p_jp", $access ) ) {
		/* count the JPs */
		$countJPs = "SELECT postid, postTitle, postSave FROM sms_posts WHERE ( postAuthor LIKE '%,$crew,%' OR postAuthor LIKE '$crew,%' OR postAuthor LIKE '%,$crew' ) AND postStatus = 'saved'";
		$countJPsResult = mysql_query( $countJPs );
		$countJPsFinal = mysql_num_rows( $countJPsResult );
	}
	
	if( in_array( "p_log", $access ) ) {
		/* count the personal logs */
		$countLogs = "SELECT logid, logTitle FROM sms_personallogs WHERE logAuthor = '$crew' AND logStatus = 'saved'";
		$countLogsResult = mysql_query( $countLogs );
		$countLogsFinal = mysql_num_rows( $countLogsResult );
	}
	
	if( in_array( "p_news", $access ) ) {
		/* count the news items */
		$countNews = "SELECT newsid, newsTitle FROM sms_news WHERE newsAuthor = '$crew' AND newsStatus = 'saved'";
		$countNewsResult = mysql_query( $countNews );
		$countNewsFinal = mysql_num_rows( $countNewsResult );
	}

	/* add up all the counts to get the final count */
	$count = ( $countPostsFinal + $countJPsFinal + $countLogsFinal + $countNewsFinal );

	/* do some logic to determine the plurality */
	if( $count == "1" ) {
		$countPlural = "entry";
	} elseif( $count > "1" ) {
		$countPlural = "entries";
	}
	
	if( $count > "0" ) {
		echo "<br />";
		echo "<div class='update'>";
			echo "<img src='" . WEBLOC . "images/saved.png' border='0' alt='' style='float:left; padding: 0 12px 0 0;' />";
			echo "<span class='fontTitle'>" . $count . " Saved " . ucwords( $countPlural ) . "</span>";

			echo "<br /><br />";
			echo "<table>";

			if( $countPostsFinal > "0" ) {
				echo "<tr>";
					echo "<td><b>Mission Posts</b></td>";
				echo "</tr>";
				
				while( $postFetch = mysql_fetch_array( $countPostsResult ) ) {
					extract( $postFetch, EXTR_OVERWRITE );
					
					echo "<tr>";
						echo "<td class='fontNormal'><a href='" . WEBLOC . "admin.php?page=post&sub=mission&id=" . $postFetch['postid'] . "'>";
						
						if( !empty( $postFetch['postTitle'] ) ) {
							echo $postFetch['postTitle'];
						} else {
							echo "<i>[ No Subject ]</i>";
						}
						
						echo " &raquo;</a></td>";
					echo "</tr>";

				}

				echo "<tr><td height='5'></td></tr>";
			} if( $countJPsFinal > 0 ) {
				echo "<tr>";
					echo "<td><b>Joint Mission Posts</b></td>";
				echo "</tr>";
				
				while( $jpFetch = mysql_fetch_array( $countJPsResult ) ) {
					extract( $jpFetch, EXTR_OVERWRITE );

					echo "<tr>";
						echo "<td class='fontNormal'>";
						
						if( $postSave > 0 ) {
							if( $postSave == $crew ) { } else {
								echo "<img src='" . WEBLOC . "images/message-unread-icon.png' border='0' alt='' /> &nbsp;";
							}
						}
						
						echo "<a href='" . WEBLOC . "admin.php?page=post&sub=jp&id=" . $jpFetch['postid'] . "'>";
						
						if( !empty( $jpFetch['postTitle'] ) ) {
							echo $jpFetch['postTitle'];
						} else {
							echo "<i>[ No Subject ]</i>";
						}
						
						echo " &raquo;</a></td>";
					echo "</tr>";

				}

				echo "<tr><td height='5'></td></tr>";
			} if( $countLogsFinal > "0" ) {
				echo "<tr>";
					echo "<td><b>Personal Logs</b></td>";
				echo "</tr>";
				
				while( $logFetch = mysql_fetch_array( $countLogsResult ) ) {
					extract( $logFetch, EXTR_OVERWRITE );

					echo "<tr>";
						echo "<td class='fontNormal'><a href='" . WEBLOC . "admin.php?page=post&sub=log&id=" . $logFetch['logid'] . "'>";
						
						if( !empty( $logFetch['logTitle'] ) ) {
							echo $logFetch['logTitle'];
						} else {
							echo "<i>[ No Subject ]</i>";
						}
						
						echo " &raquo;</a></td>";
					echo "</tr>";

				}

				echo "<tr><td height='5'></td></tr>";
			} if( $countNewsFinal > "0" ) {
				echo "<tr>";
					echo "<td><b>News Items</b></td>";
				echo "</tr>";
				
				while( $newsFetch = mysql_fetch_array( $countNewsResult ) ) {
					extract( $newsFetch, EXTR_OVERWRITE );

					echo "<tr>";
						echo "<td class='fontNormal'><a href='" . WEBLOC . "admin.php?page=post&sub=news&id=" . $newsFetch['newsid'] . "'>";
						
						if( !empty( $newsFetch['newsTitle'] ) ) {
							echo $newsFetch['newsTitle'];
						} else {
							echo "<i>[ No Subject ]</i>";
						}
						
						echo " &raquo;</a></td>";
					echo "</tr>";

				}

				echo "<tr><td height='5'></td></tr>";
			}

			echo "</table>";
			
		echo "</div>";
	}

}
/* END FUNCTION */

/**
	Function that displays the about information regarding SMS
**/
function aboutSMS( $version, $location ) {

	/* query the database for the system info */
	$getSys = "SELECT * FROM sms_system WHERE sysid = '1' LIMIT 1";
	$getSysResult = mysql_query( $getSys );
	$system = mysql_fetch_assoc( $getSysResult );

	/* spit the information out */
	echo "<ul class='version'>";
		echo "<li><b>File Version</b>: " . $version . "</li>";
		echo "<li><b>Database Version</b>: " . $system['sysVersion'] . "</li>";
	echo "</ul>";

	echo "<ul class='version'>";
		echo "<li><b>Web Location:</b> " . $location . "</li>";
	echo "</ul>";
	
}
/* END FUNCTION */

/**
	Function that displays the about information regarding SMS
**/
function checkUnreadMessages( $crew ) {

	/* count the posts */
	$countMessages = "SELECT pmid, pmSubject, pmAuthor FROM sms_privatemessages ";
	$countMessages.= "WHERE pmRecipient = '$crew' AND pmStatus = 'unread' ";
	$countMessages.= "AND pmRecipientDisplay = 'y'";
	$countMessagesResult = mysql_query( $countMessages );
	$countMessagesFinal = mysql_num_rows( $countMessagesResult );

	/* do some logic to determine the plurality */
	if( $countMessagesFinal == "1" ) {
		$countPlural = "message";
	} elseif( $countMessagesFinal > "1" ) {
		$countPlural = "messages";
	}
	
	if( $countMessagesFinal > "0" ) {
		echo "<br />";
		echo "<div class='update'>";
			echo "<img src='" . WEBLOC . "images/messages-unread.png' border='0' alt='' style='float:left; padding: 0 12px 0 0;' />";
			echo "<span class='fontTitle'>" . $countMessagesFinal . " Unread Private " . ucwords( $countPlural ) . "</span>";

			echo "<br /><br />";
			echo "<table>";
			
				while( $msgFetch = mysql_fetch_array( $countMessagesResult ) ) {
					extract( $msgFetch, EXTR_OVERWRITE );
					
					echo "<tr>";
						echo "<td class='fontNormal'><a href='" . WEBLOC . "admin.php?page=user&sub=message&id=" . $msgFetch['pmid'] . "'>";
						
						if( !empty( $msgFetch['pmSubject'] ) ) {
							echo $msgFetch['pmSubject'];
						} else {
							echo "<i>[ No Subject ]</i>";
						}
						
						echo "</a> from " . printCrewNameEmail( $msgFetch['pmAuthor'], "rank", "noLink" ) . "</td>";
					echo "</tr>";

				}

			echo "</table>";

			echo "<br />";
			echo "<a href='" . WEBLOC . "admin.php?page=user&sub=inbox'>Go to Inbox &raquo;</a>";
			
		echo "</div>";
	}

}
/* END FUNCTION */

/**
	Display error message if someone tries a SQL injection
**/
function errorMessageIllegal( $page, $crewid, $expected, $actual ) {
	echo "<div class='body'>";
		echo "<span class='fontTitle'>Warning!</span><br /><br />";
		echo "You have attempted an illegal operation!";
	echo "</div>";
}
/* END FUNCTION */

/**
	Function that displays the update button for user access control
**/
function accessControls( $webLocation, $skin ) {

?>

	<table>
		<tr>
			<td colspan="3" height="25"></td>
		</tr>
		<tr>
			<td colspan="3">
				<input type="image" src="<?=$webLocation;?>skins/<?=$skin;?>/buttons/update.png" name="action_update" value="Update" class="button" />
			</td>
		</tr>
	</table>

<?

}

/**
	Active Crew Select Menu
**/
function print_active_crew_select_menu( $type, $author, $id, $section, $sub ) {
	
	if( $type != "post" ) {

		if( $type == "pm" ) {
			echo "<select name='" . $type . "Recipient'>";
		} else {
			echo "<select name='" . $type . "Author'>";
		}
		
		$users = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$users.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$users.= "WHERE crew.crewType = 'active' AND crew.rankid = rank.rankid ORDER BY crew.rankid";
		$usersResult = mysql_query( $users );
		
		if( empty( $author ) ) { 
			echo "<option value='0'>No Author Selected</option>";
		}
		
		while( $userFetch = mysql_fetch_assoc( $usersResult ) ) {
			extract( $userFetch, EXTR_OVERWRITE );
				
			if( $author == $userFetch['crewid'] ) {
				echo "<option value='$author' selected>$rankName $firstName $lastName</option>";
			} else {
				echo "<option value='$userFetch[crewid]'>$rankName $firstName $lastName</option>";
			}
		}
	
	echo "</select>";
	
	} elseif( $type == "post" ) {
		
		$authorArray = explode( ",", $author );
		
		$i = 0;
		
		foreach( $authorArray as $key=>$value ) {
			
			echo "<select name='" . $type . "Author" . $i . "'>";
			
			$users = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
			$users.= "FROM sms_crew AS crew, sms_ranks AS rank ";
			$users.= "WHERE crew.crewType = 'active' AND crew.rankid = rank.rankid ORDER BY crew.rankid";
			$usersResult = mysql_query( $users );
			
			while( $userFetch = mysql_fetch_assoc( $usersResult ) ) {
				extract( $userFetch, EXTR_OVERWRITE );
				
				if( in_array( $authorArray[$i], $userFetch ) ) {
					echo "<option value='$authorArray[$i]' selected>$rankName $firstName $lastName</option>";
				} else {
					echo "<option value='$userFetch[crewid]'>$rankName $firstName $lastName</option>";
				}
				
			}
			
			echo "</select>";
			
			/*
				if there are less than 8 array keys, allow a user to add another one
				if there is a second array key, allow a user to delete a user, otherwise don't
			*/
			if( $i < JP_AUTHORS ) {
				echo "&nbsp;&nbsp;";
				
				if(isset($_GET['id']))
				{
					$href = WEBLOC . "admin.php?page=" . $section . "&sub=" . $sub . "&id=" . $_GET['id'] . "&add=1&postid=" . $id;
				}
				else
				{
					$href = WEBLOC . "admin.php?page=manage&sub=posts&add=1&postid=" . $id;
				}
				
				echo "<a href='" . $href . "' class='add_icon image'>&nbsp;&nbsp;&nbsp;&nbsp;</a>";
				
			} if( array_key_exists( "1", $authorArray ) ) {
				echo "&nbsp;";
				
				if(isset($_GET['id']))
				{
					$href2 = WEBLOC . "admin.php?page=" . $section . "&sub=" . $sub . "&id=" . $_GET['id'] . "&delete=" . $i . "&postid=" . $id;
				}
				else
				{
					$href2 = WEBLOC . "admin.php?page=" . $section . "&sub=" . $sub . "&delete=" . $i . "&postid=" . $id;
				}
				
				echo "<a href='" . $href2 . "' class='remove_icon image'>&nbsp;&nbsp;&nbsp;&nbsp;</a>";
				
			}
			
			/* as long as $i is under 7, keep adding 1 to it */
			if( $i < JP_AUTHORS ) {
				$i = $i +1;
			}
			
			echo "<br />\n";
			
		}
		
		/* count the number of items in the array */
		$authorCount = count( $authorArray );
		
		/* return the array count to be used to put the author string together */
		return $authorCount;
		
	}
	
}
/* END FUNCTION */

?>