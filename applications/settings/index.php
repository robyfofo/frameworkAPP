<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * settings/index.php v.1.0.0. 11/09/2018
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
	

switch(substr(Core::$request->method,-4,4)) {
	case 'Ivaa':
		$App->sessionName .= '-iva';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module(Core::$request->action,'');
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/iva.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/iva.php");
	break;	
	
	default:
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/moment/moment-with-locales.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>';			
		$App->sessionName .= '-purchase';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','order'=>'dateins DESC'));
		if (isset($App->params->tables['item'])) $Module = new Module(Core::$request->action,$App->params->tables['item']);
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/items.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/items.php");
		$App->defaultJavascript = "messages['inserisci articolo'] = '".addslashes(ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['inserisci %ITEM%'])))."';".PHP_EOL;
		$App->defaultJavascript .= "messages['inserisci testo articolo'] = '".addslashes(ucfirst(Config::$localStrings['inserisci testo articolo']))."';".PHP_EOL;
		$App->defaultJavascript .= "messages['modifica articolo'] = '".addslashes(ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['modifica %ITEM%'])))."';".PHP_EOL;
		$App->defaultJavascript .= "messages['modifica'] = '".addslashes(ucfirst(Config::$localStrings['modifica']))."';".PHP_EOL;
		$App->defaultJavascript .= "var defDateins = '".$App->item->dateins."';".PHP_EOL;
		$App->defaultJavascript .= "var defDatesca = '".$App->item->datesca."';";
		$App->defaultJavascript .= "var module = '".Core::$request->action."';";
		$App->defaultJavascript .= "var defTax = '".$App->company->iva."';";
		
		/* aggiorna config con dati company */
		$App->params->fields['itep']['rivalsa']['defValue'] = $App->company->rivalsa;	
	break;
	}
?>