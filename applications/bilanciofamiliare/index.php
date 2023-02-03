<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * bilancio_familiare/index.php v.1.0.1. 17/05/2019
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
	
$App->patchdatapicker = 1;

$App->annocorrente = date('Y');
$App->annoprecedente = date('Y',strtotime("-1 year"));


switch(substr(Core::$request->method,-4,4)) {
	case 'Entr':
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>';			
		$App->sessionName .= '-entries';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','order'=>'dateins DESC'));
		$Module = new Module(Core::$request->action,$App->params->tables['items']);
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/entries.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/entries.php");
		$App->defaultJavascript = "var defDateins = '".$App->item->dateins."';".PHP_EOL;
		$App->defaultJavascript .= "var module = '".Core::$request->action."';";
	break;
	case 'Outp':
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>';			
		$App->sessionName .= '-outputs';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','order'=>'dateins DESC'));
		$Module = new Module(Core::$request->action,$App->params->tables['items']);
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/outputs.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/outputs.php");
		$App->defaultJavascript = "var defDateins = '".$App->item->dateins."';".PHP_EOL;
		$App->defaultJavascript .= "var module = '".Core::$request->action."';";
	break;	
	case 'Item':
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>';			
		$App->sessionName .= '-items';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','order'=>'dateins DESC'));
		$Module = new Module(Core::$request->action,$App->params->tables['items']);
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/items.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/items.php");
		$App->defaultJavascript = "var defDateins = '".$App->item->dateins."';".PHP_EOL;
		$App->defaultJavascript .= "var module = '".Core::$request->action."';";
	break;	
	}
?>