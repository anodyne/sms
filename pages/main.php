<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/main.php
Purpose: The main file that the system defaults to on the user side

System Version: 2.6.0
Last Modified: 2007-11-12 1504 EST
**/

/* define the page class */
$pageClass = "main";

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

?>

<div class="body">
	<span class="fontTitle">Welcome to the <? printText( $shipPrefix ); ?> <? printText( $shipName ); ?></span>
	<p><? printText( $welcomeMessage ); ?></p>
	
	<? if( $showNews == "y" ) { ?>
	<br /><br />
	<span class="fontTitle">Recent News</span> &nbsp;&nbsp;&nbsp;
	<span class="fontNormal">
		[ <a href="<?=$webLocation;?>index.php?page=news">Show All News &raquo;</a> ]
	</span><br /><br />
	
	<?
	
	/* query the db to get all the news data */
	$getNewsItems = "SELECT news.*, cat.* FROM sms_news AS news, sms_news_categories AS cat ";
	$getNewsItems.= "WHERE news.newsCat = cat.catid AND news.newsStatus = 'activated' ";
	$getNewsItems.= "ORDER BY newsPosted DESC LIMIT $showNewsNum";
	$getNewsItemsResult = mysql_query( $getNewsItems );
	
	/* loop through everything until you run out of results */
	while( $newsItems = mysql_fetch_array( $getNewsItemsResult ) ) {
		extract( $newsItems, EXTR_OVERWRITE );
		
		/* query the database to get the author rank, first name, and last name */
		$getNewsAuthor = "SELECT crew.crewid, crew.firstName, crew.lastName, rank.rankName ";
		$getNewsAuthor.= "FROM sms_crew AS crew, sms_ranks AS rank WHERE crew.rankid = rank.rankid ";
		$getNewsAuthor.= "AND crew.crewid = '$newsAuthor'";
		$getNewsAuthorResult = mysql_query( $getNewsAuthor );
		
		while( $newsAuthorArray = mysql_fetch_array( $getNewsAuthorResult ) ) {
			extract( $newsAuthorArray, EXTR_OVERWRITE );
		}
		
		/* set the author var w/ link */
		$author = "<a href='" . $webLocation . "index.php?page=bio&crew=" . $crewid . "'>" . $rankName . " " . $firstName . " " . $lastName . "</a>";
		
		/* if the news item is set to private and the person isn't logged in, don't show them anything */
		if( $newsPrivate == 'y' && !isset( $sessionCrewid ) ) {} else {
		
	?>
	
	<span class="fontMedium"><b><? printText( $newsTitle ); ?></b></span><br />
	<span class="fontSmall">
		Posted by <? printText( $author ); ?> on <?=dateFormat( "long", $newsPosted );?><br />
		Category: <? printText( $catName ); ?>
	</span><br />
	<div style="padding: 1em 0 3em 1em;">
		<? printText( $newsContent ); ?>
	</div>
	
	<? } } } ?>
	
</div>