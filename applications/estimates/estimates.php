<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * estimates/estimetes.php v.1.3.0. 07/09/2020
*/
//die(Core::$request->method);
if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

/* GESTIONE CUSTOMERS */
$App->customers = new stdClass;
Sql::initQuery($App->params->tables['cust'],array('*'),array(),'active = 1 AND (id_type = 3)');
Sql::setOptions(array('fieldTokeyObj'=>'id'));
Sql::setOrder('surname ASC, name ASC');
$App->customers = Sql::getRecords();

switch(Core::$request->method) {

	/* ARTICLES */
	case 'deleteArtItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {
			$id_art = (isset(Core::$request->params[0]) ? intval(Core::$request->params[0]) : 0);
			if ($id_art > 0) {

				// cancello il record
				Sql::initQuery($App->params->tables['arts'],array('id'),array($id_art),'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% cancellato'])).'!';
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id.'/tab/2');

			} else {
				ToolsStrings::redirect(URL_SITE.'error/404');
			}

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	/* END ARTICLES */

	case 'deleteItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {

			$count = Sql::countRecordQry($App->params->tables['arts'],'id','estimates_id = ?',array($App->id));
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			if ($count > 0) {
				$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articoli'],Config::$localStrings['Errore! Ci sono ancora %ITEM% associati!']));
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
			}

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
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); }
		$App->item = new stdClass;
		$App->item->dateins = Config::$nowDate;
		$App->item->datesca = Config::$nowDate;
		$App->item->rivalsa = $App->company->rivalsa;
		$App->item->tax = 0;
		$App->item->active = 1;
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%']);
		$App->methodForm = 'insertItem';
		$App->viewMethod = 'form';
		$_SESSION[$App->sessionName]['formTabActive'] = 1;
	break;

	case 'modifyItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		$App->item = new stdClass;
		$App->item->dateins = Config::$nowDate;
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }

		$App->item_articles = new stdClass;
		$qryFields = array("*");
		$qryFieldsValues = array($App->id);
		$clause = 'estimates_id = ?';
		Sql::initQuery($App->params->tables['arts'],$qryFields,$qryFieldsValues,$clause);
		Sql::setResultPaged(false);
		Sql::setOrder('id ASC');
		if (Core::$resultOp->error == 0) $obj = Sql::getRecords();
		//if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		// sistemo dati
		$tot_price_total = 0;
		$tot_price_tax = 0;
		$tot_total = 0;
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				$value->total = $value->price_total + $value->price_tax;
				$value->price_unity_label = '??? '.number_format($value->price_unity,2,',','.');
				$value->price_total_label = '??? '.number_format($value->price_total,2,',','.');
				$value->price_tax_label = '??? '.number_format($value->price_tax,2,',','.');
				$value->total_label = '??? '.number_format($value->total,2,',','.');
				$tot_price_total += $value->price_total;
				$tot_price_tax += $value->price_tax;
				$tot_total += $value->total;
				$arr[] = $value;
			}
		}
		$App->item_articles = $arr;
		$App->item->art_tot_price_total_label = '??? '.number_format($tot_price_total,2,',','.');
		$App->item->art_tot_price_tax_label = '??? '.number_format($tot_price_tax,2,',','.');
		$App->item->art_tot_total_label = '??? '.number_format($tot_total,2,',','.');
		// calcola tassa aggiuntiva
		$App->item->invoiceTotalTax = 0;
		if ($App->item->tax > 0) $App->item->invoiceTotalTax = ($tot_price_total * $App->item->tax) / 100;
		// calcola rivalsa
		$App->item->invoiceTotalRivalsa = 0;
		if ($App->item->rivalsa > 0) $App->item->invoiceTotalRivalsa = ($tot_price_total * $App->item->rivalsa) / 100;
		$App->item->invoiceTotal = (float)$tot_price_total + $tot_price_tax + $App->item->invoiceTotalTax + $App->item->invoiceTotalRivalsa;
		$App->item->invoiceTotalTax_label = '??? '.number_format($App->item->invoiceTotalTax,2,',','.');
		$App->item->invoiceTotalRivalsa_label = '??? '.number_format($App->item->invoiceTotalRivalsa,2,',','.');
		$App->item->invoiceTotal_label = '??? '.number_format($App->item->invoiceTotal,2,',','.');
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['modifica %ITEM%']);
		$App->methodForm = 'updateItem';
		$App->viewMethod = 'form';
		$_SESSION[$App->sessionName]['formTabActive'] = 2;
		if (
			isset(Core::$request->params[0]) &&
			Core::$request->params[0] == 'tab' &&
			isset(Core::$request->params[1]) &&
			Core::$request->params[0] != ''
			) $_SESSION[$App->sessionName]['formTabActive'] = Core::$request->params[1];
	break;

	case 'insertItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($_POST) {

			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newItem');
			}

			// controllo date
			DateFormat::checkDateFormatIniEndInterval($_POST['dateins'],$_POST['datesca'],'Y-m-d','>');
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.Config::$localStrings['Intervallo tra le due date errato!'];
				$_SESSION[$App->sessionName]['formTabActive'] = 1;
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id.'/tab/1');
			}

			Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
			if (Core::$resultOp->error > 0) {ToolsStrings::redirect(URL_SITE.'error/db');}

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% inserito']));
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;



	case 'updateItem':
		Core::setDebugMode(1);
		if ($_POST) {

			if (isset($_POST['submitArtForm']) &&  $_POST['submitArtForm'] == 'submitArt') {

				$_POST['estimates_id'] = $_POST['id'];
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

						// parsa i campi
						Form::parsePostByFields($App->params->fields['arts'],Config::$localStrings,array());
						if (Core::$resultOp->error > 0) {
							echo $_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
							ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id).'/tab/2';
						}

						// memorizza record
						Sql::insertRawlyPost($App->params->fields['arts'],$App->params->tables['arts']);
						if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

						$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% inserito'])).'!';
						ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id.'/tab/2');

					}

					if (isset($_POST['artFormMode']) &&  $_POST['artFormMode'] == 'mod') {

						$id_art = (isset($_POST['id_article']) ? intval($_POST['id_article']) : 0);

						// parsa i campi
						Form::parsePostByFields($App->params->fields['arts'],Config::$localStrings,array());
						if (Core::$resultOp->error > 0) {
							$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
							ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id).'/tab/2';
						}

						// memorizza record
						Sql::updateRawlyPost($App->params->fields['arts'],$App->params->tables['arts'],'id',$id_art);
						if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

						$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% modificato'])).'!';
						ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id.'/tab/2');


					}


				} else {
					$_SESSION['message'] = '1|'.ucfirst(Config::$localStrings['articolo non aggiunto']);
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
				}

			} else {

				// form estimates
				// parsa i post in base ai campi
				Form::parsePostByFields($App->params->fields['item'],Config::$localStrings,array());
				if (Core::$resultOp->error > 0) {
					$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
				}

				// controllo date
				DateFormat::checkDateFormatIniEndInterval($_POST['dateins'],$_POST['datesca'],'Y-m-d','>');
				if (Core::$resultOp->error > 0) {
					$_SESSION['message'] = '1|'.Config::$localStrings['Intervallo tra le due date errato!'];
					$_SESSION[$App->sessionName]['formTabActive'] = 1;
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id.'/tab/1');
				}

				// memorizza record
				Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

				//end form estimates

			}

			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
			}


		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}

		die();
	break;

/*  AJAX */

	case 'getArticleAjaxItem':
		$App->item = new stdClass;
		$id = (isset($_POST['id']) ? intval($_POST['id']) : 0);
		if ($id > 0) {
			Sql::initQuery($App->params->tables['arts'],array('*'),array($id),'id = ?');
			$App->item = Sql::getRecord();
			$json = json_encode($App->item);
			echo $json;
		}
		die();
	break;

/* END AJAX */


	case 'listAjaxItem':
		//Core::setDebugMode(1);
		//print_r($_REQUEST);

		/* limit */
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
		}
		/* end limit */

		/* orders */
		$order = array();
		$orderFields = array('id','dateins','datesca','customer','total');
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

		// search
		$where = '';
		$and = '';
		$fieldsValue = array();

		// permissions query
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('fieldprefix'=>'ite.','onlyuser'=>true));
		if (isset($permClause) && $permClause != '') {
			$where .= $and.'('.$permClause.')';
			$and = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$fieldsValue = array_merge($fieldsValue,$fieldsValuesPermClause);
		}
		// end permissions items

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
		/* end search */

		$table = $App->params->tables['item']." AS ite";
		$table .= " LEFT JOIN ".$App->params->tables['arts']." AS art  ON (ite.id = art.estimates_id)";
		$fields[] = 'ite.*';
		$fields[] = "SUM(art.price_total) AS total";
		$fields[] = "SUM(art.price_tax) AS total_tax";

		/* conta tutti i records */
		$recordsTotal = Sql::countRecordQry($App->params->tables['item']." AS ite",'id',$where,$fieldsValue);
		$recordsFiltered = $recordsTotal;

		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),'',array('groupby'=>'ite.id'));
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);
		}

		Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),$limit,array('groupby'=>'ite.id'));
		if (Core::$resultOp->error == 0) $obj = Sql::getRecords();

		/* sistemo dati */
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				$pdf = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/estimateExpPdf/'.$value->id.'" title="'.ucfirst(Config::$localStrings['esporta in pdf']).' '.Config::$localStrings['voce'].'" target="_blank"><i class="fas fa-print"></i></a>';
				$actions = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/modifyItem/'.$value->id.'" title="'.ucfirst(Config::$localStrings['modifica']).' '.Config::$localStrings['voce'].'"><i class="far fa-edit"></i></a><a class="btn btn-default btn-sm confirmdelete" href="'.URL_SITE.Core::$request->action.'/deleteItem/'.$value->id.'" title="'.ucfirst(Config::$localStrings['cancella']).' '.Config::$localStrings['voce'].'"><i class="fas fa-trash-alt"></i></a>';
				$data = DateTime::createFromFormat('Y-m-d',$value->dateins);
				$data1 = DateTime::createFromFormat('Y-m-d',$value->datesca);
				$value->totalLabel = '??? '.number_format($value->total + $value->total_tax,2,',','.');
				$tablefields = array(
					'id'=>$value->id,
					'note'=>$value->note,
					'dateinslocal'=>$data->format(Config::$localStrings['data format']),
					'datescalocal'=>$data1->format(Config::$localStrings['data format']),
					'customer'=>$value->customer,
					'total'=>$value->totalLabel,
					'pdf'=>$pdf,
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
	case 'form':
		$App->templateApp = 'formEstimate.html';
		$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formEstimate.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formEstimate.js"></script>';
	break;

	case 'list':
		$App->item = new stdClass;
		$App->item->dateins = Config::$nowDate;
		$App->item->datesca = Config::$nowDate;
		$App->pageSubTitle = preg_replace('/%ITEMS%/',Config::$localStrings['voci'],Config::$localStrings['lista dei %ITEMS%']);
		$App->templateApp = 'listEstimates.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listEstimates.js"></script>';
	break;

	default:
	break;
}
?>
