<?php
/* ajax/renderuseravatarfromdb.php v.4.5.1. 20/11/2018 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('PATH','../');

include_once(PATH."include/configuration.inc.php");
include_once(PATH."classes/class.Config.php");
include_once(PATH."classes/class.Core.php");
include_once(PATH."classes/class.Sessions.php");
include_once(PATH."classes/class.Sql.php");
include_once(PATH."classes/class.SanitizeStrings.php");

//Core::setDebugMode(1);

$Config = new Config();
Config::setGlobalSettings($globalSettings);
$Core = new Core();

/* avvio sessione */
$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME,SESSIONS_COOKIE_NAME);
$my_session->my_session_start();
$_MY_SESSION_VARS = array();
$_MY_SESSION_VARS = $my_session->my_session_read();

/* variabili globali */
$App = new stdClass;
define('DB_TABLE_PREFIX',Sql::getTablePrefix());

$App->item = new stdClass;

$array_avatarInfo = '';
$avatarInfo = '';
$id = (isset($_GET['id']) ? intval($_GET['id']) : 0);
if ($id > 0) {	
	Sql::initQuery(DB_TABLE_PREFIX.'users',array('*'),array($id),"id = ?");
	$App->item = Sql::getRecord();	
	if (Core::$resultOp->error == 0) {	
		$avatarInfo = $App->item->avatar;
		$array_avatarInfo = unserialize($App->item->avatar_info);
	}
}
//print_r($App->item);die();
//echo $avatarInfo; die();
//print_r($array_avatarInfo); die();

if ($avatarInfo != '') {
	$img = $avatarInfo;
	@header ("Content-type: ".$array_avatarInfo['type']);
	echo $img;
} else {
	$file = PATH.'templates/default/img/avatar.png';
	@header ("Content-type: image/png");
	@header('Content-Length: ' . filesize($file));
	echo file_get_contents($file);
}
die();
?>