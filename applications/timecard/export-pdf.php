<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @autror Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * timecard/export-pdf.php v.1.3.0. 30/12/2020
*/
//ini_set('display_errors',1);
//print_r(Core::$request);
//Core::setDebugMode(1);

switch(Core::$request->method) {

	case '__timecardaSpdf':

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
		if (isset($_SESSION[$App->sessionNameAite]['id_project']) &&  intval($_SESSION[$App->sessionNameAite]['id_project']) > 0  ) {
			$whereAll .= $andAll.'id_project = ?';
			$andAll = ' AND ';
			$fieldsValueAll[] = intval($_SESSION[$App->sessionNameAite]['id_project']);
			$where .= $and.'id_project = ?';
			$and = ' and ';
			$fieldsValue[] = intval($_SESSION[$App->sessionNameAite]['id_project']);
		}

		// aggiunge per la data
		// se corrente mese
		if (isset($_SESSION[$App->sessionNameAite]['intervaldata']) &&  $_SESSION[$App->sessionNameAite]['intervaldata'] == 'cm' ) {
			$data = DateTime::createFromFormat('Y-m-d',Config::$nowDate);
			$montr = $data->format('m');
			$year = $data->format('Y');
			$whereAll .= $and.'datains LIKE ?';
			$andAll = ' and ';
			$fieldsValueAll[] = '%'.$year.'-'.$montr.'%';
			$where .= $and.'datains LIKE ?';
			$and = ' and ';
			$fieldsValue[] = '%'.$year.'-'.$montr.'%';
		}

		// se mese precedente
		if (isset($_SESSION[$App->sessionNameAite]['intervaldata']) &&  $_SESSION[$App->sessionNameAite]['intervaldata'] == 'pm' ) {
			$dt = DateTime::createFromFormat('Y-m-d',Config::$nowDate);
			$day = $dt->format('j');
			$dt->modify('first day of -1 month');
			$dt->modify('+' . (min($day, $dt->format('t')) - 1) . ' days');
			$montr = $dt->format('m');
			$year = $dt->format('Y');
			$whereAll .= $and.'datains LIKE ?';
			$andAll = ' and ';
			$fieldsValueAll[] = '%'.$year.'-'.$montr.'%';
			$where .= $and.'datains LIKE ?';
			$and = ' and ';
			$fieldsValue[] = '%'.$year.'-'.$montr.'%';
		}

		// se intervallo date
		if (isset($_SESSION[$App->sessionNameAite]['intervaldata']) &&  $_SESSION[$App->sessionNameAite]['intervaldata'] == 'id' ) {
			echo 'intervallo date';
			$dataini = (isset($_SESSION[$App->sessionNameAite]['dataini']) &&  $_SESSION[$App->sessionNameAite]['dataini'] != '' ? $_SESSION[$App->sessionNameAite]['dataini'] : Config::$nowDate);
			$dataend = (isset($_SESSION[$App->sessionNameAite]['dataend']) &&  $_SESSION[$App->sessionNameAite]['dataend'] != '' ? $_SESSION[$App->sessionNameAite]['dataend'] : Config::$nowDate);

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
		//print_r($fieldsValue);echo Sql::getQuery();die();

		$z = 0;
		if (is_array($obj) && count($obj) > 0) {

			echo '<table cellpadding="5" border="1" align="center" widtr="400" style="margin-bottom:20px;"><thead><tr> <th>id</th> <th>'.Config::$localStrings['utente'].'</th> <th>'.Config::$localStrings['progetto'].'</th> <th>'.Config::$localStrings['contenuto'].'</th> <th>'.Config::$localStrings['data'].'</th> <th>'.Config::$localStrings['ora inizio'].'</th> <th>'.Config::$localStrings['ora fine'].'</th> <th>'.Config::$localStrings['ore lavoro'].'</th> </tr></thead><tbody>';

			foreach ($obj AS $key=>$value) {
				$datatimecard[] = array
				(
					'utente'			=> $value->username,
					'Progetto'   		=> $value->project,
					'Contenuto'			=> $value->content,
					'Data'				=> $value->datains,
					'Inizio'			=> $value->starttime,
					'Fine'				=> $value->endtime,
					'Lavorato'			=> $value->worktime
				);

				$timecardpdf[$z]['progetto'] 	= $value->project;
				$timecardpdf[$z]['contenuto'] 	= $value->content;
				$timecardpdf[$z]['data'] 		= DateFormat::convertDateFormats($value->datains,'Y-m-d','d/m/Y',Config::$nowDate);
				$timecardpdf[$z]['orainizio']	= $value->starttime;
				$timecardpdf[$z]['orafine'] 	= $value->endtime;
				$timecardpdf[$z]['orelavoro'] 	= $value->worktime;
				$z++;
				echo '<tr> <td>'.$value->id.'</td> <td>'.$value->username.'</td> <td>'.$value->project.'</td> <td>'.$value->content.'</td> <td>'.$value->datains.'</td> <td>'.$value->starttime.'</td> <td>'.$value->endtime.'</td> <td>'.$value->worktime.'</td> </tr>';
			}
			echo '</tbody></table>';
		}
		die();

	break;

	case 'timecardaSpdf':

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
		if (isset($_SESSION[$App->sessionNameAite]['id_project']) &&  intval($_SESSION[$App->sessionNameAite]['id_project']) > 0  ) {
			$whereAll .= $andAll.'id_project = ?';
			$andAll = ' AND ';
			$fieldsValueAll[] = intval($_SESSION[$App->sessionNameAite]['id_project']);
			$where .= $and.'id_project = ?';
			$and = ' and ';
			$fieldsValue[] = intval($_SESSION[$App->sessionNameAite]['id_project']);
		}

		// aggiunge per la data
		// se corrente mese
		if (isset($_SESSION[$App->sessionNameAite]['intervaldata']) &&  $_SESSION[$App->sessionNameAite]['intervaldata'] == 'cm' ) {
			$data = DateTime::createFromFormat('Y-m-d',Config::$nowDate);
			$montr = $data->format('m');
			$year = $data->format('Y');
			$whereAll .= $and.'datains LIKE ?';
			$andAll = ' and ';
			$fieldsValueAll[] = '%'.$year.'-'.$montr.'%';
			$where .= $and.'datains LIKE ?';
			$and = ' and ';
			$fieldsValue[] = '%'.$year.'-'.$montr.'%';
		}

		// se mese precedente
		if (isset($_SESSION[$App->sessionNameAite]['intervaldata']) &&  $_SESSION[$App->sessionNameAite]['intervaldata'] == 'pm' ) {
			$dt = DateTime::createFromFormat('Y-m-d',Config::$nowDate);
			$day = $dt->format('j');
			$dt->modify('first day of -1 month');
			$dt->modify('+' . (min($day, $dt->format('t')) - 1) . ' days');
			$montr = $dt->format('m');
			$year = $dt->format('Y');
			$whereAll .= $and.'datains LIKE ?';
			$andAll = ' and ';
			$fieldsValueAll[] = '%'.$year.'-'.$montr.'%';
			$where .= $and.'datains LIKE ?';
			$and = ' and ';
			$fieldsValue[] = '%'.$year.'-'.$montr.'%';
		}

		// se intervallo date
		if (isset($_SESSION[$App->sessionNameAite]['intervaldata']) &&  $_SESSION[$App->sessionNameAite]['intervaldata'] == 'id' ) {
			//echo 'intervallo date';
			$dataini = (isset($_SESSION[$App->sessionNameAite]['dataini']) &&  $_SESSION[$App->sessionNameAite]['dataini'] != '' ? $_SESSION[$App->sessionNameAite]['dataini'] : Config::$nowDate);
			$dataend = (isset($_SESSION[$App->sessionNameAite]['dataend']) &&  $_SESSION[$App->sessionNameAite]['dataend'] != '' ? $_SESSION[$App->sessionNameAite]['dataend'] : Config::$nowDate);

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
		//echo Sql::getQuery();die();

		$z = 0;
		if (is_array($obj) && count($obj) > 0) {

			echo '<table cellpadding="5" border="1" align="center" widtr="400" style="margin-bottom:20px;"><thead><tr> <th>id</th> <th>'.Config::$localStrings['utente'].'</th> <th>'.Config::$localStrings['progetto'].'</th> <th>'.Config::$localStrings['contenuto'].'</th> <th>'.Config::$localStrings['data'].'</th> <th>'.Config::$localStrings['ora inizio'].'</th> <th>'.Config::$localStrings['ora fine'].'</th> <th>'.Config::$localStrings['ore lavoro'].'</th> </tr></thead><tbody>';

			foreach ($obj AS $key=>$value) {
				$datatimecard[] = array
				(
					'utente'			=> $value->username,
					'Progetto'   		=> $value->project,
					'Contenuto'			=> $value->content,
					'Data'				=> $value->datains,
					'Inizio'			=> $value->starttime,
					'Fine'				=> $value->endtime,
					'Lavorato'			=> $value->worktime
				);

				$timecardpdf[$z]['progetto'] 	= $value->project;
				$timecardpdf[$z]['contenuto'] 	= $value->content;
				$timecardpdf[$z]['data'] 		= DateFormat::convertDateFormats($value->datains,'Y-m-d','d/m/Y',Config::$nowDate);
				$timecardpdf[$z]['orainizio']	= $value->starttime;
				$timecardpdf[$z]['orafine'] 	= $value->endtime;
				$timecardpdf[$z]['orelavoro'] 	= $value->worktime;
				$z++;
				echo '<tr> <td>'.$value->id.'</td> <td>'.$value->username.'</td> <td>'.$value->project.'</td> <td>'.$value->content.'</td> <td>'.$value->datains.'</td> <td>'.$value->starttime.'</td> <td>'.$value->endtime.'</td> <td>'.$value->worktime.'</td> </tr>';
			}
			echo '</tbody></table>';
			
		}

die();
		// Initialize a ROS PDF class object using DIN-A4, with background color gray
		$pdf = new Cezpdf('a4','portrait','color',array(255,255,255));
		// Set pdf Bleedbox
		$pdf->ezSetMargins("50","50","70","70");
		// Use one of the pdf core fonts
		$mainFont = 'Helvetica';
		// Select the font
		$pdf->selectFont($mainFont);
		// Define the font size
		$size=12;
		// Modified to use the local file if it can
		$pdf->openHere('Fit');

		$datacreator = array (
			'Title'			=>'Timecard',
			'Author'		=>SITE_OWNER,
			'Subject'		=>'Lista archivio timecard',
			'Creator'		=>SITE_NAME,
			'Producer'		=>URL_SITE
			);
		$pdf->addInfo($datacreator);

		// riga info
		$info = ucfirst(Config::$localStrings['archivio timecard']);
		if (isset($_SESSION[$App->sessionNameAite]['intervaldata']) &&  $_SESSION[$App->sessionNameAite]['intervaldata'] == 'cm' ) {
			$info .= ' '.Config::$localStrings['mese corrente'];
		}
		if (isset($_SESSION[$App->sessionNameAite]['intervaldata']) &&  $_SESSION[$App->sessionNameAite]['intervaldata'] == 'pm' ) {
			$info .= ' '.Config::$localStrings['mese precedente'];
		}

		// eleco timecard
		$colspdf['progetto'] 		= '<b>'.ucfirst(Config::$localStrings['progetto']).'</b>';
		$colspdf['contenuto'] 		= '<b>'.ucfirst(Config::$localStrings['contenuto']).'</b>';
		$colspdf['data'] 			= '<b>'.ucfirst(Config::$localStrings['data']).'</b>';
		$colspdf['orainizio']		= '<b>'.ucwords(Config::$localStrings['ora inizio']).'</b>';
		$colspdf['orafine'] 		= '<b>'.ucwords(Config::$localStrings['ora fine']).'</b>';
		$colspdf['orelavoro'] 		= '<b>'.ucwords(Config::$localStrings['ore lavoro']).'</b>';
		$opt = array(
			'showHeadings'			=> 1,
			'gridlines'				=> EZ_GRIDLINE_DEFAULT,
			'showLines'				=> 1,
			'fontSize'				=> 8,
			'titleFontSize' 		=> 10,
			'width' 				=> 500,
			'maxWidth' 				=> 500,
			'cols'					=> array(
				'data'			=> array(
					'width'			=> 60
				),
				'orainizio'			=> array(
					'width'			=> 55
				),
				'orafine'			=> array(
					'width'			=> 55
				),
				'orelavoro'			=> array(
					'width'			=> 55
				)

			)

		);

		$col = $pdf->ezTable($timecardpdf, $colspdf,$info,$opt);

		//Output the pdf as stream, but uncompress
		$namefile = SanitizeStrings::urlslug($info) . '.pdf';
		$applicationtype = "application/pdf";
		header("Content-type: $applicationtype");
		header("Content-Disposition: attachment; filename=".basename($namefile).";");
		$pdf->ezStream(array('compress'=>0,'download'=>1,'Content-Disposition'=>$namefile));
		die();

	break;

	case 'timecardSpdf':

		$id_project = (isset(Core::$request->params[0]) && Core::$request->params[0] != '' ? intval(Core::$request->params[0]) : 0);
		$appdata = (isset(Core::$request->param) && Core::$request->param != '' ? Core::$request->param : Config::$nowDate);
		$data = DateTime::createFromFormat('Y-m-d',$appdata);
		$montr = $data->format('m');
		$year = $data->format('Y');
		$fv = array('%'.$year.'-'.$montr.'%');
		$where = "datains LIKE ?";
		if ($id_project > 0) {
			$fv[] = $id_project;
			$where .= " AND id_project = ?";
		}
		Sql::initQuery(
			$App->params->tables['item'].' AS t LEFT JOIN '.$App->params->tables['prog'].' AS p ON (t.id_project = p.id)',
			array('t.*,p.title AS project'),
				$fv,
				$where
			);
		Sql::setOrder('t.id_project ASC,t.datains ASC, t.starttime ASC');
 		$obj = Sql::getPdoObjRecords();

		// Initialize a ROS PDF class object using DIN-A4, with background color gray
		$pdf = new Cezpdf('a4','portrait','color',array(255,255,255));
		// Set pdf Bleedbox
		$pdf->ezSetMargins("50","50","70","70");
		// Use one of the pdf core fonts
		$mainFont = 'Helvetica';
		// Select the font
		$pdf->selectFont($mainFont);
		// Define the font size
		$size=12;
		// Modified to use the local file if it can
		$pdf->openHere('Fit');
		$datacreator = array (
			'Title'			=> Config::$localStrings['lista delle voci'],
			'Author'		=> SITE_OWNER,
			'Subject'		=> 'La lista delle timecard del mese selezionato',
			'Creator'		=> SITE_NAME,
			'Producer'		=> URL_SITE
			);
		$pdf->addInfo($datacreator);
		$info = ucfirst(Config::$localStrings['lista delle voci']);
		$info = ucfirst(Config::$localStrings['ore lavoro']);

		$colspdf['data'] 		= '<b>'.ucfirst(Config::$localStrings['data']).'</b>';
		$colspdf['contenuto'] 		= '<b>'.ucfirst(Config::$localStrings['contenuto']).'</b>';
		$colspdf['orainizio']		= '<b>'.ucwords(Config::$localStrings['ora inizio']).'</b>';
		$colspdf['orafine'] 		= '<b>'.ucwords(Config::$localStrings['ora fine']).'</b>';
		$colspdf['orelavoro'] 		= '<b>'.ucwords(Config::$localStrings['ore lavoro']).'</b>';
		$opt = array(
			'showHeadings'			=> 1,
			'gridlines'				=> EZ_GRIDLINE_DEFAULT,
			'showLines'				=> 1,
			'fontSize'				=> 8,
			'titleFontSize' 		=> 10,
			'width' 				=> 500,
			'maxWidth' 				=> 500,
			'cols'					=> array(
				'data'			=> array(
					'width'			=> 60
				),
				'orainizio'			=> array(
					'width'			=> 55
				),
				'orafine'			=> array(
					'width'			=> 55
				),
				'orelavoro'			=> array(
					'width'			=> 55
				)

			)
		);

 		$timecards = array();
 		$timecardsProjectLabel = array();
 		$timecardsProjectWorkTime = array();
 		$timecardsWorkTimeTotal = array();
 		while ($row = $obj->fetch()) {
			$timecards[$row->id_project][] = $row;
			if (!isset($timecardsProjectLabel[$row->id_project])) $timecardsProjectLabel[$row->id_project] =  $row->project;
			$timecardsProjectWorkTime[$row->id_project][] = $row->worktime;
		}

 		if (is_array($timecards) && count($timecards) > 0){

			foreach($timecards AS $key=>$timecard){

				$z = 0;
				$timecardpdf = array();

				$projectWorkTime = DateFormat::sum_the_time($timecardsProjectWorkTime[$key]);
				$timecardsWorkTimeTotal[] = $projectWorkTime;
				//echo '<table cellpadding="5" border="1" align="center" widtr="400" style="margin-bottom:20px;">';
					if (is_array($timecard) && count($timecard) > 0){

						$z1 = 0;
						$timecardpdf = array();

						foreach($timecard AS $keyt=>$time){
							//echo '<tr><td>'.$time->project.'</td><td>'.$time->datains.'</td><td>'.$time->worktime.'</td></tr>';
							$projectname = '';
							if ($projectname != $time->project) $projectname = $time->project;

							$timecardpdf[$z1]['data'] 		= DateFormat::convertDateFormats($time->datains,'Y-m-d','d/m/Y',Config::$nowDate);
							$timecardpdf[$z1]['contenuto'] 	= $time->content;
							$timecardpdf[$z1]['orainizio']	= $time->starttime;
							$timecardpdf[$z1]['orafine'] 	= $time->endtime;
							$timecardpdf[$z1]['orelavoro'] 	= $time->worktime;

							$z1++;
						}


						$z1++;
						$timecardpdf[$z1]['data'] 		= '';
						$timecardpdf[$z1]['contenuto'] 	= '';
						$timecardpdf[$z1]['orainizio']	= '';
						$timecardpdf[$z1]['orafine'] 	= '<b>'.ucwords(Config::$localStrings['totale']).'</b>';
						$timecardpdf[$z1]['orelavoro'] 	= $projectWorkTime;

					}



					$col = $pdf->ezTable($timecardpdf, $colspdf,$projectname,$opt);
					$pdf->ezSetDy(-15);
					//echo '<tr><td colspan="2">Totale</td><td>'.$projectWorkTime.'</td></tr>';
				//echo '</table>';
				$z++;
			}



		}

		$timecardpdf = array();

		$opt = array(
			'showHeadings'			=> 0,
			'gridlines'				=> EZ_GRIDLINE_DEFAULT,
			'showLines'				=> 0,
			'fontSize'				=> 11,
			'titleFontSize' 		=> 12,
			'width' 				=> 500,
			'maxWidth' 				=> 500,
			'cols'					=> array(
				'1'			=> array(
					'width'			=> 55
				)

			)
		);
		$colspdf['0'] 			= '';
		$colspdf['1'] 			= '';
		$timecardpdf[0]['0'] 			= '<b>'.ucfirst(Config::$localStrings['ore lavoro']).'</b>';
		$timecardpdf[0]['1'] 	= DateFormat::sum_the_time($timecardsWorkTimeTotal);

		$col = $pdf->ezTable($timecardpdf, $colspdf,'',$opt);
		/*
  		echo '<table cellpadding="5" border="1" align="center" widtr="400" style="margin-bottom:20px;">';
 		echo '<tr><td colspan="2">Totale</td><td>'.DateFormat::sum_the_time($timecardsWorkTimeTotal).'</td></tr>';
		echo '</table>';
		*/


		//Output the pdf as stream, but uncompress
		$orelavorodata = $year.'-'.$montr;
		$orelavoroprogetto = '';
		if (isset($timecardsProjectLabel[$id_project])) $orelavoroprogetto = $timecardsProjectLabel[$id_project];
		$foo = $info.'-'.$orelavoroprogetto.'-'.$orelavorodata;
		$namefile = SanitizeStrings::urlslug($foo) . '.pdf';
		$applicationtype = "application/pdf";
		header("Content-type: $applicationtype");
		header("Content-Disposition: attachment; filename=".basename($namefile).";");
		$pdf->ezStream(array('compress'=>0,'download'=>1,'Content-Disposition'=>$namefile));
		die();
	break;
}
die();
?>
