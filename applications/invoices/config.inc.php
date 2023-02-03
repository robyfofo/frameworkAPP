<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * invoices/config.inc.php v.1.2.0. 14/08/2020
*/

$App->params = new stdClass();
$App->params->label = ucfirst(Config::$localStrings['fatture']);
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('invoices'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;
if (!isset($App->params->label) || (isset($App->params->label) && $App->params->label == '')) die('Error reading module settings!');

$App->params->codeVersion = ' 1.2.0.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->defaultTax = '0';
$App->params->defaultNumberYear = '2020';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadDirs = array();
$App->params->uploadPaths = array();
$App->params->ordersType = array();

/* INVOICES PURCHASE type = 0 */
$App->params->tables['InvPur']  = DB_TABLE_PREFIX.'invoices_purchases';
$App->params->fields['InvPur']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'id_customer'=>array('label'=>Config::$localStrings['cliente'],'searchTable'=>false,'required'=>true,'type'=>'int|8','defValue'=>'0','validate'=>'int'),
	'dateins'=>array('label'=>Config::$localStrings['data'],'searchTable'=>true,'required'=>true,'type'=>'date','defValue'=>$App->nowDate,'validate'=>'datepicker'),
	'datesca'=>array('label'=>Config::$localStrings['data scadenza'],'searchTable'=>true,'required'=>true,'type'=>'date','defValue'=>$App->nowDate,'validate'=>'datepicker'),
	'number'=>array('label'=>Config::$localStrings['numero'],'searchTable'=>true,'required'=>true,'type'=>'varchar|20','defValue'=>''),
	'type'=>array('label'=>Config::$localStrings['tipo'],'searchTable'=>false,'required'=>true,'type'=>'int|1','defValue'=>'0','validate'=>'int'),
	
	'customer_ragione_sociale'=>array('label'=>Config::$localStrings['ragione sociale'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'customer_name'=>array('label'=>Config::$localStrings['nome'],'searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	'customer_surname'=>array('label'=>Config::$localStrings['cognome'],'searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	'customer_street'=>array('label'=>Config::$localStrings['via'],'searchTable'=>false,'required'=>false,'type'=>'varchar|100'),
	'customer_city'=>array('label'=>Config::$localStrings['città'],'searchTable'=>false,'required'=>false,'type'=>'varchar|100'),
	'customer_zip_code'=>array('label'=>Config::$localStrings['cap'],'searchTable'=>false,'required'=>false,'type'=>'varchar|10'),
	'customer_province'=>array('label'=>Config::$localStrings['provincia'],'searchTable'=>false,'required'=>false,'type'=>'varchar|100'),
	'customer_state'=>array('label'=>Config::$localStrings['stato'],'searchTable'=>false,'required'=>false,'type'=>'varchar|100'),
	'customer_telephone'=>array('label'=>Config::$localStrings['telefono'],'searchTable'=>false,'required'=>false,'type'=>'varchar|20'),
	'customer_email'=>array('label'=>Config::$localStrings['email'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'customer_fax'=>array('label'=>Config::$localStrings['fax'],'searchTable'=>true,'required'=>false,'type'=>'varchar|20'),
	'customer_partita_iva'=>array('label'=>Config::$localStrings['partita IVA'],'searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	'customer_codice_fiscale'=>array('label'=>Config::$localStrings['codice fiscale'],'searchTable'=>false,'required'=>false,'type'=>'varchar|50'),
	'customer_pec'=>array('label'=>'PEC','searchTable'=>true,'required'=>false,'type'=>'varchar|255'),
	'customer_sid'=>array('label'=>'SID','searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
	);
	
/* INVOICES SALES type = 1 */
$App->params->tables['InvSal']  = DB_TABLE_PREFIX.'invoices_sales';
$App->params->fields['InvSal']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'id_customer'=>array('label'=>Config::$localStrings['cliente'],'searchTable'=>false,'required'=>true,'type'=>'int|8','defValue'=>'0','validate'=>'int'),
	'dateins'=>array('label'=>Config::$localStrings['data'],'searchTable'=>true,'required'=>true,'type'=>'date','defValue'=>$App->nowDate,'validate'=>'datepicker'),
	'datesca'=>array('label'=>Config::$localStrings['data scadenza'],'searchTable'=>true,'required'=>true,'type'=>'date','defValue'=>$App->nowDate,'validate'=>'datepicker'),
	'number'=>array('label'=>Config::$localStrings['numero'],'searchTable'=>true,'required'=>true,'type'=>'varchar|20','defValue'=>''),
	'note'=>array('label'=>Config::$localStrings['Note (visibili in fattura)'],'searchTable'=>true,'required'=>false,'type'=>'varchar|255','defValue'=>''),
	'number_year'=>array('label'=>Config::$localStrings['anno'],'searchTable'=>true,'required'=>true,'type'=>'varchar|4','defValue'=>$App->params->defaultNumberYear,'validate'=>'int'),
	'type'=>array('label'=>Config::$localStrings['tipo'],'searchTable'=>false,'required'=>true,'type'=>'int|1','defValue'=>'0','validate'=>'int'),
	'tax'=>array('label'=>'IVA','searchTable'=>false,'required'=>false,'type'=>'int|2','defValue'=>'0','validate'=>'int'),
	'rivalsa'=>array('label'=>'Rivalsa','searchTable'=>false,'required'=>false,'type'=>'int|2','defValue'=>'0','validate'=>'int'),
	
	'customer_ragione_sociale'=>array('label'=>Config::$localStrings['ragione sociale'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'customer_name'=>array('label'=>Config::$localStrings['nome'],'searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	'customer_surname'=>array('label'=>Config::$localStrings['cognome'],'searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	'customer_street'=>array('label'=>Config::$localStrings['via'],'searchTable'=>false,'required'=>false,'type'=>'varchar|100'),
	'customer_city'=>array('label'=>Config::$localStrings['città'],'searchTable'=>false,'required'=>false,'type'=>'varchar|100'),
	'customer_zip_code'=>array('label'=>Config::$localStrings['cap'],'searchTable'=>false,'required'=>false,'type'=>'varchar|10'),
	'customer_province'=>array('label'=>Config::$localStrings['provincia'],'searchTable'=>false,'required'=>false,'type'=>'varchar|100'),
	'customer_state'=>array('label'=>Config::$localStrings['stato'],'searchTable'=>false,'required'=>false,'type'=>'varchar|100'),
	'customer_telephone'=>array('label'=>Config::$localStrings['telefono'],'searchTable'=>false,'required'=>false,'type'=>'varchar|20'),
	'customer_email'=>array('label'=>Config::$localStrings['email'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'customer_fax'=>array('label'=>Config::$localStrings['fax'],'searchTable'=>true,'required'=>false,'type'=>'varchar|20'),
	'customer_partita_iva'=>array('label'=>Config::$localStrings['partita IVA'],'searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	'customer_codice_fiscale'=>array('label'=>Config::$localStrings['codice fiscale'],'searchTable'=>false,'required'=>false,'type'=>'varchar|50'),
	'customer_pec'=>array('label'=>'PEC','searchTable'=>true,'required'=>false,'type'=>'varchar|255'),
	'customer_sid'=>array('label'=>'SID','searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	
	'stampa_quantita'=>array('label'=>'Stampa quantità','searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>'0','validate'=>'int'),
	'stampa_unita'=>array('label'=>'Stampa unità','searchTable'=>false,'required'=>false,'type'=>'int|1','defValue'=>'0','validate'=>'int'),
	
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
	);
	
/* INVOICES PURCHASE ARTICLES */
$App->params->tables['ArtPur']  = DB_TABLE_PREFIX.'invoices_purchases_articles';
$App->params->fields['ArtPur']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'id_invoice'=>array('label'=>Config::$localStrings['voce'],'searchTable'=>false,'required'=>true,'type'=>'int|8','defValue'=>'0','validate'=>'int'),
	'content'=>array('label'=>Config::$localStrings['contenuto'],'searchTable'=>false,'required'=>true,'type'=>'text','defValue'=>''),
	'price_unity'=>array('label'=>Config::$localStrings['prezzo unitario'],'searchTable'=>true,'required'=>true,'type'=>'float|10,2','defValue'=>'0.00','validate'=>'float'),
	'price_tax'=>array('label'=>Config::$localStrings['imponibile'],'searchTable'=>true,'required'=>false,'type'=>'float|10,2','defValue'=>'0.00','validate'=>'float'),
	'price_total'=>array('label'=>Config::$localStrings['prezzo totale'],'searchTable'=>true,'required'=>true,'type'=>'float|10,2','defValue'=>'0.00','validate'=>'float'),
	'quantity'=>array('label'=>Config::$localStrings['quantità'],'searchTable'=>true,'required'=>true,'type'=>'int|4','defValue'=>'22','validate'=>'float|4,1'),	
	'tax'=>array('label'=>Config::$localStrings['tassa'],'searchTable'=>true,'required'=>true,'type'=>'int|2','defValue'=>'22'),
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
	);
	
/* INVOICES SALES ARTICLES */
$App->params->tables['ArtSal']  = DB_TABLE_PREFIX.'invoices_sales_articles';
$App->params->fields['ArtSal']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'users_id'=>array('label'=>Config::$localStrings['proprietario'],'searchTable'=>false,'required'=>false,'type'=>'int|8','defValue'=>$App->userLoggedData->id),
	'id_invoice'=>array('label'=>Config::$localStrings['voce'],'searchTable'=>false,'required'=>true,'type'=>'int','defValue'=>'0','validate'=>'int'),
	'content'=>array('label'=>Config::$localStrings['contenuto'],'searchTable'=>false,'required'=>true,'type'=>'text','defValue'=>''),
	'price_unity'=>array('label'=>Config::$localStrings['prezzo unitario'],'searchTable'=>true,'required'=>false,'type'=>'float|','defValue'=>'0.00','validate'=>'float'),
	'price_tax'=>array('label'=>Config::$localStrings['imponibile'],'searchTable'=>true,'required'=>false,'type'=>'float','defValue'=>'0.00','validate'=>'float'),
	'price_total'=>array('label'=>Config::$localStrings['prezzo totale'],'searchTable'=>true,'required'=>false,'type'=>'float','defValue'=>'0.00','validate'=>'float'),
	'quantity'=>array('label'=>Config::$localStrings['quantità'],'searchTable'=>true,'required'=>true,'type'=>'int','defValue'=>'22','validate'=>'float|4,1'),	
	'tax'=>array('label'=>Config::$localStrings['tassa'],'searchTable'=>true,'required'=>true,'type'=>'varchar','defValue'=>'22'),
	'created'=>array('label'=>Config::$localStrings['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime','defValue'=>$App->nowDateTime,'validate'=>'datatimeiso'),
	'active'=>array('label'=>Config::$localStrings['attiva'],'required'=>false,'type'=>'int|1','defValue'=>0,'validate'=>'int')
	);
/* THIRDPARTY */
$App->params->tables['cust']  = DB_TABLE_PREFIX.'thirdparty';

/* COMPANY */
$App->params->tables['comp']  = DB_TABLE_PREFIX.'company';
?>