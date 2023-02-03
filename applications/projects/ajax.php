<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * projects/ajax.php v.1.0.0. 04/07/2018
*/

switch(Core::$request->method) {
	
	case 'listTimecardsAjax':
		$idproject = intval(Core::$request->param);
		if ($idproject > 0) {
			$output = '';
			
			if (in_array(DB_TABLE_PREFIX.'thirdparty',$App->tablesOfDatabase) && file_exists(PATH.$App->pathApplications."thirdparty/index.php") && Permissions::checkIfModulesIsReadable('thirdparty',$App->userLoggedData) === true) {
				
				//  preleva tutte le timecard del progetto
				$table = $App->params->tables['time'];
				$fields = array('*');
				$fieldsVal = array($idproject);
				$where = 'id_project = ?';
				
				// tempo ricerca
				$when = (isset(Core::$request->params[0]) && Core::$request->params[0] != '' ? Core::$request->params[0] : 'cm');
								
				if ($when == 'cm') $where .= " AND MONTH(datains) = MONTH(CURRENT_DATE()) AND YEAR(datains) = YEAR(CURRENT_DATE())";
				if ($when == 'pm') {
					$where .= " AND MONTH(datains) = MONTH(CURRENT_DATE()) - 1";
					// aggiunge l'anno in base al mese (se il mese corrente è 1 aggiunte l'anno precedente)
					if (intval(date('m')) == 1) {
						$where .= " AND YEAR(datains) = YEAR(CURRENT_DATE()) - 1";
					} else {
						$where .= " AND YEAR(datains) = YEAR(CURRENT_DATE())";
					}
				}
			
				Sql::initQuery($table,$fields,$fieldsVal,$where);
				Sql::setOrder('datains DESC');
				$objalltime = Sql::getRecords();
							
				// crea output
				$output .= '<div class="table-responsive"><table class="table table-fixed table-sm">';
				$output .= '<thead>';
					$output .= '<tr><th scope="col" class="col-3">'.ucfirst($_lang['data']).'</th><th scope="col" class="col-3">'.ucfirst($_lang['contenuto']).'</th><th scope="col" class="col-3">'.ucfirst($_lang['inizio']).' - '.ucfirst($_lang['fine']).'</th><th scope="col" class="col-3">'.ucfirst($_lang['tempo lavorato']).'</th></tr>';
				$output .= '</thead>';
				$output .= '<tbody>';
				
				if (is_array($objalltime) && count($objalltime) > 0) {
					foreach ($objalltime AS $value) {
						$output .= '<tr><th scope="row" class="col-3">'.$value->datains.'</th><td class="col-3">'.$value->content.'</td><td class="col-3">'.$value->starttime.' - '.$value->endtime.'</td><td class="col-3 text-right">'.$value->worktime.'</td></tr>';
					}
				}		
				$output .= '</tbody>';
				$output .= '</table></div>';
			} else {
				$output = 'Timecard non disponibile';
			}
			echo $output;
		}
		die();
	break;
	
	case 'getTimecardsProjectAjax':	
		$idproject = intval(Core::$request->param);
		if ($idproject > 0) {
			
			$date = DateTime::createFromFormat('Y-m-d',$App->nowDate);
			$monthyear = $date->format('Y-m');
			$date->modify('-1 month');
			$premonthyear = $date->format('Y-m');
			
			$totalltime = array();
			$totmonthtime = array();
			$totpremonthtime = array();
			
			/*  preleva tutte le timecard */
			$objalltime = new stdClass;
			Sql::initQuery($App->params->tables['time'],array('*'),array($idproject),'id_project = ?');
			Sql::setOrder('datains ASC');
			$objalltime = Sql::getRecords();				
			if (is_array($objalltime) && count($objalltime) > 0) {
				foreach ($objalltime AS $valuealltime) {
					$totalltime[] = $valuealltime->worktime;			
				}
			}
				
			/*  preleva tutte le timecard del mese corrente*/	
			$datarifini = $monthyear.'-01';
			$datarifend = $monthyear.'-31';			
			$objmonthtime = new stdClass;
			$where = "id_project = ? AND (datains >= '".$datarifini."' AND datains <= '".$datarifend."')";
			Sql::initQuery($App->params->tables['time'],array('*'),array($idproject),$where,'');
			Sql::setOrder('datains ASC');
			$objmonthtime = Sql::getRecords();				
			if (is_array($objmonthtime) && count($objmonthtime) > 0) {
				foreach ($objmonthtime AS $valuemonthtime) {
					$totmonthtime[] = $valuemonthtime->worktime;			
				}
			}				

			/*  preleva tutte le timecard del mese precedente */				
			$datarifini = $premonthyear.'-01';
			$datarifend = $premonthyear.'-31';			
			$objpremonthtime = new stdClass;
			$where = "id_project = ? AND (datains >= '".$datarifini."' AND datains <= '".$datarifend."')";
			Sql::initQuery($App->params->tables['time'],array('*'),array($idproject),$where,'');
			Sql::setOrder('datains ASC');
			$objpremonthtime = Sql::getRecords();				
			if (is_array($objpremonthtime) && count($objpremonthtime) > 0) {
				foreach ($objpremonthtime AS $valuepremonthtime) {
					$totpremonthtime[] = $valuepremonthtime->worktime;			
				}
			}				
						
			$valuetotalltime = DateFormat::sum_the_time($totalltime);
			$valuetotmonthtime = DateFormat::sum_the_time($totmonthtime);
			$valuetotpremonthtime = DateFormat::sum_the_time($totpremonthtime);
			$output = '<table class="table table-striped table-condensed"><tbody><tr><td>'.ucfirst($_lang['tempo lavorato totale']).':</td><td class="text-right">'.$valuetotalltime.'</td></tr><tr><td><strong>'.ucfirst($_lang['tempo lavorato mese corrente']).':</strong></td><td class="text-right"><strong>'.$valuetotmonthtime.'</strong></td></tr><tr><td>'.ucfirst($_lang['tempo lavorato mese precedente']).':</td><td class="text-right">'.$valuetotpremonthtime.'</td></tr></tbody></table>';
			echo $output;
			}
		die();	
	break;
	
	}
?>
