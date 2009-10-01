<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/specifications.php
Purpose: Provides a listing from the database of the ship's specs

System Version: 2.6.0
Last Modified: 2007-10-10 1017 EST
**/

/* define the page class */
$pageClass = "ship";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

$getSpecs = "SELECT * FROM sms_specs WHERE specid = '1'";
$getSpecsResult = mysql_query( $getSpecs );

while( $specFetch = mysql_fetch_array( $getSpecsResult ) ) {
	extract( $specFetch, EXTR_OVERWRITE );
}

?>

<div class="body">
	<span class="fontTitle"><i><? printText( $shipPrefix . " " . $shipName ); ?></i> Specifications</span>
	<?

	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if( isset( $sessionCrewid ) && in_array( "m_specs", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=specifications' class='image'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
		echo "</a>";
	}
	
	?>
	<br /><br />
	
	<table>
		<tr>
			<td class="tableCellLabel">Name</td>
			<td>&nbsp;</td>
			<td><? printText( $shipPrefix . " " . $shipName ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Registry</td>
			<td>&nbsp;</td>
			<td><? printText( $shipRegistry ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Class</td>
			<td>&nbsp;</td>
			<td><? printText( $shipClass ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Role</td>
			<td>&nbsp;</td>
			<td><? printText( $shipRole ); ?></td>
		</tr>
		
		<? if( !empty( $fleet ) ) { ?>
		<tr>
			<td class="tableCellLabel">Fleet</td>
			<td>&nbsp;</td>
			<td>
				<?
				
				if( !empty( $fleetURL ) ) {
					echo "<a href='" . $fleetURL . "' target='_blank'>";
					printText( $fleet );
					echo "</a>";
				} else {
					printText( $fleet );
				}
				
				?>
			</td>
		</tr>
		<? } ?>
		
		<? if( $tfMember == "y" ) { ?>
		<tr>
			<td class="tableCellLabel">Task Force</td>
			<td>&nbsp;</td>
			<td>
				<?
				
				if( !empty( $tfURL ) ) {
					echo "<a href='" . $tfURL . "' target='_blank'>";
					printText( $tfName );
					echo "</a>";
				} else {
					printText( $tfName );
				}
				
				?>
			</td>
		</tr>
		<? } ?>
		
		<? if( $tgMember == "y" ) { ?>
		<tr>
			<td class="tableCellLabel">Task Group</td>
			<td>&nbsp;</td>
			<td>
				<?
				
				if( !empty( $tgURL ) ) {
					echo "<a href='" . $tgURL . "' target='_blank'>";
					printText( $tgName );
					echo "</a>";
				} else {
					printText( $tgName );
				}
				
				?>
			</td>
		</tr>
		<? } ?>
		
		<tr>
			<td colspan="3" height="15">
		</tr>
		
		<tr>
			<td class="tableCellLabel">Expected Duration</td>
			<td>&nbsp;</td>
			<td><? printText( $duration . " " . $durationUnit ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Time Before Refit</td>
			<td>&nbsp;</td>
			<td><? printText( $refit . " " . $refitUnit ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Time Before Resupply</td>
			<td>&nbsp;</td>
			<td><? printText( $resupply . " " . $resupplyUnit ); ?></td>
		</tr>
		
		<tr>
			<td colspan="3" height="15">
		</tr>
		
		<tr>
			<td class="tableCellLabel">Length</td>
			<td>&nbsp;</td>
			<td><?=$length . " Meters";?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Width</td>
			<td>&nbsp;</td>
			<td><?=$width . " Meters";?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Height</td>
			<td>&nbsp;</td>
			<td><?=$height . " Meters";?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Decks</td>
			<td>&nbsp;</td>
			<td><?=$decks;?></td>
		</tr>
		
		<tr>
			<td colspan="3" height="15">
		</tr>
		
		<tr>
			<td class="tableCellLabel">Standard Compliment</td>
			<td>&nbsp;</td>
			<td>
				<?=$complimentOfficers + $complimentEnlisted + $complimentMarines + $complimentCivilians;?>
			</td>
		</tr>
		
		<? if( !empty( $complimentOfficers ) ) { ?>
		<tr class="fontSmall">
			<td class="tableCellLabel">Officers</td>
			<td>&nbsp;</td>
			<td><?=$complimentOfficers;?></td>
		</tr>
		<? } if( !empty( $complimentEnlisted ) ) { ?>
		<tr class="fontSmall">
			<td class="tableCellLabel">Enlisted Officers</td>
			<td>&nbsp;</td>
			<td><?=$complimentEnlisted;?></td>
		</tr>
		<? } if( !empty( $complimentMarines ) ) { ?>
		<tr class="fontSmall">
			<td class="tableCellLabel">Marines</td>
			<td>&nbsp;</td>
			<td><?=$complimentMarines;?></td>
		</tr>
		<? } if( !empty( $complimentCivilians ) ) { ?>
		<tr class="fontSmall">
			<td class="tableCellLabel">Civilians</td>
			<td>&nbsp;</td>
			<td><?=$complimentCivilians;?></td>
		</tr>
		<? } ?>
		
		<tr>
			<td class="tableCellLabel">Emergency Capacity</td>
			<td>&nbsp;</td>
			<td><?=$complimentEmergency;?></td>
		</tr>
		
		<tr>
			<td colspan="3" height="15">
		</tr>
		
		<tr>
			<td class="tableCellLabel">Standard Velocity</td>
			<td>&nbsp;</td>
			<td>Warp <?=$warpCruise;?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Maximum Velocity</td>
			<td>&nbsp;</td>
			<td>
				Warp <?=$warpMaxCruise;?>
				<?
				
				if( !empty( $warpMaxTime ) ) {
					echo $warpMaxTime;
				}
				
				?>
			</td>
		</tr>
		<tr>
			<td class="tableCellLabel">Emergency Velocity</td>
			<td>&nbsp;</td>
			<td>
				Warp <?=$warpEmergency;?>
				<?
				
				if( !empty( $warpEmergencyTime ) ) {
					echo $warpEmergencyTime;
				}
				
				?>
			</td>
		</tr>
		
		<tr>
			<td colspan="3" height="15">
		</tr>
		
		<tr>
			<td class="tableCellLabel">Phasers</td>
			<td>&nbsp;</td>
			<td><? printText( $phasers ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Torpedo Launchers</td>
			<td>&nbsp;</td>
			<td><? printText( $torpedoLaunchers ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Torpedo Compliment</td>
			<td>&nbsp;</td>
			<td><? printText( $torpedoCompliment ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Defensive Systems</td>
			<td>&nbsp;</td>
			<td><? printText( $defensive ); ?></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Shields</td>
			<td>&nbsp;</td>
			<td><? printText( $shields ); ?></td>
		</tr>
		
		<tr>
			<td colspan="3" height="15">
		</tr>
		
		<tr>
			<td class="tableCellLabel">Shuttlebays</td>
			<td>&nbsp;</td>
			<td><?=$shuttlebays;?></td>
		</tr>
		
		<? if( $hasShuttles == "y" ) { ?>
		<tr>
			<td class="tableCellLabel">Shuttles</td>
			<td>&nbsp;</td>
			<td><? printText( $shuttles ); ?></td>
		</tr>
		<? } if( $hasRunabouts == "y" ) { ?>
		<tr>
			<td class="tableCellLabel">Runabouts</td>
			<td>&nbsp;</td>
			<td><? printText( $runabouts ); ?></td>
		</tr>
		<? } if( $hasFighters == "y" ) { ?>
		<tr>
			<td class="tableCellLabel">Fighters</td>
			<td>&nbsp;</td>
			<td><? printText( $fighters ); ?></td>
		</tr>
		<? } if( $hasTransports == "y" ) { ?>
		<tr>
			<td class="tableCellLabel">Transports</td>
			<td>&nbsp;</td>
			<td><? printText( $transports ); ?></td>
		</tr>
		<? } ?>
	</table>
</div> <!-- close the div id content tag -->