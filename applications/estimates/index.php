<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * estimetes/index.php v.1.3.0. 07/09/2020
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

if (!isset($_SESSION[$App->sessionName]['formTabActive'])) $_SESSION[$App->sessionName]['formTabActive'] = 1;

$App->patchdatapicker = 1;
$App->defaultJavascript = "";

// preleva company
Sql::initQuery($App->params->tables['comp'],array('*'),array(),'id = 1');
$App->company = Sql::getRecord();
if (Core::$resultOp->error > 0) die('Error read company');

switch(substr(Core::$request->method,-6,6)) {
	case 'ExpPdf':
		$App->sessionName .= '-pdf';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module(Core::$request->action,'');
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/export-pdf.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/export-pdf.php");
	break;

	default:
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>';

		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','order'=>'dateins DESC'));
		if (isset($App->params->tables['item'])) $Module = new Module(Core::$request->action,$App->params->tables['item']);
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/estimates.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/estimates.php");

		// aggiorna config con dati company */
		$App->params->fields['itep']['rivalsa']['defValue'] = $App->company->rivalsa;
	break;
}

$App->defaultJavascript .= "messages['inserisci articolo'] = '".addslashes(ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['inserisci %ITEM%'])))."';".PHP_EOL;
$App->defaultJavascript .= "messages['inserisci testo articolo'] = '".addslashes(ucfirst(Config::$localStrings['inserisci testo articolo']))."';".PHP_EOL;
$App->defaultJavascript .= "messages['modifica articolo'] = '".addslashes(ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['modifica %ITEM%'])))."';".PHP_EOL;
$App->defaultJavascript .= "messages['modifica'] = '".addslashes(ucfirst(Config::$localStrings['modifica']))."';".PHP_EOL;
$App->defaultJavascript .= "var defDateins = '".$App->item->dateins."';".PHP_EOL;
$App->defaultJavascript .= "var defDatesca = '".$App->item->datesca."';";
$App->defaultJavascript .= "var module = '".Core::$request->action."';";
$App->defaultJavascript .= "var defTax = '".$App->company->iva."';";
?>
