<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * invoices/invoice-sale.php v.1.2.0. 01/07/2020
*/

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

$App->type = 0;

/* GESTIONE CUSTOMERS */
$App->customers = new stdClass;
Sql::initQuery($App->params->tables['cust'],array('*'),array(),'active = 1 AND (id_type = 2 OR id_type = 3)');
Sql::setOptions(array('fieldTokeyObj'=>'id'));
Sql::setOrder('surname ASC, name ASC');
$App->customers = Sql::getRecords();

switch(Core::$request->method) {

	case 'pagaInvSal':
		if ($App->id > 0) {

			Sql::initQuery($App->params->tables['InvSal'],array('pagata'),array(1,$App->id),'id = ?');
			Sql::updateRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(Config::$localStrings['fattura segnata come pagata']);
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listInvSal');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
		die();
	break;

	/* ARTICLES */
	case 'deleteArtInvSal':
		if ($App->id > 0) {
			$id_art = (isset(Core::$request->params[0]) ? intval(Core::$request->params[0]) : 0);
			if ($id_art > 0) {

				// cancello il record
				Sql::initQuery($App->params->tables['ArtSal'],array('id'),array($id_art),'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% cancellato'])).'!';
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvSal/'.$App->id.'/tab/2');

			} else {
				ToolsStrings::redirect(URL_SITE.'error/404');
			}

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;
	/* END ARTICLES */

	case 'deleteInvSal':
		if ($App->id > 0) {

			// controlla se ha figli
			$count = Sql::countRecordQry($App->params->tables['ArtSal'],'id','id_invoice = ?',array($App->id));
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			if ($count > 0) {
				$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articoli'],Config::$localStrings['Errore! Ci sono ancora %ITEM% associati!']));
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvSal/'.$App->id);
			}

			// cancello il record
			Sql::initQuery($App->params->tables['InvSal'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce-p'],Config::$localStrings['%ITEM% cancellata'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listInvSal');


		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'newInvSal':
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%']);
		$App->viewMethod = 'formNew';
		$App->tabActive = 1;
	break;

	case 'insertInvSal':

		if ($_POST) {

			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['InvSal'],Config::$localStrings,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newInvSal');
			}

			// controllo date
			DateFormat::checkDateFormatIniEndInterval($_POST['dateins'],$_POST['datesca'],'Y-m-d','>');
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.Config::$localStrings['Intervallo tra le due date errato!'];
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newInvSal');
			}

			// controllo numero fattura
			if (Sql::countRecordQry($App->params->tables['InvSal'],'id','id_customer = ? AND number_year = ? AND number = ?',array(intval($_POST['id_customer']),intval($_POST['number_year']),intval($_POST['number']))) > 0) {
				$_SESSION['message'] = '1|'.Config::$localStrings['il numero fattura esiste già!'];
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newInvSal');
			}

			// memorizza record
			Sql::insertRawlyPost($App->params->fields['InvSal'],$App->params->tables['InvSal']);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% inserita'])).'!';
			$tabActive = 1;

			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listInvSal');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'modifyInvSal':
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['modifica %ITEM%']);
		$App->viewMethod = 'formMod';
		$App->tabActive = 1;
		if ( (isset(Core::$request->params[0]) && Core::$request->params[0] == 'tab') && isset(Core::$request->params[1]) ) $App->tabActive = Core::$request->params[1];
	break;

	case 'updateInvSal':
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

				if ($_POST['quantity'] > 0 && $_POST['price_unity'] > 0) {
					$_POST = $Module->calculateArt($_POST);

					if (isset($_POST['artFormMode']) &&  $_POST['artFormMode'] == 'ins') {

						// parsa i campi
						Form::parsePostByFields($App->params->fields['ArtSal'],Config::$localStrings,array());
						if (Core::$resultOp->error > 0) {
							$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
							ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvSal/'.$App->id).'/tab/2';
						}

						// memorizza record
						Sql::insertRawlyPost($App->params->fields['ArtSal'],$App->params->tables['ArtSal']);
						if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

						$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% inserito'])).'!';
						$tabActive = 2;

						ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvSal/'.$App->id.'/tab/'.$tabActive);

					}

					if (isset($_POST['artFormMode']) &&  $_POST['artFormMode'] == 'mod') {

						$id_art = (isset($_POST['id_article']) ? intval($_POST['id_article']) : 0);

						// parsa i campi
						Form::parsePostByFields($App->params->fields['ArtSal'],Config::$localStrings,array());
						if (Core::$resultOp->error > 0) {
							$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
							ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvSal/'.$App->id).'/tab/2';
						}

						// memorizza record
						Sql::updateRawlyPost($App->params->fields['ArtSal'],$App->params->tables['ArtSal'],'id',$id_art);
						if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

						$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% modificato'])).'!';
						$tabActive = 2;

						ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvSal/'.$App->id.'/tab/'.$tabActive);

					}

				}

			} else {

				// form invoice */

				// parsa i post in base ai campi
				Form::parsePostByFields($App->params->fields['InvSal'],Config::$localStrings,array());
   			if (Core::$resultOp->error > 0) {
					$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvSal/'.$App->id);
				}

				// controllo date
				DateFormat::checkDateFormatIniEndInterval($_POST['dateins'],$_POST['datesca'],'Y-m-d','>');
				if (Core::$resultOp->error > 0) {
					$_SESSION['message'] = '1|'.Config::$localStrings['Intervallo tra le due date errato!'];
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvSal/'.$App->id);
				}

				// memorizza record
				Sql::updateRawlyPost($App->params->fields['InvSal'],$App->params->tables['InvSal'],'id',$App->id);
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% modificata'])).'!';
				$tabActive = 1;
			}

			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyInvSal/'.$App->id.'/tab/'.$tabActive);
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listInvSal');
			}


		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

/*  AJAX */

	case 'getArticleAjaxInvSal':
		$App->item = new stdClass;
		$id = (isset($_POST['id']) ? intval($_POST['id']) : 0);
		if ($id > 0) {
			Sql::initQuery($App->params->tables['ArtSal'],array('*'),array($id),'id = ?');
			$App->item = Sql::getRecord();
			$json = json_encode($App->item);
			echo $json;
		}
		die();
	break;

/* END AJAX */


	case 'listAjaxInvSal':
		//Core::setDebugMode(1);
		//print_r($_REQUEST);

		/* limit */
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
			}
		/* end limit */

		/* orders */
		$orderFields = array('id','dateins','datesca','ragione_sociale','number','note','total','total_tax','total_invoice');
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
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('fieldprefix'=>'ite.','onlyuser'=>true));
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
				list($w,$fv) = Sql::getClauseVarsFromAppSession($_REQUEST['search']['value'],$App->params->fields['InvSal'],'');
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

		$table = $App->params->tables['InvSal']." AS ite";
		//$table .= " LEFT JOIN ".$App->params->tables['cust']." AS cus  ON (ite.id_customer = cus.id)";
		$table .= " LEFT JOIN ".$App->params->tables['ArtSal']." AS art  ON (ite.id = art.id_invoice)";
		$fields[] = 'ite.*';
		$fields[] = "SUM(art.price_total) AS total,SUM(art.price_tax) AS total_tax";
		$fields[] = "SUM(art.price_total) + ((SUM(art.price_total) * ite.tax) / 100) + ((SUM(art.price_total) * ite.rivalsa) / 100) AS total_invoice";

		/* conta tutti i records */
		$recordsTotal = Sql::countRecordQry($App->params->tables['InvSal']." AS ite",'id',$where,$fieldsValue);
		$recordsFiltered = $recordsTotal;

		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),'',array('groupby'=>'ite.id'));
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);

		}

		Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),$limit,array('groupby'=>'ite.id'));
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		//print_r($obj);
		/* sistemo dati */
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				/* crea la colonna actions */
				$pdf = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/invoicesExpPdf/'.$value->id.'" title="'.ucfirst(Config::$localStrings['esporta in pdf']).' '.Config::$localStrings['la voce'].'" target="_blank"><i class="fas fa-print"></i></a>';
				$actions = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/modifyInvSal/'.$value->id.'" title="'.ucfirst(Config::$localStrings['modifica']).' '.Config::$localStrings['la voce'].'"><i class="far fa-edit"></i></a><a class="btn btn-default btn-sm confirmdelete" href="'.URL_SITE.Core::$request->action.'/deleteInvSal/'.$value->id.'" title="'.ucfirst(Config::$localStrings['cancella']).' '.Config::$localStrings['la voce'].'"><i class="fas fa-trash-alt"></i></a>';
				/* calcola tassa aggiuntiva */
				$invoiceTotalTax = 0;
				if ($value->tax > 0) $invoiceTotalTax = ($value->total * $value->tax) / 100;

				/* calcola rivalsa */
				$invoiceTotalRivalsa = 0;
				if ($value->rivalsa > 0) $invoiceTotalRivalsa = ($value->total * $value->rivalsa) / 100;

				// numero fattura automatico
				$value->invoice_number = $value->id_customer.'-'.intval($value->number).'-'.$value->number_year;


				$value->totalLabel = '€ '.number_format($value->total,2,',','.');
				$value->totalTaxesLabel = '€ '.number_format($value->total_tax + $invoiceTotalTax + $invoiceTotalRivalsa,2,',','.');
				$value->totalInvoiceLabel = '€ '.number_format($value->total + $invoiceTotalTax + $invoiceTotalRivalsa,2,',','.');
				$value->totalInvoiceLabel = '€ '.number_format($value->total_invoice,2,',','.');

				$pagata = ' <a href="#" class="btn btn-info btn-sm"><i class="fas fa-money success" aria-hidden="true"></i></a>';
				if ($value->pagata == 1) $pagata = ' <a href="'.URL_SITE.Core::$request->action.'/pagaInvSal/'.$value->id.'" title="'.ucfirst(Config::$localStrings['segna come pagata']).'" class="btn btn-success btn-sm segnapagata"><i class="fas fa-money-bill-alt success" aria-hidden="true"></i></a>';
				if ($value->pagata == 0)
				{
					$pagata = ' <a href="'.URL_SITE.Core::$request->action.'/pagaInvSal/'.$value->id.'" title="'.ucfirst(Config::$localStrings['segna come pagata']).'" class="btn btn-danger btn-sm segnapagata"><i class="fas fa-money-bill-alt success" aria-hidden="true"></i></a>';

				}

				$tablefields = array(
					'id'=>$value->id,
					'invoice_number' => $value->invoice_number,
					'dateinslocal'=>DateFormat::dateFormating($value->dateins,'Y-m-d',Config::$localStrings['data format']),
					'datescalocal'=>DateFormat::dateFormating($value->datesca,'Y-m-d',Config::$localStrings['data format']),
					'pagata'=>$pagata,
					'customer'=>$value->customer_ragione_sociale,
					'note'=>$value->note,
					'total'=>$value->totalLabel,
					'totaltaxes'=>$value->totalTaxesLabel,
					'totalinvoice'=>$value->totalInvoiceLabel,
					'pdf'=>$pdf,
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
		$App->item->dateins = Config::$nowDate;
		$App->item->datesca = Config::$nowDate;
		$App->item->rivalsa = $App->company->rivalsa;
		$App->item->tax = 0;
		$App->item->active = 1;
		$App->item->stampa_quantita = 1;
		$App->item->stampa_unita = 1;

		$App->item->id_customer = 0;

		$App->item->number = 1;
		// preleva l'ultimo numero della fattura
		$qryFields = array("MAX(number) + 1 AS newnumber");
		$qryFieldsValues = array($App->item->id_customer,$App->params->defaultNumberYear);
		$clause = 'id_customer = ? AND number_year = ?';
		Sql::initQuery($App->params->tables['InvSal'],$qryFields,$qryFieldsValues,$clause);
		$obj = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
		if (isset($obj->newnumber)) $App->item->number = $obj->newnumber;


		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['InvSal']);
		$App->templateApp = 'formInvSal.html';
		$App->methodForm = 'insertInvSal';
		$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formInvSal.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formInvSal.js"></script>';
	break;

	case 'formMod':
		if ($App->id > 0) {
			$App->item = new stdClass;
			$App->item->dateins = Config::$nowDate;
			Sql::initQuery($App->params->tables['InvSal'],array('*'),array($App->id),'id = ?');
			$App->item = Sql::getRecord();
			if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['InvSal']);

			$App->item_articles = new stdClass;
			$qryFields = array("*");
			$qryFieldsValues = array($App->id);
			$clause = 'id_invoice = ?';
			Sql::initQuery($App->params->tables['ArtSal'],$qryFields,$qryFieldsValues,$clause);
			Sql::setResultPaged(false);
			Sql::setOrder('id ASC');
			if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
			/* sistemo dati */
			$tot_price_unity = 0;
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

					$tot_price_unity += $value->price_unity;
					$tot_price_total += $value->price_total;
					$tot_quantity_total += $value->quantity;
					$tot_price_tax += $value->price_tax;
					$tot_total += $value->total;

					$arr[] = $value;
					}
				}
			$App->item_articles = $arr;

			$App->item->art_tot_price_unity_label = '€ '.number_format($tot_price_unity,2,',','.');
			$App->item->art_tot_price_total_label = '€ '.number_format($tot_price_total,2,',','.');
			$App->item->art_tot_quantity_label = $tot_quantity_total;
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

			$App->templateApp = 'formInvSal.html';
			$App->methodForm = 'updateInvSal';
			$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formInvSal.css" rel="stylesheet">';
			$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formInvSal.js"></script>';

			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listInvPur');
				die();
				}
	break;

	case 'list':
		$App->item = new stdClass;
		$App->item->dateins = Config::$nowDate;
		$App->item->datesca = Config::$nowDate;
		$App->pageSubTitle = preg_replace('/%ITEMS%/',Config::$localStrings['voci'],Config::$localStrings['lista delle %ITEMS%']);
		$App->templateApp = 'listInvSal.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listInvSal.js"></script>';
	break;

	default:
	break;
	}
?>
