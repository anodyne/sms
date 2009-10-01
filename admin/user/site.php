<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/user/site.php
Purpose: Page that allows a user to various site options

System Version: 2.6.1
Last Modified: 2008-08-16 1631 EST
**/

/* access check */
if( in_array( "u_options", $sessionAccess ) ) {
	
	/* set the page variables */
	$pageClass = "admin";
	$subMenuClass = "user";
	$query = FALSE;
	$result = FALSE;
	
	if( isset( $_GET['crew'] ) ) {
		if( is_numeric( $_GET['crew'] ) ) {
			$crew = $_GET['crew'];
		} else {
			exit;
		}
	}
	
	if( isset($_GET['sec']) && is_numeric($_GET['sec']) ) {
		$sec = $_GET['sec'];
	} else {
		$sec = 1;
	}
	
	if(isset($_POST))
	{
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		if(isset($options_x))
		{
			/* build the query */
			$update = "UPDATE sms_crew SET cpShowPosts = %s, cpShowLogs = %s, cpShowNews = %s, ";
			$update.= "cpShowPostsNum = %d, cpShowLogsNum = %d, cpShowNewsNum = %d WHERE crewid = '$sessionCrewid' LIMIT 1";
			
			/* escape the strings into the query */
			$query = sprintf(
				$update,
				escape_string( $cpShowPosts ),
				escape_string( $cpShowLogs ),
				escape_string( $cpShowNews ),
				escape_string( $cpShowPostsNum ),
				escape_string( $cpShowLogsNum ),
				escape_string( $cpShowNewsNum )
			);
			
			/* execute the query */
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_crew" );
			
			/* set the type */
			$type = "site options";
			
		} /* close OPTIONS section */
		if(isset($menu_update_x))
		{
			/* build the query */
			$update = "UPDATE sms_crew SET menu1 = %s, menu2 = %s, menu3 = %s, menu4 = %s, menu5 = %s, ";
			$update.= "menu6 = %s, menu7 = %s, menu8 = %s, menu9 = %s, menu10 = %s WHERE crewid = '$sessionCrewid' LIMIT 1";
			
			/* escape the strings into the query */
			$query = sprintf(
				$update,
				escape_string( $menu1 ),
				escape_string( $menu2 ),
				escape_string( $menu3 ),
				escape_string( $menu4 ),
				escape_string( $menu5 ),
				escape_string( $menu6 ),
				escape_string( $menu7 ),
				escape_string( $menu8 ),
				escape_string( $menu9 ),
				escape_string( $menu10 )
			);
			
			/* execute the query */
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_crew" );
			
			/* set the type */
			$type = "personalized menu";
			
		} /* close MENU section */
		if(isset($rank_update_x))
		{
			/* build the query */
			$update = "UPDATE sms_crew SET displayRank = %s WHERE crewid = '$sessionCrewid' LIMIT 1";
			
			/* escape the strings into the query */
			$query = sprintf(
				$update,
				escape_string( $rankSet )
			);
			
			/* execute the query */
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_crew" );
			
			/* set the type */
			$type = "rank set";
			
			/* set a new session variable */
			$_SESSION['sessionDisplayRank'] = $rankSet;
			$sessionDisplayRank = $_SESSION['sessionDisplayRank'];
			
		} /* close RANK section */
		if(isset($skin_update_x))
		{
			/* build the query */
			$update = "UPDATE sms_crew SET displaySkin = %s WHERE crewid = '$sessionCrewid' LIMIT 1";
			
			/* escape the strings into the query */
			$query = sprintf(
				$update,
				escape_string( $changeSkin )
			);
			
			/* execute the query */
			$result = mysql_query( $query );
			
			/* optimize the table */
			optimizeSQLTable( "sms_crew" );
			
			/* set the type */
			$type = "skin";
			
			/* set a new session variable */
			$_SESSION['sessionDisplaySkin'] = $changeSkin;
			$sessionDisplaySkin = $_SESSION['sessionDisplaySkin'];
			
		} /* close RANK section */
	} /* close if(isset($_POST)) */

?>
	
	<script type="text/javascript">
		$(document).ready(function(){
			$('#container-1 > ul').tabs(<?=$sec;?>);
		});
	</script>
	
<?php
	
	/* query the database for the general items */
	$query1 = "SELECT * FROM sms_menu_items WHERE menuAvailability = 'on' AND menuCat = 'general' ";
	$query1.= "ORDER BY menuMainSec, menuGroup, menuOrder ASC";
	$result1 = mysql_query( $query1 );
	
	/* query the database for the admin items */
	$query2 = "SELECT * FROM sms_menu_items WHERE menuAvailability = 'on' AND menuCat = 'admin' ";
	$query2.= "ORDER BY menuMainSec, menuGroup, menuOrder ASC";
	$result2 = mysql_query( $query2 );
	
	/* query the database for the admin items */
	$query3 = "SELECT * FROM sms_database WHERE dbDisplay = 'y' AND dbType != 'offsite' ORDER BY dbOrder ASC";
	$result3 = mysql_query( $query3 );
	
	/* loop through the general items and put them into a 2d array */
	while( $fetch1 = mysql_fetch_assoc( $result1 ) ) {
		extract( $fetch1, EXTR_OVERWRITE );
		
		$array1[] = array(
			$fetch1['menuid'],
			$fetch1['menuTitle'],
			$fetch1['menuMainSec']
		);
		
	}
	
	/* loop through the admin items and them into a 2d array */
	while( $fetch2 = mysql_fetch_assoc( $result2 ) ) {
		extract( $fetch2, EXTR_OVERWRITE );
		
		$array2[] = array(
			$fetch2['menuid'],
			$fetch2['menuTitle'],
			$fetch2['menuAccess'],
			$fetch2['menuMainSec']
		);
		
	}
	
	/* rip through the array and remove any items that shouldn't be there */
	foreach( $array2 as $a => $b ) {
		if( !in_array( $b[2], $sessionAccess ) ) {
			unset( $array2[$a] );
		}
	}
	
	/* loop through the database items and put them into a 2d array */
	while( $fetch3 = mysql_fetch_assoc( $result3 ) ) {
		extract( $fetch3, EXTR_OVERWRITE );
		
		$array3[] = array(
			$fetch3['dbid'],
			$fetch3['dbTitle']
		);
		
	}
	
?>
	
	<div class="body">
		
		<?
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
				
		if( !empty( $check->query ) ) {
			$check->message( $type, "update" );
			$check->display();
		}
	
		?>
		
		<span class="fontTitle">Site Options</span><br /><br />
		
		SMS gives users more control of what they see when they&rsquo;re logged in now. From this page,
		you can set the skin you use when you&rsquo;re logged in, the rank set you see as well as control
		panel options and personalized menu items.<br />
	
		<?
	
		$getUserInfo = "SELECT cpShowPosts, cpShowLogs, cpShowNews, cpShowPostsNum, ";
		$getUserInfo.= "cpShowLogsNum, cpShowNewsNum, menu1, menu2, menu3, menu4, menu5, ";
		$getUserInfo.= "menu6, menu7, menu8, menu9, menu10 FROM sms_crew WHERE crewid = '$sessionCrewid' LIMIT 1";
		$getUserInfoResult = mysql_query( $getUserInfo );
	
		while( $userPrefs = mysql_fetch_assoc( $getUserInfoResult ) ) {
			extract( $userPrefs, EXTR_OVERWRITE );
		}
	
		?>
		
		<div id="container-1">
			<ul>
				<li><a href="#one"><span>Control Panel</span></a></li>
				<li><a href="#two"><span>Personalized Menu</span></a></li>
				<li><a href="#three"><span>Rank Set</span></a></li>
				<li><a href="#four"><span>Site Skin</span></a></li>
			</ul>
	
			<div id="one" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=site&sec=1">
				<table>
					<tr>
						<td colspan="3" class="fontMedium"><b>Show the following in the Control Panel?</b></td>
					</tr>
					<tr>
						<td colspan="3" height="1"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest Mission Entries</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="showPostsY" name="cpShowPosts" value="y" <? if( $cpShowPosts == "y" ) { echo "checked"; } ?>/> <label for="showPostsY">Yes</label>
							<input type="radio" id="showPostsN" name="cpShowPosts" value="n" <? if( $cpShowPosts == "n" ) { echo "checked"; } ?>/> <label for="showPostsN">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest Personal Logs</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="showLogsY" name="cpShowLogs" value="y" <? if( $cpShowLogs == "y" ) { echo "checked"; } ?>/> <label for="showLogsY">Yes</label>
							<input type="radio" id="showLogsN" name="cpShowLogs" value="n" <? if( $cpShowLogs == "n" ) { echo "checked"; } ?>/> <label for="showLogsN">No</label>
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest News Items</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" id="showNewsY" name="cpShowNews" value="y" <? if( $cpShowNews == "y" ) { echo "checked"; } ?>/> <label for="showNewsY">Yes</label>
							<input type="radio" id="showNewsN" name="cpShowNews" value="n" <? if( $cpShowNews == "n" ) { echo "checked"; } ?>/> <label for="showNewsN">No</label>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="5"></td>
					</tr>
					<tr>
						<td colspan="3" class="fontMedium">
							<b>How many of the following should be shown in the Control Panel?</b>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="1"></td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest Mission Entries</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="order" name="cpShowPostsNum" value="<?=$cpShowPostsNum;?>" maxlength="3" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest Personal Logs</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="order" name="cpShowLogsNum" value="<?=$cpShowLogsNum;?>" maxlength="3" />
						</td>
					</tr>
					<tr>
						<td class="tableCellLabel">Latest News Items</td>
						<td>&nbsp;</td>
						<td>
							<input type="text" class="order" name="cpShowNewsNum" value="<?=$cpShowNewsNum;?>" maxlength="3" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="options" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="two" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=site&sec=2">
				<table>
					<tr>
						<td colspan="3">Personalized menus allow you to pick up to 10 links that you visit often and access them whenever you&rsquo;re logged in from the main navigation. You are limited to only those menu items which you have access to normally. In addition, you can select any <em>active</em> database item to include your list as well. Once you have updated this list, click Update. The changes will take affect when you visit another page.</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<?php

					for( $i=1; $i<11; $i++ )
					{
						$menu = "menu" . $i;

						echo "<tr>";
							echo "<td class='tableCellLabel'>Menu Item #" . $i . "</td>";
							echo "<td>&nbsp;</td>";
							echo "<td>";
								echo "<select name='" . $menu . "'>";
									echo "<option value='0'>No Selection</option>";

									echo "<optgroup label='General'>";
										foreach( $array1 as $key1 => $value1 )
										{
											if( $$menu == $value1[0] ) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='" . $value1[0] . "'" . $selected . ">";
												echo ucwords( $value1[2] ) . " - " . $value1[1];
											echo "</option>";
										}
									echo "</optgroup>";
									
									echo "<optgroup label='Database Items'>";
										foreach( $array3 as $key3 => $value3 )
										{
											if( $$menu == 'd_' . $value3[0] ) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='d_" . $value3[0] . "'" . $selected . ">";
												echo $value3[1];
											echo "</option>";
										}
									echo "</optgroup>";

									echo "<optgroup label='Admin'>";
										foreach( $array2 as $key2 => $value2 )
										{
											if( $$menu == $value2[0] ) {
												$selected = " selected";
											} else {
												$selected = "";
											}

											echo "<option value='" . $value2[0] . "'" . $selected . ">";
												echo ucwords( $value2[3] ) . " - " . $value2[1];
											echo "</option>";
										}
									echo "</optgroup>";
								echo "</select>";
							echo "</td>";
						echo "</tr>";
					}

					?>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="menu_update" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="three" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=site&sec=3">
				<table>
					<tr>
						<td colspan="2"></td>
						<td>
							<?php
		
							/* break the string into an array */
							$rankArray = explode( ",", $allowedRanks );
		
							/* loop through the array */
							foreach( $rankArray as $key => $value ) {
		
							?>
							
								<input type="radio" id="rank_<?=$value;?>" name="rankSet" value="<?=$value;?>"<? if( $sessionDisplayRank == trim( $value ) ) { echo " checked"; } ?> />
								<label for="rank_<?=$value;?>"><img src="<?=$webLocation;?>images/ranks/<?=trim( $value );?>/preview.png" alt="" border="0" /></label><br />
		
							<?php } ?>
							
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="rank_update" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			
			<div id="four" class="ui-tabs-container ui-tabs-hide">
				<form method="post" action="<?=$webLocation;?>admin.php?page=user&sub=site&sec=4">
				<table>
					<tr>
						<td colspan="2"></td>
						<td>
							<?php
		
							/* break the string into an array */
							$skinArray = explode( ",", $allowedSkins );
		
							/* loop through the array */
							foreach( $skinArray as $key1 => $value1 ) {
		
							?>
							
								<input type="radio" id="skin_<?=$value1;?>" name="changeSkin" value="<?=$value1;?>"<? if( $sessionDisplaySkin == trim($value1) ) { echo " checked='yes'"; } ?> />
								<label for="skin_<?=$value1;?>"><img src="<?=$webLocation;?>skins/<?=trim( $value1 );?>/preview.jpg" alt="" border="0" style="border:1px solid #efefef;" /></label><br /><br />
		
							<?php } ?>
							
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="image" src="<?=path_userskin;?>buttons/update.png" name="skin_update" value="Update" class="button" />
						</td>
					</tr>
				</table>
				</form>
				
				
				
				<? /* ?>
				<h3>Current Skin</h3>
				<h4><?=ucwords( $displaySkin );?></h4>
				<p>
					<img src="<?=$webLocation;?>skins/<?=$sessionDisplaySkin;?>/preview.jpg" alt="<?=$sessionDisplaySkin;?>" style="border: 2px solid #efefef;" />
				</p>

				<h3>Other Available Skins</h3>
				<?

					foreach( $allowedSkinsArray as $key => $value ) {

						if( $value != $sessionDisplaySkin ) {
							echo "<h4>" . ucwords( $value ) . "</h4>";
							echo "<a href='" . $webLocation . "admin.php?page=user&sub=site&action=skin&changeskin=" . trim( $value ) . "'>";
							echo "<img src='" . $webLocation . "skins/" . trim( $value ) . "/preview.jpg' alt='" . $value . "' style='border: 2px solid #efefef;' class='image' />";
							echo "</a>";
						} else {
						}
					}
				*/
				?>
			
			</div>
		</div>
	
	</div>
	
<? } else { errorMessage( "site options" ); } ?>