<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: install.php
Purpose: Main page to direct users to one of three installation options

System Version: 2.6.8
Last Modified: 2009-01-11 0220 EST
**/

if(isset($_GET['type']) && $_GET['type'] == "update") {

	/* pull in the DB connection variables */
	require_once( 'framework/variables.php' );
	
	/* database connection */
	$db = @mysql_connect( "$dbServer", "$dbUser", "$dbPassword" ) or die ( "<b>$dbErrorMessage</b>" );
	mysql_select_db( "$dbTable",$db ) or die ( "<b>Unable to select the appropriate database.  Please try again later.</b>" );
	
	/* query the db for the system information */
	$getVer = "SELECT sysVersion FROM sms_system WHERE sysid = 1";
	$getVerResult = mysql_query( $getVer );
	$updateVersion = mysql_fetch_array( $getVerResult );
	
	/* format the version string properly */
	$updateVersion = str_replace( ".", "", $updateVersion[0] );
	
	/* do some checking for the 2.4.4.1 release */
	if( $updateVersion == "2441" ) {
		$updateVersion = "244";
	}
	
}

?>

<html>
<head>
	<title>SMS 2.6 Install</title>
	<link rel="stylesheet" type="text/css" href="install/install.css" />
	<script type="text/javascript" src="framework/js/jquery.js"></script>
</head>
<body>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#readme').click( function() {
				$('.readme').toggle();
				return false;
			});
		});
	</script>
	<div id="install">
		<div class="header">
			<h1>SMS 2.6 Installation Center</h1>
		</div> <!-- close .header -->
		
		<div class="full">
			
			<p>Welcome to the Simm Management System installation center and thank you for choosing the SIMM Management System by Anodyne Productions. We have worked very hard to build the best possible product for you to manage your simm online. If you have questions, please refer to the <a href="http://docs.anodyne-productions.com/" target="_blank">documentation</a> on the Anodyne site or our <a href="http://forums.anodyne-productions.com/" target="_blank">support forums</a> for more help.</p>
			
			<p>From this page you will be able to proceed with the installation or upgrade of SMS to the latest version (2.6) by one of several means:
				<ul>
					<li>If you don't have SMS 2 installed on your server, OR, you want to ignore any previous versions of SMS and start fresh, select the fresh install option.</li>
					<li>If you have a previous version of SMS 2 on your server and would like to update to 2.6, select the update option.</li>
				</ul>
			</p>
			
			<p class="bold red">Before you begin, please make sure you have read the readme file included with SMS in its entirety. The readme contains important information about setting up SMS on your server. You can view the readme file by <a href="#" id="readme">clicking here</a>.</p>
			
			<div class="readme" style="display:none;">
				<?php include_once('install/readme.html');?>
			</div>
			
			<h2>Fresh Install</h2>
			<p>The fresh install option will guide you through the clean install process for SMS 2.6. Once the installation is complete, you will be able to use SMS for your simm. In order to proceed, you will need the following information:
				<ul>
					<li>Your database connection parameters (database location, database name, database username, and database password) which you likely received from your host when you opened your account</li>
					<li>Character information for the character you will be playing</li>
					<li>Ship information for your sim</li>
				</ul>
			</p>
			<h3><a href="install/install.php">Go to fresh install &raquo;</a></h3>
			
			<h2>Upgrade</h2>
			<p>The upgrade option will guide you through upgrading your current version of SMS to the latest version. Once the installation is complete, you will be able to use SMS 2.6 for your simm. In order to proceed, you <b>must</b> be running SMS 2.3.0 or higher!</p>
			<h3><a href="update.php?version=<?php echo $updateVersion;?>">Go to update &raquo;</a></h3>
			
		</div>
		
		<div class="footer">
			Copyright &copy; 2005-<?php echo date('Y'); ?> by <a href="http://www.anodyne-productions.com/" target="_blank">Anodyne Productions</a>
		</div> <!-- close .footer -->
		
	</div> <!-- close #install -->
</body>
</html>