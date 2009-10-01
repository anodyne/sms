<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/removeaward.php
Purpose: Page that allows an admin to remove an award from a player

System Version: 2.6.3
Last Modified: 2008-09-06 1016 EST
**/

/* access check */
if( in_array( "m_removeaward", $sessionAccess ) ) {
	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_GET['crew']) && is_numeric($_GET['crew'])) {
		$crew = $_GET['crew'];
	} else {
		$crew = NULL;
	}
	
	/* if an award key is in the URL */
	if(isset($_POST['action_type']) && $_POST['action_type'] == "remove")
	{
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		if(!is_numeric($action_crew)) {
			$action_crew = NULL;
		}
		
		if(!is_numeric($action_award)) {
			$action_award = NULL;
		}
		
		$pullAwards = "SELECT awards FROM sms_crew WHERE crewid = $action_crew LIMIT 1";
		$pullAwardsResult = mysql_query($pullAwards);
		$stringAwards = mysql_fetch_array($pullAwardsResult);
		$arrayAwards = explode(";", $stringAwards[0]);
		
		/* HAVE TO REVERSE THE ARRAY OR BAD THINGS HAPPEN! */
		$arrayAwards = array_reverse($arrayAwards);
		
		/* remove the award */
		unset($arrayAwards[$action_award]);
		
		/* have to reverse the array again so things do what they are supposed to */
		$arrayAwards = array_reverse($arrayAwards);

		/* put the string back together */
		$joinedString = implode(";", $arrayAwards);
		
		/* dump the comma separated field back into the db */
		$update = "UPDATE sms_crew SET awards = %s WHERE crewid = $action_crew LIMIT 1";
		$query = sprintf($update, escape_string($joinedString));
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
	}

	if( !isset($crew) ) {
		
		/* active crew */
		$getActive = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getActive.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$getActive.= "WHERE crew.rankid = rank.rankid AND crew.crewType = 'active' ";
		$getActive.= "ORDER BY crew.rankid ASC";
		$getActiveResult = mysql_query( $getActive );
		$activeCount = mysql_num_rows( $getActiveResult );
		
		/* inactive crew */
		$getInactive = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getInactive.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$getInactive.= "WHERE crew.rankid = rank.rankid AND crew.crewType = 'inactive' ";
		$getInactive.= "ORDER BY crew.rankid ASC";
		$getInactiveResult = mysql_query( $getInactive );
		$inactiveCount = mysql_num_rows( $getInactiveResult );
		
		/* npcs */
		$getNPC = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getNPC.= "FROM sms_crew AS crew, sms_ranks AS rank ";
		$getNPC.= "WHERE crew.rankid = rank.rankid AND crew.crewType = 'npc' ";
		$getNPC.= "ORDER BY crew.rankid ASC";
		$getNPCResult = mysql_query( $getNPC );
		$npcCount = mysql_num_rows( $getNPCResult );
	
?>
	
	<script type="text/javascript">
		$(document).ready(function(){
			$('#container-1 > ul').tabs();
		});
	</script>
	
	<div class="body">
	
		<span class="fontTitle">Remove Award From Crew Member</span><br /><br />
		To begin, please select a crew member from the list below to view and remove their awards.<br /><br />
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Active Crew (<?php echo $activeCount;?>)</span></a></li>
				<li><a href="#two"><span>Inactive Crew (<?php echo $inactiveCount;?>)</span></a></li>
				<li><a href="#three"><span>Non-Playing Characters (<?php echo $npcCount;?>)</span></a></li>
			</ul>
			
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				if($activeCount == 0)
				{
					echo "<strong class='fontLarge orange'>No active users found</strong>";
				}
				else
				{
				
				?>
				<b class="fontLarge">Active Crew</b>
				<ul class="list-dark">
					<?

					while( $userFetch = mysql_fetch_assoc( $getActiveResult ) ) {
						extract( $userFetch, EXTR_OVERWRITE );

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $userFetch['crewid'] . "'>" . stripslashes( $userFetch['rankName'] . " " . $userFetch['firstName'] . " " . $userFetch['lastName'] ) . "</a></li>";

						}

					?>
				</ul>
				<?php } ?>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				if($inactiveCount == 0)
				{
					echo "<strong class='fontLarge orange'>No inactive users found</strong>";
				}
				else
				{
				
				?>
				<b class="fontLarge">Inactive Crew</b>
				<ul class="list-dark">
					<?

					while( $inactiveFetch = mysql_fetch_assoc( $getInactiveResult ) ) {
						extract( $inactiveFetch, EXTR_OVERWRITE );

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $inactiveFetch['crewid'] . "'>" . stripslashes( $inactiveFetch['rankName'] . " " . $inactiveFetch['firstName'] . " " . $inactiveFetch['lastName'] ) . "</a></li>";

						}

					?>
				</ul>
				<?php } ?>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				if($npcCount == 0)
				{
					echo "<strong class='fontLarge orange'>No NPCs found</strong>";
				}
				else
				{
				
				?>
				<b class="fontLarge">Non-Playing Characters</b>
				<ul class="list-dark">
					<?

					while( $npcFetch = mysql_fetch_assoc( $getNPCResult ) ) {
						extract( $npcFetch, EXTR_OVERWRITE );

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=removeaward&crew=" . $npcFetch['crewid'] . "'>" . stripslashes( $npcFetch['rankName'] . " " . $npcFetch['firstName'] . " " . $npcFetch['lastName'] ) . "</a></li>";

						}

					?>
				</ul>
				<?php } ?>
			</div>
		</div>
		
	</div>
	<? } elseif( isset($crew) ) { ?>
	
	<script type="text/javascript">
		$(document).ready(function(){
			$('.zebra tr:nth-child(odd)').addClass('alt');

			$("a[rel*=facebox]").click(function() {
				var award = $(this).attr("myAward");
				var crew = $(this).attr("myID");

				jQuery.facebox(function() {
					jQuery.get('admin/ajax/award_remove.php?c=' + crew + '&a=' + award, function(data) {
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
			$check->message("crew award", "remove");
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Remove Award From <? printCrewName( $crew, "rank", "noLink" ); ?></span><br /><br />
		<b class="fontLarge">
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=removeaward">&laquo; Back to Crew List</a>
		</b>
		<br /><br />
	
		<table class="zebra" cellpadding="3" cellspacing="0">
		<?
	
		$getAwards = "SELECT awards FROM sms_crew WHERE crewid = $crew LIMIT 1";
		$getAwardsResult = mysql_query( $getAwards );
		$fetchAwards = mysql_fetch_array( $getAwardsResult );
	
		/* if $myrow isn't empty, continue */
		if(!empty($fetchAwards[0]))
		{
			/* explode the string at the comma */
			$awardsRaw = explode(";", $fetchAwards[0]);
			$awardsRaw = array_reverse($awardsRaw);
			
			/* explode the array again */
			foreach($awardsRaw as $a => $b)
			{
				$awardsRaw[$a] = explode( "|", $b );
			}
			
			/*
				Start the loop based on whether there are key/value pairs
				and keep doing 'something' until you run out of pairs
			*/
			foreach($awardsRaw as $key => $value)
			{
				/* do the database query */
				$pullAward = "SELECT * FROM sms_awards WHERE awardid = '$value[0]'";
				$pullAwardResult = mysql_query( $pullAward );
				
				if(empty($value[1])) {
					$valign = "middle";
				} else {
					$valign = "top";
				}
	
				/* Start pulling the array and populate the variables */
				while( $awardArray = mysql_fetch_array( $pullAwardResult ) ) {
					extract( $awardArray, EXTR_OVERWRITE );
					
		?>
		
			<tr height="40">
				<td width="70" align="center" valign="middle">
					<img src="<?=$webLocation;?>images/awards/<?=$awardImage;?>" alt="<?=$awardName;?>" border="0" />
				</td>
				<td valign="<?=$valign;?>">
					<strong><? printText( $awardName ); ?></strong>
					<?php if(!empty($value[1])) { ?>
					<br />
					<span class="fontSmall">Awarded: <?=dateFormat('medium2', $value[1]);?></span>
					<?php } ?>
					</span>
				</td>
				<td width="55%" valign="middle">
					<?
					
					if(!empty($value[2]))
					{
						printText($value[2]);
					}
					else
					{
						echo "<span class='orange'>No reason given</strong>";
					}
					
					?>
				</td>
				<td width="15%" align="center">
					<a href="#" rel="facebox" myAward="<?=$key;?>" myID="<?=$crew;?>" class="delete"><strong>Remove Award</strong></a>
				</td>
			</tr>
		
		<?php
	
				}
			}
		} else {
			echo "<tr class='fontLarge orange'>";
				echo "<td colspan='4'>";
					echo "<strong>There are no awards to remove!</strong>";
				echo "</td>";
			echo "</tr>";
		}
	
		?>
		</table>
	</div>
	
	<? } ?>
	
<? } else { errorMessage( "remove crew award" ); } ?>