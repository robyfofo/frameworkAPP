<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/users/config.inc.php v.1.3.0. 24/09/2020
*/

$App->params = new stdClass();
$App->params->label = 'Utenti';
// prende i dati del modulo
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('section','name','label','help_small','help'),array('users'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && is_object($obj)) $App->params = $obj;

$App->params->codeVersion = ' 1.3.0.';
$App->params->pageTitle = $App->params->label;
$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->params->tables = array();
$App->params->fields = array();
$App->params->uploadDirs = array();
$App->params->uploadPaths = array();
$App->params->ordersType = array();

$App->params->moduleAccessRead = (Permissions::checkIfModulesIsReadable($App->params->name,$App->userLoggedData) === true ? 1 : 0);
$App->params->moduleAccessWrite = (Permissions::checkIfModulesIsWritable($App->params->name,$App->userLoggedData) === true ? 1 : 0);

$App->params->tables['nations'] = DB_TABLE_PREFIX.'location_nations';
$App->params->tables['province'] = DB_TABLE_PREFIX.'location_province';
$App->params->tables['comuni'] = DB_TABLE_PREFIX.'location_comuni';

// items
$App->params->tables['item']  = DB_TABLE_PREFIX.'users';
$App->params->fields['item']  = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'int|8','autoinc'=>true,'nodb'=>true,'primary'=>true),
	'username'=>array('label'=>$_lang['nome utente'],'searchTable'=>true,'required'=>true,'type'=>'varchar|50'),
	'password'=>array('label'=>$_lang['password'],'searchTable'=>false,'required'=>false,'type'=>'password|255'),
	'name'=>array('label'=>$_lang['nome'],'searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	'surname'=>array('label'=>$_lang['cognome'],'searchTable'=>true,'required'=>false,'type'=>'varchar|50'),
	'street'=>array('label'=>$_lang['via'],'searchTable'=>false,'required'=>false,'type'=>'varchar|100'),

	'location_comuni_id'	=> array('label'=>$_lang['comune'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),
	'city'					=> array('label'=>$_lang['altro comune'],'searchTable'=>false,'required'=>false,'type'=>'varchar|150'),

	'zip_code'=>array('label'=>$_lang['cap'],'searchTable'=>false,'required'=>false,'type'=>'varchar|10'),
	'telephone'=>array('label'=>$_lang['telefono'],'searchTable'=>false,'required'=>false,'type'=>'varchar|20'),
	'email'=>array('label'=>$_lang['email'],'searchTable'=>true,'required'=>true,'type'=>'varchar|255'),
	'mobile'=>array('label'=>$_lang['cellulare'],'searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'fax'=>array('label'=>$_lang['fax'],'searchTable'=>true,'required'=>false,'type'=>'varchar|20'),
	'skype'=>array('label'=>$_lang['skype'],'searchTable'=>true,'required'=>false,'type'=>'varchar|100'),
	'template'=>array('label'=>$_lang['template'],'searchTable'=>true,'type'=>'varchar|100'),
	'avatar'=>array('label'=>$_lang['avatar'],'searchTable'=>false,'type'=>'blob'),
	'avatar_info'=>array('label'=>$_lang['avatar'],'searchTable'=>false,'type'=>'text'),
	'id_level'=>array('label'=>$_lang['livello'],'searchTable'=>false,'type'=>'int|8'),
	'is_root'=>array('label'=>'Root','searchTable'=>false,'type'=>'int|1','defValue'=>0),
	'hash'=>array('label'=>$_lang['hash'],'searchTable'=>false,'type'=>'varchar|255'),
	'created'=>array('label'=>$_lang['creazione'],'searchTable'=>false,'required'=>false,'type'=>'datatime'),
	'active'=>array('label'=>$_lang['attiva'],'required'=>false,'type'=>'int|1','defValue'=>1,'validate'=>'int'),

	'provincia'				=> array('label'=>$_lang['altra provincia'],'searchTable'=>true,'required'=>false,'type'=>'varchar|150','defValue'=>''),
	'location_province_id'	=> array('label'=>$_lang['provincia'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),

	'nation'				=> array('label'=>$_lang['nazione'],'searchTable'=>true,'required'=>false,'type'=>'varchar|150','defValue'=>''),
	'location_nations_id'	=> array('label'=>$_lang['nazione'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),

);
?>
