<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/addaward.php
Purpose: Page that allows an admin to add an award for a player

System Version: 2.6.3
Last Modified: 2008-09-06 1042 EST
**/

/* access check */
if( in_array( "m_giveaward", $sessionAccess ) ) {
	
	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$query = FALSE;
	$result = FALSE;
	
	/* do some checking to make sure someone's not trying to do a SQL injection */
	if( isset( $_GET['crew'] ) && !is_numeric( $_GET['crew'] ) ) {
		errorMessageIllegal( "add award page" );
		exit();
	} elseif( isset( $_GET['crew'] ) && is_numeric( $_GET['crew'] ) ) {
		$crew = $_GET['crew'];
	}
	
	if( isset( $_GET['award'] ) && !is_numeric( $_GET['award'] ) ) {
		errorMessageIllegal( "add award page" );
		exit();
	} elseif( isset( $_GET['award'] ) && is_numeric( $_GET['award'] ) ) {
		$award = $_GET['award'];
	}
		
	/* if an award key is in the URL */
	if(isset($_POST['action_type']) && $_POST['action_type'] == "give")
	{
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		if(!is_numeric($action_crew)) {
			$action_crew = NULL;
		}
		
		$pullAwards = "SELECT awards FROM sms_crew WHERE crewid = $action_crew LIMIT 1";
		$pullAwardsResult = mysql_query($pullAwards);
		$stringAwards = mysql_fetch_array($pullAwardsResult);
		
		/* don't explode the array if there's nothing there to start with */
		if(!empty($stringAwards[0])) {
			$arrayAwards = explode(";", $stringAwards[0]);
		} else {
			$arrayAwards = array();
		}
		
		/* get the date info from PHP */
		$now = getdate();
		
		/* make sure there are no semicolons in the reason */
		$reason = str_replace(";", ",", $reason);
		
		/* build the new award entry */
		$arrayAwards[] = $action_award . "|" . $now[0] . "|" . $reason;

		/* put the string back together */
		$joinedString = implode(";", $arrayAwards);
		
		/* dump the comma separated field back into the db */
		$update = "UPDATE sms_crew SET awards = %s WHERE crewid = $action_crew LIMIT 1";
		$query = sprintf($update, escape_string($joinedString));
		$result = mysql_query($query);
		
		/* optimize the table */
		optimizeSQLTable( "sms_crew" );
	}
	
	if(!isset($crew))
	{
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
		
		$disableInactive = NULL;
		$disableNPC = NULL;
		
		if( $inactiveCount == 0 ) {
			$disableInactive = "2, ";
		} if( $npcCount == 0 ) {
			$disableNPC = "3";
		}

		$disable = $disableInactive . $disableNPC;
	
?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#container-1 > ul').tabs({ disabled: [<?php echo $disable; ?>] });
		});
	</script>
	
	<div class="body">
	
		<span class="fontTitle">Give Award To Crew Member</span><br /><br />
		Awards are broken into three categories: <b>in character</b>, <b>out of character</b>, and <b>both</b>. Playing characters
		can be given awards from any of those three categories. Non-playing characters can only be given in character awards. To
		begin, please select a crew member from the list below to give them an award.<br /><br />
	
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Active Crew (<?php echo $activeCount;?>)</span></a></li>
				<li><a href="#two"><span>Inactive Crew (<?php echo $inactiveCount;?>)</span></a></li>
				<li><a href="#three"><span>Non-Playing Characters (<?php echo $npcCount;?>)</span></a></li>
			</ul>
			
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<b class="fontLarge">Active Crew</b>
				<ul class="list-dark">
					<?

					while( $userFetch = mysql_fetch_assoc( $getActiveResult ) ) {
						extract( $userFetch, EXTR_OVERWRITE );

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=addaward&crew=" . $userFetch['crewid'] . "'>" . stripslashes( $userFetch['rankName'] . " " . $userFetch['firstName'] . " " . $userFetch['lastName'] ) . "</a></li>";

						}

					?>
				</ul>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<b class="fontLarge">Inactive Crew</b>
				<ul class="list-dark">
					<?

					while( $inactiveFetch = mysql_fetch_assoc( $getInactiveResult ) ) {
						extract( $inactiveFetch, EXTR_OVERWRITE );

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=addaward&crew=" . $inactiveFetch['crewid'] . "'>" . stripslashes( $inactiveFetch['rankName'] . " " . $inactiveFetch['firstName'] . " " . $inactiveFetch['lastName'] ) . "</a></li>";

						}

					?>
				</ul>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<b class="fontLarge">Non-Playing Characters</b>
				<ul class="list-dark">
					<?

					while( $npcFetch = mysql_fetch_assoc( $getNPCResult ) ) {
						extract( $npcFetch, EXTR_OVERWRITE );

						echo "<li><a href='" . $webLocation . "admin.php?page=manage&sub=addaward&crew=" . $npcFetch['crewid'] . "'>" . stripslashes( $npcFetch['rankName'] . " " . $npcFetch['firstName'] . " " . $npcFetch['lastName'] ) . "</a></li>";

						}

					?>
				</ul>
			</div>
		</div>
		
	</div>
	<? } elseif(isset($crew)) { ?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.zebra tr:nth-child(odd)').addClass('alt');
			
			$("a[rel*=facebox]").click(function() {
				var award = $(this).attr("myAward");
				var crew = $(this).attr("myID");

				jQuery.facebox(function() {
					jQuery.get('admin/ajax/award_give.php?c=' + crew + '&a=' + award, function(data) {
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
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "player award", "add" );
			$check->display();
		}
		
		?>
		
		<span class="fontTitle">Give Award To <? printCrewName( $crew, "rank", "noLink" ); ?></span><br /><br />
		<b class="fontMedium">
			<a href="<?=$webLocation;?>admin.php?page=manage&sub=addaward">&laquo; Back to Crew List</a>
		</b><br /><br />
		
		<table class="zebra" cellpadding="3" cellspacing="0">
		<?
		
		$getCrew = "SELECT crewType FROM sms_crew WHERE crewid = $crew LIMIT 1";
		$getCrewResult = mysql_query($getCrew);
		$crewFetch = mysql_fetch_array($getCrewResult);
		
		if( $crewFetch[0] == "npc" ) {
			$tail = "WHERE awardCat = 'ic' ORDER BY awardOrder ASC";
		} else {
			$tail = "ORDER BY awardOrder ASC";
		}
		
		$getAwards = "SELECT * FROM sms_awards $tail";
		$getAwardsResult = mysql_query( $getAwards );
	
		/* Start pulling the array and populate the variables */
		while( $awardFetch = mysql_fetch_array( $getAwardsResult ) ) {
			extract( $awardFetch, EXTR_OVERWRITE );
	
		?>
	
			<tr height="40">
				<td width="70" align="center" valign="middle">
					<img src="<?=$webLocation;?>images/awards/<?=$awardImage;?>" alt="<?=$awardName;?>" border="0" />
				</td>
				<td valign="top">
					<strong><? printText( $awardName ); ?></strong><br />
					<span class="fontSmall"><? printText( $awardDesc ); ?></span>
				</td>
				<td width="10%" align="center">
					<a href="#" rel="facebox" myAward="<?=$awardid;?>" myID="<?=$crew;?>" class="add"><strong>Give Award</strong></a>
				</td>
			</tr>
	
		<? } ?>
		</table>
	</div>
	
	<? } ?>

<? } else { errorMessage( "add crew award" ); } ?>