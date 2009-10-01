<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ anodyne.sms@gmail.com ]
File: admin/reports/strikes.php
Purpose: Page to view all strikes against players

System Version: 2.5.0
Last Modified: 2007-03-29 1215 EST
**/

/* access check */
if( in_array( "r_strikes", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "reports";

?>

	<div class="body">
		<span class="fontTitle">Strike List</span><br /><br />
		
			The strike list shows a complete list of all strikes issued as well as the reason why the
			player was struck. The CO can add and remove strikes from the strike moderation page.
			The strike system allows for color-coding up to five strikes.<br /><br />
			
			<ul style="margin: 0; list-style-type: none; padding: 0;">
				<?
				
				$strikes = "SELECT * FROM sms_strikes WHERE crewid > '' ORDER BY strikeid DESC";
				$strikesResult = mysql_query($strikes);
				
				while( $allStrikes = mysql_fetch_assoc( $strikesResult ) ) {
					extract( $allStrikes, EXTR_OVERWRITE );
				
				?>
				<li>
					<?
					
					echo "<b class='";
					
					switch( $number ) {
						case 1:
							echo "blue";
							break;
						case 2:
							echo "green";
							break;
						case 3:
							echo "yellow";
							break;
						 case 4:
							echo "orange";
							break;
						 case 5:
							echo "red";
							break;
					}
						
					echo "'>";
					
					?>
					<i><?=dateFormat( "long", $strikeDate );?></i> - 
					<? printCrewName( $crewid, "rank", "noLink" ); ?> [ Strike #<?=$allStrikes['number'];?> ]</b> - 
					<? printText( $reason ); ?>
				</li><br />
				<? } ?>
			</ul>
	</div>

<? } else { errorMessage( "strike listing" ); } ?>