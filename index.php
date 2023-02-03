<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/index.php v.1.3.1. 17/02/2023
*/
session_start();
if (!isset($_SESSION['csrftoken'])) $_SESSION['csrftoken'] = bin2hex(openssl_random_pseudo_bytes(64));

define('PATH','');
define('MAXPATH', str_replace("includes","",dirname(__FILE__)).'');
if(!ini_get('date.timezone')) date_default_timezone_set('GMT');
setlocale(LC_TIME, 'ita', 'it_IT');

include_once(PATH."include/configuration.inc.php");

//Load composer's autoloader
require 'classes/vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
$debuglog = new Logger('debug');
$debuglog->pushHandler(new StreamHandler(PATH_SITE . 'logs/debuglog-'.date('Y-m-d').'.log', Logger::DEBUG));

$accesslog = new Logger('access');
$accesslog->pushHandler(new StreamHandler(PATH_SITE . 'logs/accesslog.log', Logger::DEBUG));
Core::$debuglog = $debuglog;

Config::init();
Config::setGlobalSettings($globalSettings);
Config::$dbTablePrefix = Config::$globalSettings[Config::$dbName]['tableprefix'];
$Core = new Core();
Core::$debuglog = $debuglog;

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

$App->lastLogin = Config::$nowDateTime;
// legge last login
if(isset( $_COOKIE[ Config::$globalSettings['cookiestecniciadminlastlogin'] ] )) {
	$App->lastLogin = $_COOKIE[ Config::$globalSettings['cookiestecniciadminlastlogin'] ];
}

Config::loadLanguageVars('it',$path='');
setlocale(LC_TIME,Config::$localStrings['lista lingue abbreviate'][Config::$localStrings['user']], Config::$localStrings['charset date']);
//ToolsStrings::dump(Config::$globalSettings);


// caricla l'eleco delle tabelle database
Config::initDatabaseTables($path='');
$App->tablesOfDatabase = Sql::getTablesDatabase($globalSettings[DATABASE]['name']);

// carica i moduli utente
Permissions::getUserModules();
//ToolsStrings::dump(Permissions::$userModules);
Config::$userModules = Permissions::$userModules;
//ToolsStrings::dump(Config::$userModules);

// carica i livelli utente
Config::$userLevels = Permissions::getUserLevels();
//ToolsStrings::dump(Config::$userLevels);

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
Permissions::getUserLevelModulesRights($App->userLoggedData);
$App->user_modules_active = Permissions::$accessModules;
//ToolsStrings::dump($App->user_modules_active);

// controlla permessi per accesso modulo
$App->user_first_module_active = Core::$globalSettings['requestoption']['defaultaction'];
$App->user_modules_active = Core::$globalSettings['requestoption']['defaultaction'];
if (Permissions::checkIfModulesIsReadable(Core::$request->action,$App->userLoggedData) == false) {
	//echo '<br>accesso negato';
	Core::$request->action = $App->user_first_module_active;
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
		'App'																=> $App,
		'LocalStrings'											=> Config::$localStrings,
		'URLSITE'														=> URL_SITE,
		'PATHSITE'													=> URL_SITE,
		'UPLOADDIR'													=> UPLOAD_DIR,
		'CoreRequest'												=> Core::$request,
		'CoreResultOp'											=> Core::$resultOp,
		'MySessionVars'											=> $_MY_SESSION_VARS,
		'Session'   												=> $_SESSION,
		'GlobalSettings'										=> $globalSettings,
		'DatabaseTablesFields'							=> Config::$DatabaseTablesFields,
		'DatabaseTables'										=> Config::$DatabaseTables

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
