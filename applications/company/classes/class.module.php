<?php
/**
 * Framework App PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * customers/module.class.php v.1.0.0. 02/11/2017
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
		
	public function listMainData($fields,$page,$itemsForPage,$languages,$opt=array()) {
		$optDef = array('lang'=>'it','type'=>0,'multilanguage'=>0,'tableItems'=>'');	
		$opt = array_merge($optDef,$opt);	
		$qry = "SELECT c.id AS id,
		c.parent AS parent,c.title,c.active AS active,
		(SELECT COUNT(id) FROM ".$this->table." AS s WHERE s.parent = c.id)  AS sons,
		(SELECT COUNT(id) FROM ".$opt['tableItems']." AS ite WHERE ite.id_cat = c.id)  AS items,";
		$qry .= "(SELECT p.title FROM ".$this->table." AS p WHERE c.parent = p.id)  AS titleparent";
		$qry .= " FROM ".$this->table." AS c
		WHERE c.parent = :parent";			
		Sql::resetListTreeData();
		Sql::resetListDataVar();
		Sql::setListTreeData($qry,0,$opt);				
		$this->mainData = Sql::getListTreeData();
		}
		
	public function getMainData(){
		return $this->mainData;
		}	

	public function getPagination(){
		return $this->pagination;
		}		
	}
?>