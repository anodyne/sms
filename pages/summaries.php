<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/summaries.php
Purpose: To display the mission summary for new players

System Version: 2.6.0
Last Modified: 2007-11-13 2105 EST
**/

/* define the page class */
$pageClass = "simm";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* pull all info on current mission */
$getmissionCurrent = "SELECT * ,";
$getmissionCurrent.= "DATE_FORMAT(missionStart,'%W, %M %d, %Y at %h:%i%p') as dateStart, ";
$getmissionCurrent.= "DATE_FORMAT(missionEnd,'%W, %M %d, %Y at %h:%i%p') as dateEnd ";
$getmissionCurrent.= "FROM sms_missions ";
$getmissionCurrent.= "WHERE missionStatus = 'current'";
$getmissionCurrentResult = mysql_query( $getmissionCurrent );
$currentCount = mysql_num_rows( $getmissionCurrentResult );

$getmissionCompleted = "SELECT * ,";
$getmissionCompleted.= "DATE_FORMAT(missionStart,'%W, %M %d, %Y at %h:%i%p') as dateStart, ";
$getmissionCompleted.= "DATE_FORMAT(missionEnd,'%W, %M %d, %Y at %h:%i%p') as dateEnd ";
$getmissionCompleted.= "FROM sms_missions ";
$getmissionCompleted.= "WHERE missionStatus = 'completed' ORDER BY missionOrder DESC";
$getmissionCompletedResult = mysql_query( $getmissionCompleted );
$completedCount = mysql_num_rows( $getmissionCompletedResult );

if( $currentCount == 0 ) {
	$disableCurrent = "1, ";
} else {
	$disableCurrent = "";
}

if( $completedCount == 0 ) {
	$disableCompleted = "2 ";
} else {
	$disableCompleted = "";
}

$disable = $disableCurrent . $disableCompleted;

?>

<div class="body">
	
	<script type="text/javascript">
		$(document).ready(function(){
			$('#container-1 > ul').tabs({ disabled: [<?php echo $disable; ?>] });
		});
	</script>

	<span class="fontTitle">Mission Summaries</span>
	<?
	
	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if( isset( $sessionCrewid ) && in_array( "m_missionsummaries", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=summaries' class='image'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
		echo "</a>";
	}
	
	?>
	<br /><br />
	
	<div id="container-1">
		<ul>
			<li><a href="#one"><span>Current Mission</span></a></li>
			<li><a href="#two"><span>Completed Missions</span></a></li>
		</ul>
		
		<div id="one" class="ui-tabs-container ui-tabs-hide">
			<?php
			
			while( $missionCurrent = mysql_fetch_array( $getmissionCurrentResult ) ) {
				extract( $missionCurrent, EXTR_OVERWRITE );
				
			?>
			
			<b class="fontLarge">
				<a href="<?=$webLocation;?>index.php?page=mission&id=<?=$missionid;?>">
				<?php printText ( $missionTitle ); ?>
				</a>&nbsp;
				<span class="yellow">[In Progress]</span>
			</b><br />
			
			<div class="specialPadding1">
				<?php printText( $missionSummary ); ?>
			</div>
			
			<?php } /* close the while loop */ ?>
		</div>
		
		<div id="two" class="ui-tabs-container ui-tabs-hide">
			<?php
			
			while( $missionComplete = mysql_fetch_array( $getmissionCompletedResult ) ) {
				extract( $missionComplete, EXTR_OVERWRITE );
				
			?>
			
			<b class="fontLarge">
				<a href="<?=$webLocation;?>index.php?page=mission&id=<?=$missionid;?>">
				<?php printText ( $missionTitle ); ?>
				</a>
			</b><br />
			
			<div class="specialPadding1">
				<?php printText( $missionSummary ); ?>
			</div>
			
			<?php } /* close the while loop */ ?>
		</div>
	
	</div>

</div> <!--Close the div body class tag-->