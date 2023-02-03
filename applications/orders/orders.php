<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/orders/invoice-sale.php v.1.3.0. 30/09/2020
*/

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

$App->type = 0;

/* GESTIONE CUSTOMERS */
$App->thirdparty = new stdClass;
Sql::initQuery($App->params->tables['thirdparty'],array('*'),array(),'active = 1 AND (id_type = 2 OR id_type = 3)');
Sql::setOptions(array('fieldTokeyObj'=>'id'));
Sql::setOrder('surname ASC, name ASC');
$App->thirdparty = Sql::getRecords();

switch(Core::$request->method) {

	/* ARTICLES */
	case 'deleteArtOrd':	
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {
			$id_art = (isset(Core::$request->params[0]) ? intval(Core::$request->params[0]) : 0);
			if ($id_art > 0) {
				// cancello il record
				Sql::initQuery($App->params->tables['articles'],array('id'),array($id_art),'id = ?');
				Sql::deleteRecord();
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% cancellato'])).'!';
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyOrders/'.$App->id.'/tab/2');
			} else {
				ToolsStrings::redirect(URL_SITE.'error/404');
			}
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;
	/* END ARTICLES */

	case 'deleteOrders':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {


			// controlla se ha articoli
			$count = Sql::countRecordQry($App->params->tables['articles'],'id','orders_id = ?',array($App->id));
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			if ($count > 0) {
				$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articoli'],Config::$localStrings['Errore! Ci sono ancora %ITEM% associati!']));
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyOrders/'.$App->id);
			}

			// cancello il record
			Sql::initQuery($App->params->tables['orders'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// cancello thirdparty
			Sql::initQuery($App->params->tables['orders_thirdparty'],array('id'),array($App->id),'orders_id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			// cancello company
			Sql::initQuery($App->params->tables['orders_company'],array('id'),array($App->id),'orders_id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% cancellato'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listOrders');


		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'newOrders':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		//Core::setDebugMode(1);
		$App->item = new stdClass;
		$App->item->dateins = $App->nowDate;
		$App->item->thirdparty_id = 0;
		$App->item->active = 1;

		$App->item->number = 1;
		// preleva l'ultimo numero della fattura
		$qryFields = array("MAX(number) + 1 AS newnumber");
		$qryFieldsValues = array($App->params->defaultNumberYear);
		$clause = 'number_year = ?';
		Sql::initQuery($App->params->tables['orders'],$qryFields,$qryFieldsValues,$clause);
		$obj = Sql::getRecord();
		//if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
		if (isset($obj->newnumber)) $App->item->number = $obj->newnumber;

		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['inserisci %ITEM%']);
		$App->methodForm = 'insertOrders';
		$App->viewMethod = 'form';
		$_SESSION[$App->sessionName]['formTabActive'] = 1;
	break;

	case 'insertOrders':
		if ($_POST) {		
			Core::setDebugMode(1);
			$App->item = new stdClass;

			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['orders'],Config::$localStrings,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newOrders');
			}
			// controllo numero fattura
			if (Sql::countRecordQry($App->params->tables['orders'],'id','number_year = ? AND number = ?',array(intval($_POST['number_year']),intval($_POST['number']))) > 0) {
				$_SESSION['message'] = '1|'.Config::$localStrings['il numero ordine esiste già!'];
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newOrders');
			}

			// company
			$_POST['company_id'] = $App->company->id;
			//print_r($App->company);
			//die();

			// memorizza record
			Sql::insertRawlyPost($App->params->fields['orders'],$App->params->tables['orders']);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			$order_id = Sql::getLastInsertedIdVar();

			// salvo thirdparty
			$f = array(
				'orders_id',
				'thirdparty_ragione_sociale',
				'thirdparty_name',
				'thirdparty_surname',
				'thirdparty_street',
				'thirdparty_comune',
				'thirdparty_zip_code',
				'thirdparty_provincia',
				'thirdparty_nation',
				'thirdparty_telephone',
				'thirdparty_email',
				'thirdparty_fax',
				'thirdparty_partita_iva',
				'thirdparty_codice_fiscale',
				'thirdparty_pec',
				'thirdparty_sid',
				'created'
			);
			$fv = array(
				$order_id,
				$_POST['thirdparty_ragione_sociale'],
				$_POST['thirdparty_name'],
				$_POST['thirdparty_surname'],
				$_POST['thirdparty_street'],
				$_POST['thirdparty_comune'],
				$_POST['thirdparty_zip_code'],
				$_POST['thirdparty_provincia'],
				$_POST['thirdparty_nation'],
				$_POST['thirdparty_telephone'],
				$_POST['thirdparty_email'],
				$_POST['thirdparty_fax'],
				$_POST['thirdparty_partita_iva'],
				$_POST['thirdparty_codice_fiscale'],
				$_POST['thirdparty_pec'],
				$_POST['thirdparty_sid'],
				$App->nowDateTime				
			);

			Sql::initQuery($App->params->tables['orders_thirdparty'],$f,$fv);
			Sql::insertRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			$thirdparty_id = Sql::getLastInsertedIdVar();

			// salvo company
			$f = array(
				'orders_id',
				'company_ragione_sociale',
				'company_name',
				'company_surname',
				'company_street',
				'company_comune',
				'company_zip_code',
				'company_provincia',
				'company_nation',
				'company_telephone',
				'company_email',
				'company_fax',
				'company_partita_iva',
				'company_codice_fiscale',
				'company_pec',
				'company_sid',
				'created'
			);
			$fv = array(
				$order_id,
				$App->company->ragione_sociale,
				$App->company->name,
				$App->company->surname,
				$App->company->street,
				$App->company->comune,
				$App->company->zip_code,
				$App->company->provincia,
				$App->company->nation,
				$App->company->telephone,
				$App->company->email,
				$App->company->fax,
				$App->company->partita_iva,
				$App->company->codice_fiscale,
				$App->company->pec,
				$App->company->sid,
				$App->nowDateTime		
			);

			Sql::initQuery($App->params->tables['orders_company'],$f,$fv);
			Sql::insertRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			$company_id = Sql::getLastInsertedIdVar();
			

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% inserito'])).'!';
			$_SESSION[$App->sessionName]['formTabActive'] = 1;
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listOrders');
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'modifyOrders':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		//Core::setDebugMode(1);
		$App->item = new stdClass;
		$App->item->dateins = $App->nowDate;
		Sql::initQuery($App->params->tables['orders'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404');  }

		// leggi i dati thirdparty
		// prelevo i dati thirdparty
		Sql::initQuery($App->params->tables['orders_thirdparty'],array('*'),array($App->id),'orders_id = ?');
		$obj = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
		if (isset($obj) && is_object($obj) && count((array)$obj) > 0) {
			foreach ($obj as $attr => $value) {
				$App->item->{$attr} = $value;
			}
		}
		//print_r($App->item);

		// legge articoli
		$App->item_articles = new stdClass;
		$qryFields = array("*");
		$qryFieldsValues = array($App->id);
		$clause = 'orders_id = ?';
		Sql::initQuery($App->params->tables['articles'],$qryFields,$qryFieldsValues,$clause);
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
		
		$App->item->invoiceTotal = (float)$tot_price_total + $tot_price_tax;
		$App->item->invoiceTotal_label = '€ '.number_format($App->item->invoiceTotal,2,',','.');
		// fine articoli
		
		$App->pageSubTitle = preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['modifica %ITEM%']);
		$App->methodForm = 'updateOrders';
		$App->viewMethod = 'form';
		$_SESSION[$App->sessionName]['formTabActive'] = 2;
		if (
			isset(Core::$request->params[0]) &&
			Core::$request->params[0] == 'tab' &&
			isset(Core::$request->params[1]) &&
			Core::$request->params[0] != ''
			) $_SESSION[$App->sessionName]['formTabActive'] = Core::$request->params[1];
		
	break;

	case 'updateOrders':
		if ($_POST) {
			if (isset($_POST['submitArtForm']) &&  $_POST['submitArtForm'] == 'submitArt') {
				$_POST['orders_id'] = $_POST['id'];
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
						Form::parsePostByFields($App->params->fields['articles'],Config::$localStrings,array());
						if (Core::$resultOp->error > 0) {
							$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
							ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyOrders/'.$App->id).'/tab/2';
						}
						// memorizza record
						Sql::insertRawlyPost($App->params->fields['articles'],$App->params->tables['articles']);
						if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
						$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% inserito'])).'!';
						$tabActive = 2;
						ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyOrders/'.$App->id.'/tab/'.$tabActive);
					}
					if (isset($_POST['artFormMode']) &&  $_POST['artFormMode'] == 'mod') {
						$id_art = (isset($_POST['id_article']) ? intval($_POST['id_article']) : 0);
						// parsa i campi
						Form::parsePostByFields($App->params->fields['articles'],Config::$localStrings,array());
						if (Core::$resultOp->error > 0) {
							$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
							ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyOrders/'.$App->id).'/tab/2';
						}
						// memorizza record
						Sql::updateRawlyPost($App->params->fields['articles'],$App->params->tables['articles'],'id',$id_art);
						if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
						$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['articolo'],Config::$localStrings['%ITEM% modificato'])).'!';
						ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyOrders/'.$App->id.'/tab/2');
					}
				}
			} else {

				//Core::setDebugMode(1);
				// form invoice */

				// parsa i post in base ai campi
				Form::parsePostByFields($App->params->fields['orders'],Config::$localStrings,array());
   				if (Core::$resultOp->error > 0) {
					$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
					ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyOrders/'.$App->id);
				}

				// memorizza record
				Sql::updateRawlyPost($App->params->fields['orders'],$App->params->tables['orders'],'id',$App->id);
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

				// aggiorno thirdparty
				$f = array(
					'thirdparty_ragione_sociale',
					'thirdparty_name',
					'thirdparty_surname',
					'thirdparty_street',
					'thirdparty_comune',
					'thirdparty_zip_code',
					'thirdparty_provincia',
					'thirdparty_nation',
					'thirdparty_telephone',
					'thirdparty_email',
					'thirdparty_fax',
					'thirdparty_partita_iva',
					'thirdparty_codice_fiscale',
					'thirdparty_pec',
					'thirdparty_sid'
				);

				$fv = array(
					$_POST['thirdparty_ragione_sociale'],
					$_POST['thirdparty_name'],
					$_POST['thirdparty_surname'],
					$_POST['thirdparty_street'],
					$_POST['thirdparty_comune'],
					$_POST['thirdparty_zip_code'],
					$_POST['thirdparty_provincia'],
					$_POST['thirdparty_nation'],
					$_POST['thirdparty_telephone'],
					$_POST['thirdparty_email'],
					$_POST['thirdparty_fax'],
					$_POST['thirdparty_partita_iva'],
					$_POST['thirdparty_codice_fiscale'],
					$_POST['thirdparty_pec'],
					$_POST['thirdparty_sid']
				);

				if (Sql::countRecordQry($App->params->tables['orders_thirdparty'],'id','orders_id = ?',array($App->id)) == 0) {
					array_unshift($f,'orders_id');
					array_unshift($fv,$App->id);
					$f[] = 'created';
					$fv[] = $App->nowDateTime;					
					Sql::initQuery($App->params->tables['orders_thirdparty'],$f,$fv);
					Sql::insertRecord();
				} else {
					$fv[] = $App->id;
					Sql::initQuery($App->params->tables['orders_thirdparty'],$f,$fv,'orders_id = ?');
					Sql::updateRecord();					
				}
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

				// aggiorno company
				$f = array(
					'company_ragione_sociale',
					'company_name',
					'company_surname',
					'company_street',
					'company_comune',
					'company_zip_code',
					'company_provincia',
					'company_nation',
					'company_telephone',
					'company_email',
					'company_fax',
					'company_partita_iva',
					'company_codice_fiscale',
					'company_pec',
					'company_sid'
				);
				
				$App->company->pec = '';
				$App->company->sid = '';

				$fv = array(
					$App->company->ragione_sociale,
					$App->company->name,
					$App->company->surname,
					$App->company->surname,
					$App->company->street,
					$App->company->zip_code,
					$App->company->provincia,
					$App->company->nation,
					$App->company->telephone,
					$App->company->email,
					$App->company->fax,
					$App->company->partita_iva,
					$App->company->codice_fiscale,
					$App->company->pec,
					$App->company->sid
				);

				if (Sql::countRecordQry($App->params->tables['orders_company'],'id','orders_id = ?',array($App->id)) == 0) {
					array_unshift($f,'orders_id');
					array_unshift($fv,$App->id);
					$f[] = 'created';
					$fv[] = $App->nowDateTime;
					Sql::initQuery($App->params->tables['orders_company'],$f,$fv);
					Sql::insertRecord();			
				} else {
					$fv[] = $App->id;
					Sql::initQuery($App->params->tables['orders_company'],$f,$fv,'orders_id = ?');
					Sql::updateRecord();					
				}
				if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }				
				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',Config::$localStrings['voce'],Config::$localStrings['%ITEM% modificato'])).'!';
				
			}

			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyOrders/'.$App->id);
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listOrders');
			}


		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
		die();
	break;


	case 'listAjaxOrders':
		//Core::setDebugMode(1);
		//print_r($_REQUEST);

		$whereAll = '';
		$andAll = '';
		$fieldsValueAll = array();
		$where = '';
		$and = '';
		$fieldsValue = array();
		$limit = '';
		$order = '';
		$filtering = false;
		$tableAll = $App->params->tables['orders']." AS ite";
		$table = $App->params->tables['orders']." AS ite";
		$table .= " LEFT JOIN ".$App->params->tables['articles']." AS art  ON (ite.id = art.orders_id)";
		$fields[] = "ite.*";
		$fields[] = "CONCAT(ite.number,'-',ite.number_year) AS order_number";
		$fields[] = "SUM(art.price_total) AS total,SUM(art.price_tax) AS total_tax,SUM(art.price_total) + SUM(art.price_tax) AS total_order";
		$whereFields = array(
			'order_number'=>array('field'=>"CONCAT(ite.number,'-',ite.number_year)",'type'=>'datafield'),
		);
		$orderFields = array('id','order_number','dateins','note','total','total_tax','total_order');

		// limit	
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
		}				
		//end limit

		// orders
		$orderTable = array();
		$order = '';	
		if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {		
			foreach ($_REQUEST['order'] AS $key=>$value)	{				
				$orderTable[] = $orderFields[$value['column']].' '.$value['dir'];
			}
		}
		if (is_array($orderTable) && count($orderTable) > 0) $order = implode(', ', $orderTable);
		// end orders

		/* permissions query */
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($App->userLoggedData,array('fieldprefix'=>'ite.','onlyuser'=>true));
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

		// filters
		$wf = array();
		$wfv = array();
		if (isset($_REQUEST['search']) && is_array($_REQUEST['search']) && count($_REQUEST['search']) > 0) {		
			if (isset($_REQUEST['search']['value']) && $_REQUEST['search']['value'] != '') {
				list($w,$fv) = Sql::getClauseVarsFromAppSession($_REQUEST['search']['value'],$App->params->fields['orders'],'',array('tableAlias'=>'ite'));
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

		// end filters

		//echo '<br> - whereall: '.$whereAll.'<br> - andall: '.$andAll.'<br> - fieldsValueAll: '.print_r($fieldsValueAll);
		//echo '<br> - where: '.$where.'<br> - and: '.$and.'<br> - fieldsValue: '.print_r($fieldsValue);
		//echo '<br> - order: '.$order.'<br> - limit: '.$limit;
		//die();
		
		// conta tutti i records
		$recordsTotal = Sql::countRecordQry($tableAll,'ite.id',$whereAll,$fieldsValueAll);
		$recordsFiltered = $recordsTotal;

		// se ci sono i filtri
		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,$order,'',array('groupby'=>'ite.id'));
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);
		}

		Sql::initQuery($table,$fields,$fieldsValue,$where,$order,$limit,array('groupby'=>'ite.id'));
		$obj = Sql::getRecords();
		//echo Sql::getQuery();
		//print_r($obj);
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				/* crea la colonna actions */
				$info = '<button type="button" href="'.URL_SITE.Core::$request->action.'/getCompanyDetailAjaxSr/'.$value->id.'" data-remote="false" data-target="#myModal" data-toggle="modal" title="'.ucfirst(Config::$localStrings['mostra i dati azienda']).'" class="btn btn-default btn-sm"><i class="fas fa-industry" ></i></button>';
				$info1 = '<button type="button" href="'.URL_SITE.Core::$request->action.'/getThirdpartyDetailAjaxSr/'.$value->id.'" data-remote="false" data-target="#myModal1" data-toggle="modal" title="'.ucfirst(Config::$localStrings['mostra i dati anagrafica']).'" class="btn btn-default btn-sm"><i class="fas fa-user"></i></button>';
				$pdf = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/ordersExpPdf/'.$value->id.'" title="'.ucfirst(Config::$localStrings['esporta in pdf']).' '.Config::$localStrings['voce'].'" target="_blank"><i class="fas fa-print"></i></a>';
				$actions = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/modifyOrders/'.$value->id.'" title="'.ucfirst(Config::$localStrings['modifica']).' '.Config::$localStrings['voce'].'"><i class="far fa-edit"></i></a><a class="btn btn-default btn-sm confirmdelete" href="'.URL_SITE.Core::$request->action.'/deleteOrders/'.$value->id.'" title="'.ucfirst(Config::$localStrings['cancella']).' '.Config::$localStrings['voce'].'"><i class="far fa-trash-alt"></i></a>';
				
				$value->totalLabel = '€ '.number_format($value->total,2,',','.');			
				$value->totalTaxesLabel = '€ '.number_format($value->total_tax,2,',','.');
				$value->totalOrderLabel = '€ '.number_format($value->total_order,2,',','.');
				$tablefields = array(
					'id'					=> $value->id,
					'order_number' 			=> $value->order_number,
					'dateinslocal'			=> DateFormat::convertDateFormats($value->dateins,'Y-m-d',Config::$localStrings['data format'],$App->nowDate),
					'note'					=> $value->note,
					'total'					=> $value->totalLabel,
					'totaltaxes'			=> $value->totalTaxesLabel,
					'totalorder'			=> $value->totalOrderLabel,
					'info'					=> $info.$info1,
					'pdf'					=> $pdf,
					'actions'				=> $actions
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

	case 'form':
		$App->templateApp = 'formOrder.html';
		$App->css[] = '<link href="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/css/formOrder.css" rel="stylesheet">';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formOrder.js"></script>';
	break;

	case 'list':
	default:	
		$App->item = new stdClass;
		$App->item->dateins = $App->nowDate;
		$App->item->datesca = $App->nowDate;
		$App->pageSubTitle = preg_replace('/%ITEMS%/',Config::$localStrings['voci'],Config::$localStrings['lista delle %ITEMS%']);
		$App->templateApp = 'listOrders.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listOrders.js"></script>';
	break;
}
?>
