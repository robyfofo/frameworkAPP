<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * settings/iva.php v.1.0.0. 11/09/2018
*/

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);	

switch(Core::$request->method) {

	case 'activeItem':
	case 'disactiveItem':
		Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['item'],$App->id,$opt=array('labelA'=>Config::$localStrings['voce'].' '.Config::$localStrings['attivato'],'labelD'=>Config::$localStrings['voce'].' '.Config::$localStrings['disattivato']));
		$App->viewMethod = 'list';		
	break;
	
	case 'deleteItem':
		if ($App->id > 0) {
			$delete = true;
			/* controlla se ha figli */
			if (Sql::countRecordQry($App->params->tables['arts'],'id','id_estimate = ?',array($App->id)) > 0) {
				Core::$resultOp->error = 2;
				Core::$resultOp->message = Config::$localStrings['Errore! Ci sono ancora figli associati!'];
				$delete = false;	
				}
			
			if ($delete == true && Core::$resultOp->error == 0) {	
				Sql::initQuery($App->params->tables['item'],array('id'),array($App->id),'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error == 0) {
					Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% cancellato'])).'!';
					}
				}
			}		
		$App->viewMethod = 'list';
	break;
	
	case 'newItem':			
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%']);
		$App->viewMethod = 'formNew';	
		$App->tabActive = 1;
	break;
	
	case 'insertItem':
		if ($_POST) {
			/* parsa i post in base ai campi */
			Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());			
			if (Core::$resultOp->error == 0) {
				if (DateFormat::checkDateFormatIniEndInterval($_POST['dateins'],$_POST['datesca'],'Y-m-d','>') == true) {									
					Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
					if (Core::$resultOp->error == 0) {												   						   							   				
		   			}
		   		} else {
		   			Core::$resultOp->error = 1;
						Core::$resultOp->message = Config::$localStrings['Intervallo tra le due date errato!'];	 
						}
				}
			} else {
				Core::$resultOp->error = 1;
				}
		list($id,$App->viewMethod,$App->pageSubTitle,Core::$resultOp->message) = Form::getInsertRecordFromPostResults(0,Core::$resultOp,array('label inserted'=>preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% inserito']),'label insert'=>preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%'])));				
		$App->tabActive = 1;	
	break;
	
	case 'modifyItem':				
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['modifica %ITEM%']);
		$App->viewMethod = 'formMod';
		$App->tabActive = 2;
	break;
	
	case 'updateItem':
		if ($_POST) {	
			if (isset($_POST['submitArtForm']) &&  $_POST['submitArtForm'] == 'submitArt') {
				
				$_POST['id_estimate'] = $_POST['id'];
				$_POST['active'] = 1;
				$_POST['content'] = $_POST['art_content'];
				$_POST['price_unity'] = $_POST['art_price_unity'];
				$_POST['price_tax'] = 0;
				$_POST['price_total'] = $_POST['art_price_total'];
				$_POST['quantity'] = $_POST['art_quantity'];
				$_POST['tax'] = $_POST['art_tax'];
				
				if ($_POST['quantity'] > 0 && $_POST['price_unity'] > 0) {			
					$_POST = $Module->calculateArt($_POST);	
									
					if (isset($_POST['artFormMode']) &&  $_POST['artFormMode'] == 'ins') {
						Form::parsePostByFields($App->params->fields['arts'],Config::$localStrings,array());	
						if (Core::$resultOp->error == 0) {
							Sql::insertRawlyPost($App->params->fields['arts'],$App->params->tables['arts']);
							if (Core::$resultOp->error == 0) {
								Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% inserito'])).'!';
								$App->tabActive = 2;
								}
							} else {
								Core::$resultOp->error = 1;
								}					
						}
				
					if (isset($_POST['artFormMode']) &&  $_POST['artFormMode'] == 'mod') {
						$id_art = (isset($_POST['id_article']) ? intval($_POST['id_article']) : 0);
						Form::parsePostByFields($App->params->fields['arts'],Config::$localStrings,array());	
						if (Core::$resultOp->error == 0) {
							Sql::updateRawlyPost($App->params->fields['arts'],$App->params->tables['arts'],'id',$id_art);	
							if (Core::$resultOp->error == 0) {
								Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% modificato'])).'!';
								$App->tabActive = 2;
								}
							} else {
								Core::$resultOp->error = 1;
								}
						}
				
					}
		
				} else {
					/* form estimates */
					/* parsa i post in base ai campi */ 
					Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());
					if (Core::$resultOp->error == 0) {
						if (DateFormat::checkDateFormatIniEndInterval($_POST['dateins'],$_POST['datesca'],'Y-m-d','>') == true) {												
							Sql::updateRawlyFields($App->params->fields['item'],$App->params->tables['item'],$_POST,array('clause'=>'id = ?','clauseVals'=>array($App->id)));
							if (Core::$resultOp->error == 0) {
								$App->tabActive = 1;																	
								}							
				   		} else {
				   			Core::$resultOp->error = 1;
								Core::$resultOp->message = Config::$localStrings['Intervallo tra le due date errato!'];	 
								}			
						}
					/* end form estimates */	
					}	
														
			} else {
				Core::$resultOp->error = 1;
				}	
		list($id,$App->viewMethod,$App->pageSubTitle,Core::$resultOp->message) = Form::getUpdateRecordFromPostResults($App->id,Core::$resultOp,array('label done'=>Config::$localStrings['modifiche effettuate'],'label modified'=>preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% modificato']),'label modify'=>preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['modifica %ITEM%']),'label insert'=>preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%'])));	
	break;

	case 'messageItem':
		Core::$resultOp->error = $App->id;
		if (isset(Core::$request->params[0])) Core::$resultOp->message = urldecode(Core::$request->params[0]);
		$App->viewMethod = 'list';		
	break;

	case 'listAjaxIvaa':
		//Core::setDebugMode(1);
		//print_r($_REQUEST);

		/* limit */		
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
			}				
		/* end limit */	

		/* orders */
		$order = array();
		$orderFields = array('id','note','amount');	
		/* default da sessione */
		if (isset($_MY_SESSION_VARS[$App->sessionName]['order']) && $_MY_SESSION_VARS[$App->sessionName]['order'] != '') {
			$order[] = $_MY_SESSION_VARS[$App->sessionName]['order'];
			}
		
		if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {	
			$order = array();	
			foreach ($_REQUEST['order'] AS $key=>$value)	{				
				$order[] = $orderFields[$value['column']].' '.$value['dir'];
				}
			/* salva in sessione */
			if (is_array($order) && count($order) > 0) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'order',implode(',',$order));
			}
		/* end orders */

		/* search */
		/* aggiunge campi join */
		$where = 'ite.id_user = ?';
		$and = ' AND ';
		$fieldsValue = array($App->userLoggedData->id);
		$filtering = false;
		if (isset($_REQUEST['search']) && is_array($_REQUEST['search']) && count($_REQUEST['search']) > 0) {		
			if (isset($_REQUEST['search']['value']) && $_REQUEST['search']['value'] != '') {
				list($w,$fv) = Sql::getClauseVarsFromAppSession($_REQUEST['search']['value'],$App->params->fields['ivaa'],'');
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
		//print_r($fieldsValue);
		/* end search */

		$table = $App->params->tables['ivaa']." AS ite";
		$fields[] = 'ite.*';		
		/* conta tutti i records */
		$recordsTotal = Sql::countRecordQry($App->params->tables['ivaa'],'id','',array());
			
		Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),$limit,array());
		$obj = Sql::getRecords();
		//print_r($obj);
		/* sistemo dati */	
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				$actions = '<a class="btn btn-default btn-circle" href="'.URL_SITE.Core::$request->action.'/modifyIvaa/'.$value->id.'" title="'.ucfirst(Config::$localStrings['modifica']).' '.Config::$localStrings['la voce'].'"><i class="fa fa-edit"> </i></a><a class="btn btn-default btn-circle confirmdelete" href="'.URL_SITE.Core::$request->action.'/deleteItem/'.$value->id.'" title="'.ucfirst(Config::$localStrings['cancella']).' '.Config::$localStrings['la voce'].'"><i class="fa fa-cut"> </i></a>';					
				$tablefields = array(
					'id'=>$value->id,
					'note'=>$value->note,
					'amount'=>$value->amount,
					'actions'=>$actions
					);
				$arr[] = $tablefields;
				}
			}
		$App->items = $arr;
		$recordsFiltered = $recordsTotal;
		if ($filtering == true) $recordsFiltered = count($App->items);
		$json = array();
		$json['draw'] = intval($_REQUEST['draw']);
		$json['recordsTotal'] = $recordsTotal;
		$json['recordsFiltered'] = $recordsFiltered;	
		$json['data'] = $App->items;	
		echo json_encode($json);
		die();
	break;

	default;	
		$App->viewMethod = 'list';	
	break;	
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {
	case 'formNew':
		$App->item = new stdClass;	
		$App->item->dateins = $App->nowDate;
		$App->item->datesca = $App->nowDate;
		$App->item->rivalsa = $App->company->rivalsa;
		$App->item->tax = 0;			
		$App->item->active = 1;
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
		$App->templateApp = 'formItem.tpl.php';
		$App->methodForm = 'insertItem';	
		$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formItem.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
	break;
	
	case 'formMod':
		if ($App->id > 0) {
			$App->item = new stdClass;
			$App->item->dateins = $App->nowDate;
			Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
			$App->item = Sql::getRecord();
			if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);
			$App->item_articles = new stdClass;
			$qryFields = array("*");
			$qryFieldsValues = array($App->id);
			$clause = 'id_estimate = ?';
			Sql::initQuery($App->params->tables['arts'],$qryFields,$qryFieldsValues,$clause);
			Sql::setResultPaged(false);
			Sql::setOrder('id ASC');
			if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();	
			/* sistemo dati */	
			$tot_price_total = 0;
			$tot_price_tax = 0;
			$tot_total = 0;
			$arr = array();
			if (is_array($obj) && count($obj) > 0) {
				foreach ($obj AS $key=>$value) {
					$value->total = $value->price_total + $value->price_tax;
					$value->price_unity_label = '€ '.number_format($value->price_unity,2,',','.');
					$value->price_total_label = '€ '.number_format($value->price_total,2,',','.');
					$value->price_tax_label = '€ '.number_format($value->price_tax,2,',','.');
					$value->total_label = '€ '.number_format($value->total,2,',','.');
					$tot_price_total += $value->price_total;
					$tot_price_tax += $value->price_tax;
					$tot_total += $value->total;			
					$arr[] = $value;
					}
				}
			$App->item_articles = $arr;	
			$App->item->art_tot_price_total_label = '€ '.number_format($tot_price_total,2,',','.');
			$App->item->art_tot_price_tax_label = '€ '.number_format($tot_price_tax,2,',','.');
			$App->item->art_tot_total_label = '€ '.number_format($tot_total,2,',','.');
			/* calcola tassa aggiuntiva */
			$App->item->invoiceTotalTax = 0;
			if ($App->item->tax > 0) $App->item->invoiceTotalTax = ($tot_price_total * $App->item->tax) / 100;		
			/* calcola rivalsa */
			$App->item->invoiceTotalRivalsa = 0;
			if ($App->item->rivalsa > 0) $App->item->invoiceTotalRivalsa = ($tot_price_total * $App->item->rivalsa) / 100;
			$App->item->invoiceTotal = (float)$tot_price_total + $tot_price_tax + $App->item->invoiceTotalTax + $App->item->invoiceTotalRivalsa;	
			$App->item->invoiceTotalTax_label = '€ '.number_format($App->item->invoiceTotalTax,2,',','.');
			$App->item->invoiceTotalRivalsa_label = '€ '.number_format($App->item->invoiceTotalRivalsa,2,',','.');
			$App->item->invoiceTotal_label = '€ '.number_format($App->item->invoiceTotal,2,',','.');

			$App->templateApp = 'formItem.tpl.php';
			$App->methodForm = 'updateItem';
			$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formItem.css" rel="stylesheet">';
			$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formItem.js"></script>';
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
				die();
				}		
	break;

	case 'list':
		$App->item = new stdClass;		
		$App->item->dateins = $App->nowDate;
		$App->item->datesca = $App->nowDate;
		$App->pageSubTitle = preg_replace('/%ITEMS%/',Config::$localStrings['percentuali iva'],Config::$localStrings['lista dei %ITEMS%']);
		$App->templateApp = 'listIvaa.tpl.php';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listIvaa.js"></script>';	
	break;
	
	default:
	break;
	}	
?>