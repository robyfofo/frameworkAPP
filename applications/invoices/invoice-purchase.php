<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * invoices/invoice-purchase.php v.1.2.0. 01/07/2020
*/

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

$App->type = 1;

/* GESTIONE CUSTOMERS */
$App->icustomers = new stdClass;	
Sql::initQuery($App->params->tables['cust'],array('*'),array(),'active = 1 AND (id_type = 1 OR id_type = 2)');
Sql::setOptions(array('fieldTokeyObj'=>'id'));
Sql::setOrder('surname ASC, name ASC');
$App->customers = Sql::getRecords();		

switch(Core::$request->method) {
	
	/* ARTICLES */
	case 'deleteArtInvPur':
		if ($App->id > 0) {
			$id_art = (isset(Core::$request->params[0]) ? intval(Core::$request->params[0]) : 0);		
			if ($id_art > 0) {
				
				// cancello il record
				Sql::initQuery($App->params->tables['ArtPur'],array('id'),array($id_art),'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
				
				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% cancellato'])).'!';
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvPur/'.$App->id.'/tab/2');
		
			} else {
				ToolsStrings::redirect(URL_SITE.'error/404');
			}		
		
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}	
	break;		
	/* END ARTICLES */
	
	case 'deleteInvPur':
		if ($App->id > 0) {

			/* controlla se ha figli */
			$count = Sql::countRecordQry($App->params->tables['ArtPur'],'id','id_invoice = ?',array($App->id));
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			if ($count > 0) {
				$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articoli'],Config::$localStrings['Errore! Ci sono ancora %ITEM% associati!']));
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvPur/'.$App->id);	
			}

			// cancello il record
			Sql::initQuery($App->params->tables['InvPur'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce-p'],Config::$localStrings['%ITEM% cancellata'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listInvPur');
			
				
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}	
	break;
	
	case 'newInvPur':			
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce-p'],Config::$localStrings['inserisci %ITEM%']);
		$App->viewMethod = 'formNew';	
		$App->tabActive = 1;
	break;
	
	case 'insertInvPur':
		if ($_POST) {
			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['InvPur'],Config::$localStrings,array());
			if (Core::$resultOp->error > 0) { 
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newInvPur');
			}	
			
			// controllo date
			DateFormat::checkDateFormatIniEndInterval($_POST['dateins'],$_POST['datesca'],'Y-m-d','>');
			if (Core::$resultOp->error > 0) { 
				$_SESSION['message'] = '1|'.Config::$localStrings['Intervallo tra le due date errato!'];
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newInvPur/'.$App->id);
			}
	
			// memorizza record
			Sql::insertRawlyPost($App->params->fields['InvPur'],$App->params->tables['InvPur']);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce-p'],Config::$localStrings['%ITEM% inserita'])).'!';
			$tabActive = 1;
			
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listInvPur');
			
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}			
	break;

	case 'modifyInvPur':				
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce-p'],Config::$localStrings['modifica %ITEM%']);
		$App->viewMethod = 'formMod';
		$App->tabActive = 1;
		if ( (isset(Core::$request->params[0]) && Core::$request->params[0] == 'tab') && isset(Core::$request->params[1]) ) $App->tabActive = Core::$request->params[1];
	break;
	
	case 'updateInvPur':
		if ($_POST) {
			if (isset($_POST['submitArtForm']) &&  $_POST['submitArtForm'] == 'submitArt') {
				
				$_POST['id_invoice'] = $_POST['id'];
				$_POST['active'] = 1;
				$_POST['content'] = $_POST['art_content'];
				$_POST['price_unity'] = $_POST['art_price_unity'];
				$_POST['price_tax'] = 0;
				$_POST['price_total'] = $_POST['art_price_total'];
				$_POST['quantity'] = $_POST['art_quantity'];
				$_POST['tax'] = $_POST['art_tax'];
				
				if ($_POST['quantity'] > 0 && isset($_POST['price_unity'])) {		
					
					$_POST = $Module->calculateArt($_POST);	
									
					if (isset($_POST['artFormMode']) &&  $_POST['artFormMode'] == 'ins') {
						
						// parsa i campi
						Form::parsePostByFields($App->params->fields['ArtPur'],Config::$localStrings,array());	
						if (Core::$resultOp->error > 0) { 
							$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
							ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvPur/'.$App->id).'/tab/2';
						}	

						// memorizza record
						Sql::insertRawlyPost($App->params->fields['ArtPur'],$App->params->tables['ArtPur']);
						if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
						
						$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% inserito'])).'!';
						$tabActive = 2;
						
						ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvPur/'.$App->id.'/tab/'.$tabActive);
						
					}
						
				
					if (isset($_POST['artFormMode']) &&  $_POST['artFormMode'] == 'mod') {
						
						$id_art = (isset($_POST['id_article']) ? intval($_POST['id_article']) : 0);
						
						// parsa i campi
						Form::parsePostByFields($App->params->fields['ArtPur'],Config::$localStrings,array());	
						if (Core::$resultOp->error > 0) { 
							$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
							ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvPur/'.$App->id).'/tab/2';
						}	
						
						// memorizza record
						Sql::updateRawlyPost($App->params->fields['ArtPur'],$App->params->tables['ArtPur'],'id',$id_art);
						if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
							
						$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% modificato'])).'!';
						$tabActive = 2;
						
						ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvPur/'.$App->id.'/tab/'.$tabActive);
						
					}
					
				}				
						
			} else {

				// form invoice	
										
				// parsa i post in base ai campi */ 	
				Form::parsePostByFields($App->params->fields['InvPur'],Config::$localStrings,array());
   			if (Core::$resultOp->error > 0) { 
					$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvPur/'.$App->id);
				}	
				
				// controllo date
				DateFormat::checkDateFormatIniEndInterval($_POST['dateins'],$_POST['datesca'],'Y-m-d','>');
				if (Core::$resultOp->error > 0) { 
					$_SESSION['message'] = '1|'.Config::$localStrings['Intervallo tra le due date errato!'];
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvPur/'.$App->id);
				}
				
				// memorizza record
				Sql::updateRawlyPost($App->params->fields['InvPur'],$App->params->tables['InvPur'],'id',$App->id);
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
									
				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce-p'],Config::$localStrings['%ITEM% modificata'])).'!';
				$tabActive = 1;
			}
					
			
			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvPur/'.$App->id.'/tab/'.$tabActive);
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listInvPur');
			}	
					
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}			
	break;
	
/*  AJAX */

	case 'getArticleAjaxInvPur':
		$App->item = new stdClass;
		$id = (isset($_POST['id']) ? intval($_POST['id']) : 0);
		if ($id > 0) {
			Sql::initQuery($App->params->tables['ArtPur'],array('*'),array($id),'id = ?');
			$App->item = Sql::getRecord();
			$json = json_encode($App->item);
			echo $json;
			}	
		die();	
	break;

/* END AJAX */

	case 'messageInvPur':
		Core::$resultOp->error = $App->id;
		if (isset(Core::$request->params[0])) Core::$resultOp->message = urldecode(Core::$request->params[0]);
		$App->viewMethod = 'list';		
	break;
	
	case 'listAjaxInvPur':
		//Core::setDebugMode(1);
		//print_r($_REQUEST);

		/* limit */		
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
			}				
		/* end limit */	

		/* orders */
		$order = array();
		$orderFields = array('id','dateins','datesca','ragione_sociale','number','total');	
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
		$where = '';
		$and = '';
		$fieldsValue = array();
		
		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('fieldprefix'=>'ite.','onlyuser'=>true));
		if (isset($permClause) && $permClause != '') {
			$where .= $and.'('.$permClause.')';
			$and = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$fieldsValue = array_merge($fieldsValue,$fieldsValuesPermClause);	
		}
		/* end permissions items */

		$filtering = false;
		if (isset($_REQUEST['search']) && is_array($_REQUEST['search']) && count($_REQUEST['search']) > 0) {		
			if (isset($_REQUEST['search']['value']) && $_REQUEST['search']['value'] != '') {
				list($w,$fv) = Sql::getClauseVarsFromAppSession($_REQUEST['search']['value'],$App->params->fields['InvPur'],'');
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
		///echo $where;
		/* end search */
		
		
		
		
		$table = $App->params->tables['InvPur']." AS ite";
		//$table .= " LEFT JOIN ".$App->params->tables['cust']." AS cus  ON (ite.id_customer = cus.id)";
		$table .= " LEFT JOIN ".$App->params->tables['ArtPur']." AS art  ON (ite.id = art.id_invoice)";		
		$fields[] = 'ite.*';
		//$fields[] = "cus.ragione_sociale AS customer";
		$fields[] = "SUM(art.price_total) AS total";
		$fields[] = "SUM(art.price_tax) AS total_tax";
		
		/* conta tutti i records */
		$recordsTotal = Sql::countRecordQry($App->params->tables['InvPur'],'id',$where,$fieldsValue);
		$recordsFiltered = $recordsTotal;
				
		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),'',array('groupby'=>'ite.id'));
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);

		}
							
		Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),$limit,array('groupby'=>'ite.id'));
		$obj = Sql::getRecords();
		
		//print_r($obj);
		/* sistemo dati */	
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				/* crea la colonna actions */
				$actions = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/modifyInvPur/'.$value->id.'" title="'.ucfirst(Config::$localStrings['modifica']).' '.Config::$localStrings['la voce-p'].'"><i class="far fa-edit"> </i></a><a class="btn btn-default btn-sm confirmdelete" href="'.URL_SITE.Core::$request->action.'/deleteInvPur/'.$value->id.'" title="'.ucfirst(Config::$localStrings['cancella']).' '.Config::$localStrings['la voce-p'].'"><i class="fas fa-trash-alt"> </i></a>';
				$data = DateTime::createFromFormat('Y-m-d',$value->dateins);
				$data1 = DateTime::createFromFormat('Y-m-d',$value->datesca);						
				$value->totalLabel = '€ '.number_format($value->total + $value->total_tax,2,',','.');				
						
				$tablefields = array(
					'id'=>$value->id,
					'number'=>$value->number,
					'dateinslocal'=>$data->format(Config::$localStrings['data format']),
					'datescalocal'=>$data1->format(Config::$localStrings['data format']),
					'customer'=>$value->customer_ragione_sociale,
					'total'=>$value->totalLabel,
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
		$App->item->active = 1;
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['InvPur']);
		$App->templateApp = 'formInvPur.html';
		$App->methodForm = 'insertInvPur';
		$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formInvPur.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formInvPur.js"></script>';
	break;
	
	case 'formMod':
		if ($App->id > 0) {
			
			$App->item = new stdClass;
			$App->item->dateins = $App->nowDate;
			Sql::initQuery($App->params->tables['InvPur'],array('*'),array($App->id),'id = ?');
			$App->item = Sql::getRecord();
			if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['InvPur']);
			
			$App->item_articles = new stdClass;
			$qryFields = array("*");
			$qryFieldsValues = array($App->id);
			$clause = 'id_invoice = ?';
			Sql::initQuery($App->params->tables['ArtPur'],$qryFields,$qryFieldsValues,$clause);
			Sql::setResultPaged(false);
			Sql::setOrder('id ASC');
			if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();	
			/* sistemo dati */	
			$tot_price_total = 0;
			
			$tot_quantity_total = 0;
			
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
					
					$tot_quantity_total += $value->quantity;
					
					$tot_price_tax += $value->price_tax;
					$tot_total += $value->total;			
					$arr[] = $value;
					}
				}
			$App->item_articles = $arr;	
			$App->item->art_tot_price_total_label = '€ '.number_format($tot_price_total,2,',','.');
			
			$App->item->art_tot_quantity_label = $tot_quantity_total;
			
			$App->item->art_tot_price_tax_label = '€ '.number_format($tot_price_tax,2,',','.');
			$App->item->art_tot_total_label = '€ '.number_format($tot_total,2,',','.');
			/* calcola tassa aggiuntiva */
			$App->item->invoiceTotalTax = 0;
			/* calcola rivalsa */
			$App->item->invoiceTotalRivalsa = 0;
			$App->item->invoiceTotal = (float)$tot_price_total + $tot_price_tax + $App->item->invoiceTotalTax + $App->item->invoiceTotalRivalsa;	
			$App->item->invoiceTotalTax_label = '€ '.number_format($App->item->invoiceTotalTax,2,',','.');
			$App->item->invoiceTotalRivalsa_label = '€ '.number_format($App->item->invoiceTotalRivalsa,2,',','.');
			$App->item->invoiceTotal_label = '€ '.number_format($App->item->invoiceTotal,2,',','.');

			$App->templateApp = 'formInvPur.html';
			$App->methodForm = 'updateInvPur';	
			$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formInvPur.css" rel="stylesheet">';
			$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formInvPur.js"></script>';
			
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listInvPur');
				die();
				}		
	break;

	case 'list':
		$App->item = new stdClass;		
		$App->item->dateins = $App->nowDate;
		$App->item->datesca = $App->nowDate;
		$App->pageSubTitle = preg_replace('/%ITEMS%/',Config::$localStrings['voci-p'],Config::$localStrings['lista delle %ITEMS%']);
		$App->templateApp = 'listInvPur.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listInvPur.js"></script>';	
	break;
	
	default:
	break;
	}	
?>