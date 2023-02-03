<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/third-party/config.inc.php v.1.3.0. 25/09/2020
*/

$App->params = new stdClass();
$App->params->label = ucfirst(Config::$localStrings['soggetti terzi']); 
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('section','name','label','help_small','help'),array('thirdparty'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;

$App->params->codeVersion = ' 1.3.0.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadDirs = array();
$App->params->uploadPaths = array();
$App->params->orderTypes = array();

$App->params->moduleAccessRead = (Permissions::checkIfModulesIsReadable($App->params->name,$App->userLoggedData) === true ? 1 : 0);
$App->params->moduleAccessWrite = (Permissions::checkIfModulesIsWritable($App->params->name,$App->userLoggedData) === true ? 1 : 0);

$App->params->tables['nations'] = DB_TABLE_PREFIX.'location_nations';
$App->params->tables['province'] = DB_TABLE_PREFIX.'location_province';
$App->params->tables['comuni'] = DB_TABLE_PREFIX.'location_comuni';

/* ITEMS */
$App->params->tables['item']  = DB_TABLE_PREFIX.'thirdparty';
$App->params->fields['item']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'categories_id'=>array('label'=>'ID '.Config::$localStrings['categoria'],'required'=>true,'type'=>'int|8'),
	'id_type'=>array('label'=>Config::$localStrings['tipo'],'required'=>false,'type'=>'int','defValue'=>0,'validate'=>'int|1'),
	'ragione_sociale'=>array('label'=>Config::$localStrings['ragione sociale'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'name'=>array('label'=>Config::$localStrings['nome'],'searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	'surname'=>array('label'=>Config::$localStrings['cognome'],'searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	'street'=>array('label'=>Config::$localStrings['via'],'searchTable'=>false,'required'=>false,'type'=>'varchar|100'),
	
	'location_comuni_id'	=> array('label'=>Config::$localStrings['comune'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),
	'city'					=>array('label'=>Config::$localStrings['altro comune'],'searchTable'=>false,'required'=>false,'type'=>'varchar|150'),

	'zip_code'=>array('label'=>Config::$localStrings['cap'],'searchTable'=>false,'required'=>false,'type'=>'varchar|10'),
	'telephone'=>array('label'=>Config::$localStrings['telefono'],'searchTable'=>false,'required'=>false,'type'=>'varchar|20'),
	'email'=>array('label'=>Config::$localStrings['email'],'searchTable'=>true,'required'=>false,'type'=>'varchar|255'),
	'mobile'=>array('label'=>Config::$localStrings['cellulare'],'searchTable'=>true,'required'=>false,'type'=>'varchar|20'),
	'fax'=>array('label'=>Config::$localStrings['fax'],'searchTable'=>true,'required'=>false,'type'=>'varchar|20'),
	'partita_iva'=>array('label'=>Config::$localStrings['partita IVA'],'searchTable'=>false,'required'=>false,'type'=>'varchar|50'),
	'codice_fiscale'=>array('label'=>Config::$localStrings['codice fiscale'],'searchTable'=>false,'required'=>false,'type'=>'varchar|50'),
	'pec'=>array('label'=>'PEC','searchTable'=>true,'required'=>false,'type'=>'varchar|255'),
	'sid'=>array('label'=>'SID','searchTable'=>true,'required'=>false,'type'=>'varchar|50'),

	'provincia'				=> array('label'=>Config::$localStrings['altra provincia'],'searchTable'=>true,'required'=>false,'type'=>'varchar|150','defValue'=>''),
	'location_province_id'	=> array('label'=>Config::$localStrings['provincia'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),

	'nation'				=> array('label'=>Config::$localStrings['nazione'],'searchTable'=>true,'required'=>false,'type'=>'varchar|150','defValue'=>''),
	'location_nations_id'	=> array('label'=>Config::$localStrings['nazione'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),
	
	'stampa_quantita'=>array('label'=>'Stampa quantità','searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>'0','validate'=>'int'),
	'stampa_unita'=>array('label'=>'Stampa unità','searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>'0','validate'=>'int'),

	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>Config::$nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
	);	
	
/* SUBCATEGORIES */
$App->params->orderTypes['cate'] = 'DESC';
$App->params->tables['cate']  = DB_TABLE_PREFIX.'thirdparty_categories';
$App->params->fields['cate']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'parent'=>array('label'=>'Parent','required'=>false,'type'=>'int','defValue'=>0,'validate'=>'int'),
	'title'=>array('label'=>Config::$localStrings['titolo'],'searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>Config::$nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
	);	
	
/* TYPES */
$App->params->tables['type']  = DB_TABLE_PREFIX.'thirdparty_types';
?>