<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public	License
 * app/todo/config.inc.php v.1.3.0. 24/09/2020
*/

$App->params = new stdClass();
$App->params->label = ucfirst(Config::$localStrings['da fare']);
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('section','name','label','help_small','help'),array('todo'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;

$App->params->codeVersion = ' 1.3.0.';
$App->params->pageTitle = ucfirst(Config::$localStrings['da fare']);
$App->params->breadcrumb = '<li class="active"><i class="fa fa-bookmark-o"></i> '.ucfirst(Config::$localStrings['da fare']).'</li>';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadDirs = array();
$App->params->uploadPaths = array();
$App->params->orderTypes = array();
$App->params->labels = array();

$App->params->moduleAccessRead = (Permissions::checkIfModulesIsReadable($App->params->name,$App->userLoggedData) === true ? 1 : 0);
$App->params->moduleAccessWrite = (Permissions::checkIfModulesIsWritable($App->params->name,$App->userLoggedData) === true ? 1 : 0);

$App->params->tables['prog'] = DB_TABLE_PREFIX.'projects';
$App->params->status = $globalSettings['status to do'];

/* ITEMS */
$App->params->ordersType['item'] = 'DESC';
$App->params->tables['item'] = DB_TABLE_PREFIX.'todo';
$App->params->fields['item'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'id_project'=>array('label'=>ucfirst(Config::$localStrings['progetto']),'required'=>true,'type'=>'int','defValue'=>0,'validate'=>'int|8','validate'=>'int'),
	'title'=>array('label'=>ucfirst(Config::$localStrings['titolo']),'searchTable'=>true,'required'=>true,'type'=>'varchar|100'),
	'content'=>array('label'=>ucfirst(Config::$localStrings['contenuto']),'searchTable'=>true,'required'=>false,'type'=>'text'),
	'status'=>array('label'=>Config::$localStrings['status'],'searchTable'=>true,'required'=>false,'type'=>'int','defValue'=>0,'validate'=>'int|1','validate'=>'int'),
	'access_type'=>array('label'=>Config::$localStrings['tipo accesso'],'searchTable'=>false,'required'=>true,'type'=>'int|1','defValue'=>0,'validate'=>'int'),	
	'access_read'=>array('label'=>Config::$localStrings['accesso lettura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
	'access_write'=>array('label'=>Config::$localStrings['accesso scrittura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>Config::$nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
	);
?>