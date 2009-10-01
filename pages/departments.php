<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/departments.php
Purpose: To display the list of departments offered by the SIMM and their
	associated positions

System Version: 2.6.8
Last Modified: 2008-12-29 1524 EST
**/

/* define the page class and vars */
$pageClass = "ship";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

/* pull all the available departments that should be displayed */
$getDept = "SELECT * FROM sms_departments WHERE deptDisplay = 'y' ORDER BY deptOrder ASC";
$getDeptResult = mysql_query( $getDept );
$positionsArray = array();

?>
<script type="text/javascript">
	$(document).ready(function() {
		$('a.toggle').click(function() {
			var id = $(this).attr('myID');
			var div = "#" + id;
			$(div).toggle();
			
			return false;
		});
		
		$('.zebra tr:even').addClass('rowColor1');
	});
</script>

<div class="body">
	<span class="fontTitle">Departments &amp; Positions</span>
	<?
	
	/* if they have access, display an icon that will take them to edit the entry */
	if( isset( $sessionCrewid ) && in_array( "m_departments", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=departments' class='image'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
		echo "</a>";
	}
	
	?>
	<br /><br />
	
	<table>
	
	<?
	
	/* rip through the dept data */
	while( $deptinfo = mysql_fetch_array( $getDeptResult ) ) {
		extract( $deptinfo, EXTR_OVERWRITE );
		
		/* get the positions */
		$getPositions = "SELECT * FROM sms_positions WHERE positionDisplay = 'y' AND positionDept = '$deptid' ORDER BY positionOrder ASC";
		$getPositionsResult = mysql_query( $getPositions );
		
		/* rip through the positions and put them into a multi-dimensional array */
		while($posFetch = mysql_fetch_assoc($getPositionsResult)) {
			extract($posFetch, EXTR_OVERWRITE);

			$positionsArray[$deptid][] = array($positionName, $positionDesc);

		}
		
	?>
	
	<tr>
		<td width="30%" valign="top">
			<b class="fontMedium"><span style="color:#<?=$deptColor;?>;"><?php printText( $deptName ); ?></span></b><br />
			<a href="#" myID="<?=$deptid;?>" class="fontSmall toggle">[ Toggle Positions ]</a>
		</td>
		<td width="5"></td>
		<td valign="top">
			<? printText( $deptDesc ); ?>
			<div id="<?=$deptid;?>" style="display:none;">
				<br />
				<table class="fontNormal zebra" cellspacing="0" cellpadding="3">
					<?php foreach($positionsArray[$deptid] as $key => $value) { ?>
					<tr>
						<td class="tableCellLabel"><b><?php printText( $value[0] );?></b></td>
						<td width="10"></td>
						<td><?php printText( $value[1] );?></td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	
	<?php } /* close the while statement */ ?>
	
	</table>
</div>