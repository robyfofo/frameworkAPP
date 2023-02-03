<?php
/*
	framework siti html-PHP-Mysql
	copyright 2011 Roberto Mantovani
	http://www.robertomantovani.vr;it
	email: me@robertomantovani.vr.it
	wscms/classes/class.Config.php v.2.6.2. 11/02/2016
*/
class Config {
	static $confArray;
	public static $resultOp;
	public static $messageToUser;
	public static $debugMode;
	public static $moduleConfig;	
	public static $dbName;
	public static $dbConfig;
	public static $globalSettings;
		
	public function __construct(){	
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
		//self::$globalSettings = $globalSettings;
		//self::$dbConfig = $globalSettings['database'][self::$dbName]
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
		$dbConfig = self::$globalSettings['database'][self::$dbName];
		return $dbConfig;
		}
		
	public static function setDatabase($database) {
		self::$dbName = $database;
		}
	}
?>
