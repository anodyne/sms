<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/join.php
Purpose: To display the join application and submit it

System Version: 2.6.7
Last Modified: 2008-12-22 1304 EST
**/

/* define the page class and vars */
$pageClass = "simm";
$query = FALSE;
$result = FALSE;

if( isset( $_GET['agree'] ) ) {
	$agree = $_GET['agree'];
}

if( isset( $_POST['action_x'] ) ) {
	$action = $_POST['action_x'];
}

if(isset($_GET['position']) && is_numeric($_GET['position'])) {
	$pid = $_GET['position'];
} else {
	$pid = NULL;
}

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
	$skinChoice = $sessionDisplaySkin;
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
	$skinChoice = $skin;
}

/* submit the application */
if( isset( $action ) ) {
	
	/* get today's date */
	$today = getdate();
	
	/* build the insert query that's going to be used */
	$join = "INSERT INTO sms_crew ( username, password, crewType, email, realName, aim, msn, yim, icq, ";
	$join.= "positionid, firstName, middleName, lastName, gender, species, heightFeet, heightInches, ";
	$join.= "weight, eyeColor, hairColor, age, physicalDesc, personalityOverview, strengths, ambitions, hobbies, ";
	$join.= "languages, history, serviceRecord, father, mother, brothers, sisters, spouse, children, ";
	$join.= "otherFamily, image, joinDate ) ";
	$join.= "VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, ";
	$join.= "%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d )";
	
	/* run the query through sprintf and the safety function to scrub for security issues */
	$query = sprintf(
		$join,
		escape_string( $_POST['username'] ),
		escape_string( md5( $_POST['password'] ) ),
		escape_string( 'pending' ),
		escape_string( $_POST['email_address'] ),
		escape_string( $_POST['realname'] ),
		escape_string( $_POST['aim'] ),
		escape_string( $_POST['msn'] ),
		escape_string( $_POST['yim'] ),
		escape_string( $_POST['icq'] ),
		escape_string( $_POST['position'] ),
		escape_string( $_POST['firstName'] ),
		escape_string( $_POST['middleName'] ),
		escape_string( $_POST['lastName'] ),
		escape_string( $_POST['gender'] ),
		escape_string( $_POST['species'] ),
		escape_string( $_POST['feet'] ),
		escape_string( $_POST['inches'] ),
		escape_string( $_POST['weight'] ),
		escape_string( $_POST['eyeColor'] ),
		escape_string( $_POST['hairColor'] ),
		escape_string( $_POST['age'] ),
		escape_string( $_POST['appearance'] ),
		escape_string( $_POST['personality'] ),
		escape_string( $_POST['strengths'] ),
		escape_string( $_POST['ambitions'] ),
		escape_string( $_POST['hobbies'] ),
		escape_string( $_POST['languages'] ),
		escape_string( $_POST['history'] ),
		escape_string( $_POST['serviceRecord'] ),
		escape_string( $_POST['father'] ),
		escape_string( $_POST['mother'] ),
		escape_string( $_POST['brothers'] ),
		escape_string( $_POST['sisters'] ),
		escape_string( $_POST['spouse'] ),
		escape_string( $_POST['children'] ),
		escape_string( $_POST['otherFamily'] ),
		escape_string( $_POST['image'] ),
		escape_string( $today[0] )
	);
	
	/* run the query */
	$result = mysql_query( $query );
	
	/* if there's a positive result from the query, send the emails */
	if ( $result != "" ) {
	
		/* loop through the POST array and dynamically assign variables */
		foreach( $_POST as $key => $value )
		{
			$$key = stripslashes( $value );
		}
	
		/* set variables and send email to User */
		$subject = $emailSubject . " Application Submitted";
		$to = $email_address;
		$from = printCO('short_rank') . " <" . printCOEmail() . ">";
		$message = "Greetings $realname,
	
You have recently submitted an application to join the $shipPrefix $shipName.  The CO has been informed of this and should be looking over you application.  Expect an answer within the next few days on whether or not you are accepted. 
	
Thank you for your interest.

This is an automatically generated message, please do not respond.";
		
		/* send the email */
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion() );

		/* get the position name of the application */
		$getPositionName = "SELECT positionName FROM sms_positions WHERE positionid = '$position'";
		$getPositionNameResult = mysql_query( $getPositionName );
		$positioninfo = mysql_fetch_array( $getPositionNameResult );
		
		/* set the subject */
		$subject = $emailSubject . " Character Awaiting Approval";
		
		/* set the TO email addresses */
		$emFetch = "SELECT crewid, email FROM sms_crew WHERE (accessOthers LIKE 'x_approve_users,%' OR accessOthers LIKE '%,x_approve_users' ";
		$emFetch.= "OR accessOthers LIKE '%,x_approve_users,%')";
		$emFetchR = mysql_query($emFetch);
		
		$email_array = array();
		
		while($em_raw = mysql_fetch_array($emFetchR)) {
			extract($em_raw, EXTR_OVERWRITE);
			
			$email_array[] = $em_raw[1];
		}
		
		/* if there isn't anything in the email array, put the CO into the string */
		if(count($email_array) == 0) {
			$to = printCOEmail();
		} else {
			$to = implode(",", $email_array);
		}
		
		$from = $realname . " <" . $email_address . ">";
	
		$message = "A new user has applied to join the " . $shipName . ".  Below you will find the information along with the link to the site to login and approve or deny the application.

== USER INFORMATION ==
Real Name: $realname
Email Address: $email_address
Instant Messengers: 	
AIM - $aim
MSN - $msn
YIM - $yim
ICQ - $icq

== GENERAL INFORMATION ==
NAME: $firstName $middleName $lastName
POSITION: " . $positioninfo['0'] . "
RANK: To Be Assigned
SPECIES: $species
GENDER: $gender
AGE: $age

== APPEARANCE PROFILE ==
HEIGHT: $feet ft. $inches in.
WEIGHT: $weight lbs.
EYE COLOR: $eyeColor
HAIR COLOR: $hairColor
DESCRIPTION: $appearance

== PERSONALITY PROFILE ==
PERSONALITY: $personality
STRENGTHS & WEAKNESSES: $strengths
AMBITIONS: $ambitions
HOBBIES: $hobbies
LANGUAGES: $languages

== HISTORY ==
$history

== FAMILY ==
FATHER: $father
MOTHER: $mother
BROTHER(S): $brothers
SISTER(S): $sisters
SPOUSE: $spouse
CHILDREN: $children
OTHER FAMILY: $otherFamily

IMAGE: $image

== PLAYER EXPERIENCE ==
$playerExperience

== SAMPLE POST ==
$samplePost

Login to your control panel at " . $webLocation . "login.php?action=login to approve or deny this application.";
	
		mail( $to, $subject, $message, "From: " . $from . "\nX-Mailer: PHP/" . phpversion());
	
	} /* close the if statement to send emails */
	
} /* close the if statement on submitting the application */

?>

<div class="body">
	
	<?
	
	$check = new QueryCheck;
	$check->checkQuery( $result, $query );
			
	if( !empty( $check->query ) ) {
		$check->message( "application", "submit" );
		$check->display();
	}
	
	?>
	
	<span class="fontTitle">Join the <i><? printText( $shipPrefix . " " . $shipName ); ?></i></span>
	<br /><br />

	<? if( !isset( $agree ) ) { ?>

	<div style="padding: 1em;">Before continuing, you must agree to the following terms of use:</div>
	<div style="padding: 2em;"><i><? printText( $joinDisclaimer ); ?></i></div>
	<div style="padding: 1em;">
		<? if( isset( $pid ) ) { ?>
		<a href="<?=$webLocation;?>index.php?page=join&position=<?=$pid;?>&agree=yes" class="fontMedium"><b>Agree</b></a>
		<? } else { ?>
		<a href="<?=$webLocation;?>index.php?page=join&agree=yes" class="fontMedium"><b>Agree</b></a>
		<? } ?>
	</div>

	<? } else { ?>

	<table>
	<form method="post" action="<?=$webLocation;?>index.php?page=join&agree=yes">
		<tr>
			<td colspan="3" class="fontLarge"><b>Player Information</b></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Username</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="16" name="username" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Password</td>
			<td>&nbsp;</td>
			<td><input type="password" class="image" name="password" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Real Name</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="32" name="realname" /></td>
		</tr> 
		<tr>
			<td class="tableCellLabel">Email</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="64" name="email_address" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Instant Messenger</td>
			<td>&nbsp;</td>
			<td>
				<table class="fontNormal">
					<tr>
						<td width="40" align="right" style="color: #f6c731; font-weight:bold;">AIM</td>
						<td><input type="text" class="image" maxlength="32" name="aim"></td>
					</tr>
					<tr>
						<td width="40" align="right" style="color: #005ca6; font-weight:bold;">MSN</td>
						<td><input type="text" class="image" maxlength="32" name="msn"></td>
					</tr>
					<tr>
						<td width="40" align="right" style="color: #cf181e; font-weight:bold;">Yahoo!</td>
						<td><input type="text" class="image" maxlength="32" name="yim"></td>
					</tr>
					<tr>
						<td width="40" align="right" style="color: #18a218; font-weight:bold;">ICQ</td>
						<td><input type="text" class="image" maxlength="32" name="icq"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" height="20"></td>
		</tr>
		<tr>
			<td colspan="3" class="fontLarge"><b>Character Information</b</td>
		</tr>	
		<tr>
			<td class="tableCellLabel">First Name</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="32" name="firstName" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Middle Name</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="32" name="middleName" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Last Name</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="32" name="lastName" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Species</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="32" name="species" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Gender</td>
			<td>&nbsp;</td>
			<td>
				<select name="gender">
					<option value="Male">Male</option>
					<option value="Female">Female</option>
					<option value="Hermaphrodite">Hermaphrodite</option>
					<option value="Neuter">Neuter</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tableCellLabel">Age</td>
			<td>&nbsp;</td>
			<td><input type="text" class="order" maxlength="3" name="age" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Position</td>
			<td>&nbsp;</td>
			<td>
				<select name="position">
			
				<?
				
				/* get position id if applying through the open positions page */
				if(isset($pid)) {
				
				/* setup sql query for specific position */
				$getPosition = "SELECT * FROM sms_positions WHERE positionid = $pid";
				$getPositionResult = mysql_query( $getPosition ); 
				
				/* extract variables */
				$positioninfo = mysql_fetch_array( $getPositionResult );
					extract( $positioninfo, EXTR_OVERWRITE );
				
				/* pull the department color for the specific position */
				$getDept = "SELECT * ";
				$getDept.= "FROM sms_departments ";
				$getDept.= "WHERE deptid = '$positionDept'";
				$getDeptResult = mysql_query ( $getDept );
				
				/* extract variables */
				$deptinfo = mysql_fetch_array( $getDeptResult );
					extract ( $deptinfo, EXTR_OVERWRITE );
				
					/* set the department name if command/dept head position */
					if( $positionType == "senior" ) {
						$deptName = "Senior Staff";
					}
						
				?>	
	
					<option value="<?=$positionid;?>" style="color:#<?=$deptColor;?>"><? printText( $deptName . " - " . $positionName ); ?></option>
			
				<? } /* close the if pid isset statement */
	
				/* pull all open positions */
				$getPositions = "SELECT a.positionid, a.positionName, a.positionDept, ";
				$getPositions.= "a.positionType, a.positionDisplay, b.deptName, b.deptColor FROM sms_positions AS a, ";
				$getPositions.= "sms_departments AS b WHERE a.positionOpen > '0' AND a.positionDisplay = 'y' AND ";
				$getPositions.= "b.deptDisplay = 'y' AND b.deptid = a.positionDept AND ";
				$getPositions.= "b.deptType = 'playing' ORDER BY a.positionType, a.positionDept, ";
				$getPositions.= "a.positionOrder ASC";
				$getPositionsResult = mysql_query( $getPositions );
			
				/* extract into variables */	
				while ( $positionArray = mysql_fetch_array( $getPositionsResult )) {
					extract( $positionArray, EXTR_OVERWRITE );
			
					/* set the department name if command/dept head position */
					if( $positionType == "senior" ) {
						$deptName = "Senior Staff";
					}
						
				?>
				
					<option value="<?=$positionid;?>" style="color:#<?=$deptColor;?>"><? printText( $deptName . " - " . $positionName ); ?></option>
	
				<? } ?>
				
				</select>
			</td>
		</tr>
			
		<tr>
			<td colspan="3" height="20"></td>
		</tr>
			
		<tr>
			<td class="tableCellLabel">Height</td>
			<td>&nbsp;</td>
			<td>
				<input type="text" class="order" maxlength="3" name="feet" /> &prime; 
				<input type="text" class="order" maxlength="3" name="inches" /> &Prime;
			</td>
		</tr>
		<tr>
			<td class="tableCellLabel">Weight</td>
			<td>&nbsp;</td>
			<td>
				<input type="text" class="text" maxlength="3" size="5" name="weight" /> lbs.
			</td>
		</tr>
		<tr>
			<td class="tableCellLabel">Eye Color</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="16" size="16" name="eyeColor" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Hair Color</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="16" size="16" name="hairColor" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Appearance</td>
			<td>&nbsp;</td>
			<td><textarea rows="6" name="appearance" class="desc"></textarea></td>
		</tr>
		
		<tr>
			<td colspan="3" height="20"></td>
		</tr>
			
		<tr>
			<td class="tableCellLabel">Personality</td>
			<td>&nbsp;</td>
			<td><textarea class="desc" rows="10" name="personality"></textarea></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Strengths &amp;<br>Weaknesses</td>
			<td>&nbsp;</td>
			<td><textarea class="desc" rows="6" name="strengths"></textarea></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Ambitions</td>
			<td>&nbsp;</td>
			<td><textarea class="desc" rows="4" name="ambitions"></textarea></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Hobbies &amp;<br>Interests</td>
			<td>&nbsp;</td>
			<td><textarea class="desc" rows="4" name="hobbies"></textarea></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Languages</td>
			<td>&nbsp;</td>
			<td><input type="text" class="text" maxlength="100" size="32" name="languages" /></td>
		</tr>
		
		<tr>
			<td colspan="3" height="20"></td>
		</tr>
			
		<tr>
			<td class="tableCellLabel">Father</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="100" name="father" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Mother</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="100" name="mother" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Brother(s)</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" name="brothers" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Sister(s)</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" name="sisters" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Spouse</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" name="spouse" /></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Children</td>
			<td>&nbsp;</td>
			<td><textarea class="desc" name="children" rows="3"></textarea></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Other Relatives</td>
			<td>&nbsp;</td>
			<td><textarea class="desc" rows="3" name="otherFamily"></textarea></td>
		</tr>
		
		<tr>
			<td colspan="3" height="20"></td>
		</tr>
			
		<tr>
			<td class="tableCellLabel">History</td>
			<td>&nbsp;</td>
			<td><textarea class="desc" rows="20" name="history"></textarea></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Service Record</td>
			<td>&nbsp;</td>
			<td><textarea class="desc" rows="20" name="serviceRecord"></textarea></td>
		</tr>
		<tr>
			<td class="tableCellLabel">Image</td>
			<td>&nbsp;</td>
			<td><input type="text" class="image" maxlength="100" name="image" /></td>
		</tr>
		<tr>
			<td colspan="3" height="20"></td>
		</tr>

		<tr>
			<td class="tableCellLabel">
				Player Experience<br />
				<span class="fontSmall yellow">
					This information will only be seen by the CO and will not have any
					bearing on your acceptance or denial.
				</span>
			</td>
			<td>&nbsp;</td>
			<td><textarea class="desc" rows="10" name="playerExperience"></textarea></td>
		</tr>
		<tr>
			<td colspan="3" height="20"></td>
		</tr>
		
		<? if( $useSamplePost == "y" ) { ?>
		<tr>
			<td class="tableCellLabel">Sample Post</td>
			<td>&nbsp;</td>
			<td>
				<i><? printText( $samplePostQuestion ); ?></i>
				<br /><br />
				<textarea class="desc" rows="15" name="samplePost"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="3" height="20"></td>
		</tr>
		<? } ?>
		
		<tr>
			<td colspan="2"></td>
			<td>
				<b>Please check over and proofread your biography before you submit it!</b><br />
				<i>Please make sure to save a copy of your bio on your computer!</i>
			</td>
		</tr>
		<tr>
			<td colspan="3" height="10"></td>
		</tr>
		<tr>
			<td colspan="2"></td>
			<td><input type="image" src="<?=$webLocation;?>skins/<?=$skinChoice;?>/buttons/submit.png" name="action" class="button" value="Submit" /></td>
		</tr>
	</form>
	</table>

<? } ?>

</div> <!--Close the div body class tag-->