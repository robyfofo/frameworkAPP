<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/company/items.php v.1.3.0. 29/09/2020
*/

$App->id = 1;
switch(Core::$request->method) {
	case 'getComuniAjaxItem';
		//Core::setDebugMode(1);
		$comuniArray = array();
		$comuniArray[] = array('nome'=>'Altro comune','id'=>0);
		$q = $_POST['q']; //This is the textbox value
		if ($q != '') {
			Sql::initQuery($App->params->tables['comuni'],array('id,nome'),array('%'.$q.'%'),'nome LIKE ? AND active = 1');
			$pdoObject = Sql::getPdoObjRecords();
			while ($row = $pdoObject->fetch()) {
					$comuniArray[] = array('nome'=>$row->nome,'id'=>$row->id);
			}		
		}
		echo json_encode($comuniArray);
		die();
	break;
	
	case 'updateItem':
		
		if ($_POST) {		
			
			if (isset($_POST['location_comuni_id']) && intval($_POST['location_comuni_id']) > 0) {
				$App->comuni = new stdClass;
				Sql::initQuery($App->params->tables['comuni'],array('nome'),array(intval($_POST['location_comuni_id'])),'id = ? AND active = 1');
				$App->comune = Sql::getRecord();
				if (isset($App->comune->nome)) {
					$_POST['city'] = $App->comune->nome;
				}
			}
			
			if (isset($_POST['location_province_id']) && intval($_POST['location_province_id']) > 0) {
				$App->provincia = new stdClass;
				Sql::initQuery($App->params->tables['province'],array('nome'),array(intval($_POST['location_province_id'])),'id = ? AND active = 1');
				$App->provincia = Sql::getRecord();
				if (isset($App->provincia->nome)) {
					$_POST['provincia'] = $App->provincia->nome;
				}
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
			}
			
			/* parsa i post in base ai campi */ 	
			Form::parsePostByFields($App->params->fields['item'],$_lang,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/editItem');
			}

			Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
	   		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			echo 'aaa'.$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% modificati'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/editItem');
					
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404'); die();
		}		
	break;

	default;

		$App->province = new stdClass;
		Sql::initQuery($App->params->tables['province'],array('*'),array(),'active = 1','nome ASC');
		$App->province = Sql::getRecords();
		//if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$App->nations = new stdClass;
		Sql::initQuery($App->params->tables['nations'],array('*'),array(),'active = 1','title_'.$_lang['user'].' ASC');
		$App->nations = Sql::getRecords();
		//if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
		
		$App->item = new stdClass;			
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);

		$App->comune = new stdClass;
		$App->comune->selected = new stdClass;
		$App->comune->selected->id = 0;
		$App->comune->selected->nome = '';
		if ($App->item->location_comuni_id > 0) {
			$App->comune->selected->id = $App->item->location_comuni_id;
			$App->comune->selected->nome = $App->item->city;
		}
		
		$App->methodForm = 'updateItem';	
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['voci'],$_lang['modifica %ITEM%']);
		$App->viewMethod = 'form';	
	break;	
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'form':	
	default:	
		$App->templateApp = 'formCompany.html';		
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formCompany.js"></script>';
	break;
}	
?>