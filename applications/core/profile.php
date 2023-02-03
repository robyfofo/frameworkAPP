<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * core/profile.php v.1.3.0. 24/09/20120
*/

//Sql::setDebugMode(1);

include_once(PATH.$App->pathApplicationsCore."class.module.php");
$Module = new Module(DB_TABLE_PREFIX."users");

/* variabili ambiente */
$App->codeVersion = ' 3.5.4.';
$App->pageTitle = ucfirst($_lang['profilo']);
$App->pageSubTitle = preg_replace('/%ITEM%/', $_lang['profilo'], $_lang['modifica il %ITEM%']);
//$App->breadcrumb[] = '<li class="active"><i class="icon-user"></i> '.$_lang['profilo'].'</li>';
$App->templateApp = Core::$request->action.'.html';
$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

$App->coreModule = true;

$App->params =new stdClass;
$App->params->tables['nations'] = DB_TABLE_PREFIX.'location_nations';
$App->params->tables['province'] = DB_TABLE_PREFIX.'location_province';
$App->params->tables['comuni'] = DB_TABLE_PREFIX.'location_comuni';

$fields = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'name'=>array('label'=>'Nome','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'surname'=>array('label'=>'Cognome','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'street'=>array('label'=>'Via','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	
	'location_comuni_id'	=> array('label'=>$_lang['comune'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),
	'city'=>array('label'	=>$_lang['altro comune'],'searchTable'=>false,'required'=>false,'type'=>'varchar|150'),

	'zip_code'=>array('label'=>'C.A.P.','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'telephone'=>array('label'=>'Telefono','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'email'=>array('label'=>'Email','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'mobile'=>array('label'=>'Cellulare','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'fax'=>array('label'=>'Fax','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'skype'=>array('label'=>'Skype','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'template'=>array('label'=>'Template','searchTable'=>true,'type'=>'varchar'),
	'avatar'=>array('label'=>'Avatar','searchTable'=>false,'type'=>'blob'),
	'avatar_info'=>array('label'=>'Avatar Info','searchTable'=>false,'type'=>'varchar'),
	'active'=>array('label'=>'Attiva','required'=>false,'type'=>'int','defValue'=>0),

	'provincia'				=> array('label'=>$_lang['altra provincia'],'searchTable'=>true,'required'=>false,'type'=>'varchar|150','defValue'=>''),
	'location_province_id'	=> array('label'=>$_lang['provincia'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),

	'nation'				=> array('label'=>$_lang['nazione'],'searchTable'=>true,'required'=>false,'type'=>'varchar|150','defValue'=>''),
	'location_nations_id'	=> array('label'=>$_lang['nazione'],'searchTable'=>false,'required'=>false,'type'=>'int|10','defValue'=>0),

);

$App->province = new stdClass;
Sql::initQuery($App->params->tables['province'],array('*'),array(),'active = 1','nome ASC');
$App->province = Sql::getRecords();
if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

$App->nations = new stdClass;
Sql::initQuery($App->params->tables['nations'],array('*'),array(),'active = 1','title_'.$_lang['user'].' ASC');
$App->nations = Sql::getRecords();
if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

switch(Core::$request->method) {

	default;
		$App->item = new stdClass;
		//echo $App->id;
		if ($App->id > 0) {
			if ($_POST) {			
				if (!isset($_POST['active'])) $_POST['active'] = 1;	
				
				if (isset($_POST['location_comuni_id']) && intval($_POST['location_comuni_id']) > 0) {
					$App->comuni = new stdClass;
					Sql::initQuery($App->params->tables['comuni'],array('nome'),array(intval($_POST['location_comuni_id'])),'id = ? AND active = 1');
					$App->comune = Sql::getRecord();
					if (isset($App->comune->nome)) {
						$_POST['city'] = $App->comune->nome;
					}
				} else {
					$_POST['location_comuni_id'] = 0;
				}
				
				if (isset($_POST['location_province_id']) && intval($_POST['location_province_id']) > 0) {
					$App->provincia = new stdClass;
					Sql::initQuery($App->params->tables['province'],array('nome'),array(intval($_POST['location_province_id'])),'id = ? AND active = 1');
					$App->provincia = Sql::getRecord();
					if (isset($App->provincia->nome)) {
						$_POST['provincia'] = $App->provincia->nome;
					}
				} else {
					$_POST['location_province_id'] = 0;
				}
	
				$_POST['nation'] = '';
				if (isset($_POST['location_nations_id']) && intval($_POST['location_nations_id']) > 0) {
					$App->nation = new stdClass;
					Sql::initQuery($App->params->tables['nations'],array('title_'.$_lang['user']),array(intval($_POST['location_nations_id'])),'id = ? AND active = 1');
					$App->nation = Sql::getRecord();
					$field = 'title_'.$_lang['user'];
					if (isset($App->nation->$field)) {
						$_POST['nation'] =$App->nation->$field;
					}
				} else {
					$_POST['location_nations_id'] = 0;
				}

				/* recupero dati avatar */
				list($_POST['avatar'],$_POST['avatar_info']) = $Module->getAvatarData($App->id,$_lang);
				if ($Module->message != '') Core::$resultOp->messages[] = $Module->message;
				Core::$resultOp->type =  $Module->errorType;
				Core::$resultOp->error =  $Module->error;
				if (Core::$resultOp->error == 0) {
					/* controlla i campi obbligatori */
					Sql::checkRequireFields($fields);
					if (Core::$resultOp->error == 0) {	
						Sql::stripMagicFields($_POST);
						Sql::updateRawlyPost($fields,DB_TABLE_PREFIX."users",'id',$App->id);
						if(Core::$resultOp->error == 0) {
							Core::$resultOp->message = $_lang['Account modificato correttamente! Per rendere effettive le modifiche devi uscire dal sistema e loggarti nuovamente.'];	
						}
					}		
				} else {
					Core::$resultOp->error = 1;
				}	
			} 
								
			/* recupera i dati memorizzati */
			/* (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false)) */
			Sql::initQuery(DB_TABLE_PREFIX.'users',array('*'),array($App->id),"id = ?");
			$App->item = Sql::getRecord();			
			$App->templatesAvaiable = $Module->getUserTemplatesArray();
			if($Module->error == 1) {	
				Core::$resultOp->error = 1;
				Core::$resultOp->message = $Module->message;
			}
			
			$App->comune = new stdClass;
			$App->comune->selected = new stdClass;	
			if (isset($App->item->location_comuni_id) && $App->item->location_comuni_id > 0) {
				$App->comune->selected->id = $App->item->location_comuni_id;
				$App->comune->selected->nome = $App->item->city;
			}		

		} else {
			ToolsStrings::redirect(URL_SITE."home");
			die();						
		}
	break;	
}
$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">';
$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/bootstrap-select/js/bootstrap-select.min.js"></script>';

$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/ajax-bootstrap-select/css/ajax-bootstrap-select.min.css" rel="stylesheet">';

$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/ajax-bootstrap-select/js/ajax-bootstrap-select.min.js"></script>';
$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/ajax-bootstrap-select/js/locale/ajax-bootstrap-select.'.$_lang['charset'].'.min.js"></script>';

$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplicationsCore.'/templates/'.$App->templateUser.'/js/profile.js" type="text/javascript"></script>';
?>