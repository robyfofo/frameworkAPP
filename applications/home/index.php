<?php
/**
* Framework App PHP-MySQL
* PHP Version 7
* @author Roberto Mantovani (<me@robertomantovani.vr.it>
* @copyright 2009 Roberto Mantovani
* @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
* app/home/index.php v.1.3.0. 16/09/2020
*/

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".Config::$localStrings['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");

//print_r($App->tablesOfDatabase);
//print_r($App->user_modules_active);

$App->params = new stdClass();
$App->params->label = 'Home';
/* prende i dati del modulo */
Sql::initQuery(DB_TABLE_PREFIX.'modules',array('label','help_small','help'),array('home'),'name = ?');
$obj = Sql::getRecord();
if (Core::$resultOp->error == 0 && isset($obj) && count((array)$obj) > 1) $App->params = $obj;
if (!isset($App->params->label) || (isset($App->params->label) && $App->params->label == '')) die('Error reading module settings!');

/* variabili ambiente */
$App->codeVersion = ' 1.3.0.';
$App->pageTitle = $App->params->label;
$App->pageSubTitle = Config::$localStrings['pagesubtitle'];
$Module = new Module('','home');
$App->Module = $Module;

$App->params->breadcrumb = '<li class="active"><i class="icon-user"></i> '.$App->params->label.'</li>';

$App->countPanel = array();
$App->lastLoginLang = DateFormat::getDateTimeIsoFormatString($App->lastLogin,Config::$localStrings['data time format string'],Config::$localStrings['months'],Config::$localStrings['months'],array());

$App->templateApp = 'list.html';
$numCountPanel = 0;
switch(Core::$request->method) {
	default;	
		$App->moduleHome = array();
		$App->homeBlocks = array();
		$App->homeTables = array();	
		/* 
		colori disponibili
		.panel-primary (blue)
		.panel-default (grigio)
		.panel-success (verde)
		.panel-info (turchese)
		.panel-warning
		.panel-danger
		.panel-green 
		.panel-red 
		.panel-yellow
		*/		 
		$App->panels = array(
			'info'=>array('primary','default','info'),
			'alert'=>array('warning'),
			'danger'=>array('danger'),
			'success'=>array('success')	
		);		
		$App->panelsInfo = count($App->panels['info']);
		$App->panelsAlert = count($App->panels['alert']);
		$App->panelsDanger = count($App->panels['danger']);
		$App->panelsSuccess = count($App->panels['success']);


		// prendo i dati per moduli custom
		if (file_exists(PATH.$App->pathApplications."home/custom.php")) include_once(PATH.$App->pathApplications."home/custom.php");
		
		// prendo i dati per moduli base
		if (file_exists(PATH.$App->pathApplications."home/base.php")) include_once(PATH.$App->pathApplications."home/base.php");
		
		// prendo i dati per moduli custom */
		//if (file_exists(PATH.$App->pathApplications."home/charts.php")) include_once(PATH.$App->pathApplications."home/charts.php");
			
	break;	
}

//Core::setDebugMode(1);
//print_r($App->homeBlocks);

// render i blocchi
$arr = array();
if (is_array($App->homeBlocks) && count($App->homeBlocks) > 0) {
	$panelsinfo = 0;
	$panelsalert = 0;
	$panelsdanger = 0;
	$panelssuccess = 0;
	
	foreach ($App->homeBlocks AS $key => $value) {
		$module = (isset($value['module']) && $value['module'] != '' ? $value['module'] : $key);
		$value['class'] = (isset($value['class']) ? $value['class'] :'');
		$value['type'] = (isset($value['type']) ? $value['type'] :'');	

		$clause = (isset($value['sqloption']['clause']) ? $value['sqloption']['clause'] : 'created > ?');
		$clausevals = (isset($value['sqloption']['clausevals']) ? $value['sqloption']['clausevals'] : array($App->lastLogin));	
		$iduser = (isset($value['sqloption']['iduser']) ? $value['sqloption']['iduser'] : 0);
		$usersid = (isset($value['sqloption']['usersid']) ? $value['sqloption']['usersid'] : 1);
		
		$where = '';
		$and = '';	
		$fieldsVals = array();
		
		/* add clause */
		if ($clause != '') {
			$where .= $and.$clause;
			if (is_array($clausevals)) $fieldsVals = $clausevals;
			$and = ' AND ';
		}
		
		/* add user */
		if ($iduser == 1) {
			$where .= $and."id_user = ?";
			$fieldsVals = array_merge($fieldsVals,array($App->userLoggedData->id));
		}
		/* add user */
		if ($usersid == 1) {
			$where .= $and."users_id = ?";
			$fieldsVals = array_merge($fieldsVals,array($App->userLoggedData->id));
		}
		
		Sql::initQuery($value['table'],array('id'),$fieldsVals,$where,'','',false);			
		$items = Sql::countRecord();
		$value['items'] =  $items;	
		if ($value['class'] == '') {
			switch($value['type']) {
				case 'alert':
					$value['class'] = $App->panels['alert'][$panelsalert];
					$panelsalert = $panelsalert + 1;
					if ($panelsalert > ($App->panelsAlert - 1)) $panelsalert = 0;
				break;					
				case 'danger':
					$value['class'] = $App->panels['danger'][$panelsdanger];
					$panelsalert = $panelsdanger + 1;
					if ($panelsdanger > ($App->panelsDanger - 1)) $panelsdanger = 0;
				break;
				case 'success':
					$value['class'] = $App->panels['success'][$panelssuccess];
					$panelssuccess = $panelssuccess + 1;
					if ($panelssuccess > ($App->panelsSuccess - 1)) $panelssuccess = 0;
				break;
										
				default:
				case 'info':
					$value['class'] = $App->panels['info'][$panelsinfo];
					$panelsinfo = $panelsinfo + 1;
					if ($panelsinfo > ($App->panelsInfo - 1)) $panelsinfo = 0;
				break;				
				}
			
			/* aggiungi url */
			if (isset($value['url']) && $value['url'] == true) {
				$value['url'] = $Module->getItemBlockUrl($value,$App->lastLogin,$value);
				} else {
					$value['url'] = URL_SITE.$module;
					}
			}
		$arr[] = $value;
		}
}
$App->homeBlocks = $arr;					

// renter le tabelle
	$arr = array();
if (is_array($App->homeTables) && count($App->homeTables) > 0) {
	foreach ($App->homeTables AS $key => $value) {	
		/* aggiunge i campi */
		$fieldsVals = array();
		$where = '';
		$and = '';	
		
		$table = $value['table'];
		$fields = (isset($value['sqloption']['fields']) ? $value['sqloption']['fields'] : '*');
		
		$clause = (isset($value['sqloption']['clause']) ? $value['sqloption']['clause'] : '');
		$clausevals = (isset($value['sqloption']['clausevals']) ? $value['sqloption']['clausevals'] : array($App->lastLogin));	
		
		$order = (isset($value['sqloption']['order']) ? $value['sqloption']['order'] : 'created DESC');
		$fieldcreated = (isset($value['sqloption']['fieldcreated']) ? $value['sqloption']['fieldcreated'] : 'created');
		
		$iduser = (isset($value['sqloption']['iduser']) ? $value['sqloption']['iduser'] : 0);
		$usersid = (isset($value['sqloption']['usersid']) ? $value['sqloption']['usersid'] : 1);

		// add clause
		if ($clause != '') {
			$where .= $and.$clause;
			if (is_array($clausevals)) $fieldsVals = $clausevals;
			$and = ' AND ';
		}
		
		/* add user */
		if ($iduser == 1) {
			$where .= $and."id_user = ?";
			$fieldsVals = array_merge($fieldsVals,array($App->userLoggedData->id));
		}
		/* add user */
		if ($usersid == 1) {
			$where .= $and."users_id = ?";
			$fieldsVals = array_merge($fieldsVals,array($App->userLoggedData->id));
		}

		//echo $where;
		//print_r($fieldsVals);

		Sql::initQuery($table,array($fields),$fieldsVals,$where,$order,' LIMIT 5 OFFSET 0','',false);
		$value['itemdata'] = Sql::getRecords();

		/* sistemo i dati */
		$arr1 = array();
		if (is_array($value['itemdata']) && count($value['itemdata']) > 0) {
			foreach ($value['itemdata'] AS $key1 => $value1) {
				/* data */
				$datecreateformat = (isset($value['sqloption']['datecreateformat']) ? $value['sqloption']['datecreateformat'] : 'datetime');
				if ($datecreateformat == 'date') {
					$data = DateTime::createFromFormat('Y-m-d',$value1->$fieldcreated);				
					$value1->datacreated = '<a href="'.URL_SITE.$key.'" title="'.ucfirst(Config::$localStrings['creata il']).' '.$data->format('d/m/Y').'"><i class="fas fa-clock"></i></a>';
				} else {
					$data = DateTime::createFromFormat('Y-m-d H:i:s',$value1->$fieldcreated);				
					$value1->datacreated = '<a href="'.URL_SITE.$key.'" title="'.ucfirst(Config::$localStrings['creata il']).' '.$data->format('d/m/Y').' '.$data->format('H:i:s').'"><i class="fas fa-clock"></i></a>';
				}
				/* genera url */
				$value1->url = URL_SITE.$key;				
				if (is_array($value['fields']) && count($value['fields']) > 0) {
					foreach ($value['fields'] AS $keyF => $valueF) {
						/* creo output del del campo */	
						$str = '';						
						if ($keyF != '') {
							//echo $keyF;
							$type = (isset($value['fields'][$keyF]['type']) && $value['fields'][$keyF]['type'] != '' ? $value['fields'][$keyF]['type'] : '');
							switch($type){									
								case 'text':
									$f = $keyF;
									if (isset($value['fields'][$keyF]['multilanguage']) && $value['fields'][$keyF]['multilanguage'] == 1) {
										$f = $keyF.Config::$localStrings['field_suffix'];
										}
									$output = ToolsStrings::getStringFromTotNumberChar($value1->$f,array('numchars'=>200,'suffix'=>'...'));
								break;
								case 'image':																
									$path = (isset($value['fields'][$keyF]['path']) ? $value['fields'][$keyF]['path'] :  UPLOAD_DIR.'/');	
									$pathdef = (isset($value['fields'][$keyF]['path def']) ? $value['fields'][$keyF]['path def'] :  '');	
									if ($pathdef == '')	$pathdef = $path;																																
									if ($value1->$keyF != ''){
										$output = '<a class="" href="'.$path.$value1->$keyF.'" data-lightbox="image-1" data-title="'.$value1->$keyF.'" title="'.ucfirst(Config::$localStrings['immagine zoom']).'"><img class="img-thumbnail img-miniature"  src="'.$path.$value1->$keyF.'" alt="'.$path.$value1->$keyF.'"></a>';
									} else {
										$output = '<img class="img-thumbnail img-miniature"  src="'.$pathdef.$value1->$keyF.'default/image.png" alt="'.ucfirst(Config::$localStrings['immagine di default']).'">';
									}
								break;							
								case 'imagefolder':		
									$folderField = (isset($value['fields'][$keyF]['folderField']) ? $value['fields'][$keyF]['folderField'] : 'folder_name');														
									$path = (isset($value['fields'][$keyF]['path']) ? $value['fields'][$keyF]['path'] :  UPLOAD_DIR.'/');
									$path =	$path.$value1->$folderField;																		
									if ($value1->$keyF != ''){
										$output = '<a class="" href="'.$path.$value1->$keyF.'" title="'.ucfirst(Config::$localStrings['immagine zoom']).'"><img class="img-thumbnail"  src="'.$path.$value1->$keyF.'" alt=""></a>';
									}
								break;																
								case 'file':															
									if ($value1->$keyF != ''){
										$u = $Module->getItemUrl($value1,$value['fields'][$keyF]['url item']);
										$output = '<a class="" href="'.$u.'" title="'.ucfirst(Config::$localStrings['scarica il file']).'">'.$value1->$keyF.'</a>';
									}
								break;
								
								case 'avatar':															
									if ($value1->$keyF != ''){
										$output = '<a class="" href="'.URL_SITE.'ajax/renderuseravatarfromdb.php?id='.$value1->id.'" data-lightbox="image-1" data-title="Avatar" title="'.ucfirst(Config::$localStrings['immagine zoom']).'"><img class="img-thumbnail img-miniature" src="'.URL_SITE.'ajax/renderuseravatarfromdb.php?id='.$value1->id.'" alt="Avatar"></a>';
									}
								break;
								
								case 'amount':															
									if ($value1->$keyF != '') {
										$output = number_format($value1->$keyF, 2,',','.');
									} else {
										$output = '0,00';
									}
								break;
								
								default:
									$f = $keyF;
									if (isset($value['fields'][$keyF]['multilanguage']) && $value['fields'][$keyF]['multilanguage'] == 1) {
										$f = $keyF.Config::$localStrings['field_suffix'];
										}
									$output = $value1->$f;
								break;
								
							}
								
								/* aggiungi url */
							if (isset($value['fields'][$keyF]['url']) && $value['fields'][$keyF]['url'] == true) {
								if (isset($value['fields'][$keyF]['url item']) && is_array($value['fields'][$keyF]['url item']) && count($value['fields'][$keyF]['url item']) > 0) {
									$u = $Module->getItemUrl($value1,$value['fields'][$keyF]['url item']);
									$output = '<a href="'.$u.'" title="'.ucfirst(Config::$localStrings['vai alla lista']).'">'.$output.'</a>';								
								} else {
									$output = '<a href="'.URL_SITE.$key.'" title="'.ucfirst(Config::$localStrings['vai alla lista']).'">'.$output.'</a>';										
								}							
							}								
								$value1->$keyF = $output;							
						}							
					}
				}				
				$arr1[] = $value1;
			}
		}		
		$value['itemdata'] =  $arr1;		
		$value['icon panel'] = (isset($value['icon panel']) ? $value['icon panel'] : 'fa-newspaper-o');
		$arr[] = $value;
	}
}
$App->homeTables = $arr;	

$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/module.js"></script>';
?>
