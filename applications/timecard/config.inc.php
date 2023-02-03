<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * timecard/config.inc.php v.1.2.0. 21/12/2019
*/

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('help_small','help'),array('timecard'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;

$App->params->codeVersion = ' 1.0.0.';
$App->params->pageTitle = ucfirst(Config::$localStrings['timecard']);
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.ucfirst(Config::$localStrings['timecard']).'</li>';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadDirs = array();
$App->params->uploadPaths = array();
$App->params->ordersType = array();
$App->params->labels = array();

$App->params->tables['prog'] = DB_TABLE_PREFIX.'projects';
$App->params->tables['item'] = DB_TABLE_PREFIX.'timecard';
$App->params->tables['user'] = DB_TABLE_PREFIX.'users';

/* PITE */
$App->params->ordersType['pite'] = 'DESC';
$App->params->tables['pite'] = DB_TABLE_PREFIX.'timecard_predefinite';
$App->params->fields['pite'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'title'=>array('label'=>Config::$localStrings['titolo'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'content'=>array('label'=>Config::$localStrings['contenuto'],'searchTable'=>true,'required'=>false,'type'=>'text'),
	'starttime'=>array('label'=>Config::$localStrings['ora inizio'],'searchTable'=>false,'required'=>false,'type'=>'time','validate'=>'timepicker'),
	'endtime'=>array('label'=>Config::$localStrings['ora fine'],'searchTable'=>false,'required'=>false,'type'=>'time','validate'=>'timepicker'),
	'worktime'=>array('label'=>Config::$localStrings['ore lavoro'],'searchTable'=>false,'required'=>false,'type'=>'time','defValue'=>'00:00:00','validate'=>'time'),
	'access_read'=>array('label'=>Config::$localStrings['accesso lettura'],'searchTable'=>true,'required'=>false,'type'=>'text','defValue'=>'none'),
	'access_write'=>array('label'=>Config::$localStrings['accesso scrittura'],'searchTable'=>true,'required'=>false,'type'=>'text','defValue'=>'none'),
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>Config::$nowDateTime),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
	);

?>