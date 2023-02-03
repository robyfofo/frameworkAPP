<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * invoices/index.php v.1.2.0. 09/07/2020
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

$App->defaultJavascript = "";

/* preleva company */
Sql::initQuery($App->params->tables['comp'],array('*'),array(),'id = 1');
$App->company = Sql::getRecord();
if (Core::$resultOp->error > 0) die('Error read company');

switch(substr(Core::$request->method,-6,6)) {
	case 'AjaxSr':
		$App->sessionName .= '-ajax';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module(Core::$request->action,'');
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/ajax.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/ajax.php");
	break;	

	case 'ExpPdf':
		$App->sessionName .= '-pdf';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>''));
		$Module = new Module(Core::$request->action,'');
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/export-pdf.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/export-pdf.php");
	break;	
	case 'InvSal':
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>';			
		$App->sessionName .= '-sale';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','order'=>'dateins DESC'));
		if (isset($App->params->tables['InvSal'])) $Module = new Module(Core::$request->action,$App->params->tables['InvSal']);
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/invoice-sale.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/invoice-sale.php");
	break;	
	
	case 'InvPur':
	default:
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/moment/js/moment-with-locales.min.js"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>';			
		$App->sessionName .= '-purchase';
		if (!isset($_MY_SESSION_VARS[$App->sessionName]['page'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleVars($_MY_SESSION_VARS,$App->sessionName,array('page'=>1,'ifp'=>'10','srcTab'=>'','order'=>'dateins DESC'));
		if (isset($App->params->tables['InvPur'])) $Module = new Module(Core::$request->action,$App->params->tables['InvPur']);
		if (file_exists(PATH.$App->pathApplications.Core::$request->action."/invoice-purchase.php")) include_once(PATH.$App->pathApplications.Core::$request->action."/invoice-purchase.php");
		$App->defaultJavascript .= "var defTax = '".$App->company->iva."';";
		
		/* aggiorna config con dati company */
		$App->params->fields['itep']['rivalsa']['defValue'] = $App->company->rivalsa;	
	break;
	}
	
$App->defaultJavascript .= "messages['inserisci articolo'] = '".addslashes(ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['inserisci %ITEM%'])))."';".PHP_EOL;
$App->defaultJavascript .= "messages['inserisci testo articolo'] = '".addslashes(ucfirst(Config::$localStrings['inserisci testo articolo']))."';".PHP_EOL;
$App->defaultJavascript .= "messages['modifica articolo'] = '".addslashes(ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['modifica %ITEM%'])))."';".PHP_EOL;
$App->defaultJavascript .= "messages['modifica'] = '".addslashes(ucfirst(Config::$localStrings['modifica']))."';".PHP_EOL;

$App->defaultJavascript .= "messages['ci sono state modifiche negli articoli'] = '".addslashes(ucfirst(Config::$localStrings['ci sono state modifiche negli articoli']))."!';".PHP_EOL;

$App->defaultJavascript .= "messages['numero fattura valido'] = '".addslashes(ucfirst(Config::$localStrings['il numero fattura è valido!']))."';".PHP_EOL;
$App->defaultJavascript .= "messages['numero fattura esiste'] = '".addslashes(ucfirst(Config::$localStrings['il numero fattura esiste già!']))."';".PHP_EOL;

$App->defaultJavascript .= "var defDateins = '".$App->item->dateins."';".PHP_EOL;
$App->defaultJavascript .= "var defDatesca = '".$App->item->datesca."';";
$App->defaultJavascript .= "var module = '".Core::$request->action."';";
?>
