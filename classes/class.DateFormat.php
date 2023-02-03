<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * classes/class.DateFormat.php v.1.3.0. 08/09/2020
*/

class DateFormat extends Core  {
	private static $dateVars = array();
	private static $timeVars = array();
	private static $year = 2000;
	private static $month = 1;
	private static $day = 1;
	private static $hours = 0;
	private static $minutes = 0;
	private static $seconds = 0;

	public function __construct() {
		parent::__construct();
	}

	public static function checkDateTimeIsoIniEndInterval($datetimeisoini,$datetimeisoend,$compare='>') {
		$res = true;
		$res = self::checkDateTimeIso($datetimeisoini);
		if ($res == true) $res = self::checkDateTimeIso($datetimeisoend);
		if ($res == true) {
			$dateini = DateTime::createFromFormat('Y-m-d H:i:s',$datetimeisoini);
			$dateend = DateTime::createFromFormat('Y-m-d H:i:s',$datetimeisoend);
			switch ($compare) {
  				default:
  					if ($dateini->getTimestamp() > $dateend->getTimestamp()) {
  						Core::$resultOp->error = 1;
  						$res = false;
  					}
  				break;
  			}
		}
		return $res;
	}

	public static function checkDateFormatIniEndInterval($dateini,$dateend,$format,$compare='>') {
		$res = true;
		if ($format == '') $format = 'Y-m-d H:i:s';
		$res = self::checkDateFormat($dateini,$format);
		if ($res == true) $res = self::checkDateFormat($dateend,$format);
		if ($res == true) {
			$dini = DateTime::createFromFormat($format,$dateini);
			$dend = DateTime::createFromFormat($format,$dateend);
			switch ($compare) {
  				default:
  					if ($dini->getTimestamp() > $dend->getTimestamp()) {
  						Core::$resultOp->error = 1;
  						$res = false;
  						} else {
  							}
  				break;
  				}
			}
		return $res;
		}



	/* DATEPICKER */
	public static function checkDateTimeFromDatepicker($datetime,$format) {
		if ($format == '') $format = 'd/m/Y H:i:s';
    	$d = DateTime::createFromFormat($format,$datetime);
      $errors = DateTime::getLastErrors();
		return ($errors['warning_count'] == 0 && $errors['error_count'] == 0) ? true : false;
		}

	public static function convertDatepickerToIso($datetime,$formatPick,$formatISO,$defaultDateTime) {
		/* controlla default data se iso */
		if ($formatPick == '') $format = 'd/m/Y';
		if ($formatISO == '') $formatISO = 'Y-m-d H:i:s';
		/* imposta la data di default */
		if (self::checkDateTimeIso($defaultDateTime) == false) $defaultDateTime = date('Y-m-d H:i:s');
		$res = self::checkDateTimeFromDatepicker($datetime,$formatPick);
		if ($res == true) {
			/* converto in iso */
			$d = DateTime::createFromFormat($formatPick,$datetime);
			$errors = DateTime::getLastErrors();
			$datetimeIso = ($errors['warning_count'] == 0 && $errors['error_count'] == 0) ? $d->format($formatISO)  : $defaultDateTime;
			} else {
				$datetimeIso = $defaultDateTime;
				}
		return $datetimeIso;
		}


	/* ISO */
	public static function checkDateTimeIso($datetimeiso) {
	  	$d = DateTime::createFromFormat('Y-m-d H:i:s',$datetimeiso);
	  	$errors = DateTime::getLastErrors();
		return ($errors['warning_count'] == 0 && $errors['error_count'] == 0) ? true : false;
		}

	public static function checkDateIso($dateiso) {
		$d = DateTime::createFromFormat('Y-m-d',$dateiso);
		$errors = DateTime::getLastErrors();
		return ($errors['warning_count'] == 0 && $errors['error_count'] == 0) ? true : false;
	}

	/* LAYOUT */
	public static function getDateTimeIsoFormatString($datetimeIso='',$format='',$langMonts,$langDays,$opt) {
		if ($datetimeIso != '') self::explodeDateTimeIso($datetimeIso);
		$s = self::getDateString($format,$langMonts,$langDays);
		return $s;
		}


	/* TOOLS */

	public static function convertDateFormats($date,$formatinput,$formatoutput,$defaultDate) {
		if (self::checkDateIso($defaultDate) == false) $defaultDate = date('Y-m-d');
		$d = DateTime::createFromFormat($formatinput,$date);
		$errors = DateTime::getLastErrors();
		$date = ($errors['warning_count'] == 0 && $errors['error_count'] == 0) ? $d->format($formatoutput)  : $defaultDate;
		return $date;
	}

	public static function checkDateFormat($date,$format) {
	  	$d = DateTime::createFromFormat($format,$date);
	  	$errors = DateTime::getLastErrors();
		return ($errors['warning_count'] == 0 && $errors['error_count'] == 0) ? true : false;
		}

	public static function sum_the_time($times) {
  		//$times = array($time1, $time2);
  		$seconds = 0;
  		$sum_time = '00:00:00';
  		if (isset($times) && is_array($times) && count($times) > 0) {
 			foreach ($times as $time) {
				list($hour,$minute,$second) = explode(':', $time);
				$seconds += $hour*3600;
				$seconds += $minute*60;
				$seconds += $second;
  			}
			$hours = floor($seconds/3600);
			$seconds -= $hours*3600;
			$minutes  = floor($seconds/60);
			$seconds -= $minutes*60;

			$sum_time = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds); // Thanks to Patrick
  		}
   		// return "{$hours}:{$minutes}:{$seconds}";
		return $sum_time;
	}

	public static function explodeDateTimeIso($datetime) {
		$d = explode(' ',$datetime);
		list($date,$time) = $d;
		self::$dateVars = explode('-',$date);
		self::$timeVars =  explode(':',$time);
		self::$day = self::$dateVars[2];
		self::$month = self::$dateVars[1];
		self::$year = self::$dateVars[0];
		self::$hours = self::$timeVars[0];
		self::$minutes = self::$timeVars[1];
		self::$seconds = self::$timeVars[2];
		}

	public static function getDateString($format='',$langMonts,$langDays) {
		$s = '';
		$month = intval(self::$month);
		$day = intval(self::$day);

		$format = preg_replace('/%DAY%/',self::$day,$format);
		$format = preg_replace('/%STRINGMONTH%/',ucfirst($langMonts[$month]),$format);
		$format = preg_replace('/%STRINGDATADAY%/',self::getDayOfDate($langDays,array()),$format);
		$format = preg_replace('/%MONTH%/',self::$month,$format);
		$format = preg_replace('/%YEAR%/',self::$year,$format);
		$format = preg_replace('/%HH%/',self::$hours,$format);
		$format = preg_replace('/%II%/',self::$minutes,$format);
		$s = $format;

		switch ($format) {
			case 'dd StringMonth YYYY':
				$s = self::$day. ' '.ucfirst($langMonts[$month]).' '.self::$year;
			break;

			case 'StringDay StringMonth YYYY':
				$s = self::$day. ' '.ucfirst($langMonts[$month]).' '.self::$year;
			break;

			case 'StringMonth dd, YYYY':
				$s = ucfirst($langMonts[$month]).' '.self::$day. ', '.self::$year;
			break;

			case 'StringMonth':
				$s = ucfirst($langMonts[$month]);
			break;

			case 'dd/mm/YYYY':
			/* dd/mm/YYYY */
				$s = self::$day.'/'.self::$month.'/'.self::$year;
			break;

			case 'hh:mm':
			/* dd/mm/YYYY */
				$s = self::$hours.':'.self::$minutes;
			break;

			case 'YYYY-mm-dd':
				$s = self::$year.'-'.self::$month.'-'.self::$day;
			break;

			case 'dd':
				$s = self::$day;
			break;

			}
		return $s;
		}

	public static function getDayOfDate($langDays,$opt) {
		$optDef = array();
		$opt = array_merge($optDef,$opt);
		$dt = self::$year.'-'.self::$month.'-'.self::$day;
		$date = DateTime::createFromFormat('Y-m-d',$dt);
		$errors = DateTime::getLastErrors();
		if ($errors['error_count'] > 0 && $errors['warning_count']) {
			return 'n.d.';
			} else {
				$d = intval($date->format('N'));
				$ds = $langDays[$d];
				return $ds;
				}
		}

	}
