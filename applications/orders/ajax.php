<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * invoices/ajax.php v.1.2.0. 20/01/2020
*/


switch(Core::$request->method) {

	case 'getArticleOrderAjaxSr':
		$App->item = new stdClass;
		$id = (isset($_POST['id']) ? intval($_POST['id']) : 0);
		if ($id > 0) {
			Sql::initQuery($App->params->tables['articles'],array('*'),array($id),'id = ?');
			$App->item = Sql::getRecord();
			$json = json_encode($App->item);
			echo $json;
		}
		die();
	break;

	case 'checkNumberOrderAjaxSr':
		$result = 1;
		if ( isset($_POST['number_year']) && isset($_POST['number']) ) {	
			$qryFieldsValues = array(intval($_POST['number_year']),intval($_POST['number']));
			$clause = 'number_year = ? AND number = ?';
			if (isset($_POST['id']) && $_POST['id'] != '') {
				$qryFieldsValues[] = intval($_POST['id']);
				$clause .= ' AND id  <> ?';
			}	
			$count = Sql::countRecordQry($App->params->tables['orders'],'id',$clause,$qryFieldsValues);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			if ($count == 0) {
				$result = 0;						
			}		
		}
		echo $result;
		die();
	break;
	
	case 'getNumberOrderAjaxSr':
		$result = 1;
		if ( isset($_POST['number_year']) ) {	
			$qryFields = array("MAX(number) + 1 AS newnumber");
			$qryFieldsValues = array(intval($_POST['number_year']));
			$clause = 'number_year = ?';
			Sql::initQuery($App->params->tables['orders'],$qryFields,$qryFieldsValues,$clause);	
			$obj = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			$result = (isset($obj->newnumber) ? $obj->newnumber : 1);
		}
		echo $result;
		die();
	break;

	case 'getCompanyDetailAjaxSr':

		$id = intval(Core::$request->param);
		// leggi i dati 
		Sql::initQuery($App->params->tables['orders_company'],array('*'),array($id),'orders_id = ?');
		$obj = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
		//print_r($obj);

		$output = '
		<i class="fas fa-industry"></i>
		<span><span>'.Config::$localStrings['ragione sociale'].'</span>'.$obj->company_ragione_sociale.'</span>
		<span><span>'.Config::$localStrings['nome'].'</span>'.$obj->company_name.'</span>
		<span><span>'.Config::$localStrings['cognome'].'</span>'.$obj->company_surname.'</span>
		<span><span>'.Config::$localStrings['via'].'</span>'.$obj->company_street.'</span>
		<span><span>'.Config::$localStrings['comune'].'</span>'.$obj->company_comune.'</span>
		<span><span>'.Config::$localStrings['cap'].'</span>'.$obj->company_zip_code.'</span>
  		<span><span>'.Config::$localStrings['provincia'].'</span>'.$obj->company_provincia.'</span>
		<span><span>'.Config::$localStrings['nazione'].'</span>'.$obj->company_nation.'</span>
   		<span><span>'.Config::$localStrings['telefono'].'</span>'.$obj->company_telephone.'</span>
   		<span><span>'.Config::$localStrings['email'].'</span>'.$obj->company_email.'</span>
   		<span><span>'.Config::$localStrings['fax'].'</span>'.$obj->company_fax.'</span>
   		<span><span>'.Config::$localStrings['partita IVA'].'</span>'.$obj->company_partita_iva.'</span>
   		<span><span>'.Config::$localStrings['codice fiscale'].'</span>'.$obj->company_codice_fiscale.'</span>
   		<span><span>PEC</span>'.$obj->company_pec.'&nbsp;</span>
  		<span><span>SID</span>'.$obj->company_sid.'&nbsp;</span>';

  		 echo $output;
		
		
		die();
	break;

	case 'getThirdpartyDetailAjaxSr':
		$id = intval(Core::$request->param);
		// leggi i dati 
		Sql::initQuery($App->params->tables['orders_thirdparty'],array('*'),array($id),'orders_id = ?');
		$obj = Sql::getRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
		//print_r($obj);

		$output = '
		<i class="fas fa-iuser"></i>
		<span><span>'.Config::$localStrings['ragione sociale'].'</span>'.$obj->thirdparty_ragione_sociale.'</span>
		<span><span>'.Config::$localStrings['nome'].'</span>'.$obj->thirdparty_name.'</span>
		<span><span>'.Config::$localStrings['cognome'].'</span>'.$obj->thirdparty_surname.'</span>
		<span><span>'.Config::$localStrings['via'].'</span>'.$obj->thirdparty_street.'</span>
		<span><span>'.Config::$localStrings['comune'].'</span>'.$obj->thirdparty_comune.'</span>
		<span><span>'.Config::$localStrings['cap'].'</span>'.$obj->thirdparty_zip_code.'</span>
  		<span><span>'.Config::$localStrings['provincia'].'</span>'.$obj->thirdparty_provincia.'</span>
		<span><span>'.Config::$localStrings['nazione'].'</span>'.$obj->thirdparty_nation.'</span>
   		<span><span>'.Config::$localStrings['telefono'].'</span>'.$obj->thirdparty_telephone.'</span>
   		<span><span>'.Config::$localStrings['email'].'</span>'.$obj->thirdparty_email.'</span>
   		<span><span>'.Config::$localStrings['fax'].'</span>'.$obj->thirdparty_fax.'</span>
   		<span><span>'.Config::$localStrings['partita IVA'].'</span>'.$obj->thirdparty_partita_iva.'</span>
   		<span><span>'.Config::$localStrings['codice fiscale'].'</span>'.$obj->thirdparty_codice_fiscale.'</span>
   		<span><span>PEC</span>'.$obj->thirdparty_pec.'&nbsp;</span>
  		<span><span>SID</span>'.$obj->thirdparty_sid.'&nbsp;</span>';

  		 echo $output;
		die();
	break;

}
?>