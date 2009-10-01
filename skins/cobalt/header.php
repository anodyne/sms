<?php

/**
Edits to this skin are permissible if the original credits stay intact.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: skins/cobalt/header.php
Purpose: The header file that the system calls for the template

Skin Version: 2.0
Last Modified: 2008-08-31 0113 EST
**/

$path = basename(dirname(__FILE__)); /* get the current directory */
$skins = 'skins/'; /* set the skins directory with slashes */

if (substr($webLocation, -1) != '/')
{ /* make sure the skins directory slashes are right */
	$skins = '/' . $skins;
}

define('SKIN_PATH', $skins . $path . '/'); /* define the skin path */

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title><?=$shipPrefix . " " . $shipName;?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<link rel="stylesheet" href="<?=$webLocation . SKIN_PATH;?>style.css" type="text/css" />
		
		<script type="text/javascript">
			<? include_once( "framework/functionsJavascript.js" ); ?>
		</script>
	</head>
	<body>