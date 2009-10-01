<?php

/**
This is a necessary system file. Do not modify this page unless you are highly
knowledgeable as to the structure of the system. Modification of this file may
cause SMS to no longer function.

Author: David VanScott [ davidv@anodyne-productions.com ]
File: framework/classMenu.php
Purpose: Page with the menu class that is called by the skin to build the various
	menus used throughout SMS

System Version: 2.6.10
Last Modified: 2009-09-03 1227 EST
**/

class Menu
{
	var $skin;
	
	/* function that builds the mainNav */
	function main() {

		/* get the mainNav items from the DB */
		$getMenu = "SELECT * FROM sms_menu_items WHERE menuCat = 'main' ";
		$getMenu.= "AND menuAvailability = 'on' ORDER BY menuGroup, menuOrder ASC";
		$getMenuResult = mysql_query( $getMenu );
		
		/* loop through whatever comes out of the database */
		while( $fetchMenu = mysql_fetch_array( $getMenuResult ) ) {
			extract( $fetchMenu, EXTR_OVERWRITE );
			
			/* create a multi-dimensional array with the data
				[x] => array
				[x]['title'] => title
				[x]['link'] => link
				[x]['login'] => login
				[x]['linkType'] => link type
			*/
			$menuArray[] = array(
				'title' => $menuTitle,
				'link' => $menuLink,
				'login' => $menuLogin,
				'linkType' => $menuLinkType,
			);
			
		}
		
		/* open the unordered list */
		echo "<ul id='nav-main'>";
		
		/* loop through each key of the array, evaluate it, then spit it out */
		foreach( $menuArray as $key => $value ) {
			
			/* check the link type and then set the prefix and target */
			if( $value['linkType'] == "onsite" ) {
				$prefix = WEBLOC;
				$target = "";
			} else {
				$prefix = "";
				$target = " target='_blank'";
			}
				
			/* if the item doesn't require a login, display it */
			if( $value['login'] == "n" ) {
				
				if( $key != 0 ) {
					echo "<li class='spacer'>&nbsp;</li>";
				}
				
				/* print out the item */
				echo "<li><a href='" . $prefix . $value['link'] . "'" . $target . ">" . $value['title'] . "</a></li>";
				
			} else {
				if (isset($_SESSION['sessionCrewid']) && UID == $_SESSION['systemUID']) {
					
					if( $key != 0 ) {
						echo "<li class='spacer'>&nbsp;</li>";
					}
				
					/* print out the item */
					echo "<li><a href='" . $prefix . $value['link'] . "'" . $target . ">" . $value['title'] . "</a></li>";
					
				}	/* close the if */
			} /* close the if/else logic */

		} /* close the foreach loop */
		
		echo "</ul>";

	} /* close the function */
	
	/* function that builds the mainNav */
	function user( $sessionCrewid ) {
		
		/* get the items from the user's prefs */
		$getPrefs = "SELECT menu1, menu2, menu3, menu4, menu5, menu6, menu7, menu8, menu9, menu10 FROM sms_crew ";
		$getPrefs.= "WHERE crewid = '$sessionCrewid' LIMIT 1";
		$getPrefsResult = mysql_query( $getPrefs );
		$prefs = mysql_fetch_array( $getPrefsResult );
		$prefs = array_unique( $prefs );
		$prefsCount = count($prefs);
		
		if($prefsCount > 0)
		{
		
			/* loop through and build an array of the user's items */
			foreach( $prefs as $key => $value )
			{

				if( substr( $value, 0, 2 ) == "d_" )
				{
					$n_table = "sms_database";
					$n_id = "dbid";
					$n_value = substr_replace( $value, '', 0, 2 );
				}
				else
				{
					$n_table = "sms_menu_items";
					$n_id = "menuid";
					$n_value = $value;
				}
				
				/* get the mainNav items from the DB */
				$getMenu = "SELECT * FROM $n_table WHERE $n_id = '$n_value' LIMIT 1";
				$getMenuResult = mysql_query( $getMenu );
			
				/* loop through whatever comes out of the database */
				while( $fetchMenu = mysql_fetch_array( $getMenuResult ) ) {
					extract( $fetchMenu, EXTR_OVERWRITE );
				
					/* create a multi-dimensional array with the data
						[x] => array
						[x]['title'] => title
						[x]['link'] => link
						[x]['login'] => login
						[x]['linkType'] => link type
						[x]['access'] => menu access
					*/
					
					if( $n_table == "sms_menu_items" )
					{
						$menuArray[] = array(
							'title' => $menuTitle,
							'link' => $menuLink,
							'login' => $menuLogin,
							'linkType' => $menuLinkType,
							'access' => $menuAccess,
							'section' => $menuMainSec
						);
					}
					else
					{
						$menuArray[] = array(
							'title' => $dbTitle,
							'linkType' => $dbType,
							'link' => $dbURL,
							'id' => $dbid,
							'login' => "n",
							'section' => ""
						);
					}
				
				} /* close the while loop */
			
			} /* close the foreach */
			
			/* figure out if the current skin uses a special arrow */
			if(file_exists('skins/' . $this->skin . '/images/arrow.png'))
			{
				$image = 'skins/' . $this->skin . '/images/arrow.png';
			}
			else
			{
				$image = 'images/arrow.png';
			}
			
			/* open the unordered list */
			echo "<ul id='list'>";
				echo "<li><img src='" . $image . "' alt='>>' border='0' />";
					echo "<ul class='hidemenu'>";
		
					/* loop through each key of the array, evaluate it, then spit it out */
					foreach( $menuArray as $key => $value ) {
			
						/* check the link type and then set the prefix and target */
						if( $value['linkType'] == "onsite" || $value['linkType'] == "entry" ) {
							$prefix = WEBLOC;
							$target = "";
						
							if( 
								$value['section'] == "user" && (
									substr( $value['access'], -1, 1 == "1" ) ||
									substr( $value['access'], -1, 1 == "2" ) ||
									substr( $value['access'], -1, 1 == "3" )
								)
							) {
								$crew = "&crew=" . $sessionCrewid;
							} else {
								$crew = "";
							}
						
						} else {
							$prefix = "";
							$target = " target='_blank'";
						}
				
						/* if the item doesn't require a login, display it */
						if( $value['login'] == "n" ) {
							/* print out the item */
							if( $n_table == "sms_menu_items" )
							{
								echo "<li><a href='" . $prefix . $value['link'] . $crew . "'" . $target . ">" . $value['title'] . "</a></li>";
							}
							else
							{
								if( $value['linkType'] == "entry" ) {
									$p = WEBLOC . "index.php?page=database&entry=" . $value['id'];
								} else {
									$p = WEBLOC . $value['link'];
								}
								
								echo "<li><a href='" . $p . "'>" . $value['title'] . "</a></li>";
							}
				
						} else {
							if( isset( $sessionCrewid ) ) {
					
								/* print out the item */
								echo "<li><a href='" . $prefix . $value['link'] . $crew . "'" . $target . ">" . $value['title'] . "</a></li>";
					
							}	/* close the if */
						} /* close the if/else logic */

					} /* close the foreach loop */
		
					echo "</ul>";
				echo "</li>";
		
			/* close the unordered list */
			echo "</ul>";
			
		} /* close the check for an array longer than 0 */

	} /* close the function */
	
	/* function the builds the admin control panel menus */
	function admin( $section, $access, $crewid ) {
		
		if( in_array( $section, $access ) ) {
		
			echo "<span class='fontLarge'><b>";
			echo ucfirst( $section );
			echo "</b></span>";
	
			/* get the mainNav items from the DB */
			$getMenu = "SELECT * FROM sms_menu_items ";
			$getMenu.= "WHERE menuCat = 'admin' AND menuMainSec = '$section' ";
			$getMenu.= "AND menuAvailability = 'on' ORDER BY menuGroup, menuOrder ASC";
			$getMenuResult = mysql_query( $getMenu );
			
			/* loop through whatever comes out of the database */
			while( $fetchMenu = mysql_fetch_array( $getMenuResult ) ) {
				extract( $fetchMenu, EXTR_OVERWRITE );
				
				/* create a multi-dimensional array with the data
					[x] => array
					[x]['title'] => title
					[x]['link'] => link
					[x]['linkType'] => link type
					[x]['group'] => group
					[x]['access'] => access
				*/
				$menuArray[] = array(
					'title' => $menuTitle,
					'link' => $menuLink,
					'linkType' => $menuLinkType,
					'group' => $menuGroup,
					'access' => $menuAccess
				);
				
				/*
					make sure that the group array is set up to only to
					add items to the array if the user has access to something
					within that group
				*/
				if( in_array( $menuAccess, $access ) ) {
					
					/* fixes a PHP warning */
					if( !isset( $groupArray ) ) {
						$groupArray = "";
					}
					
					/* set up the group array */
					if( !is_array( $groupArray ) ) {
						$groupArray = array( $menuGroup );
					} elseif( is_array( $groupArray ) && !in_array( $menuGroup, $groupArray ) ) {
						$groupArray[] = $menuGroup;
					}
					
				} /* close the item->group array check */
				
			} /* close the while loop */
			
			/* open the unordered list */
			echo "<ul>";
	
			/* loop through each key of the groups array */
			foreach( $groupArray as $key2 => $value2 ) {
				
				/* if it isn't the first group, spit out the separation gap */
				if( $key2 != 0 ) {
					echo "<li class='spacer'>&nbsp;</li>";
				}
			
				/* loop through each key of the array, evaluate it, then spit it out */
				foreach( $menuArray as $key => $value ) {
					
					/* make sure the group of the first foreach matches the group in this loop */
					if( $value2 == $value['group'] ) {
					
						/* check the link type and then set the prefix and target */
						if( $value['linkType'] == "onsite" ) {
							$prefix = WEBLOC;
							$target = "";
							
							if( 
								$section == "user" && (
									substr( $value['access'], -1, 1 == "1" ) ||
									substr( $value['access'], -1, 1 == "2" ) ||
									substr( $value['access'], -1, 1 == "3" )
								)
							) {
								$crew = "&crew=" . $crewid;
							} else {
								$crew = "";
							}
							
						} else {
							$prefix = "";
							$target = " target='_blank'";
						}
						
						/* if the item doesn't require a login, display it */
						if( in_array( $value['access'], $access ) ) {
							
							/* print out the item */
							echo "<li><a href='" . $prefix . $value['link'] . $crew . "'" . $target . ">" . $value['title'] . "</a></li>";
							
						}
					
					} /* close the check to see if they're in the same group */
		
				} /* close the menu array foreach loop */
				
			} /* close the group array foreach loop */
			
			/* close the unordered list */
			echo "</ul>";
			
		} /* close the access to the section check */
		
	} /* close the function */
	
	/* function that creates the menus for the main, ship, simm, and personnel sections */
	function general( $class ) {
			
		echo "<span class='fontLarge'><b>";
		
		/* setup the menu headers */
		switch( $class ) {
		case "main":
			echo "Main";
			break;
		case "personnel":
			echo "Personnel";
			break;
		case "ship":
			echo "The Ship";
			break;
		case "starbase":
			echo "The Starbase";
			break;
		case "simm":
			echo "The Simm";
			break;
		}
		
		echo "</b></span>";
		
		/* change the class to something the script can use */
		if( $class == "starbase" ) {
			$cat = "ship";
		} else {
			$cat = $class;
		}

		/* get the mainNav items from the DB */
		$getMenu = "SELECT * FROM sms_menu_items ";
		$getMenu.= "WHERE menuCat = 'general' AND menuMainSec = '$cat' ";
		$getMenu.= "AND menuAvailability = 'on' ORDER BY menuGroup, menuOrder ASC";
		$getMenuResult = mysql_query( $getMenu );
		
		/* loop through whatever comes out of the database */
		while( $fetchMenu = mysql_fetch_array( $getMenuResult ) ) {
			extract( $fetchMenu, EXTR_OVERWRITE );
			
			/* create a multi-dimensional array with the data
				[x] => array
				[x]['title'] => title
				[x]['link'] => link
				[x]['login'] => login
				[x]['linkType'] => link type
				[x]['group'] => group
			*/
			$menuArray[] = array(
				'title' => $menuTitle,
				'link' => $menuLink,
				'login' => $menuLogin,
				'linkType' => $menuLinkType,
				'group' => $menuGroup
			);
			
			/* fixes a PHP warning */
			if( !isset( $groupArray ) ) {
				$groupArray = "";
			}
			
			/* set up the group array */
			if( !is_array( $groupArray ) ) {
				$groupArray = array( $menuGroup );
			} elseif( is_array( $groupArray ) && !in_array( $menuGroup, $groupArray ) ) {
				$groupArray[] = $menuGroup;
			}
			
		}
		
		/* open the unordered list */
		echo "<ul>";
		
		/* loop through each key of the groups array */
		foreach( $groupArray as $key2 => $value2 ) {
			
			/* if it isn't the first group, spit out the separation gap */
			if( $key2 != 0 ) {
				echo "<li class='spacer'>&nbsp;</li>";
			}
		
			/* loop through each key of the array, evaluate it, then spit it out */
			foreach( $menuArray as $key => $value ) {
				
				/* make sure the group of the first foreach matches the group in this loop */
				if( $value2 == $value['group'] ) {
				
					/* check the link type and then set the prefix and target */
					if( $value['linkType'] == "onsite" ) {
						$prefix = WEBLOC;
						$target = "";
					} else {
						$prefix = "";
						$target = " target='_blank'";
					}
					
					/* if the item doesn't require a login, display it */
					if( $value['login'] == "n" ) {
						
						/* print out the item */
						echo "<li><a href='" . $prefix . $value['link'] . "'" . $target . ">" . $value['title'] . "</a></li>";
						
					} else {
						if (isset($_SESSION['sessionCrewid']))
						{
							/* print out the item */
							echo "<li><a href='" . $prefix . $value['link'] . "'" . $target . ">" . $value['title'] . "</a></li>";
						}	/* close the if */
					} /* close the if/else logic */
				
				} /* close the check to see if they're in the same group */
	
			} /* close the menu array foreach loop */
			
		} /* close the group array foreach loop */
		
		/* close the unordered list */
		echo "</ul>";

	} /* close the function */
	
} /* close the class */

?>