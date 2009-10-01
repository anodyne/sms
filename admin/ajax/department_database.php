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

if(in_array("m_departments", $sessionAccess))
{
	include_once('../../framework/functionsGlobal.php');
	include_once('../../framework/functionsUtility.php');
	
	$getD = "SELECT * FROM sms_departments ORDER BY deptOrder ASC";
	$getDR = mysql_query($getD);

?>

	<h2>Update Departmental Database Access</h2>
	<p>Use the fields below to change whether or not a department has access to their own database. If a department does have access to their own database, someone in that department will need to be given <strong class="orange">Database-1</strong> access as well.</p>
	<br />

	<form method="post" action="">
		<table class="hud_guts" cellpadding="3">
			<?php
			
			while($depts = mysql_fetch_assoc($getDR)) {
				extract($depts, EXTR_OVERWRITE);
			
			?>
			
			<tr>
				<td class="hudLabel"><?php printText($deptName); ?></td>
				<td></td>
				<td>
					<input type="radio" id="dept_<?=$deptid;?>_y" name="dept_<?=$deptid;?>" value="y" <? if($deptDatabaseUse == "y") { echo "checked "; } ?>/> <label for="dept_<?=$deptid;?>_y">Yes</label>
					<input type="radio" id="dept_<?=$deptid;?>_n" name="dept_<?=$deptid;?>" value="n" <? if($deptDatabaseUse == "n") { echo "checked "; } ?>/> <label for="dept_<?=$deptid;?>_n">No</label>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="5"></td>
			</tr>
			
			<?php } ?>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
		</table>

		<div>
			<input type="hidden" name="action_type" value="database" />
	
			<input type="image" src="<?=$webLocation;?>images/hud_button_ok.png" name="activate" value="Activate" />
		</div>

	</form>

<?php } /* close the referer check */ ?>