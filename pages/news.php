<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: pages/news.php
Purpose: Page to display the news items

System Version: 2.6.10
Last Modified: 2009-09-02 0658 EST
**/

/* define the page class and vars */
$pageClass = "main";

if( isset( $_GET['disp'] ) ) {
	$display = $_GET['disp'];
}

if( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) {
	$id = $_GET['id'];
}

/* pull in the menu */
if( isset( $sessionCrewid ) ) {
	include_once( 'skins/' . $sessionDisplaySkin . '/menu.php' );
} else {
	include_once( 'skins/' . $skin . '/menu.php' );
}

?>

<div class="body">
	
	<? if( !isset( $id ) ) { ?>
	<div align="center">
	<span class="fontNormal">
		<a href="<?=$webLocation;?>index.php?page=news">All News</a>

		<?
		
		/* get the news categories */
		$categories = "SELECT * FROM sms_news_categories WHERE catVisible = 'y' ORDER BY catid ASC";
		$categoriesResult = mysql_query( $categories );
		
		while ( $catList = mysql_fetch_assoc( $categoriesResult ) ) {
			extract( $catList, EXTR_OVERWRITE );
		
		?>
		
			&nbsp; &middot; &nbsp;
			<a href="<?=$webLocation;?>index.php?page=news&disp=<?=$catid;?>"><?=$catName;?></a>
		
		<?
		
		}
		
		if( isset( $display ) ) {
			
			$newsCatTitle = "SELECT catName FROM sms_news_categories WHERE catid = '$display'";
			$newsCatTitleResult = mysql_query( $newsCatTitle );
			$category = mysql_fetch_assoc( $newsCatTitleResult );
			
		}
		
		?>
	
	</span>
	</div> <!-- close the centering div -->
	<br />
	
	<span class="fontTitle">
	<?
	
	if( !isset( $display ) ) {
		echo "All News";
	} else {
		echo $category['catName'];
	}
	
	?>
	</span><br /><br />
	
	<?
		
		if( !isset( $display ) ) {
		
			$news = "SELECT news.*, cat.* FROM sms_news AS news, sms_news_categories AS cat ";
			$news.= "WHERE news.newsCat = cat.catid AND news.newsStatus = 'activated' ";
			$news.= "ORDER BY newsPosted DESC";
			$newsResults = mysql_query( $news );
		
		} else {
		
			$news = "SELECT news.*, cat.* FROM sms_news AS news, sms_news_categories AS cat ";
			$news.= "WHERE news.newsCat = '$display' AND news.newsStatus = 'activated' AND news.newsCat = cat.catid ";
			$news.= "GROUP BY news.newsid ORDER BY news.newsPosted DESC";
			$newsResults = mysql_query( $news );

		}
		
		while ( $newsList = mysql_fetch_assoc( $newsResults ) ) {
			extract( $newsList, EXTR_OVERWRITE );
			
			$length = 50; /* The number of words you want */
			$words = explode(' ', $newsContent); /* Creates an array of words */
			$words = array_slice($words, 0, $length); /* Slices the array */
			$text = implode(' ', $words); /* Grabs only the specified number of words */
			
			if( $newsPrivate == 'y' && !isset( $sessionCrewid ) ) {} else {
			
		?>
		
		<span class="fontMedium"><b><? printText( $newsList['newsTitle'] ); ?></b></span><br />
		<span class="fontSmall">
			Posted by <? printCrewName( $newsAuthor, "rank", "link" ); ?> on <?=dateFormat( "long", $newsPosted );?><br />
			Category: <? printText( $newsList['catName'] ); ?>
		</span><br />
		<div style="padding: 1em 0 3em 1em;">
			<?
			
			printText( $text );
			
			echo " ... [ <a href='" . $webLocation . "index.php?page=news&id=" . $newsid . "'>Read More &raquo;</a> ]";
			
			?>
		</div>
		
		<? } } ?>
		
		<?
		
		} else { /* close the if NO id section */
		
			$news = "SELECT news.*, cat.* FROM sms_news AS news, sms_news_categories AS cat ";
			$news.= "WHERE news.newsid = '$id' AND news.newsCat = cat.catid";
			$newsResults = mysql_query( $news );
			
			while ( $newsList = mysql_fetch_assoc( $newsResults ) ) {
				extract( $newsList, EXTR_OVERWRITE );
				
				/* pull all posts to create the next and prev post links */
				$getNext = "SELECT newsid FROM sms_news WHERE newsStatus = 'activated' AND ";
				$getNext.= "newsPosted > $newsList[newsPosted] ORDER BY newsPosted ASC LIMIT 1";
				$getNextR = mysql_query($getNext);
				$fetchNext = mysql_fetch_array($getNextR);
				$next = $fetchNext[0];
				
				$getPrev = "SELECT newsid FROM sms_news WHERE newsStatus = 'activated' AND ";
				$getPrev.= "newsPosted < $newsList[newsPosted] ORDER BY newsPosted DESC LIMIT 1";
				$getPrevR = mysql_query($getPrev);
				$fetchPrev = mysql_fetch_array($getPrevR);
				$prev = $fetchPrev[0];
				
				if( $newsPrivate == 'y' && !isset( $sessionCrewid ) ) {} else {
		
		?>
		
		<span class="fontTitle"><? printText( $newsTitle ); ?></span><br /><br />
		
		<span class="fontNormal postDetails">
		<div align="center">
		
			<?
			
			/* point the previous and next post buttons to the correct posts */
			if ($prev != FALSE)
			{
				echo "<a href='". $webLocation ."/index.php?page=news&id=". $prev ."' class='image'>";
					echo "<img src='". $webLocation ."/images/previous.png' alt='Previous Entry' border='0' />";
				echo "</a>";
			}
			
			if ($next != FALSE)
			{
				echo "<a href='". $webLocation ."/index.php?page=news&id=". $next ."' class='image'>";
					echo "<img src='". $webLocation ."/images/next.png' alt='Next Entry' border='0' />";
				echo "</a>";
			}
		
			?>
				
			<br />
			<strong>News Details</strong><br />
			<?
		
			if( in_array( "m_news", $sessionAccess ) ) {
				echo "<a href='" . $webLocation . "admin.php?page=manage&sub=news&id=" . $id . "' class='edit'><b>Edit</b></a>";
				echo "&nbsp; &middot; &nbsp;";

			?>	
	
				<script type="text/javascript">
					document.write( "<a href=\"<?=$webLocation;?>admin.php?page=manage&sub=news&remove=<?=$id;?>\" class=\"delete\" onClick=\"javascript:return confirm('This action is permanent and cannot be undone. Are you sure you want to delete this personal log?')\"><b>Delete</b></a>" );
				</script>
				<noscript>
					<a href="<?=$webLocation;?>admin.php?page=manage&sub=news&remove=<?=$id;?>" class="delete"><b>Delete</b></a>
				</noscript>
					
				<?
					
					if( $newsList['newsStatus'] == "pending" ) {
					
						echo "&nbsp; &middot; &nbsp;";
						echo "<a href='" . $webLocation . "admin.php?page=manage&sub=activate'><b>Activate</b></a>";
					
					}
				}
				
				?><p></p>
			</div> <!-- close the centering div -->
			
			<table>
				<tr>
					<td class="tableCellLabel">Title</td>
					<td>&nbsp;</td>
					<td><? printText( $newsTitle ); ?></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Category</td>
					<td>&nbsp;</td>
					<td><? printText( $catName ); ?></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Author</td>
					<td>&nbsp;</td>
					<td><? printCrewName( $newsAuthor, "rank", "link" ); ?></td>
				</tr>
				<tr>
					<td class="tableCellLabel">Posted</td>
					<td>&nbsp;</td>
					<td><?=dateFormat( "medium", $newsPosted );?></td>
				</tr>
			</table>
			<br />
			<div align="center">
				<b><a href="<?=$webLocation;?>index.php?page=news">Back to All News</a></b>
			</div>
		</span>
		
		<? printText( $newsContent );?>
		
		
	<? } } } ?>
	
</div>