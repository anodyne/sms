<?php

/* need to connect to the database */
require_once('../../framework/dbconnect.php');

/* pulling a function from new library */
require_once('../../framework/session.name.php');

/* get system unique identifier */
$sysuid = get_system_uid();

/* rewrite master php.ini session.name */
ini_set('session.name', $sysuid);

session_start();

if( !isset( $sessionAccess ) ) {
	$sessionAccess = FALSE;
}

if( !is_array( $sessionAccess ) ) {
	$sessionAccess = explode( ",", $_SESSION['sessionAccess'] );
}

if(in_array("m_database1", $sessionAccess) || in_array("m_database2",$sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	
	if(in_array("m_database2", $sessionAccess))
	{
		$depts = "SELECT * FROM sms_departments WHERE deptDisplay = 'y'";
		$deptsR = mysql_query($depts);
	}
	elseif(in_array("m_database1", $sessionAccess))
	{
		$depts = "SELECT crew.positionid, position.positionDept, dept.deptName, dept.deptColor FROM ";
		$depts.= "sms_crew AS crew, sms_positions AS position, sms_departments AS dept WHERE ";
		$depts.= "crew.crewid = '$_SESSION[sessionCrewid]' AND crew.positionid = position.positionid LIMIT 1";
		$deptsR = mysql_query($depts);
	}
	
	if(isset($_GET['id']) && is_numeric($_GET['id']))
	{
		$id = $_GET['id'];
	}
	
	/* get the data */
	$get = "SELECT * FROM sms_database WHERE dbid = $id LIMIT 1";
	$getR = mysql_query( $get );
	$pendingArray = mysql_fetch_assoc( $getR );

?>

	<h2>Edit Database Entry</h2>
	<p>Use the fields below to edit your database entry. <strong class="yellow">Note:</strong> For off-site URL forwarding entries, give the full URL (e.g. http://www.something.com/), for on-site URL forwarding entries only give what comes after the location of SMS (e.g. index.php?page=manifest). For reference with on-site entries, your web location is: <strong><?php echo WEBLOC; ?></strong>.</p>
	<p>
		<em><sup>&dagger;</sup>Applies only to on-site &amp; off-site entries</em><br />
		<em><sup>&Dagger;</sup>Applies only to entries</em>
	</p><br />

	<form method="post" action="">
		<table class="hud_guts" cellpadding="3">
			<tr>
				<td class="hudLabel">Title</td>
				<td></td>
				<td><input type="text" class="image" name="dbTitle" value="<?=$pendingArray['dbTitle'];?>" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Type</td>
				<td></td>
				<td>
					<select name="dbType">
						<option value="entry"<? if($pendingArray['dbType'] == "entry") { echo " selected"; } ?>>Database Entry</option>
						<option value="onsite"<? if($pendingArray['dbType'] == "onsite") { echo " selected"; } ?>>URL Forward (On-Site)</option>
						<option value="offsite"<? if($pendingArray['dbType'] == "offsite") { echo " selected"; } ?>>URL Forward (Off-Site)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="hudLabel">URL<sup>&dagger;</sup></td>
				<td></td>
				<td><input type="text" class="image" name="dbURL" value="<?=$pendingArray['dbURL'];?>" /></td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Order</td>
				<td></td>
				<td><input type="text" class="order" name="dbOrder" value="<?=$pendingArray['dbOrder'];?>" /></td>
			</tr>
			<tr>
				<td class="hudLabel">Display?</td>
				<td></td>
				<td>
					<select name="dbDisplay">
						<option value="y"<? if($pendingArray['dbDisplay'] == "y") { echo " selected"; } ?>>Yes</option>
						<option value="n"<? if($pendingArray['dbDisplay'] == "n") { echo " selected"; } ?>>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="hudLabel">Department</td>
				<td></td>
				<td>
					<?php if(in_array("m_database2", $sessionAccess)) { ?>
						<select name="dbDept">
							<option value="0">Global Database</option>
							
							<?php
							
							while($deptFetch = mysql_fetch_assoc($deptsR)) {
								extract($deptFetch, EXTR_OVERWRITE);
							
							?>
								
								<option value="<?=$deptFetch['deptid'];?>"<? if($pendingArray['dbDept'] == $deptFetch['deptid']) { echo " selected"; } ?>><? printText($deptFetch['deptName']);?></option>
							
							<?php } ?>
							
						</select>
					<?php
					
					} elseif(in_array("m_database1", $sessionAccess)) {
						$deptFetch = mysql_fetch_row($deptsR);
						
						echo "<input type='hidden' name='dbDept' value='" . $deptFetch[1] . "' />";
						echo "<strong style='color:#" . $deptFetch[3] . ";'>";
						printText($deptFetch[2]);
						echo "</strong>";
					}
					
					?>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			
			<tr>
				<td class="hudLabel">Short Description</td>
				<td></td>
				<td><textarea name="dbDesc" rows="2" class="desc"><?=$pendingArray['dbDesc'];?></textarea></td>
			</tr>
			<tr>
				<td class="hudLabel">Content<sup>&Dagger;</sup></td>
				<td></td>
				<td><textarea name="dbContent" rows="12" class="desc"><?=$pendingArray['dbContent'];?></textarea></td>
			</tr>
		</table>

		<div>
			<input type="hidden" name="action_id" value="<?=$pendingArray['dbid'];?>" />
			<input type="hidden" name="action_type" value="update" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>