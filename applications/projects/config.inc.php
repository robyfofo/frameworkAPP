<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/projects/config.inc.php v.1.3.0. 25/09/2020
*/

$App->params = new stdClass();
$App->params->label = ucfirst($_lang['progetti']);
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('section','name','label','help_small','help'),array('projects'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;

$App->params->codeVersion = ' 1.3.0.';
$App->params->pageTitle = $App->params->label;
//$App->params->breadcrumb = '<li class="active"><i class="icon-projects"></i> '.ucfirst($_lang['progetti']).'</li>';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadDirs = array();
$App->params->uploadPaths = array();
$App->params->orderTypes = array();
$App->params->labels = array();

$App->params->moduleAccessRead = (Permissions::checkIfModulesIsReadable($App->params->name,$App->userLoggedData) === true ? 1 : 0);
$App->params->moduleAccessWrite = (Permissions::checkIfModulesIsWritable($App->params->name,$App->userLoggedData) === true ? 1 : 0);

$App->params->tables['cont'] = DB_TABLE_PREFIX.'contacts';
$App->params->tables['time'] = DB_TABLE_PREFIX.'timecard';
$App->params->tables['todo'] = DB_TABLE_PREFIX.'todo';
$App->params->tables['thirdparty'] = DB_TABLE_PREFIX.'thirdparty';

$App->params->status = $globalSettings['status project'];
$App->params->statusTodo = $globalSettings['status to do'];

/* ITEMS */
$App->params->ordersType['item'] = 'DESC';
$App->params->tables['item'] = DB_TABLE_PREFIX.'projects';
$App->params->fields['item'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>$_lang['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'id_contact'=>array('label'=>$_lang['contatto'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>0,'validate'=>'int'),
	'title'=>array('label'=>$_lang['titolo'],'searchTable'=>true,'required'=>true,'type'=>'varchar|100'),
	'content'=>array('label'=>$_lang['contenuto'],'searchTable'=>true,'required'=>false,'type'=>'mediumtext'),
	'current'=>array('label'=>$_lang['selezionato'],'searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int'),
	'timecard'=>array('label'=>$_lang['timecard'],'searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int'),
	'status'=>array('label'=>$_lang['status'],'searchTable'=>false,'required'=>false,'type'=>'int|2','defValue'=>1,'validate'=>'int'),
	'costo_orario'=>array('label'=>$_lang['costo orario'],'searchTable'=>true,'required'=>false,'type'=>'float|10,2','defValue'=>'0.00','validate'=>'float'),
	'completato'=>array('label'=>$_lang['completato'],'searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'minmax','valuesRif'=>array('min'=>0,'max'=>100)),
	'access_type'=>array('label'=>$_lang['tipo accesso'],'searchTable'=>false,'required'=>true,'type'=>'int|1','defValue'=>0,'validate'=>'int'),	
	'access_read'=>array('label'=>$_lang['accesso lettura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
	'access_write'=>array('label'=>$_lang['accesso scrittura'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>'none'),
	'created'=>array('label'=>$_lang['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>$_lang['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
	);

/* THIRDPARTY */
$App->params->tables['cust']  = DB_TABLE_PREFIX.'thirdparty';
?>
