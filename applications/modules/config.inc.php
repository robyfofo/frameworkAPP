<?php
/**
 * Framework Siti HTML-PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/modules/config.inc.php v.1.3.0. 18/09/2020
*/

$App->params = new stdClass();
$App->params->label = "Moduli";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('modules'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadPaths = array();
$App->params->uploadDirs = array();
$App->params->ordersType = array();

$App->params->codeVersion = ' 1.3.0.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tableRif =  DB_TABLE_PREFIX.'modules';

$App->sections = $globalSettings['module sections'];

/* ITEM */
$App->params->ordersType['item'] = 'ASC';
$App->params->uploadPaths['item'] = PATH_UPLOAD_DIR."modules/";
$App->params->uploadDirs['item'] = UPLOAD_DIR."modules/";
$App->params->tables['item'] = $App->params->tableRif;
$App->params->fields['item'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'primary'=>true),
	'name'=>array('required'=>true,'label'=>ucfirst(Config::$localStrings['nome']),'searchTable'=>true,'type'=>'varchar|255'),
	'label'=>array('required'=>true,'label'=>ucfirst(Config::$localStrings['etichetta']),'searchTable'=>true,'type'=>'varchar|255'),
	'alias'=>array('required'=>true,'label'=>ucfirst(Config::$localStrings['alias']),'searchTable'=>true,'type'=>'varchar|100'),
	'content'=>array('label'=>ucfirst(Config::$localStrings['contenuto']),'searchTable'=>true,'type'=>'text'),
	'code_menu'=>array('label'=>ucfirst(Config::$localStrings['codice menu']),'searchTable'=>true,'type'=>'text'),
	'ordering'=>array('label'=>ucfirst(Config::$localStrings['ordine']),'searchTable'=>false,'type'=>'int|8','defValue'=>'0'),
	'section'=>array('label'=>ucfirst(Config::$localStrings['sezione']),'searchTable'=>false,'type'=>'int|1'),
	'help_small'=>array('label'=>ucfirst(Config::$localStrings['aiuto breve']),'searchTable'=>false,'type'=>'varchar|255'),
	'help'=>array('label'=>ucfirst(Config::$localStrings['aiuto']),'searchTable'=>false,'type'=>'text'),	'active'=>array('label'=>'Attiva','required'=>false,'type'=>'int','validate'=>'int|1','defValue'=>'0')
);		
?>