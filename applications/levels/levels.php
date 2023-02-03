<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/levels/items.php v.1.3.0. 27/08/2020
*/

if(isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if(isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

switch(Core::$request->method) {
	
	case 'activeItem':
	case 'disactiveItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {
			Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['item'],$App->id,array('label'=>Config::$localStrings['voce'],'attivata'=>Config::$localStrings['attivato'],'disattivata'=>Config::$localStrings['disattivato']));
			$_SESSION['message'] = '0|'.Core::$resultOp->message;
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');	
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'deleteItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {
			Sql::initQuery($App->params->tables['item'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% cancellato'])).'!';	
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');		
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;
	
	case 'newItem':		
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		$App->item = new stdClass;		
		$App->item->active = 1;
		$App->item->modules = array();		
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%']);
		$App->methodForm = 'insertItem';
		$App->viewMethod = 'form';	
	break;
	
	case 'insertItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); }
		if ($_POST) {
			
			// forzo il modulo home se non è settato
			$_POST['modules_read'][$App->module_home_id] = 1;
			
			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newItem');
			}

			Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);				
			if (Core::$resultOp->error > 0) { die();ToolsStrings::redirect(URL_SITE.'error/db'); }
			
			// prende ultimo id
			$App->id = Sql::getLastInsertedIdVar();
			// asserra i record con lo stesso livello
			Sql::initQuery($App->params->tables['ass-item'],array(),array($App->id),'levels_id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db');die(); }	
			
			// memorizzo associazioni
			foreach($App->modules AS $sectionKey=>$sectionModules) {
				foreach($sectionModules AS $module) {					
					$accessread = (isset($_POST['modules_read'][$module->id]) ? $_POST['modules_read'][$module->id] : 0);
					$accesswrite = (isset($_POST['modules_write'][$module->id]) ? $_POST['modules_write'][$module->id] : 0);
					
					Sql::initQuery($App->params->tables['ass-item'],array('modules_id','users_id','levels_id','read_access','write_access'),array($module->id,'0',$App->id,$accessread,$accesswrite),'');
					Sql::insertRecord();
					if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db');die(); }
				}
			}
				
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% inserito']));
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
						
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'modifyItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); }
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();		
		$App->level_modules = Permissions::getLevelModulesRights($App->id);
		//print_r($App->level_modules);
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['modifica %ITEM%']);
		$App->methodForm = 'updateItem';
		$App->viewMethod = 'form';
	break;

	case 'updateItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); }
		if ($_POST) {

			// forzo il modulo home se non è settato
			$_POST['modules_read'][$App->module_home_id] = 1;
						
			// asserra i record con lo stesso livello
			Sql::initQuery($App->params->tables['ass-item'],array(),array($App->id),'levels_id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db');die(); }	
			
			// memorizzo associazioni
			foreach($App->modules AS $sectionKey=>$sectionModules) {
				foreach($sectionModules AS $module) {					
					$accessread = (isset($_POST['modules_read'][$module->id]) ? $_POST['modules_read'][$module->id] : 0);
					$accesswrite = (isset($_POST['modules_write'][$module->id]) ? $_POST['modules_write'][$module->id] : 0);
					
					Sql::initQuery($App->params->tables['ass-item'],array('modules_id','users_id','levels_id','read_access','write_access'),array($module->id,'0',$App->id,$accessread,$accesswrite),'');
					Sql::insertRecord();
					if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db');die(); }
				}
			}
						
			Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
			}
			
			Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db');die(); }	
			
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% modificato']));
			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
			}				
			
			
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'pageItem':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
	break;

	case 'listItem':
	default:	
		$App->item = new stdClass;						
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);
		$qryFields = array('*');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['item'],'');
			}		
		if (isset($sessClause) && $sessClause != '') $clause .= $sessClause;
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->params->tables['item'],$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo i dati */
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $value) {	
				
				$App->level_modules = Permissions::getLevelModulesRights($value->id);
				$modules = array();
				foreach ($App->level_modules AS $k1=>$v1) {	
					if ($v1->read_access == 1 || $v1->write_access == 1) {
    					$modules[] = $v1->module_name;   	
    				}
										
				}
				$value->modules = implode(', ',$modules);

				$arr[] = $value;
			}
		}
		$App->items = $arr;
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->pageSubTitle = preg_replace('/%ITEMS%/',Config::$localStrings['voci'],Config::$localStrings['lista dei %ITEMS%']);		
		$App->viewMethod = 'list';	
	break;	
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {	
	case 'form':
		$App->templateApp = 'formLevel.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formLevel.js"></script>';
	break;

	case 'list':
		$App->templateApp = 'listLevels.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listLevels.js"></script>';
	break;	
	default:
	break;
}