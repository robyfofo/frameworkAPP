<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * classes/class.Subcategories.php v.1.3.0. 14/09/2020
*/

class Subcategories extends Core {

	public static $ordering = 'title ASC';
	public static $langUser = 'it';
	public static $countItems = 1;
	public static $initParent = 0;
	public static $levelString = '<i class="fas fa-chevron-right"></i>&nbsp;';
	public static $fieldKey = '';

	public static $hideId = 0;
	public static $hideSons = 0;
	public static $rifId = '';
	public static $rifIdValue = '';

	private static $dbTable = '';
	private static $dbTableItem = '';

	public function __construct(){
		parent::__construct();
	}

	/* SQL QUERIES */

	public static function getObjFromSubCategories() {
		//Core::setDebugMode(1);
		$qry = "SELECT c.id AS id,c.parent AS parent, c.title AS title,";
		$qry .= "c.active AS active,";
		if ( self::$countItems == true) $qry .= "(SELECT COUNT(i.id) FROM ".self::$dbTableItem." AS i WHERE i.categories_id = c.id) AS items,";
		$qry .= '(SELECT p.title FROM '.self::$dbTable.' AS p WHERE c.parent = p.id)  AS titleparent,';
		$qry .= "(SELECT COUNT(id) FROM ".self::$dbTable." AS s WHERE s.parent = c.id)  AS sons";
		$qry .= " FROM ".self::$dbTable." AS c
		WHERE c.parent = :parent
		ORDER BY ".self::$ordering;

		//echo $qry; die('fatto');

		$opt = array(
			'orgQry'							=> $qry,
			'qryCountParentZero'				=> '',
			'lang'								=> self::$langUser,
			'fieldKey'							=> self::$fieldKey,
			'hideId'							=> 0,
			'hideSons'							=> 0,
			'rifIdValue'						=> '',
			'rifId'								=> '',
			'getbreadcrumbs'					=> 0,
			'levelString'						=> self::$levelString
		);

		Sql::resetListTreeData();
		Sql::resetListDataVar();
		Sql::setListTreeData($qry,self::$initParent,$opt);
		$obj = Sql::getListTreeData();
		return $obj;
		}

	public static function getCategoryDetails($id,$table,$opz) {
		$obj =  new stdClass;
		$actived = (isset($opz['actived']) ? $opz['actived'] : true);
		/* prende la categoria indicata */
		$clause = 'id = ?';
		if ($actived == true) $clause .= ' AND active = 1';
		Sql::initQuery($table,array('*'),array($id),$clause);
		$obj = Sql::getRecord();
		$obj = self::addCustomFields($obj);
		return $obj;
	}


	public static function setDbTable($value) {
	    self::$dbTable = $value;
	}

	public static function setDbTablItem($value) {
	    self::$dbTableItem = $value;
	}

	public static function addCustomFields($obj) {
		return $obj;
	}

}
?>
