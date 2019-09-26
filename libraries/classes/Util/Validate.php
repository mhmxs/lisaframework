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

/**
 * Validate class check data validation
 * @package Util
 * @author kovacsricsi + Sitemakers Kft
 */

class Validate
{
	/**
	 * Constructor protecte the classs, because it is stataic.
	 * @access private
	 * @throws Exception
	 * @return void
	 */
	private function __construct()
	{
		throw new Exception("Illegal operation, this class is only static class!");
	}

	/**
	 * Returns all variable from $_POST array, witch begin with "$prefix_".
	 * @access public
	 * @static
	 * @param string $prefix
	 * @return array
	 */
	public static function getFormData($prefix)
	{
		$ret = array();

		foreach ($_POST as $key => $field) {
			if (strpos($key, $prefix."_") !== false) {
				$newkey = str_replace($prefix."_", "", $key);
				$ret[$newkey] = trim($field);
			}
		}
		return $ret;
	}

	/**
	 * Validate date.
	 * @access public
	 * @static
	 * @param string $date
	 * @param string $separator
	 * @param integer $format
	 * @return boolean
	 */
	public static function date($date, $separator = "-", $format = 422)
	{
		setType($format, "string");
		if (strlen($format) != 3) {
			return false;
		}

		return (preg_match("/^[0-9]{" . $format[0] . "}" . $separator . "[0-9]{" . $format[1] . "}" . $separator . "[0-9]{" . $format[2] . "}$/i", $date)) ? true : false;
	}

	/**
	 * Validate dateTime, valid format is yyyy-mm-dd hh:mm:ss.
	 * @access public
	 * @static
	 * @param string $date
	 * @return boolean
	 */
	public static function dateTime($date)
	{
		return (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $date)) ? true : false;
	}

	/**
	 * Validate email.
	 * @access public
	 * @static
	 * @param string $email
	 * @return boolean
	 */
	public static function email($email)
	{
		return (preg_match("/^(?:[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/i", $email)) ? true : false;
	}

	/**
	 * Validate passwords.
	 * @access public
	 * @static
	 * @param string $pass
	 * @param string $pass2
	 * @return boolean
	 */
	public static function passwd($pass, $pass2)
	{
		return self::isEqual($pass, $pass2);
	}

	/**
	 * Validate equals.
	 * @access public
	 * @static
	 * @param mixed $in1
	 * @param mixed $in2
	 * @return boolean
	 */
	public static function isEqual($in1, $in2)
	{
		return ($in1 === $in2) ? true : false;
	}

	/*
	 * Validate string as it contains not only space(s).
	 * @access public
	 * @static
	 * @param string $string
	 * @return boolean
	 */
	public static function isEmpty($string, $charset = null){

		if ($charset === null) {
			$charset = CHARSET;
		}

		return (mb_strlen(trim((string)$string), $charset) == 0) ? true : false;
	}


	/**
	 * Checks if a string contains characters [0-9A-Za-z_-].
	 * @access public
	 * @static
	 * @param  string
	 * @return boolean
	 */
	public static function isSafeString($string)
	{
	    return (preg_match("/^[0-9A-Za-z_-]+$/", $string)) ? true : false;
	}

	/**
	 * Checks if input is a natural number except 0, and check maxlength or length
	 * @access public
	 * @static
	 * @param  string $input
	 * @param strinf $length
	 * @param boolean $maxLength
	 * @return boolean
	 */
	public static function isPosInt($input, $length = 0, $maxLength = true)
	{
		settype($length, "integer");
		settype($maxLength, "boolean");

		if ($length == 0) {
			return ((preg_match("/[\D]/", $input)) || ($input == 0)) ? false : true;
		} elseif ($maxLength == true) {
			return (((preg_match("/[\D]/", $input)) || ($input == 0)) || (strlen((string)$input) > $length)) ? false : true;
		} elseif ($maxLength == false) {
			return (((preg_match("/[\D]/", $input)) || ($input == 0)) || (strlen((string)$input) != $length)) ? false : true;
		}
	}
}
?>
