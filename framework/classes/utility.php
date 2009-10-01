<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/classes/utility.php
Purpose: Page with the class that is called by the system to check for the
	successful execution of a SQL query and display the appropriate
	messages

System Version: 2.6.0
Last Modified: 2008-04-22 1937 EST

Included Classes:
	QueryCheck
	FirstLaunch
	MessageReplace
**/

class QueryCheck
{

	/* set the class variables */
	var $result;
	var $image;
	var $message;
	var $query;

	/* check the query coming through */
	function checkQuery( $result, $query ) {
		
		/*
			if the query is empty (AKA they haven't hit submit yet)
			the result will be a blank string, otherwise, it'll be
			the query which will be evaluated to see if the next 2
			functions should be run or not
		*/
		$this->query = $query;
		
		/*
			evaluate if the result is good or bad and set the
			result and image variables accordingly
		*/
		if( !empty( $result ) ) {
			$this->result = TRUE;
			$this->image = "images/update.png";
		} else {
			$this->result = FALSE;
			$this->image = "images/fail.png";
		}

	} /* close checkQuery() */

	/* set the message that will be displayed */
	function message( $object, $action ) {
		
		/* verb array */
		$verbs = array(
			0 => 'site globals',
			1 => 'site messages',
			2 => 'specifications',
			3 => 'site options',
			4 => 'private messages',
			5 => 'player access levels',
			6 => 'crew access levels',
			7 => 'user moderation flags'
		);
		
		/* verb tense logic */
		if( in_array( $object, $verbs ) ) {
			$verb = "were";
		} elseif( $object == "chain of command" ) {
			$verb = "position was";
		} else {
			$verb = "was";
		}
		
		/* action array */
		$actions = array(
			'activate' => array( 'activation', 'activated' ),
			'update' => array( 'update', 'updated' ),
			'delete' => array( 'deletion', 'deleted' ),
			'create' => array( 'creation', 'created' ),
			'reject' => array( 'rejection', 'rejected' ),
			'approve' => array( 'approval', 'approved' ),
			'add' => array( 'addition', 'added' ),
			'remove' => array( 'removal', 'removed' ),
			'save' => array( 'save', 'saved' ),
			'send' => array( 'send', 'sent' ),
			'post' => array( 'post', 'posted' ),
			'submit' => array( 'submit', 'submitted' ),
			'deactivate' => array( 'deactivation', 'deactivated' ),
			'reset' => array( 'reset', 'reset' ),
			'accept' => array( 'acceptance', 'accepted' ),
			'deny' => array( 'denial', 'denied' )
		);
		
		/* take the neccessary action based on whether the result of the query is TRUE or FALSE */
		if( $this->result == TRUE ) {
			
			/* define the successful message */
			$this->message = ucfirst( $object ) . " " . $verb . " successfully " . $actions[$action][1] . "!";
			
			/* this phrase should be added for skin and rank set updates */
			if( $object == "skin" || $object == "rank set" ) {
				$this->message.= " Please refresh this page or navigate to a new page to see your changes.";
			}
			
			/* this phrase should be added for skin and rank set updates */
			if( $object == "site globals" ) {
				$this->message.= " Some changes to the Site Globals require menu changes as well. Please check the <a href='admin.php?page=manage&sub=menus'>menu management</a> page for instructions on any menu changes that need to be made.";
			}
			
			/* this phrase should be added for skin and rank set updates */
			if( $object == "site options" ) {
				$this->message.= " Personalized menu changes require going to another page for the updates to take affect.";
			}
			
		} else {
			
			/* define the unsuccessful message */
			$this->message = ucfirst( $actions[$action]['0'] ) . " failed! " . ucfirst( $object ) . " " . $verb . " not successfully " . $actions[$action]['1'] . ".";
			
			/*
				the phrase should be different in the event that an account update has failed
				since it's likely that the user has messed up a password
			*/
			if( $object == "account" ) {
				$this->message.= " If you are trying to update your username, real name, or email address, please make sure you have included your current password. If you are trying to reset your password, please make sure that both passwords match and try again.";
			}
			
			$this->message.= "<br /><br />If this problem persists, please use the <a href='http://forums.anodyne-productions.com/' target='_blank'>Anodyne Support Forums</a> for more help. To expedite the support process, please copy and paste the following error report into a new topic (or an existing topic if one exists) on the forums, making sure to substitute sensitive information, such as passwords, with an *.<br /><br />";
			
			/* sms error report */
			$this->message.= "SMS ERROR REPORT<br />=====<br /><br />";
			$this->message.= "Web Location: " . WEBLOC . "<br />";
			$this->message.= "File Version: " . VER_FILES . "<br />";
			$this->message.= "Database Version: " . VER_DB . "<br /><br />";
			$this->message.= "MySQL Query: " . $this->query;
			
		} /* close the logic */

	} /* close message() */

	/* display all the information */
	function display() {

		echo "<div class='update'>";
			
			switch( $this->result )
			{
				case TRUE:
					echo "<div class='notify-green'>";
					break;
				case FALSE:
					echo "<div class='notify-red'>";
					break;
			}
			
			echo $this->message;
			echo "</div>";
		echo "</div>";
		echo "<br />";

	} /* close display() */

} /* close the class */

class FirstLaunch
{

	/* set the class variables */
	var $status;
	var $version;
	var $summary;
	
	/* pulls the system launch status */
	function checkStatus() {
	
		/* query the database */
		$query = "SELECT sysLaunchStatus FROM sms_system WHERE sysid = '1'";
		$result = mysql_query( $query );
		$fetch = mysql_fetch_array( $result );
		
		/* update the status variable */
		$this->status = $fetch[0];
	
	} /* close checkStatus() */
	
	/* gather the info */
	function gather() {
	
		$query = "SELECT sys.*, ver.* FROM sms_system AS sys, sms_system_versions AS ver ";
		$query.= "WHERE sys.sysid = '1' AND sys.sysVersion = ver.version LIMIT 1";
		$result = mysql_query( $query );
		$fetch = mysql_fetch_assoc( $result );
		
		$this->version = $fetch['sysVersion'];
		$this->summary = $fetch['versionShortDesc'];
	
	} /* close gather() */
	
	/* print out the info */
	function display() {
		
		echo "<div class='update'>";
			echo "<div class='notify-blue'>";
				echo "<b class='blue case'>SMS First Launch</b> &mdash; ";
				echo "Congratulations, this is your first time launching SMS " . $this->version . "! " . $this->summary;
				echo "<br />";
				echo "For a complete listing of new features and bug fixes, please view the <a href='admin.php?page=reports&sub=history'>version history</a>.";
			echo "</div>";
		echo "</div><br />";
	
	} /* close display() */
	
	/* update the launch field */
	function update() {
	
		$query = "UPDATE sms_system SET sysLaunchStatus = 'y' WHERE sysid = '1'";
		$result = mysql_query( $query );
	
	} /* close update() */

} /* close the FirstLaunch class */

class MessageReplace
{

	var $message;
	var $substitute;
	var $shipName;
	var $player;
	var $rank;
	var $position;
	
	/* this function runs in the setArray() function to substitute values for the array */
	function substitute()
	{
		
		/* get the rank */
		$getRank = "SELECT rankName FROM sms_ranks WHERE rankid = '$this->rank' LIMIT 1";
		$getRankResult = mysql_query( $getRank );
		$fetchRank = mysql_fetch_array( $getRankResult );
		$this->rank = $fetchRank[0];
		
		/* get the position */
		$getPos = "SELECT positionName FROM sms_positions WHERE positionid = '$this->position' LIMIT 1";
		$getPosResult = mysql_query( $getPos );
		$fetchPos = mysql_fetch_array( $getPosResult );
		$this->position = $fetchPos[0];
		
		/* get the first and last names */
		$getPlayer = "SELECT firstName, lastName FROM sms_crew WHERE crewid = '$this->player' LIMIT 1";
		$getPlayerResult = mysql_query( $getPlayer );
		$fetchPlayer = mysql_fetch_array( $getPlayerResult );
		$this->player = $fetchPlayer[0] . " " . $fetchPlayer[1];
	
	}
	
	/* run the substitute() function and then build array for substitution */
	function setArray()
	{
		
		$this->substitute();
	
		$this->substitute = array(
			"player" => $this->player,
			"ship" => $this->shipName,
			"rank" => $this->rank,
			"position" => $this->position
		);
	
	}
	
	/* change the message based on the values */
	function changeMessage()
	{
		$result = $this->message;

		/* iterate over the substitute values and replace each one in the result string */
		foreach( $this->substitute as $key => $value ) {
			if( strpos( $result, "#" . $key . "#" ) !== FALSE ) {
				$result = str_replace( "#" . $key . "#", $value, $result );
			}
		}

		return $result;
	}

} /* close the substitution class */

?>