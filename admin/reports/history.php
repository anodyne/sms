<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/reports/history.php
Purpose: Page to show the version history of SMS

System Version: 2.6.0
Last Modified: 2008-02-05 1425 EST
**/

/* access check */
if( in_array( "r_versions", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "reports";
	
	/* query the database for the data */
	$query = "SELECT * FROM sms_system_versions ORDER BY versionDate DESC";
	$result = mysql_query( $query );
	$versions = array( '2.6' => array(), '2.5' => array(), '2.4' => array(), '2.3' => array(), '2.2' => array(), '2.1' => array(), '2.0' => array() );
	
	while( $versionFetch = mysql_fetch_array( $result ) ) {
		extract( $versionFetch, EXTR_OVERWRITE );
		
		$ver = substr( $version, 0, 3);
		
		$versions[$ver][$version] = array( $versionDate, $versionDesc, $versionRev );
		
	}
	
	/*echo "<pre>";
	print_r($versions);
	echo "</pre>";*/
	
?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#container-1 > ul').tabs();
		});
	</script>
	
	<div class="body">
		<span class="fontTitle">SMS Version History</span><br /><br />
		At Anodyne Productions, we believe that it's not enough to put out a good product,
		you have to maintain it too.  To that end, we are committed to providing frequent
		and substantial updates to SMS to patch bugs that we have missed in testing, add
		additional functionality to existing features in the hope of making life even easier
		for COs, and adding new features that will have COs wondering what they did before
		it came along.  Though updates may not always be the easiest or fastest thing, we
		believe they are beneficial to making life onboard your sim easier for the CO as well
		as the player.  Below is a version history of SMS since the release of SMS 2 on
		July 24, 2006.<br /><br />
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>2.6</span></a></li>
				<li><a href="#two"><span>2.5</span></a></li>
				<li><a href="#three"><span>2.4</span></a></li>
				<li><a href="#four"><span>2.3</span></a></li>
				<li><a href="#five"><span>2.2</span></a></li>
				<li><a href="#six"><span>2.1</span></a></li>
				<li><a href="#seven"><span>2.0</span></a></li>
			</ul>
			
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				foreach($versions['2.6'] as $key => $value)
				{
					/* split the description into an array */
					$desc = explode( ";", $value[1] );

					echo "<b class='fontMedium'>" . $key;
					echo "&nbsp;&nbsp;&nbsp;";
					echo "<span class='fontSmall blue'>[ " . dateFormat( 'short2', $value[0] ) . " ]</span></b>";
					echo "<ul class='version'>";

					/* loop through the array and print out the data */
					foreach( $desc as $k => $v ) {

						echo "<li>" . $v . "</li>";

					}

					/* close the list */
					echo "</ul>";
				}
				
				?>
			</div>
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				foreach($versions['2.5'] as $key => $value)
				{
					/* split the description into an array */
					$desc = explode( ";", $value[1] );

					echo "<b class='fontMedium'>" . $key;
					echo "&nbsp;&nbsp;&nbsp;";
					echo "<span class='fontSmall blue'>[ " . dateFormat( 'short2', $value[0] ) . " ]</span></b>";
					echo "<ul class='version'>";

					/* loop through the array and print out the data */
					foreach( $desc as $k => $v ) {

						echo "<li>" . $v . "</li>";

					}

					/* close the list */
					echo "</ul>";
				}
				
				?>
			</div>
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				foreach($versions['2.4'] as $key => $value)
				{
					/* split the description into an array */
					$desc = explode( ";", $value[1] );

					echo "<b class='fontMedium'>" . $key;
					echo "&nbsp;&nbsp;&nbsp;";
					echo "<span class='fontSmall blue'>[ " . dateFormat( 'short2', $value[0] ) . " ]</span></b>";
					echo "<ul class='version'>";

					/* loop through the array and print out the data */
					foreach( $desc as $k => $v ) {

						echo "<li>" . $v . "</li>";

					}

					/* close the list */
					echo "</ul>";
				}
				
				?>
			</div>
			<div id="four" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				foreach($versions['2.3'] as $key => $value)
				{
					/* split the description into an array */
					$desc = explode( ";", $value[1] );

					echo "<b class='fontMedium'>" . $key;
					echo "&nbsp;&nbsp;&nbsp;";
					echo "<span class='fontSmall blue'>[ " . dateFormat( 'short2', $value[0] ) . " ]</span></b>";
					echo "<ul class='version'>";

					/* loop through the array and print out the data */
					foreach( $desc as $k => $v ) {

						echo "<li>" . $v . "</li>";

					}

					/* close the list */
					echo "</ul>";
				}
				
				?>
			</div>
			<div id="five" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				foreach($versions['2.2'] as $key => $value)
				{
					/* split the description into an array */
					$desc = explode( ";", $value[1] );

					echo "<b class='fontMedium'>" . $key;
					echo "&nbsp;&nbsp;&nbsp;";
					echo "<span class='fontSmall blue'>[ " . dateFormat( 'short2', $value[0] ) . " ]</span></b>";
					echo "<ul class='version'>";

					/* loop through the array and print out the data */
					foreach( $desc as $k => $v ) {

						echo "<li>" . $v . "</li>";

					}

					/* close the list */
					echo "</ul>";
				}
				
				?>
			</div>
			<div id="six" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				foreach($versions['2.1'] as $key => $value)
				{
					/* split the description into an array */
					$desc = explode( ";", $value[1] );

					echo "<b class='fontMedium'>" . $key;
					echo "&nbsp;&nbsp;&nbsp;";
					echo "<span class='fontSmall blue'>[ " . dateFormat( 'short2', $value[0] ) . " ]</span></b>";
					echo "<ul class='version'>";

					/* loop through the array and print out the data */
					foreach( $desc as $k => $v ) {

						echo "<li>" . $v . "</li>";

					}

					/* close the list */
					echo "</ul>";
				}
				
				?>
			</div>
			<div id="seven" class="ui-tabs-container ui-tabs-hide">
				<?php
				
				foreach($versions['2.0'] as $key => $value)
				{
					/* split the description into an array */
					$desc = explode( ";", $value[1] );

					echo "<b class='fontMedium'>" . $key;
					echo "&nbsp;&nbsp;&nbsp;";
					echo "<span class='fontSmall blue'>[ " . dateFormat( 'short2', $value[0] ) . " ]</span></b>";
					echo "<ul class='version'>";

					/* loop through the array and print out the data */
					foreach( $desc as $k => $v ) {

						echo "<li>" . $v . "</li>";

					}

					/* close the list */
					echo "</ul>";
				}
				
				?>
			</div>
		</div>
				
	</div>
	
<? } else { errorMessage( "SMS version history" ); } ?>