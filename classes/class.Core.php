<?php
/*
	framework siti html-PHP-Mysql
	copyright 2011 Roberto Mantovani
	http://www.robertomantovani.vr;it
	email: me@robertomantovani.vr.it
	classes/class.Core.php v.1.0.0. 19/10/2018
*/
class Core extends Config {
	public static $request;
	public static $debuglog;
	
	public function __construct(){			
		parent::__construct();
		self::$request = new stdclass;
		}	
		
	public static function getRequest($opz = array()) {
		$opzDef = array('othermodules'=>array(),'defaulttemplate'=>'default');	
		$opz = array_merge($opzDef,$opz);	

		self::$request->type = 'module';
		self::$request->action = '';
		self::$request->method = '';
		self::$request->param = '';
		self::$request->params = array();
		/* altre sezioni */
		self::$request->page = 1;
		self::$request->lang = '';
		self::$request->templateUser = $opz['defaulttemplate'];
		/* pagina */
		self::$request->page_alias = '';
		self::$request->page_id = 0;
		
      $reqs = (empty($_GET['request'])) ? '' : $_GET['request'];
		if (!empty($reqs)) {
			/*** le prime parti (action,method e id) ***/
		
			$parts = explode('/', $reqs);		
			$parts = self::parseInitReqs($parts,$opz);
		 
			self::$request->action = (isset($parts[0]) ? $parts[0] : $opz['defaultaction']);
			self::$request->method = (isset($parts[1]) ? $parts[1] : '');
			self::$request->param = (isset($parts[2]) ? $parts[2] : '');
			self::$request->params = array();
			
			/* controllo del method in caso di page, lang o altri speciali */
			if (self::$request->method == 'page') self::$request->page = self::$request->param;
						
			/* controllo del param in caso di page, lang o altri speciali */
			if (self::$request->param == 'page' && isset($parts[3])) self::$request->page = $parts[3];
			
			
			/* prende le altre parti e le salva come params */		
			if (count($parts) > 3) {
				unset($parts[0]);
				unset($parts[1]);	
				unset($parts[2]);		
				
				$pageKey = 0;
				$langKey = 0;	
				foreach ($parts AS $key=>$value) {
					self::$request->params[] = ($value != '' ? $value : 'NULL');					
					if ($value == 'page') $pageKey = $key+1;										
					/* se trova il key memorizza */
					if ($pageKey == $key && $value != '') self::$request->page = $value;				
					}	
				}			
			}
		/* gestisce il post */
		/* parametri post */
		if (isset($_POST['action']) && $_POST['action'] != '') self::$request->action = $_POST['action'];
		if (isset($_POST['method']) && $_POST['method'] != '') self::$request->method = $_POST['method'];
		if (isset($_POST['id']) && $_POST['id'] != '') self::$request->param = intval($_POST['id']);
		if (isset($_POST['param']) && $_POST['param'] != '') self::$request->param = $_POST['param'];
		if (isset($_POST['page']) && $_POST['page'] != '') self::$request->page = $_POST['page'];
		if (isset($_POST['lang']) && $_POST['lang'] != '') self::$request->lang = $_POST['lang'];
		//print_r(self::$request);
		/* pulisce le voci */
		if (self::$request->action != '') self::$request->action = SanitizeStrings::urlslug(self::$request->action,array('delimiter'=>''));
		if (self::$request->method != '') self::$request->method = SanitizeStrings::urlslug(self::$request->method,array('delimiter'=>'','lowercase'=>false));
		if (self::$request->param != '') self::$request->param = SanitizeStrings::xssClean(self::$request->param);
		if (is_array(self::$request->params) && count(self::$request->params) > 0) {
			foreach (self::$request->params AS $key=>$value) {
				if (isset(self::$request->params[$key]) && self::$request->params[$key] != '') self::$request->params[$key] = SanitizeStrings::urlslug(self::$request->params[$key],array('delimiter'=>' '));
				}
			}
		}			

	public static function parseInitReqs($parts,$opz) {	
		$changeaction = false;
		$action = (isset($parts[0]) ? $parts[0] : '');
		if (isset($action) && $action != '') {	
			/* controlla se il lingua */
			if (in_array($action,Core::$globalSettings['languages'])) {
				self::$request->type = "lang";
				self::$request->lang = $action;
				unset($parts[0]);
				$parts = array_values($parts);				
				}
				
									
			$action = (isset($parts[0]) ? $parts[0] : '');	
			/* controlla se è nell/elenco moduli */
			Sql::initQuery(Sql::getTablePrefix().'modules',array('id'),array($action),'active = 1 AND alias = ?');
			$obj = Sql::getRecord();
			if (Core::$resultOp->error == 1) die('Errore db lettura moduli!');			
			if (Sql::getFoundRows() == 0) {				
				$changeaction = true;			
				}
				
			if ($changeaction == true && in_array($action,$opz['othermodules'])) {
				$changeaction = false;
				}
			
			
			if ($opz['managechangeaction'] == 1 && $changeaction == true) {
				$arr = array('page');
				$parts = array_merge($arr,$parts);				
				self::$request->type = "page";
				Sql::initQuery(Sql::getTablePrefix().'site_pages',array('id,alias'),array($action),'active = 1 AND (alias = ?)');
				Core::$request->page_data = Sql::getRecord();
				if (Core::$resultOp->error == 1) die('Errore db lettura pagina!');							
				if (isset(Core::$request->page_data->alias)) Core::$request->page_alias = Core::$request->page_data->alias;
				if (isset(Core::$request->page_data->id)) Core::$request->page_id = Core::$request->page_id;
				} else {
					Core::$request->page_alias = $action;
					Core::$request->page_id = 0;
					}
			}
		return $parts;
		}		

	public static function getRequestParam($param) {	
		$paramValue = '';	
		/* se è un method */
		if (self::$request->method === $param && isset(self::$request->param)) $paramValue = self::$request->param;
		/* se è un param */
		if (self::$request->param === $param && isset(self::$request->params[0])) $paramValue = self::$request->params[0];
		/* se è un params */
		if (is_array(self::$request->params) && count(self::$request->params) > 0) {
			$paramKey = -1;
			foreach (self::$request->params AS $key=>$value) {
				if ($value === $param) $paramKey = $key+1;
				/* se trova il key memorizza */
				if ($paramKey == $key && $value != '') $paramValue = $value;
				}
			}
		return $paramValue;
		}
		
	public static function createUrl($opz=array()) {
		
		/* opzioni */
		$otherparams = (isset($opz['otherparams']) ? $opz['otherparams'] : '');
		$parampage = (isset($opz['parampage']) ? $opz['parampage'] : true);		
				
		$url_arr = array();		
		if (self::$request->action != '') {
			$url_arr[] = self::$request->action;
			}
		if (self::$request->method != '') {
			$url_arr[] = self::$request->method;
			}
		if (self::$request->param != '') {
			$url_arr[] = self::$request->param;
			}
		
		if (isset(self::$request->params) && is_array(self::$request->params) && count(self::$request->params) > 0) {
			$url_arr = array_merge($url_arr,self::$request->params);
			}	
			
		/* aggiungi alti parametri se presenti */
		if (is_array($otherparams) && count($otherparams) > 0) {
			foreach ($otherparams AS $key=>$value) {
				$url_arr[] = $key;
				$url_arr[] = $value;
				}
			}	
			
		/* elimina il parametro page */
		if ($parampage == false) {
			$key = array_search('page',$url_arr);
			unset($url_arr[$key]);
			unset($url_arr[$key+1]);
			}			
		$url = URL_SITE.implode('/',$url_arr);
		return $url;
		}	
		
			
	public static function setDebugMode($value){
		self::$debugMode = $value;
		}
		
	public static function resetResultOp($value){
		self::$resultOp->type =  0;
		self::$resultOp->message =  '';
		self::$resultOp->messages =  array();	
		}
	
	public static function resetMessageToUser($value){
		self::$messageToUser->type =  0;
		self::$messageToUser->message =  '';
		self::$messageToUser->messages =  array();	
		}				

	}
?>