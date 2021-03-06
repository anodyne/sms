<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause the system to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: update.php
Purpose: New update system that will dynamically pull the right update file based
	on what version of the system is in use

System Version: 2.6.10
Last Modified: 2010-01-09 2159 EST
**/

/* define the step var */
if (isset($_GET['step']) && is_numeric($_GET['step']))
{
	$step = $_GET['step'];
}
else
{
	$step = 1;
}

if (isset($_GET['version']) && is_numeric($_GET['version']))
{
	$urlVersion = $_GET['version'];
}
else
{
	$urlVersion = FALSE;
}

/* array for controlling trailing zeroes */
$versionArray = array(
	0 => 20,
	1 => 21,
	2 => 22,
	3 => 23,
	4 => 24,
	5 => 25,
	6 => 26
);

/* array with all the possible versions */
$versionsArray = array(
	230,
	231,
	240,
	241,
	242,
	243,
	244,
	250,
	251,
	2511,
	252,
	253,
	254,
	255,
	256,
	260,
	261,
	262,
	263,
	264,
	265,
	266,
	267,
	268,
	269
);

/* count the number of items in the versions array */
$versionsCount = count($versionsArray);

/* make sure the version is formatted right */
if (in_array($urlVersion, $versionArray))
{
	$urlVersion = $urlVersion . 0;
}

/* destroy the session if it exists */
session_unset();

/* pull in the globals files */
require_once('framework/functionsGlobal.php');
include_once('framework/functionsUtility.php');

switch($step)
{
	case 2:
	
		/* if there's not version specified in the URL, try to find out what version it is */
		if( $urlVersion === FALSE ) {
			
			$getUpdateVersion = "SELECT sysVersion FROM sms_system WHERE sysid = 1 LIMIT 1";
			$getUpdateVersionResult = mysql_query( $getUpdateVersion );
			$updateVersion = mysql_fetch_array( $getUpdateVersionResult );
			
			/* make sure the periods have been removed */
			$urlVersion = str_replace(".", "", $updateVersion[0]);
	
		}
		
		if ($urlVersion == 2441)
		{
			$urlVersion = 244;
		}
		
		/** PULL IN THE UPDATE FILE **/
		foreach ($versionsArray as $key1 => $value1)
		{
			/* if we're at the right point in the array, start the update code */
			if ($urlVersion == $value1)
			{
				/* duplicate the versions array */
				$versionsArrayNew = $versionsArray;
				
				/* slice the array so it only includes the files that need to be used */
				$versionsArrayNew = array_slice($versionsArray, $key1);
				
				/* loop through the new array and pull in the update files */
				foreach ($versionsArrayNew as $key2 => $value2)
				{
					/* pull in the update files sequentially */
					include_once( "update/" . $value2 . ".php" );
				}
			}
		}
	
		/** UPDATE THE VERSION IN THE DATABASE **/
		$updateVersion = "UPDATE sms_system SET sysVersion = '2.6.10', sysBaseVersion = '2.6', ";
		$updateVersion.= "sysIncrementVersion = '.10', sysLaunchStatus = 'n' WHERE sysid = 1 LIMIT 1";
		$updateVersionResult = mysql_query( $updateVersion );
		
		break;
	case 3:

		/** PULL IN THE UPDATE FILE **/
		require_once("update/starbase.php");
		
		break;
}

?>

<html>
<head>
	<title>SMS 2.6 :: Update</title>
	<link rel="stylesheet" href="update/update.css" type="text/css" />
</head>
<body>
	<div id="install">	
		<div class="header">
			<img src="update/update.jpg" alt="" border="0" />
		</div> <!-- close .header -->
		<div class="content">
			
			<? if( $step == "1" ) { ?>
			
			SMS 2.6.10 is an update to latest release of SMS which updates the system with the following changes:
			
			<ul>
				<li>Fixed bug on news page where selecting a category would narrow down news but the category listed next to each news item wouldn't be accurate</li>
				<li>Fixed bug in menu class where setting a general menu item to require login wouldn't allow anyone to see the link, logged in or not</li>
				<li>Fixed bug where mission posts wouldn't be deleted when the delete action was triggered from the manage posts page</li>
				<li>Fixed bug where post tags couldn't be updated from the Edit Mission Post page</li>
				<li>Updated the database to set postAuthors as a text field</li>
				<li>Added ability to change the number of authors on a post</li>
				<li>Added loading graphic to the inbox to avoid blank pages while loading large inboxes</li>
				<li>Changed install process so that the variables file doesn't have a closing PHP tag to prevent the output already sent errors</li>
				<li>Updated the post count page to combine user records together by email address for compiling total count report</li>
				<li>Added a hidden field to the contact page to help prevent spam bots from sending spam messages through the contact form</li>
				<li>Cleaned up some stray PHP short open tags in the install script</li>
				<li>Added a hidden field to the join page to help prevent spam bots from sending spam messages through the join form</li>
				<li>Added a hidden field to the docking request page to help prevent spam bots from sending spam messages through the docking request form</li>
				<li>Updated the version check class to handle 2 digits in the third section of the version number</li>
			</ul>
			
			<h1><a href="update.php?step=2&version=<?=$urlVersion;?>">Next Step &raquo;</a></h1>
			
			<? } elseif( $step == "2" ) { ?>
			
			The changes have been made to your system.  If you still experience problems with any of the issues that have been fixed, please report them on the Anodyne Support Forum.

			<h1>
				<a href="<?=$webLocation;?>index.php?page=main">Return to your site &raquo;</a>
			</h1>
			
			<? } ?>
		</div>
		<div class="footer">
			Copyright &copy; <?php echo date('Y'); ?> by <a href="http://www.anodyne-productions.com/" target="_blank">Anodyne Productions</a>
		</div> <!-- close .footer -->
	</div> <!-- close #install -->
</body>
</html>