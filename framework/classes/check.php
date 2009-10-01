<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/classes/check.php
Purpose: Page with the class that is called by the system to check for
	ACP items liked saved posts, pending items, SMS updates and PMs

System Version: 2.6.9
Last Modified: 2009-08-11 0911 EST

Included Classes:
	SystemCheck
**/

class SystemCheck
{
	var $access;
	var $output_array = array(
		array( 'version', '' ),
		array( 'pending', '' ),
		array( 'saved', '' ),
		array( 'messages', '' )
	);

	function version( $notify )
	{
		/* pull in the main fetch file */
		require_once 'framework/rss/rss_fetch.inc';
		
		/* set the url of the XML file */
		/* DO NOT CHANGE THIS URL! Doing so will break the version checking function! */
		$url = "http://www.anodyne-productions.com/feeds/version_sms.xml";
		$rss = fetch_rss($url);
		$continue = 1;
		
		/* define the variables coming out of the XML file */
		foreach ($rss->items as $item)
		{
			$rssVersion = $item['version'];
			$notes = $item['notes'];
			$severity = $item['severity'];
		}
		
		/* logic to figure out if we're supposed to show the update notification */
		if ($notify == "none")
		{
			$continue = 0;
		}
		
		if ($notify == "major" && $severity == "minor")
		{
			$continue = 0;
		}
		
		/* check the major version info */
		$major = substr($rssVersion, 0, 1);
		
		/* make sure we're using the right label, Nova/SMS */
		$label = ($major >= 3) ? 'Nova' : 'SMS';
		
		/* do some replacement for a fixed version based on the product */
		$versionFixed = ($major >= 3) ? substr_replace($rssVersion, '1', 0, 1) : $rssVersion;
		
		/* if we're supposed to show the update info, do it */
		if($continue == 1)
		{
			/* if the version the user has and the version from the XML file are different, display the notice */
			if( VER_FILES < $rssVersion && VER_DB < $rssVersion ) {
			
				$this->output_array[0][1] = "<div class='notify-red'>";
				$this->output_array[0][1].= "<b class='red case'>Update Available</b> &mdash; ";
				$this->output_array[0][1].= $label ." ". $versionFixed . " is now available.<br /><br />";
			
				$this->output_array[0][1].= $notes;
				$this->output_array[0][1].= "<br /><br />";
				$this->output_array[0][1].= "Go to the <a href='http://www.anodyne-productions.com/' target='_blank'>Anodyne site</a> to download this update.";
				$this->output_array[0][1].= "</div>";
			
			} if( VER_DB > VER_FILES && VER_DB == $rssVersion ) {
			
				$this->output_array[0][1] = "<div class='notify-orange'>";
				$this->output_array[0][1].= "<b class='orange case'>Update Warning</b> &mdash; ";
				$this->output_array[0][1].= "Your database is running SMS version " . VER_DB . ", however, your files are running version " . VER_FILES . " and need to be updated. Please upload the correct files before continuing. If you do not update your files and database SMS will not work correctly!";
				$this->output_array[0][1].= "</div>";
			
			} if( VER_FILES > VER_DB && VER_FILES == $rssVersion ) {
	
				/* format the version right for the URL pass */
				$urlVersion = str_replace( ".", "", VER_DB );
	
				/* do some logic to make sure that the urlVersion var is right */
				if( $urlVersion == "20" || $urlVersion == "21" || $urlVersion == "22" || $urlVersion == "23" || $urlVersion == "24" || $urlVersion == "25" || $urlVersion == "26" ) {
					$urlVersion = $urlVersion . "0";
				}
			
				$this->output_array[0][1] = "<div class='notify-orange'>";
				$this->output_array[0][1].= "<b class='orange case'>Update Warning</b> &mdash; ";
				$this->output_array[0][1].= "Your files are running SMS version " . VER_FILES . ", however, your database is running version " . VER_DB . " and needs to be updated. Please use the link below to finish your update. If you do not update your files and database SMS will not work correctly!<br /><br />";
				$this->output_array[0][1].= "<a href='" . WEBLOC . "update.php?version=" . $urlVersion . "'>Update SMS Database</a>";
				$this->output_array[0][1].= "</div>";
			
			}
		} /* close the continue variable */
	}
	
	function pendings()
	{
		/* define the variables ahead of time */
		$pendingUsers = "";
		$pendingPosts = "";
		$pendingLogs = "";
		$pendingNews = "";
		$pendingDockings = "";
		$pendingAwards = "";
		
		if( in_array( "x_approve_users", $this->access ) ) {
			/* check for pending users */
			$checkUsers = "SELECT crewid FROM sms_crew WHERE crewType = 'pending'";
			$checkUsersResult = mysql_query( $checkUsers);
			$pendingUsers = mysql_num_rows( $checkUsersResult );
		}
		
		if( in_array( "x_approve_posts", $this->access ) ) {
			/* check for pending posts */
			$checkPosts = "SELECT postid FROM sms_posts WHERE postStatus = 'pending'";
			$checkPostsResult = mysql_query( $checkPosts );
			$pendingPosts = mysql_num_rows( $checkPostsResult );
		}
		
		if( in_array( "x_approve_logs", $this->access ) ) {
			/* check for pending logs */
			$checkLogs = "SELECT logid FROM sms_personallogs WHERE logStatus = 'pending'";
			$checkLogsResult = mysql_query( $checkLogs );
			$pendingLogs = mysql_num_rows( $checkLogsResult );
		}
		
		if( in_array( "x_approve_news", $this->access ) ) {
			/* check for pending news items */
			$checkNews = "SELECT newsid FROM sms_news WHERE newsStatus = 'pending'";
			$checkNewsResult = mysql_query( $checkNews );
			$pendingNews = mysql_num_rows( $checkNewsResult );
		}
		
		if( in_array( "x_approve_docking", $this->access ) ) {
		
			/* check for pending docking requests */
			$checkDockings = "SELECT dockid FROM sms_starbase_docking WHERE dockingStatus = 'pending'";
			$checkDockingsResult = mysql_query( $checkDockings );
			$pendingDockings = mysql_num_rows( $checkDockingsResult );
			
		}
		
		if( in_array( "m_giveaward", $this->access ) ) {
		
			/* check for pending docking requests */
			$checkAwards = "SELECT id FROM sms_awards_queue WHERE status = 'pending'";
			$checkAwardsResult = mysql_query( $checkAwards );
			$pendingAwards = mysql_num_rows( $checkAwardsResult );
			
		}
		
		/* compile the pending count */
		$pendingCount = ( $pendingUsers + $pendingPosts + $pendingLogs + $pendingNews + $pendingDockings + $pendingAwards );
		
		/* do some logic to make sure the notification is using the right verb tenses */
		if( $pendingCount == 1 ) {
			$pendings = "is 1 item";
		} elseif( $pendingCount > 1 ) {
			$pendings = "are " . $pendingCount . " items";
		}
		
		/* create an array and populate with the counts */
		$pendingArray = array();

		/* figure out what should and shouldn't be in the array */
		if( in_array( "x_approve_users", $this->access ) ) {
			$pendingArray[] = array( "users", $pendingUsers );
		} if( in_array( "x_approve_posts", $this->access ) ) {
			$pendingArray[] = array( "posts", $pendingPosts );
		} if( in_array( "x_approve_logs", $this->access ) ) {
			$pendingArray[] = array( "logs", $pendingLogs );
		} if( in_array( "x_approve_news", $this->access ) ) {
			$pendingArray[] = array( "news items", $pendingNews );
		} if( in_array( "x_approve_docking", $this->access ) ) {
			$pendingArray[] = array( "docking requests", $pendingDockings );
		} if( in_array( "m_giveaward", $this->access ) ) {
			$pendingArray[] = array( "award nominations", $pendingAwards );
		}
		
		foreach( $pendingArray as $k => $v )
		{
			if( $v[1] == 0 ) {
				unset( $pendingArray[$k] );
			}
		}
		
		/* reset the array's keys */
		$pendingArray = array_values( $pendingArray );
		
		/* display the message if one of the 5 have pendings */
		if(
			( $pendingUsers > 0 && in_array( "x_approve_users", $this->access ) ) ||
			( $pendingPosts > 0 && in_array( "x_approve_posts", $this->access ) ) ||
			( $pendingLogs > 0 && in_array( "x_approve_logs", $this->access ) ) ||
			( $pendingNews > 0 && in_array( "x_approve_news", $this->access ) ) ||
			( $pendingDockings > 0 && in_array( "x_approve_docking", $this->access ) ) ||
			( $pendingAwards > 0 && in_array( "m_giveaward", $this->access ) )
		) {
			
			$this->output_array[1][1] = "<div class='notify-orange'>";
			$this->output_array[1][1].= "<b class='orange case'>Pending Items</b> &mdash; ";
			$this->output_array[1][1].= "There " . $pendings . " [ ";
			
			/* get a count of the number of items in the array and subtract one to give us a pointer to the last key */
			$keyCount = count( $pendingArray ) - 1;
			
			/* loop through the array and act on each key */
			foreach( $pendingArray as $key => $value ) {
				
				/* if it's the last key of the array, display the AND */
				if( $key > 0 && $key == $keyCount ) {
					$this->output_array[1][1].= " &amp; ";
				} elseif( $key == 0 ) {
					$this->output_array[1][1].= "";
				} else {
					$this->output_array[1][1].= ", ";
				}
				
				/* do some logic to make sure the plurality of the word is right */
				if( $value[1] == 1 ) {
					$value[0] = substr_replace( $value[0], '', -1 );
				}
				
				$this->output_array[1][1].= $value[1] . " " . $value[0];
				
			}
			
			$this->output_array[1][1].= " ] awaiting moderation. Visit the <a href='" . WEBLOC . "admin.php?page=manage&sub=activate'>activation page</a> to view activation options.";
			$this->output_array[1][1].= "</div>";
		}
	}
	
	function saved( $crew, $access )
	{
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
		if( $count == 1 ) {
			$countPlural = "entry";
		} elseif( $count > 1 ) {
			$countPlural = "entries";
		}
		
		if( $count > 0 ) {
			$this->output_array[2][1] = "<div class='notify-blue'>";
				$this->output_array[2][1].= "<b class='blue case'>" . $count . " saved " . $countPlural . "</b>";
				
				$this->output_array[2][1].= "<table border='0' cellpadding='0' cellspacing='0'>";
					$this->output_array[2][1].= "<tr><td height='10'></td></tr>";
	
				if( $countPostsFinal > 0 ) {
					$this->output_array[2][1].= "<tr>";
						$this->output_array[2][1].= "<td><b>Mission Posts</b></td>";
					$this->output_array[2][1].= "</tr>";
					
					while( $postFetch = mysql_fetch_array( $countPostsResult ) ) {
						extract( $postFetch, EXTR_OVERWRITE );
						
						$this->output_array[2][1].= "<tr>";
							$this->output_array[2][1].= "<td class='fontNormal'><a href='" . WEBLOC . "admin.php?page=post&sub=mission&id=" . $postFetch['postid'] . "'>";
							
							if( !empty( $postFetch['postTitle'] ) ) {
								$this->output_array[2][1].= $postFetch['postTitle'];
							} else {
								$this->output_array[2][1].= "<i>[ No Subject ]</i>";
							}
							
							$this->output_array[2][1].= " &raquo;</a></td>";
						$this->output_array[2][1].= "</tr>";
	
					}
	
					$this->output_array[2][1].= "<tr><td height='15'></td></tr>";
				} if( $countJPsFinal > 0 ) {
					$this->output_array[2][1].= "<tr>";
						$this->output_array[2][1].= "<td><b>Joint Mission Posts</b></td>";
					$this->output_array[2][1].= "</tr>";
					
					while( $jpFetch = mysql_fetch_array( $countJPsResult ) ) {
						extract( $jpFetch, EXTR_OVERWRITE );
	
						$this->output_array[2][1].= "<tr>";
							$this->output_array[2][1].= "<td class='fontNormal'>";
							
							if( $postSave > 0 ) {
								if( $postSave == $crew ) { } else {
									$this->output_array[2][1].= "<img src='" . WEBLOC . "images/message-unread-icon.png' border='0' alt='' /> &nbsp;";
								}
							}
							
							$this->output_array[2][1].= "<a href='" . WEBLOC . "admin.php?page=post&sub=jp&id=" . $jpFetch['postid'] . "'>";
							
							if( !empty( $jpFetch['postTitle'] ) ) {
								$this->output_array[2][1].= $jpFetch['postTitle'];
							} else {
								$this->output_array[2][1].= "<i>[ No Subject ]</i>";
							}
							
							$this->output_array[2][1].= " &raquo;</a></td>";
						$this->output_array[2][1].= "</tr>";
	
					}
	
					$this->output_array[2][1].= "<tr><td height='15'></td></tr>";
				} if( $countLogsFinal > "0" ) {
					$this->output_array[2][1].= "<tr>";
						$this->output_array[2][1].= "<td><b>Personal Logs</b></td>";
					$this->output_array[2][1].= "</tr>";
					
					while( $logFetch = mysql_fetch_array( $countLogsResult ) ) {
						extract( $logFetch, EXTR_OVERWRITE );
	
						$this->output_array[2][1].= "<tr>";
							$this->output_array[2][1].= "<td class='fontNormal'><a href='" . WEBLOC . "admin.php?page=post&sub=log&id=" . $logFetch['logid'] . "'>";
							
							if( !empty( $logFetch['logTitle'] ) ) {
								$this->output_array[2][1].= $logFetch['logTitle'];
							} else {
								$this->output_array[2][1].= "<i>[ No Subject ]</i>";
							}
							
							$this->output_array[2][1].= " &raquo;</a></td>";
						$this->output_array[2][1].= "</tr>";
	
					}
	
					$this->output_array[2][1].= "<tr><td height='15'></td></tr>";
				} if( $countNewsFinal > "0" ) {
					$this->output_array[2][1].= "<tr>";
						$this->output_array[2][1].= "<td><b>News Items</b></td>";
					$this->output_array[2][1].= "</tr>";
					
					while( $newsFetch = mysql_fetch_array( $countNewsResult ) ) {
						extract( $newsFetch, EXTR_OVERWRITE );
	
						$this->output_array[2][1].= "<tr>";
							$this->output_array[2][1].= "<td class='fontNormal'><a href='" . WEBLOC . "admin.php?page=post&sub=news&id=" . $newsFetch['newsid'] . "'>";
							
							if( !empty( $newsFetch['newsTitle'] ) ) {
								$this->output_array[2][1].= $newsFetch['newsTitle'];
							} else {
								$this->output_array[2][1].= "<i>[ No Subject ]</i>";
							}
							
							$this->output_array[2][1].= " &raquo;</a></td>";
						$this->output_array[2][1].= "</tr>";
	
					}
	
				}
	
				$this->output_array[2][1].= "</table>";
				
			$this->output_array[2][1].= "</div>";
		}
	}
	
	function messages( $crew )
	{
		$countMessages = "SELECT pmid, pmSubject, pmAuthor FROM sms_privatemessages ";
		$countMessages.= "WHERE pmRecipient = '$crew' AND pmStatus = 'unread' ";
		$countMessages.= "AND pmRecipientDisplay = 'y'";
		$countMessagesResult = mysql_query( $countMessages );
		$countMessagesFinal = mysql_num_rows( $countMessagesResult );
	
		/* do some logic to determine the plurality */
		if( $countMessagesFinal == 1 ) {
			$countPlural = "message";
		} elseif( $countMessagesFinal > 1 ) {
			$countPlural = "messages";
		}
		
		if( $countMessagesFinal > 0 ) {
			$this->output_array[3][1] = "<div class='notify-orange'>";
			$this->output_array[3][1].= "<b class='orange case'>" . $countMessagesFinal . " unread private " . $countPlural . "</b> ";
			$this->output_array[3][1].= "<span class='fontNormal'>&mdash; <a href='" . WEBLOC . "admin.php?page=user&sub=inbox'>Go to Inbox</a></span><br />";
			
				$this->output_array[3][1].= "<table>";
				
					while( $msgFetch = mysql_fetch_array( $countMessagesResult ) ) {
						extract( $msgFetch, EXTR_OVERWRITE );
						
						$this->output_array[3][1].= "<tr>";
							$this->output_array[3][1].= "<td class='fontNormal'><a href='" . WEBLOC . "admin.php?page=user&sub=message&id=" . $msgFetch['pmid'] . "'>";
							
							if( !empty( $msgFetch['pmSubject'] ) ) {
								$this->output_array[3][1].= $msgFetch['pmSubject'];
							} else {
								$this->output_array[3][1].= "<i>[ No Subject ]</i>";
							}
							
							$this->output_array[3][1].= "</a> from " . printCrewNameEmail( $msgFetch['pmAuthor'], "rank", "noLink" ) . "</td>";
						$this->output_array[3][1].= "</tr>";
	
					}
	
				$this->output_array[3][1].= "</table>";
			$this->output_array[3][1].= "</div>";
		}
	}
	
	function output()
	{
		foreach( $this->output_array as $key => $value )
		{
			if( empty( $value[1] ) )
			{
				unset( $this->output_array[$key] );
			} /* close the if logic */
		} /* close the foreach loop */
		
		/* reset the keys */
		$this->output_array = array_values( $this->output_array );
		
		/* count the keys and do the math for evaluating the keys */
		$keyCount = count( $this->output_array );
		$keyCount = $keyCount - 1;
		
		/* if the array isn't empty, show the output */
		if( !empty( $this->output_array ) ) {
			echo "<div class='update'>";
				foreach( $this->output_array as $k => $v )
				{
					if( ( $keyCount > 0 && $k == 0 ) || ( $keyCount == 0 ) )
					{
						echo "";
					}
					else
					{
						echo "<br />";
					}
					
					echo $this->output_array[$k][1];
				}
			echo "</div><br />";
		} /* close the if not empty check */
	} /* close the output function */

}

?>