<?php
/**
 * Framework siti html-PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * admin/todo/class.module.php v.3.0.0. 06/02/2017
*/

class Module {
	private $action;
	public $error;
	public $message;
	public $messages;

	public function __construct($action,$appTable) 	{
		$this->action = $action;
		$this->appTable = $appTable;
		$this->error = 0;	
		$this->message ='';
		$this->messages = array();	
		}

	}
?>