<?php
/*
	framework siti html-PHP-Mysql
	copyright 2011 Roberto Mantovani
	http://www.robertomantovani.vr;it
	email: me@robertomantovani.vr.it
	classes/class.Config.php v.1.4.0. 12/05/2022
*/
class Config {
	static $confArray;
	public static $resultOp;
	public static $messageToUser;
	public static $debugMode;
	public static $moduleConfig;	
	public static $dbName;
	public static $dbTablePrefix;
	public static $dbConfig;
	public static $globalSettings;

	public static $nowDate;
	public static $nowDateTime;
	public static $nowTime;

	public static $nowDateIta;
	public static $nowDateTimeIta;
	public static $nowTimeIta;

	public static $DatabaseTables;
	public static $DatabaseTablesFields;
	public static $localStrings;
	public static $modules;
	public static $userModules;
	public static $userLevels;

	public static $pathIncludes;
	
	public function __construct()
	{	
	}

	public static function init() 
	{
		self::$resultOp =  new stdclass;
		self::$resultOp->type = 0;
		self::$resultOp->error =  0;
		self::$resultOp->message =  '';
		self::$resultOp->messages =  array();	
		self::$messageToUser =  new stdclass;
		self::$messageToUser->type =  0;
		self::$messageToUser->message =  '';
		self::$messageToUser->messages =  array();
		self::$debugMode = 0;
		self::$dbName = DATABASE;

		self::$nowDate = date('Y-m-d');
		self::$nowDateTime = date('Y-m-d H:i:s');
		self::$nowTime = date('H:i:s');

		self::$nowDateIta = date('d/m/Y');
		self::$nowDateTimeIta = date('d/m/Y H:i:s');
		self::$nowTimeIta = date('H:i:s');

	}	

	public static function initDatabaseTables($path = '') 
	{
		if (file_exists(self::$pathIncludes."include/configuration_database_core_structure.php")) {
			include_once(self::$pathIncludes."include/configuration_database_core_structure.php");
		} else {
			die('il file '.self::$pathIncludes.'include/configuration_database_core_structure.php non esiste!');
		}

		$tables = $DatabaseTables;
		$tableOptions = $DatabaseTables;
		$fields = $DatabaseTablesFields;	

		if (file_exists(self::$pathIncludes."include/configuration_database_modules_structure.php")) {
			include_once(self::$pathIncludes."include/configuration_database_modules_structure.php");
		} else {
			die('il file '.self::$pathIncludes.'include/configuration_database_modules_structure.php non esiste!');
		}

		$tables1 = $DatabaseTables;
		$tableOptions1 = $DatabaseTables;
		$fields1 = $DatabaseTablesFields;

		self::$DatabaseTables = array_merge($tables,$tables1);
		self::$DatabaseTables = array_merge($tableOptions,$tableOptions1);
		self::$DatabaseTablesFields = array_merge($fields,$fields1);
	}
	

	public static function loadLanguageVars($currentlanguage,$path = '')
	{
		$localStrings = array();
		if ($currentlanguage != '') {
			if (file_exists($path."languages/".$currentlanguage.".inc.php")) {
				include_once($path."languages/".$currentlanguage.".inc.php");
			} else {
				include_once($path."languages/it.inc.php");
			}			
		} else {
			include_once($path."languages/it.inc.php");
		}
		self::$localStrings = $localStrings;
	}
		
	public static function checkModuleConfig($table,$configs) {	
		if (Sql::tableExists($table) == true) {
		/* legge la configurazione */
		self::$moduleConfig = new stdClass();
		Sql::initQuery($table,array('*'),array(),'active = 1');
		Sql::setOptions(array('fieldTokeyObj'=>'name'));
		self::$moduleConfig = Sql::getRecords();
		
		/* controlla se ci sono i parametri richiesti */
		if (is_array($configs) && count($configs) > 0) {
			foreach ($configs AS $value) {
				if (!isset(self::$moduleConfig[$value['name']]) || (isset(self::$moduleConfig[$value['name']]) && self::$moduleConfig[$value['name']]->name == '')) {
					self::$resultOp->error = 1;
					self::$resultOp->messages[] = 'Il parametro di configurazione "'.$value['name'].'" non è presente oppure è vuoto!';
					}
				}
			}
		
		if(self::$resultOp->type == 1) {
			self::$resultOp->error = 1;
			self::$resultOp->messages[] = 'La tabella della configurazione non è presente!';
			} else {
				/* controlla se ci sono presenti le configurazioni */
				}
		} else {
			self::$resultOp->error = 1;
			self::$resultOp->messages[] = 'La tabella della configurazione non è presente!';
			}
		}

	public static function read($name) {
		return self::$confArray[$name];
		}
	
	public static function write($name, $value) {
		self::$confArray[$name] = $value;
		}
	public static function setGlobalSettings($globalSettings) {
		self::$globalSettings = $globalSettings;
		}
		
	public static function getDatabaseSettings() {
		$dbConfig = self::$globalSettings[self::$dbName];
		return $dbConfig;
	}
		
	public static function setDatabase($database) {
		self::$dbName = $database;
		}
	}
?>
