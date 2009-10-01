<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: admin/reports/about.php
Purpose: Page to show the information about the site

System Version: 2.6.0
Last Modified: 2008-02-07 0932 EST
**/

/* access check */
if( in_array( "r_about", $sessionAccess ) ) {

	/* set the page class */
	$pageClass = "admin";
	$subMenuClass = "reports";
	
	/* get the data */
	$get = "SELECT * FROM sms_system_plugins ORDER BY pid ASC";
	$getR = mysql_query($get);
	
?>
	
	<div class="body">
		<span class="fontTitle">About Simm Management System</span><br /><br />
		
			The information on this page is generated dynamically by SMS and will show you
			the some of the major pieces of information about your version of SMS, including the
			various plugins used by SMS.  Please note, there is no way to change these variables
			manually.  They are stored in the database and updated only in the event of a patch
			or update. <b class="orange">Please do not attempt to update any of the plugins as an
			update may have break core functionality!</b> If you need to request support with the
			system, please copy and paste the following information into the post on our 
			<a href="http://forums.anodyne-productions.com/" target="_blank"> support forums</a>.
			<br /><br />
	
			<? aboutSMS( $version, $webLocation ); ?>
			
			<span class="fontTitle">Plugins</span>
			
			<?php

			while($plugins = mysql_fetch_assoc($getR)) {
				extract($plugins, EXTR_OVERWRITE);

				echo "<ul class='version'>";
				echo "<li><b class='fontMedium'><a href='" . $plugins['pluginSite'] . "' target='_blank'>" . $plugins['plugin'] . "</a></b></li>";
				
				echo "<li><b>Uses</b></li>";
					echo "<ul>";
						
						$uses = explode(";", $plugins['pluginUse']);
						foreach($uses as $a => $b)
						{
							echo "<li>" . $b . "</li>";
						}
						
					echo "</ul>";
				
				echo "<li><b>Files</b></li>";
					echo "<ul>";
						
						$files = explode(";", $plugins['pluginFiles']);
						foreach($files as $c => $d)
						{
							echo "<li>" . $d . "</li>";
						}
						
					echo "</ul>";
			echo "</ul>";

			}

			?>
			
	</div>

<? } else { errorMessage( "about SMS" ); } ?>