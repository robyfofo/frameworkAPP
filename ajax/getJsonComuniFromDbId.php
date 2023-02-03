<?php
/* ajax/getComuneFromDbId.php v.4.5.1. 20/11/2018 */

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

$App->params = new stdClass;

$App->params->tables['nations'] = DB_TABLE_PREFIX.'location_nations';
$App->params->tables['province'] = DB_TABLE_PREFIX.'location_province';
$App->params->tables['comuni'] = DB_TABLE_PREFIX.'location_comuni';

$comuniArray = array();
$comuniArray[] = array('nome'=>'Altro comune','id'=>0);

$q = (isset($_POST['q']) ? $_POST['q'] : '');
$province_id = (isset($_POST['province_id']) ? intval($_POST['province_id']) : '0');

//print_r($_POST);
/*
$q = 'san';
$province_id = 23;
*/

if ($q != '') {
    $where = 'nome LIKE ? AND active = 1';
    $f = array('id,nome');
    $fv = array('%'.$q.'%');

    if ($province_id > 0 ) {
       $fv[] = $province_id;
        $where .= ' AND location_province_id = ?';
    }

    Sql::initQuery($App->params->tables['comuni'],$f,$fv,$where);
    $pdoObject = Sql::getPdoObjRecords();
    while ($row = $pdoObject->fetch()) {
            $comuniArray[] = array('nome'=>$row->nome,'id'=>$row->id);
    }		
}
echo json_encode($comuniArray);
die();
?>