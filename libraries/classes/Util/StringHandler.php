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
 * String manipulations.
 * @package Util
 * @author nullstring
 */
class StringHandler
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
	 * convert non locale characters to [a-z] characters
	 * TODO : needs to be expanded with all kind of letters
	 * @param  string $string
	 * @return string
	 */
	public static function localeToCommonLower ($string, $charset = null)
	{
		if ($charset === null) {
			$charset = CHARSET;
		}

	    $string     = mb_strtolower($string, $charset);
	    $chars_from = array("ö", "ü", "ó", "ő", "ú", "é", "á", "ű", "í", "û", "õ");
	    $chars_to   = array("o", "u", "o", "o", "u", "e", "a", "u", "i", "u", "o");
	    $string     = str_replace($chars_from, $chars_to, $string);

		return $string;
	}


	/**
	 * remove special characters from the string
	 *
	 * @param  string $string
	 * @return string
	 */
	public static function createAlias ($string, $charset = null)
	{
		if ($charset === null) {
			$charset = CHARSET;
		}

    	$string     = trim($string);
    	$string     = self::localeToCommonLower($string, $charset);
	    $string     = preg_replace("/[^a-z0-9-]/i", "-", $string);

	    do {
	        $string = str_replace("--", "-", $string);
	    } while (ereg("--", $string));

	    $string = trim($string, "-");

	    return $string;
	}

	/**
	 * remove < and > characters from the given string
	 * @param  string $text
	 * @return string
	 */
	public static function removeHtml ($string)
	{
	    $string = str_replace("<", "&lt;", $string);
	    $string = str_replace(">", "&gt;", $string);

	    return $string;
	}

	/**
	 * create safe filname without special chars etc.
	 *
	 * @param  string $fileName
	 * @return string
	 */
	public static function safeFileName ($fileName, $charset = null)
	{
		if ($charset === null) {
			$charset = CHARSET;
		}

	    $ext  = substr(strrchr($fileName, "."), 1);
	    $name = mb_substr($fileName, 0, -mb_strlen($ext), $charset);

	    return self::CreateAlias($name) . "." . self::CreateAlias($ext);
	}

	/**
	 * convert any localization string's first letter to uppercase
	 *
	 * @param  string $string
	 * @return string
	 */
	public static function ucFirst($string, $encoding = null)
	{
		if ($charset === null) {
			$charset = CHARSET;
		}

		$first    = mb_strtoupper(mb_substr($string, 0, 1, $encoding), $encoding);
    	$string   = $first . mb_substr($string, 1, mb_strlen($string, $encoding), $encoding);
    	return $string;
	}
}

?>