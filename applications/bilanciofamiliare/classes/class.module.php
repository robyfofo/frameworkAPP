<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * invoices/module.class.php v.1.0.0. 09/11/2017
*/

class Module {
	private $action;
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
		
	public function calculateArt($post) {	
		$post['price_total'] = $post['price_unity'] * $post['quantity'];
		$post['price_tax'] = ($post['price_total'] * $post['tax']) / 100;
		return $post;
		}

	}
?>