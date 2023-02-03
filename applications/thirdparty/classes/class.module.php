<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * third-party/module.class.php v.1.2.0. 13/08/2020
*/

class Module {
	private $action;
	private $mainData;
	private $pagination;
	public $error;
	public $message;
	public $messages;
	public $errorType;

	public function __construct($action,$table) 	{
		$this->action = $action;
		$this->table = $table;
		$this->error = 0;	
		$this->message ='';
		$this->messages = array();
		}
		
	public function getUserdataAjax($id) {
		$obj = new stdClass;	
		if (intval($id) > 0) {
			/* recupera i dati memorizzati */		
			Sql::initQuery($this->table,array('*'),array($id),'id = ?');	
			$obj = Sql::getRecord();			
			}
		return $obj;
	}
}
?>