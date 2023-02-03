<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/projects/projects.php v.1.3.0. 25/09/2020
*/

if (isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

$App->id_cat = 0;

/* GESTIONE CUSTOMERS */
$App->icustomers = new stdClass;	
Sql::initQuery($App->params->tables['cust'],array('*'),array(),'active = 1 AND (id_type = 2 OR id_type = 3)');
Sql::setOptions(array('fieldTokeyObj'=>'id'));
Sql::setOrder('ragione_sociale ASC');
$App->customers = Sql::getRecords();

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

			// cancella le timecard con il progetto associato
			Sql::initQuery($App->params->tables['time'],array('id'),array($App->id),'id_project = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// cancella i todo con il progetto associato
			Sql::initQuery($App->params->tables['todo'],array('id'),array($App->id),'id_project = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			Sql::initQuery($App->params->tables['item'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% cancellato'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;
	
	case 'currentItem':
		if ($App->id > 0) {
			Sql::initQuery($App->params->tables['item'],array('current'),array('0'));
			Sql::updateRecord();
			if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db');}
			Sql::initQuery($App->params->tables['item'],array('current'),array('1',$App->id),'id = ?');
			Sql::updateRecord();
			if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db');}
			$_SESSION['message'] = '0|'.ucfirst(Config::$localStrings['voce corrente']).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404'); die();
		}
	break;
	
	case 'timecardItem':
		if ($App->id > 0) {
			Sql::switchFieldOnOff($App->params->tables['item'],'timecard','id',$App->id,array('labelOn'=>ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['timecard'],Config::$localStrings['%ITEM% attivata'])).'!','labelOff'=>ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['timecard'],Config::$localStrings['%ITEM% disattivata'])).'!'));
			}		
		$App->viewMethod = 'list';	
	break;
	
	case 'newItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		$App->item_read_write_access = 2; // 0 = no access; 1 = read; 2 = read write; 
		$App->item = new stdClass;		
		$App->item->active = 1;
		$App->item->id_contact = 0;
		$App->item->created = Config::$nowDateTime;
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%']);
		$App->defaultJavascript = "var idproject = '0';";
		$App->methodForm = 'insertItem';
		$App->viewMethod = 'form';	
	break;
	
	case 'insertItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); }
		if ($_POST) {

			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newItem');
			}

			// se current uguale 1 azzerra tutti gli altri
			if ($_POST['current'] == 1) {
				Sql::initQuery($App->params->tables['item'],array('current'),array('0'));
				Sql::updateRecord();
				if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db');}
			}		

			Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
			if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db');}

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% inserito'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}	
	break;

	case 'modifyItem':		
		
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }

		$App->item_read_write_access = Permissions::checkReadWriteAccessOfItem($App->params->tables['item'],$App->id,$App->userLoggedData); // 0 = no access; 1 = read; 2 = read write; 
		if ($App->item_read_write_access == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }

		// preleva i todo del progetto 
		$App->item_todo = new stdClass;
		Sql::initQuery($App->params->tables['todo'],array('*'),array($App->id),'active = 1 AND id_project = ?');
		$obj = Sql::getRecords();
		if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db');}
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				$s = $App->params->statusTodo[$value->status];
				$value->statusLabel = (isset(Config::$localStrings[$App->params->statusTodo[$value->status]]) ? Config::$localStrings[$App->params->statusTodo[$value->status]] : $App->params->statusTodo[$value->status]);
				$arr[] = $value;
			}
		}
		$App->item_todo = $arr;

		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db');}
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }

		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['modifica %ITEM%']);
		$App->defaultJavascript = "var idproject = '".$App->id."';";
		$App->methodForm = 'updateItem';
		$App->viewMethod = 'form';
		
	break;
	
	case 'updateItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); }

		$App->item_read_write_access = Permissions::checkReadWriteAccessOfItem($App->params->tables['item'],$App->id,$App->userLoggedData); // 0 = no access; 1 = read; 2 = read write; 
		//if ($App->item_read_write_access < 2) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		$App->item_read_write_access = 0;
		if ($App->item_read_write_access == 2) { // permette solo se read write = 2

			if ($_POST) {	

				// parsa i post in base ai campi
				Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());
				if (Core::$resultOp->error > 0) {
					$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
				}

				// se current uguale 1 azzerra tutti gli altri
				if ($_POST['current'] == 1) {
					Sql::initQuery($App->params->tables['item'],array('current'),array('0'));
					Sql::updateRecord();
					if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db');}
				}

				Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% modificato'])).'!';
				if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
				} else {
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
				}

			} else {
				ToolsStrings::redirect(URL_SITE.'error/404');
			}
		} else {
			$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['non hai il permesso per modificare il %ITEM%'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
		}
	break;
	
	case 'listAjaxItem':
		//Core::setDebugMode(1);
		//print_r($_REQUEST);
		
		/* limit */		
		$limit = '';
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
		}				
		/* end limit */	
			
		/* orders */
		$orderFields = array('id','title','status','completato');
		$order = array();	
		if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {		
			foreach ($_REQUEST['order'] AS $key=>$value)	{				
				$order[] = $orderFields[$value['column']].' '.$value['dir'];
			}
		}
		/* end orders */		
			
		/* SEARCH QUERY */			
		$whereAll = '';
		$andAll = '';
		$fieldsValueAll = array();
		$where = '';
		$and = '';
		$fieldsValue = array();
				
		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('fieldprefix'=>'','onlyuser'=>false));
		if (isset($permClause) && $permClause != '') {
			$whereAll .= $andAll.'('.$permClause.')';
			$andAll = ' AND ';
			$where .= $and.'('.$permClause.')';
			$and = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$fieldsValueAll = array_merge($fieldsValueAll,$fieldsValuesPermClause);
			$fieldsValue = array_merge($fieldsValue,$fieldsValuesPermClause);	
		}
		/* end permissions items */
		
		/* SEARCH QUERY */
		$filtering = false;
		if (isset($_REQUEST['search']) && is_array($_REQUEST['search']) && count($_REQUEST['search']) > 0) {		
			if (isset($_REQUEST['search']['value']) && $_REQUEST['search']['value'] != '') {
				list($w,$fv) = Sql::getClauseVarsFromAppSession($_REQUEST['search']['value'],$App->params->fields['item'],'');
				if ($w != '') {
					$where .= $and."(".$w.")";
					$and = ' AND ';
				}
				if (is_array($fv) && count($fv) > 0) {
					$fieldsValue = array_merge($fieldsValue,$fv);
					$filtering = true;
				}
			}
		}
		/* END SEARCH QUERY */
		
		$table = $App->params->tables['item'];
		$fields[] = '*';
		
		/* conta tutti i records */		
		$recordsTotal = Sql::countRecordQry($table,'id',$whereAll,$fieldsValueAll);
		$recordsFiltered = $recordsTotal;
		
		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),'',array());
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);
		}

		Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),$limit);
		if ($Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		
		/* sistemo dati */	
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				$actions = '<a class="btn btn-sm btn-default" href="'.URL_SITE.Core::$request->action.'/'.($value->active == 1 ? 'disactive' : 'active').'Item/'.$value->id.'" title="'.($value->active == 1 ? ucfirst(Config::$localStrings['disattiva']).' '.Config::$localStrings['la voce'] : ucfirst(Config::$localStrings['attiva']).' '.Config::$localStrings['la voce']).'"><i class="fas fa-'.($value->active == 1 ? 'unlock' : 'lock').'"> </i></a><a class="btn btn-sm btn-default" href="'.URL_SITE.Core::$request->action.'/modifyItem/'.$value->id.'" title="'.ucfirst(Config::$localStrings['modifica']).' '.Config::$localStrings['la voce'].'"><i class="far fa-edit"> </i></a><a class="btn btn-sm btn-default confirmdelete" href="'.URL_SITE.Core::$request->action.'/deleteItem/'.$value->id.'" title="'.ucfirst(Config::$localStrings['cancella']).' '.Config::$localStrings['la voce'].'"><i class="far fa-trash-alt"></i></a>';
				
				$timecards = '<button type="button" href="'.URL_SITE.Core::$request->action.'/getTimecardsProjectAjax/'.$value->id.'" data-remote="false" data-target="#myModal" data-toggle="modal" title="'.ucfirst(Config::$localStrings['mostra tempo lavorato al progetto']).'" class="btn btn-sm btn-default"><i class="far fa-clock"></i></button>';											
				$options = '<a class="btn btn-sm '.($value->timecard == 1 ? 'btn-info' : 'btn-warning').'" href="'.URL_SITE.Core::$request->action.'/timecardItem/'.$value->id.'" title="'.($value->timecard == 1 ? ucfirst(Config::$localStrings['non associa timecard']) : ucfirst(Config::$localStrings['associa timecard'])).'"><i class="'.($value->timecard == 1 ? 'far fa-clock' : 'fas fa-ban').'"></i></a>&nbsp;
				<a class="btn btn-sm btn-default" href="'.URL_SITE.Core::$request->action.'/currentItem/'.$value->id.'" title="'.($value->current == 1 ? ucfirst(Config::$localStrings['imposta come non corrente']) : ucfirst(Config::$localStrings['imposta come corrente'])).'"><i class="'.($value->current == 1 ? 'fas fa-star' : 'far fa-star').'"></i></a>';

				$tablefields = array(
					'id'=>$value->id,
					'title'=>$value->title,
					'status'=>'(<small>'.$value->status.'</small>) '.ucfirst(isset(Config::$localStrings[$App->params->status[$value->status]]) ? Config::$localStrings[$App->params->status[$value->status]] : $App->params->status[$value->status]),
					'completato'=>$value->completato.' %',
					'times'=>$timecards,
					'opts'=>$options,
					'actions'=>$actions
					);
				$arr[] = $tablefields;
			}
		}
		$App->items = $arr;
		
		$json = array();
		$json['draw'] = intval($_REQUEST['draw']);
		$json['recordsTotal'] = intval($recordsTotal);
		$json['recordsFiltered'] = intval($recordsFiltered);		
		$json['data'] = $App->items;	
		echo json_encode($json);
		die();
	break;

	case 'listItem':
	default;	
		$App->viewMethod = 'list';	
	break;	
}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	
	case 'form':
		$App->templateApp = 'formProject.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formProject.js"></script>';
	break;

	case 'list':
	default:

		$App->pageSubTitle = preg_replace('/%ITEMS%/',Config::$localStrings['voci'],Config::$localStrings['lista dei %ITEMS%']);
		$App->templateApp = 'listProjects.html';			
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listProjects.js"></script>';	
	break;

}	
?>
