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

?>

<h2>Rank Change Notice</h2>
<p>You cannot change this character&rsquo;s rank because your rank class does not match their rank class. This usually happens because you do not have a high enough access level. If you want to be able to change characters to any rank of any color, you must have <strong class="orange">Bio-3</strong> privileges. Please contact your CO to request this access level.</p>
<br />


<a href=""><img src="<?=$webLocation;?>images/hud_button_ok.png" alt="Ok" /></a>