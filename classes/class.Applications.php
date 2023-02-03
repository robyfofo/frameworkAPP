<?php 
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * classes/class.Subcategories.php v.1.4.0. 09/02/2021
*/

class Applications extends Core {
	public function __construct() 	{
		parent::__construct();
	}

	public static function setFiltersSessions($post,$session)
	{
		//ToolsStrings::dumpArray($post);
		unset($session['filters']);
		if (is_array($post) && count($post) > 0) {
			foreach ($post AS $key=>$value) {
				if ($value != '') {
					$session['filters'][$key] = $value;
				}
			}
		}
		return $session;
	}

	public static function getWhereClauseDBFromFiltersPost($session,$whereClause,$whereAndClause,$fields,$fieldsValues,$exclude=array()) 
	{
			//print_r($session);
		$and = '';
		if (isset($session['filters']) && is_array($session['filters']) && count($session['filters']) > 0) {
			$whereClause .= $whereAndClause.'(';

			// per date
			if ( isset($session['filters']['datadal']) || isset($session['filters']['dataal']) ) {
				$datadal = (isset($session['filters']['datadal']) ? $session['filters']['datadal'] : '');
				$dataal = (isset($session['filters']['dataal']) ? $session['filters']['dataal'] : '');

				if ($datadal != '' && $dataal == '') {
					$fieldsValues[] = DateFormat::dateFormating($datadal,'Y-m-d');
					$whereClause .= $and." DATE_FORMAT(data,'%Y-%m-%d') = ?";
					$and = ' AND ';
				} else if ( $datadal != '' && $dataal != '' ) {
					$fieldsValues[] = DateFormat::dateFormating($datadal,'Y-m-d');
					$fieldsValues[] = DateFormat::dateFormating($dataal,'Y-m-d');
					$whereClause .= $and." DATE_FORMAT(data,'%Y-%m-%d') BETWEEN ? AND ?";
					$and = ' AND ';
				}
			}
			$whereAndClause = $and;
			// fine date

			foreach ($session['filters'] AS $key=>$value) {
				if ( $value != '' && !in_array($key,$exclude) ) {
					$whereClause .= $and.$key.' = ?';
					$and = ' AND ';
					$fieldsValues[] = $value;
					$whereAndClause = ' AND ';

				}
			}
			$whereClause .= ')';
		}
		return array($whereClause,$whereAndClause,$fields,$fieldsValues);
	}
	
	// for table
	public static function setTableFieldOrderSession($field='',$session,$opt=array()) 
	{
		$optDef = array(	
		);	
		$opt = array_merge($optDef,$opt);
		
		if (!isset($session[$field])) {
			$session[$field] = '';
		}

	}

	// dataTables

	public static function resetDataTableArrayVars() {
		$array['whereAll'] = '';
		$array['andAll'] = '';
		$array['fieldsValueAll'] = array();
		$array['where'] = '';
		$array['and'] = '';
		$array['fieldsValue'] = array();
		$array['limit'] = '';
		$array['order'] = '';
		$array['filtering'] = false;
		$array['orderFields'] = '';
		return $array;
	}

	public static function setDataTableArrayVars($array,$request,$userLoggedData,$fieldsItemsBase,$params=array()) {
	$params = array_merge(
		array(
			'permissiminoOnlyUser' => true
		),$params
		);

		// limit	
		if (isset($request['start']) && $request['length'] != '-1') {
			$array['limit'] = " LIMIT ".$request['length']." OFFSET ".$request['start'];
		}	
		//end limit	

		// orders
		$array['orderTable'] = array();
		$array['order'] = '';	
		if (isset($request['order']) && is_array($request['order']) && count($request['order']) > 0) {		
			foreach ($request['order'] AS $key=>$value)	{				
				$array['orderTable'][] = $array['orderFields'][$value['column']].' '.$value['dir'];
			}
		}
		if (is_array($array['orderTable']) && count($array['orderTable']) > 0) {
			$array['order'] = implode(', ', $array['orderTable']);
		}
		// end orders

		// permissions query
		list($permClause,$fieldsValuesPermClause) = Permissions::getSqlQueryItemPermissionForUser($userLoggedData,array('fieldprefix'=>'ite.','onlyuser'=>$params['permissiminoOnlyUser']));
		if (isset($permClause) && $permClause != '') {
			$array['whereAll'] .= $array['andAll'].'('.$permClause.')';
			$array['andAll'] = ' AND ';
			$array['where'] .= $array['and'].'('.$permClause.')';
			$array['andAll'] = ' AND ';
		}
		if (is_array($fieldsValuesPermClause) && count($fieldsValuesPermClause) > 0) {
			$array['fieldsValueAll'] = array_merge($array['fieldsValueAll'],$fieldsValuesPermClause);
			$array['fieldsValue'] = array_merge($array['fieldsValue'],$fieldsValuesPermClause);	
		}
		// end permissions items

		// filters
		$wf = array();
		$wfv = array();
		if (isset($request['search']) && is_array($request['search']) && count($request['search']) > 0) {		
			if (isset($request['search']['value']) && $request['search']['value'] != '') {

				list($w,$fv) = Sql::getClauseVarsFromAppSession($request['search']['value'],$fieldsItemsBase,'',array('tableAlias'=>'ite'));
				if ($w != '') {
					$wf[] = $w;
					$wfv = $fv;
				}		
				// aggiungi query join		
				if (is_array($array['whereFields']) && count($array['whereFields']) > 0) {	
					$wf1 = array();
					$wfv1 = array();
					$valueFV = array();
					$valueF = '';
					foreach ($array['whereFields'] AS $keyqw=>$valueqw) {						
						
						
						switch($valueqw['type']) {	
													
							case 'datalabelint':
								$keys = preg_grep( '/'.$request['search']['value'].'/', $valueqw['arrayRif'] );
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
								$fv = array('%'.$request['search']['value'].'%');
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
					$array['where'] .= $array['and']."(".implode('',$wf).")";
					$array['and'] = ' AND ';
				}
				if (is_array($wfv) && count($wfv) > 0) {
					$array['fieldsValue'] = array_merge($array['fieldsValue'],$wfv);
					$array['filtering'] = true;
				} 				
			}				
		}

		return $array;
	}
	
}
?>