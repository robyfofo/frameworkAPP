<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/include/configuration.inc.php v.1.3.0. 24/02/2023
*/

$globalSettings['site name'] = "Framework App";
$globalSettings['code version'] = '1.3.1.';

$globalSettings['site host'] = 'www.phprojekt.altervista.org/';
$globalSettings['folder site'] = 'frameworkAPP/';
$globalSettings['database'] = array(
	'user'=>'',
	'password'=>'',
	'host'=>'',
	'name'=>'',
	'tableprefix'=>'fap131_'
);
$globalSettings['server timezone'] = '';

$globalSettings['cookiestecnici'] = 'frameworkapp';
$globalSettings['cookiestecnicilastlogin'] = 'frameworkapplastlogin';

$globalSettings['use gmail for send email'] = 0;
$globalSettings['gmail username'] = '';
$globalSettings['gmail password'] = '';
$globalSettings['use php mail for send email'] = 1;
$globalSettings['use class mail for send email'] = 1;

$globalSettings['default email'] = '';
$globalSettings['default email label'] = '';
$globalSettings['send email debug'] = 1;
$globalSettings['email debug'] = "";

// computer
if ( $_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost' ){
	$globalSettings['site host'] = '127.0.0.1/';
	$globalSettings['folder site'] = '';
	$globalSettings['database'] = array(
		'user'=>'',
		'password'=>'',
		'host'=>'localhost',
		'name'=>'',
		'tableprefix'=>'fap131_'
	);

	$globalSettings['cookiestecniciadmin'] = 'locframeworkapp';
	$globalSettings['cookiestecniciadminlastlogin'] = 'locframeworkapplastlogin';

	$globalSettings['use gmail for send email'] = 0;
	$globalSettings['gmail username'] = '';
	$globalSettings['gmail password'] = '';
	$globalSettings['use php mail for send email'] = 1;
	$globalSettings['use class mail for send email'] = 1;
	
	$globalSettings['default email'] = '';
	$globalSettings['default email label'] = '';
	$globalSettings['send email debug'] = 1;
	$globalSettings['email debug'] = "";
}

$globalSettings['password backdoor'] = '$2y$10$uzEy8mB8nwateM6P5hIstu4MetH8eVgryuvVxLYiWLx6sfSrGXOmq';

/* GESTIONE TEMA INTERFACCIA */
$globalSettings['default template'] = 'defaul';

/* CHIAVE HASH */
$globalSettings['site code key'] = '123456789';

/* LANGUAGE */
$globalSettings['default language'] = 'it';
$globalSettings['languages'] = array('it','en');

/* UPLOAD */
$globalSettings['image type available'] = array('jpg','png','gif');
$globalSettings['file type available'] = array('doc','pdf','xls');

/* APP */
$globalSettings['site owner'] = 'Roberto Mantovani';
$globalSettings['copyright'] = '&copy; 2017 Roberto Mantovani';
$globalSettings['meta tags page'] = array(
	'title ini'=>'',
	'title end'=>'frameworkAPP',
	'title separator'=>' | ',
	'description'=>'Framework in PHP e MySQL utile per sviluppare applocazioni interneto e/o intranet. Il suo sistema modulare permette di creare moduli personalizzati per una infinità di compiti.',
	'keyword'=>'php, mysql, applicazione, internet, intranet, moduli, modulare'
);



/* DA NON MODIFICARE */

$globalSettings['status to do'] = array('n.d.','visto','in lavorazione','sospeso','cancellato','rifiutato','finito');
$globalSettings['status project'] = array('n.d.','preventivato','in lavorazione','sospeso','cancellato','rifiutato','finito');
$globalSettings['article type'] = array('0'=>'def acquisto e vendita','1'=>'def acquisto','2'=>'def vendita');

/* impostationi Request url */
$globalSettings['requestoption'] = array(
	'defaulttemplate'=>'default',
	'templatesforusers'=>array('default'),
	'managechangeaction'=>0,
	'defaultaction'=>'home',
	'blankaction'=>'home',
	'othermodules' => array()
	);

$globalSettings['module sections'] = array('Moduli Applicativo','Impostazioni','Root');
$globalSettings['languages'] = array('it','en');

define('SITE_NAME', $globalSettings['site name']);
define('CODE_VERSION',$globalSettings['code version']);
define('FOLDER_SITE',$globalSettings['folder site']);
define('SITE_HOST', $globalSettings['site host']);
define('TIMEZONE',$globalSettings['server timezone']);

define('DATABASE',$database);

/* HTTP/S */
$http = 'http://';
if (isset($_SERVER['HTTPS'])) $http = 'https://';
define('URL_SITE', $http.SITE_HOST.FOLDER_SITE);
define('URL_SITE_APPLICATION', $http.SITE_HOST.FOLDER_SITE.'application/');
define('TMP_DIR', $http.SITE_HOST.FOLDER_SITE.'tmp/');
define('PATH_DOCUMENT', $_SERVER['DOCUMENT_ROOT'].'/');
define('PATH_SITE', $_SERVER['DOCUMENT_ROOT'].'/'.FOLDER_SITE);
/* PATHS */
define('UPLOAD_DIR', $http.SITE_HOST.FOLDER_SITE.'uploads/');
define('PATH_UPLOAD_DIR', PATH_SITE.'uploads/');

/* SESSIONS */
define('SESSIONS_TABLE_NAME',$globalSettings['database'][$database]['tableprefix'].'sessions');
define('SESSIONS_TIME',86400*10);
define('SESSIONS_GC_TIME',2592000);

/* COOKIES */
define('SESSIONS_COOKIE_NAME',$cookies);
define('DATA_SESSIONS_COOKIE_NAME','data_'.$cookies); 

define('SITE_CODE_KEY',$globalSettings['site code key']);

define('SITE_OWNER',$globalSettings['site owner']);
define('COPYRIGHT',$globalSettings['copyright']);
?>
