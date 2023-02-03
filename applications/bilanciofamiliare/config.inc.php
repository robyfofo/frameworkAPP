<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * bilanciofamiliare/config.inc.php v.1.0.1. 24/07/2018
*/

$App->params = new stdClass();
$App->params->label = ucfirst(Config::$localStrings['fatture']);
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('bilanciofamiliare'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;
if (!isset($App->params->label) || (isset($App->params->label) && $App->params->label == '')) die('Error reading module settings!');

$App->params->codeVersion = ' 1.0.1.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->defaultTax = '0';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadDirs = array();
$App->params->uploadPaths = array();
$App->params->ordersType = array();

/* INVOICES PURCHASE type = 0 */
$App->params->tables['items']  = DB_TABLE_PREFIX.'bilancio_familiare';
$App->params->fields['items']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'type'=>array('label'=>Config::$localStrings['tipo'],'searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>'0','validate'=>'int'),
	'dateins'=>array('label'=>Config::$localStrings['data'],'searchTable'=>true,'required'=>true,'type'=>'date','defValue'=>Config::$nowDate,'validate'=>'datepicker'),
	'amount'=>array('label'=>Config::$localStrings['importo'],'searchTable'=>true,'required'=>true,'type'=>'float|10,2','defValue'=>'0.00','validate'=>'float'),
	'description'=>array('label'=>Config::$localStrings['descrizione'],'searchTable'=>true,'required'=>false,'type'=>'text'),
	'notax'=>array('label'=>Config::$localStrings['tassa'],'searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>'1','validate'=>'int'),	
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>Config::$nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>1,'validate'=>'int')
	);
	
?>