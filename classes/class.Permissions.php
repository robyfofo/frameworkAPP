<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * classes/class.Permissions.php v.1.4.0. 11/05/2022
*/

class Permissions extends Core {
	
	public static $accessModules = array();
	public static $userModules = array();
	static $permissions;
	
	private static $dbTableModules = DB_TABLE_PREFIX.'modules';
	
	public function __construct() {
		parent::__construct();
	}

	public static function checkExpiredToken($token,$users_id)
	{
		Sql::initQuery(
			Config::$DatabaseTables['access_token'],
			array('token_key',),
			array(Config::$nowDateTime),
			'expire_date <= ?'
		);
		$foo = Sql::getRecord();
		if (Core::$resultOp->error > 0) { die('accesso negato3'); ToolsStrings::redirect(URL_SITE.'error/db'); }
		if (!isset($foo->token_key) || $foo->token_key == '') {
			die('accesso negato');
			ToolsStrings::redirect(URL_SITE.'error/nopm');
		}

		if ($foo->token_key !== $token) {
			die('accesso negato1');
			ToolsStrings::redirect(URL_SITE.'error/nopm');
		}
		
	}

	public static function createCsrftoken()
	{
		if (!isset($_SESSION['csrftoken'])) $_SESSION['csrftoken'] = bin2hex(openssl_random_pseudo_bytes(64));
	}

	public static function createExpiredToken($users_id)
	{
		// rimuovo i vecchi 
		Sql::initQuery(
			Config::$DatabaseTables['access_token'],
			array('id'),
			array(Config::$nowDateTime),
			'expire_date <= ?'
		);
		Sql::deleteRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }

		$token = bin2hex(openssl_random_pseudo_bytes(64));

		$time = new DateTime(Config::$nowDateTime);
		$time->add(new DateInterval('PT5M'));
		$expired = $time->format('Y-m-d H:i:s');
		//echo mb_strlen($token);

		Sql::initQuery(
			Config::$DatabaseTables['access_token'],
			array('users_id','token_key','expire_date','created_date'),
			array($users_id,$token,Config::$nowDateTime,$expired),
			''
		);
		Sql::insertRecord();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); }
		return $token;
	}

	public static function checkCsrftoken()
	{


		$csrftoken = filter_input(INPUT_POST, 'csrftoken', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$sessioncsrftoken = $_SESSION['csrftoken'];
		unset($_SESSION['csrftoken']);
		//echo '<br>post: '.$csrftoken;
		//echo '<br>sess: '.$sessioncsrftoken;
		if (!$csrftoken || $csrftoken !== $sessioncsrftoken) {	
			ToolsStrings::redirect(URL_SITE.'error/nopm'); 
		}
		return true;
	}
	
	public static function dbgetUserModules()
	{	
		//Sql::setDebugMode(1);
		// carica array access modules
		$table = Sql::getTablePrefix().'modules';
		$fields = array('*');
		Sql::initQuery($table,$fields,array(),'active = 1','');
		Sql::setOptions(array('fieldTokeyObj'=>'name'));
		self::$userModules = Sql::getRecords();	
		//print_r(self::$userModules);die();
	}
	
	public static function getLevelModulesRights($levels_id) 
	{
		$table = Sql::getTablePrefix().'modules_levels_access AS mla INNER JOIN '.self::$dbTableModules." AS m ON (mla.modules_id = m.id)";
		$fields = array('mla.*,m.name AS module_name,m.label AS module_label');
		Sql::initQuery($table,$fields,array($levels_id),'mla.levels_id = ?','');
		Sql::setOptions(array('fieldTokeyObj'=>'module_name'));
		$obj = Sql::getRecords();	
		return $obj;
	
	}
	
	public static function getUserModules(){	
		self::dbgetUserModules();
		return self::$accessModules;		
	}
	
	public static function dbgetUserLevelModulesRights($user){	
		//Config::$debugMode = 1;
		$levels_id = (isset($user->levels_id) ? $user->levels_id : 0);
		$table = Sql::getTablePrefix().'modules_levels_access AS a INNER JOIN '.Sql::getTablePrefix().'modules AS m ON (a.modules_id = m.id)';
		$fields = array('a.id AS id, a.read_access AS read_access, a.write_access AS write_access,m.name AS module');
		Sql::initQuery($table,$fields,array($levels_id),'a.levels_id = ? AND m.active = 1','');
		Sql::setOptions(array('fieldTokeyObj'=>'module'));
		self::$accessModules = Sql::getRecords();	
	}
		
		
	public static function getUserLevelModulesRights($user){	
		self::dbgetUserLevelModulesRights($user);
		return self::$accessModules;		
	}
	
	
	public static function checkIfModulesIsReadable($module,$user){	
		$result = false;		
		if (isset($user->is_root) && $user->is_root == 1) {
			$result = true;
		} else {
			if (isset(self::$accessModules[$module]->read_access) && self::$accessModules[$module]->read_access == 1) {
				$result = true;
			}
		}			
		// aggiunge il controllo sul core
		if (in_array($module,self::$globalSettings['requestoption']['coremodules'])) {
			$result = true;
		}
       
		return $result;
	}
	
	public static function checkIfUserModuleIsActive($module) {
		//print_r(self::$userModules);die();
		//print_r(self::$globalSettings['requestoption']);die();
		$result = false;	
		if (array_key_exists($module,self::$userModules )) {
			$result = true;
		}
		if (in_array($module,self::$globalSettings['requestoption']['othermodules'])) {
			$result = true;
		}
		return $result;	
	}
	
	public static function checkIfModulesIsWritable($module,$user){	
		$result = false;		
		if (isset($user->is_root) && $user->is_root == 1) {
			$result = true;
		} else {
			if (isset(self::$accessModules[$module]->write_access) && self::$accessModules[$module]->write_access == 1) {
				$result = true;
			}
		}			
		return $result;
	}

	public static function getUserLevels(){		
		Sql::initQuery(Sql::getTablePrefix().'levels',array('*'),array(),'active = 1','title ASC');
		Sql::setOptions(array('fieldTokeyObj'=>'id'));
		$obj = Sql::getRecords();
		$obj[0] = (object)array('id'=>0,'title'=>'Anonimo','modules'=>'','active'=>1);
		return $obj;		
	}
		
 // obsolete
	public static function __getUserLevelLabel($user_levels,$id_level,$is_root=0) 
	{
		$s = '';
		if ($is_root == 1) {
			$s = 'Root';
		} else {
			//$s .= $id_level;
			if (is_array($user_levels) && count($user_levels) > 0) {
				foreach($user_levels AS $value) {
					if ($value->id == $id_level) {
						$s = $value->title;
						break;
					}
				}
			}
		}
		return $s;
	}
	// obsolete

	public static function getUserLevelLabel($levels_id,$is_root=0) 
	{
		//ToolsStrings::dump(Config::$userLevels);
		//echo '<br>$levels_id: '.$levels_id;
		$s = '';
		if($is_root == 1) {
			$s = 'Root';
		} else {
			if (isset(Config::$userLevels[$levels_id]->title)) $s = Config::$userLevels[$levels_id]->title;
		}
		return $s;
	}
		
	public static function checkAccessUserModule($moduleName,$userLoggedData,$userModulesActive) {
		//print_r($userModulesActive);
		if (isset($userLoggedData->is_root) && $userLoggedData->is_root === 1) {
			return true;
		} else {
			/* se ?? un modulo cre da l'accsso comunque */
			if (in_array($moduleName,$userModulesActive)) {
				 return true;
			} else {
				return false;
			}				 	
		}
	}

	public static function getSqlQueryItemPermissionForUser($userLoggedData,$opt=array()) {
		$optDef = array('onlyuser'=>false,'fieldprefix'=>'','addowner'=>true);	

		
		
		$opt = array_merge($optDef,$opt); 

		//ToolsStrings::dump($opt);

		$clause = '';
		$clauseValues = array();
		$and = '';
		
		/* permissionfor user owner only */
		$clause = $opt['fieldprefix'].'users_id = ?';
		$clauseValues[] = $userLoggedData->id;
		
		if ($opt['onlyuser'] == false) {
			// add item public - access_type 0
			$clause .= ' OR '.$opt['fieldprefix'].'access_type = 0';
		}
			
		/* se root azzerra tutto */
		if (isset($userLoggedData->is_root) && intval($userLoggedData->is_root) === 1) {
			//echo 'root';
			$clause = '';
			$clauseValues = array();
		}

		//echo '<br>clause: '.$clause;
		//ToolsStrings::dump($clauseValues);
		return array($clause,$clauseValues);
	}
		
	public static function  checkReadWriteAccessOfItem($table,$id,$userLoggedData) {
		//print_r($userLoggedData);
		$access = 0;
		/* get item data */
		if ($id > 0) {
			$item = new stdClass;
			Sql::initQuery($table,array('id,users_id,access_type'),array($id),'id = ?');
			$item = Sql::getRecord();
			if (isset($item->id) && $item->id > 0) {

				/* if is ownwer read write */
				if ($userLoggedData->id === $item->users_id) {
					$access = 2;
				}
				
				/* if is not ownwer but item is public read */
				if ($userLoggedData->id <> $item->users_id && $item->access_type == 0) {
					$access = 1;
				}
			
				/* se root set read write */
				if (isset($userLoggedData->is_root) && intval($userLoggedData->is_root) === 1) {
					$access = 2;
				}
			}
		}
		/* if access = 0 go to error */
		if ($access == 0) ToolsStrings::redirect(URL_SITE.'error/access');
		return $access;
		}	
		
		
	public static function seePermissions() {
		echo '<pre>'.print_r(self::$permissions).'</pre>';
	}
	
	/*
	public static function seePermissionsLevel($level) {
		echo '<pre>'.print_r(self::$permissions[$level]).'</pre>';
	}
	*/
		
}
?>