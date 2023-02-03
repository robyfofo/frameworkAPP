<?php
/* app/location/Province.php v.1.3.0. 20/09/2020 */

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch (Core::$request->method) {

	case 'activeProvin':
	case 'disactiveProvin':
		Sql::manageFieldActive(substr(Core::$request->method,0,-6),$App->params->tables['province'],$App->id,array('label'=>$_lang['provincia'],'attivata'=>$_lang['attivata'],'disattivata'=>$_lang['disattivata']));
		$_SESSION['message'] = '0|'.Core::$resultOp->message;
		ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listProvin');	
	break;
	
	case 'deleteProvin':
		if ($App->id > 0) {

			// cancello il record
			Sql::initQuery($App->params->tables['province'],array(),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['provincia'],$_lang['%ITEM% cancellata'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listProvin');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;	

	case 'newProvin':
		$App->item = new stdClass;
		$App->item->created = $App->nowDateTime;
		$App->item->active = 1;
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['provincia'],$_lang['inserisci %ITEM%']);
		$App->methodForm = 'insertProvin';
		$App->viewMethod = 'form';	
	break;
	
	case 'insertProvin':
		if ($_POST) {
			
			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['province'],$_lang,array());
			if (Core::$resultOp->error > 0) {
			 	$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
			 	ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newProvin');
			}
			 
			Sql::insertRawlyPost($App->params->fields['province'],$App->params->tables['province']);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$App->id = Sql::getLastInsertedIdVar(); /* preleva l'id della pagina */
			  
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['provincia'],$_lang['%ITEM% inserita'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listProvin');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'modifyProvin':	
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['province'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['provincia'],$_lang['modifica %ITEM%']);
		$App->methodForm = 'updateProvin';	
		$App->viewMethod = 'form';
	break;
	
	case 'updateProvin':
		if ($_POST) {

			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['province'],$_lang,array());
			if (Core::$resultOp->error > 0) {
					$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newProvin');
			}
			
			Sql::updateRawlyPost($App->params->fields['province'],$App->params->tables['province'],'id',$App->id);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['provincia'],$_lang['%ITEM% modificata'])).'!';
			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyProvin/'.$App->id);
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listProvin');
			}
			   
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}	break;
	
	case 'pageProvin':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);

	case 'listProvin':
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
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['province'],'');
			}	
		if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->params->tables['province'],$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		Sql::setOrder('nome '.$App->params->orderTypes['province']);	
		$App->items = Sql::getRecords();
		
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = ucfirst($_lang['mostra da %START% a %END% di %ITEM% elementi']);
		$App->paginationTitle = preg_replace('/%START%/',$App->pagination->firstPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',$App->pagination->lastPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',$App->pagination->itemsTotal,$App->paginationTitle);

		$App->pageSubTitle = preg_replace('/%ITEMS%/',$_lang['province'],$_lang['lista %ITEMS%']);
		$App->viewMethod = 'list';	
	break;	
}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'form':
		$App->templateApp = 'formProvincia.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formProvincia.js"></script>';
	break;

	case 'list':
	default:	
		$App->templateApp = 'listProvince.html';	
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listProvince.js"></script>';
	break;	
}	
?>