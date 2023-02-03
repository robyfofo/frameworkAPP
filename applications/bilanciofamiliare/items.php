<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * bilanciofamiliare/items.php v.1.2.0. 20/12/2019
*/

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);


switch(Core::$request->method) {
	
	case 'getAjaxTotalItem':	
		//Core::setDebugMode(1);
		$totali_entrate_dodici_mesi = 0;
		$totali_uscite_dodici_mesi = 0;
		
		$totali_entrate_mesepre = 0;
		$totali_uscite_mesepre = 0;
		
		// ULTIMI 12 mesi
		$data_corrente = $App->nowDate;
		$data = DateTime::createFromFormat('Y-m-d',$data_corrente);
		$data->modify('-12 month');
		$data_inizio = $data->format('Y-m-d');
		$where = 'active = 1 AND dateins BETWEEN ? AND ? AND type = 1';
		$fieldsValue = array($data_inizio,$data_corrente);	
		$and = ' AND ';
		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('onlyuser'=>true));
		if (isset($permClause) && $permClause != '') {
			$where .= $and.'('.$permClause.')';
			$and = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$fieldsValue = array_merge($fieldsValue,$fieldsValuesPermClause);	
		}	
		/* end permissions items */
		Sql::initQuery($App->params->tables['items'],array('SUM(amount) AS tot_entrate'),$fieldsValue,$where);
 		$obj = Sql::getRecord();
 		if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db'); die(); }
 		if (isset($obj->tot_entrate)) $totali_entrate_dodici_mesi = $obj->tot_entrate;
 		
 		$where = 'active = 1 AND dateins BETWEEN ? AND ? AND type = 0';
 		$fieldsValue = array($data_inizio,$data_corrente);	
		$and = ' AND ';
		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('onlyuser'=>true));
		if (isset($permClause) && $permClause != '') {
			$where .= $and.'('.$permClause.')';
			$and = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$fieldsValue = array_merge($fieldsValue,$fieldsValuesPermClause);	
		}	
		Sql::initQuery($App->params->tables['items'],array('SUM(amount) AS tot_uscite'),$fieldsValue,$where);
 		$obj = Sql::getRecord();
 		if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db'); die(); }
 		if (isset($obj->tot_uscite)) $totali_uscite_dodici_mesi = $obj->tot_uscite;

 		
		$totali_bilancio_dodici_mesi = $totali_entrate_dodici_mesi - $totali_uscite_dodici_mesi;
		
		// MEDIE 12 MESI
	
		$medie_entrate_dodici_mesi = $totali_entrate_dodici_mesi / 12;
		$medie_uscite_dodici_mesi = $totali_uscite_dodici_mesi / 12;
		$medie_bilancio_dodici_mesi = $medie_entrate_dodici_mesi - $medie_uscite_dodici_mesi;
		
		// ULTIMO MESE PRECEDENTE		
		$totali_entrate_mesepre = 0;
		$totali_uscite_mesepre = 0;		
		$data_corrente = $App->nowDate;
		$data = DateTime::createFromFormat('Y-m-d',$data_corrente);
		$data->modify('-1 month');
		$data_mese_precedente = $data->format('Y-m').'%';		
		$where = 'active = 1 AND dateins LIKE ? AND type = 1';
		$fieldsValue = array($data_mese_precedente);	
		$and = ' AND ';
		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('onlyuser'=>true));
		if (isset($permClause) && $permClause != '') {
			$where .= $and.'('.$permClause.')';
			$and = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$fieldsValue = array_merge($fieldsValue,$fieldsValuesPermClause);	
		}	
		Sql::initQuery($App->params->tables['items'],array('SUM(amount) AS tot_entrate'),$fieldsValue,$where);
 		$obj = Sql::getRecord();
 		//if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db'); die(); }
 		if (isset($obj->tot_entrate)) $totali_entrate_mesepre = $obj->tot_entrate;
 		
 		$where = 'active = 1 AND dateins LIKE ? AND type = 0';
 		$fieldsValue = array($data_mese_precedente);	
		$and = ' AND ';
		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('onlyuser'=>true));
		if (isset($permClause) && $permClause != '') {
			$where .= $and.'('.$permClause.')';
			$and = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$fieldsValue = array_merge($fieldsValue,$fieldsValuesPermClause);	
		}	
		Sql::initQuery($App->params->tables['items'],array('SUM(amount) AS tot_uscite'),$fieldsValue,$where);
 		$obj = Sql::getRecord();
 		//if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db'); die(); }
 		if (isset($obj->tot_uscite)) $totali_uscite_mesepre = $obj->tot_uscite;
 		$totali_bilancio_mesepre = $totali_entrate_mesepre - $totali_uscite_mesepre;
 		
 		// MESE CORRENTE
		$data_mese = date('Y-m').'%';
		$totali_uscite_mese = 0;
		$totali_entrate_mese = 0;
		
		$where = 'active = 1 AND dateins LIKE ? AND type = 1';
		 $fieldsValue = array($data_mese);	
		$and = ' AND ';
		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('onlyuser'=>true));
		if (isset($permClause) && $permClause != '') {
			$where .= $and.'('.$permClause.')';
			$and = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$fieldsValue = array_merge($fieldsValue,$fieldsValuesPermClause);	
		}	
		Sql::initQuery($App->params->tables['items'],array('SUM(amount) AS tot_entrate'),$fieldsValue,$where);
 		$obj = Sql::getRecord();
 		//if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db'); die(); }
 		if (isset($obj->tot_entrate)) $totali_entrate_mese = $obj->tot_entrate;
 		
 		$where = 'active = 1 AND dateins LIKE ? AND type = 0';
 		 $fieldsValue = array($data_mese);	
		$and = ' AND ';
		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('onlyuser'=>true));
		if (isset($permClause) && $permClause != '') {
			$where .= $and.'('.$permClause.')';
			$and = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$fieldsValue = array_merge($fieldsValue,$fieldsValuesPermClause);	
		}	
		Sql::initQuery($App->params->tables['items'],array('SUM(amount) AS tot_uscite'),$fieldsValue,$where);
 		$obj = Sql::getRecord();
 		//if ( Core::$resultOp->error > 0 ) { ToolsStrings::redirect(URL_SITE.'error/db'); die(); }
 		if (isset($obj->tot_uscite)) $totali_uscite_mese = $obj->tot_uscite;
 		$totali_bilancio_mese = $totali_entrate_mese - $totali_uscite_mese;


	
	
		$json = array();
		$json['totali_entrate_dodici_mesi'] = number_format($totali_entrate_dodici_mesi,2,',','.');
		$json['totali_uscite_dodici_mesi'] = number_format($totali_uscite_dodici_mesi,2,',','.');
		$json['totali_bilancio_dodici_mesi'] = number_format($totali_bilancio_dodici_mesi,2,',','.');	
		
		
		$json['medie_entrate_dodici_mesi'] = number_format($medie_entrate_dodici_mesi,2,',','.');
		$json['medie_uscite_dodici_mesi'] = number_format($medie_uscite_dodici_mesi,2,',','.');
		$json['medie_bilancio_dodici_mesi'] = number_format($medie_bilancio_dodici_mesi,2,',','.');		
		
		$json['totali_entrate_mesepre'] = number_format($totali_entrate_mesepre,2,',','.');
		$json['totali_uscite_mesepre'] = number_format($totali_uscite_mesepre,2,',','.');
		$json['totali_bilancio_mesepre'] = number_format($totali_bilancio_mesepre,2,',','.');	
		
		$json['totali_entrate_mese'] = number_format($totali_entrate_mese,2,',','.');
		$json['totali_uscite_mese'] = number_format($totali_uscite_mese,2,',','.');
		$json['totali_bilancio_mese'] = number_format($totali_bilancio_mese,2,',','.');			
		
	
		echo json_encode($json);
		die();
	
	break;

	case 'listAjaxItem':
		//Core::setDebugMode(1);
		//print_r($_REQUEST);

		/* limit */		
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
			}				
		/* end limit */	

		/* orders */
		$orderFields = array('dateins','amount','description');
		$order = array();
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
		$where = '';
		$and = '';
		$fieldsValue = array();
		
		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('onlyuser'=>true));
		if (isset($permClause) && $permClause != '') {
			$where .= $and.'('.$permClause.')';
			$and = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$fieldsValue = array_merge($fieldsValue,$fieldsValuesPermClause);	
		}
		/* end permissions items */

		
		/* aggiunge campi join */
		//$App->params->fields['InvSal']['cus.ragione_sociale'] = array('searchTable'=>true,'type'=>'varchar');
		//$App->params->fields['itap']['ite.total'] = array('searchTable'=>true,'type'=>'float');
		
		
		$filtering = false;
		if (isset($_REQUEST['search']) && is_array($_REQUEST['search']) && count($_REQUEST['search']) > 0) {		
			if (isset($_REQUEST['search']['value']) && $_REQUEST['search']['value'] != '') {
				list($w,$fv) = Sql::getClauseVarsFromAppSession($_REQUEST['search']['value'],$App->params->fields['items'],'');
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
		/* end search */
		
		//echo $where;
		//print_r($fieldsValue);

		$table = $App->params->tables['items'];
		$fields[] = '*';
	
		/* conta tutti i records */
		$recordsTotal = Sql::countRecordQry($App->params->tables['items'],'id','',array());
		$recordsFiltered = $recordsTotal;

		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),'',array());
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);

		}
		


		Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),$limit,array());
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		//print_r($obj);
		/* sistemo dati */	
		$arr = array();
		$totentry = 0;
		$totoutput = 0;
		$_SESSION['totentry'] = 0;
		$_SESSION['totoutput'] = 0;
		
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				$type = 'Outp';
				if ($value->type == 1) $type = 'Entr';
				/* crea la colonna actions */
				$actions = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/modify'.$type.'/'.$value->id.'" title="'.ucfirst($_lang['modifica']).' '.$_lang['la voce'].'"><i class="far fa-edit"></i></a><a class="btn btn-default btn-sm confirmdelete" href="'.URL_SITE.Core::$request->action.'/deleteItem/'.$value->id.'" title="'.ucfirst($_lang['cancella']).' '.$_lang['la voce'].'"><i class="fas fa-trash-alt"></i></a>';						
									
				$entry = $value->amount;
				$output = $value->amount;
				if ( $value->type == 1) {
					$entry = $value->amount;
					$output = '';
					$totentry = $totentry + $entry;
				} else {
					$entry = '';
					$output = $value->amount;
					$totoutput = $totoutput + $value->amount;
				}
				
				$tablefields = array (
					'dateinslocal'			=>DateFormat::convertDateFormats($value->dateins,'Y-m-d',$_lang['data format'],$App->nowDate),
					'entry'					=> ($entry != '' ? '€ '.number_format($entry,2,',','.') : ''),
					'output'					=> ($output != '' ? '€ '.number_format($output,2,',','.') : ''),
					'description'			=> $value->description,
					'actions'				=> $actions
				);
				$arr[] = $tablefields;
				}
			}
		$App->items = $arr;
		
		//$App->items->totentry = '€ '.number_format($totentry,2,',','.');
		//$App->items->totoutput = '€ '.number_format($totoutput,2,',','.');
		
		$_SESSION['totentry'] = '€ '.number_format($totentry,2,',','.');
		$_SESSION['totoutput'] = '€ '.number_format($totoutput,2,',','.');
		
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

	case 'list':
		$App->item = new stdClass;		
		$App->item->dateins = $App->nowDate;
		$App->pageSubTitle = preg_replace('/%ITEMS%/',$_lang['voci'],$_lang['lista dei %ITEMS%']);
		$App->templateApp = 'listItems.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listItems.js"></script>';	
	break;
	
	default:
	break;
	}	
?>