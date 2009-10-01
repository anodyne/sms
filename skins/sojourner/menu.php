<?php
/**
Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/sojourner/menu.php
Purpose: Page that creates the navigation menu for SMS 2

Skin Version: 1.0
Last Modified: 2009-06-01 0832 EST
**/

include_once(SKIN_PATH . 'assets/functions.php');
include_once(SKIN_PATH . 'assets/classMenuOverride.php');

/* create a new instance of the menu class */
$menu = new MenuOverride;

if(isset($sessionCrewid))
{
	$menu->skin = $sessionDisplaySkin;
}

$name_raw = explode('/', $_SERVER['SCRIPT_NAME']);
$name = end($name_raw);
$page = (isset($_GET['page'])) ? $_GET['page'] : 'main';

?>

<script type="text/javascript">
	$(document).ready(function(){
		var name = '<?php echo $name;?>';
		
		$('ul.sf-menu').superfish();
		
		$('#cycle').cycle({ 
		    fx:     'fade', 
		    speed:  'slow', 
		    timeout: 0, 
		    next:   '#next', 
		    prev:   '#prev' 
		});
		
		$('a#userpanel').toggle(function(){
			$('div.panel-body').slideDown('normal');
			return false;
		}, function(){
			$('div.panel-body').slideUp('normal');
			return false;
		});
		
		if (name == 'admin.php')
		{
			$('#container .content .body').css('marginRight', '20%');
		}
	});
</script>

<?php

$unread = unreadMessages($sessionCrewid);

$unreadIcon = ($unread == 0) ? 'icon-gray' : 'icon-green';
$unreadImage = '<img src="skins/sojourner/images/'. $unreadIcon .'.png" border="0" alt="" /> &nbsp;';

?>

<div id="panel">
	<div class="panel-body">
		<div class="wrapper">
			<?php if (!isset($sessionCrewid)): ?>
				<form method="post" action="<?php echo $webLocation;?>login.php?action=checkLogin">
					<table class="panel-login">
						<tbody>
							<tr>
								<td align="right"><strong>Username</strong></td>
								<td>&nbsp;</td>
								<td><input type="text" name="username" class="text" /></td>
								<td rowspan="2">&nbsp;</td>
								<td rowspan="2" valign="middle">
									<input type="image" src="<?=SKIN_PATH;?>buttons/login-small.png" name="submit" class="buttonSmall" value="Login" />
								</td>
							</tr>
							<tr>
								<td align="right"><strong>Password</strong></td>
								<td>&nbsp;</td>
								<td><input type="password" name="password" class="text" /></td>
							</tr>
							<tr>
								<td colspan="2"></td>
								<td><br /><a href="<?=$webLocation;?>login.php?action=reset">&laquo; Reset Password</a></td>
								<td colspan="2"></td>
							</tr>
						</tbody>
					</table>
				</form>
			<?php else: ?>
				<table class="table100">
					<tbody>
						<tr>
							<td class="panel_1 align-top"><h4><? printCrewName( $sessionCrewid, "rank", "noLink" ); ?></h4>
								<ul class="none">
									<li><a href="<?php echo $webLocation;?>admin.php?page=user&sub=account&crew=<?php echo $sessionCrewid;?>">Edit Account</a></li>
									<li><a href="http://localhost/nova/trunk/index.php/user/preferences">Edit Preferences</a></li>
									<li><a href="http://localhost/nova/trunk/index.php/user/status">Request LOA</a></li>
									<li><a href="http://localhost/nova/trunk/index.php/messages/index">Private Messages</a></li>
									
									<li><a href="<?php echo $webLocation;?>login.php?action=logout"><span>Logout</span></a></li>
								</ul>
							</td>
							<td class="panel_spacer"></td>
							<td class="panel_2 align-top">
								<h4>Characters</h4>
								<ul class="none">
									<li>
										<a href="http://localhost/nova/trunk/index.php/personnel/character/7">Major Alex Diaz</a> 
										&nbsp;&nbsp;
										<a href="http://localhost/nova/trunk/index.php/#" class="edit">[ Edit ]</a>
									</li>
								</ul>
							</td>
							<td class="panel_spacer"></td>
							<td class="panel_3 align-top">
								<h4>My Links</h4>
								<?php $menu->user($sessionCrewid);?>
							</td>
						</tr>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
	<div class="panel-handle">
		<div class="wrapper">
			<?php if (isset($sessionCrewid)): ?>
				<a href="#" id="userpanel" class="panel-trigger"><span>Dashboard</span></a>

				<ul id="panel-handle-left">
					<li><a href="<?php echo $webLocation;?>admin.php?page=user&sub=inbox"><span><?php echo $unreadImage;?>Inbox (<?php echo $unread;?>)</span></a></li>
					<li><a href="<?php echo $webLocation;?>login.php?action=logout"><span>Logout</span></a></li>
				</ul>
			<?php else: ?>
				<a href="#" id="userpanel" class="panel-trigger"><span>Login</span></a>
			<?php endif;?>
		</div>
	</div>
</div>

<div id="header">
	<div class="mainNav">
		<div class="wrapper">
			<div class="float-right"><img src="<?php echo SKIN_PATH;?>images/sojourner.png" alt="" /></div>
			<?php $menu->main();?>
		</div>
	</div>
</div>

<div id="subhead">
	<?php if ($name == 'index.php'): ?>
		<div class="cycle-content">
			<div class="cycle-nav">
				<a href="#" id="prev" class="nav-link prev-link">Prev</a>
				<a href="#" id="next" class="nav-link next-link">Next</a>
			</div>
			<div id="cycle" class="cycle-inner">
				<div class="cycle-container cycle-1">
					<div class="cycle-1-content"><?php echo missionInfo();?></div>
				</div>
				<div class="cycle-container cycle-2">
					<div class="cycle-1-content">
						<h2>Backstory</h2>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
						<h3><a href="#">Read the backstory &raquo;</a></h3>
					</div>
				</div>
				<div class="cycle-container cycle-3">
					<div class="cycle-1-content">
						<h2>Join Today</h2>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
						<h3><a href="#">Join now &raquo;</a></h3>
					</div>
				</div>
				<div class="cycle-container">Mission Archives</div>
			</div>
		</div>
	<?php else: ?>
	
	<?php endif;?>
</div>

<div id="container" class="wrapper">
	<div class="content">
		
		<?php if (CUR_PAGE == 'admin.php'): ?>
			<div class="nav">
				<div class="login">
					<?php include_once('framework/stardate.php');?>
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
		<?php endif;?>