<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/coc.php
Purpose: Page to display the chain of command

System Version: 2.6.0
Last Modified: 2008-04-20 1409 EST
**/

/* define the page class */
$pageClass = "personnel";

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

?>

<div class="body">
	<span class="fontTitle">Chain of Command</span>
	
	<?
	
	if( isset( $sessionCrewid ) && in_array( "m_coc", $sessionAccess ) ) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='" . $webLocation . "admin.php?page=manage&sub=coc' class='image'>";
		echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
		echo "</a>";
	}
	
	?><br /><br />
	
	<table>
		<tr>
			<td class="dept_left" align="center" colspan="4">&nbsp;</td>
		</tr>
		<?

		/* do the database query */
		$sql = "SELECT coc.cocid, coc.crewid, crew.crewid, crew.firstName, crew.lastName, crew.species, ";
		$sql.= "crew.gender, crew.loa, rank.rankName, rank.rankImage, position.positionName FROM ";
		$sql.= "sms_coc AS coc, sms_crew AS crew, sms_ranks AS rank, sms_positions AS position WHERE ";
		$sql.= "coc.crewid = crew.crewid AND crew.rankid = rank.rankid AND crew.positionid = position.positionid ";
		$sql.= "AND crew.crewType = 'active' ORDER BY coc.cocid ASC";
		$result = mysql_query( $sql );
		
		while( $myrow = mysql_fetch_array( $result ) ) {
			extract( $myrow, EXTR_OVERWRITE );
		
		?>
		
		<tr>
			<td height="38">
				<font class="fontNormal"><b><? printText( $positionName ); ?></b></font>
			</td>
			<td width="130"><center><img src="<?=$webLocation . 'images/ranks/' . $rankSet . '/' . $rankImage;?>" alt="rank image" /></center></td>
			<td width="40%"><font class="fontSmall">
				<b><? printText( $rankName . " " . $firstName . " " . $lastName ); ?></b><br />
				<? printText( $species . " " . $gender ); ?>
			</td>
			<td width="37" align="center">
				<a href="<?=$webLocation;?>index.php?page=bio&crew=<?=$crewid;?>" class="image">
				<? if($loa == 1) { ?>
					<img src="images/combadge-loa.png" border="0" alt="loa" />
				<? } elseif($loa == 0) { ?>
					<img src="images/combadge.png" border="0" alt="combadge" />
				<? } elseif($loa == 2) { ?>
					<img src="images/combadge-eloa.png" border="0" alt="combadge" />
				<? } ?>
				</a>
			</td>
		  </tr>
	
		<? } ?>
	
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
	</table>
</div>