<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/company/config.inc.php v.1.3.0. 23/09/2020
*/

$App->params = new stdClass();
$App->params->label = 'Azienda';

/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('section','name','label','help_small','help'),array('company'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && isset($obj->label)) $App->params = $obj;

$App->params->codeVersion = ' 1.3.0.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadDirs = array();
$App->params->uploadPaths = array();
$App->params->ordersType = array();

$App->params->tables['nations'] = DB_TABLE_PREFIX.'location_nations';
$App->params->tables['province'] = DB_TABLE_PREFIX.'location_province';
$App->params->tables['comuni'] = DB_TABLE_PREFIX.'location_comuni';

/* ITEMS */
$App->params->tables['item']  = DB_TABLE_PREFIX.'company';
$App->params->fields['item']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'ragione_sociale'=>array('label'=>Config::$localStrings['ragione sociale'],'searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'name'=>array('label'=>Config::$localStrings['nome'],'searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'surname'=>array('label'=>Config::$localStrings['cognome'],'searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'street'=>array('label'=>Config::$localStrings['via'],'searchTable'=>false,'required'=>false,'type'=>'varchar'),

	'location_comuni_id'	=> array('label'=>Config::$localStrings['comune'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),
	'city'=>array('label'=>Config::$localStrings['altro comune'],'searchTable'=>false,'required'=>false,'type'=>'varchar|150'),

	'zip_code'=>array('label'=>Config::$localStrings['c.a.p.'],'searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'telephone'=>array('label'=>Config::$localStrings['telefono'],'searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'email'=>array('label'=>Config::$localStrings['email'],'searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'mobile'=>array('label'=>Config::$localStrings['cellulare'],'searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'fax'=>array('label'=>Config::$localStrings['fax'],'searchTable'=>true,'required'=>false,'type'=>'varchar'),
	
	'partita_iva'=>array('label'=>Config::$localStrings['partita IVA'],'searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'codice_fiscale'=>array('label'=>Config::$localStrings['codice fiscale'],'searchTable'=>true,'required'=>true,'type'=>'varchar'),
	
	'gestione_iva'=>array('label'=>Config::$localStrings['gestione IVA'],'searchTable'=>false,'required'=>false,'type'=>'int','defValue'=>'0','validate'=>'int'),
	'iva'=>array('label'=>Config::$localStrings['IVA'],'searchTable'=>false,'required'=>false,'type'=>'int','defValue'=>'0','validate'=>'int'),
	'text_noiva'=>array('label'=>Config::$localStrings['testo no IVA'],'searchTable'=>true,'required'=>false,'type'=>'varchar','defValue'=>''),
	'gestione_rivalsa'=>array('label'=>Config::$localStrings['gestione rivalsa'],'searchTable'=>false,'required'=>false,'type'=>'int','defValue'=>'1','validate'=>'int'),
	'rivalsa'=>array('label'=>Config::$localStrings['rivalsa'],'searchTable'=>true,'required'=>false,'type'=>'int','defValue'=>'0','validate'=>'int'),
	'text_rivalsa'=>array('label'=>Config::$localStrings['testo rivalsa'],'searchTable'=>true,'required'=>false,'type'=>'varchar','defValue'=>''),
	
	'banca'=>array('label'=>Config::$localStrings['banca'],'searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'intestatario'=>array('label'=>Config::$localStrings['intestatario'],'searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'iban'=>array('label'=>Config::$localStrings['iban'],'searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'bic_swift'=>array('label'=>Config::$localStrings['bic swift'],'searchTable'=>false,'required'=>true,'type'=>'varchar'),

	'provincia'				=> array('label'=>Config::$localStrings['altra provincia'],'searchTable'=>true,'required'=>false,'type'=>'varchar|150','defValue'=>''),
	'location_province_id'	=> array('label'=>Config::$localStrings['provincia'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),

	'nation'				=> array('label'=>Config::$localStrings['nazione'],'searchTable'=>true,'required'=>false,'type'=>'varchar|150','defValue'=>''),
	'location_nations_id'	=> array('label'=>Config::$localStrings['nazione'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),

);
?>