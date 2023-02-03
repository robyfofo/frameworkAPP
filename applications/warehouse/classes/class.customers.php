<?php
/* wscms/ecommerce/class/class.customers.php v.3.3.0. 14/06/2017 */

class Customers {
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
		
	public function checkUsername($id,$_lang){
		$this->error = 0;
		$oldUsername = '';
		$App = new stdClass;	
      $App->oldItem = new stdClass;	
		if (intval($id) > 0) {
			/* recupera i dati memorizzati */
			Sql::initQuery($this->appTable,array('username'),array($id),'id = ?');	
			$App->oldItem = Sql::getRecord();
			$oldUsername = $App->oldItem->username;			
			}
		if ($oldUsername != $_POST['username']) {
			Sql::initQuery($this->appTable,array('id'),array($_POST['username']),'username = ?');
			$count = Sql::countRecord();
			if ($count > 0) {
				$this->message = preg_replace('/%USERNAME%/',$_POST['username'],$_lang['Username %USERNAME% risulta già presente nel nostro database!']);

	      	$this->error = 1;
	     	 	$_POST['username'] = '';
	   		}	
	   	}
	   return $_POST['username'];
		}
				
	public function checkPassword($id,$_lang) {
		$id = intval($id);
		$App = new stdClass;	
      $App->oldItem = new stdClass;
		$oldPassword = '';
		if ($id > 0) {
			/* modifica*/
			/* recupera i dati memorizzati */
			Sql::initQuery($this->appTable,array('password'),array($id),'id = ?');
			$App->oldItem = Sql::getRecord();
			$oldPassword = $App->oldItem->password;	
			if ($_POST['password'] != '') {
				if ($_POST['password'] === $_POST['passwordCF']) {
					$_POST['password'] = md5($_POST['password']);
		   		}	else {
		   			$_POST['password'] = $oldPassword;
		   			$this->message = $_lang['Le due password non corrispondono! Sarà comunque mantenuta quella precedentemente memorizzata'];
			      	$this->errorType = 2;
		   			}
				
				} else {
      			$_POST['password'] = $oldPassword;
      			}
			
			} else {
				/* inserisci */
				if ($_POST['password'] != '') {
					if ($_POST['password'] === $_POST['passwordCF']) {
		      			$_POST['password'] = md5($_POST['password']);
		      		} else {
		      			$this->message = $_lang['Le due password non corrispondono!'];
		      			$this->error = 1;		      			
		      			}
					} else {
				 		$this->message = $_lang['Devi inserire la password!'];
		      		$this->error = 1;
		      		$_POST['password'] = '';
				 	}
				}
	   return $_POST['password'];
		}
		
	public function checkUsernameAjax($id,$username){
		$App = new stdClass;	
      $App->oldItem = new stdClass;
		$oldUsername = '';
		$count = 0;
		if (intval($id) > 0) {
			/* recupera i dati memorizzati */		
			Sql::initQuery($this->appTable,array('username'),array($id),'id = ?');	
			$App->oldItem = Sql::getRecord();
			$oldUsername = $App->oldItem->username;			
			}
		if($oldUsername != $username) {
			Sql::initQuery($this->appTable,array('id'),array($_POST['username']),'username = ?');
			$count = Sql::countRecord();
			}
		return $count;
		}
		
	public function checkExistEmail($id,$_lang) {
		$this->error = 0;
		$oldEmail = '';
		$App = new stdClass;	
      $App->oldItem = new stdClass;	
		if (intval($id) > 0) {
			/* recupera i dati memorizzati */
			Sql::initQuery($this->appTable,array('email'),array($id),'id = ?');
			$App->oldItem = Sql::getRecord();
			$oldEmail = $App->oldItem->email;			
			}
		if($oldEmail != $_POST['email']) {
			Sql::initQuery($this->appTable,array('id'),array($_POST['email']),'email = ?');
			$count = Sql::countRecord();
			if ($count > 0) {
				$this->message = preg_replace('/%EMAIL%/',$_POST['email'],$_lang['Indirizzo email %EMAIL% risulta già presente nel nostro database!']);
	      	$this->error = 1;
	     	 	$_POST['email'] = '';
	   		}	
	   	}
	   return $_POST['email'];
		}

	public function checkExistEmailAjax($id,$email){
		$App = new stdClass; 
		$oldItem = new stdClass;     
		$oldEmail = '';
		$count = 0;
		if (intval($id) > 0) {
			/* recupera i dati memorizzati */			
			Sql::initQuery($this->appTable,array('email'),array($id),'id = ?');	
			$oldItem = Sql::getRecord();
			$oldEmail = $oldItem->email;			
			}
		if ($oldEmail != $email) {
			Sql::initQuery($this->appTable,array('id'),array($email),'email = ?');
			$count = Sql::countRecord();
			}
		return $count;
		}

	}
?>
