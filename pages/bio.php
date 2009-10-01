<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/bio.php
Purpose: Page to display the requested bio

System Version: 2.6.8
Last Modified: 2009-01-02 1548 EST
**/

/* define the page class and set the vars */
$pageClass = "personnel";

/* make sure the crew id is legit */
if( isset( $_GET['crew'] ) && !is_numeric($_GET['crew'] ) ) {
	errorMessageIllegal( "bio page" );
	exit;
} else {
	$crew = $_GET['crew'];
}

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* set the rank variable */
if( isset( $sessionCrewid ) ) {
	$rankSet = $sessionDisplayRank;
} else {
	$rankSet = $rankSet;
}

$getCrew = "SELECT * FROM sms_crew WHERE crewid = '$crew' LIMIT 1";
$getCrewResult = mysql_query( $getCrew );

while( $fetchCrew = mysql_fetch_array( $getCrewResult ) ) {
	extract( $fetchCrew, EXTR_OVERWRITE );
	
	/* get the rank information */
	$getRank = "SELECT rankName, rankImage FROM sms_ranks WHERE rankid = '$fetchCrew[rankid]'";
	$getRankResult = mysql_query( $getRank );
	$fetchRank = mysql_fetch_assoc( $getRankResult );

	/* get the latest logs for the user */
	$getLogs = "SELECT * FROM sms_personallogs WHERE logStatus = 'activated' AND logAuthor = '$crew' ";
	$getLogs.= "ORDER BY logPosted DESC LIMIT $bioShowLogsNum";
	$getLogsResult = mysql_query( $getLogs );
	$NumLogs = mysql_num_rows( $getLogsResult );

	/* get the latest posts for the user */
	$getPosts = "SELECT post.*, mission.missionid, mission.missionTitle ";
	$getPosts.= "FROM sms_posts AS post, sms_missions AS mission ";
	$getPosts.= "WHERE post.postMission = missionid AND post.postStatus = 'activated' AND ( postAuthor LIKE '$crew,%' OR ";
	$getPosts.= "postAuthor LIKE '%,$crew' OR postAuthor LIKE '%,$crew,%' OR postAuthor = '$crew' ) ";
	$getPosts.= "ORDER BY post.postPosted DESC LIMIT $bioShowPostsNum";
	$getPostsResult = mysql_query( $getPosts );
	$NumPosts = mysql_num_rows( $getPostsResult );

?>

<script type="text/javascript">
	$(document).ready(function() {
		var options = {
			resizeLgImages:     true,
			displayNav:         true,
			handleUnsupported:  'remove',
			keysClose:          ['c', 27] // c or esc
		};

		Shadowbox.init(options);
		
		$('a#togglePosting').click(function() {
			$('#posting').toggle(75);
			return false;
		});
		$('a#toggleAwards').click(function() {
			$('#awards').toggle(75);
			return false;
		});
	});
</script>

<div class="body">
	<span class="fontTitle">
		<?
		
		if( !empty( $fetchCrew['rankid'] ) ) {
			printCrewName( $fetchCrew['crewid'], "rank", "noLink" );
		} else {
			printCrewName( $fetchCrew['crewid'], "noRank", "noLink" );
		}
		
		?>
	</span>
	&nbsp;&nbsp;
	<? if( $fetchCrew['crewType'] == "pending" ) { ?><b class="yellow">[ Activation Pending ]</b><? } ?>
	
	<? if( $loa == 1 ) { ?><br /><b class="red">[ On Leave of Absence ]</b><? } ?>
	<? if( $loa == 2 ) { ?><br /><b class="orange">[ On Extended Leave of Absence ]</b><? } ?>
	<? if( $fetchCrew['crewType'] == "npc" ) { ?><br /><b class="blue">[ Non-Playing Character ]</b><? } ?>
	
	<br /><br />
	
	<?php if(!empty($image)) { ?>
	<div style="float:right; max-width:175px; text-align:center;">
		<?php
		
		/* put the pics into an array */
		$pics = explode(",", $image);
		
		foreach ($pics as $key => $value)
		{ /* make sure there isn't a stray RETURN or space and clear out empty values */
			if ($value == "\n" || $value == '')
			{
				unset($pics[$key]);
			}
		}
		
		/* count them */
		$count = count($pics);
		$diff = $count - 1;
		
		switch ($count)
		{
			case 1:
				echo "<strong class='fontSmall'>Click the image for a larger version</strong><br /><br />";
				
				break;
			
			default:
				if ($diff == 1)
				{
					echo "<strong class='fontSmall'>Click the image to see the other image in the gallery</strong><br /><br />";
				}
				else
				{
					echo "<strong class='fontSmall'>Click the image to see the other ". $diff ." images in the gallery</strong><br /><br />";
				}
				
				break;
		}
		
		?>
		
		<a href="<?=$pics[0];?>" rel="shadowbox[Bio]">
			<img src="<?=$pics[0];?>" alt="" border="0" width="175" class="image reflect rheight30 ropacity40" />
		</a>
	</div>
	<?php } ?>
	
	<table class="narrowTable">
		<? if( $contactInfo == "y" && isset( $sessionCrewid ) && ( $fetchCrew['crewType'] == "active" || $fetchCrew['crewType'] == "inactive" || $fetchCrew['crewType'] == "pending") ) { ?>
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Player Information</b></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Player Name</td>
			<td>&nbsp;</td>
			<td><?=$realName;?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Email Address</td>
			<td>&nbsp;</td>
			<td><?=$email;?></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td class="fontNormal">
				<a href="<?=$webLocation;?>admin.php?page=user&sub=inbox&crew=<?=$sessionCrewid;?>&tab=3&id=<?=$crewid;?>">
					[ Send a Private Message ]
				</a>
			</td>
		</tr>
		
		<? if( !empty( $aim ) || !empty( $msn ) || !empty( $yim ) || !empty( $icq ) ) { ?>
		<tr>
			<td colspan="3" height="10"></td>
		</tr>
		<? } ?>
		
		<? if( !empty( $aim ) ) { ?>
		<tr>
			<td class="tableCellLabel">AIM</td>
			<td>&nbsp;</td>
			<td><?=$aim;?></td>
		</tr>
		<? } if( !empty( $msn ) ) { ?>
		<tr>
			<td class="tableCellLabel">MSN</td>
			<td>&nbsp;</td>
			<td><?=$msn;?></td>
		</tr>
		<? } if( !empty( $yim ) ) { ?>
		<tr>
			<td class="tableCellLabel">Yahoo!</td>
			<td>&nbsp;</td>
			<td><?=$yim;?></td>
		</tr>
		<? } if( !empty( $icq ) ) { ?>
		<tr>
			<td class="tableCellLabel">ICQ</td>
			<td>&nbsp;</td>
			<td><?=$icq;?></td>
		</tr>
		<? } ?>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<? } /* close the logic that checks if the user wants their info shown */ ?>
		
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Character Information</b></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Name</td>
			<td>&nbsp;</td>
			<td><? printText( $firstName . " " . $middleName . " " . $lastName ); ?></td>
		</tr>
		
		<? if( !empty( $fetchCrew['rankid'] ) ) { ?>
		<tr>
			<td class="tableCellLabel">Rank</td>
			<td>&nbsp;</td>
			<td><?=$fetchRank['rankName'];?></td>
		</tr>
		<? } ?>
		
		<tr>
			<td class="tableCellLabel">Position</td>
			<td>&nbsp;</td>
			<td><? printPlayerPosition( $fetchCrew['crewid'], $positionid, "" ); ?></td>
		</tr>
		<? if( !empty( $positionid2 ) ) { ?>
		<tr>
			<td class="tableCellLabel">Second Position</td>
			<td>&nbsp;</td>
			<td><? printPlayerPosition( $fetchCrew['crewid'], $positionid2, "2" ); ?></td>
		</tr>
		<? } ?>
		<tr>
			<td class="tableCellLabel">Gender</td>
			<td>&nbsp;</td>
			<td><? printText( $gender ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Species</td>
			<td>&nbsp;</td>
			<td><? printText( $species ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Age</td>
			<td>&nbsp;</td>
			<td><?=$age;?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Physical Appearance</b></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Height</td>
			<td>&nbsp;</td>
			<td><?=$heightFeet;?>&rsquo; <?=$heightInches;?>&rdquo;</td>
		</tr>
		<tr>
			<td class="tableCellLabel">Weight</td>
			<td>&nbsp;</td>
			<td><?=$weight;?> lbs.</td>
		</tr>
		<tr>
			<td class="tableCellLabel">Eye Color</td>
			<td>&nbsp;</td>
			<td><? printText( $eyeColor ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Hair Color</td>
			<td>&nbsp;</td>
			<td><? printText( $hairColor ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Physical Description</td>
			<td>&nbsp;</td>
			<td><? printText( $physicalDesc ); ?></td>
		</tr>
	</table>
	
	<div style="clear:both;"></div>
	
	<table>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Personality &amp; Traits</b></td>
		</tr>
		<tr>
			<td colspan="3" class="fontMedium"><b>General Overview</b></td>
		</tr>
		<tr>
			<td colspan="3"><? printText( $personalityOverview ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td colspan="3" class="fontMedium"><b>Strengths &amp; Weaknesses</b></td>
		</tr>
		<tr>
			<td colspan="3"><? printText( $strengths ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td colspan="3" class="fontMedium"><b>Ambitions</b></td>
		</tr>
		<tr>
			<td colspan="3"><? printText( $ambitions ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td colspan="3" class="fontMedium"><b>Hobbies &amp; Interests</b></td>
		</tr>
		<tr>
			<td colspan="3"><? printText( $hobbies ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Languages</td>
			<td>&nbsp;</td>
			<td><? printText( $languages ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
		
		<tr>
			<td colspan="3" align="center" class="fontLarge"><b>Family</b></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Father</td>
			<td>&nbsp;</td>
			<td><? printText( $father ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Mother</td>
			<td>&nbsp;</td>
			<td><? printText( $mother ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Brother(s)</td>
			<td>&nbsp;</td>
			<td><? printText( $brothers ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Sister(s)</td>
			<td>&nbsp;</td>
			<td><? printText( $sisters ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Spouse</td>
			<td>&nbsp;</td>
			<td><? printText( $spouse ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Children</td>
			<td>&nbsp;</td>
			<td><? printText( $children ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Other Family</td>
			<td>&nbsp;</td>
			<td><? printText( $otherFamily ); ?></td>
		</tr>
		<tr>
			<td colspan="3" height="15"></td>
		</tr>
	</table>
	
	<div align="center">
		<b class="fontLarge">History</b>
	</div>
	<? printText( $history ); ?>
	
	<p>&nbsp;</p>
	
	<div align="center">
		<b class="fontLarge">Service Record</b>
	</div>
	<? printText( $serviceRecord ); ?>
	
	<p>&nbsp;</p>
		
	<div align="center">
		<b class="fontLarge">Awards</b>&nbsp;
		<a href="#" id="toggleAwards" class="fontSmall">[ Hide/Show ]</a>
		
		<? if( in_array( "m_giveaward", $sessionAccess ) || in_array( "m_removeaward", $sessionAccess ) ) { ?>
		<br />
		<span class="fontSmall">
			<? if( in_array( "m_giveaward", $sessionAccess ) ) { ?>
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=addaward&crew=<?=$crew;?>">Add Award</a>
			<? } ?>
			
			<? if( in_array( "m_giveaward", $sessionAccess ) && in_array( "m_removeaward", $sessionAccess ) ) { ?>
			&nbsp; &middot &nbsp;
			<? } ?>
			
			<? if( in_array( "m_removeaward", $sessionAccess ) ) { ?>
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=removeaward&crew=<?=$crew;?>">Remove Award</a>
			<? } ?>
		</span>
		<? } ?>
	</div>
	
	<div id="awards" style="display:none; width:97%;">
		<table cellspacing="0" cellpadding="5">
			<?php
		
			/* do the database query */
			$getAwards = "SELECT awards FROM sms_crew WHERE crewid = '$crew'";
			$getAwardsResult = mysql_query( $getAwards );
			$fetchAwards = mysql_fetch_array( $getAwardsResult );
			
			/* if there are awards, continue */
			if( !empty( $fetchAwards[0] ) ) {
	
				/* explode the string at the semicolon */
				$awardsRaw = explode( ";", $fetchAwards[0] );
				$awardsRaw = array_reverse($awardsRaw);
				
				/* explode the array again */
				foreach($awardsRaw as $a => $b)
				{
					$awardsRaw[$a] = explode( "|", $b );
				}
				
				$rowCount = 0;
				$color1 = "rowColor1";
				$color2 = "rowColor2";
		
				foreach($awardsRaw as $key => $value) {
			
					/* do the database query */
					$pullAward = "SELECT * FROM sms_awards WHERE awardid = '$value[0]'";
					$pullAwardResult = mysql_query( $pullAward );

					while( $awardArray = mysql_fetch_array( $pullAwardResult ) ) {
						extract( $awardArray, EXTR_OVERWRITE );
						
						$rowColor = ( $rowCount % 2 ) ? $color1 : $color2;
		
			?>	
	
			<tr class="fontNormal <?=$rowColor;?>">	
				<td width="70"><img src="<?=$webLocation;?>images/awards/<?=$awardImage;?>" alt="<?=$awardName;?>" border="0" />
				<td>
					<b><? printText( $awardName ); ?></b>
					<?php
					
					if(count($value) > 1)
					{
						echo "<br /><span class='fontNormal'>Awarded: ";
						echo dateFormat( "short2", $value[1] );
						echo "</span>";
					}
					
					?>
				</td>
				<td>
					<?
					
					if(count($value) > 1)
					{
						printText( $value[2] );
					}
					
					?>
				</td>
			</tr>
	
			<?
	
					$rowCount++;
					
					}	/* close the while loop */
				}	/* close the foreach loop */
			} else { /* if there's nothing in the awards field */
	
			?>
	
			<tr>
				<td colspan="3" class="fontMedium orange"><b>No Awards</b></td>
			</tr>
	
			<? } ?>
		</table>
	</div>
	
	<? if( $fetchCrew['crewType'] != "npc" ) { ?>
	
		<? if( $bioShowPosts == "y" || $bioShowLogs == "y" ) { ?>
			<p>&nbsp;</p>
			
			<div align="center">
				<b class="fontLarge">Posting Activity</b>&nbsp;
				<a href="#" id="togglePosting" class="fontSmall">[ Hide/Show ]</a>
			</div>
			
			<div id="posting" style="display:none;width:97%;">
				<table>
					<? if ( $bioShowPosts == "y" ) { ?>
						<tr>
							<td colspan="4">
								<b class="fontMedium">Recent Posts</b>&nbsp;
								<span class="fontSmall">
									<a href="<?=$webLocation;?>index.php?page=userpostlist&crew=<?=$crew;?>&t=1">[ Show All Posts ]</a>
								</span>
							</td>
						</tr>

						<? if( $NumPosts == 0 ) { ?>
						<tr>
							<td colspan="4"><b class="fontMedium orange">No Posts Recorded</b></td>
						</tr>
						<? } else { ?>
						<tr class="fontNormal">
							<td width="30%"><b>Date</b></td> 
							<td width="30%"><b>Title</b></td>
							<td width="20%"><b>Location</b></td>
							<td width="20%"><b>Timeline</b></td>
						</tr>

						<?

						while( $postinfo = mysql_fetch_array( $getPostsResult ) ) {
							extract( $postinfo, EXTR_OVERWRITE );

							/* define title when no title was entered */
							if ( $postTitle == "" ) {
								$postTitle = "[ Untitled ]";
							}

						?>

						<tr class="fontNormal">
							<td><?=dateFormat( "medium", $postPosted );?></td> 
							<td><a href="<?=$webLocation;?>index.php?page=post&id=<?=$postid;?>"><? printText( $postTitle ); ?></a></td> 
							<td><? printText( $postLocation ); ?></td>
							<td><? printText( $postTimeline ); ?></td>
						</tr>
						<? } /* close the while statement */ ?>
						<tr>
							<td colspan="4" height="20"></td>
						</tr>
					</table>
				
				<?

					} /* close the else statement to show posts if present */
				
				} if ( $bioShowLogs == "y" ) {

				?>
					<table>
					<tr>
						<td colspan="4">
							<b class="fontMedium">Recent Logs</b>&nbsp;
							<span class="fontSmall">
								<a href="<?=$webLocation;?>index.php?page=userpostlist&crew=<?=$crew;?>&t=2">[ Show All Logs ]</a>
							</span>
						</td>
					</tr>

					<? if( $NumLogs == 0 ) { ?>
					<tr>
						<td colspan="4"><b class="fontMedium orange">No Logs Recorded</b></td>
					</tr>
					<? } else { ?>
					<tr class="fontNormal">
						<td width="30%"><b>Date</b></td>
						<td><b>Title</b></td> 
					</tr>

					<?

					while( $loginfo = mysql_fetch_array( $getLogsResult ) ) {
						extract( $loginfo, EXTR_OVERWRITE );

						/* define title when no title was entered */
						if( $logTitle == "" ) {
							$logTitle = "[ Untitled ]";
						}

					?>

					<tr class="fontNormal">
						<td><?=dateFormat( "medium", $logPosted );?></td>
						<td><a href="<?=$webLocation;?>index.php?page=log&id=<?=$logid;?>"><? printText( $logTitle ); ?></a></td>
					</tr>

			<?
					
					} /* close the while statement */

				} /* close the else statement to show logs if present */
					
			} /* close the full if statement to view logs */
				
			?>
				
				</table>
			</div>
			<? } /* close the check on if posting should be shown or not */ ?>
		
		<? } /* close the if not NPC */ ?>
</div>

<?php foreach ($pics as $key => $value): ?>
	<?php if ($key > 0): ?>
		<a href="<?=trim($value);?>" rel="shadowbox[Bio]">
			<img src="<?=trim($value);?>" alt="" border="0" width="100" style="display:none" />
		</a><br />
	<?php endif; ?>
<?php endforeach; ?>

<?php } ?>