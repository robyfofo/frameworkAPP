<?php
/**
* Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * warehouse/subcategories.php v.1.3.0. 14/09/2020
*/

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

if (Core::$request->method == 'listCate' && $App->id > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_cat',$App->id);

Subcategories::setDbTable($App->params->tables['cate']);
Subcategories::setDbTablItem($App->params->tables['prod']);

switch(Core::$request->method) {
	case 'activeCate':
	case 'disactiveCate':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {
			Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['cate'],$App->id,array('label'=>$_lang['categoria'],'attivata'=>$_lang['attivata'],'disattivata'=>$_lang['disattivata']));
			$_SESSION['message'] = '0|'.Core::$resultOp->message;
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listCate');
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'deleteCate':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {

			Sql::initQuery($App->params->tables['cate'],array('id'),array($App->id),'parent = ?');
			$count = Sql::countRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			if ($count > 0) {
				$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',$_lang['categorie'],$_lang['Errore! Ci sono ancora %ITEM% associate!']));
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listCate');
			}

			// controlla se ha prodotti associati
			Sql::initQuery($App->params->tables['prod'],array('id'),array($App->id),'categories_id = ?');
			$count = Sql::countRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			if ($count > 0) {
				$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',$_lang['prodotti'],$_lang['Errore! Ci sono ancora %ITEM% associati!']));
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listCate');
			}

			// cancello il record
			Sql::initQuery($App->params->tables['cate'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['categoria'],$_lang['%ITEM% cancellata'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listCate');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
		die();
	break;

	case 'newCate':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		$App->item = new stdClass;
		$App->item->active = 1;
		$App->item->ordering = 0;

		$App->itemTags = array();

		// select per parent
		$App->categories = new stdClass();
		Subcategories::$levelString = '-->';
		$App->categories = Subcategories::getObjFromSubCategories();

		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['categoria'],$_lang['inserisci %ITEM%']);
		$App->methodForm = 'insertCate';
		$App->viewMethod = 'form';
	break;

	case 'insertCate':
		if ($_POST) {

	   		// parsa i post in base ai campi
	   		Form::parsePostByFields($App->params->fields['cate'],$_lang,array());
	   		if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newCate');
			}

	   		Sql::insertRawlyPost($App->params->fields['cate'],$App->params->tables['cate']);
	   		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$App->id = Sql::getLastInsertedIdVar(); /* preleva l'id della pagina */
			// sposto il file

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['categoria'],$_lang['%ITEM% inserita'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listCate');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'modifyCate':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		$App->item = new stdClass;

		// select per parent
		$App->subCategories = new stdClass();
		Subcategories::$hideId = 1;
		Subcategories::$hideSons = 1;
		Subcategories::$rifId = 'id';
		Subcategories::$rifIdValue = $App->id;
		Subcategories::$levelString = '-->';
		$App->categories = Subcategories::getObjFromSubCategories(array());

		Sql::initQuery($App->params->tables['cate'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }

		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['categoria'],$_lang['modifica %ITEM%']).'!';
		$App->methodForm = 'updateCate';
		$App->viewMethod = 'form';
	break;

	case 'updateCate':
		if ($_POST) {

			// preleva dati vecchio
			Sql::initQuery($App->params->tables['cate'],array('*'),array($App->id),'id = ?');
			$App->itemOld = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['cate'],$_lang,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyCate/'.$App->id);
			}

			Sql::updateRawlyPost($App->params->fields['cate'],$App->params->tables['cate'],'id',$App->id);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['categoria'],$_lang['%ITEM% modificata'])).'!';
			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyCate/'.$App->id);
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listCate');
			}

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'pageCate':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listCate');
	break;

	case 'listCate':
	default;
		//Core::setDebugMode(1);
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 10);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);

		Sql::setItemsForPage($App->itemsForPage);
		Sql::setPage($App->page);
		Sql::setResultPaged(true);

		$App->renderSub = 1;

		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			$qryFields = array('cat.*');
			$qryFields[] = "(SELECT COUNT(ite.id) FROM ".$App->params->tables['prod']." AS ite WHERE ite.categories_id = cat.id) AS items";
			$qryFieldsValues = array();
			$qryFieldsValuesClause = array();
			$clause = '';
			$and = '';

			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['cate'],'');

			if (isset($sessClause) && $sessClause != '') $clause .= $and.'('.$sessClause.')';
			if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
				$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);
			}
			Sql::initQuery($App->params->tables['cate']." AS cat",$qryFields,$qryFieldsValues,$clause);
			Sql::setItemsForPage($App->itemsForPage);
			Sql::setPage($App->page);
			Sql::setResultPaged(true);
			Sql::setOrder('title '.$App->params->orderTypes['cate']);
			$obj = Sql::getRecords();
			$App->items = $obj;

		} else {
			$App->renderSub = 0;
			$App->items = Subcategories::getObjFromSubCategories();
		}

		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = ucfirst($_lang['mostra da %START% a %END% di %ITEM% elementi']);
		$App->paginationTitle = preg_replace('/%START%/',$App->pagination->firstPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',$App->pagination->lastPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',$App->pagination->itemsTotal,$App->paginationTitle);

		$App->pageSubTitle = preg_replace('/%ITEMS%/',$_lang['categorie'],$_lang['lista delle %ITEMS%']);
		$App->viewMethod = 'list';
	break;
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {

	case 'form':
		$App->templateApp = 'formCategory.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formCategory.js"></script>';
	break;

	case 'list':
	default:
		$App->templateApp = 'listCategories.html';
		$App->css[] = '<link href="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/jquery.treegrid/jquery.treegrid.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/jquery.cookie/jquery.cookie.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.'templates/'.$App->templateUser.'/plugins/jquery.treegrid/jquery.treegrid.min.js" type="text/javascript"></script>';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listCategories.js"></script>';
	break;

}
?>
