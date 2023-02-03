<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/users/users.php v.1.3.0. 24/09/2020
*/

if(isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if(isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

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
		$App->province = new stdClass;
		Sql::initQuery($App->params->tables['province'],array('*'),array(),'active = 1','nome ASC');
		$App->province = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$App->nations = new stdClass;
		Sql::initQuery($App->params->tables['nations'],array('*'),array(),'active = 1','title_'.Config::$localStrings['user'].' ASC');
		$App->nations = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		$App->item = new stdClass;
		$App->item->created = Config::$nowDateTime;
		$App->item->active = 1;
		$App->item->id_level = 0;
		$App->templatesAvaiable = $Module->getUserTemplatesArray();
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['utente'],Config::$localStrings['inserisci %ITEM%']);
		$App->methodForm = 'insertItem';
		$App->viewMethod = 'form';
	break;

	case 'insertItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); }
		if ($_POST) {
			$_POST['is_root'] = 0;
			if (!isset($_POST['created'])) $_POST['created'] = Config::$nowDateTime;
			if (!isset($_POST['active'])) $_POST['active'] = 0;

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
				Sql::initQuery($App->params->tables['nations'],array('title_'.Config::$localStrings['user']),array(intval($_POST['location_nations_id'])),'id = ? AND active = 1');
				$App->nation = Sql::getRecord();
				$field = 'title_'.Config::$localStrings['user'];
				if (isset($App->nation->$field)) {
					$_POST['nation'] =$App->nation->$field;
				}
			} else {
				$_POST['location_nations_id'] = 0;
			}

			// recupero dati avatar
			list($_POST['avatar'],$_POST['avatar_info']) = $Module->getAvatarData(0,Config::$localStrings);
			if ($Module->errorType > 0) {
				if ($Module->message != '') Core::$resultOp->messages[] = $Module->message;
				Core::$resultOp->error =  $Module->error;
				Core::$resultOp->type =  $Module->errorType;
				$App->formTabActive = 4;
			}

			// controllo password
			$_POST['password'] = $Module->checkPassword(0,Config::$localStrings);
			if ($Module->error > 0) {
				if ($Module->message != '') Core::$resultOp->message[] = $Module->message;
				Core::$resultOp->type =  $Module->errorType;
				Core::$resultOp->error =  0;
				$App->formTabActive = 1;
			}

			// controllo nome utente
			$_POST['username'] = $Module->checkUsername(0,Config::$localStrings);
			if ($Module->error > 0) {
				if ($Module->message != '') Core::$resultOp->messages[] = $Module->message;
				Core::$resultOp->type =  $Module->errorType;
				Core::$resultOp->error =  1;
				$App->formTabActive = 1;
			}

			// controllo email univoca */
			$_POST['email'] = $Module->checkEmail(0,Config::$localStrings);
			if ($Module->error > 0) {
				if ($Module->message != '') Core::$resultOp->messages[] = $Module->message;
				Core::$resultOp->type =  $Module->errorType;
				Core::$resultOp->error =  1;
				$App->formTabActive = 1;
			}

			if (Core::$resultOp->error == 0) {
				$_POST['hash'] = password_hash(SITE_CODE_KEY.$_POST['username'].$_POST['email'],PASSWORD_DEFAULT);
				$_POST['hash'] = SanitizeStrings::base64url_encode($_POST['hash']);

				// parsa i post in base ai campi
				Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());
				if (Core::$resultOp->error > 0) {
					$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newItem');
				}

				Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
				if (Core::$resultOp->error > 0) {ToolsStrings::redirect(URL_SITE.'error/db');}

				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['utente'],Config::$localStrings['%ITEM% inserito'])).'!';
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');

			} else {
				$_SESSION[$App->sessionName]['formTabActive'] = $App->formTabActive;
				$_SESSION['message'] = Core::$resultOp->type.'|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newItem');
			}

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'modifyItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		
		$App->province = new stdClass;
		Sql::initQuery($App->params->tables['province'],array('*'),array(),'active = 1','nome ASC');
		$App->province = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$App->nations = new stdClass;
		Sql::initQuery($App->params->tables['nations'],array('*'),array(),'active = 1','title_'.Config::$localStrings['user'].' ASC');
		$App->nations = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }	
	
		$App->item = new stdClass;
		$App->templatesAvaiable = $Module->getUserTemplatesArray();
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }
		
		$App->comune = new stdClass;
		$App->comune->selected = new stdClass;	
		if (isset($App->item->location_comuni_id) && $App->item->location_comuni_id > 0) {
			$App->comune->selected->id = $App->item->location_comuni_id;
			$App->comune->selected->nome = $App->item->city;
		}		
								
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['utente'],Config::$localStrings['modifica %ITEM%']);
		$App->methodForm = 'updateItem';
		$App->viewMethod = 'form';
	break;

	case 'updateItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); }
		if ($_POST) {
			$_POST['is_root'] = 0;
			if (!isset($_POST['created'])) $_POST['created'] = Config::$nowDateTime;
			if (!isset($_POST['active'])) $_POST['active'] = 0;

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
				Sql::initQuery($App->params->tables['nations'],array('title_'.Config::$localStrings['user']),array(intval($_POST['location_nations_id'])),'id = ? AND active = 1');
				$App->nation = Sql::getRecord();
				$field = 'title_'.Config::$localStrings['user'];
				if (isset($App->nation->$field)) {
					$_POST['nation'] =$App->nation->$field;
				}
			} else {
				$_POST['location_nations_id'] = 0;
			}

			/* requpero i vecchi dati */
			$App->oldItem = new stdClass;
			Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
			$App->oldItem = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// recupero dati avatar
			list($_POST['avatar'],$_POST['avatar_info']) = $Module->getAvatarData($App->id,Config::$localStrings);
			if ($Module->errorType > 0) {
				if ($Module->message != '') $Core::$resultOp->messages[] = $Module->message;
				Core::$resultOp->type =  $Module->errorType;
				Core::$resultOp->error =  $Module->error;
				$App->formTabActive = 4;
			}

			// controllo password
			$_POST['password'] = $Module->checkPassword($App->id,Config::$localStrings);
			if ($Module->errorType > 0) {
				if ($Module->message != '') Core::$resultOp->message = $Module->message;
				Core::$resultOp->type =  $Module->errorType;
				Core::$resultOp->error =  0;
				$App->formTabActive = 1;
			}

			// controllo nome utente
			if ($_POST['username'] != $App->oldItem->username) {
				$_POST['username'] = $Module->checkUsername($App->id,Config::$localStrings);
				if ($Module->errorType > 0) {
					if ($Module->message != '') Core::$resultOp->messages[] = $Module->message;
					Core::$resultOp->type =  $Module->errorType;
					Core::$resultOp->error =  1;
					$App->formTabActive = 1;
				}
			}

			// controllo email univoca
			if ($_POST['email'] != $App->oldItem->email) {
				$_POST['email'] = $Module->checkEmail($_POST['id'],Config::$localStrings);
				if ($Module->errorType > 0) {
					if ($Module->message != '') Core::$resultOp->messages[] = $Module->message;
					Core::$resultOp->type =  $Module->errorType;
					Core::$resultOp->error =  1;
					$App->formTabActive = 1;
				}
			}

			if (Core::$resultOp->error == 0) {

				$_POST['hash'] = password_hash(SITE_CODE_KEY.$_POST['username'].$_POST['email'],PASSWORD_DEFAULT);
				$_POST['hash'] = SanitizeStrings::base64url_encode($_POST['hash']);

				// parsa i post in base ai campi
				Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());
				if (Core::$resultOp->error > 0) {
					$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
				}

				Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['utente'],Config::$localStrings['%ITEM% modificato'])).'!';
				if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
				} else {
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
				}

			} else {
				$_SESSION[$App->sessionName]['formTabActive'] = $App->formTabActive;
				$_SESSION['message'] = Core::$resultOp->type.'|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem');
			}

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'checkUserAjaxItem':
		$count = $Module->checkUsernameAjax($_POST['id'],$_POST['username']);
		if ($count > 0) {
			echo '<span style="color:red;">'.ucfirst(preg_replace('/%USERNAME%/',$_POST['username'],Config::$localStrings['username <strong>%USERNAME%</strong> risulta già presente nel nostro database'])).'!</span>';
		} else {
			echo '<span style="color:green;">'.ucfirst(preg_replace('/%USERNAME%/',$_POST['username'],Config::$localStrings['username <strong>%USERNAME%</strong> è libero'])).'!</span>';
		}
		$renderTpl = false;
		die();
	break;

	case 'checkEmailAjaxItem':
		$count = $Module->checkEmailAjax($_POST['id'],$_POST['email']);
		if($count > 0) {
			echo '<span style="color:red;">'.ucfirst(preg_replace('/%EMAIL%/',$_POST['email'],Config::$localStrings['indirizzo <strong>%EMAIL%</strong> risulta già presente nel nostro database'])).'!</span>';
		} else {
			echo '<span style="color:green;">'.ucfirst(preg_replace('/%EMAIL%/',$_POST['email'],Config::$localStrings['indirizzo <strong>%EMAIL%</strong> è libero'])).'!</span>';
		}
		$renderTpl = false;
		die();
	break;

	case 'pageItem':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
	break;

	case 'listItem':
	default;
		$App->item = new stdClass;
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);
		$qryFields = array('*');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = 'is_root = 0';
		$and = " AND ";
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['item'],'');
		}
		if (isset($sessClause) && $sessClause != '') $clause .=  $and.'('.$sessClause.')';
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);
		}
		Sql::initQuery($App->params->tables['item'],$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);
		Sql::setPage($App->page);
		Sql::setResultPaged(true);
		if (Core::$resultOp->error <> 1) $App->items = Sql::getRecords();
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->pageSubTitle =  preg_replace('/%ITEMS%/',Config::$localStrings['utenti'],Config::$localStrings['lista degli %ITEMS%']);
		$App->viewMethod = 'list';
	break;
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'form':
		$App->templateApp = 'formUser.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formUser.js"></script>';
	break;

	case 'list':
	default:
		$App->templateApp = 'listUsers.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listUsers.js"></script>';
	break;
	}
?>
