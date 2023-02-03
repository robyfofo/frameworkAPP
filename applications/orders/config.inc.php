<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/orders/config.inc.php v.1.3.0. 29/09/2020
*/

$App->params = new stdClass();
$App->params->label = ucfirst(Config::$localStrings['fatture']);
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('section','name','label','help_small','help'),array('orders'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;
if (!isset($App->params->label) || (isset($App->params->label) && $App->params->label == '')) die('Error reading module settings!');

$App->params->codeVersion = ' 1.3.0.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->defaultTax = '0';
$App->params->defaultNumberYear = date('Y');

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadDirs = array();
$App->params->uploadPaths = array();
$App->params->ordersType = array();

$App->params->moduleAccessRead = (Permissions::checkIfModulesIsReadable($App->params->name,$App->userLoggedData) === true ? 1 : 0);
$App->params->moduleAccessWrite = (Permissions::checkIfModulesIsWritable($App->params->name,$App->userLoggedData) === true ? 1 : 0);
	
// orders
$App->params->tables['orders']  = DB_TABLE_PREFIX.'orders';
$App->params->fields['orders']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'dateins'=>array('label'=>Config::$localStrings['data'],'searchTable'=>true,'required'=>true,'type'=>'date','defValue'=>$App->nowDate,'validate'=>'datepicker'),
	'number'=>array('label'=>Config::$localStrings['numero'],'searchTable'=>true,'required'=>true,'type'=>'varchar|20','defValue'=>''),
	'number_year'=>array('label'=>Config::$localStrings['anno'],'searchTable'=>true,'required'=>true,'type'=>'varchar|4','defValue'=>$App->params->defaultNumberYear,'validate'=>'int'),
	'note'=>array('label'=>Config::$localStrings['Note (visibili in fattura)'],'searchTable'=>true,'required'=>false,'type'=>'varchar|255','defValue'=>''),
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
	);
	
// order articles
$App->params->tables['articles']  = DB_TABLE_PREFIX.'orders_articles';
$App->params->fields['articles']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'orders_id'=>array('label'=>Config::$localStrings['voce'],'searchTable'=>false,'required'=>true,'type'=>'int','defValue'=>'0','validate'=>'int'),
	'content'=>array('label'=>Config::$localStrings['contenuto'],'searchTable'=>false,'required'=>true,'type'=>'text','defValue'=>''),
	'price_unity'=>array('label'=>Config::$localStrings['prezzo unitario'],'searchTable'=>true,'required'=>false,'type'=>'float|','defValue'=>'0.00','validate'=>'float'),
	'price_tax'=>array('label'=>Config::$localStrings['imponibile'],'searchTable'=>true,'required'=>false,'type'=>'float','defValue'=>'0.00','validate'=>'float'),
	'price_total'=>array('label'=>Config::$localStrings['prezzo totale'],'searchTable'=>true,'required'=>false,'type'=>'float','defValue'=>'0.00','validate'=>'float'),
	'quantity'=>array('label'=>Config::$localStrings['quantità'],'searchTable'=>true,'required'=>true,'type'=>'int','defValue'=>'22','validate'=>'float|4,1'),	
	'tax'=>array('label'=>Config::$localStrings['tassa'],'searchTable'=>true,'required'=>true,'type'=>'varchar','defValue'=>'22'),
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
);

$App->params->tables['orders_thirdparty']  = DB_TABLE_PREFIX.'orders_thirdparty';
$App->params->tables['orders_company']  = DB_TABLE_PREFIX.'orders_company';

// thirdparty
$App->params->tables['thirdparty']  = DB_TABLE_PREFIX.'thirdparty';

// company
$App->params->tables['company']  = DB_TABLE_PREFIX.'company';

// warehouse products
$App->params->tables['prod'] = DB_TABLE_PREFIX.'warehouse_products';
?>