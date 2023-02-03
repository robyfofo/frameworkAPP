<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/estimates/config.inc.php v.1.3.0. 08/09/2020
*/

$App->params = new stdClass();

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('section','name','label','help_small','help'),array('estimates'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;
if (!isset($App->params->label) || (isset($App->params->label) && $App->params->label == '')) die('Error reading module settings!');

$App->params->codeVersion = ' 1.3.0.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->defaultTax = '0';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadDirs = array();
$App->params->uploadPaths = array();
$App->params->orderTypes = array();

$App->params->moduleAccessRead = (Permissions::checkIfModulesIsReadable($App->params->name,$App->userLoggedData) === true ? 1 : 0);
$App->params->moduleAccessWrite = (Permissions::checkIfModulesIsWritable($App->params->name,$App->userLoggedData) === true ? 1 : 0);

/* ITEMS */
$App->params->tables['item']  = DB_TABLE_PREFIX.'estimates';
$App->params->fields['item']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'users_id'=>array('label'=>$_lang['proprietario'],'searchTable'=>false,'required'=>true,'type'=>'int','defValue'=>$App->userLoggedData->id),
	'thirdparty_id'=>array('label'=>$_lang['cliente'],'searchTable'=>false,'required'=>true,'type'=>'int','defValue'=>'0','validate'=>'int'),
	'customer'=>array('label'=>$_lang['cliente'],'searchTable'=>false,'required'=>false,'type'=>'text','defValue'=>''),
	'dateins'=>array('label'=>$_lang['data'],'searchTable'=>true,'required'=>true,'type'=>'date','defValue'=>$App->nowDate,'validate'=>'datepicker'),
	'datesca'=>array('label'=>$_lang['data scadenza'],'searchTable'=>true,'required'=>true,'type'=>'date','defValue'=>$App->nowDate,'validate'=>'datepicker'),
	'note'=>array('label'=>$_lang['note'],'searchTable'=>true,'required'=>false,'type'=>'varchar','defValue'=>''),
	'content'			=> array('label'=>$_lang['descrizione'],'searchTable'=>true,'required'=>false,'type'=>'longtext','defValue'=>''),
	'tax'				=> array('label'=>'IVA','searchTable'=>false,'required'=>false,'type'=>'int|2','defValue'=>'0','validate'=>'int'),
	'rivalsa'=>array('label'=>'Rivalsa','searchTable'=>false,'required'=>false,'type'=>'int|2','defValue'=>'0','validate'=>'int'),
	'created'=>array('label'=>$_lang['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datetimeiso'),
	'active'=>array('label'=>ucfirst($_lang['attiva']),'required'=>false,'type'=>'int','defValue'=>'0','validate'=>'int')
	);


/* ITEMS ARTICLES */
$App->params->tables['arts']  = DB_TABLE_PREFIX.'estimates_articles';
$App->params->fields['arts']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>$_lang['proprietario'],'searchTable'=>false,'required'=>true,'type'=>'int','defValue'=>$App->userLoggedData->id),
	'estimates_id'=>array('label'=>$_lang['voce'],'searchTable'=>false,'required'=>true,'type'=>'int','defValue'=>'0','validate'=>'int'),
	'content'=>array('label'=>$_lang['contenuto'],'searchTable'=>false,'required'=>true,'type'=>'text','defValue'=>''),
	'price_unity'=>array('label'=>$_lang['prezzo unitario'],'searchTable'=>true,'required'=>true,'type'=>'float','defValue'=>'0.00','validate'=>'float'),
	'price_tax'=>array('label'=>$_lang['imponibile'],'searchTable'=>true,'required'=>false,'type'=>'float','defValue'=>'0.00','validate'=>'float'),
	'price_total'=>array('label'=>$_lang['prezzo totale'],'searchTable'=>true,'required'=>true,'type'=>'float','defValue'=>'0.00','validate'=>'float'),
	'quantity'=>array('label'=>$_lang['quantità'],'searchTable'=>true,'required'=>true,'type'=>'int','defValue'=>'1','validate'=>'int'),
	'tax'=>array('label'=>$_lang['tassa'],'searchTable'=>true,'required'=>false,'type'=>'varchar','defValue'=>'0'),
	'created'=>array('label'=>$_lang['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datetimeiso'),
	'active'=>array('label'=>ucfirst($_lang['attiva']),'required'=>false,'type'=>'int','defValue'=>'0','validate'=>'int')
	);
/* THIRDPARTY */
$App->params->tables['cust']  = DB_TABLE_PREFIX.'thirdparty';

/* COMPANY */
$App->params->tables['comp']  = DB_TABLE_PREFIX.'company';
?>
