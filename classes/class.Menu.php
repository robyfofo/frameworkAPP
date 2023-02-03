<?php
/**
 * Framework siti html-PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * admin/classes/class.Menu.php v.3.0.0. 01/11/2016
*/

class Menu extends Core {

	public static $output = '';
	public static $level = 0;
	public static $subItems = 0;
	
	public function __construct() 	{
		parent::__construct();
		}
		
	public static function getOneLevelMenuFromSubPages($obj,$parent,$opz) {	
		$opzDef = array(
			'liClass'=>'','arefClass'=>'','arefSuffixString'=>'','titleSuffixString'=>'','getatitle'=>false,'titleField'=>'','lang'=>'it','valueUrlEmpty'=>'javascript:void(0)','pagesModule'=>URL_SITE.'page/','valueUrlDefault'=>'{{ALIAS}}/{{SEO}}'
		); 
		$opz = array_merge($opzDef,$opz);
		
		if (is_array($obj) && count($obj) > 0) {
			foreach($obj AS $key=>$value) {
				if (intval($value->parent) == $parent) {			
					/* crea titles */
	       		$titlesVal = self::getTitlesVal($value,$opz);          		
	       		/* crea url */
	       		$hrefUrl = 'javascript:void(0)';
	       		if (isset($value->type)) {
	       			$hrefUrl = self::getUrlFromPageType($value,$titlesVal,$opz);
	       			}
					self::$output .= '<li class="'.$opz['liClass'].'"><a class="'.$opz['arefClass'].'" href="'.$hrefUrl.'"'.$opz['arefSuffixString'].'>'.$titlesVal['title'].$opz['titleSuffixString'].'</a></li>'.PHP_EOL;     
					$id = intval($value->id);
					self::$level++;	
					self::getOneLevelMenuFromSubPages($obj,$id,$opz); 
					self::$level--;										
					}
				}
		 	}
		return self::$output;
		}
	
	
	public static function getMenuFromSubPages($obj,$parent,$opz) {	
		$opzDef = array(
			'ulMainClass'=>'','ulSubClass'=>'',
			'liMainClass'=>'','liSubClass'=>'',
			'arefMainClass'=>'','arefSubClass'=>'',
			'arefMainSuffixString'=>'','arefSubSuffixString'=>'',
			'titleMainSuffixString'=>'','titleSubSuffixString'=>'','titleSubParentSuffixString'=>'',
			
			'ulIsMain'=>0,'getatitle'=>false,'titleField'=>'','lang'=>'it','valueUrlEmpty'=>'javascript:void(0)','pagesModule'=>URL_App.'page/','valueUrlDefault'=>'{{ID}}/{{SEO}}'
		); 
		$opz = array_merge($opzDef,$opz);
		$has_children = false;

		if(is_array($obj) && count($obj) > 0) {
			foreach($obj AS $key=>$value) {				
				if (intval($value->parent) == $parent) { 			    
					if ($has_children === false) {
						$has_children = true;					
						
						if (self::$level == 0) $ulClass = $opz['ulMainClass'];
						if (self::$level > 0) $ulClass = $opz['ulSubClass'];									
            		if (self::$level > $opz['ulIsMain']) self::$output .= '<ul class="'.$ulClass.'">'."\n";  
           		}					
					
					/* gestione classi dinamiche */
					$liClass = '';
					if(self::$level == 0) $liClass = $opz['liMainClass']; 
					if(self::$level == 0 && $value->sons == 0) $liClass = $opz['liSubClass'];	
														
					/* active page */
         		if(self::$level == 0 && $value->alias == $opz['activepage']) $liClass .= ' active';	
	
					$arefClass = '';
					if(self::$level == 0) $arefClass = $opz['arefMainClass']; 
					if(self::$level == 0 && $value->sons == 0) $arefClass = $opz['arefSubClass']; 
										
					$arefSuffixString = '';
					if(self::$level == 0) $arefSuffixString =  $opz['arefMainSuffixString']; 
					if(self::$level == 0 && $value->sons == 0) $arefSuffixString = $opz['arefSubSuffixString'];
					
					$titleSuffixString = '';					 					
 					if (self::$level == 0 && $value->sons == 0) $titleSuffixString = $opz['titleMainSuffixString'];
 					if (self::$level == 0 && $value->sons > 0) $titleSuffixString = $opz['titleSubParentSuffixString']; 
					if (self::$level > 0 && $value->sons > 0) $titleSuffixString = $$opz['titleSubSuffixString'];
					
					/*
					
          		
         		if(self::$level > 0 && $value->sons > 0) $classLi = $classSubLiParent;
         		if(self::$level > 0 && $value->sons == 0) $classLi = $classDefLi;       		
         		
					
          		
         		if(self::$level > 0 && $value->sons > 0) $classAref = $classSubArefParent;					
					
          		
         		if(self::$level > 0 && $value->sons > 0) $aRefSuffixString = $aRefSuffixStringSubParent;         		
					if(self::$level == 0) $titlePrefixString = $titlePrefixStringMain; 
          		if(self::$level == 0 && $value->sons == 0) $titlePrefixString = $titlePrefixStringSub; 
         		if(self::$level > 0 && $value->sons > 0) $titlePrefixString = $titlePrefixStringSubParent;
         		if(self::$level > 0 && $value->sons == 0) $titlePrefixString = $titlePrefixStringSub;
         		
         		
         		
         		
          		if(self::$level == 0 && $value->sons == 0) $titleSuffixString = $titleSuffixStringSub; 
         		if(self::$level > 0 && $value->sons > 0) $titleSuffixString = $titleSuffixStringSubParent;
         		if(self::$level > 0 && $value->sons == 0) $titleSuffixString = $titlePrefixStringSub;
					*/
					
					
					
					
					//if($value->alias == $opz['activepage']) $liMainClass .= ' active';
					 		
	       		/* crea titles */
	       		$titlesVal = self::getTitlesVal($value,$opz);          		
	       		/* crea url */
	       		$hrefUrl = 'javascript:void(0)';
	       		if (isset($value->type)) {
	       			$hrefUrl = self::getUrlFromPageType($value,$titlesVal,$opz);
	       			}
   
             
 					self::$output .= '<li class="'.$liClass.'"><a class="'.$arefClass.'" href="'.$hrefUrl.'"'.$arefSuffixString.'>'.$titlesVal['title'].$titleSuffixString.'</a>'.PHP_EOL;     
 					
 					$id = intval($value->id);
 					self::$level++;	
						self::getMenuFromSubPages($obj,$id,$opz); 
					self::$level--;										
					self::$output .= '</li>'."\n";
	 				}	 		
		 		}
		 	}
			if ($has_children === true && self::$level > $opz['ulIsMain']) self::$output .= '</ul>'."\n";
		return self::$output;
		}
		
		
		
	

		
	public static function getTitlesVal($value,$opz) {
		$titlesVal = array();
		$fieldTitle = 'title_';
 		$fieldTitleSeo = 'title_seo_';
 		$fieldTitleMeta = 'title_meta_';         		        		
 		if ($opz['titleField'] != '')  $fieldTitle = rtrim($opz['titleField'] ,'_').'_'.$langSuffix;		 
 		/* gestione multilingua */  
 		$titlesVal['title'] = Multilanguage::getLocaleObjectValue($value,$fieldTitle,$opz['lang'],array());
 		$titlesVal['titleSeo'] = Multilanguage::getLocaleObjectValue($value,$fieldTitleSeo,$opz['lang'],array());
 		$titlesVal['titleMeta'] = Multilanguage::getLocaleObjectValue($value,$fieldTitleMeta,$opz['lang'],array()); 
		return $titlesVal;	
		}
	
	public static function getUrlFromPageType($value,$titlesVal,$opz) {
		$url = '';
		switch($value->type) {
			case 'label':
				$url = $opz['valueUrlEmpty'];						
			break;
			case 'module':
				$url = URL_SITE.$value->url;
			break;			
			case 'url':
				$url = $value->url;							
			break;						
			default:		      
	  			$url = $opz['pagesModule'].$opz['valueUrlDefault'];
			break;
	  		}
	  		
		$url = str_replace('{{URLSITE}}',URL_SITE,$url);	
		$url = preg_replace('/{{ID}}/',$value->id,$url);
		$url = preg_replace('/{{ALIAS}}/',$value->alias,$url);
		$url = preg_replace('/{{SEO}}/',$titlesVal['titleSeo'],$url);
		$url = preg_replace('/{{SEOCLEAN}}/', SanitizeStrings::urlslug($titlesVal['titleSeo'],array('separator'=>' ')),$url);
		$url = preg_replace('/{{SEOENCODE}}/', urlencode($titlesVal['titleSeo']),$url);
		$url = preg_replace('/{{TITLE}}/', urlencode($titlesVal['titleSeo']),$url); 

		return $url;
		}
		
	/* SQL QUERIES */
	
	public static function setMainPagesData($opz) {
		$obj = new stdClass();
		Sql::initQuery(DB_TABLE_PREFIX.'site_pages',array('*'),array(),'active = 1 AND parent = 0',' ordering ASC');	
		$obj = Sql::getRecords();
		return $obj;
		}
		
	public static function setSubPagesData($parent,$opz) {
		$obj = new stdClass();
		Sql::initQuery(DB_TABLE_PREFIX.'site_pages',array('*'),array($parent),'active = 1 AND parent = ?',' ordering ASC');	
		$obj = Sql::getRecords();
		return $obj;
		}
		
	public static function setMainTreePagesData($opz) {
		$opzDef = array(
			'languages'=>array('it')
		); 
		$opz = array_merge($opzDef,$opz);

		
		$qry = "SELECT c.id AS id,c.parent AS parent,";	
		foreach($opz['languages'] AS $lang) {		
			$qry .= "c.title_meta_".$lang." AS title_meta_".$lang.",c.title_seo_".$lang." AS title_seo_".$lang.",c.title_".$lang." AS title_".$lang.",";
			}		
		$qry .= "c.id_template AS id_template,c.ordering AS ordering,c.type AS type,c.alias AS alias,c.url AS url,c.target AS target,c.active AS active,(SELECT tp.title_it FROM ".DB_TABLE_PREFIX."site_templates AS tp WHERE c.id_template = tp.id)  AS template_name,";
		foreach($opz['languages'] AS $lang) {		
			$qry .= "(SELECT p.title_".$lang." FROM ".DB_TABLE_PREFIX."site_pages AS p WHERE c.parent = p.id)  AS titleparent_".$lang.",";
			}
		$qry .= "(SELECT COUNT(id) FROM ".DB_TABLE_PREFIX."site_pages AS s WHERE s.parent = c.id) AS sons FROM ".DB_TABLE_PREFIX."site_pages AS c WHERE c.active = 1 AND c.parent = :parent AND menu = 1 ORDER BY ordering ASC";		
		Sql::resetListTreeData();
		Sql::resetListDataVar();
		Sql::setListTreeData($qry,0,array('fieldKey'=>'alias'));
		$obj = Sql::getListTreeData();
		if (Core::$resultOp->error == 1) die('Errore database pagine dinamiche');
		return $obj;
		}	
	
	}
?>