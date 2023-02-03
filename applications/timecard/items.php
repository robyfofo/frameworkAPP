<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * timecard/items.php v.1.2.0. 10/12/2019
*/

/* trova tutti i progetti con timecard attivata */
$App->progetti = new stdClass;

Sql::initQuery($App->params->tables['prog'],array('*'),array(),'active = 1 AND timecard = 1','current DESC');
$App->progetti = Sql::getRecords();

/* trova tutti i progetti */
$App->allprogetti = new stdClass;
Sql::initQuery($App->params->tables['prog'],array('*'),array(),'active = 1','current DESC');
$App->allprogetti = Sql::getRecords();
	
/* trova tutte le timecard predefinite */
$App->allpreftimecard = new stdClass;
Sql::initQuery($App->params->tables['pite'],array('*'),array(),'active = 1');
$App->allpreftimecard = Sql::getRecords();

/* trova il progetto predefinito */
$App->projectForSelect = new stdClass;
Sql::initQuery($App->params->tables['prog'],array('*'),array(),'active = 1 AND current = 1','');
$App->projectForSelect = Sql::getRecords();
$App->currentProjectId = 0;
if (isset($App->projectForSelect->id)) $App->currentProjectId = $App->projectForSelect->id;
		
/* GESTIONE SELECT */

$App->idProjectForSelect = $App->currentProjectId;
if (isset($_MY_SESSION_VARS[$App->sessionName]['id_project'])) $App->idProjectForSelect = $_MY_SESSION_VARS[$App->sessionName]['id_project'];

/*
echo 'idProjectSession: '.$_MY_SESSION_VARS[$App->sessionName]['id_project'];
echo ' - idProjectForSelect: '.$App->idProjectForSelect;
echo ' - currentProjectId: '.$App->currentProjectId;
*/

switch(Core::$request->method) {
	
	case 'ajaxCheckTimeInterval':
		$id_project = (isset($_POST['id_project']) ? intval($_POST['id_project']) : 0);
		if ($id_project > 0) {
			
			$data = DateFormat::convertDatepickerToIso($_POST['data'],$_lang['datepicker data format'],'Y-m-d',$_MY_SESSION_VARS[$App->sessionName]['data-timecard']);					
			// controlla l'ora iniziale
			$starttimeiso = DateFormat::convertDatepickerToIso($_POST['startTime'],$_lang['datepicker time format'],'H:i:s','00:00:01');
			$endtimeiso = DateFormat::convertDatepickerToIso($_POST['endTime'],$_lang['datepicker time format'],'H:i:s','00:00:01');							
			$datatimeisoini = $data .' '.$starttimeiso;
			$datatimeisoend = $data .' '.$endtimeiso;
			DateFormat::checkDateTimeIsoIniEndInterval($datatimeisoini,$datatimeisoend,'>');
			if (Core::$resultOp->error == 0) {		
				$Module->checkTimeInterval($App->userLoggedData->id,$id_project,$data,$starttimeiso,$endtimeiso,$opt=array());
			}
			
		} else {
			Core::$resultOp->error = 1;
		}
			
		echo Core::$resultOp->error;
		die();
	break;

	case 'modappData':
		if (isset($_POST['appdata'])) {
			$data = DateFormat::convertDatepickerToIso($_POST['appdata'],$_lang['datepicker data format'],'Y-m-d',$App->nowDate);
			$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'data-timecard',$data);			
			}	
		$App->viewMethod = 'list';
	break;

	case 'setappData':
		if (isset(Core::$request->param)) {
			$data = (DateFormat::checkDateFormat(Core::$request->param,'Y-m-d') == true ? Core::$request->param : $App->nowDate);
			$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'data-timecard',$data);
			}	
		$App->viewMethod = 'list';
	break;

	case 'modappProj':
		if (isset($_POST['id_project'])) {
			$id_project = intval($_POST['id_project']);
			$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'id_project',$id_project);
			$App->currentProjectId = $id_project;
			if (ToolsStrings::findValueInArrayWithObject($App->progetti,'id',$id_project,array()) == true) $App->idProjectForSelect = $id_project;
			}	
		$App->viewMethod = 'list';
	break;

	case 'deleteTime':
		if ($App->id > 0) {
			Sql::initQuery($App->params->tables['item'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error == 0) {
				Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% cancellata'])).'!';
				}
				
			}		
		$App->viewMethod = 'list';
	break;
	
	case 'modifyTime':	
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();		
		if (Core::$resultOp->error == 1) Utilities::setItemDataObjWithPost($App->item,$App->params->fields['item']);	
		$App->defaultFormData = $App->item->datains;
		$App->timeIniTimecard = $App->item->starttime;
		$App->timeEndTimecard = $App->item->endtime;
		$App->idProjectForSelect =  $App->item->id_project;		
		$App->viewMethod = 'form';
	break;
	
	case 'insert1Time':
		if ($_POST) {	
			$id_progetto = (isset($_POST['project1']) ? intval($_POST['project1']) : 0);
			if ($id_progetto > 0) {
				$datarif = DateFormat::convertDatepickerToIso($_POST['data1'],$_lang['datepicker data format'],'Y-m-d',$_MY_SESSION_VARS[$App->sessionName]['data-timecard']);
				$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'data-timecard',$datarif);
				/* trova la timecard */
				if (isset($_POST['timecard']) && $_POST['timecard'] != '') {											
					$App->timecard = new stdClass;
					Sql::initQuery($App->params->tables['pite'],array('*'),array(intval($_POST['timecard'])),'id = ?');
					$App->timecard = Sql::getRecord();		
					if (Core::$resultOp->error == 0 && isset($App->timecard->id) && $App->timecard->id > 0) {						
						$starttimeiso = $App->timecard->starttime;
						$holdtime = 0;
						if (isset($_POST['usedata']) && $_POST['usedata'] == 1)	{
							$starttimeiso = DateFormat::convertDatepickerToIso($_POST['starttime1'],$_lang['datepicker time format'],'H:i:s','00:00:01');							
							$holdtime = 1;
						}										
						$endtimeiso = $App->timecard->endtime;
						if ($holdtime == 1) {
							/* scompone il worktime in minuti seconti */
							$timeVars =  explode(':',$App->timecard->worktime);		
							$hours = $timeVars[0];
							$minutes = $timeVars[1];										
							$t = $datarif.' '.$starttimeiso;
							$time = DateTime::createFromFormat('Y-m-d H:i:s',$t);  
							$errors = DateTime::getLastErrors();
							if ($errors['warning_count'] == 0 && $errors['error_count'] == 0) {
								/* aggiungi le ore e minuti */
								$st = "PT".intval($hours)."H".intval($minutes)."M";
								$time->add(new DateInterval($st));
								$endtimeiso = $time->format("H:i:s");		
							} else {
								Core::$resultOp->message = $_lang['La data inserita non è valida!'];
	      					Core::$resultOp->error = 1;
							}
						}																
						$Module->checkTimeInterval($App->userLoggedData->id,$id_progetto,$datarif,$starttimeiso,$endtimeiso,$opt=array());
						if (Core::$resultOp->error == 0) {															
							/* salva il tutto */
							$fields = array('users_id','id_project','datains','starttime','endtime','worktime','content');
		   	 			$fieldsValues = array($App->userLoggedData->id,$id_progetto,$datarif,$starttimeiso,$endtimeiso,$App->timecard->worktime,$App->timecard->content);
			  	  	 		Sql::initQuery($App->params->tables['item'],$fields,$fieldsValues,'');
	 						Sql::insertRecord();					
							if (Core::$resultOp->error == 0) {
	 								Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',$_lang['tempo'],$_lang['%ITEM% inserito']))."!";
	 						}

						} else {
		      			Core::$resultOp->message = $_lang['Intervallo di tempo si sovrappone ad un altro inserito nella stessa data!'];
		      			Core::$resultOp->error = 1;
						}									
					} else {
	     				Core::$resultOp->message = $_lang['Timecard non trovata!'];	
	      			Core::$resultOp->error = 1;
					}					
				} else {
		     		Core::$resultOp->message = $_lang['Devi selezionare una timecard!'];
		      	Core::$resultOp->error = 1;
				}
						
			} else {
				Core::$resultOp->message = $_lang['Devi selezionare un progetto!'];	 
				Core::$resultOp->error = 1;
			}
		
		} else {
			Core::$resultOp->error = 1;
		}
		$App->viewMethod = 'list';			
	break;

	case 'insertTime':
		if ($_POST) {
			$id_progetto = (isset($_POST['progetto']) ? intval($_POST['progetto']) : 0);
			if ($id_progetto > 0) {	
				$datarif = DateFormat::convertDatepickerToIso($_POST['data'],$_lang['datepicker data format'],'Y-m-d',$_MY_SESSION_VARS[$App->sessionName]['data-timecard']);					
				$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'Y-m-d',$datarif);
				/* controlla l'ora iniziale */
				$starttimeiso = DateFormat::convertDatepickerToIso($_POST['startTime'],$_lang['datepicker time format'],'H:i:s','00:00:01');
				$endtimeiso = DateFormat::convertDatepickerToIso($_POST['endTime'],$_lang['datepicker time format'],'H:i:s','00:00:01');							
				/* controlla l'intervallo */
				$datatimeisoini = $datarif .' '.$starttimeiso;
				$datatimeisoend = $datarif .' '.$endtimeiso;
				DateFormat::checkDateTimeIsoIniEndInterval($datatimeisoini,$datatimeisoend,'>');
				if (Core::$resultOp->error == 0) {								
					$Module->checkTimeInterval($App->userLoggedData->id,$id_progetto,$datarif,$starttimeiso,$endtimeiso,$opt=array());
					if (Core::$resultOp->error == 0) {		   			
		   			$dteStart = new DateTime($datatimeisoini);
						$dteEnd   = new DateTime($datatimeisoend); 
						$dteDiff  = $dteStart->diff($dteEnd);
						$workHour = $dteDiff->format("%H:%I");											
						$fields = array('users_id','id_project','datains','starttime','endtime','worktime','content');
	   	 			$fieldsValues = array($App->userLoggedData->id,$id_progetto,$datarif,$starttimeiso,$endtimeiso,$workHour,$_POST['content']);
		  	  	 		Sql::initQuery($App->params->tables['item'],$fields,$fieldsValues,'');
 						Sql::insertRecord();					
						if (Core::$resultOp->error == 0) {
 								Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',$_lang['tempo'],$_lang['%ITEM% inserito']))."!";
 						}
					} else {
	      			Core::$resultOp->message = $_lang['Intervallo di tempo si sovrappone ad un altro inserito nella stessa data!'];
	      			Core::$resultOp->error = 1;
					}							
 												
				} else {
      			Core::$resultOp->message = $_lang['La ora inizio deve essere prima della ora fine!'];	 
      			Core::$resultOp->error = 1;
				}			   		
							
			} else {
				Core::$resultOp->message = $_lang['Devi selezionare un progetto!'];	 
				Core::$resultOp->error = 1;
			}
		} else {
			Core::$resultOp->error = 1;
		}
		$App->viewMethod = 'list';
	break;
	
	case 'updateTime':
		if ($_POST) {
			$id_progetto = (isset($_POST['progetto']) ? intval($_POST['progetto']) : 0);
			$id = (isset($_POST['id']) ? intval($_POST['id']) : 0);
			if ($id > 0) {
				if ($id_progetto > 0) {
					$datarif = DateFormat::convertDatepickerToIso($_POST['data'],$_lang['datepicker data format'],'Y-m-d',$_MY_SESSION_VARS[$App->sessionName]['data-timecard']);					
					$_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'data-timecard',$datarif);
					/* controlla l'ora iniziale */
					$starttimeiso = DateFormat::convertDatepickerToIso($_POST['startTime'],$_lang['datepicker time format'],'H:i:s','00:00:01');
					$endtimeiso = DateFormat::convertDatepickerToIso($_POST['endTime'],$_lang['datepicker time format'],'H:i:s','00:00:01');							
					/* controlla l'intervallo */
					$datatimeisoini = $datarif .' '.$starttimeiso;
					$datatimeisoend = $datarif .' '.$endtimeiso;
					DateFormat::checkDateTimeIsoIniEndInterval($datatimeisoini,$datatimeisoend,'>');
					if (Core::$resultOp->error == 0) {
						$Module->checkTimeInterval($App->userLoggedData->id,$id_progetto,$datarif,$starttimeiso,$endtimeiso,$opt=array('id_timecard'=>$id));
						if (Core::$resultOp->error == 0) {
	
							$dteStart = new DateTime($datatimeisoini);
							$dteEnd   = new DateTime($datatimeisoend); 
							$dteDiff  = $dteStart->diff($dteEnd);
							$workHour = $dteDiff->format("%H:%I");
													
							$fields = array('id_project','datains','starttime','endtime','worktime','content');
		   	 			$fieldsValues = array($_POST['progetto'],$datarif,$starttimeiso,$endtimeiso,$workHour,$_POST['content'],$id);
			  	  	 		Sql::initQuery($App->params->tables['item'],$fields,$fieldsValues,'id = ?');
	 						Sql::updateRecord();					
							if (Core::$resultOp->error == 0) {
	 								Core::$resultOp->message = ucfirst(preg_replace('/%ITEM%/',$_lang['tempo'],$_lang['%ITEM% modificato']))."!";
	 							}
				 										 							
							} else {
		      				Core::$resultOp->message = $_lang['Intervallo di tempo si sovrappone ad un altro inserito nella stessa data!'];
		      				Core::$resultOp->error = 1;
								}			 							
			 											
						} else {
	      				Core::$resultOp->message = $_lang['La ora inizio deve essere prima della ora fine!'];	 
	      				Core::$resultOp->error = 1;
							}
							
					} else {
				     	Core::$resultOp->message = $_lang['Devi selezionare un progetto!'];	 
				      Core::$resultOp->error = 1;
						}
				} else {
  					Core::$resultOp->message = $_lang['Timecard non trovata!'];	 
   				Core::$resultOp->error = 1;
					}
			} else {
				Core::$resultOp->error = 1;
				}	
		$App->viewMethod = 'list';
	break;

	default;
		$App->viewMethod = 'list';
	break;	
	}

/*
echo 'idProjectSession: '.$_MY_SESSION_VARS[$App->sessionName]['id_project'];
echo ' - idProjectForSelect: '.$App->idProjectForSelect;
echo ' - currentProjectId: '.$App->currentProjectId;
*/

/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

	/* trova tutti i giorni del mese corrente */
	
	
	
	$data = DateTime::createFromFormat('Y-m-d',$_MY_SESSION_VARS[$App->sessionName]['data-timecard']);
	
	$month = $data->format('m');
	$year = $data->format('Y');	
	$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
 	$App->dates_month = array();
 	$tottimes = array();
	for ($i = 1; $i <= $num; $i++) {
		
		$data1 = DateTime::createFromFormat('Y-m-d',$year.'-'.$month.'-'.$i);
		
		//$mktime = mktime(0, 0, 0, $month, $i, $year);
		$dateL = $data1->format('d/m/Y');
		$dateV = $data1->format('Y-m-d');
		$numberday = $data1->format('w');
		$nameday = $_lang['days'][$numberday];
		$nameabbday = ucfirst((strlen($nameday) > 3 ? mb_strcut($nameday,0,3) : $nameday));
		
		$App->dates_month[$i] = array('label'=>$dateL,'value'=>$dateV,'numberday'=>$numberday,'nameabbday'=>$nameabbday,'nameday'=>$nameday);	
		
		/* memorizza le time card per ogni data /*/	
		$where = "datains = '".$dateV."'";
		if ($_MY_SESSION_VARS[$App->sessionName]['id_project'] > 0) $where .= " AND id_project = '".intval($_MY_SESSION_VARS[$App->sessionName]['id_project'])."'";
		Sql::initQuery($App->params->tables['item'].' AS t LEFT JOIN '.$App->params->tables['prog'].' AS p ON (t.id_project = p.id)',array('t.*,p.title AS project'),array(),$where);
		Sql::setOrder('starttime ASC');
 		$obj = Sql::getRecords();
 		$times = array();
 		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {	
				$tottimes[] = $value->worktime;
				$times[] = $value->worktime;							
				}
			}				
		$App->timecards[$dateV]['timecards'] = $obj;
		$App->timecards_total[$dateV] = DateFormat::sum_the_time($times);
 		}
 		
 		
//print_r($App->timecards);
//print_r($App->timecards_total);

	$App->timecards_total_time = DateFormat::sum_the_time($tottimes);
 			
		
	$App->methodForm1 = 'insert1Time';
	$App->defaultFormData1 = $_MY_SESSION_VARS[$App->sessionName]['data-timecard'];


switch((string)$App->viewMethod) {
	case 'form':
		$App->methodForm = 'updateTime';
		$App->templateApp = 'formItem.html';	
	break;
	
	case 'list':
		$App->item = new stdClass;
		$App->item->id_project = $App->currentProjectId;
		/* sistemo ora inizio e fine */
		$time = DateTime::createFromFormat('H:i:s',$App->nowTime);
		$App->timeIniTimecard =  $time->format($_lang['datepicker time format']);
		$time->add(new DateInterval('PT1H'));
		$App->timeEndTimecard = $time->format($_lang['datepicker time format']);	
		$App->defaultFormData = $_MY_SESSION_VARS[$App->sessionName]['data-timecard'];
		$App->pageSubTitle = $_lang['lista delle voci'];
		$App->methodForm = 'insertTime';
		$App->templateApp = 'formItem.html';
	break;
	
	default:
	break;
	}
?>