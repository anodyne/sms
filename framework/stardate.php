<?php

/**
Author: Phillip L. Sublett
File: framework/stardate.php
Reference: http://TrekGuide.com/Stardates.htm
**/

?>

<script type="text/javascript">
	function StardateTNG( year, month, day, hour, minute )
	{
		YearInput = parseInt( year );
		MonthInput = parseInt( month );
		DayInput = parseInt( day );
		HourInput = parseInt( hour );
		MinuteInput = parseInt( minute );
		
		var StardateOrigin = new Date( "May 25, 2322 00:00:00" );
		var StardateInput = new Date();
		
		StardateInput.setYear( YearInput );
		StardateInput.setMonth( MonthInput );
		StardateInput.setDate( DayInput );
		StardateInput.setHours( HourInput );
		StardateInput.setMinutes( MinuteInput );
		StardateInput.setSeconds( 0 );
		StardateInput.toGMTString( 0 );
		
		var findMilliseconds = StardateInput.getTime() - StardateOrigin.getTime();
		
		var findStarYear = findMilliseconds / ( 60 * 60 * 24 * 365.2422 );
		
		findStarYear = Math.floor( findStarYear * 100 );
		findStarYear = findStarYear / 100;
			
		return findStarYear;
	}
	
	function StardateTOS( year, month, day, hour, minute )
	{
		YearInputTOS = parseInt( year );
		MonthInputTOS = parseInt( month );
		DayInputTOS = parseInt( day );
		HourInputTOS = parseInt( hour );
		MinuteInputTOS = parseInt( minute );
		
		var StardateOrigin = new Date( "May 1, 2265 00:00:00" );
		var StardateInputTOS = new Date();
		
		StardateInputTOS.setYear( YearInputTOS )
		StardateInputTOS.setMonth( MonthInputTOS )
		StardateInputTOS.setDate( DayInputTOS )
		StardateInputTOS.setHours( HourInputTOS )
		StardateInputTOS.setMinutes( MinuteInputTOS )
		StardateInputTOS.setSeconds( 0 )
		StardateInputTOS.toGMTString( 0 )
		
		var findMilliseconds = StardateInputTOS.getTime() - StardateOrigin.getTime();
		
		var findStarYear = findMilliseconds / ( 60 * 60 * 24 * 365.2422 );
		
		findStarYear = findStarYear * 2.7113654892;
		findStarYear = Math.floor( findStarYear * 1000 );
		findStarYearResult = Math.floor( findStarYear );
		findStarYearResult = findStarYearResult / 10;
		findStarYearResult = Math.floor( findStarYearResult );
		findStarYearResult = findStarYearResult / 100;
			
		return findStarYearResult;
	}
	
	function CalendarTNG( stardate )
	{
		var StardateOrigin = new Date("May 25, 2322 00:00:00");
		
		var StardateIn = eval( stardate );
		
		var DateOut = StardateIn * 60 * 60 * 24 * 365.2422 ;
		
		var ResultMilliseconds = StardateOrigin.getTime() + DateOut;
		
		var ResultDate = new Date();
		
		ResultDate.setTime( ResultMilliseconds );
		
		var m_names = new Array(
			"January",
			"February",
			"March",
			"April",
			"May",
			"June",
			"July",
			"August",
			"September",
			"October",
			"November",
			"December"
		);
		
		var NiceDate = m_names[ResultDate.getMonth()] + " " + ResultDate.getDate() + ", " + ResultDate.getFullYear();
		
		return NiceDate;
	}
	
	function CalendarizeThisTOS( stardate )
	{
		var StardateOriginTOS = new Date("May 1, 2265 00:00:00");
		
		var StardateInTOS = eval( stardate );
		
		var DateOutTOS = StardateInTOS * 60 * 60 * 24 * 365.2422 /  2.7113654892 ;
		
		var ResultMillisecondsTOS = StardateOriginTOS.getTime() + DateOutTOS;
		
		var ResultDateTOS = new Date();
		
		ResultDateTOS.setTime(ResultMillisecondsTOS);
		
		var m_names = new Array(
			"January",
			"February",
			"March",
			"April",
			"May",
			"June",
			"July",
			"August",
			"September",
			"October",
			"November",
			"December"
		);
		
		var NiceDate = m_names[ResultDateTOS.getMonth()] + " " + ResultDateTOS.getDate() + ", " + ResultDateTOS.getFullYear();
		
		return NiceDate;
	}
</script>

<?php

/* pull in the globals for the defined vars */
include_once( 'functionsGlobal.php' );

/* get today's date information */
$today = getdate();

/* set the variables used by the javascript */
$year = SIM_YEAR;
$month = $today['mon'] -1;
$day = $today['mday'];
$hour = $today['hours'];
$minute = $today['minutes'];

/* set up the variable that'll be passed to the functions */
$variable_pass = $year . ", " . $month . ", " . $day . ", " . $hour . ", " . $minute;

/*
do logic based on timeframe to determine which functions to use

Before 2265 = Enterprise era
2265-2321 = TOS/Movie era
After 2322 = TNG/DS9/VOY era
*/
if( $year >= 2322 ) {
	if( $stardateDisplaySD == "y" ) {
		echo "<span class='fontNormal'>";
		echo "<b>Stardate: </b>";
		echo "<script type='text/javascript'>document.write( StardateTNG( " . $variable_pass . " ) )</script>";
		echo "</span>";
	}
	
	if( $stardateDisplaySD == "y" && $stardateDisplayDate == "y" ) {
		echo "<br />";
	}
	
	if( $stardateDisplayDate == "y" ) {
		echo "<span class='fontNormal'>";
		echo "<b>Date: </b>";
		echo "<script type='text/javascript'>document.write( CalendarTNG( StardateTNG( " . $variable_pass . " ) ) )</script>";
		echo "</span>";
	}
} elseif( $year >= 2265 ) {
	if( $stardateDisplaySD == "y" ) {
		echo "<span class='fontNormal'>";
		echo "<b>Stardate: </b>";
		echo "<script type='text/javascript'>document.write( StardateTOS( " . $variable_pass . " ) )</script>";
		echo "</span>";
	}
	
	if( $stardateDisplaySD == "y" && $stardateDisplayDate == "y" ) {
		echo "<br />";
	}
	
	if( $stardateDisplayDate == "y" ) {
		echo "<span class='fontNormal'>";
		echo "<b>Date: </b>";
		echo "<script type='text/javascript'>document.write( CalendarTOS( StardateTOS( " . $variable_pass . " ) ) )</script>";
		echo "</span>";
	}
} elseif( $year < 2265 ) {
	echo "<span class='fontNormal'><b>Date: </b>" . $today['month'] . " " . $today['mday'] . ", " . $year . "</span>";
}

?>