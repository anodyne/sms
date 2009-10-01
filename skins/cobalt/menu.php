<?php

/**
Edits to this skin are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/cobalt/menu.php
Purpose: Page that creates the navigation menu for SMS 2

Skin Version: 2.0
Last Modified: 2008-10-05 0948 EST
**/

$ship = array($shipPrefix, $shipName, $shipRegistry);

foreach ($ship as $key => $value)
{
	if (empty($value))
	{
		unset($ship[$key]);
	}
}

$shipString = implode(' ', $ship);

?>

<script type="text/javascript">
	$(document).ready(function(){
		$('#list').clickMenu();
		$('ul.hidemenu').show();
	});
</script>

<?php

/* create a new instance of the menu class */
$menu = new Menu;

if(isset($sessionCrewid))
{
	$menu->skin = $sessionDisplaySkin;
}

?>

<div id="header">
	<img src="<?=SKIN_PATH;?>images/header_left.png" border="0" alt="" style="float:left;" />
	<img src="<?=SKIN_PATH;?>images/header_right.png" border="0" alt="" style="float:right;" />
	<div class="head">
		<img src="<?=SKIN_PATH;?>images/sms.png" border="0" alt="SMS2" style="float:right;" />
		<?=$shipString;?>
	</div>
	<div class="mainNav">
		<?php

		$menu->main();

		if(isset($sessionCrewid))
		{
			$menu->user($sessionCrewid);
		}

		?>
	</div><br />
</div>

<div id="content" class="wrapper">
	<div class="nav">
		<div class="login">
		<? if (isset($sessionCrewid)) { ?>
			<i>Hello, <? printCrewName( $sessionCrewid, "noRank", "noLink" ); ?></i><br />
			{ <a href="<?=$webLocation;?>login.php?action=logout">Log Out</a> }
		<? } else { ?>
			<form method="post" action="<?=$webLocation;?>login.php?action=checkLogin" class="login">
				<b>Username</b><br />
				<input type="text" name="username" size="12" class="text" /><br /><br />
				
				<b>Password</b><br />
				<input type="password" name="password" size="12" class="text" /><br /><br />
				
				<input type="image" src="<?=SKIN_PATH;?>buttons/login-small.png" name="submit" class="buttonSmall" value="Login" />
			</form>
			<br />
			<a href="<?=$webLocation;?>login.php?action=reset">&laquo; Reset Password</a>
		<? } ?>
		<br /><br />
		<? include_once( 'framework/stardate.php' ); ?>
		</div> <!-- close the .login layer -->
		<br />

		<?
		
		if( $pageClass == "main" ) {
		
			/* pull in the menu */
			$menu->general( "main" );
		
			/* include the info page */
			include_once( "pages/info.php" );
		
		} elseif( $pageClass == "personnel" ) {
		
			/* pull in the menu */
			$menu->general( "personnel" );
		
		} elseif( $pageClass == "ship" ) {
		
			/* pull in the menu */
			$menu->general( $simmType );
		
		} elseif( $pageClass == "simm" ) {
			
			/* pull in the menu */
			$menu->general( "simm" );
			
		} elseif( $pageClass == "admin" ) {
			
			/* pull in the admin menus */
			$menu->admin( "post", $sessionAccess, $sessionCrewid );
			$menu->admin( "manage", $sessionAccess, $sessionCrewid );
			$menu->admin( "reports", $sessionAccess, $sessionCrewid );
			$menu->admin( "user", $sessionAccess, $sessionCrewid );
		
		}
		
		?>
	</div> <!-- close the .nav layer -->