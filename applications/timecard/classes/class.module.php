<?php
/**
 * Framework siti html-PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * timecard/module.class.php v.1.0.0. 07/03/2018
*/
class Module {
	private $action;
	
	public function __construct($action,$appTable) 	{
		$this->action = $action;
		$this->appTable = $appTable;
		}
		
	public function checkTimeInterval($userid,$id_progetto,$data,$starttime,$endtime,$opt) {
		$optDef = array('id_timecard'=>0);	
		$opt = array_merge($optDef,$opt);
		$item = new stdClass();				
		$valueRif = array($userid,$data,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime);	
		$clauseRif = 'users_id = ? AND datains = ? AND (? BETWEEN starttime AND endtime OR ? BETWEEN starttime AND endtime OR starttime BETWEEN ? AND ? OR endtime BETWEEN ? AND ?)';
		if ($opt['id_timecard'] > 0) {
			$clauseRif = $clauseRif.' AND id <> ?';
			$valueRif[] = $opt['id_timecard'];
			}	
		Sql::initQuery($this->appTable,array('*'),$valueRif,$clauseRif);
		$item = Sql::getRecord();
		if (Core::$resultOp->error == 1) die('Error db read exist timecard');	
		if (isset($item->id) && $item->id > 0) {
			Core::$resultOp->error = 1;
			$match = 1;
			if ($starttime == $item->endtime && $endtime > $item->endtime) {
				$match = 0;
				}
		if ($endtime == $item->starttime && $endtime < $item->endtime) {
				$match = 0;
				}
			if ($match == 0) {
				Core::$resultOp->error = 0;
				}
			}
		}
	}
?>