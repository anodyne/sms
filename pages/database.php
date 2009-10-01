<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/database.php
Purpose: Page to display the database entries

System Version: 2.6.9
Last Modified: 2009-06-15 0617 EST
**/

/* define the page class and vars */
$pageClass = "simm";

if(isset($_GET['entry']) && is_numeric($_GET['entry']))
{
	$entry = $_GET['entry'];
}
else
{
	$entry = FALSE;
}

if(isset($_GET['dept']) && is_numeric($_GET['dept']))
{
	$d = $_GET['dept'];
}
else
{
	$d = 0;
}

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

if($d == 0)
{
	$d_name = "Global";
}
else
{
	$deptName = "SELECT deptName FROM sms_departments WHERE deptid = $d LIMIT 1";
	$deptNameR = mysql_query($deptName);
	$deptX = mysql_fetch_row($deptNameR);

	$d_name = $deptX[0];
}

/* get the departments from the database */
$getDepartments = "SELECT * FROM sms_departments WHERE deptDatabaseUse = 'y' ORDER BY deptOrder ASC";
$getDepartmentsResult = mysql_query($getDepartments);
$countDepts = mysql_num_rows($getDepartmentsResult);
$countDeptsFinal = $countDepts - 1;

/*
two arrays are being set up so that we can figure out if a user is
supposed to be allowed admin access based on whether or not their
department is setup to use the departmental database feature
*/
$d_array = array();
$d_not = array();

/* loop through the results and fill the form */
while($deptFetch = mysql_fetch_assoc($getDepartmentsResult)) {
	extract($deptFetch, EXTR_OVERWRITE);
	
	if($deptDatabaseUse == "y")
	{
		$d_array[] = array('id' => $deptid, 'dept' => $deptName);
	}
	else
	{
		$d_not[] = $deptid;
	}
}

?>

<div class="body">

<?php if($entry == FALSE) { ?>
	<div class="update notify-normal fontNormal">
		<strong class="orange">Click on the department name to view its database entries:</strong><br /><br />
		
		<?php
		
		echo "<a href='" . $webLocation . "index.php?page=database&dept=0'>Global Entries</a>";
		if($countDeptsFinal > 0)
		{
			echo "&nbsp; &middot; &nbsp;";
		}
		
		foreach( $d_array as $key => $value )
		{
			echo "<nobr><a href='" . $webLocation . "index.php?page=database&dept=" . $value['id'] . "'>" . $value['dept'] . "</a>";
		
			/*
				if it's the last element of the array, just close the HREF
				otherwise, put a middot between the array values
			*/
			if( $key >= $countDeptsFinal ) {
				echo "</nobr> ";
			} else {
				echo "&nbsp; &middot; &nbsp; </nobr> ";
			}
		}

		?>
		<br /><br />
	</div><br />

	<span class="fontTitle"><? printText($d_name);?> Database Entries</span>
	
	<?php
	
	/*
		if the person is logged in and has level 5 access, display an icon
		that will take them to edit the entry
	*/
	if(isset($sessionCrewid) && (in_array("m_database1", $sessionAccess) || in_array("m_database2", $sessionAccess)))
	{
		$access = FALSE;
		
		if(!in_array("m_database2", $sessionAccess))
		{
			$depts = "SELECT position.positionDept FROM sms_crew AS crew, sms_positions AS position, sms_departments AS dept WHERE ";
			$depts.= "crew.crewid = '$sessionCrewid' AND crew.positionid = position.positionid LIMIT 1";
			$deptsR = mysql_query($depts);
			$deptFetch = mysql_fetch_row($deptsR);
			$myDept = $deptFetch[0];
			
			if(!in_array($myDept, $d_not))
			{
				$access = TRUE;
			}
		}
		else
		{
			$access = TRUE;
		}
		
		if($access === TRUE)
		{
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href='" . $webLocation . "admin.php?page=manage&sub=database' class='image'>";
			echo "<img src='" . $webLocation . "images/edit.png' alt='Edit' border='0' />";
			echo "</a>";
		}
	}
	
	echo "<br /><br />";

	$getEntries = "SELECT * FROM sms_database WHERE dbDisplay = 'y' AND dbDept = $d ORDER BY dbOrder ASC";
	$getEntriesResult = mysql_query($getEntries);
	$countEntries = mysql_num_rows($getEntriesResult);
	
	if($countEntries == 0)
	{
		echo "<strong class='fontMedium orange'>No database entries found.</strong>";
	}
	else
	{
		/* Start pulling the array and populate the variables */
		while($entries = mysql_fetch_array($getEntriesResult)) {
			extract($entries, EXTR_OVERWRITE);
	
			echo "<strong class='fontMedium'>";

			/* build a different link based on the type of entry it is */
			if( $dbType == "entry" ) {
				echo "<a href='" . $webLocation . "index.php?page=database&entry=" . $dbid . "'>";
			} elseif( $dbType == "onsite" ) {
				echo "<a href='" . $webLocation . $dbURL . "'>";
			} elseif( $dbType == "offsite" ) {
				echo "<a href='" .$dbURL . "' target='_blank'>";
			}
		
			printText( $dbTitle );
			echo "</a>";
			echo "</strong><br />";
			printText( $dbDesc );
			echo "<br /><br />";

		}
	}

} else {

	$getEntry = "SELECT * FROM sms_database WHERE dbid = $entry LIMIT 1";
	$getEntryResult = mysql_query( $getEntry );

	/* Start pulling the array and populate the variables */
	while( $entry = mysql_fetch_array( $getEntryResult ) ) {
		extract( $entry, EXTR_OVERWRITE );
	}

?>

	<span class="fontTitle">Database: <? printText($dbTitle);?></span><br /><br />

	<?=stripslashes( $dbContent ); ?><br /><br />
	
	<strong class="fontMedium"><a href="<?=$webLocation;?>index.php?page=database">&laquo; Back to Database Entries</a></strong>

<?php } ?>

</div> <!-- close .body -->