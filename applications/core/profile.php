<?php
/* admin/core/profile.php v.7.0.0. 04/02/2022 */

//Sql::setDebugMode(1);

include_once(PATH.$App->pathApplicationsCore."class.module.php");
$Module = new Module(Config::$dbTablePrefix."users");

/* variabili ambiente */
$App->codeVersion = ' 3.5.4.';
$App->pageTitle = ucfirst(Core::$localStrings['profilo']);
$App->pageSubTitle = preg_replace('/%ITEM%/', Core::$localStrings['profilo'], Core::$localStrings['modifica il %ITEM%']);
$App->breadcrumb[] = '<li class="active"><i class="icon-user"></i> '.Core::$localStrings['profilo'].'</li>';
$App->templateApp = Core::$request->action.'.html';

if (isset($_POST['id'])) $App->id = intval($_POST['id']);
$App->coreModule = true;

$App->params = new stdClass();
unset(Config::$DatabaseTablesFields['users']['password']);
unset(Config::$DatabaseTablesFields['users']['username']);
unset(Config::$DatabaseTablesFields['users']['levels_id']);
unset(Config::$DatabaseTablesFields['users']['hash']);
unset(Config::$DatabaseTablesFields['users']['is_root']);
unset(Config::$DatabaseTablesFields['users']['in_admin']);
unset(Config::$DatabaseTablesFields['users']['from_site']);
unset(Config::$DatabaseTablesFields['users']['active']);

$App->fieldsName = 'users';

$App->id = (isset($App->userLoggedData->id) && $App->userLoggedData->id > 0 ? $App->userLoggedData->id : 0);

//echo '<br>id: '.$App->id;

$App->province = new stdClass;
Sql::initQuery(Config::$DatabaseTables['location_province']['name'],array('*'),array(),'active = 1','nome ASC');
$App->province = Sql::getRecords();
if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

$App->nations = new stdClass;
Sql::initQuery(Config::$DatabaseTables['location_nations']['name'],array('*'),array(),'active = 1','title_'.Core::$localStrings['user'].' ASC');
$App->nations = Sql::getRecords();
if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

$App->comuni = new stdClass;
Sql::initQuery(Config::$DatabaseTables['location_comuni']['name'],array('*'),array(),'active = 1');
Sql::setOptions(array('fieldTokeyObj'=>'id'));
$App->comuni = Sql::getRecords();
if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
                                 
switch(Core::$request->method) {
	case 'update':
		if (!$_POST) { ToolsStrings::redirect(URL_SITE.'error/404'); 	}
		if ($App->id == 0) { ToolsStrings::redirect(URL_SITE.'error/404'); }
		Permissions::checkCsrftoken();
		//ToolsStrings::dump($_POST);


		if (!isset($_POST['location_comuni_id']) || (isset($_POST['location_comuni_id']) && $_POST['location_comuni_id'] == '')) $_POST['location_comuni_id'] = 0;
		if (!isset($_POST['location_province_id']) || (isset($_POST['location_province_id']) && $_POST['location_province_id'] == '')) $_POST['location_province_id'] = 0;
		if (!isset($_POST['location_nations_id']) || (isset($_POST['location_nations_id']) && $_POST['location_nations_id'] == '')) $_POST['location_nations_id'] = 0;

		if ( isset($_POST['location_comuni_id']) && $_POST['location_comuni_id'] > 0 ) $_POST['comune_alt'] = '';
		if ( isset($_POST['location_province_id']) && $_POST['location_province_id'] > 0 ) $_POST['provincia_alt'] = '';
		
		if ($_POST['location_comuni_id'] == 0 && $_POST['comune_alt'] == '') {
			$_SESSION['message'] = '1|'.preg_replace('/%ITEM%/',Core::$localStrings['comune'],Config::$localStrings['Devi inserire un %ITEM%!']);
			ToolsStrings::redirect(URL_SITE.Core::$request->action);
			die();
		}
		if ($_POST['location_province_id'] == 0 && $_POST['provincia_alt'] == '') {
			$_SESSION['message'] = '1|'.preg_replace('/%ITEM%/',Core::$localStrings['provincia'],Config::$localStrings['Devi inserire una %ITEM%!']);
			ToolsStrings::redirect(URL_SITE.Core::$request->action);
			die();
		}
		if ($_POST['location_nations_id'] == 0) {
			$_SESSION['message'] = '1|'.preg_replace('/%ITEM%/',Core::$localStrings['nazione'],Config::$localStrings['Devi inserire una %ITEM%!']);
			ToolsStrings::redirect(URL_SITE.Core::$request->action);
			die();
		}					
				
		// recupero dati avatar */
		list($_POST['avatar'],$_POST['avatar_info']) = $Module->getAvatarData($_POST['id'],Core::$localStrings);
		if ($Module->errorType > 0) 
		{
			$_SESSION[$App->sessionName]['formTabActive'] = 4;
			$_SESSION['message'] = 	$Module->errorType.'|'.$Module->message;
			ToolsStrings::redirect(URL_SITE.Core::$request->action);
		}

		// parsa i post in base ai campi
		Form::parsePostByFields(Config::$DatabaseTablesFields['users'],Core::$localStrings,array());
		if (Core::$resultOp->error > 0) {
			$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			ToolsStrings::redirect(URL_SITE.Core::$request->action);
		}
		
		Sql::updateRawlyPost(Config::$DatabaseTablesFields['users'],Config::$DatabaseTables['users']['name'],'id',$App->id);
		if (Core::$resultOp->error > 0) { die('errore modifica record');ToolsStrings::redirect(URL_SITE.'error/db'); }
					
		$foo = Core::$localStrings['Account modificato correttamente! Per rendere effettive le modifiche devi uscire dal sistema e loggarti nuovamente.'];
		$_SESSION['message'] = '0|'.$foo;
		ToolsStrings::redirect(URL_SITE.Core::$request->action);

	break;

	default;
		$App->item = new stdClass;
		Sql::initQuery(Config::$DatabaseTables['users']['name'],array('*'),array($App->id),"id = ?");
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$App->templatesAvaiable = $Module->getUserTemplatesArray();
		if ($Module->error == 1) {	
			$_SESSION['message'] = '1|'.$Module->message;
			ToolsStrings::redirect(URL_SITE.Core::$request->action);
		}
		
		$App->selected = new stdClass;
		$App->selected->location_nations_id = $App->item->location_nations_id;
		$App->selected->location_province_id = $App->item->location_province_id;
		$App->selected->location_comuni_id = 0;
		$App->selected->location_comuni_nome = ucfirst(Config::$localStrings['altro comune']);
		if (isset($App->item->location_comuni_id) && $App->item->location_comuni_id > 0) {
			$App->selected->location_comuni_id = $App->item->location_comuni_id;
			$App->selected->location_comuni_nome = $App->comuni[$App->item->location_comuni_id]->nome;
		}
		$App->default = new stdClass;
		$App->default->provincia_alt = $App->item->provincia_alt;
		$App->default->comune_alt = $App->item->comune_alt;
		
	break;	
}
	
$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">';
$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/js/bootstrap-select.min.js"></script>';
$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/ajax-bootstrap-select/css/ajax-bootstrap-select.min.css" rel="stylesheet">';
$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/ajax-bootstrap-select/js/ajax-bootstrap-select.min.js"></script>';
$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/ajax-bootstrap-select/js/locale/ajax-bootstrap-select.'.Core::$localStrings['charset'].'.min.js"></script>';

$App->defaultJavascript = "
messages['Devi inserire una provincia!'] = '".preg_replace('/%ITEM%/',Config::$localStrings['provincia'],Config::$localStrings['Devi inserire una %ITEM%!'])."';
messages['Devi inserire un comune!'] = '".preg_replace('/%ITEM%/',Config::$localStrings['comune'],Config::$localStrings['Devi inserire un %ITEM%!'])."';

let selected_location_nations_id = '".intval($App->selected->location_nations_id)."';
let selected_location_province_id = '".intval($App->selected->location_province_id)."';
let selected_location_comuni_id = '".intval($App->selected->location_comuni_id)."';

let default_provincia_alt = '".addslashes($App->default->provincia_alt)."';
let default_comune_alt = '".addslashes($App->default->comune_alt)."';
";



$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplicationsCore.'templates/'.$App->templateUser.'/js/profile.js"></script>';
?>