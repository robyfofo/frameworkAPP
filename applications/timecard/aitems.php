<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * timecard/aitems.php v.1.3.0. 28/08/2020
*/

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

if (isset($_POST['itemsforpage'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable'])) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);

/* trova tutti i progetti */
$App->allprogetti = new stdClass;
Sql::initQuery($App->params->tables['prog'],array('*'),array(),'active = 1','current DESC');
$App->allprogetti = Sql::getRecords();

switch(Core::$request->method) {

	case 'exportXlsAite';

		/* orders */
		$order = array();
		$order[] = 'datains DESC';
		$order[] = 'starttime DESC';
		$order[] = 'project DESC';

		$orderFields = array('ite.id','ite.id_user','ite.id_project','ite.content','ite.datains','ite.starttime','ite.endtime','ite.worktime');
		if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {
			foreach ($_REQUEST['order'] AS $key=>$value)	{
				$order[] = $orderFields[$value['column']].' '.$value['dir'];
			}
		}

		$table = $App->params->tables['item']." AS ite";
		$fields[] = "ite.*";

		/* search */
		$whereAll = '';
		$andAll = '';
		$fieldsValueAll = array();
		$where = '';
		$and = '';
		$fieldsValue = array();

		/* aggiunge campi join */
		$App->params->fields['item']['pro.title'] = array('searchTable'=>true,'type'=>'varchar');
		$App->params->fields['item']['usr.username'] = array('searchTable'=>true,'type'=>'float');

		// aggiunge per il projetto
		if (isset($_SESSION[$App->sessionName]['id_project']) &&  intval($_SESSION[$App->sessionName]['id_project']) > 0  ) {
			$whereAll .= $andAll.'id_project = ?';
			$andAll = ' AND ';
			$fieldsValueAll[] = intval($_SESSION[$App->sessionName]['id_project']);
			$where .= $and.'id_project = ?';
			$and = ' and ';
			$fieldsValue[] = intval($_SESSION[$App->sessionName]['id_project']);
		}

		// aggiunge per la data
		// se corrente mese
		if (isset($_SESSION[$App->sessionName]['intervaldata']) &&  $_SESSION[$App->sessionName]['intervaldata'] == 'cm' ) {
			$data = DateTime::createFromFormat('Y-m-d',$App->nowDate);
			$month = $data->format('m');
			$year = $data->format('Y');
			$whereAll .= $and.'datains LIKE ?';
			$andAll = ' and ';
			$fieldsValueAll[] = '%'.$year.'-'.$month.'%';
			$where .= $and.'datains LIKE ?';
			$and = ' and ';
			$fieldsValue[] = '%'.$year.'-'.$month.'%';
		}

		// se mese precedente
		if (isset($_SESSION[$App->sessionName]['intervaldata']) &&  $_SESSION[$App->sessionName]['intervaldata'] == 'pm' ) {
			$dt = DateTime::createFromFormat('Y-m-d',$App->nowDate);
			$day = $dt->format('j');
			$dt->modify('first day of -1 month');
			$dt->modify('+' . (min($day, $dt->format('t')) - 1) . ' days');
			$month = $dt->format('m');
			$year = $dt->format('Y');
			$whereAll .= $and.'datains LIKE ?';
			$andAll = ' and ';
			$fieldsValueAll[] = '%'.$year.'-'.$month.'%';
			$where .= $and.'datains LIKE ?';
			$and = ' and ';
			$fieldsValue[] = '%'.$year.'-'.$month.'%';
		}

		// se intervallo date
		if (isset($_SESSION[$App->sessionName]['intervaldata']) &&  $_SESSION[$App->sessionName]['intervaldata'] == 'id' ) {
			$dataini = (isset($_SESSION[$App->sessionName]['dataini']) &&  $_SESSION[$App->sessionName]['dataini'] != '' ? $_SESSION[$App->sessionName]['dataini'] : $App->nowDate);
			$dataend = (isset($_SESSION[$App->sessionName]['dataend']) &&  $_SESSION[$App->sessionName]['dataend'] != '' ? $_SESSION[$App->sessionName]['dataend'] : $App->nowDate);

			$dataisoini = $dataini .' 00:00:00';
			$dataisoend = $dataend .' 23:59:59';
			DateFormat::checkDateTimeIsoIniEndInterval($dataisoini,$dataisoend,'>');
			if (Core::$resultOp->error == 0) {

				$whereAll .= $and.'datains BETWEEN CAST( ? AS DATE) AND CAST( ? AS DATE)';
				//$whereAll .= $and."datains BETWEEN CAST( '".$dataini."' AS DATE) AND CAST( '".$dataend."' AS DATE)";
				$andAll = ' and ';
				$fieldsValueAll[] = $dataini;
				$fieldsValueAll[] = $dataend;
				$where .= $and.'datains BETWEEN CAST( ? AS DATE) AND CAST( ? AS DATE)';
				//$where .= $and."datains BETWEEN CAST( '".$dataini."' AS DATE) AND CAST( '".$dataend."' AS DATE)";
				$and = ' and ';
				$fieldsValue[] = $dataini;
				$fieldsValue[] = $dataend;
			}
		}

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

		// SEARCH QUERY
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
		// END SEARCH QUERY

		$table .= " LEFT JOIN ".$App->params->tables['prog']." AS pro  ON (ite.id_project = pro.id)";
		$table .= " LEFT JOIN ".$App->params->tables['user']." AS usr  ON (ite.users_id = usr.id)";

		$fields[] = "pro.title AS project";
		$fields[] = "usr.username AS username";

		/* conta tutti i records */
		$recordsTotal = Sql::countRecordQry($table,'ite.id',$whereAll,$fieldsValueAll);
		$recordsFiltered = $recordsTotal;

		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),'',array());
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);
		}

		Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),'');
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();

		//echo Sql::getQuery();//die();
		//print_r($fieldsValue);
		//print_r($obj);die();

		if (is_array($obj) && count($obj) > 0) {

			foreach ($obj AS $key=>$value) {
				$datatimecard[] = array
				(
				'Utente'				=>$value->username,
				'Progetto'   			=> $value->project,
				'Contenuto'				=> $value->content,
				'Data'					=> DateFormat::convertDateFormats($value->datains,'Y-m-d','d/m/Y',$App->nowDate),
				'Ora Inizio'			=> $value->starttime,
				'Ora Fine'				=> $value->endtime,
				'Ore Lavoro'			=> $value->worktime
				);
			}



			//if (count($data) == 0) ToolsStrings::redirect(URL_SITE.'error/404');

			// Create new Spreadsheet object
			$spreadSheet = new Spreadsheet();
			$workSheet = $spreadSheet->getActiveSheet();
			// Create new Spreadsheet object
			$workSheet->fromArray(array_keys($datatimecard[0]), NULL, 'A1');
			$workSheet->fromArray($datatimecard,NULL,'A2');
			foreach (range('A','I') as $col) {
				$workSheet->getColumnDimension($col)->setAutoSize(true);
			}

			// Redirect output to a client’s web browser (Xls)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="timecards.xls"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0

			$writer = IOFactory::createWriter($spreadSheet, 'Xls');
			$writer->save('php://output');
		}
		die();
	break;

	case 'deleteAite':
		if ($App->id > 0) {
			Sql::initQuery($App->params->tables['item'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error == 0) {
				$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% cancellata'])).'!';
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listAite');
			} else {
				ToolsStrings::redirect(URL_SITE.'error');
			}
		}	else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
		die();
	break;

	case 'listAjaxAite':
		//Core::setDebugMode(1);
		//print_r($_REQUEST);

		/* limit */
		$limit = '';
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
		}
		/* end limit */

		/* orders */
		$orderFields = array('ite.id','ite.users_id','ite.id_project','ite.content','ite.datains','ite.starttime','ite.endtime','ite.worktime');
		$order = array();
		if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {
			foreach ($_REQUEST['order'] AS $key=>$value)	{
				$order[] = $orderFields[$value['column']].' '.$value['dir'];
			}
		}
		/* end orders */

		$table = $App->params->tables['item']." AS ite";
		$fields[] = "ite.*";

		$whereAll = '';
		$andAll = '';
		$fieldsValueAll = array();
		$where = '';
		$and = '';
		$fieldsValue = array();

		/* aggiunge campi join */
		$App->params->fields['item']['pro.title'] = array('searchTable'=>true,'type'=>'varchar');
		$App->params->fields['item']['usr.username'] = array('searchTable'=>true,'type'=>'float');

		// aggiunge per il projetto
		if (isset($_REQUEST['id_project']) &&  intval($_REQUEST['id_project']) > 0  ) {
			$whereAll .= $andAll.'id_project = ?';
			$andAll = ' AND ';
			$fieldsValueAll[] = intval($_REQUEST['id_project']);
			$where .= $and.'id_project = ?';
			$and = ' and ';
			$fieldsValue[] = intval($_REQUEST['id_project']);
			$_SESSION[$App->sessionName]['id_project'] = $_REQUEST['id_project'];
		} else {
			$_SESSION[$App->sessionName]['id_project'] = 0;
		}


		// aggiunge per la data

		// se corrente mese
		if (isset($_REQUEST['intervaldata']) &&  $_REQUEST['intervaldata'] == 'cm' ) {
			$data = DateTime::createFromFormat('Y-m-d',$App->nowDate);
			$month = $data->format('m');
			$year = $data->format('Y');
			$whereAll .= $and.'datains LIKE ?';
			$andAll = ' and ';
			$fieldsValueAll[] = '%'.$year.'-'.$month.'%';
			$where .= $and.'datains LIKE ?';
			$and = ' and ';
			$fieldsValue[] = '%'.$year.'-'.$month.'%';
			$_SESSION[$App->sessionName]['intervaldata'] = $_REQUEST['intervaldata'];
		}

		// se mese precedente
		if (isset($_REQUEST['intervaldata']) &&  $_REQUEST['intervaldata'] == 'pm' ) {
			$dt = DateTime::createFromFormat('Y-m-d',$App->nowDate);
			$day = $dt->format('j');
			$dt->modify('first day of -1 month');
			$dt->modify('+' . (min($day, $dt->format('t')) - 1) . ' days');
			$month = $dt->format('m');
			$year = $dt->format('Y');
			$whereAll .= $and.'datains LIKE ?';
			$andAll = ' and ';
			$fieldsValueAll[] = '%'.$year.'-'.$month.'%';
			$where .= $and.'datains LIKE ?';
			$and = ' and ';
			$fieldsValue[] = '%'.$year.'-'.$month.'%';
			$_SESSION[$App->sessionName]['intervaldata'] = $_REQUEST['intervaldata'];
		}

		// se intervallo date
		$dataini = (isset($_REQUEST['dataini']) &&  $_REQUEST['dataini'] != '' ? $_REQUEST['dataini'] : $App->nowDate);
		$dataend = (isset($_REQUEST['dataend']) &&  $_REQUEST['dataend'] != '' ? $_REQUEST['dataend'] : $App->nowDate);
		if (isset($_REQUEST['intervaldata']) &&  $_REQUEST['intervaldata'] == 'id' ) {
			$dataisoini = $dataini .' 00:00:00';
			$dataisoend = $dataend .' 23:59:59';
			DateFormat::checkDateTimeIsoIniEndInterval($dataisoini,$dataisoend,'>');
			if (Core::$resultOp->error == 0) {

				$whereAll .= $and.'datains BETWEEN CAST( ? AS DATE) AND CAST( ? AS DATE)';
				//$whereAll .= $and."datains BETWEEN CAST( '".$dataini."' AS DATE) AND CAST( '".$dataend."' AS DATE)";
				$andAll = ' and ';
				$fieldsValueAll[] = $dataini;
				$fieldsValueAll[] = $dataend;
				$where .= $and.'datains BETWEEN CAST( ? AS DATE) AND CAST( ? AS DATE)';
				//$where .= $and."datains BETWEEN CAST( '".$dataini."' AS DATE) AND CAST( '".$dataend."' AS DATE)";
				$and = ' and ';
				$fieldsValue[] = $dataini;
				$fieldsValue[] = $dataend;

				$_SESSION[$App->sessionName]['intervaldata'] = $_REQUEST['intervaldata'];
			}

		}
		$_SESSION[$App->sessionName]['dataini'] = $dataini;
		$_SESSION[$App->sessionName]['dataend'] = $dataend;

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

		// SEARCH QUERY
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
		// END SEARCH QUERY

		$table .= " LEFT JOIN ".$App->params->tables['prog']." AS pro  ON (ite.id_project = pro.id)";
		$table .= " LEFT JOIN ".$App->params->tables['user']." AS usr  ON (ite.users_id = usr.id)";
		$fields[] = "pro.title AS project";
		$fields[] = "usr.username AS username";

		/* conta tutti i records */
		$recordsTotal = Sql::countRecordQry($table,'ite.id',$whereAll,$fieldsValueAll);
		$recordsFiltered = $recordsTotal;

		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),'',array());
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);
		}

		Sql::initQuery($table,$fields,$fieldsValue,$where,implode(', ', $order),$limit);
		if (Core::$resultOp->error <> 1) $obj = Sql::getRecords();
		//echo Sql::getQuery();die();

		/* sistemo dati */
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				$actions = '<a class="btn btn-sm btn-default confirmdelete" href="'.URL_SITE.Core::$request->action.'/deleteAite/'.$value->id.'" title="'.ucfirst($_lang['cancella']).' '.$_lang['la voce'].'"><i class="fas fa-trash-alt"></i></a>';
				$tablefields = array(
					'id'=>$value->id,
					'id_user'=>$value->username,
					'project'=>$value->project,
					'content'=>$value->content,
					'datains'=>DateFormat::convertDateFormats($value->datains,'Y-m-d',$_lang['data format'],$App->nowDate),
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
	default;
		$time = DateTime::createFromFormat('H:i:s',$App->nowTime);
		$App->timeIniTimecard =  $time->format('H:i');
		$time->add(new DateInterval('PT1H'));
		$App->timeEndTimecard = $time->format('H:i');
		$App->viewMethod = 'list';
	break;
	}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {

	case 'list':
		$App->pageSubTitle = $_lang['lista delle voci'];
		$App->templateApp = 'listAitems.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listAitems.js"></script>';
	break;

	default:
	break;
	}
?>
