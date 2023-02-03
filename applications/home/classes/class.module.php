<?php
/**
* Framework App PHP-Mysql
* PHP Version 7
* @author Roberto Mantovani (<me@robertomantovani.vr.it>
* @copyright 2009 Roberto Mantovani
* @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
* home/class.module.php v.1.0.0. 24/09/2018
*/

class Module {
	private $action;
	public $error;
	public $message;
	public $messages;
	
	public function __construct($action,$table) 	{
		$this->action = $action;
		$this->appTable = $table;
		$this->error = 0;	
		$this->message ='';
		$this->messages = array();	
		}
		
	public function getScatBlockUrl($arr,$lastLogin) {
		$str = '';
		/* preleva i dati del lla prima voce */
		/*
		$where = (isset($arr['where']) && $arr['where'] != '' ? $arr['where'] : 'created > ?');
		Sql::initQuery($arr['table'],array('*'),array($lastLogin),$where,'','',false);		
		$itemData = Sql::getRecord();		
		$str = $arr['url item']['string'];
		$opz = $arr['url item']['opz'];
		$str = preg_replace('/%URLSITE%/',URL_SITE,$str);	
		/* aggoinge parametri */
		/*
		if (isset($opz) && is_array($opz) && count($opz) > 0) {
			$str.= '/';
			foreach ($opz As $key=>$value) {
				switch($key) {
					case 'fieldScatRif':
						if (isset($itemData->$value)) $str.= $itemData->$value;
					break;
					}
				}
			rtrim($str,'/');
			}
		*/	
		return $str;
		}
	
			
	public function getScatUrl($itemData,$arr) {
		//print_r($itemData);
		$str = $arr['string'];
		$opz = $arr['opz'];
		$str = preg_replace('/%URLSITE%/',URL_SITE,$str);	
		/* aggoinge parametri */
		if (isset($opz) && is_array($opz) && count($opz) > 0) {
			$str.= '/';
			foreach ($opz As $key=>$value) {
				switch($key) {
					case 'fieldScatRif':
						if (isset($itemData->$value)) $str.= $itemData->$value;
					break;
					}
				}
			rtrim($str,'/');
			}
			
		return $str;
		}
		
	public function getItemBlockUrl($arr,$lastLogin,$opt) {
		/* preleva i dati del lla prima voce */
		$clause = (isset($opt['sqloption']['clause']) ? $opt['sqloption']['clause'] : 'created > ?');
		$clausevals = (isset($opt['sqloption']['clausevals']) ? $opt['sqloption']['clausevals'] : array($lastLogin));	
	
		$where = '';
		$and = '';	
		$fieldsVals = array();
		
		/* add clause */
		if ($clause != '') {
			$where .= $and.$clause;
			if (is_array($clausevals)) $fieldsVals = $clausevals;
			$and = ' AND ';
			}
			
		Sql::initQuery($arr['table'],array('*'),$clausevals,$where,'','',false);	
		
			
		$itemData = Sql::getRecord();		
		$str = $arr['url item']['string'];
		$opz = $arr['url item']['opz'];
		$str = preg_replace('/%URLSITE%/',URL_SITE,$str);	
		/* aggoinge parametri */
		if (isset($opz) && is_array($opz) && count($opz) > 0) {
			$str.= '/';
			foreach ($opz As $key=>$value) {
				switch($key) {
					case 'fieldItemRif':
						if (isset($itemData->$value)) $str.= $itemData->$value;
					break;
					}
				}
			rtrim($str,'/');
			}
			
		return $str;
		}
				
	public function getItemUrl($itemData,$arr) {
		$str = '';
		//print_r($itemData);

		$str = $arr['string'];
		$opz = $arr['opz'];
		$str = preg_replace('/%URLSITE%/',URL_SITE,$str);	
		/* aggoinge parametri */

		if (isset($opz) && is_array($opz) && count($opz) > 0) {
			$str.= '/';
			foreach ($opz As $key=>$value) {
				switch($key) {
					case 'fieldItemRif':
						if (isset($itemData->$value)) $str.= $itemData->$value;
					break;
					}
				}
			rtrim($str,'/');
			}
			
		return $str;
		}

	}
?>