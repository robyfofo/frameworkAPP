<?php
/* app/location/Comuni.php v.1.3.0. 20/09/2020 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch (Core::$request->method) {

	case 'activeComuni':
	case 'disactiveComuni':
		Sql::manageFieldActive(substr(Core::$request->method,0,-6),$App->params->tables['comuni'],$App->id,array('label'=>$_lang['comune'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listComuni');	
	break;
	
	case 'deleteComuni':
		if ($App->id > 0) {

			// cancello il record
			Sql::initQuery($App->params->tables['comuni'],array(),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['comune'],$_lang['%ITEM% cancellato'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listComuni');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;	

	case 'newComuni':	
		$App->province = new stdClass;
		Sql::initQuery($App->params->tables['province'],array('*'),array(),'active = 1','nome ASC');
		$App->province = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$App->nations = new stdClass;
		Sql::initQuery($App->params->tables['nations'],array('*'),array(),'active = 1','title_'.$_lang['user'].' ASC');
		$App->nations = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$App->item = new stdClass;
		$App->item->created = $App->nowDateTime;
		$App->item->active = 1;
		$App->item->location_nations_id = 116;
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['comune'],$_lang['inserisci %ITEM%']);
		$App->methodForm = 'insertComuni';
		$App->viewMethod = 'form';	
	break;
	
	case 'insertComuni':
		if ($_POST) {

			$_POST['provincia'] = '';
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
			
			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['comuni'],$_lang,array());
			if (Core::$resultOp->error > 0) {
			 	$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			 	ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newComuni');
			}
			 
			Sql::insertRawlyPost($App->params->fields['comuni'],$App->params->tables['comuni']);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$App->id = Sql::getLastInsertedIdVar(); /* preleva l'id della pagina */
			  
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['comune'],$_lang['%ITEM% inserito'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listComuni');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'modifyComuni':	

		$App->province = new stdClass;
		Sql::initQuery($App->params->tables['province'],array('*'),array(),'active = 1','nome ASC');
		$App->province = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$App->nations = new stdClass;
		Sql::initQuery($App->params->tables['nations'],array('*'),array(),'active = 1','title_'.$_lang['user'].' ASC');
		$App->nations = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['comuni'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['comune'],$_lang['modifica %ITEM%']);
		$App->methodForm = 'updateComuni';	
		$App->viewMethod = 'form';
	break;
	
	case 'updateComuni':
		if ($_POST) {

			$_POST['provincia'] = '';
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

			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['comuni'],$_lang,array());
			if (Core::$resultOp->error > 0) {
					$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newComuni');
			}
			
			Sql::updateRawlyPost($App->params->fields['comuni'],$App->params->tables['comuni'],'id',$App->id);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['comune'],$_lang['%ITEM% modificato'])).'!';
			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyComuni/'.$App->id);
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listComuni');
			}
			   
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}	break;
		die();
	case 'pageComuni':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);

	case 'listComuni':
	default;
		$App->items = new stdClass;			
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);				
		$qryFields = array('*');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		$and = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['comuni'],'');
			}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->params->tables['comuni'],$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('nome '.$App->params->orderTypes['comuni']);	
		$App->items = Sql::getRecords();
		
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = ucfirst($_lang['mostra da %START% a %END% di %ITEM% elementi']);
		$App->paginationTitle = preg_replace('/%START%/',$App->pagination->firstPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',$App->pagination->lastPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',$App->pagination->itemsTotal,$App->paginationTitle);

		$App->pageSubTitle = preg_replace('/%ITEMS%/',$_lang['comuni'],$_lang['lista %ITEMS%']);
		$App->viewMethod = 'list';	
	break;	
}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'form':
		$App->templateApp = 'formComune.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formComune.js"></script>';
	break;

	case 'list':
	default:	
		$App->templateApp = 'listComuni.html';	
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listComuni.js"></script>';
	break;	
}	
?>