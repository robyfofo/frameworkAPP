<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * warehouse/config.inc.php v.1.3.0. 11/09/2020
*/

$App->params = new stdClass();
$App->params->label = "Magazzino";
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('name','label','help_small','help'),array('warehouse'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;

/* configurazione */
$App->params->applicationName = Core::$request->action;

$App->params->databases = array();
$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadPathDirs = array();
$App->params->uploadDirs = array();
$App->params->orderTypes = array();

$App->params->moduleAccessRead = (Permissions::checkIfModulesIsReadable($App->params->name,$App->userLoggedData) === true ? 1 : 0);
$App->params->moduleAccessWrite = (Permissions::checkIfModulesIsWritable($App->params->name,$App->userLoggedData) === true ? 1 : 0);

$App->params->codeVersion = ' 1.3.0.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->uploadPaths['base'] = PATH_UPLOAD_DIR."warehouse/";
$App->params->uploadDirs['base'] = UPLOAD_DIR."warehouse/";


$App->params->tableRif =  DB_TABLE_PREFIX.'warehouse_';

/* configurazione */
$App->params->ordersType['conf'] = 'DESC';
$App->params->tables['conf'] =$App->params->tableRif.'_configuration';
$App->params->uploadPaths['conf'] = PATH_UPLOAD_DIR."warehouse/";
$App->params->uploadDirs['conf'] = UPLOAD_DIR."warehouse/";
$App->params->fields['conf'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>false,'primary'=>false),
	'filename'=>array('label'=>Config::$localStrings['immagine'].' Top','searchTable'=>false,'required'=>false,'type'=>'varchar|255'),
	'org_filename'=>array('label'=>Config::$localStrings['nome file originale'].' Top','searchTable'=>false,'required'=>false,'type'=>'varchar|255'),
);

// categorie e subcategorie */
$App->params->orderTypes['cate'] = 'ASC';
$App->params->tables['cate'] = $App->params->tableRif.'categories';
$App->params->fields['cate'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'primary'=>true),
	'parent'=>array('label'=>'Parent','searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>0),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'title'=>array('label'=>Config::$localStrings['titolo'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','validate'=>'int','defValue'=>'0')
);

/* PRODOTTI */
$App->params->uploadPaths['prod'] = PATH_UPLOAD_DIR."warehouse/products/";
$App->params->uploadDirs['prod'] = UPLOAD_DIR."warehouse/products/";
$App->params->ordersType['prod'] = 'ASC';
$App->params->tables['prod'] = $App->params->tableRif.'products';
$App->params->fields['prod'] = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'primary'=>true),
	'users_id'			=> array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'categories_id'		=> array('label'=>'ID Cat','required'=>false,'type'=>'int'),
	'price_unity'		=> array('label'=>Config::$localStrings['prezzo unitario'],'searchTable'=>true,'required'=>true,'type'=>'float|10,2','defValue'=>'0.00','validate'=>'float'),
	'tax'				=> array('label'=>Config::$localStrings['iva'],'searchTable'=>true,'required'=>false,'type'=>'float|10,2','defValue'=>'0.00','validate'=>'float'),
	'filename'			=> array('label'=>Config::$localStrings['immagine'],'searchTable'=>true,'required'=>false,'type'=>'varchar|255'),
	'org_filename'		=> array('label'=>Config::$localStrings['nome file originale'],'searchTable'=>true,'required'=>false,'type'=>'varchar|255'),
	'created'			=> array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datatimeiso'),
	'active'			=> array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','validate'=>'int','defValue'=>'0'),
	'title' 			=> array('label'=>Config::$localStrings['titolo'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255','defValue'=>''),
	'content'			=> array('label'=>Config::$localStrings['descrizione'],'searchTable'=>true,'required'=>false,'type'=>'mediumtext','defValue'=>''),
);


// attributi prodotto
$App->params->tables['proatypes'] = $App->params->tableRif.'products_attribute_types';

$App->params->tables['proa'] = $App->params->tableRif.'product_attributes';
$App->params->fields['proa'] = array (
'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'primary'=>true),
'products_id'=>array('label'=>'ID Prodotto','required'=>false,'searchTable'=>false,'type'=>'int|8'),
'products_attribute_types_id'=>array('label'=>'ID Tipo Attributo','required'=>false,'searchTable'=>false,'type'=>'int|8'),
'code'=>array('label'=>Config::$localStrings['codice'],'required'=>false,'searchTable'=>false,'type'=>'varchar|100','defValue'=>''),
'value_string'=>array('label'=>Config::$localStrings['valore stringa'],'required'=>false,'searchTable'=>true,'type'=>'varchar|100','defValue'=>''),
'value_int'=>array('label'=>Config::$localStrings['valore intero'],'required'=>false,'searchTable'=>true,'type'=>'int|8','validate'=>'int','defValue'=>'0'),
'value_float'=>array('label'=>Config::$localStrings['valore float'],'required'=>false,'searchTable'=>true,'type'=>'float|10,2','validate'=>'float','defValue'=>'0.00'),
'value_type'=>array('label'=>Config::$localStrings['valore tipo'],'required'=>false,'searchTable'=>true,'type'=>'varchar|10','defValue'=>''),
'quantity'=>array('label'=>Config::$localStrings['quantità'],'required'=>false,'searchTable'=>true,'type'=>'int|8','validate'=>'int','defValue'=>'0'),
'active'=>array('label'=>Config::$localStrings['attivazione'],'required'=>false,'type'=>'int|1','validate'=>'int','defValue'=>'0')
);

/* MODULES ITEM RESOURCES */
$App->params->tables['modules_item_resources'] = $App->params->tableRif.'modules_item_resources';
$App->params->fields['modules_item_resources'] = array (
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'primary'=>true),
	'id_owner'=>array('label'=>'IDOwner','required'=>true,'searchTable'=>false,'type'=>'int|8'),
	'resource_type'=>array('label'=>'Type resource','required'=>true,'searchTable'=>false,'type'=>'int|8'),
	'filename'=>array('label'=>'File','searchTable'=>false,'required'=>true,'type'=>'varchar|255'),
	'org_filename'=>array('label'=>'Nome Originale','searchTable'=>true,'required'=>false,'type'=>'varchar|255'),
	'extension'=>array('label'=>'Ext','searchTable'=>false,'required'=>false,'type'=>'varchar|40'),
	'code'=>array('label'=>'Code','searchTable'=>false,'required'=>false,'type'=>'text'),
	'size_file'=>array('label'=>'Dimensione','searchTable'=>false,'required'=>false,'type'=>'varchar|20'),
	'size_image'=>array('label'=>'Dimensione','searchTable'=>false,'required'=>false,'type'=>'varchar|40'),
	'type'=>array('label'=>'Tipo','searchTable'=>true,'required'=>false,'type'=>'varchar|100'),
	'url_code'=>array('label'=>'Codice Url','searchTable'=>false,'required'=>false,'type'=>'varchar|255','defValue'=>''),
	'url_image'=>array('label'=>'Image Url','searchTable'=>false,'required'=>false,'type'=>'varchar|255','defValue'=>''),
	'module_name'=>array('label'=>'Modulo','searchTable'=>true,'required'=>false,'type'=>'varchar|255'),
	'module_table'=>array('label'=>'Tabella','searchTable'=>true,'required'=>false,'type'=>'varchar|255'),
	'ordering'=>array('label'=>Config::$localStrings['ordinamento'],'required'=>false,'type'=>'int|8','validate'=>'int'),
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attivazione'],'required'=>false,'type'=>'int|1','validate'=>'int','defValue'=>'0')
);
foreach($globalSettings['languages'] AS $lang) {
	$searchTable = true;
	$required = ($lang == Config::$localStrings['user'] ? true : false);
	$App->params->fields['modules_item_resources']['title_'.$lang] = array('label'=>'Titolo '.$lang,'searchTable'=>$searchTable,'required'=>$required,'type'=>'varchar|255');
	$App->params->fields['modules_item_resources']['content_'.$lang] = array('label'=>Config::$localStrings['contenuto'].'  '.$lang,'searchTable'=>true,'required'=>false,'type'=>'text');
}
?>
