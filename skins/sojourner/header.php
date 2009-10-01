<?php

/**
Edits to this skin are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/default/header.php
Purpose: The header file that the system calls for the template

Skin Version: 2.5
Last Modified: 2008-08-31 0112 EST
**/

$path = basename(dirname(__FILE__)); /* get the current directory */
$skins = 'skins/'; /* set the skins directory with slashes */

if (substr($webLocation, -1) != '/')
{ /* make sure the skins directory slashes are right */
	$skins = '/' . $skins;
}

$name = explode('/', $_SERVER['SCRIPT_NAME']);

define('SKIN_PATH', $skins . $path . '/'); /* define the skin path */
define('CUR_PAGE', end($name));

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title><?php echo $shipPrefix .' '. $shipName;?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<link rel="stylesheet" href="<?php echo $webLocation . SKIN_PATH;?>style.css" type="text/css" />
		
		<script type="text/javascript">
			<?php include_once('framework/functionsJavascript.js');?>
		</script>
		<script type="text/javascript" src="<?php echo SKIN_PATH;?>assets/jquery.cycle.js"></script>
		<script type="text/javascript" src="<?php echo SKIN_PATH;?>assets/hoverIntent.js"></script>
		<script type="text/javascript" src="<?php echo SKIN_PATH;?>assets/superfish.js"></script>
	</head>
	<body>