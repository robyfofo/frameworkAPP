<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/todo/items.php v.1.3.0. 24/09/2020
*/

if (isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

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
		if (isset($App->currentProject->id)) $App->item->id_project = $App->currentProject->id;
		$App->item->created = Config::$nowDateTime;
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%']);
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

			Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
			if (Core::$resultOp->error > 0) {ToolsStrings::redirect(URL_SITE.'error/404');}

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% inserito'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}	
	break;

	case 'modifyItem':	
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db');}	
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }	
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['modifica %ITEM%']);
		$App->methodForm = 'updateItem';
		$App->viewMethod = 'form';
	break;
	
	case 'updateItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); }
		if ($_POST) {			
				
			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
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
	break;

	case 'listAjaxItem':
		//Core::setDebugMode(1);
		//print_r($_REQUEST);
		//print_r($App->params->status);
		
		/* limit */		
		$limit = '';
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
		}				
		/* end limit */	
			
		/* orders */
		$orderFields = array('id','project','title','status');
		$orderTable = array();
		$order = '';	
		if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {		
			foreach ($_REQUEST['order'] AS $key=>$value)	{				
				$orderTable[] = $orderFields[$value['column']].' '.$value['dir'];
			}
		}
		if (is_array($orderTable) && count($orderTable) > 0) $order = implode(', ', $orderTable);
		/* end orders */		
		
		/* SEARCH QUERY */			
		$whereAll = '';
		$andAll = '';
		$fieldsValueAll = array();
		$where = '';
		$and = '';
		$fieldsValue = array();
		
		// permissions query 
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('fieldprefix'=>'i.','onlyuser'=>false));
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
		// end permissions items 

		// serch query

		$filtering = false;
		$whereFields = array();
		$whereFields = array(
			'project'=>array('field'=>'p.title','type'=>'datafield'),
			'status'=>array('field'=>'i.status','type'=>'datalabelint')
		);

		$wf = array();
		$wfv = array();

		if (isset($_REQUEST['search']) && is_array($_REQUEST['search']) && count($_REQUEST['search']) > 0) {		
			if (isset($_REQUEST['search']['value']) && $_REQUEST['search']['value'] != '') {
				list($w,$fv) = Sql::getClauseVarsFromAppSession($_REQUEST['search']['value'],$App->params->fields['item'],'',array('tableAlias'=>'i'));
				if ($w != '') {
					$wf[] = $w;
					$wfv = $fv;
				}
			
				// aggiungi query join		
				if (is_array($whereFields) && count($whereFields) > 0) {	
					$wf1 = array();
					$wfv1 = array();
					$valueFV = array();
					$valueF = '';
					foreach ($whereFields AS $keyqw=>$valueqw) {	
						
						switch($valueqw['type']) {	
													
							case 'datalabelint':
								$keys = preg_grep( '/'.$_REQUEST['search']['value'].'/', $App->params->status );
								if (is_array($keys) && count($keys) > 0) {
									$f = array();
									$fv = array();
									foreach ($keys AS $keyk=>$valuek) {
										$f[] = $valueqw['field'].' = ?';
										$fv[] = $keyk;
									}								
								}							
								if (isset($f) && is_array($f) && count($f) > 0) $valueF .= ' OR '.implode(' OR ',$f);
								if (isset($fv) && is_array($fv) && count($fv) > 0) $valueFV = array_merge($valueFV,$fv);						
							break;
							
							default;			 					
								$valueF  .= ' OR '.$valueqw['field'].' LIKE ?';
								$fv = array('%'.$_REQUEST['search']['value'].'%');
								$valueFV = array_merge($valueFV,$fv);							
							break;							
						}						
						
					}
						
					$wf1[] = $valueF;
					$wfv1 = $valueFV;
						
					if (is_array($wf1) && count($wf1) > 0) $wf = array_merge($wf,$wf1);
					if (is_array($wfv1) && count($wfv1) > 0) $wfv = array_merge($wfv,$wfv1);
				}
				
								
				if (is_array($wf) && count($wf) > 0) {	
					$where .= $and."(".implode('',$wf).")";
					$and = ' AND ';
				}

				if (is_array($wfv) && count($wfv) > 0) {
					$fieldsValue = array_merge($fieldsValue,$wfv);
					$filtering = true;
				} 				
			}				
		}
	
		// end search
		//echo $where;
		//print_r($fieldsValue);

		$table = $App->params->tables['item']." AS i LEFT JOIN ".$App->params->tables['prog']." AS p ON (i.id_project = p.id)";
		$fields[] = "i.*";
		$fields[] = 'p.title AS project';

		/* conta tutti i records */		
		$recordsTotal = Sql::countRecordQry($table,'i.id',$whereAll,$fieldsValueAll);
		$recordsFiltered = $recordsTotal;
		
		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),'',array());
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);
		}

		Sql::initQuery($table,$fields,$fieldsValue,$where,$order,$limit);
		$obj = Sql::getRecords();
		//echo Sql::getQuery();
		//print_r($obj);

		/* sistemo dati */	
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				$actions = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/'.($value->active == 1 ? 'disactive' : 'active').'Item/'.$value->id.'" title="'.($value->active == 1 ? ucfirst(Config::$localStrings['disattiva']).' '.Config::$localStrings['la voce'] : ucfirst(Config::$localStrings['attiva']).' '.Config::$localStrings['la voce']).'"><i class="fas fa-'.($value->active == 1 ? 'unlock' : 'lock').'"></i></a><a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/modifyItem/'.$value->id.'" title="'.ucfirst(Config::$localStrings['modifica']).' '.Config::$localStrings['la voce'].'"><i class="far fa-edit"></i></a><a class="btn btn-default btn-sm confirmdelete" href="'.URL_SITE.Core::$request->action.'/deleteItem/'.$value->id.'" title="'.ucfirst(Config::$localStrings['cancella']).' '.Config::$localStrings['la voce'].'"><i class="far fa-trash-alt"></i></a>';
				$tablefields = array(
					'id'				=> $value->id,
					'project'			=> $value->project,
					'title'				=> $value->title,
					'statuslabel'		=> (isset(Config::$localStrings[$App->params->status[$value->status]]) ? Config::$localStrings[$App->params->status[$value->status]] : $App->params->status[$value->status]),
					'actions'			=> $actions
				);
				$arr[] = $tablefields;
			}
		}
		$totalRows = Sql::getTotalsItems();
		$App->items = $arr;
		//print_r($App->items);
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
		$App->pageSubTitle = preg_replace('/%ITEMS%/',Config::$localStrings['voci'],Config::$localStrings['lista dei %ITEMS%']);
		$App->viewMethod = 'list';	
	break;	
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'form':
		$App->templateApp = 'formTodo.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formTodo.js"></script>';	
	break;

	case 'list':		
		$App->templateApp = 'listTodos.html';			
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listTodos.js"></script>';	
	break;
}	
?>