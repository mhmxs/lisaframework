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
namespace Util;

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
		throw new \Exception("Illegal operation, this class is only static class!");
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
	public static function isDate($date, $separator = "-", $format = 422)
	{
		setType($format, "string");
		if (strlen($format) != 3) {
			return false;
		}

		return (bool)preg_match("/^[0-9]{" . $format[0] . "}" . $separator . "[0-9]{" . $format[1] . "}" . $separator . "[0-9]{" . $format[2] . "}$/i", $date);
	}

	/**
	 * Validate dateTime, valid format is yyyy-mm-dd hh:mm:ss.
	 * @access public
	 * @static
	 * @param string $date
	 * @return boolean
	 */
	public static function isDateTime($date)
	{
		return (bool)preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $date);
	}

	/**
	 * Validate email.
	 * @access public
	 * @static
	 * @param string $email
	 * @return boolean
	 */
	public static function isEmail($email)
	{
		return (bool)preg_match('/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD', (string) $email);
	}

	/**
	 * Validate domain.
	 * @access public
	 * @static
	 * @param string $domain
	 * @param mixed protocol
	 * @return boolean
	 */
	public static function isDomain($domain, $protocol = array("http", "https"))
	{
		if (!is_array($protocol)) {
			$protocol = array($protocol);
		}
		return (bool)preg_match("/^(" . join("|", $protocol) . "):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i", $domain);
	}

	/**
	 * Validate URL
	 * @access public
	 * @static
	 * @param string $url
	 * @return boolean
	 */
	public static function isUrl($url)
	{
		return (bool)filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
	}

	/**
	 * Validate IP
	 * @access public
	 * @static
	 * @param string $ip
	 * @param boolean $ipv6
	 * @param boolean $allowPrivate
	 * @return boolean
	 */
	public static function isIp($ip, $ipv6 = false, $allowPrivate = true)
	{
		$flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
		if ($allowPrivate === true) {
			$flags =  FILTER_FLAG_NO_RES_RANGE;
		}

		if ($ipv6 === true) {
			return (bool)filter_var($ip, FILTER_VALIDATE_IP, $flags);
		}

		return (bool)filter_var($ip, FILTER_VALIDATE_IP, $flags | FILTER_FLAG_IPV4);
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
		return $in1 === $in2;
	}

	/*
	 * Validate string as it contains not only space(s).
	 * @access public
	 * @static
	 * @param string $string
	 * @return boolean
	 */
	public static function isEmpty($string, $charset = null)
	{
		if ($charset === null) {
			$charset = CHARSET;
		}

		return (bool)mb_strlen(trim((string)$string), $charset) == 0;
	}

	/*
	 * Validate array as items contains not only spaces.
	 * @access public
	 * @static
	 * @param array $array
	 * @return boolean
	 */
	public static function isEmptyArray($array, $charset = null){
		foreach($array as $value) {
			if (!self::isEmpty($value, $charset)) {
				return false;
			}
		}
		return true;
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
		if ( !$this->isEmpty($string) ){
			return false;
		}
	    return (bool)preg_match("/^[0-9A-Za-z_-]+$/", $string);
	}

	/**
	 * Checks if input is a natural number except 0, and check maxlength or length.
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
			return (((bool)preg_match("/[\D]/", $input)) || ($input == 0)) ? false : true;
		} elseif ($maxLength == true) {
			return ((((bool)preg_match("/[\D]/", $input)) || ($input == 0)) || (strlen((string)$input) > $length)) ? false : true;
		} elseif ($maxLength == false) {
			return ((((bool)preg_match("/[\D]/", $input)) || ($input == 0)) || (strlen((string)$input) != $length)) ? false : true;
		}
	}

	/**
	 * Checks if a string is a proper decimal format. You can specific decimal fomat.
	 * @access public
	 * @static
	 * @param string $input
	 * @param array $decimal "x" or "x", "y"
	 * @return boolean
	 */
	public static function isDecimal($input, $format = null)
	{
		$pattern = '/^[0-9]%s\.[0-9]%s$/';

		if (!empty($format)) {
			if (count($format) > 1) {
				$pattern = sprintf($pattern, '{'.$format[0].'}', '{'.$format[1].'}');
			} elseif (count($format) > 0) {
				$pattern = sprintf($pattern, '+', '{'.$format[0].'}');
			}
		} else {
			$pattern = sprintf($pattern, '+', '+');
		}

		return (bool)preg_match($pattern, (string) $input);
	}

    /**
     * Check to see if the string is a float.
     * @access public
     * @static
     * @param string $input
     * @return boolean
     */
    public static function isFloat($input) {
        return (bool)preg_match("/^[\d]+([.|,][\d]+)?$/i", $input);
    }

    /**
     * Check to see if the string is a sha1 hash.
     * @access public
     * @static
     * @param string $input
     * @return boolean
     */
    public static function isSha1($input) {
        return (bool)preg_match("/^[a-f0-9]{40}$/i", $input);
    }

	/**
     * Check to see if the string is a md5 hash.
     * @access public
     * @static
     * @param string $input
     * @return boolean
     */
    public static function isMd5($input) {
        return (bool)preg_match("/^[a-f0-9]{32}$/i", $input);
    }

	/**
	 * Validate host reachable
	 * @access public
	 * @static
	 * @param string $hostname
	 * @param string $port
	 * @param integer $connectionTimeout
	 * @return boolean
	 */
	public static function isHostReachable( $hostname, $port = 80, int $connectionTimeout = null)
	{
		$socket = false;
		$socket = @fsockopen($hostname, $port, null, null, $connectionTimeout);

		if ( $socket === false ){
			return false;
		} else {
			fclose( $socket );
			return true;
		}
	}

    /**
	 * Validate image
	 * @access public
	 * @static
	 * @param string $uri
	 * @return boolean
	 */
	public static function isImage($uri)
	{
        $fileInfo = @getimagesize($uri);
        return !empty($fileInfo);
	}
}
?>
