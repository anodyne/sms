<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: login.php
Purpose: The file that controls logging in, logging out, and checking the 
	submitted values against the values stored in the database. Also sets the three 
	SESSION variables that are used through the system: crewid, access levels, and 
	displaySkin.

System Version: 2.6.3
Last Modified: 2008-10-11 1217 EST
**/

/* pull in the DB connection variables */
require_once('framework/dbconnect.php');

/* pulling a function from new library */
require_once('framework/session.name.php');

/* get system unique identifier */
$sysuid = get_system_uid();

/* rewrite master php.ini session.name */
ini_set('session.name', $sysuid);

/* start the session */
session_start();

/* fixing error reporting issues */
error_reporting(0);
ini_set('display_errors', 0);

/* get the referenced page from the URL */
if(isset($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = 'login';
}

if(isset($_GET['error'])) {
	$error = $_GET['error'];
} else {
	$error = NULL;
}

/* define the variables */
$login = NULL;
$redirectLocation = NULL;
$redirectTime = NULL;

/* pull in the global functions files */
include_once( 'framework/functionsGlobal.php' );
include_once( 'framework/functionsAdmin.php' );
include_once( 'framework/functionsUtility.php' );

if( $action == "checkLogin" ) {
	if( !isset( $_POST['username'] ) ) {
		$login = "false";
		$error = "0";
	} elseif( !isset( $_POST['password'] ) ) {
		$login = "false";
		$error = "1";
	} elseif( !isset( $_POST['username'] ) && !isset( $_POST['password'] ) ) {
		$login = "false";
		$error = "2";
	} else {

		/* pull the information from the user table */
		$userLogin = "SELECT crewid, displaySkin, displayRank, accessPost, ";
		$userLogin.= "accessManage, accessReports, accessUser, accessOthers, crewType ";
		$userLogin.= "FROM sms_crew WHERE username = '$_POST[username]' AND ";
		$userLogin.= "password = md5( '$_POST[password]' ) AND crewType = 'active' LIMIT 1";
		$userLoginResult = mysql_query( $userLogin );
		$users = mysql_num_rows( $userLoginResult );

		/* if the number of users returned is 1, continue */
		if( $users == 1 ) {
			
			/* pull the system UID information from the database */
			$systemUID = "SELECT sysuid FROM sms_system WHERE sysid = '1' LIMIT 1";
			$uidResult = mysql_query( $systemUID );
			$uid = mysql_fetch_array( $uidResult );

			/* pull the row */
			$user = mysql_fetch_row( $userLoginResult );

			/* concatenate the access variables together */
			$newAccessString = $user[3] . "," . $user[4] . "," . $user[5] . "," . $user[6] . "," . $user[7];

			/* set the session variables */
			$_SESSION['sessionCrewid'] = $user[0];
			$_SESSION['sessionDisplaySkin'] = $user[1];
			$_SESSION['sessionDisplayRank'] = $user[2];
			$_SESSION['sessionAccess'] = $newAccessString;
			$_SESSION['systemUID'] = $uid[0];

			/* update the lastLogin for the crew member */
			$timestamp = "UPDATE sms_crew SET lastLogin = UNIX_TIMESTAMP() WHERE crewid = '$user[0]'";
			$timestampQuery = mysql_query( $timestamp );
			
			/* optimize the table */
			optimizeSQLTable( "sms_crew" );
			
			$login = "true";
			
		} else {
			if($user[8] != 'active') {
				$error = 4;
			} else {
				$error = 2;
			}
			
			$login = "false";
		}
	}
} if( $action == "resetPassword" ) {
	
	foreach ($_POST as $key => $value)
	{
		$$key = escape_string($value);
	}
	
	if (!empty($username) || !empty($email))
	{
		$checkEmail = "SELECT crewid, email FROM sms_crew WHERE username = $username AND email = $email LIMIT 1";
		$checkEmailResult = mysql_query($checkEmail);
		$emailCount = mysql_num_rows($checkEmailResult);
	
		/* determine temporary password */
		if( $emailCount == 1 ) {
		
			$fetch = mysql_fetch_array($checkEmailResult);
	
			/* define the length */
			$length = 8;
		
			/* start with a blank password */
			$password = "";
		
			/* define possible characters */
			$possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
		
			/* set up a counter */
			$i = 0; 
		
			/* add random characters to $password until $length is reached */
			while( $i < $length ) { 
		
				/* pick a random character from the possible ones */
				$char = substr( $possible, mt_rand( 0, strlen( $possible )-1 ), 1 );
			
				/* we don't want this character if it's already in the password */
				if( !strstr( $password, $char ) ) { 
					$password .= $char;
					$i++;
				}
		
			}
		
			$from = printCOEmail();
			$newPassword = md5( $password );
			$to = $fetch[1];
			$from = printCO() .' <'. printCOEmail() .'>';
			$subject = "[" . $shipPrefix . " " . $shipName . "] Password Reset";
			$message = "Your new password is listed below. You can log in with this new password and your existing username. It is recommended that you change your password once you log in.

	Password: $password

	This is an automatically generated email, please do not reply.";

			mail($to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion());
				
			$updatePassword = "UPDATE sms_crew SET password = '$newPassword' WHERE crewid = '$fetch[0]' LIMIT 1";
			$passwordResult = mysql_query( $updatePassword );
		}
	}
}
	
?>

<!DOCTYPE html PUBLIC "-/*W3C/*DTD XHTML 1.0 Transitional/*EN"
"http:/*www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" href="<?=$webLocation;?>skins/<?=$skin;?>/style.css" type="text/css" media="screen" />
	<?
	
	/* set the redirect times and locations */
	if( $login == "true" ) {
		$redirectTime = "5";
		$redirectLocation = $webLocation . "admin.php?page=main";
	} elseif( $action == "checkLogin" && $login == "false" ) {
		$redirectTime = "0";
		$redirectLocation = $webLocation . "login.php?action=login&login=false&error=" . $error;
	} elseif( $action == "logout" ) {
		$redirectTime = "5";
		$redirectLocation = $webLocation . "index.php?page=main";
	} elseif( $action == "resetPassword" ) {
		switch ($error)
		{
			case 1:
				$redirectTime = "0";
				$redirectLocation = $webLocation . "login.php?action=login&login=false&error=5";
				
				break;
			
			default:
				$redirectTime = "120";
				$redirectLocation = $webLocation . "login.php?action=login";
				
				break;
		}
	}
	
	?>
	<meta http-equiv="refresh" content="<?=$redirectTime;?>;URL=<?=$redirectLocation;?>" />
	
	<? if( $action == "checkLogin" || $action == "resetPassword" || $action == "logout" ) { } else { ?>
	<script type="text/javascript">
		function focus() {
			document.getElementById('username').focus();
		}
		window.onload = focus;
	</script>
	<? } ?>
	
</head>
<body>
	<div id="login">
		<div class="header">
			<img src="skins/<?=$skin;?>/images/login.jpg" border="0" alt="SMS" /><br />
			<? printText( $shipPrefix . " " . $shipName . " " . $shipRegistry ); ?>
		</div>
	
	<? if( $action == "checkLogin" && $login != "true" ) { ?>
	
		<div class="content">
			<b>Verifying Login Information...</b>
		</div>
		
	<? } elseif( $login == "true" ) { ?>
		
		<div class="content">
			<b>Logging In...</b><br /><br />
			You will be redirected to the <a href="<?=$webLocation;?>admin.php?page=main">
			Control Panel</a> in 5 seconds.
		</div>
		
	<? } elseif( $action == "login" ) { ?>
	
	<form method="post" action="<?=$webLocation;?>login.php?action=checkLogin">
		<div class="content">
		
			<? if(isset($error)) { ?>
			<div class="error" style="margin:0 auto; width:50%;">
				<?
				
				switch( $error ) {
					case 0:
						echo "Your username does not match our records.  Please try again.";
						break;
					case 1:
						echo "Your password does not match our records.  Please try again.  If further attempts fail, please try resetting your password.";
						break;
					case 2:
						echo "Your username and password combination do not match our records.  Please try again.";
						break;
					case 3:
						echo "Either you are not an authorized member of this sim or your session has timed out.  Please try logging in. If you still receive this error and believe you have received it in error, please contact the sim's CO.";
						break;
					case 4:
						echo "You are not an active member of this sim. In order to log in, your account must be active! If you believe you have received this message in error, please contact the sim's CO.";
						break;
					case 5:
						echo "Password reset failed! You must supply both a username and email address to reset your account. Please try again.";
						break;
				}
				
				?>
				<br /><br />
			</div>
			<? } ?>
			
			<b>Username</b><br />
			<input type="text" size="16" maxlength="16" name="username" id="username" class="textboxLarge" /><br /><br />
			<b>Password</b><br />
			<input type="password" size="16" maxlength="16" name="password" class="textboxLarge" /><br /><br /><br />
			<input type="image" src="<?=$webLocation;?>skins/<?=$skin;?>/buttons/login.png" class="submitButton" /><br /><br /><br />

			<div class="footer">
				<a href="<?=$webLocation;?>">&laquo; Back to site</a>
				&nbsp; &nbsp; &nbsp; &nbsp;
				<a href="<?=$webLocation;?>login.php?action=reset">Lost your password?</a>
			</div>
		</div>
	</form>
	
	<? } elseif( $action == "reset" ) { ?>
	
	<form method="post" action="<?=$webLocation;?>login.php?action=resetPassword">
		<div class="content">
			Please provide your username and email address associated with your account. Your new
			password will be emailed to that account.<br /><br />
			
			<b>Username</b><br />
			<input type="text" size="16" maxlength="16" name="username" id="username" class="textboxLarge" /><br /><br />
			<b>Email Address</b><br />
			<input type="text" size="16" maxlength="50" name="email" class="textboxLarge" /><br /><br /><br />
			<input type="image" src="<?=$webLocation;?>skins/<?=$skin;?>/buttons/reset.png" class="submitButton" /><br /><br /><br />

			<div class="footer">
				<a href="<?=$webLocation;?>login.php?action=login">&laquo; Back to login</a>
			</div>
		</div>
	</form>

<? } elseif( $action == "resetPassword" ) {

	/* determine if password was really reset */
	if (empty($passwordResult)) { ?>

	<form method="post" action="<?=$webLocation;?>login.php?action=resetPassword">
		<div class="content">
			<b>Password Not Reset</b><br /><br />
			<div class="error" style="width:50%; margin:0 auto;">
				The information that you have supplied is not the same as what is listed in the database.
				Please re-enter your information.  If you continue to get this error please email the CO 
				and inform them of this error.
			</div>
			<br /><br />
	
			<b>Username</b><br />
			<input type="text" size="16" maxlength="16" name="username" id="username" class="textboxLarge" /><br /><br />
			<b>Email Address</b><br />
			<input type="text" size="16" maxlength="50" name="email" class="textboxLarge" /><br /><br /><br />
			<input type="image" src="<?=$webLocation;?>skins/<?=$skin;?>/buttons/reset.png" class="submitButton" /><br /><br /><br />
			
			<div class="footer">
				<a href="<?=$webLocation;?>login.php?action=login">&laquo; Back to login</a>
			</div>
		</div>
	</form>
	
<?  } else 

	/* if password was reset inform the user */

{  ?>
		<div class="content">
			<b class="fontTitle">Password Reset</b><br /><br />
			
			Your new password has been emailed to you. Once you have logged in, please change your 
			password to something easier to remember.<br /><br /><br />
			
			<div class="footer">
				<a href="<?=$webLocation;?>login.php?action=login">&laquo; Back to login</a>
			</div>
		</div>

<? } } elseif( $action == "logout" ) {
	
		session_unset();
		session_destroy();
		ini_restore( "session.cookie_lifetime" );
	
	?>

		<div class="content">
			<b>Logging Out...</b><br /><br />
			You may <a href="<?=$webLocation;?>login.php?action=login">login</a> again, otherwise,
			you will be redirected to the <a href="<?=$webLocation;?>">main page</a> in 5 seconds.
		</div>


	<? } ?>

	</div> <!-- close the login id -->
</body>
</html>