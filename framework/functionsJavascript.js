/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/functionsJavascript.js
Purpose: Handles all Javascript actions by the system, including pulling in
	the various jQuery elements

System Version: 2.6.0
Last Modified: 2008-04-23 1930 EST
**/

/**
	Function that will include JS files
**/
function include_dom( type, script_filename )
{
	if( type == "js" )
	{
		var html_doc = document.getElementsByTagName( 'head' ).item(0);
		var js = document.createElement( 'script' );
		js.setAttribute( 'language', 'javascript' );
		js.setAttribute( 'type', 'text/javascript' );
		js.setAttribute( 'src', script_filename );
		html_doc.appendChild( js );
		return false;
	}
	if( type == "css" )
	{
		var cssNode = document.createElement( 'link' );
		cssNode.setAttribute( 'rel', 'stylesheet' );
		cssNode.setAttribute( 'type', 'text/css' );
		cssNode.setAttribute( 'href', script_filename );
		document.getElementsByTagName( 'head' )[0].appendChild( cssNode );
		return false;
	}
}
/** END FUNCTION **/

/** pull in the JS files **/
include_dom( 'js', 'framework/js/jquery.js' );
include_dom( 'js', 'framework/js/ui.tabs.js' );
include_dom( 'js', 'framework/js/clickmenu.js' );
include_dom( 'js', 'framework/js/shadowbox-jquery.js' );
include_dom( 'js', 'framework/js/shadowbox.js' );
include_dom( 'js', 'framework/js/facebox.js' );
include_dom( 'js', 'framework/js/linkscrubber.js' );
include_dom( 'js', 'framework/js/reflect.js' );

/** pull in the CSS files **/
include_dom( 'css', 'framework/css/shadowbox.css' );
include_dom( 'css', 'framework/css/facebox.css' );
include_dom( 'css', 'framework/css/global.css' );

/**
	Function that toggles checkboxes
**/
function selectAll(formObj, isInverse) 
{
	for (var i=0;i < formObj.length;i++) 
	{
		fldObj = formObj.elements[i];
		if (fldObj.type == 'checkbox')
		{
			if(isInverse)
				fldObj.checked = (fldObj.checked) ? false : true;
			else fldObj.checked = true; 
		}
	}
}
/** END FUNCTION **/

/**
	Function to make sure that jp authors doesn't go
	outside the acceptable range
**/
function checkNumber(upper, actual)
{
	if(actual < 1)
		window.alert("ERROR: You can't set the author number below 1! Please try again.");
	else if(actual > upper)
		window.alert("WARNING: You do not have " + actual + " crew members, you only have " + upper + "! The number you input should not exceed your crew count.");
}
/** END FUNCTION **/