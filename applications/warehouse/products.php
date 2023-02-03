<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * warehouse/products.php v.1.3.0. 14/09/2020
*/

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

if (Core::$request->method == 'listProd' && $App->id > 0) $_SESSION[$App->sessionName]['categories_id'] = $App->id;
if (isset($_POST['categories_id']) && isset($_SESSION[$App->sessionName]['categories_id']) && $_SESSION[$App->sessionName]['categories_id'] != $_POST['categories_id']) $_SESSION[$App->sessionName]['categories_id'] = $_POST['categories_id'];

$App->categories_id = (isset($_SESSION[$App->sessionName]['categories_id']) ? $_SESSION[$App->sessionName]['categories_id'] : 0);


// select per categorie
$App->categories = new stdClass();
Subcategories::setDbTable($App->params->tables['cate']);
Subcategories::setDbTablItem($App->params->tables['prod']);
Subcategories::$fieldKey =	'id';
$App->categories = Subcategories::getObjFromSubCategories();
//print_r($App->categories);//die();

if (!is_array($App->categories) || (is_array($App->categories) && count($App->categories) == 0)) {
	$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['categoria'],Config::$localStrings['Devi creare o attivare almeno una %ITEM%'].'!'));
	ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listCate');
}

switch(Core::$request->method) {
	case 'activeProd':
	case 'disactiveProd':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {
			Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['prod'],$App->id,array('label'=>Config::$localStrings['prodotto'],'attivata'=>Config::$localStrings['attivato'],'disattivata'=>Config::$localStrings['disattivato']));
			$_SESSION['message'] = '0|'.Core::$resultOp->message;
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listProd');
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'deleteProd':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {

			// prendo i vecchi dati
			$App->itemOld = new stdClass;
			Sql::initQuery($App->params->tables['prod'],array('filename'),array($App->id),'id = ?');
			$App->itemOld = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// cancello attributi
			Sql::initQuery($App->params->tables['proa'],array(),array($App->id),'products_id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// cancello il record
			Sql::initQuery($App->params->tables['prod'],array(),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// cancello il file associato
			if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['prod'].$App->itemOld->filename)) {
				@unlink($App->params->uploadPaths['prod'].$App->itemOld->filename);
			}

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['prodotto'],Config::$localStrings['%ITEM% cancellato'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listProd');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'newProd':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		$App->item = new stdClass;
		$App->item->active = 1;
		$App->item->tax = 20.00;
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['prodotto'],Config::$localStrings['inserisci %ITEM%']);
		$App->methodForm = 'insertProd';
		$App->viewMethod = 'form';
	break;

	case 'insertProd':
		if ($_POST) {
			//Core::setDebugMode(1);

			// preleva il filename dal form
			ToolsUpload::setFilenameFormat($globalSettings['image type available']);
	   		ToolsUpload::getFilenameFromForm();
	   		if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newProd');
			}
			$_POST['filename'] = ToolsUpload::getFilenameMd5();
	   		$_POST['org_filename'] = ToolsUpload::getOrgFilename();

	   		// parsa i post in base ai campi
	   		Form::parsePostByFields($App->params->fields['prod'],Config::$localStrings,array());
	   		if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newProd');
			}

	   		Sql::insertRawlyPost($App->params->fields['prod'],$App->params->tables['prod']);
	   		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$App->id = Sql::getLastInsertedIdVar(); /* preleva l'id della pagina */
			// sposto il file
			if ($_POST['filename'] != '') {
				move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['prod'].$_POST['filename']) or die('Errore caricamento file');
			}

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['prodotto'],Config::$localStrings['%ITEM% inserito'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listProd');
			
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
		die();
	break;

	case 'modifyProd':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['prod'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }
		$App->categories_id = $App->item->categories_id;
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['prodotto'],Config::$localStrings['modifica %ITEM%']);
		$App->methodForm = 'updateProd';
		$App->viewMethod = 'form';
	break;

	case 'updateProd':
		//Core::setDebugMode(1);
		if ($_POST) {

			// preleva dati vecchio
			Sql::initQuery($App->params->tables['prod'],array('*'),array($App->id),'id = ?');
			$App->itemOld = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// preleva il filename dal form
			ToolsUpload::setFilenameFormat($globalSettings['image type available']);
			ToolsUpload::getFilenameFromForm();
	   		if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyProd/'.$App->id);
			}

			$_POST['filename'] = ToolsUpload::getFilenameMd5();
		   	$_POST['org_filename'] = ToolsUpload::getOrgFilename();
			$uploadFilename = $_POST['filename'];
			// imposta il nomefile precedente se non si è caricata un file (serve per far passare il controllo campo file presente)
			if ($_POST['filename'] == '' && $App->itemOld->filename != '') $_POST['filename'] = $App->itemOld->filename;
			if ($_POST['org_filename'] == '' && $App->itemOld->org_filename != '') $_POST['org_filename'] = $App->itemOld->org_filename;
			// opzione cancella immagine
			if (isset($_POST['deleteFile']) && $_POST['deleteFile'] == 1) {
			   if (file_exists($App->params->uploadPaths['prod'].$App->itemOld->filename)) {
					@unlink($App->params->uploadPaths['prod'].$App->itemOld->filename);
				}
				$_POST['filename'] = '';
			   	$_POST['org_filename'] = '';
			}

			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['prod'],Config::$localStrings,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyProd/'.$App->id);
			}

			Sql::updateRawlyPost($App->params->fields['prod'],$App->params->tables['prod'],'id',$App->id);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			if ($uploadFilename != '') {
				move_uploaded_file(ToolsUpload::getTempFilename(),$App->params->uploadPaths['prod'].$uploadFilename) or die('Errore caricamento file');
			   	// cancella l'immagine vecchia
				if (isset($App->itemOld->filename) && file_exists($App->params->uploadPaths['prod'].$App->itemOld->filename)) {
					@unlink($App->params->uploadPaths['prod'].$App->itemOld->filename);
				}
			}

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['prodotto'],Config::$localStrings['%ITEM% modificato'])).'!';
			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyProd/'.$App->id);
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listProd');
			}

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
		die();
	break;

	case 'pageProd':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listProd');
	break;

	case 'listProd':
	default;
		Products::setDbTable(Sql::getTablePrefix().'warehouse_products');
		Products::setDbTableCat(Sql::getTablePrefix().'warehouse_categories');
		Products::setLangUser(Config::$localStrings['user']);
		$App->items = new stdClass;
		$App->item = new stdClass;
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$qryClause = '';
		$and = '';

		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['prod'],'');
		}
		if (isset($sessClause) && $sessClause != '') $qryClause .= $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);
		}

		Products::setOptQryFieldsValues($qryFieldsValues);
		Products::setOptQryClause($qryClause);
		Products::setOptGetCategoryOwner(true);
		Sql::setItemsForPage($App->itemsForPage);
		Sql::setPage($App->page);
		Sql::setResultPaged(true);
		Sql::setOrder('title '.$App->params->ordersType['prod']);
		$App->items = Products::getProductsList($App->categories_id);
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->paginationTitle = Config::$localStrings['mostra da %START% a %END% di %ITEM% elementi'];
		$App->paginationTitle = preg_replace('/%START%/',$App->pagination->firstPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%END%/',$App->pagination->lastPartItem,$App->paginationTitle);
		$App->paginationTitle = preg_replace('/%ITEM%/',$App->pagination->itemsTotal,$App->paginationTitle);

		$App->pageSubTitle = preg_replace('/%ITEMS%/',Config::$localStrings['prodotti'],Config::$localStrings['lista dei %ITEMS%']);
		$App->viewMethod = 'list';
	break;
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {

	case 'formSeo':
		$App->templateApp = 'formSeoProduct.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formSeoProduct.js"></script>';
	break;

	case 'form':
		$App->templateApp = 'formProduct.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formProduct.js"></script>';
	break;

	case 'list':
	default:
		$App->templateApp = 'listProducts.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listProducts.js"></script>';
	break;

}
?>
