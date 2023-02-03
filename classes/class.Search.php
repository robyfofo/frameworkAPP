<?php 
// wscms/classes/class.Search.php v.1.0.0. 12/07/2016

class Search extends Core {

	public function __construct() 	{
		parent::__construct();
		}
		
	public static function inFrameWork($keyword,$languages,$lang) {
		$res = array();
		/* site-pages */
		$opz['section'] = $LocalStrings['pagine'];
		$opz['alias'] = 'page';
		$opz['itemmethod'] = '';
		$opz['ordering'] = 'title_it ASC';
		
		$opz['table'] = DB_TABLE_PREFIX.'site_pages_contents AS c LEFT JOIN '.DB_TABLE_PREFIX.'site_pages AS p ON (c.id_page = p.id)';
 		$opz['active'] = false;
 		$opz['fields'] = array('c.id_page AS id_page','p.id AS id');		 		
 		$opz['fieldsSearch'] = array();
 		foreach($languages AS $l) {
 			$opz['fields'][] = 'p.title_'.$l.' AS title_'.$l;
 			$opz['fieldsSearch'][] = 'content_'.$l;	
			}
		$res = self::inTable($keyword,$languages,'site-pages',$opz);
		
		
		return $res;
		}
	
	public static function inTableModule($keyword,$languages,$lang,$module,$opz) {
		$opzDef = array('alias'=>'');	
		$opz = array_merge($opzDef,$opz);
		
		$res = array();		
		switch ($module) {			
		 	case 'news':
		 		$opz['table'] = DB_TABLE_PREFIX.'news';
		 		$opz['active'] = true;
		 		$opz['fields'] = array('id');		 		
		 		$opz['fieldsSearch'] = array();
		 		$opz['section'] = $LocalStrings['notizie'];
		 		$opz['ordering'] = 'datatimeins ASC';
		 		foreach($languages AS $l) {
		 			$opz['fields'][] = 'title_'.$l;		 			
		 			$opz['fieldsSearch'][] = 'title_'.$l;
					$opz['fieldsSearch'][] = 'summary_'.$l;
		 			$opz['fieldsSearch'][] = 'content_'.$l;	
		 			}	 		
			break;
						
			case 'faq':
		 		$opz['table'] = DB_TABLE_PREFIX.'faq';
		 		$opz['active'] = true;
		 		$opz['fields'] = array('id');		 		
		 		$opz['fieldsSearch'] = array();
		 		$opz['section'] = $LocalStrings['faq'];
		 		$opz['ordering'] = 'ordering ASC';
		 		foreach($languages AS $l) {
		 			$opz['fields'][] = 'title_'.$l;		 			
		 			$opz['fieldsSearch'][] = 'title_'.$l;
					$opz['fieldsSearch'][] = 'content_'.$l;	
		 			}	 		
			break;			
			}
						
		if ($opz['table'] != '') {
			$res = self::inTable($keyword,$languages,$module,$opz);
			} else {
				Core::$resultOp->error = 1;
				}			
		return $res;
		}	

	public static function inTable($keyword,$languages,$module,$opz) {
		$res = array();
		$opzDef = array('table'=>'','active'=>true,'ordering'=>'','section'=>'','alias'=>'','itemidfield'=>'id','itemtitlefield'=>'title','itemmethod'=>'dt','itemaction'=>'');	
		$opz = array_merge($opzDef,$opz);
		$res = array();
		$clause = '';
		$and = '';		
		$fieldsVal = array();			
		if ($opz['active'] == true) {
			$clause = 'active = 1';
			$and = ' AND ';	
			}			
		list($subClause,$subFieldsVal) = Sql::getClauseVarsFromSession($keyword,$opz['fieldsSearch'],array('separator'=>','));
		if ($subClause != '') {
			$clause .= $and.'('.$subClause.')';
			$and = ' AND ';
			}				
		if (count($subFieldsVal) > 0) $fieldsVal = array_merge ($fieldsVal,$subFieldsVal);		
		Sql::initQuery($opz['table'],$opz['fields'],$fieldsVal,$clause);		
		if ($opz['ordering'] != '') Sql::setOrder($opz['ordering']);
		$obj = Sql::getRecords();
		
		$res[$module] = array();
		$res[$module]['alias'] = $opz['alias'];
		$res[$module]['itemidfield'] = $opz['itemidfield'];
		$res[$module]['itemtitlefield'] = $opz['itemtitlefield'];
		$res[$module]['itemaction'] = ($opz['itemaction'] != '' ? $opz['itemaction'] : $opz['alias']);
		$res[$module]['itemmethod'] = $opz['itemmethod'];		
		$res[$module]['section'] = $opz['section'];
		$res[$module]['sqldata'] = $obj;
		return $res;
		}


	}