<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * timecard/index.php v.1.3.0. 27/08/2020
*/

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".Config::$localStrings['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/config.inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");
$App->includeJscriptPHPTop = Core::$request->action."/templates/".$App->templateUser."/js/script.js.php";

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb .= $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

/* trova tutti i progetti */
$App->progetti = new stdClass;
Sql::initQuery($App->params->tables['prog'],array('*'),array(),'active = 1','current DESC');
$App->progetti = Sql::getRecords();

$App->currentProjectId = 0;
/* trova il progetto corrente */
$App->currentProject = new stdClass;
Sql::initQuery($App->params->tables['prog'],array('*'),array(),'current = 1');
$App->currentProject = Sql::getRecord();

/* imposta il progetto predefinito */
if (isset($App->currentProject->id)) $App->currentProjectId = $App->currentProject->id;

/* imposta il progetto della sessione */
if (!isset($_MY_SESSION_VARS[$App->sessionName]['id_project'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_project',$App->currentProjectId);


switch(substr(Core::$request->method,-4,4)) {
	case 'Spdf':
		$App->sessionNameAite = $App->sessionName.'-aite';
		$App->sessionNamePite = $App->sessionName.'-pite';
		$App->sessionName .= '-pdf';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module(Core::$request->action,'');
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/export-pdf.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/export-pdf.php");
	break;

	case 'Aite':
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">';
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formAitems.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>';
		$App->sessionName = $App->sessionName.'-aite';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','order'=>'datains DESC'));
		$Module = new Module($App->sessionName,$App->params->tables['item']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/aitems.php");
		//$App->defaultJavascript = "defaultTimeIni = '".$App->timeIniTimecard."';";
		//$App->defaultJavascript .= "defaultTimeEnd = '".$App->timeEndTimecard."';";

		if (!isset($_SESSION[$App->sessionName]['dataini'])) $_SESSION[$App->sessionName]['dataini'] = Config::$nowDate;
		if (!isset($_SESSION[$App->sessionName]['dataend'])) $_SESSION[$App->sessionName]['dataend'] = Config::$nowDate;

		$App->defaultJavascript = "let defaultDataini = '".$_SESSION[$App->sessionName]['dataini']."';";
		$App->defaultJavascript .= "let defaultDataend = '".$_SESSION[$App->sessionName]['dataend']."';";
		$App->defaultJavascript .= "messages['Intervallo tra le due date errato!'] = '".ucfirst(Config::$localStrings['Intervallo tra le due date errato!'])."';";


	break;
	case 'Pite':
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">';
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/pitems.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>';
		$App->sessionName = $App->sessionName.'-pite';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10'));
		$Module = new Module($App->sessionName,$App->params->tables['item']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/pitems.php");
		$App->defaultJavascript = "defaultTimeIni = '".$App->timeIniTimecard."';";
		$App->defaultJavascript .= "defaultTimeEnd = '".$App->timeEndTimecard."';";
	break;

	default:
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">';
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formItem.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10'));
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['data-timecard'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'data-timecard',Config::$nowDate);
		$Module = new Module($App->sessionName,$App->params->tables['item']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/items.php");
		$App->defaultJavascript = "messages['Devi selezionare un progetto'] = '".preg_replace('/%ITEM%/',Config::$localStrings['progetto'],Config::$localStrings['Devi selezionare un %ITEM%!'])."';";
		$App->defaultJavascript .= "messages['intervallo di tempo si sovrappone ad un altro inserito nella stessa data'] = '".ucfirst(Config::$localStrings['Intervallo di tempo si sovrappone ad un altro inserito nella stessa data!'])."';";
		$App->defaultJavascript .= "defaultappdata = '".$_MY_SESSION_VARS[$App->sessionName]['data-timecard']."';";
		$App->defaultJavascript .= "defaultdata = '".$App->defaultFormData."';";
		$App->defaultJavascript .= "defaultdata1 = '".$App->defaultFormData1."';";
		$App->defaultJavascript .= "defaultTimeIni = '".$App->timeIniTimecard."';";
		$App->defaultJavascript .= "defaultTimeEnd = '".$App->timeEndTimecard."';";
	break;
	}

//print_r($_SESSION);
?>
