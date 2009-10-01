<?php

class MenuOverride extends Menu
{
	var $skin;
	var $pages = array(
		'main' => array('contact', 'credits', 'join', 'main', 'news'),
		'personnel' => array('bio', 'coc', 'manifest', 'userpostlist'),
		'simm' => array(
			'crewawards', 'loglist', 'mission', 'missions', 'post',
			'postlist','rules', 'simm', 'statistics', 'summaries'),
		'ship' => array('decklisting', 'departments', 'history', 'ship', 'specifications', 'tour'),
		'admin' => array('post', 'reports', 'manage', 'user'),
		'database' => array('database')
	);
	
	function main()
	{
		$output = FALSE;
		
		/* get the mainNav items from the DB */
		$getMenu = "SELECT * FROM sms_menu_items WHERE menuCat = 'main' ";
		$getMenu.= "AND menuAvailability = 'on' ORDER BY menuGroup, menuOrder ASC";
		$getMenuResult = mysql_query( $getMenu );
		
		/* loop through whatever comes out of the database */
		while($fetchMenu = mysql_fetch_assoc($getMenuResult)) {
			extract($fetchMenu, EXTR_OVERWRITE);
			
			/* create a multi-dimensional array with the data
				[x] => array
				[x]['title'] => title
				[x]['link'] => link
				[x]['login'] => login
				[x]['linkType'] => link type
			*/
			$menuArray[] = array(
				'title'		=> $fetchMenu['menuTitle'],
				'link'		=> $fetchMenu['menuLink'],
				'login'		=> $fetchMenu['menuLogin'],
				'linkType'	=> $fetchMenu['menuLinkType'],
				'section'	=> $fetchMenu['menuMainSec']
			);
		}
		
		$name_raw = explode('/', $_SERVER['SCRIPT_NAME']);
		$name = end($name_raw);
		
		$page = (isset($_GET['page'])) ? $_GET['page'] : 'main';
		
		$server = explode('/', $_SERVER['PHP_SELF']);
		$count = count($server);
		$k = $count - 1;
		
		$ext = $server[$k];
		
		$sections = array('main', 'personnel', 'simm', 'ship');
		
		echo "<ul class='sf-menu' id='nav-main'>\n";
		
		foreach ($menuArray as $key => $value)
		{
			if (!in_array($value['section'], $sections))
			{
				if ($value['linkType'] == "onsite")
				{
					$link = WEBLOC . $value['link'];
					$target = "";
				}
				else
				{
					$link = $value['link'];
					$target = " target='_blank'";
				}
			}
			else
			{
				$link = '#';
				$target = '';
			}
			
			$active = ($this->_page_check($page) == $value['section'] && $name == 'index.php') ? ' class="active"' : FALSE;
				
			if ($value['login'] == "n")
			{
				echo "<li>";
				echo "<a id='". $value['section'] ."' href='" . $link . "'" . $target . $active .">". $value['title'];
				
				if (in_array($value['section'], $sections))
				{
					echo " <img src='skins/sojourner/images/nav-arrow.png' alt='' border='0' /></a>";
					$this->general($value['section']);
				}
				else
				{
					echo "</a>";
				}
				
				echo "</li>\n";
			}
			else
			{
				if (isset($_SESSION['sessionCrewid']) && UID == $_SESSION['systemUID'])
				{
					echo "<li><a href='" . $link . "'" . $target . $active .">" . $value['title'] . "</a></li>";
				}
			}
		}
		
		echo "</ul>\n";
	}
	
	function general($class)
	{	
		$cat = ($class == 'starbase') ? 'ship' : $class;

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
				'group' => $menuGroup,
				'section' => $menuMainSec
			);
			
			if (!isset($groupArray))
			{
				$groupArray = "";
			}
			
			if (!is_array($groupArray))
			{
				$groupArray = array( $menuGroup );
			}
			elseif (is_array($groupArray) && !in_array($menuGroup, $groupArray))
			{
				$groupArray[] = $menuGroup;
			}	
		}
		
		echo "<ul>";
		
		foreach ($groupArray as $key2 => $value2)
		{
			if ($key2 != 0)
			{
				echo "<li class='spacer'>&nbsp;</li>";
			}
		
			foreach ($menuArray as $key => $value)
			{
				if ($value2 == $value['group'])
				{
					if ($value['linkType'] == "onsite")
					{
						$prefix = WEBLOC;
						$target = "";
					}
					else
					{
						$prefix = "";
						$target = " target='_blank'";
					}
					
					if ($value['login'] == "n")
					{
						echo "<li><a class='". $value['section'] ."' href='" . $prefix . $value['link'] . "'" . $target . ">" . $value['title'] . "</a></li>";
					}
					else
					{
						if (isset($sessionCrewid))
						{
							echo "<li><a href='" . $prefix . $value['link'] . "'" . $target . ">" . $value['title'] . "</a></li>";	
						}
					}
				}	
			}
		}
		
		echo "</ul>";
	}
	
	function user($sessionCrewid)
	{
		/* get the items from the user's prefs */
		$getPrefs = "SELECT menu1, menu2, menu3, menu4, menu5, menu6, menu7, menu8, menu9, menu10 FROM sms_crew ";
		$getPrefs.= "WHERE crewid = '$sessionCrewid' LIMIT 1";
		$getPrefsResult = mysql_query( $getPrefs );
		$prefs = mysql_fetch_array( $getPrefsResult );
		$prefs = array_unique( $prefs );
		$prefsCount = count($prefs);
		
		if($prefsCount > 0)
		{
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
				}
			}
			
			echo "<ul class='none'>";
		
			foreach( $menuArray as $key => $value )
			{
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
		
				if( $value['login'] == "n" )
				{
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
						echo "<li><a href='" . $prefix . $value['link'] . $crew . "'" . $target . ">" . $value['title'] . "</a></li>";
					}
				}
			}
			
			echo "</ul>";	
		}
	}
	
	function _page_check($page = '')
	{
		foreach ($this->pages as $key => $value)
		{
			$search = array_search($page, $value);
			
			if ($search !== FALSE)
			{
				return $key;
			}
		}
		
		return FALSE;
	}
	
}

?>