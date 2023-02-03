<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * timecard/pitems.php v.1.2.0. v.1.2.0. 31/01/2020
*/

if (isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);


switch(Core::$request->method) {
	case 'activePite':
	case 'disactivePite':
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['pite'],$App->id,$opt=array('labelA'=>Config::$localStrings['voce'].' '.Config::$localStrings['attivata'],'labelD'=>Config::$localStrings['voce'].' '.Config::$localStrings['disattivata']));
		$App->viewMethod = 'list';
	break;
	
	case 'deletePite':
		if ($App->id > 0) {
			$App->itemOld = new stdClass;
			Sql::initQuery($App->params->tables['pite'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error == 0) {
				Core::$resultOp->message = 'aaa'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% cancellata'])).'!';
			}				
		}		
		$App->viewMethod = 'list';
	break;
	
	case 'newPite':
		$App->pageSubTitle =preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%']);
		$App->viewMethod = 'formNew';	
	break;
	
	case 'insertPite':
		if ($_POST) {
			if (Core::$resultOp->error == 0) {				
				/* parsa i post in base ai campi */
				Form::parsePostByFields($App->params->fields['pite'],Config::$localStrings,array());
				if (Core::$resultOp->error == 0) {						
					/* controlla l'intervallo */
					$datatimeisoini = Config::$nowDate .' '.$_POST['starttime'];
					$datatimeisoend = Config::$nowDate .' '.$_POST['endtime'];				
					DateFormat::checkDateTimeIsoIniEndInterval($datatimeisoini,$datatimeisoend,'>');
					if (Core::$resultOp->error == 0) {
						$dteStart = new DateTime($datatimeisoini);
						$dteEnd   = new DateTime($datatimeisoend); 
						$dteDiff  = $dteStart->diff($dteEnd);
						$_POST['worktime'] = $dteDiff->format("%H:%I");
						} else {
      					Core::$resultOp->message = Config::$localStrings['La ora inizio deve essere prima della ora fine!'];	 
							}					
					if (Core::$resultOp->error == 0) {
						Sql::insertRawlyPost($App->params->fields['pite'],$App->params->tables['pite']);
						if (Core::$resultOp->error == 0) {
		   				}			   			
		   			}			   		
					}
				}
			} else {
				Core::$resultOp->error = 1;
				}			
		list($id,$App->viewMethod,$App->pageSubTitle,Core::$resultOp->message) = Form::getInsertRecordFromPostResults(0,Core::$resultOp,array('label inserted'=>preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% inserita']),'label insert'=>preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%'])));				
	break;

	case 'modifyPite':				
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['modifica %ITEM%']);
		$App->viewMethod = 'formMod';
	break;
	
	case 'updatePite':
		if ($_POST) {			
			/* parsa i post in base ai campi */ 	
			Form::parsePostByFields($App->params->fields['pite'],Config::$localStrings,array());
			if (Core::$resultOp->error == 0) {
				/* controlla l'intervallo */
				$datatimeisoini = Config::$nowDate .' '.$_POST['starttime'];
				$datatimeisoend = Config::$nowDate .' '.$_POST['endtime'];				
				DateFormat::checkDateTimeIsoIniEndInterval($datatimeisoini,$datatimeisoend,'>');
				if (Core::$resultOp->error == 0) {						
					$dteStart = new DateTime($datatimeisoini);
					$dteEnd   = new DateTime($datatimeisoend); 
					$dteDiff  = $dteStart->diff($dteEnd);
					$_POST['worktime'] = $dteDiff->format("%H:%I");
					} else {
      				Core::$resultOp->message = Config::$localStrings['La ora inizio deve essere prima della ora fine!'];	 
						}
				if (Core::$resultOp->error == 0) {		
					Sql::updateRawlyPost($App->params->fields['pite'],$App->params->tables['pite'],'id',$App->id);
					if(Core::$resultOp->error == 0) {
				   	}				   					
					}			
				}
			} else {
				Core::$resultOp->error = 1;
				}			
		list($id,$App->viewMethod,$App->pageSubTitle,Core::$resultOp->message) = Form::getUpdateRecordFromPostResults($App->id,Core::$resultOp,array('label done'=>Config::$localStrings['modifiche effettuate'],'label modified'=>preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% modificata']),'label modify'=>preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['modifica %ITEM%']),'label insert'=>preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%'])));	
	break;	
		
	case 'pagePite':
		$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'page',$App->id);
		$App->viewMethod = 'list';	
	break;

	case 'messagePite':
		Core::$resultOp->error = $App->id;
		Core::$resultOp->message = urldecode(Core::$request->params[0]);
		$App->viewMethod = 'list';
	break;
	
	case 'listAjaxPite':
		//Core::setDebugMode(1);
		//print_r($_REQUEST);
		/* limit */		
		$limit = '';
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
		}				
		/* end limit */	
					
		/* orders */
		$orderFields = array('id','title','content','starttime','endtime','worktime');
		$order = array();	
		if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {		
			foreach ($_REQUEST['order'] AS $key=>$value)	{				
				$order[] = $orderFields[$value['column']].' '.$value['dir'];
			}
		}
		/* end orders */		
					
		$table = $App->params->tables['pite'];
		$fields[] = "*";

		$whereAll = '';
		$andAll = '';
		$fieldsValueAll = array();
		$where = '';
		$and = '';	
		$fieldsValue = array();	
		
		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('fieldprefix'=>'','onlyuser'=>true));
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


		// SEARCH QUERY 
		$filtering = false;
		if (isset($_REQUEST['search']) && is_array($_REQUEST['search']) && count($_REQUEST['search']) > 0) {				
			if (isset($_REQUEST['search']['value']) && $_REQUEST['search']['value'] != '') {
				list($w,$fv) = Sql::getClauseVarsFromAppSession($_REQUEST['search']['value'],$App->params->fields['pite'],'');			
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
		// END SEARCH QUERY

		
		/* conta tutti i records */
		$recordsTotal = Sql::countRecordQry($table,'id',$whereAll,$fieldsValueAll);
		$recordsFiltered = $recordsTotal;
		
		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),'',array());
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);
		}

		
		Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),$limit);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		/* sistemo dati */	
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				$actions = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/'.($value->active == 1 ? 'disactive' : 'active').'Pite/'.$value->id.'" title="'.($value->active == 1 ? ucfirst(Config::$localStrings['disattiva']).' '.Config::$localStrings['la voce'] : ucfirst(Config::$localStrings['attiva']).' '.Config::$localStrings['la voce']).'"><i class="fas fa-'.($value->active == 1 ? 'unlock' : 'lock').'"></i></a><a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/modifyPite/'.$value->id.'" title="'.ucfirst(Config::$localStrings['modifica']).' '.Config::$localStrings['la voce'].'"><i class="far fa-edit"></i></a><a class="btn btn-default btn-sm confirmdelete" href="'.URL_SITE.Core::$request->action.'/deletePite/'.$value->id.'" title="'.ucfirst(Config::$localStrings['cancella']).' '.Config::$localStrings['la voce'].'"><i class="fas fa-trash-alt"></i></a>';
				$tablefields = array(
					'id'=>$value->id,
					'title'=>$value->title,
					'content'=>$value->content,
					'starttime'=>$value->starttime,
					'endtime'=>$value->endtime,
					'worktime'=>$value->worktime,
					'actions'=>$actions
					);
				$arr[] = $tablefields;
			}
		}
		$totalRows = Sql::getTotalsItems();
		$App->items = $arr;
		$json = array();
		$json = array();
		$json['draw'] = intval($_REQUEST['draw']);
		$json['recordsTotal'] = intval($recordsTotal);
		$json['recordsFiltered'] = intval($recordsFiltered);		
		$json['data'] = $App->items;	
		echo json_encode($json);
		die();
	break;

	case 'listPite':
		$App->viewMethod = 'list';		
	break;

	default;	
		$App->viewMethod = 'list';	
	break;	
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'formNew':
		$time = DateTime::createFromFormat('H:i:s',$App->nowTime);
		$App->timeIniTimecard =  $time->format('H:i');
		$time->add(new DateInterval('PT1H'));
		$App->timeEndTimecard = $time->format('H:i');	
		
		$App->item = new stdClass;
		$App->item->active = 1;
		$App->item->id_contact = 0;
		$App->item->created = Config::$nowDateTime;
		if (Core::$resultOp->error > 0) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['pite']);
		$App->templateApp = 'formPitem.html';
		$App->methodForm = 'insertPite';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formPitem.js"></script>';
	break;
	
	case 'formMod':
		if ($App->id) {		
			$App->item = new stdClass;
			Sql::initQuery($App->params->tables['pite'],array('*'),array($App->id),'id = ?');
			$App->item = Sql::getRecord();		
			if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['pite']);
			
			$App->timeIniTimecard = $App->item->starttime;
			$App->timeEndTimecard = $App->item->endtime;
	
			$App->templateApp = 'formPitem.html';
			$App->methodForm = 'updatePite';	
			$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formPitem.js"></script>';
			} else {
				ToolsStrings::redirect(URL_SITE.'error/404');
				}
	break;

	case 'list':
		$time = DateTime::createFromFormat('H:i:s',$App->nowTime);
		$App->timeIniTimecard =  $time->format('H:i');
		$time->add(new DateInterval('PT1H'));
		$App->timeEndTimecard = $time->format('H:i');	
				
		$App->items = new stdClass;			
		$App->itemsForPage = (isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) ? $_MY_SESSION_VARS[$App->sessionName]['ifp'] : 5);
		$App->page = (isset($_MY_SESSION_VARS[$App->sessionName]['page']) ? $_MY_SESSION_VARS[$App->sessionName]['page'] : 1);				
		$qryFields = array('*');
		$qryFieldsValues = array();
		$qryFieldsValuesClause = array();
		$clause = '';
		if (isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != '') {
			list($sessClause,$qryFieldsValuesClause) = Sql::getClauseVarsFromAppSession($_MY_SESSION_VARS[$App->sessionName]['srcTab'],$App->params->fields['pite'],'');
			}		
		if (isset($sessClause) && $sessClause != '') $clause .= $sessClause;
		if (is_array($qryFieldsValuesClause) && count($qryFieldsValuesClause) > 0) {
			$qryFieldsValues = array_merge($qryFieldsValues,$qryFieldsValuesClause);	
			}
		Sql::initQuery($App->params->tables['pite'],$qryFields,$qryFieldsValues,$clause);
		Sql::setItemsForPage($App->itemsForPage);	
		Sql::setPage($App->page);		
		Sql::setResultPaged(true);
		if (Core::$resultOp->error <> 1) $App->items = Sql::getRecords();
		$App->pagination = Utilities::getPagination($App->page,Sql::getTotalsItems(),$App->itemsForPage);
		$App->pageSubTitle = Config::$localStrings['lista delle voci custom'];
		$App->templateApp = 'listPitems.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listPitems.js"></script>';
	break;	
	
	default:
	break;
	}	
?>