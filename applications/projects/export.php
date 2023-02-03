<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * projects/export.php v.1.2.0. 16/03/2020
*/

switch(Core::$request->method) {
	
	case 'listTimecardsPdfExpo':
		$idproject = intval(Core::$request->param);
		if ($idproject > 0) {
			$output = '';
			
			if (Permissions::checkAccessUserModule('timecard',$App->userLoggedData,$App->user_modules_active) == true && in_array(DB_TABLE_PREFIX.'timecard',$App->tablesOfDatabase) && file_exists(PATH.$App->pathApplications."timecard/index.php")) {
				// prendo i dettagli del progetto
				Sql::initQuery($App->params->tables['item'],array('*'),array($idproject),'id = ?');
				$project = Sql::getRecord();
				//print_r($project);die();
				
				// prelevo dati azienda
				Sql::initQuery($App->params->tables['thirdparty'],array('*'),array($project->id_contact),'id = ?');
				$thirdparty = Sql::getRecord();
				//print_r($thirdparty);die();
			
							
				//  preleva tutte le timecard del progetto
				$table = $App->params->tables['time'];
				$fields = array('*');
				$fieldsVal = array($idproject);
				$where = 'id_project = ?';
				
				// tempo ricerca
				$when = (isset(Core::$request->params[0]) && Core::$request->params[0] != '' ? Core::$request->params[0] : '');
								
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
				
//print_r($objalltime);die();
				
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
					'Title'=>ucfirst(Config::$localStrings['progetto']).' '.$project->title,
					'Author'=>SITE_OWNER,
					'Subject'=>ucfirst(Config::$localStrings['progetto']).' '.$idproject->title,
					'Creator'=>SITE_NAME,
					'Producer'=>URL_SITE
					);
				$pdf->addInfo($datacreator);
				
				
				// intestazione ditta
				
				$pdf->ezSetY(800);
				$colsheadpdf = array('titolo'=>''); 
				$headpdf[0]['titolo'] = $thirdparty->ragione_sociale;
				$headpdf[1]['titolo'] = $thirdparty->street.' - '.$thirdparty->zip_code.' - '.$thirdparty->city.' ('.$thirdparty->province.')';
				$headpdf[2]['titolo'] = Config::$localStrings['P. IVA'].' '.$thirdparty->partita_iva.' - '.Config::$localStrings['C. Fiscale'].' '.$thirdparty->codice_fiscale;
				$headpdf[3]['titolo'] = ucfirst(Config::$localStrings['tempo lavorato al progetto']).': <b>'.$project->title.'</b>';	
				
				$y = $pdf->ezTable($headpdf,$colsheadpdf,'',
					array(
						'showHeadings'=> 0,
						'gridlines'=>EZ_GRIDLINE_DEFAULT,
						'width'=>500,
						'showLines'=>0,
						'shaded'=>0
					)
				);

				
				
				//$y = $pdf->ezTable($headpdf,$colsheadpdf,'',array('showHeadings'=> 0,'gridlines'=>EZ_GRIDLINE_DEFAULT,'width'=>400,'showLines'=>0,'fontSize'=>10,'shaded'=>0,'rowGap'=>2,'colGap'=>20,'xOrientation'=>'left','cols'=>array('titolo'=>array('showLines'=>1),'testo'=>array('showLines'=>0))));

					
				$pdf->ezSetDy(-10);
				
				// tabella timecards
				$z = 0;							
				if (is_array($objalltime) && count($objalltime) > 0) {
					foreach ($objalltime AS $key=>$value) {
						$timecardsTotal[] = $value->worktime;
						$timecards[$z]['data'] = $value->datains;
						$timecards[$z]['content'] = $value->content;
						$timecards[$z]['starttime'] = $value->starttime. ' - '.$value->endtime;;
						$timecards[$z]['worktime'] = $value->worktime;								
						$z++;									 
					}
				}
				
				$colsTimecards['data'] = '<b>'.ucfirst(Config::$localStrings['data']).'</b>';
				$colsTimecards['content'] = '<b>'.ucfirst(Config::$localStrings['contenuto']).'</b>';
				$colsTimecards['starttime'] = '<b>'.ucfirst(Config::$localStrings['inizio']).' - '.ucfirst(Config::$localStrings['fine']).'</b>';
				$colsTimecards['worktime'] = '<b>'.ucfirst(Config::$localStrings['tempo lavorato']).'</b>';
				
				$opt = array( 'showHeadings' => 1, 'gridlines'=> EZ_GRIDLINE_DEFAULT,'fontSize'=>8,'width'=>400,'shaded'=>0,'rowGap' =>4,'colGap'=>4,'showLines'=>1,'lineCol'=>array(0.7,0.7,0.7),
					'cols'=> array(
						'data'=>array('width'=>60,'justification' =>'left' ),
						'starttime'=>array('width'=>80,'justification' =>'right' ),
						'worktime'=>array('width'=>50,'justification' =>'right' )
					)					
				);						
				$y = $pdf->ezTable($timecards, $colsTimecards,'',$opt);
				
				
				// totali
				$pdf->ezSetDy(-5);
				$cols = array('titolo'=>'titolo','totale'=>'totale'); 
				$data[0]['titolo'] = '<b>'.ucfirst(Config::$localStrings['tempo lavorato totale']).'</b>';
				$data[0]['totale'] = DateFormat::sum_the_time($timecardsTotal);
				$pdf->ezTable($data, $cols,'',array(
					'showHeadings'=>0,
					'gridlines'=>EZ_GRIDLINE_DEFAULT,
					'fontSize' =>8,
					'width'=>400,
					'shaded'=>0,
					'rowGap'=>5,
					'colGap'=>10,
					'showLines'=>0,
					'lineCol'=>array(0.7,0.7,0.7),
					'cols'=>array(
						'totale'=>array('justification'=>'right'),
						)
					)
				);

					
				//Output the pdf as stream, but uncompress
				/*
				$namefile = ucfirst(Config::$localStrings['tempo lavorato']).'-'.Config::$localStrings['progetto'].'-'.$project->title.".pdf";
				$applicationtype = "application/pdf";   
				//header("Content-type: $applicationtype");
				//header("Content-Disposition: attachment; filename=".basename($namefile).";");
				
				$pdf->ezOutput();
				//$pdf->ezStream(array('compress'=>0,'download'=>0,'Content-Disposition'=>$namefile));	
				*/
				$pdf->ezStream();

			} else {
				$output = 'Timecard non disponibile';
			}
			echo $output;
		}
		die();
	break;
	
}
?>
