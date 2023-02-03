<?php
/* app/app.end.php v.3.5.5. 14/05/2019 */



/* DIV MESSAGGI SISTEMA */


/* prende il messaggio */
if (isset($_MY_SESSION_VARS['message']) && $_MY_SESSION_VARS['message'] != '') {
	$mess = explode('|',$_MY_SESSION_VARS['message']);
	$_MY_SESSION_VARS = $my_session->my_session_unsetVar('message');
}
if (isset($_SESSION['message']) && $_SESSION['message'] != '') {
	$mess = explode('|',$_SESSION['message']);
	unset($_SESSION['message']);
}
if (isset($mess[0])) Core::$resultOp->error = $mess[0];
if (isset($mess[1])) Core::$resultOp->message =$mess[1];
$App->systemMessages = '';
$appErrors = Utilities::getMessagesCore(Core::$resultOp);
list($show,$error,$type,$content) = $appErrors;
if ($show == true) {
	if ($type == 0 && $error > 0) $type = $error;
	$App->systemMessages .= '<div id="systemMessageID" class="alert';
	if ($type == 2) $App->systemMessages .= ' alert-warning';
	if ($type == 1) $App->systemMessages .= ' alert-danger';
	if ($type == 0) $App->systemMessages .= ' alert-success';
	if ($type > 2) $App->systemMessages .= ' alert-danger';
	$App->systemMessages .= '">'.$content.'</div>';
}
/* DIV MESSAGGI SISTEMA */

/* MENU */


//ToolsStrings::dump(Config::$modules);


$App->rightCodeMenu = '';
foreach(Config::$modules AS $sectionKey=>$sectionModules) {
	$x1 = 0;
	foreach($sectionModules AS $module) {
		
		if (Permissions::checkIfModulesIsReadable($module->name,$App->userLoggedData) === true) {
			
			//print_r($module);
			
			$outputMenu = '';
			
			$menu = json_decode($module->code_menu) or die('Errore nel campo menu. Formato Json non valido!'.$module->code_menu);	
			
			//print_r($menu);
			
			$havesubmenu = 0;
			if (isset($menu->submenus) && count($menu->submenus)) $havesubmenu = 1;
			
			$classLiMain = ' class="nav-item"';
			if (isset($App->breadcrumb[1]['name']) && $App->breadcrumb[1]['name'] == $module->name) $classLiMain = ' class="nav-item active"'; 
			
			$auth = 0;
			if (isset($menu->auth) && $menu->auth == "1") $auth = 1; 
			
			if ($auth == 0) {
				
				$outputUlSubmenu = '';
				$outputLiSubmenu = '';

				$moduleName = (isset($module->name) ? $module->name : '');
				$moduleLabel = (isset($module->label) ? $module->label : '');
				$menuName = (isset($menu->name) ? $menu->name : '');
				$menuIcon = (isset($menu->icon) ? $menu->icon : '');
				$menuAction = (isset($menu->action) ? $menu->action : '');
				$menuLabel = (isset($menu->label) ? $menu->label : '');				
				$havesubmenu = 0;			
				$collapsed = ' collapsed';		

				// crea sub menu
				$divSubmenuClass = 'collapse';
				$divSubmenuData = ' aria-labelledby="heading'.$moduleName.'" data-parent="#accordionSidebar"';
					
				if (isset($menu->submenus) && is_array($menu->submenus) && count($menu->submenus) > 0) {
					
					$havesubmenu = 1;
					// crea li sub menu				
					foreach ($menu->submenus AS $submenu) {
						//print_r($submenu);
						$havesubmenu1 = 0;
						if (isset($submenu->submenus) && count($submenu->submenus)) $havesubmenu1 = 1;
						
						$submanuClass = '';
						if (isset( $App->breadcrumb[2]['name']) && $App->breadcrumb[2]['name'] == $submenu->name) $submanuClass = ' class="active"'; 
												
						$subauth = 0;
						if (isset($submenu->auth) && $submenu->auth == "1") $subauth = 1; 						
						if ($subauth == 0) {
							
							$submenuUrl = URL_SITE;							
							$submanuLabel = $submenu->label;
							if (isset($localStrings[$submanuLabel])) $submanuLabel = $localStrings[$submanuLabel];								
							$submenuName = (isset($submenu->name) ? $submenu->name : '');
							$submenuIcon = (isset($submenu->icon) ? $submenu->icon : '');
							$submenuAction = (isset($submenu->action) ? $submenu->action : '');
							
							$submenuUrl .= $moduleName.'/'.$submenuName;
							
							if ($submenuAction == Core::$request->action) {
								$divSubmenuClass = 'collapse show';
								$collapsed = '';
							}
							
							$submanuClass = 'collapse-item';	
							$outputLiSubmenu .= '<a class="'.$submanuClass.'" href="'.$submenuUrl.'">'.$submenuIcon.' '.$submanuLabel.($havesubmenu1 == 1 ? '<span class="fa arrow"></span>' : '').'</a>'.PHP_EOL;			
							
						}	
					}
		
					
					
					$outputUlSubmenu .= '<div id="collapse'.$moduleName.'" class="'.$divSubmenuClass.'"'.$divSubmenuData.'><div class="bg-white py-2 collapse-inner rounded">'.$outputLiSubmenu.'</div></div>';
				}
				
				$liMainClass = 'nav-item';
				$liMainData = '';
				$liMainHrefClass = 'nav-link';	
				$liMainHrefData = '';	
				
				if ($havesubmenu == 1) {
					$liMainHrefClass .= $collapsed;
					$liMainHrefData = ' data-toggle="collapse" data-target="#collapse'.$moduleName.'" aria-expanded="true" aria-controls="collapse'.$moduleName.'"';
				}
	
				// crea il li principale
				$outputMenu = '<li class="'.$liMainClass.'"'.$liMainData.'>';PHP_EOL;
				$outputMenu .= '<a class="'.$liMainHrefClass.'"'.$liMainHrefData.' href="'.URL_SITE.$moduleName.'">'.$menuIcon.' <span>'.$menuLabel.'</span>'.($havesubmenu == 1 ? '<span class="fa arrow"></span>' : '').'</a>';
				
				$outputMenu .= $outputUlSubmenu;
				
				$outputMenu .= '</li>'.PHP_EOL;	
				
				// sostituiso il modulename con la localizzazione se esiste
				if (isset($localStrings[$moduleLabel])) $moduleLabel = $localStrings[$moduleLabel];	
				$outputMenu = preg_replace('/%LABEL%/',$moduleLabel,$outputMenu);
				$outputMenu = preg_replace('/%NAME%/',$moduleLabel,$outputMenu);	
								
				$App->rightCodeMenu .= $outputMenu;		
			
				$x1++;	
				
			}			
			
		}
		
	}
	if ($x1 > 0) $App->rightCodeMenu .= '';		
}

?>