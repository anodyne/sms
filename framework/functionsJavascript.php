<link rel="stylesheet" href="<?php echo $webLocation;?>framework/css/shadowbox.css" />
<link rel="stylesheet" href="<?php echo $webLocation;?>framework/css/facebox.css" />
<link rel="stylesheet" href="<?php echo $webLocation;?>framework/css/global.css" />

<script type="text/javascript" src="<?php echo $webLocation;?>framework/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $webLocation;?>framework/js/ui.tabs.js"></script>
<script type="text/javascript" src="<?php echo $webLocation;?>framework/js/clickmenu.js"></script>
<script type="text/javascript" src="<?php echo $webLocation;?>framework/js/shadowbox-jquery.js"></script>
<script type="text/javascript" src="<?php echo $webLocation;?>framework/js/shadowbox.js"></script>
<script type="text/javascript" src="<?php echo $webLocation;?>framework/js/facebox.js"></script>
<script type="text/javascript" src="<?php echo $webLocation;?>framework/js/linkscrubber.js"></script>
<script type="text/javascript" src="<?php echo $webLocation;?>framework/js/reflect.js"></script>

<script type="text/javascript">
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
	
	function checkNumber(upper, actual)
	{
		if(actual < 1)
			window.alert("ERROR: You can't set the author number below 1! Please try again.");
		else if(actual > upper)
			window.alert("WARNING: You do not have " + actual + " crew members, you only have " + upper + "! The number you input should not exceed your crew count.");
	}
</script>