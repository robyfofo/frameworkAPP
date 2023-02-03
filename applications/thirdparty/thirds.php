<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * app/thirdparty/items.php v.1.3.0. 25/09/2020
*/

if (isset($_POST['itemsforpage']) && isset($_MY_SESSION_VARS[$App->sessionName]['ifp']) && $_MY_SESSION_VARS[$App->sessionName]['ifp'] != $_POST['itemsforpage']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'ifp',$_POST['itemsforpage']);
if (isset($_POST['searchFromTable']) && isset($_MY_SESSION_VARS[$App->sessionName]['srcTab']) && $_MY_SESSION_VARS[$App->sessionName]['srcTab'] != $_POST['searchFromTable']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'srcTab',$_POST['searchFromTable']);
if (isset($_POST['categories_id']) && isset($_MY_SESSION_VARS[$App->sessionName]['categories_id']) && $_MY_SESSION_VARS[$App->sessionName]['categories_id'] != $_POST['categories_id']) $_MY_SESSION_VARS = $my_session->addSessionsModuleSingleVar($_MY_SESSION_VARS,$App->sessionName,'categories_id',$_POST['categories_id']);

/* preleva i tipi */
Sql::initQuery($App->params->tables['type'],array('*'),array(),'active = 1','');
Sql::setOptions(array('fieldTokeyObj'=>'id'));
$App->types = Sql::getRecords();

$App->categories = new stdClass();
Subcategories::setDbTable($App->params->tables['cate']);
Subcategories::setDbTablItem($App->params->tables['item']);
Subcategories::$fieldKey =	'id';
$App->categories = Subcategories::getObjFromSubCategories();

if (!is_array($App->categories) || (is_array($App->categories) && count($App->categories) == 0)) {
	$_SESSION['message'] = '2|'.ucfirst(preg_replace('/%ITEM%/',$_lang['categoria'],$_lang['Devi creare o attivare almeno una %ITEM%'].'!'));
	ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listCate');
}

switch(Core::$request->method) {

	case 'activeItem':
	case 'disactiveItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {
			Sql::manageFieldActive(substr(Core::$request->method,0,-4),$App->params->tables['item'],$App->id,array('label'=>$_lang['voce'],'attivata'=>$_lang['attivato'],'disattivata'=>$_lang['disattivato']));
			$_SESSION['message'] = '0|'.Core::$resultOp->message;
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'deleteItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($App->id > 0) {
			Sql::initQuery($App->params->tables['item'],array('id'),array($App->id),'id = ?');
			Sql::deleteRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% cancellato'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}
	break;

	case 'newItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }

		$App->province = new stdClass;
		Sql::initQuery($App->params->tables['province'],array('*'),array(),'active = 1','nome ASC');
		$App->province = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$App->nations = new stdClass;
		Sql::initQuery($App->params->tables['nations'],array('*'),array(),'active = 1','title_'.$_lang['user'].' ASC');
		$App->nations = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		
		$App->item = new stdClass;
		$App->item->active = 1;
		$App->item->stampa_quantita = 1;
		$App->item->stampa_unita = 1;

		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['voce'],$_lang['inserisci %ITEM%']);
		$App->methodForm = 'insertItem';
		$App->viewMethod = 'form';
	break;

	case 'insertItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); }
		if ($_POST) { 

			if (isset($_POST['location_comuni_id']) && intval($_POST['location_comuni_id']) > 0) {
				$App->comuni = new stdClass;
				Sql::initQuery($App->params->tables['comuni'],array('nome'),array(intval($_POST['location_comuni_id'])),'id = ? AND active = 1');
				$App->comune = Sql::getRecord();
				if (isset($App->comune->nome)) {
					$_POST['city'] = $App->comune->nome;
				}
			} else {
				$_POST['location_comuni_id'] = 0;
			}
			
			if (isset($_POST['location_province_id']) && intval($_POST['location_province_id']) > 0) {
				$App->provincia = new stdClass;
				Sql::initQuery($App->params->tables['province'],array('nome'),array(intval($_POST['location_province_id'])),'id = ? AND active = 1');
				$App->provincia = Sql::getRecord();
				if (isset($App->provincia->nome)) {
					$_POST['provincia'] = $App->provincia->nome;
				}
			} else {
				$_POST['location_province_id'] = 0;
			}

			$_POST['nation'] = '';
			if (isset($_POST['location_nations_id']) && intval($_POST['location_nations_id']) > 0) {
				$App->nation = new stdClass;
				Sql::initQuery($App->params->tables['nations'],array('title_'.$_lang['user']),array(intval($_POST['location_nations_id'])),'id = ? AND active = 1');
				$App->nation = Sql::getRecord();
				$field = 'title_'.$_lang['user'];
				if (isset($App->nation->$field)) {
					$_POST['nation'] =$App->nation->$field;
				}
			} else {
				$_POST['location_nations_id'] = 0;
			}

			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['item'],$_lang,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/newItem');
			}

			Sql::insertRawlyPost($App->params->fields['item'],$App->params->tables['item']);
			if (Core::$resultOp->error > 0) {ToolsStrings::redirect(URL_SITE.'error/db');}

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% inserito'])).'!';
			ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');

		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}	
	break;

	case 'modifyItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }

		$App->province = new stdClass;
		Sql::initQuery($App->params->tables['province'],array('*'),array(),'active = 1','nome ASC');
		$App->province = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$App->nations = new stdClass;
		Sql::initQuery($App->params->tables['nations'],array('*'),array(),'active = 1','title_'.$_lang['user'].' ASC');
		$App->nations = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }	

		
		$App->item = new stdClass;
		Sql::initQuery($App->params->tables['item'],array('*'),array($App->id),'id = ?');
		$App->item = Sql::getRecord();
		if (!isset($App->item->id) || (isset($App->item->id) && $App->item->id < 1)) { ToolsStrings::redirect(URL_SITE.'error/404'); }
		
		$App->comune = new stdClass;
		$App->comune->selected = new stdClass;	
		if (isset($App->item->location_comuni_id) && $App->item->location_comuni_id > 0) {
			$App->comune->selected->id = $App->item->location_comuni_id;
			$App->comune->selected->nome = $App->item->city;
		}		
				
		$App->pageSubTitle = preg_replace('/%ITEM%/',$_lang['voce'],$_lang['modifica %ITEM%']);
		$App->methodForm = 'updateItem';
		$App->viewMethod = 'form';

	break;

	case 'updateItem':
		if ($App->params->moduleAccessWrite == 0) { ToolsStrings::redirect(URL_SITE.'error/nopm'); }
		if ($_POST) {

			if (isset($_POST['location_comuni_id']) && intval($_POST['location_comuni_id']) > 0) {
				$App->comuni = new stdClass;
				Sql::initQuery($App->params->tables['comuni'],array('nome'),array(intval($_POST['location_comuni_id'])),'id = ? AND active = 1');
				$App->comune = Sql::getRecord();
				if (isset($App->comune->nome)) {
					$_POST['city'] = $App->comune->nome;
				}
			} else {
				$_POST['location_comuni_id'] = 0;
			}
			
			if (isset($_POST['location_province_id']) && intval($_POST['location_province_id']) > 0) {
				$App->provincia = new stdClass;
				Sql::initQuery($App->params->tables['province'],array('nome'),array(intval($_POST['location_province_id'])),'id = ? AND active = 1');
				$App->provincia = Sql::getRecord();
				if (isset($App->provincia->nome)) {
					$_POST['provincia'] = $App->provincia->nome;
				}
			} else {
				$_POST['location_province_id'] = 0;
			}

			$_POST['nation'] = '';
			if (isset($_POST['location_nations_id']) && intval($_POST['location_nations_id']) > 0) {
				$App->nation = new stdClass;
				Sql::initQuery($App->params->tables['nations'],array('title_'.$_lang['user']),array(intval($_POST['location_nations_id'])),'id = ? AND active = 1');
				$App->nation = Sql::getRecord();
				$field = 'title_'.$_lang['user'];
				if (isset($App->nation->$field)) {
					$_POST['nation'] =$App->nation->$field;
				}
			} else {
				$_POST['location_nations_id'] = 0;
			}

			// parsa i post in base ai campi
			Form::parsePostByFields($App->params->fields['item'],$_lang,array());
			if (Core::$resultOp->error > 0) {
				$_SESSION['message'] = '1|'.implode('<br>', Core::$resultOp->messages);
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
			}

			Sql::updateRawlyPost($App->params->fields['item'],$App->params->tables['item'],'id',$App->id);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

			$_SESSION['message'] = '0|'.ucfirst(preg_replace('/%ITEM%/',$_lang['voce'],$_lang['%ITEM% modificato'])).'!';
			if (isset($_POST['applyForm']) && $_POST['applyForm'] == 'apply') {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/modifyItem/'.$App->id);
			} else {
				ToolsStrings::redirect(URL_SITE.Core::$request->action.'/listItem');
			}


		} else {
			ToolsStrings::redirect(URL_SITE.'error/404');
		}

	break;

	case 'listAjaxItem':
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
		$table = $App->params->tables['item']." AS ite";
		$table .= " LEFT JOIN ".$App->params->tables['type']." AS t ON (ite.id_type = t.id)";
		$table .= " LEFT JOIN ".$App->params->tables['cate']." AS cate ON (ite.categories_id = cate.id)";
		$fields[] = "ite.*";
		$fields[] = "t.title AS type";
		$fields[] = "cate.title AS category";
		$whereFields = array(
			'category'=>array('field'=>'cate.title','type'=>'datafield'),
		);

		// limit	
		if (isset($_REQUEST['start']) && $_REQUEST['length'] != '-1') {
			$limit = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start'];
		}				
		//end limit

		// orders
		$orderFields = array('id','categories_id','type','ragione_sociale','email');
		$orderTable = array();
		$order = '';	
		if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {		
			foreach ($_REQUEST['order'] AS $key=>$value)	{				
				$orderTable[] = $orderFields[$value['column']].' '.$value['dir'];
			}
		}
		if (is_array($orderTable) && count($orderTable) > 0) $order = implode(', ', $orderTable);
		// end orders

		// filters
		$wf = array();
		$wfv = array();
		if (isset($_REQUEST['search']) && is_array($_REQUEST['search']) && count($_REQUEST['search']) > 0) {		
			if (isset($_REQUEST['search']['value']) && $_REQUEST['search']['value'] != '') {
				list($w,$fv) = Sql::getClauseVarsFromAppSession($_REQUEST['search']['value'],$App->params->fields['item'],'',array('tableAlias'=>'ite'));
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

		//echo '<br> - whereall: '.$whereAll.'<br> - andall: '.$andAll.'<br> - fieldsValueAll: ';
		//print_r($fieldsValueAll);
		//echo '<br> - where: '.$where.'<br> - and: '.$and.'<br> - fieldsValue: ';
		//print_r($fieldsValue);
		//echo '<br> - order: '.$order;
		//echo '<br> - limit: '.$limit;
		

		// conta tutti i records
		$recordsTotal = Sql::countRecordQry($App->params->tables['item'],'id',$whereAll,$fieldsValueAll);
		$recordsFiltered = $recordsTotal;

		// se ci sono i filtri
		if ($filtering == true) {
			Sql::initQuery($table,$fields,$fieldsValue,$where,$order);
			$obj = Sql::getRecords();
			$recordsFiltered = count($obj);
		}

		Sql::initQuery($table,$fields,$fieldsValue,$where,$order,$limit);
		$obj = Sql::getRecords();
		//echo Sql::getQuery();

		// sistemo dati
		$arr = array();
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key=>$value) {
				$actions = '<a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/'.($value->active == 0 ? 'active' : 'disactive').'Item/'.$value->id.'" title="'.($value->active == 0 ? ucfirst($_lang['attiva']).' '.$_lang['la voce'] : ucfirst($_lang['disattiva']).' '.$_lang['la voce']).'"><i class="fas fa-'.($value->active == 1 ? 'unlock' : 'lock').'"></i></a><a class="btn btn-default btn-sm" href="'.URL_SITE.Core::$request->action.'/modifyItem/'.$value->id.'" title="'.ucfirst($_lang['modifica']).' '.$_lang['la voce'].'"><i class="far fa-edit"></i></a><a class="btn btn-default btn-sm confirmdelete" href="'.URL_SITE.Core::$request->action.'/deleteItem/'.$value->id.'" title="'.ucfirst($_lang['cancella']).' '.$_lang['la voce'].'"><i class="fa fa-trash-alt"></i></a>';
				$tablefields = array(
					'id'					=>	$value->id,
					'category'				=>	$value->category,
					'type'					=>	$value->type,
					'ragione_sociale'		=>	$value->ragione_sociale,
					'email'					=>	$value->email,
					'actions'				=>	$actions
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

	case 'listItem':
	default;	
		$App->pageSubTitle = preg_replace('/%ITEMS%/',$_lang['voci'],$_lang['lista dei %ITEMS%']);
		$App->viewMethod = 'list';	
	break;	

}


/* SEZIONE SWITCH VISUALIZZAZIONE TEMPLATE (LIST, FORM, ECC) */

switch((string)$App->viewMethod) {

	case 'form':		
		$App->templateApp = 'formThird.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/formThird.js"></script>';
	break;

	case 'list':
	default:
		$App->templateApp = 'listThirds.html';
		$App->jscript[] = '<script src="'.URL_SITE.$App->pathApplications.Core::$request->action.'/templates/'.$App->templateUser.'/js/listThirds.js"></script>';	
	break;

}	
?>