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

	case 'checkNumberInvoiceAjaxSr':
		$result = 1;
		if ( isset($_POST['number_customers_id']) && isset($_POST['number_year']) && isset($_POST['number']) ) {	
			$qryFieldsValues = array(intval($_POST['number_customers_id']),intval($_POST['number_year']),intval($_POST['number']));
			$clause = 'id_customer = ? AND number_year = ? AND number = ?';
			
			if (isset($_POST['id']) && $_POST['id'] != '') {
				$qryFieldsValues[] = intval($_POST['id']);
				$clause .= ' AND id  <> ?';
			}
 			
			$count = Sql::countRecordQry($App->params->tables['InvSal'],'id',$clause,$qryFieldsValues);
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			if ($count == 0) {
				$result = 0;						
			}		
		}
		echo $result;
		die();
	break;
	
	case 'getNumberInvoiceAjaxSr':
		$result = 1;
		if ( isset($_POST['number_customers_id']) && isset($_POST['number_year']) ) {	
			$qryFields = array("MAX(number) + 1 AS newnumber");
			$qryFieldsValues = array(intval($_POST['number_customers_id']),intval($_POST['number_year']));
			$clause = 'id_customer = ? AND number_year = ?';
			Sql::initQuery($App->params->tables['InvSal'],$qryFields,$qryFieldsValues,$clause);	
			$obj = Sql::getRecord();
			if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
			$result = (isset($obj->newnumber) ? $obj->newnumber : 1);
		}
		echo $result;
		die();
	break;

}
?>