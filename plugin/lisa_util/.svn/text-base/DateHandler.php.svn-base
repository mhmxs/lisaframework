<?php
/**
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; version 3 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
*/


namespace lisa_util;

if (!defined('DATE_ATOM')) {
	define('DATE_ATOM', "Y-m-d\TH:i:sP");
}
if (!defined('DATE_COOKIE')) {
  define('DATE_COOKIE', "l, d-M-y H:i:s T");
}
if (!defined('DATE_ISO8601')) {
  define('DATE_ISO8601', "Y-m-d\TH:i:sO");
}
if (!defined('DATE_RFC822')) {
  define('DATE_RFC822', "D, d M y H:i:s O");
}
if (!defined('DATE_RFC850')) {
  define('DATE_RFC850', "l, d-M-y H:i:s T");
}
if (!defined('DATE_RFC1036')) {
  define('DATE_RFC1036', "D, d M y H:i:s O");
}
if (!defined('DATE_RFC1123')) {
  define('DATE_RFC1123', "D, d M Y H:i:s O");
}
if (!defined('DATE_RFC2822')) {
  define('DATE_RFC2822', "D, d M Y H:i:s O");
}
if (!defined('DATE_RFC3339')) {
  define('DATE_RFC3339', "Y-m-d\TH:i:sP");
}
if (!defined('DATE_RSS')) {
  define('DATE_RSS', "D, d M Y H:i:s O");
}
if (!defined('DATE_W3C')) {
  define('DATE_W3C', "Y-m-d\TH:i:sP");
}

/**
 * Date handler class.
 * @package Util
 * @author Somlyai Dezsõ
 */
class DateHandler {
	/**
	 * Instance of DateHandler.
	 * @access protected
	 * @staticvar self
	 */
	protected static $_instance;

	/**
	 * Date
	 * @access private
	 * @var string
	 */
	public $date;

	/**
	 * Day
	 * @access private
	 * @var string
	 */
	private $day;

	/**
	 * Year
	 * @access private
	 * @var string
	 */
	private $year;

	/**
	 * Month
	 * @access private
	 * @var string
	 */
	private $month;

	/**
	 * Hour
	 * @access private
	 * @var string
	 */
	private $hour;

	/**
	 * Minute
	 * @access private
	 * @var string
	 */
	private $minute;

	/**
	 * Second
	 * @access private
	 * @var string
	 */
	private $second;

	/**
	 * Timestamp
	 * @access private
	 * @var int
	 */
	private $timestamp;

	/**
	 * Constructor.
	 * @access private
	 * @param string $date
	 * @return void
	 */
	public function __construct($date){
		list($datePart, $timePart) = explode(" ", $date);

		$date_parts = explode("-", $datePart);
		$this->year = (int) $date_parts[0];
		$this->month = (int) $date_parts[1];
		$this->day = (int) $date_parts[2];

		$date_parts = explode(":", $timePart);
		$this->hour = (int) $date_parts[0];
		$this->minute = (int) $date_parts[1];
		$this->second = (int) $date_parts[2];


		$this->date = $date;
		$this->timestamp = mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
	}

	/**
	 * Returns with instance of DateHandler.
	 * @access public
	 * @static
	 * @param string $date
	 * @return \Util\DateHandler
	 */
	public static function getInstance($date = null)  {
		$date = is_null($date) ? date("Y-m-d H:i:s") : $date;
		if ( !isset(static::$_instance[$date]) ) {
			static::$_instance[$date] = new self($date);
		}
		return static::$_instance[$date];
	}

	/**
	 * Factory method of DateHandler.
	 * @access public
	 * @static
	 * @param string $date
	 * @return DateHandler
	 */
	public static function init($date = null)  {
		$date = is_null($date) ? date("Y-m-d") : $date;
		return new self($date);
	}

	/**
	 * Get number of day in month.
	 * @access public
	 * @return int
	 */
	public function getNumberOfDaysInMonth() {
		return date("t", $this->timestamp);
	}

	/**
	 * Get full month name.
	 * @access public
	 * @return string
	 */
	public function getMonthName($lang = null) {
        if (is_null($lang)){
    		return date("F", $this->timestamp);
        } else {
            $configReader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Localization.ini");
            $months = explode(",", $configReader->$lang->months);
            return $months[$this->getMonth()-1];
        }
	}

	/**
	 * Get short month name.
	 * @access public
	 * @return string
	 */
	public function getShortMonthName($lang = null) {
        if (is_null($lang)){
    		return date("M", $this->timestamp);
        } else {
            $configReader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Localization.ini");
            $months = explode(",", $configReader->$lang->monthsShort);
            return $months[$this->getMonth()];
        }
	}

	/**
	 * Get full day name.
	 * @access public
	 * @return string
	 */
	public function getDayName($lang = null) {
        if (is_null($lang)){
    		return date("I", $this->timestamp);
        } else {
            $configReader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Localization.ini");
            $days = explode(",", $configReader->$lang->days);
            return $days[$this->getDayOfWeek()];
        }
	}

	/**
	 * Get short day name.
	 * @access public
	 * @return string
	 */
	public function getShortDayName($lang = null) {
        if (is_null($lang)){
    		return date("D", $this->timestamp);
        } else {
            $configReader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Localization.ini");
            $days = explode(",", $configReader->$lang->daysShort);
            return $days[$this->getDayOfWeek()];
        }
	}

	/**
	 * Get minute number.
	 * @access public
	 * @param bool $forceTwoChars
	 * @return mixed
	 */
	public function getMinute($forceTwoChars = false) {
		return ( $forceTwoChars && strlen($this->minute) == 1 ) ? "0".$this->minute: $this->minute;
	}

	/**
	 * Get hour number.
	 * @access public
	 * @param bool $forceTwoChars
	 * @return mixed
	 */
	public function getHour($forceTwoChars = false) {
		return ( $forceTwoChars && strlen($this->hour) == 1 ) ? "0".$this->hour: $this->hour;
	}

	/**
	 * Get day number.
	 * @access public
	 * @param bool $forceTwoChars
	 * @return mixed
	 */
	public function getDay($forceTwoChars = false) {
		return ( $forceTwoChars && strlen($this->day) == 1 ) ? "0".$this->day: $this->day;
	}

	/**
	 * Get day number of week.
	 * @access public
	 * @return int
	 */
	public function getDayOfWeek() {
		return date("w", $this->timestamp);
	}

	/**
	 * Get month number.
	 * @access public
	 * @param bool $forceTwoChars
	 * @return mixed
	 */
	public function getMonth($forceTwoChars = false) {
		return ( $forceTwoChars && strlen($this->month) == 1 ) ? "0".$this->month: $this->month;
	}

	/**
	 * Get year.
	 * @access public
	 * @return int
	 */
	public function getYear() {
		return $this->year;
	}

	/**
	 * Get unix timestamp.
	 * @access public
	 * @return int
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}

	/**
	 * Add month to a date.
	 * @access public
	 * @param int $month
	 * @return string
	 */
	public function addMonths($month) {
		$this->timestamp = mktime(0, 0, 0, $this->month + $month, $this->day, $this->year);
		$this->setDate();
		return $this->date;
	}

	/**
	 * Sub month to a date.
	 * @access public
	 * @param int $month
	 * @return string
	 */
	public function subMonths($month) {
		$this->timestamp = mktime(0, 0, 0, $this->month - $month, $this->day, $this->year);
		$this->setDate();
		return $this->date;
	}

	/**
	 * Add days to a date.
	 * @access public
	 * @param int $days
	 * @return string
	 */
	public function addDays($days) {
		$this->timestamp = mktime(0, 0, 0, $this->month, $this->day + $days, $this->year);
		$this->setDate();
		return $this->date;
	}

	/**
	 * Sub days to a date.
	 * @access public
	 * @param int $days
	 * @return string
	 */
	public function subDays($days) {
		$this->timestamp = mktime(0, 0, 0, $this->month, $this->day - $days, $this->year);
		$this->setDate();
		return $this->date;
	}

	/**
	 * Add years to a date.
	 * @access public
	 * @param int $years
	 * @return string
	 */
	public function addYears($years) {
		$this->timestamp = mktime(0, 0, 0, $this->month, $this->day, $this->year + $years);
		$this->setDate();
		return $this->date;
	}

	/**
	 * Sub years to a date.
	 * @access public
	 * @param int $years
	 * @return string
	 */
	public function subYears($years) {
		$this->timestamp = mktime(0, 0, 0, $this->month, $this->day, $this->year - $years);
		$this->setDate();
		return $this->date;
	}

	/**
	 * Date difference.
	 * @access public
	 * @param int $date
	 * @param string $precision (y, year, m, month, d, day, h, hour, i, minute, s, second)
	 * @return int
	 */
	public function diffDate($date = null, $precision = "d") {
		$date = (is_null($date)) ? time() : strtotime($date);
		$precision = strtolower($precision);
		switch ($precision){
			case "d":
			case "day":
				$divisor = 60*60*24;
				break;
			case "m":
			case "month":
				// TODO: not precise!
				$divisor = 60*60*24*30;
				break;
			case "y":
			case "year":
				// TODO: not precise!
				$divisor = 60*60*24*365.25;
				break;
			case "h":
			case "hour":
				$divisor = 60*60;
				break;
			case "i":
			case "minute":
				$divisor = 60;
				break;
			case "s":
			case "second":
				$divisor = 1;
				break;
			default:
				break;
		}

		return (int) (($this->timestamp - $date) / $divisor);
	}

	/**
	 * Date is today.
	 * @access public
	 * @return bool
	 */
	public function isToday() {
		return ($this->getYear()."-".$this->getMonth(true)."-".$this->getDay(true) == date("Y-m-d"));
	}

	/**
	 * Hour is this hour.
	 * @access public
	 * @return bool
	 */
	public function isThisHour() {
		return ($this->getYear()."-".$this->getMonth(true)."-".$this->getDay(true)." ".$this->getHour(true) == date("Y-m-d H"));
	}

	/**
	 * Minute is this minute.
	 * @access public
	 * @return bool
	 */
	public function isThisMinute() {
		return ($this->getYear()."-".$this->getMonth(true)."-".$this->getDay(true)." ".$this->getHour(true).":".$this->getMinute(true) == date("Y-m-d H:i"));
	}

	/**
	 * Minute is this minute.
	 * @access public
	 * @return bool
	 */
	public function isInOneMinute() {
		return $this->_inPreiod(60);
	}

	/**
	 * Minute is this minute.
	 * @access public
	 * @return bool
	 */
	public function isInOneHour() {
		return $this->_inPreiod(60*60);
	}

	/**
	 * Minute is this minute.
	 * @access public
	 * @return bool
	 */
	public function isInOneDay() {
		return $this->_inPreiod(60*60*24);
	}

	/**
	 * Returns if datetime is in a period
	 * @param int $interval seconds
	 * @return bool
	 */
	protected function _inPreiod($interval){
		return ( (time() - $interval) - $this->timestamp ) < 0;
	}

	/**
	 * Date is yesterday.
	 * @access public
	 * @return bool
	 */
	public function isYesterday() {
		$time = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$yesterday = $time - 86400;
		if ($this->timestamp == $yesterday) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Date is tomorrow.
	 * @access public
	 * @return bool
	 */
	public function isTomorrow() {
		$time = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$tomorrow = $time - 86400;
		if ($this->timestamp == $tomorrow) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Set date parts.
	 * @access private
	 * @return void
	 */
	private function setDate() {
		$this->date = date("Y-m-d", $this->timestamp);
		$date_parts = explode("-", $this->date);
		$this->year = (int) $date_parts[0];
		$this->month = (int) $date_parts[1];
		$this->day = (int) $date_parts[2];
	}
}