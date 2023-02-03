<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * classes/class.Sql.php v.1.4.0. 09/02/2021
*/

class Sql extends Core {
	static $level = 0;	
	static $itemsForPage = 2;
	static $page = 1;
	static $totalItems = 0;
	static $firstId = 1;
	static $lastInsertedId = 0;
	static $qry = '';
	static $customQry = '';
	static $table = '';
	static $fields = array();
	static $fieldsValue = array();
	static $clause = '';
	static $wherePrefix = '';
	static $order = '';
	static $limit = '';
	static $opts = '';
	static $resultPaged = false;
	static $resultRecords = 0;	
	static $addslashes = true;	
	static $foundRows = 0;
		
	static $breadcrumbs = array();
	static $countA = 0;	
	static $parentstring = '';
	static $pretitleparent = '';	
	static $languages = array('it');
	
	static $listTreeData = '';
	
	public static $lang;
	public static $optAddRowFields = 0;
	public static $optImageFolder = '';
	public static $optDetailAction = '';

	public static $options = array();
	
	static $sqlnocache = ''; // SQL_NO_CACHE 
	
	public function __construct(){	
		parent::__construct();
		}	
		
	public static function getInstanceDb() {
		self::$dbConfig = Config::getDatabaseSettings();
		//print_r(self::$dbConfig);
		$conn = '';
		$user = (isset(self::$dbConfig['user']) ? self::$dbConfig['user'] : 'nd');
		$password = (isset( self::$dbConfig['password']) ? self::$dbConfig['password'] : 'nd');
		$host = (isset(self::$dbConfig['host']) ? self::$dbConfig['host'] : 'nd');
		$name = (isset(self::$dbConfig['name']) ? self::$dbConfig['name'] : 'nd');
		$dsn = 'mysql:host='.$host.';dbname='.$name.';port=3306;connect_timeout=15';	
		$opts = array(
    		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		);		
		if( version_compare(PHP_VERSION, '5.3.6', '<') ){
    		if( defined('PDO::MYSQL_ATTR_INIT_COMMAND') ){
        		$opts[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8';
			}
		} else {
			$dsn .= ';charset=utf8';
		}	
		try {
   			$dbh = new PDO($dsn,$user,$password,$opts);
			if( version_compare(PHP_VERSION, '5.3.6', '<') && !defined('PDO::MYSQL_ATTR_INIT_COMMAND') ){
    			$sql = 'SET NAMES ' . DB_ENCODING;
					$dbh->exec($sql);
			}			
		} catch (PDOException $e) {
    		print "Error!: " . $e->getMessage() . "<br/>";
    		die();
		}
		return $dbh;
	}
		
/* QUERY CUSTOM */		

	public static function getPdoObjRecords(){			
		$obj = array();		
		$op_fieldTokeyObj = (isset(self::$options['fieldTokeyObj']) ? self::$options['fieldTokeyObj'] : '');		
		$clause = self::$clause;
		if ($clause != '') $clause = " WHERE ".$clause;			
		if (self::$customQry == '') {
			self::$qry = "SELECT ".implode(',',self::$fields)." FROM ".self::$table.$clause;		
		} else {
			self::$qry = self::$customQry.$clause;
		}			
		if (self::$order != '') self::$qry .= ' ORDER BY '.self::$order;		
		if (self::$resultPaged == true) {
			if (self::$debugMode == 1) echo '<br>Q0: '.self::$qry;	
			self::$totalItems = self::findTotalItemsFromQuery(self::$qry);
			if (self::$page > ceil(self::$totalItems/self::$itemsForPage) || intval(self::$page) == 0) self::$page = 1;
			self::$firstId = self::findFirstId(self::$page,self::$totalItems,self::$itemsForPage);
			$limitClause = " LIMIT ".self::$itemsForPage." OFFSET ".(int)self::$firstId;
		} else {
			$limitClause = '';
		}	
		if(self::$limit != '')	$limitClause = self::$limit;		
		self::$qry .= $limitClause;		
		if (self::$debugMode == 1) echo '<br>Q1: '.self::$qry;	
		try{
			$pdoCore = self::getInstanceDb();				
			$pdoObject = $pdoCore->prepare(self::$qry);				
			$pdoObject->execute(self::$fieldsValue);		
			$pdoObject->setFetchMode(PDO::FETCH_OBJ);
			self::$foundRows = $pdoCore->query("SELECT FOUND_ROWS()")->fetchColumn(); 
		}
		catch(PDOException $pe) {
			Core::$debuglog->error( $pe,['classe'=>'Sql','method'=>'getPdoObjRecords'] );
			Core::$debuglog->error( self::$qry,['classe'=>'Sql','method'=>'getPdoObjRecords'] );
			
			self::$resultOp->message = "Errore lettura records table!";
			self::$resultOp->error = 1;
			if (self::$debugMode == 1) {
				echo $pe->getMessage();				
			}
		}	
		return $pdoObject;
	}
	
	public static function getRecords()
	{	
		$obj = array();		
		/* opzioni */
		/* la key dell oggettto è presa da un campo */
		$op_fieldTokeyObj = (isset(self::$opts['fieldTokeyObj']) ? self::$opts['fieldTokeyObj'] : '');		
		/* sezione CLAUSE */
		$clause = self::$clause;
		if ($clause != '') $clause = " WHERE ".$clause;		
		/* crea la query */		
		if (self::$customQry == '') {
			self::$qry = "SELECT ".self::$sqlnocache.implode(',',self::$fields)." FROM ".self::$table.$clause;		
			} else {
				self::$qry = self::$customQry.$clause;
				}
		/* aggiunge opt groupby */
		if (isset(self::$opts['groupby']) && self::$opts['groupby'] != '') self::$qry .= ' GROUP BY '.self::$opts['groupby'];
					
		/* sezione order */
		if (self::$order != '') self::$qry .= ' ORDER BY '.self::$order;		
				
		/* sezione limit */
		if (self::$resultPaged == true) {
			if (self::$debugMode == 1) echo '<br>Q0: '.self::$qry;	
			self::$totalItems = self::findTotalItemsFromQuery(self::$qry);
			if (self::$page > ceil(self::$totalItems/self::$itemsForPage) || intval(self::$page) == 0) self::$page = 1;

			self::$firstId = self::findFirstId(self::$page,self::$totalItems,self::$itemsForPage);
			$limitClause = " LIMIT ".self::$itemsForPage." OFFSET ".(int)self::$firstId;
			} else {
				$limitClause = '';
				}	
		if(self::$limit != '')	$limitClause = self::$limit;		
		self::$qry .= $limitClause;		
		if (self::$debugMode == 1) echo '<br>Q1: '.self::$qry;	
		try{
			$pdoCore = self::getInstanceDb();				
			$pdoObject = $pdoCore->prepare(self::$qry);				
			$pdoObject->execute(self::$fieldsValue);		
			$pdoObject->setFetchMode(PDO::FETCH_OBJ);
			self::$foundRows = $pdoCore->query("SELECT FOUND_ROWS()")->fetchColumn(); 
				while ($row = $pdoObject->fetch()) {
					if ($op_fieldTokeyObj != '') {
						$obj[$row->$op_fieldTokeyObj] = $row;
						} else {
							$obj[] = $row;
							}					
					}
			}
		catch(PDOException $pe) {
			Core::$debuglog->error( $pe,['classe'=>'Sql','method'=>'getPdoObjRecords'] );
			Core::$debuglog->error( self::$qry,['classe'=>'Sql','method'=>'getPdoObjRecords'] );
			self::$resultOp->message = "Errore lettura records table!";
			self::$resultOp->error = 1;
			if (self::$debugMode == 1) {
				echo $pe->getMessage();				
				}
			}	
		return $obj;
	}
		
	public static function getRecord(){	
		//self::resetResultOp();
		$obj = array();
		$clause = self::$clause;
		if ($clause != '') $clause = " WHERE ".$clause;			
		if (self::$customQry == '') {
			self::$qry = "SELECT ".implode(',',self::$fields)." FROM ".self::$table.$clause;		
		} else {
			self::$qry = self::$customQry.$clause;
			}		
		/* sezione order */
		if (self::$order != '') self::$qry .= ' ORDER BY '.self::$order;		
		if (self::$debugMode == 1) echo '<br>'.self::$qry;
		try{
			$pdoCore = self::getInstanceDb();
			$pdoObject = $pdoCore->prepare(self::$qry);		
			$pdoObject->execute(self::$fieldsValue);	
			$pdoObject->setFetchMode(PDO::FETCH_OBJ);		
			$obj = $pdoObject->fetch();
			self::$foundRows = $pdoCore->query("SELECT FOUND_ROWS()")->fetchColumn(); 
			}
		catch(PDOException $pe) {
			if (self::$debugMode == 1) echo $pe->getMessage();
			self::$resultOp->message = "Errore lettura record!";
			self::$resultOp->error = 1;
			}	
		return $obj;
		}


	public static function insertRecord() {
			//self::resetResultOp();
			$fields = array();
			$fieldsPrepare = array();		
			/* creo l'elenco dei campi */			
			if (is_array(self::$fields) && count(self::$fields) > 0) {
				foreach(self::$fields AS $key=>$value){
						$fields[] = $value;
						$fieldsPrepare[] = '?';		
					}	
				}							
			self::$qry = "INSERT INTO ".self::$table." (".implode(',',$fields).") VALUE (".implode(',',$fieldsPrepare).")";				
			if (self::$debugMode == 1) {
				echo  '<br>'.self::$qry;
				}
			try{
				$pdoCore = self::getInstanceDb();
				$pdoObject = $pdoCore->prepare(self::$qry);		
				$pdoObject->execute(self::$fieldsValue);
				self::$lastInsertedId = $pdoCore->lastInsertId();		
				}
			catch(PDOException $pe) {
				if (self::$debugMode == 1) echo $pe->getMessage();
				self::$resultOp->message = "<br>Errore inserimento voce!";
				self::$resultOp->error = 1;
				}
		}

	public static function updateRecord() {
			$fields = array();
			$fieldsPrepare = array();			
			/* creo l'elenco dei campi */			
			if (is_array(self::$fields) && count(self::$fields) > 0) {
				foreach(self::$fields AS $key=>$value){
						$fields[] = $value.' = ?';
					}	
				}								
			/* sezione CLAUSE */
			$clause = self::$clause;
			if ($clause != '') $clause = " WHERE ".$clause;			
			self::$qry = "UPDATE ".self::$table." SET ".implode(',',$fields).$clause;				
			if (self::$debugMode == 1) {
				echo '<br>'.self::$qry;
				print_r(self::$fieldsValue);
				}
			try{
				$dbName = self::$dbName;
				$pdoCore = self::getInstanceDb();
				$pdoObject = $pdoCore->prepare(self::$qry);		
				$pdoObject->execute(self::$fieldsValue);	
				}
			catch(PDOException $pe) {
				if (self::$debugMode == 1) echo $pe->getMessage();
				self::$resultOp->message = "Errore modifica voce!";
				self::$resultOp->error = 1;
				}				
		}
		
	public static function deleteRecord(){
		self::generateQuery('delete');
		if (self::$debugMode == 1) echo '<br>'.self::$qry;
		try{
			$dbName = self::$dbName;
			$pdoCore = self::getInstanceDb();
			$pdoObject = $pdoCore->prepare(self::$qry);		
			$pdoObject->execute(self::$fieldsValue);
			self::$lastInsertedId = $pdoCore->lastInsertId();		
			}
		catch(PDOException $pe) {
			if (self::$debugMode == 1) echo  $pe->getMessage();
			self::$resultOp->message = "<br>Errore cancellazione voce !";
			self::$resultOp->error = 0;
			}
		}
		
	public static function countRecord() {
		self::generateQuery('count');	
		if (self::$debugMode == 1) {
			echo '<br>'.self::$qry.'<br>'.print_r(self::$fieldsValue);
			}
		try{
			$dbName = self::$dbName;
			$pdoCore = self::getInstanceDb();
			$pdoObject = $pdoCore->prepare(self::$qry);		
			$pdoObject->execute(self::$fieldsValue);		
			$data = $pdoObject->fetch(PDO::FETCH_NUM);		
			return $data[0];
			}
		catch(PDOException $pe) {
			if (self::$debugMode == 1) echo $pe->getMessage();			
			self::$resultOp->message = "Errore query database!";
			self::$resultOp->error = 1;
			return 0;
			}	
		}	
		
	public static function generateQuery($action) {
		
		switch($action) {
			case 'delete':
				self::$qry = 'DELETE FROM '.self::$table;				
			break;				
			case 'count':
				$keyRif = 'id';
				self::$qry = "SELECT COUNT('".self::$fields[0]."') FROM ".self::$table;	
			break;
		
			default:			
			break;			
			}
				
			$clause = self::$clause;
			if ($clause != '') $clause = " WHERE ".$clause;			
			if (self::$customQry == '') {
				self::$qry .= $clause;		
				} else {
					self::$qry = self::$customQry.$clause;
					}			
		}
		
	public static function tableExists($tableName) {
		$dbName = self::$dbName;
		$mrSql = "SHOW TABLES LIKE :table_name";
		$pdoCore = self::getInstanceDb();
		$pdoObject = $pdoCore->prepare($mrSql);
		//protect from injection attacks
		$pdoObject->bindParam(":table_name", $tableName, PDO::PARAM_STR);
		$sqlResult = $pdoObject->execute();
		if ($sqlResult) {
			$row = $pdoObject->fetch(PDO::FETCH_NUM);
			if ($row[0]) {
				//table was found
				return true;
				} else {
            	//table was not found
            	return false;
					}
    		}	else {
        		self::$resultOp->message = "Errore query database!";
				self::$resultOp->type = 1;
				return true;
    			}
		}
		
	public static function getTableFields($tableName) {
		try {
		$dbName = self::$dbName;
		$qry = "SELECT * FROM ".$tableName." LIMIT 0";
		$pdoCore = self::getInstanceDb();
		$pdoObject = $pdoCore->prepare($qry);
		//$pdoObject->bindParam(":table_name",$tableName, PDO::PARAM_STR);
		$result = $pdoObject->execute();
			for ($i = 0; $i < $pdoObject->columnCount(); $i++) {
				$col = $pdoObject->getColumnMeta($i);
				$columns[] = $col;
				}
			return $columns;
			}
		catch(PDOException $pe) {
			if (self::$debugMode == 1) echo $pe->getMessage();			
			self::$resultOp->message = "Errore query database!";
			self::$resultOp->type = 1;
			return 0;
			}	
		}
		
	public static function getTablesDatabase($database) {
		try {
			$arr = array();
			$pdoCore = self::getInstanceDb();
			$result = $pdoCore->query("SHOW TABLES");
				while ($row = $result->fetch(PDO::FETCH_NUM)) {
					$arr[] = $row[0];
					}
			return $arr;
			}
		catch(PDOException $pe) {
			if (self::$debugMode == 1) echo $pe->getMessage();			
			self::$resultOp->message = "Errore query database!";
			self::$resultOp->error = 1;
			return false;
			}	
		}
		
	/* SQL DINAMICO  */
	
	/* INSERIMENTI  DA POST */
	public static function insertRawlyPost($fields,$table) {
		$fieldListArray = array();
		$fieldValuesArray = array();
		$fieldPrepareArray = array();
		if (is_array($fields) && count($fields) > 0){
			foreach($fields AS $key=>$value){
				$autoinc = (isset($value['autoinc']) ? isset($value['autoinc']) : false);
				$nodb = (isset($value['nodb']) ? isset($value['nodb']) : false);
				if ($autoinc == false && $nodb == false) {
					$fieldListArray[] = $key;				$autoinc = (isset($value['autoinc']) ? isset($value['autoinc']) : false);
				$nodb = (isset($value['nodb']) ? isset($value['nodb']) : false);

					if (isset($_POST[$key])) $fieldValuesArray[] = $_POST[$key];
					$fieldPrepareArray[] = '?';
					}			
				}	
			}				
			$qry = "INSERT INTO ".$table." (".implode(',',$fieldListArray).") VALUE (".implode(',',$fieldPrepareArray).")";
			if (self::$debugMode == 1) echo '<br>'.$qry;
			try{
				$dbName = self::$dbName;
				$pdoCore = self::getInstanceDb();
				$pdoObject = $pdoCore->prepare($qry);		
				$pdoObject->execute($fieldValuesArray);
				self::$lastInsertedId = $pdoCore->lastInsertId();				
				}
			catch(PDOException $pe) {
				if (self::$debugMode == 1) echo $pe->getMessage();
				self::$resultOp->message = "Errore inserimento voce!";
				self::$resultOp->error = 1;
				}
		}	
	
	/* MODIFICHE DA POST */	
	public static function updateRawlyPost($fields,$table,$clauseRif,$valueRif) {
		$fieldListArray = array();
		$fieldValuesArray = array();
		if (is_array($fields) && count($fields) > 0){
			foreach($fields AS $key=>$value) {
				$autoinc = (isset($value['autoinc']) ? isset($value['autoinc']) : false);
				$nodb = (isset($value['nodb']) ? isset($value['nodb']) : false);
				if ($autoinc == false && $nodb == false) {
					$fieldListArray[] = $key.' = ?';
					$fieldValueArray[] = $_POST[$key];
					} 			
				}	
			}		
		$fieldValueArray[] = $valueRif;			
		$qry = "UPDATE ".$table." SET ".implode(',',$fieldListArray)." WHERE ".$clauseRif." = ?";			
		if (self::$debugMode == 1) {
			echo '<br>'.$qry;		
			print_r($fieldValueArray);
			}			
				
		try{
			$dbName = self::$dbName;
			$pdoCore = self::getInstanceDb();
			$pdoObject = $pdoCore->prepare($qry);		
			$pdoObject->execute($fieldValueArray);
			}
		catch(PDOException $pe) {
			if (self::$debugMode == 1) echo $pe->getMessage();
			self::$resultOp->message = "Errore inserimento voce!";
			self::$resultOp->error = 1;
			}		
		}

	public static function updateRawlyFields($fieldslist,$table,$post,$opt) {
		$optDef = array('clause'=>'','clauseVals'=>array());	
		$opt = array_merge($optDef,$opt);
		$fields = array();
		$fieldsVal = array();
		if (is_array($fieldslist) && count($fieldslist) > 0){
			foreach($fieldslist AS $key=>$value){
				if ($value['type'] != 'autoinc' && $value['type'] != 'nodb') {
					if (isset($_POST[$key])) {
						$fields[] = $key.' = ?';
						$fieldsVal[] = $post[$key];
						}
					}			
				}	
			}		
		/* add clause */
		$clause = '';
		if ($opt['clause'] != '') $clause = $opt['clause'];	
		/* add clause value */
		if (is_array($opt['clauseVals']) && count($opt['clauseVals']) > 0) $fieldsVal = array_merge($fieldsVal,$opt['clauseVals']);
		
		$qry = "UPDATE ".$table." SET ".implode(',',$fields).($clause != '' ? ' WHERE '.$clause : '');			
		if (self::$debugMode == 1) {
			echo '<br>'.$qry;	
			print_r($fieldsVal);
			}			
		try {
			$dbName = self::$dbName;
			$pdoCore = self::getInstanceDb();
			$pdoObject = $pdoCore->prepare($qry);		
			$pdoObject->execute($fieldsVal);
			}
		catch(PDOException $pe) {
			if (self::$debugMode == 1) echo $pe->getMessage();
			self::$resultOp->message = "Errore inserimento voce!";
			self::$resultOp->error = 1;
			}			
		}
		
	/* SQL AVANZATO */
	
	public static function executeCustomQuery($qry){
		if (self::$debugMode == 1) echo '<br>'.$qry;	
		try{		
			$pdoCore = self::getInstanceDb();				
			$pdoObject = $pdoCore->prepare($qry);				
			$pdoObject->execute();
			
			self::$foundRows = $pdoCore->query("SELECT FOUND_ROWS()")->fetchColumn(); 
			}
		catch(PDOException $pe) {
			if (self::$debugMode == 1) echo $pe->getMessage();
			self::$resultOp->message .= "Errore lettura max field";
			self::$resultOp->error = 1;
			}		
		}
	
	public static function getMaxValueOfField($table,$field,$clause='') {
		$qry = "SELECT MAX(".$field.") AS count FROM ".$table;	
		if ($clause != '') $qry .= " WHERE ".$clause;
		if (self::$debugMode == 1) echo '<br>'.$qry;	
		try{
			$dbName = self::$dbName;
			$pdoCore = self::getInstanceDb();
			$pdoObject = $pdoCore->prepare($qry);		
			$pdoObject->execute();
			$array = $pdoObject->fetch(PDO::FETCH_ASSOC);
			return $array['count'];
			}
		catch(PDOException $pe) {
			if (self::$debugMode == 1) echo $pe->getMessage();
			self::$resultOp->error = 1;
			return 0;
			}	
		}	
	
	public static function countRecordQry($table,$keyRif,$clauseRif,$valueRif) {
		$qry = "SELECT COUNT(".$keyRif.") FROM ".$table;
		if ($clauseRif != '') $qry .= " WHERE ".$clauseRif;			
		if (self::$debugMode == 1) echo '<br>'.$qry;
		try{
			$dbName = self::$dbName;
			$pdoCore = self::getInstanceDb();
			$pdoObject = $pdoCore->prepare($qry);		
			$pdoObject->execute($valueRif);		
			$data = $pdoObject->fetch(PDO::FETCH_NUM);		
			return $data[0];
			}
		catch(PDOException $pe) {
			if (self::$debugMode == 1) echo $pe->getMessage();
			self::$resultOp->error = 1;
			return 0;
			}	
		}	
		
	// SQL tools
	public static function checkRequireFields($fields) 
	{
		$fieldsTemp = ToolsStrings::multiSearch($fields, array('required' => true));
		if (is_array($fieldsTemp) && count($fieldsTemp) > 0){
			foreach($fieldsTemp AS $key=>$value){
			if (!isset($_POST[$key]) || $_POST[$key] == '') {		
				self::$resultOp->error = 1;
				self::$resultOp->message = 'Devi inserire il campo '.$value['label'].'<br>';
				}				
			}
		}
	}
		
	/*
	public static function stripMagicFields($array)
	{
		$resultArray = array();
		foreach($array AS $key=>$value){
			$resultArray[$key] = SanitizeStrings::stripMagic($value);		
		}
		return $resultArray;
	}
	*/
		
   public static function findTotalItemsFromQuery($qry) {
		$count = 0;
		try {		
			$dbName = self::$dbName;
			$pdoCore = self::getInstanceDb();
			$pdoObject = $pdoCore->prepare($qry);		
			$pdoObject->execute(self::$fieldsValue);
			$count = $pdoCore->query("SELECT FOUND_ROWS()")->fetchColumn(); 
			$returnValue = true;
			return $count;
		}
		catch(PDOException $pe) {
			$pe->getMessage();
			}
		return $count;
		}
		
	public static function findFirstId($page,$totalItems,$itemPage) {
		if ($page > ceil($totalItems/$itemPage) || intval($page) == 0) $page = 1;			
			$firstId = ($page - 1) * $itemPage;
			return $firstId;	
		}
		
	 public static function getClauseVarsFromAppSession($sessionApp,$fields,$clauseWhere='',$opz=array()) { 
	 	$tableAlias = (isset($opz['tableAlias']) ? $opz['tableAlias'] : '');
		$fieldsSearch = ToolsStrings::multiSearch($fields, array('searchTable' => true));
		/* sezione per la ricerca */
		$clauseQry = array();
		$fieldsVars = array();
		$qryTemp = '';		
		$words = explode(',',$sessionApp);
		if (count($fieldsSearch) > 0) {
			foreach($fieldsSearch AS $key=>$value){					
				if (count($words) > 0) {
					foreach($words AS $value1){
						$fieldsVars[] = "%".$value1."%";
						$clauseQry[] = ($tableAlias != '' ? $tableAlias.'.' : '').$key." LIKE ?";
						}
					}		
				}			
			}			
		$qryTemp = implode(' OR ',$clauseQry);			
		return array($qryTemp,$fieldsVars);
		}
		
	public static function getClauseVarsFromSession($session,$fields,$opz=array()) {
		$separator = (isset($opz['separator']) ? $opz['separator'] : ' ');
		
		/* sezione per la ricerca */
		$clauseQry = array();
		$fieldsVars = array();
		$qryTemp = '';		
		$words = explode($separator,$session);
		if (count($fields) > 0) {
			foreach($fields AS $value){					
				if (count($words) > 0) {
					foreach($words AS $value1){
						$fieldsVars[] = "%".$value1."%";
						$clauseQry[] = $value." LIKE ?";
						}
					}		
				}			
			}			
		$qryTemp = implode(' or ',$clauseQry);			
		return array($qryTemp,$fieldsVars);
		}
		
		public static function getClauseVarsFromArray($search,$fields,$opz=array()) {
			$wf = array();
			$wfv = array();
			if (is_array($fields) && count($fields) > 0) {
				$valueFV = array();
				$valueF = '';
				foreach ($fields AS $key=>$value) {	
					$keys = preg_grep( '/'.$search.'/', $value['array']);
					if (is_array($keys) && count($keys) > 0) {
						$f = array();
						$fv = array();
						foreach ($keys AS $keyk=>$valuek) {
							$f[] = $value['field'].' = ?';
							$fv[] = $keyk;						
							}
						}						
					if (isset($f) && is_array($f) && count($f) > 0) $valueF .= implode(' OR ',$f);
					if (isset($fv) && is_array($fv) && count($fv) > 0) $valueFV = array_merge($valueFV,$fv);	
					}
				if ($valueF != '') $wf[] = $valueF;
				$wfv = $valueFV;
				}
		return array($wf,$wfv);
		}

	public static function manageFieldActive($method,$appTable,$id,$opt){
		$optDef = array('label'=>'voce','attivata'=>'attivata','disattivata'=>'disattivata');	
		$opt = array_merge($optDef,$opt);
   	switch($method) {
   		case 'active':
   			self::initQuery($appTable,array('active'),array('1',$id),'id = ?');
				self::updateRecord();	
   			self::$resultOp->message = ucfirst($opt['label'])." ".$opt['attivata']."!";
   		break;
			case 'disactive':
				self::initQuery($appTable,array('active'),array('0',$id),'id = ?');
				self::updateRecord();
   			self::$resultOp->message = ucfirst($opt['label'])." ".$opt['disattivata']."!";
   		break;   		
		}  	
	}

	public static function switchFieldOnOff($appTable,$field,$fieldRif,$id,$opt){
		$optDef = array('labelOn'=>'voce attivata','labelOff'=>'voce disattivata');
		$opt = array_merge($optDef,$opt);	
   	/* preleva il valore del flag */
   	self::initQuery($appTable,array($field),array($id),$fieldRif.' = ?');
   	if (!isset($appData)) $appData = new stdClass();
   	if (!isset($appData->item)) $appData->item = new stdClass();
		$appData->item = Sql::getRecord();
		switch($appData->item->$field) {
			case 0:
				$appData->item->$field = 1;
			break;
			case 1:
			default:
				$appData->item->$field = 0;
			break;
   		}
   	/* lo aggiorna */
		self::initQuery($appTable,array($field),array($appData->item->$field,$id),$fieldRif.' = ?');
		self::updateRecord();
		switch($appData->item->$field) {
			case 0:
				self::$resultOp->message = $opt['labelOff'];
			break;
			case 1:
			default:
				self::$resultOp->message = $opt['labelOn'];
			break;
   		}
   	}
   	
   	/* VOCI ALBERO */
	public static function setListTreeData($qry,$parent=0,$opt=array()) { 	
   		self::resetListDataVar();
   		self::setListTreeDataObj($qry,$parent,$opt);
	}
   	
	public static function setListTreeDataObj($qry,$parent = 0,$opt=array()) {
		$listdata = array();			
		$listdata = self::getListParentsDataObj($qry,$listdata,$parent,$opt);
		self::$listTreeData = $listdata;
	}
	
	public static function getListParentsDataObj($qry,$listdata,$parent = 0,$opt=array()) {
		$optDef = array('orgQry'=>'','qryCountParentZero'=>'','lang'=>'it','fieldKey'=>'','hideId'=>0,'hideSons'=>0,'rifIdValue'=>'','rifId'=>'','getbreadcrumbs'=>0,'levelString'=>'-->');	
		$opt = array_merge($optDef,$opt);	
		
		//print_r($opt);die();	

		$noId = (isset($option['noId']) ? $option['noId'] : 0);
		$fieldKey = (isset($option['fieldKey']) ? $option['fieldKey'] : '');
		
		//$rifIdValue = (isset($option['rifIdValue']) ? $option['rifIdValue'] : '0');
		$hideLabel = (isset($option['hideLabel']) ? $option['hideLabel'] : 0);
		$hideLabel = (isset($option['hideLabel']) ? $option['hideLabel'] : 0);
		$getbreadcrumbs = (isset($option['getbreadcrumbs']) ? $option['getbreadcrumbs'] : 0);
		
		
		$rifId = (isset($option['rifId']) ? $option['rifId'] : 'id');
		self::$languages = self::$globalSettings['languages'];
		
		if (!isset(self::$level)) self::$level = 0;
		
		
		if (self::$resultPaged == true) {
			if ($parent == 0) {
				self::$totalItems = self::findTotalItemsFromQuery($opt['qryCountParentZero']);	
				if (self::$page > ceil(self::$totalItems/self::$itemsForPage) || intval(self::$page) == 0) self::$page = 1;
				self::$firstId = self::findFirstId(self::$page,self::$totalItems,self::$itemsForPage);
				$qry .= " LIMIT ".self::$itemsForPage." OFFSET ".(int)self::$firstId;
			} else {
				$qry = $opt['orgQry'];
			}		
		}	
		
		
		//echo '<br> query 4: '.$qry;
		
		try {	
			$dbName = self::$dbName;
			$pdoCore = self::getInstanceDb();
			$pdoObject = $pdoCore->prepare($qry);
			$pdoObject->bindParam(':parent',$parent,PDO::PARAM_INT);	
			$pdoObject->execute() or die('errore query');			
			if ($pdoCore->query("SELECT FOUND_ROWS()")->fetchColumn() > 0) {
				$pdoObject->setFetchMode(PDO::FETCH_OBJ);						
				while ($row = $pdoObject->fetch()) {					
					$varKey = self::$countA;
					if ($opt['fieldKey'] != '') {
						$f = $opt['fieldKey'];
						$varKey = $row->$f;						
						}								
					$showid = 1;
					$showsons = 1;					
					if ($opt['hideId'] == 1 && $row->$rifId == $opt['rifIdValue']) $showid = 0;
					if ($opt['hideSons'] == 1 && $row->parent == $opt['rifIdValue']) {
						$showsons = 0;
						$showid = 0;
						}
					if($hideLabel == true && $row->type == 'label') $showid = 0;			
					if($showid == 1) {								
						$listdata[$varKey] = $row;					
						$listdata[$varKey]->level = self::$level;					
						$listdata[$varKey]->levelString = '';
						for($x1=1;$x1 <= self::$level; $x1++) {
							$listdata[$varKey]->levelString .= $opt['levelString'];
							}	
							
						/* aggiunge campi localizzati */
						$field = "title_".$opt['lang'];
						if (isset($row->$field)) $listdata[$varKey]->title = $row->$field;
						
						$field = "titleparent_".$opt['lang'];
						if (isset($row->$field)) $listdata[$varKey]->titleparent = $row->$field;	
																	
						/* breadcrumbs */				
						//$get = 1;						
						//if ($get == 1 && $getbreadcrumbs == 1) {
						if ($opt['getbreadcrumbs'] == 1) {
							if (self::$level == 0) self::$breadcrumbs = array();							
							/* azzerra i superiori al livello se ce ne sono */
							$cc = count(self::$breadcrumbs);
							$xx = self::$level;
							for ($xx;$xx<=$cc;$xx++) unset(self::$breadcrumbs[$xx]);							
							
							self::$breadcrumbs[self::$level] = array();
							self::$breadcrumbs[self::$level]['id'] = $row->id;
							if (isset($row->type)) self::$breadcrumbs[self::$level]['type'] = $row->type;
							self::$breadcrumbs[self::$level]['parent'] = $row->parent;
							if (isset($row->alias)) self::$breadcrumbs[self::$level]['alias'] = $row->alias;
							foreach (self::$languages AS $langValue) {
								$breadcrumbsTitleField = 'title_'.$langValue;
								$breadcrumbsTitleparentField = 'titleparent_'.$langValue;
								
								if (isset($row->$breadcrumbsTitleField)) self::$breadcrumbs[self::$level][$breadcrumbsTitleField] = $row->$breadcrumbsTitleField;	
								if (isset($row->$breadcrumbsTitleparentField)) self::$breadcrumbs[self::$level][$breadcrumbsTitleparentField] = $row->$breadcrumbsTitleparentField;
								

								
								if (isset($row->aliasparent)) self::$breadcrumbs[self::$level]['aliasparent'] = $row->aliasparent;
								if (isset($row->typeparent)) self::$breadcrumbs[self::$level]['typeparent'] = $row->typeparent;								
								}
								/* aggiunge campi localizzati */
								$field = "title_".$opt['lang'];
								if (isset($row->$field)) self::$breadcrumbs[self::$level]['title'] = $row->$field;	
								$field = "titleparent_".$opt['lang'];
								if (isset($row->$field)) self::$breadcrumbs[self::$level]['titleparent'] = $row->$field;	
								$listdata[$varKey]->breadcrumbs = self::$breadcrumbs;									
							} 				
									
						/* end breadcrumbs */						
						}								
					if ($showsons == 1) {							
						self::$level++;												
						self::$countA++;											
						$listdata = self::getListParentsDataObj($qry,$listdata,$row->id,$opt);	
						self::$level--;
						}
					}						
				}				
			} catch(PDOException $pe) {
				if (self::$debugMode == 1) echo $pe->getMessage();
				self::$resultOp->message = "Errore lettura subrecords!";
				self::$resultOp->error = 1;
				}
		return $listdata;
	}
		
	/* GESTIONE VARIABILI */	
	
	// reset variabili
	
	public static function resetListTreeData(){
		self::$listTreeData = '';
	}
		
	public static function resetListDataVar(){
		self::$countA = 0;
		self::$level = 0;
	}

	//  set variabili	
	public static function getQuery(){
		return self::$qry;
	}
	
	public static function setItemsForPage($value){
		self::$itemsForPage = $value;
	}

	public static function setPage($value){
		self::$page = $value;
	}
		
	public static function setCustomQry($value){
		self::$customQry = $value;
	}

	public static function setTable($value){
		self::$table = $value;
	}
		
	public static function setFields($value){
		self::$fields = $value;
	}
		
	public static function setFieldsValue($value){
		self::$fieldsValue = $value;
	}
		
	public static function setClause($value){
		if (self::$addslashes == true) {
			self::$clause = addslashes($value);
		} else {
			self::$clause = $value;
		}
	}
		
	public static function setAddslashes($value){
		self::$addslashes = $value;
	}
		
	public static function setOrder($value){
		self::$order = $value;
	}
				
	public static function setLimit($value){
		self::$limit = $value;
	}
		
	public static function setOptions($value){
		self::$opts = $value;
	}

	public static function setResultPaged($value){
		self::$resultPaged = $value;
	}
		
	public static function setDebugMode($value){
		self::$debugMode = $value;
	}
	
	public static function getLastInsertedIdVar(){	
		return self::$lastInsertedId;
	}

	public static function setLanguages($array){
		self::$languages = $array;
	}
		
	public static function setSqlNoCache($value){
		self::$sqlnocache = $value;
	}

	
	/* get variabili */	

	public static function getResultRecords(){	
		$pdoCore = self::getInstanceDb();
		return $pdoCore->query("SELECT FOUND_ROWS()")->fetch(PDO::FETCH_COLUMN);	
	}

	public static function getTotalsItems(){
		return self::$totalItems;
	}
		
	public static function getTablePrefix() {	
		self::$dbConfig = Config::getDatabaseSettings();	
		$s = (isset(self::$dbConfig['tableprefix']) ? self::$dbConfig['tableprefix'] : '');	
		return $s;
	}	

	public static function getFoundRows(){
		return self::$foundRows;
	}
		
	public static function getListTreeData(){	
		return self::$listTreeData;
	}
			
	public static function initQuery($table='',$fields='',$fieldsValue=array(),$clause='',$order='',$limit='',$opts='',$resultPaged=false){
		self::$table = $table;
		self::$fields = $fields;
		self::$fieldsValue = $fieldsValue;
		self::$clause = $clause;
		self::$limit = $limit;
		self::$order = $order;
		self::$opts = $opts;
		self::$qry = '';
		self::$customQry = '';
		self::$resultRecords = 0;
		self::$resultPaged = $resultPaged;
	}
	
	public static function initQueryBasic($table='',$fields='',$fieldsValue=array(),$clause=''){
		self::$table = $table;
		self::$fields = $fields;
		self::$fieldsValue = $fieldsValue;
		self::$clause = $clause;
		self::$qry = '';
	}
		
	public static function addRowFields($object) {				
		return $object;	
	}

}
?>