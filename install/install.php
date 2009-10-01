<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: install/install.php
Purpose: Installation script for SMS

System Version: 2.6.8
Last Modified: 2009-01-11 0224 EST
**/

session_start();

/* define variables */
$varError = NULL;

/* define the step var */
if(isset($_GET['step']) && is_numeric($_GET['step']))
{
	$step = $_GET['step'];
}
else
{
	$step = 1;
}

/* pull in the db connections */
include_once('../framework/variables.php');

/* error checking in case someone hasn't taken care of the variables.php stuff */
if($step > 3 && !isset($dbName))
{
	$step = 3;
	$varError = 1;
}

switch($step)
{	
	/*
		step 3 attempts to write the variables file that the admin provided
		all the information for in the previous step
	*/
	case 3:
		/** ERROR CHECKING FOR USER INPUT FROM STEP 1 **/
		
		if(substr($_POST['webLocation'], -1) == "/")
		{
			$webLocation1 = $_POST['webLocation'];
		}
		else
		{
			$webLocation1 = $_POST['webLocation'] . "/";
		}
		
		if(substr($webLocation1, 0, 7) == "http://" || substr($webLocation1, 0, 8) == "https://")
		{
			$webLocation = $webLocation1;
		}
		else
		{
			$webLocation = "http://" . $webLocation1;
		}
		
		/* make sure the database server doesn't start with http:// */
		if(substr($_POST['dbServer'], 0, 7) == "http://")
		{
			$dbServer = str_replace("http://", "", $_POST['dbServer']);
		}
		else
		{
			$dbServer = $_POST['dbServer'];
		}
		
		/** END ERROR CHECKING **/
	
		$dbErrorMessage = stripslashes( $_POST['errorMessage'] );
		
		$filename = '../framework/variables.php';
		$somecontent = "<?php

\$webLocation = \"$webLocation\";
	
\$dbServer = \"$dbServer\";
\$dbName = \"$_POST[dbName]\";
\$dbUser = \"$_POST[dbUser]\";
\$dbPassword = \"$_POST[dbPassword]\";
\$dbErrorMessage = \"$dbErrorMessage\";

?>";

		if(!isset($varError))
		{
			$_SESSION['webLocation'] = $webLocation;
			$_SESSION['dbServer'] = $dbServer;
			$_SESSION['dbName'] = $_POST['dbName'];
			$_SESSION['dbUser'] = $_POST['dbUser'];
			$_SESSION['dbPassword'] = $_POST['dbPassword'];
			$_SESSION['dbErrorMessage'] = $dbErrorMessage;
		}
		
		if(is_writable($filename))
		{
			chmod( $filename, 0777 );
			
			if(chmod($filename, 0777) === FALSE)
			{
				$write = "failed";
			}
			else
			{
				if(!$handle = fopen($filename, 'w'))
				{
					$write = "failed";
				}
				
				if(fwrite($handle, $somecontent) === FALSE)
				{
					$write = "failed";
				}
				else
				{
					fclose($handle);
					$write = "success";
				}
				
				chmod($filename, 0644);
			}
		}
		else
		{
			$write = "failed";
		}
		break;
	
	/* step 4 handles creating the database structure */
	case 4:
		require_once( '../framework/dbconnect.php' );
		require_once( "resource_structure.php" );
		break;
	
	/*
		step 5 handles inserting the necessary data into the database that lines up
		with the tables created in step 3
	*/
	case 5:
		require_once( '../framework/dbconnect.php' );
		require_once( "resource_data.php" );
		
		mysql_query("UPDATE sms_menu_items SET menuAvailability = 'off' WHERE menuid = 89");
		mysql_query("UPDATE sms_menu_items SET menuAvailability = 'off' WHERE menuid = 90");
		mysql_query("UPDATE sms_menu_items SET menuAvailability = 'off' WHERE menuid = 91");
		mysql_query("UPDATE sms_menu_items SET menuAvailability = 'off' WHERE menuid = 92");
		mysql_query("UPDATE sms_menu_items SET menuAvailability = 'off' WHERE menuid = 93");
		mysql_query("UPDATE sms_menu_items SET menuAvailability = 'off' WHERE menuid = 94");
		mysql_query("UPDATE sms_menu_items SET menuAvailability = 'off' WHERE menuid = 95");
		
		break;
	
	/*
		step 6 handles inserting the admin's character into the database, setting their
		access level, and adjusting the position they chose in step 4
	*/
	case 6:
		require_once( '../framework/dbconnect.php' );
		
		sleep(1);
		
		$md5password = md5( $_POST['password'] );
		
		$access = "SELECT * FROM sms_accesslevels WHERE id = 1";
		$accessR = mysql_query($access);
		$levels = mysql_fetch_array($accessR);
		
		/* create the user */
		$createUser = "INSERT INTO sms_crew ( crewid, username, password, email, firstName, middleName, lastName, gender, species, rankid, positionid, joinDate, accessPost, accessManage, accessReports, accessUser, accessOthers ) ";
		$createUser.= "VALUES ( 1, '$_POST[username]', '$md5password', '$_POST[email]', '$_POST[firstName]', '$_POST[middleName]', '$_POST[lastName]', '$_POST[gender]', '$_POST[species]', '$_POST[rank]', '$_POST[position]', UNIX_TIMESTAMP(), '$levels[1]', '$levels[2]', '$levels[3]', '$levels[4]', '$levels[5]' )";
		$createUserResult = mysql_query( $createUser );
		
		/* update the position they're being given */
		$positionFetch = "SELECT positionid, positionOpen FROM sms_positions ";
		$positionFetch.= "WHERE positionid = '$_POST[position]' LIMIT 1";
		$positionFetchResult = mysql_query( $positionFetch );
		$positionX = mysql_fetch_row( $positionFetchResult );
		$open = $positionX[1];
		$revised = ( $open - 1 );
		$updatePosition = "UPDATE sms_positions SET positionOpen = '$revised' ";
		$updatePosition.= "WHERE positionid = '$_POST[position]' LIMIT 1";
		$updatePositionResult = mysql_query( $updatePosition );
		
		break;
	
	/*
		step 7 handles updating the globals that are set during step 5, including ship name,
		ship prefix, and ship registry
	*/
	case 7:
		require_once( '../framework/dbconnect.php' );
		
		/* update the globals */
		$updateGlobals = "UPDATE sms_globals SET shipName = '$_POST[shipName]', shipPrefix = '$_POST[shipPrefix]', shipRegistry = '$_POST[shipRegistry]', emailSubject = '[" . $_POST['shipPrefix'] . " " . $_POST['shipName'] . "]' WHERE globalid = '1' LIMIT 1";
		$updateGlobalsResult = mysql_query( $updateGlobals );
		break;

} /* close the switch */

$installSteps = array(
	1	=>	array('Compatibility Tests', 'step-1.png', 'step-1-active.png'),
	2	=>	array('Basic Information', 'step-2.png', 'step-2-active.png'),
	3	=>	array('Build the Database', 'step-3.png', 'step-3-active.png'),
	4	=>	array('Populate with Data', 'step-4.png', 'step-4-active.png'),
	5	=>	array('Your Character', 'step-5.png', 'step-5-active.png'),
	6	=>	array('Simm Information', 'step-6.png', 'step-6-active.png'),
	7	=>	array('Finalize Installation', 'step-7.png', 'step-7-active.png'),
);

?>

<html>
<head>
	<title>SMS 2.6 :: Fresh Install</title>
	<link rel="stylesheet" type="text/css" href="install.css" />
</head>
<body>
	<div class="header-install">
		<h1>SMS 2.6 Fresh Install</h1>
	</div> <!-- close .header -->
	
	<div id="install">	
		
		<div class="left">
			<ul>
				<?php
				
				foreach($installSteps as $a => $b)
				{
					if($a == $step)
					{
						echo "<li class='active'>";
						echo "<img src='" . $b[2] . "' alt='' border='0' class='step-image' />";
					}
					else
					{
						echo "<li>";
						echo "<img src='" . $b[1] . "' alt='' border='0' class='step-image' />";
					}
					
					echo $b[0];
					echo "</li>";
				}
				
				?>
			</ul>
		</div>
		
		<div class="right">
			<h1>Step <?php echo $step . " &ndash; " . $installSteps[$step][0];?></h1>
			
		<?php
		
		switch($step)
		{
			case 1:
				$req = array(
					'register_globals' => array('Off', ini_get('register_globals')),
					'display_errors' => array('Off', ini_get('display_errors')),
					'short_open_tag' => array('On', ini_get('short_open_tag')),
					'file_open' => array('On', ini_get('allow_url_fopen')),
					'php' => array('4.1.0', phpversion()),
					'mysql' => array('3.0.0', mysql_get_client_info())
				);
				$disable = 0;
				
		?>
			<p>The following components are required to continue installing SMS 2.6:</p>
			<table cellpadding="5" cellspacing="0">
				<tr>
					<th class="col1">Component</th>
					<th class="col2">Required</th>
					<th class="col3">You Have</th>
				</tr>
				
				<?php
				
				$explain = NULL;
				
				if($req['php'][1] >= $req['php'][0]) {
					$color = "green";
				} else {
					$color = "red";
					$explain = TRUE;
					$disable = 1;
				}
				
				?>
				<tr>
					<td class="col1">
						PHP<br />
						<?php if(isset($explain)) { ?>
						<span class="red">You are running an unsupported version of PHP. The SIMM Management System requires PHP version 4.1.0 or higher. You are running version <?php echo $req['php'][1];?>. Please contact your host and inquire about the possibility of upgrading to a newer version or you can choose to find another host for your site.</span>
						<?php } ?>
					</td>
					<td align="center" class="col2"><?php echo $req['php'][0];?></td>
					<td align="center" class="<?php echo $color;?>" class="col3"><?php echo $req['php'][1];?></td>
				</tr>
				
				<?php
				
				$explain = NULL;
				
				if($req['mysql'][1] >= $req['mysql'][0]) {
					$color = "green";
				} else {
					$color = "red";
					$explain = TRUE;
					$disable = 1;
				}
				
				?>
				<tr>
					<td class="col1">
						MySQL<br />
						<?php if(isset($explain)) { ?>
						<span class="red">You are running an unsupported version of MySQL. The SIMM Management System requires MySQL version 3.0.0 or higher. You are running version <?php echo $req['mysql'][1];?>. Please contact your host and inquire about the possibility of upgrading to a newer version or you can choose to find another host for your site.</span>
						<?php } ?>
					</td>
					<td align="center" class="col2"><?php echo $req['mysql'][0];?></td>
					<td align="center" class="<?php echo $color;?>" class="col3"><?php echo $req['mysql'][1];?></td>
				</tr>
				
				<?php
				
				$explain = NULL;
				
				if($req['short_open_tag'][1] == 1) {
					$color = "green";
				} else {
					$color = "red";
					$explain = TRUE;
				}
				
				?>
				<tr>
					<td class="col1">
						PHP Short Open Tags<br />
						<?php if(isset($explain)) { ?>
						<span class="red">You have PHP Short Open Tags turned off. The SIMM Management System makes widespread use of PHP Short Open Tags and having this turned off may negatively impact running SMS on your server. Please contact your host to ask about changing the default value from 0 to 1. Further assistance can be obtained through the <a href="http://forums.anodyne-productions.com" target="_blank">Anodyne Support Forums</a>.</span>
						<?php } ?>
					</td>
					<td align="center" class="col2"><?php echo $req['short_open_tag'][0];?></td>
					<td align="center" class="<?php echo $color;?>" class="col3">
						<?php echo ($req['short_open_tag'][1] == 1) ? "On" : "Off";?>
					</td>
				</tr>
			</table>
			
			<p>The following are Anodyne&rsquo;s recommendations for other server settings for running SMS 2.6:</p>
			<table cellpadding="5" cellspacing="0">
				<tr>
					<th class="col1">Component</th>
					<th class="col2">Recommended</th>
					<th class="col3">You Have</th>
				</tr>
				
				<?php
				
				$explain = NULL;
				
				if($req['display_errors'][1] == 0) {
					$color = "green";
				} else {
					$color = "red";
					$explain = TRUE;
				}
				
				?>
				<tr>
					<td class="col1">
						Display Errors<br />
						<?php if(isset($explain)) { ?>
						<span class="red">Your server is set to display errors. While this will not have any ill effects on the running or performance of SMS, it could become a nuisance in the event that your server prints out errors it may be having. You can contact your host about changing this or you can continue with error display turned on.</span>
						<?php } ?>
					</td>
					<td align="center" class="col2"><?php echo $req['display_errors'][0];?></td>
					<td align="center" class="<?php echo $color;?>" class="col3">
						<?php echo ($req['display_errors'][1] == 1) ? "On" : "Off";?>
					</td>
				</tr>
				
				<?php
				
				$explain = NULL;
				
				if($req['register_globals'][1] == 0) {
					$color = "green";
				} else {
					$color = "red";
					$explain = TRUE;
				}
				
				?>
				<tr>
					<td class="col1">
						Register Globals<br />
						<?php if(isset($explain)) { ?>
						<span class="red">Your server has register globals turns on! This is potentially a security risk. Please contact your host about this. If they will not turn register globals off, talk to them about using a .htaccess file to accomplish the same thing in your SMS directory. SMS will work with register globals turned on as well as turned off.</span>
						<?php } ?>
					</td>
					<td align="center" class="col2"><?php echo $req['register_globals'][0];?></td>
					<td align="center" class="<?php echo $color;?>" class="col3">
						<?php echo ($req['register_globals'][1] == 1) ? "On" : "Off";?>
					</td>
				</tr>
				
				<?php
				
				$explain = NULL;
				
				if($req['file_open'][1] == 1) {
					$color = "green";
				} else {
					$color = "red";
					$explain = TRUE;
				}
				
				?>
				<tr>
					<td class="col1">
						File Handling<br />
						<?php if(isset($explain)) { ?>
						<span class="red">Your server does not allow for the opening, reading, and writing of files on the server by SMS. While allow file handling has its risks, SMS uses the feature during the installation of the system to help speed things up. Proceeding with file handling turned off will not negatively impact SMS, but you will have to manually copy and paste the configuration variables into the appropriate file.</span>
						<?php } ?>
					</td>
					<td align="center" class="col2"><?php echo $req['file_open'][0];?></td>
					<td align="center" class="<?php echo $color;?>" class="col3">
						<?php echo ($req['file_open'][1] == 1) ? "On" : "Off";?>
					</td>
				</tr>
			</table>
			
			<?php if($disable == 0) { ?>
			<p>&nbsp;</p>
			<form method="post" action="install.php?step=2">
				<table>
					<tr>
						<td align="right">
							<input type="submit" name="submit" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			<?php } else { ?>
			<p class="red bold">Your server does not meet the minimum requirements for running SMS. Please contact your host about upgrading PHP and/or MySQL or find another host.</p>
			<?php
				
				}
				break;
				
			case 2:
			$location = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
			$location.= "://" . $_SERVER['HTTP_HOST'];
			
			$url = $_SERVER['PHP_SELF'];
			$urlArray = explode("/", $url);
			
			/* drop the last 2 items off the array (install.php and install/) */
			array_pop($urlArray);
			array_pop($urlArray);
			
			/* put the url back together */
			$url = implode("/", $urlArray);
			
			$location.= $url . '/';
			
		?>
			
			Please provide the following information to continue with the installation.<br /><br />
			
			<form method="post" action="install.php?step=3">
				<table width="100%">
					<tr>
						<td colspan="3" class="fontLarge">Website URL</td>
					</tr>
					<tr>
						<td colspan="3">
							The web location is a required variable in order for SMS to know where
							your site is and where it should be pointing users. We have done our best
							to determine what your web location is, but please verify that the value
							in the text box below is accurate. To verify this, please look at the URL
							in your address bar and make sure it matches (up to, but not including the
							<i>install/install.php</i> portion).
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="text" name="webLocation" size="45" value="<?php echo $location; ?>" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td colspan="3" class="fontLarge">Database Server</td>
					</tr>
					<tr>
						<td colspan="3">
							The database server is where your MySQL database is located. In most cases,
							using localhost is fine. If your host has given you another means to connect,
							or you would like to connect through a socket, you can change the value in
							the text box below.
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="text" name="dbServer" size="45" value="localhost" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td colspan="3" class="fontLarge">Database Name</td>
					</tr>
					<tr>
						<td colspan="3">
							The database name is the specific database that SMS will look at for all the
							information it needs. You should have received that information from your
							host when you opened your account. If you do not know the name of your database,
							please contact your host for that information.
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="text" name="dbName" size="45" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td colspan="3" class="fontLarge">Database User</td>
					</tr>
					<tr>
						<td colspan="3">
							The database user is the username you use to access the database you specified
							above. You should have received that information from your host when you opened
							your account. If you do not know your database username, please contact your 
							host for that information.
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="text" name="dbUser" size="45" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td colspan="3" class="fontLarge">Database Password</td>
					</tr>
					<tr>
						<td colspan="3">
							The database password is the password used by your MySQL database to make sure
							that no one besides you can access it. You should have received that information
							from your host when you opened your account. If you do not know your database
							password, please contact your host for that information.
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="password" name="dbPassword" size="45" />
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					
					<tr>
						<td colspan="3" class="fontLarge">Database Error Message</td>
					</tr>
					<tr>
						<td colspan="3">
							On the off chance that your connection to the database fails or is not active
							for whatever reason, the error message will be displayed for your users to
							see. You can choose to leave the default message below or set your own error
							message.
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<textarea name="errorMessage" rows="4" style="width:329px;">A database error has occurred! Please try again later.</textarea>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>

					<tr>
						<td colspan="3" align="right">
							<input type="submit" name="submit" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
		<?php
		
			break;
			case 3:
		
		?>
			
			<?php if( isset( $varError ) ) { ?>
			
			<div class="code">
				<b class="red">The database connection file (framework/variables.php) is not correctly formatted and connecting to the database may fail. This is possibly due to the file being empty or a missing web location variable. You MUST have a web location variable and all of your connection information to continue. Please paste the following information into the file and try again.</b><br /><br />
				
				<?php print( htmlentities( '<?php' ) ); ?><br /><br />
				
				$webLocation = "<?php echo $_SESSION['webLocation'];?>";<br /><br />
				$dbServer = "<?php echo $_SESSION['dbServer'];?>";<br />
				$dbName = "<?php echo $_SESSION['dbName'];?>";<br />
				$dbUser = "<?php echo $_SESSION['dbUser'];?>";<br />
				$dbPassword = "<?php echo $_SESSION['dbPassword'];?>";<br />
				$dbErrorMessage = "<?php echo $_SESSION['dbErrorMessage'];?>";<br /><br />
				
				<?php print( htmlentities( '?>' ) ); ?>
			</div>
			<br />
			
			<?php } ?>
			
			<?php if( !isset( $varError ) && $write == "failed" ) { ?>
			
			It appears that, for security reasons, your server does not allow opening and writing files. Please open the file <b>variables.php</b> from the <b>framework</b> folder and insert the following code:<br /><br />
			
			<div class="code">
				<?php print( htmlentities( '<?php' ) ); ?><br /><br />
				
				$webLocation = "<?php echo $webLocation;?>";<br /><br />
				$dbServer = "<?php echo $dbServer;?>";<br />
				$dbName = "<?php echo $_POST['dbName'];?>";<br />
				$dbUser = "<?php echo $_POST['dbUser'];?>";<br />
				$dbPassword = "<?php echo $_POST['dbPassword'];?>";<br />
				$dbErrorMessage = "<?php echo $dbErrorMessage;?>";<br /><br />
				
				<?php print( htmlentities( '?>' ) ); ?>
			</div>
			<br />
			
			Once you have completed this, you may continue with the installation.<br /><br />
			
			<?php } else { ?>
			
			You have successfully written the file containing all of the database connection parameters!<br /><br />
			
			<?php } ?>
			
			SMS 2.6 builds on the incredibly popular SMS 2 by further refining the premier RPG manager with new features, bug fixes, and feature enhancements. Some of the major changes to SMS 2.6 include the ability to set access group default levels, users being able to edit their own posts and logs, better tabs, a new awards system that makes nominating much easier, private news items, a built in stardate script, departmental databases, and much much more! A complete feature list is available after you install the system through the Admin Control Panel > Reports > Version History.<br /><br />
			
			Please proceed to the next step to build the SMS 2.6 database and create your character which you&rsquo;ll use to administer SMS.<br /><br /><br />
			
			<form method="post" action="install.php?step=4">
				<table width="100%">
					<tr>
						<td align="right">
							<input type="submit" name="submit" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
		<?php
			
			break;
			case 4:
		
		?>
			
			You have successfully created the SMS database that will drive the site!<br /><br /><br />
			
			<form method="post" action="install.php?step=5">
				<table width="100%">
					<tr>
						<td align="right">
							<input type="submit" name="submit" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
		<?php
		
			break;
			case 5:
		
		?>
			
			Use this page to create your character. You will use the username and password to log in to your SMS site, so make sure you remember it. Once you have set up SMS, you can edit your biography.
			<br /><br />
			
			<br /><br />
			
			<form method="post" action="install.php?step=6">
				<table width="100%">
					<tr>
						<td class="label">Username</td>
						<td>&nbsp;</td>
						<td><input type="text" name="username" size="32" />
					</tr>
					<tr>
						<td class="label">Password</td>
						<td>&nbsp;</td>
						<td><input type="password" name="password" size="32" />
					</tr>
					<tr>
						<td class="label">Email Address</td>
						<td>&nbsp;</td>
						<td><input type="text" name="email" size="32" />
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<tr>
						<td class="label">First Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="firstName" /></td>
					</tr>
					<tr>
						<td class="label">Middle Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="middleName" /></td>
					</tr>
					<tr>
						<td class="label">Last Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="lastName" /></td>
					</tr>
					<tr>
						<td class="label">Gender</td>
						<td>&nbsp;</td>
						<td>
							<input type="radio" name="gender" value="Male" checked="yes" /> Male
							&nbsp;&nbsp;
							<input type="radio" name="gender" value="Female" /> Female
						</td>
					</tr>
					<tr>
						<td class="label">Species</td>
						<td>&nbsp;</td>
						<td><input type="text" name="species" /></td>
					</tr>
					<tr>
						<td colspan="3" height="15"></td>
					</tr>
					<?
					
					$ranks = "SELECT rank.rankid, rank.rankName, rank.rankImage, dept.deptColor FROM sms_ranks AS rank, ";
					$ranks.= "sms_departments AS dept WHERE dept.deptClass = rank.rankClass AND dept.deptDisplay = 'y' ";
					$ranks.= "GROUP BY rank.rankid ORDER BY rank.rankClass, rank.rankOrder ASC";
					$ranksResult = mysql_query( $ranks );
					
					$positions = "SELECT position.positionid, position.positionName, dept.deptName, ";
					$positions.= "dept.deptColor FROM sms_positions AS position, sms_departments AS dept ";
					$positions.= "WHERE position.positionOpen > '0' AND dept.deptid = position.positionDept ";
					$positions.= "AND dept.deptDisplay = 'y' ORDER BY position.positionid ASC";
					$positionsResult = mysql_query( $positions );
					
					?>
					<tr>
						<td class="label">Rank</td>
						<td>&nbsp;</td>
						<td>
							<select name="rank">
								<?
								
								while( $rank = mysql_fetch_assoc( $ranksResult ) ) {
									extract( $rank, EXTR_OVERWRITE );
							
									echo "<option value='" . $rank['rankid'] . "' style='background: url( ../images/ranks/default/" . $rank['rankImage'] . " ) no-repeat 0 100%; height:50px; color:#" . $rank['deptColor'] . ";'>" . $rank['rankName'] . "</option>";
								
								}
								
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="label">Position</td>
						<td>&nbsp;</td>
						<td>
							<select name="position">
							<?
							
							while( $position = mysql_fetch_assoc( $positionsResult ) ) {
								extract( $position, EXTR_OVERWRITE );
						
								echo "<option value='" . $position['positionid'] . "'>" . $position['deptName'] . " - " . $position['positionName'] . "</option>";
								
							}
							
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3" align="right">
							<input type="submit" name="submit" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
		<?php
		
			break;
			case 6:
			
		?>
					
			<form method="post" action="install.php?step=7">
				<table width="100%">
					<tr>
						<td class="label">Ship Prefix</td>
						<td>&nbsp;</td>
						<td><input type="text" name="shipPrefix" size="32" maxlength="10" value="USS" />
					</tr>
					<tr>
						<td class="label">Ship Name</td>
						<td>&nbsp;</td>
						<td><input type="text" name="shipName" size="32" maxlength="32" />
					</tr>
					<tr>
						<td class="label">Ship Registry</td>
						<td>&nbsp;</td>
						<td><input type="text" name="shipRegistry" size="32" maxlength="16" />
					</tr>
					<tr>
						<td colspan="3" height="25"></td>
					</tr>
					<tr>
						<td colspan="3" align="right">
							<input type="submit" name="submit" value="Next Step &raquo;" />
						</td>
					</tr>
				</table>
			</form>
			
		<?php
		
			break;
			case 7:
		
		?>
			
			<h2>Installation Complete!</h2>
			
			Congratulations, you have successfully installed SMS 2.6. If the install worked properly, you 
			should now be able to see SMS running on your site. If you need technical support, please 
			visit the <a href="http://forums.anodyne-productions.com" target="_blank">Anodyne support forums</a>.
			<br /><br />
	
			Thank you for choosing the SIMM Management System from Anodyne Productions. Please delete the 
			install file and install folder from your server. Accessing it additional times can cause errors.
			<br /><br />
	
			<h2><a href="../login.php?action=login">Login to your SMS site now &raquo;</a></h2>
			
		<?php
			
			break;
		}
		
		?>
		
		</div>
		
	</div> <!-- close #install -->
	
	<div class="footer">
		Copyright &copy; 2005-<?php echo date('Y'); ?> by <a href="http://www.anodyne-productions.com/" target="_blank">Anodyne Productions</a>
	</div> <!-- close .footer -->
</body>
</html>