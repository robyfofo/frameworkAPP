<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/index.php v.1.3.1. 17/09/2020
*/
session_start();
//ini_set('display_errors',1);

define('PATH','');
define('MAXPATH', str_replace("includes","",dirname(__FILE__)).'');
if(!ini_get('date.timezone')) date_default_timezone_set('GMT');
setlocale(LC_TIME, 'ita', 'it_IT');

include_once(PATH."include/configuration.inc.php");

//Load composer's autoloader
require 'classes/vendor/autoload.php';

include_once(PATH."classes/class.Config.php");
include_once(PATH."classes/class.Core.php");
include_once(PATH."classes/class.Sessions.php");
include_once(PATH."classes/class.Permissions.php");
include_once(PATH."classes/class.ToolsStrings.php");
include_once(PATH."classes/class.SanitizeStrings.php");
include_once(PATH."classes/class.htmLawed.php");
include_once(PATH."classes/class.ToolsUpload.php");
include_once(PATH."classes/class.Sql.php");
include_once(PATH."classes/class.Utilities.php");
include_once(PATH."classes/class.DateFormat.php");
include_once(PATH."classes/class.Subcategories.php");
include_once(PATH."classes/class.Products.php");
include_once(PATH."classes/class.Form.php");
include_once(PATH."classes/class.Mails.php");

$Config = new Config();
Config::setGlobalSettings($globalSettings);
$Core = new Core();

//Sql::setDebugMode(1);

// avvio sessione
$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME,SESSIONS_COOKIE_NAME);
$my_session->my_session_start();
$_MY_SESSION_VARS = array();
$_MY_SESSION_VARS = $my_session->my_session_read();

// variabili globali
$App = new stdClass;
define('DB_TABLE_PREFIX',Sql::getTablePrefix());
$App->templateBase = 'struttura.html';
$renderTpl = true;
$renderAjax = false;
$App->templateApp = '';
$App->pathApplications = 'applications/';
$App->pathApplicationsCore = 'applications/core/';
$App->mySessionVars = $_MY_SESSION_VARS;
$App->globalSettings = $globalSettings;
$App->breadcrumb = '';
$App->metaTitlePage = SITE_NAME.' v.'.CODE_VERSION;
$App->metaDescriptionPage = $globalSettings['meta tags page']['description'];
$App->metaKeywordsPage = $globalSettings['meta tags page']['keyword'];

// date default
setlocale(LC_TIME, 'ita', 'it_IT');
$App->nowDate = date('Y-m-d');
$App->nowDateTime = date('Y-m-d H:i:s');
$App->nowTime = date('H:i:s');
$App->nowDateIta = date('d/m/Y');
$App->nowDateTimeIta = date('d/m/Y H:i:s');
$App->nowTimeIta = date('H:i:s');

// caricla l'eleco delle tabelle database
$App->tablesOfDatabase = Sql::getTablesDatabase($globalSettings['database'][DATABASE]['name']);

$App->userLoggedData = new stdClass();
/* carica dati utente loggato */
if (isset($_MY_SESSION_VARS['idUser'])) {
	Sql::initQuery(DB_TABLE_PREFIX.'users',array('*'),array($_MY_SESSION_VARS['idUser']),'active = 1 AND id = ?','');
	$App->userLoggedData = Sql::getRecord();
	if (Core::$resultOp->error == 1) die('Errore db utenti!');
	$App->userLoggedData->is_root = intval($App->userLoggedData->is_root);
}

// gestisce la richiesta http parametri get
$App->modulesCore = array('login','logout','account','password','profile','nopassword','nousername','moduleassociated','error');
Core::$globalSettings['requestoption']['coremodules'] = $App->modulesCore;
Core::$globalSettings['requestoption']['othermodules'] = array_merge(array('help'),Core::$globalSettings['requestoption']['coremodules']);
Core::$globalSettings['requestoption']['defaultaction'] = 'home';
Core::$globalSettings['requestoption']['defaultpagesmodule'] = 'home';
Core::$globalSettings['requestoption']['sectionadmin'] = 1;

if (isset($App->userLoggedData->is_root) && $App->userLoggedData->is_root == 1) Core::$globalSettings['requestoption']['isRoot'] = 1;
Core::getRequest(Core::$globalSettings['requestoption']);

//print_r(Core::$request);

if (!isset($_MY_SESSION_VARS['idUser'])){
	if (Core::$request->action != "nopassword" && Core::$request->action != "nousername") Core::$request->action = 'login';
}

// LIVELLI UTENTE
$App->user_levels = Permissions::getUserLevels();
if (Core::$resultOp->error == 1) die('Errore db livello utenti!');
if (isset($App->userLoggedData->id_level)) {
	$App->userLoggedData->labelRole = Permissions::getUserLevelLabel($App->user_levels,$App->userLoggedData->id_level,$App->userLoggedData->is_root);
}
// FINE LIVELLI UTENTE

$App->templateUser = Core::$globalSettings['requestoption']['defaulttemplate'];

// carica i dati dei moduli
foreach(Core::$globalSettings['module sections'] AS $key=>$value) {
	Sql::initQuery(DB_TABLE_PREFIX.'modules',array('*'),array($key),'active = 1 AND section = ?','ordering ASC');
	$App->modules[$key] = Sql::getRecords();
	if (Core::$resultOp->error == 1) die('Errore db livello utenti!');
}

// legge i permessi moduli
$App->user_modules_active = Permissions::getUserLevelModulesRights($App->userLoggedData);

// controlla permessi per accesso modulo
$App->user_first_module_active = Core::$globalSettings['requestoption']['defaultaction'];
$App->user_modules_active = Core::$globalSettings['requestoption']['defaultaction'];
if (Permissions::checkIfModulesIsReadable(Core::$request->action,$App->userLoggedData) == false) {
	//echo '<br>accesso negato';
	Core::$request->action = $App->user_first_module_active;
}

if ($globalSettings['default language'] != '') {
	if (file_exists(PATH."lang/".$globalSettings['default language'].".inc.php")) {
		include_once(PATH."lang/".$globalSettings['default language'].".inc.php");
	} else {
		include_once(PATH."lang/it.inc.php");
	}
} else {
	include_once(PATH."lang/it.inc.php");
}

$pathApplications = $App->pathApplications;
$action = Core::$request->action;
$index = '/index.php';
$App->coreModule = false;

if (in_array(Core::$request->action,Core::$globalSettings['requestoption']['coremodules']) == true) {
	$App->coreModule = true;
	$pathApplications = $App->pathApplicationsCore;
	$action = '';
	$index = Core::$request->action.'.php';
}

/*
echo '<br>$pathApplications: '.$pathApplications;
echo '<br>$action: '.$action;
echo '<br>$index: '.$index;
*/

if (file_exists(PATH."iniapp.php")) include_once(PATH."iniapp.php");

if (file_exists(PATH.$pathApplications.$action.$index)) {
	include_once(PATH.$pathApplications.$action.$index);
} else {
	Core::$request->action =$App->user_first_module_active;
	include_once(PATH.$pathApplications.$App->user_first_module_active."/index.php");
}

if (file_exists(PATH."endapp.php")) include_once(PATH."endapp.php");

if ($App->coreModule == true) {
	$pathtemplateApp = PATH.$pathApplications .= "templates/".$App->templateUser."/";
} else {
	if ($App->templateApp != '') $App->templateApp = Core::$request->action."/templates/".$App->templateUser."/".$App->templateApp;
}

$pathtemplateBase = "templates/".$App->templateUser;
$pathtemplateApp = $pathApplications;

/* genera il template */
if ($renderTpl == true && $App->templateApp != '') {

	$arrayVars = array(
		'App'=>$App,
		'Lang'=>$_lang,
		'URLSITE'=>URL_SITE,
		'PATHSITE'=>URL_SITE,
		'UPLOADDIR'=>UPLOAD_DIR,
		'CoreRequest'=>Core::$request,
		'CoreResultOp'=>Core::$resultOp,
		'MySessionVars'=>$_MY_SESSION_VARS,
		'Session'   => $_SESSION,
		'GlobalSettings'=>$globalSettings
	);

	$loader = new \Twig\Loader\FilesystemLoader($pathtemplateBase);
	$loader->addPath($pathtemplateApp);
	$twig = new \Twig\Environment($loader, [
		//'cache' => PATH_UPLOAD_DIR.'compilation_cache',
		'autoescape'=>false,
		'debug' => true
	]);

	$twig->addExtension(new \Twig\Extension\DebugExtension());
	$template = $twig->load($App->templateBase);
	echo $template->render($arrayVars);

	} else { if ($renderAjax != true) echo 'No templateApp found!';}

if ($renderAjax == true){
	if (file_exists($pathApplications.$App->templateApp)) {
		include_once($pathApplications.$App->templateApp);
	}
}
//print_r($_MY_SESSION_VARS);
?>
