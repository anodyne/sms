<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/manage/tour.php
Purpose: Page that moderates the ship tour pages

System Version: 2.6.0
Last Modified: 2008-04-18 1523 EST
**/

/* access check */
if(in_array("m_tour", $sessionAccess))
{
	/* set the page class and vars */
	$pageClass = "admin";
	$subMenuClass = "manage";
	$action_type = FALSE;
	$query = FALSE;
	$result = FALSE;
	
	if(isset($_POST))
	{
		/* define the POST variables */
		foreach($_POST as $key => $value)
		{
			$$key = $value;
		}
		
		/* protecting against SQL injection */
		if(isset($action_id) && !is_numeric($action_id))
		{
			$action_id = FALSE;
			exit();
		}
		
		switch($action_type)
		{
			case 'add':
				
				$insert = "INSERT INTO sms_tour (tourName, tourLocation, tourDisplay, tourOrder, tourDesc, tourPicture1, ";
				$insert.= "tourPicture2, tourPicture3, tourSummary) VALUES (%s, %s, %s, %d, %s, %s, %s, %s, %s)";
				
				$query = sprintf(
					$insert,
					escape_string($_POST['tourName']),
					escape_string($_POST['tourLocation']),
					escape_string($_POST['tourDisplay']),
					escape_string($_POST['tourOrder']),
					escape_string($_POST['tourDesc']),
					escape_string($_POST['tourPicture1']),
					escape_string($_POST['tourPicture2']),
					escape_string($_POST['tourPicture3']),
					escape_string($_POST['tourSummary'])
				);
				
				$result = mysql_query($query);

				$action = "create";
				
				break;
			case 'edit':
				
				$update = "UPDATE sms_tour SET tourName = %s, tourLocation = %s, tourDisplay = %s, tourOrder = %d, ";
				$update.= "tourDesc = %s, tourPicture1 = %s, tourPicture2 = %s, tourPicture3 = %s, tourSummary = %s ";
				$update.= "WHERE tourid = $action_id LIMIT 1";
				
				$query = sprintf(
					$update,
					escape_string($_POST['tourName']),
					escape_string($_POST['tourLocation']),
					escape_string($_POST['tourDisplay']),
					escape_string($_POST['tourOrder']),
					escape_string($_POST['tourDesc']),
					escape_string($_POST['tourPicture1']),
					escape_string($_POST['tourPicture2']),
					escape_string($_POST['tourPicture3']),
					escape_string($_POST['tourSummary'])
				);
				
				$result = mysql_query($query);

				$action = "update";
				
				break;
			case 'delete':
				
				$query = "DELETE FROM sms_tour WHERE tourid = $action_id LIMIT 1";
				$result = mysql_query($query);

				$action = "delete";
			
				break;
		}
		
		/* optimize the table */
		optimizeSQLTable( "sms_tour" );
	}

?>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$('.zebra tr:nth-child(odd)').addClass('alt');
			
			$("a[rel*=facebox]").click(function() {
				var id = $(this).attr("myID");
				var action = $(this).attr("myAction");

				jQuery.facebox(function() {
					jQuery.get('admin/ajax/tour_' + action + '.php?id=' + id, function(data) {
						jQuery.facebox(data);
					});
				});
				return false;
			});
		});
	</script>
	
	<div class="body">
		<?php
		
		$check = new QueryCheck;
		$check->checkQuery( $result, $query );
		
		if( !empty( $check->query ) ) {
			$check->message( "tour item", $action );
			$check->display();
		}
		
		?>
		<span class="fontTitle">Tour Management</span><br /><br />
		The tour feature is designed to allow COs to give their players and visitors a visual picture of some of the major locations on the ship or starbase.  To that end, you can specify not only a name and location, but a description as well as show up to three images of the location if you&rsquo;d like. If you want to include more images than three, you&rsquo;ll need to reference them in the description. In addition, images for your tour must be stored on your server and be put in the <em class="orange">images/tour</em> directory.  Simply specify the name of the image and its extension and SMS will take care of the rest!<br /><br />
		
		<a href="#" rel="facebox" myAction="add" myId="0" class="add fontMedium"><strong>Add New Tour Item &raquo;</strong></a>
		<br /><br />
		
		<table class="zebra" cellpadding="3" cellspacing="0">
		<?php
		
		/* pull the ranks from the database */
		$getTour = "SELECT * FROM sms_tour ORDER BY tourOrder ASC";
		$getTourResult = mysql_query( $getTour );
		
		/* loop through the results and fill the form */
		while( $tourFetch = mysql_fetch_assoc( $getTourResult ) ) {
			extract( $tourFetch, EXTR_OVERWRITE );

		?>
		
			<tr>
				<td><? printText( $tourName ); ?></td>
				<td align="center" width="10%"><strong><a href="<?=$webLocation;?>index.php?page=tour&id=<?=$tourid;?>">View</a></strong></td>
				<td align="center" width="10%"><b><a href="#" rel="facebox" myAction="edit" myID="<?=$tourid;?>" class="edit">Edit</a></b></td>
				<td align="center" width="10%"><b><a href="#" rel="facebox" myAction="delete" myID="<?=$tourid;?>" class="delete">Delete</a></b></td>
			</tr>
	
		<?php } ?>

		</table>
	</div>

<? } else { errorMessage( "tour management" ); } ?>