<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 *	classes/class.Form.php v.1.3.0. 07/09/2020
*/

class Form extends Core {

	public function __construct() {
		parent::__construct();
		}

	public static function getUpdateRecordFromPostResults($id,$resultOp,$opt) {
		$optDef = array('label done'=>'modifiche effettuate','modviewmethod'=>'formMod','label modified'=>'voce modificata','label modify'=>'modifica voce','label insert'=>'inserisci voce');
		$opt = array_merge($optDef,$opt);
		$viewMethod = '';
		$pageSubTitle = '';
		$message = $resultOp->message;
		if ($resultOp->error == 1) {
			$pageSubTitle = ucfirst($opt['label modify']);
			$viewMethod = $opt['modviewmethod'];
			} else {
				if (isset($_POST['submitForm'])) {
					$viewMethod = 'list';
					$message = ucfirst($opt['label modified']).'!';
					} else {
						if (isset($_POST['id'])) {
							$id = $_POST['id'];
							$pageSubTitle = $opt['label modify'];
							$viewMethod = $opt['modviewmethod'];
							$message = ucfirst($opt['label done']).'!';
							} else {
								$viewMethod = 'formNew';
								$pageSubTitle = $opt['label insert'];
								}
						}
				}
		return array($id,$viewMethod,$pageSubTitle,$message);
		}

	public static function getInsertRecordFromPostResults($id,$resultOp,$opt) {
		$optDef = array('label inserted'=>'voce inserita','label insert'=>'inserisci voce');
		$opt = array_merge($optDef,$opt);
		$viewMethod = '';
		$pageSubTitle = '';
		$message = $resultOp->message;
		if ($resultOp->error == 1) {
			$pageSubTitle = $opt['label insert'];
			$viewMethod = 'formNew';
			} else {
				$viewMethod = 'list';
				$message = ucfirst($opt['label inserted']).'!';
				}
		return array($id,$viewMethod,$pageSubTitle,$message);
		}

	public static function parsePostByFields($fields,$_lang,$opz) {
		//print_r($fields);
		$opzDef = array('stripmagicfields'=>true);
		$opz = array_merge($opzDef,$opz);
		if (is_array($fields) && count($fields) > 0) {
			foreach($fields AS $key=>$value) {

				$namefield = $key;

				/*
				echo '<br>namefield: '.$namefield;
				echo ' - '.$_POST[$namefield];
				*/
				$labelField = (isset($value['label']) ? $value['label'] : '');

				/* aggiorna con il default se vuoti */
				if ( !isset($_POST[$namefield]) ) {
					if ( isset($value['defValue']) ) $_POST[$namefield] = $value['defValue'];
				}


				/* controlla se e richiesto */
				if ( isset($value['required']) && $value['required'] == true ) {
					//echo 'nome campo: '.$namefield;
					if ( !isset($_POST[$namefield]) || (isset($_POST[$namefield]) && $_POST[$namefield] == '') ) {
						self::$resultOp->error = 1;
						self::$resultOp->messages[] = preg_replace('/%FIELD%/',$value['label'],$_lang['Devi inserire il campo %FIELD%!']);
						break;
					}
				}

				/* valida i campi se richiesto */
				if ( isset($value['validate']) && $value['validate'] != false ) {
					$_POST[$namefield] = self::validateField($namefield,$labelField,$value,$_lang);
				}

				/* aggiunge gli slashes */
				if ($opz['stripmagicfields'] == true && isset($_POST[$namefield])) $_POST[$namefield] = SanitizeStrings::stripMagic($_POST[$namefield]);
			}
		}
	}

	public static function validateField($namefield,$labelField,$value,$_lang) {
		$str = '';
		switch ($value['validate']) {
			case 'int':
				$str = self::validateInt($_POST[$namefield]);
			break;

			case 'float':
				if (!isset($value['defValue'])) $value['defValue'] = '99.99';
				$str = self::validateFloat($_POST[$namefield]);
				if ($str == '') $str = $value['defValue'];
			break;

			case 'datetimeiso':
				$str = $_POST[$namefield];
				if ($str == '') $str = $value['defValue'];
				$labelField = (isset($value['label']) ? $value['label'] : '');
				self::validateDatetimeIso($str,$labelField,$_lang);
			break;

			case 'minmax':
				$minvalue = (isset($value['valuesRif']['min']) && $value['valuesRif']['min'] != '' ? $value['valuesRif']['min'] : 0);
				$maxvalue = (isset($value['valuesRif']['max']) && $value['valuesRif']['max'] != '' ? $value['valuesRif']['max'] : 0);
				$str = self::validateMinMaxValues($_POST[$namefield],$labelField,$_lang,$minvalue,$maxvalue);
			break;

			case 'time':
				$str = self::validateTime($_POST[$namefield],$labelField,$_lang);
			break;

			case 'explodearray':
				$opz1 = (isset($value['opz']) ? $value['opz'] : array());
				$str = self::validateExplodearray($_POST[$namefield],$opz1);
			break;

			case 'timepicker':
				if (!isset($value['defValue'])) $value['defValue'] = date('H:i:s');
				$time = DateFormat::convertDatepickerToIso($_POST[$namefield],$_lang['datepicker time format'],'H:i:s',$value['defValue']);
 				$str = $time;
			break;

			case 'datetimepicker':
				$datetime = DateFormat::convertDatepickerToIso($_POST[$namefield],$_lang['datepicker data time format'],'Y-m-d H:i:s',$value['defValue']);
				$str = $datetime;
			break;

			case 'datepicker':
				if (!isset($value['defValue'])) $value['defValue'] = date('Y-m-d');
				$date = DateFormat::convertDatepickerToIso($_POST[$namefield],$_lang['datepicker data format'],'Y-m-d',$value['defValue']);
				$str = $date;
			break;
			default:
				$str = $_POST[$namefield];
			break;
			}

		return $str;

		}

/* VALITAZIONE CAMPI */

	public static function validateExplodearray($array,$opz) {
		$opzDef = array('delimiter'=>',');
		$opz = array_merge($opzDef,$opz);
		if (is_array($array)) {
			$array = implode($array,$opz['delimiter']);
			}
		return $array;
		}

	public static function validateTime($value,$labelField,$_lang) {
		$time = date('Y-m-d').' '.$value;
		$res = DateFormat::checkDateTimeIso($time);
		if ($res == false) {
			$s = $_lang['La data %FIELD% inserita non è valida!'];
			$s = preg_replace('/%FIELD%/',$labelField,$s);
			self::$resultOp->messages[] = $s;
			}

		}

	public static function validateDatetimeIso($value,$labelField,$_lang) {
		$res = DateFormat::checkDateTimeIso($value);
		if ($res == false) {
			$s = $_lang['La data %FIELD% inserita non è valida!'];
			$s = preg_replace('/%FIELD%/',$labelField,$s);
			self::$resultOp->messages[] = $s;
		}		
	}

	public static function validateInt($value) {
		return intval($value);
		}

	public static function validateFloat($value) {
		if (filter_var($value, FILTER_VALIDATE_FLOAT) == false) $value = floatval($value);
		return $value;
		}

	public static function validateMinMaxValues($valuesrif,$labelField,$_lang,$minvalue,$maxvalue) {
		if ($valuesrif < $minvalue || $valuesrif > $maxvalue) {
			self::$resultOp->error = 1;
			$s = $_lang['Il campo %FIELD% deve avere un valore superiore o uguale a %MIN% e inferiore o uguale a %MAX%!'];
			$s = preg_replace('/%MIN%/',$minvalue,$s);
			$s = preg_replace('/%MAX%/',$maxvalue,$s);
			$s = preg_replace('/%FIELD%/',$labelField,$s);
			self::$resultOp->messages[] = $s;
			}
		return $valuesrif;
		}

	}
?>
