<?php

//	START "session.name" MOD BY JON MATTERSON

function get_system_uid() {
	$query = "SELECT sysuid FROM sms_system WHERE sysid = 1";
	$result = mysql_query($query);
	
	if (!empty($result))
	{
		$sysuid = mysql_fetch_array($result);
		
		return $sysuid[0];
	}
	
	return FALSE;
}

//	END "session.name" MOD BY JON MATTERSON

?>