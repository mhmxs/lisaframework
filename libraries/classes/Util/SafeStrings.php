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
 * safe string manipulations.
 * @package Util
 * @author nullstring
 * @tutorial it is alias for StringHandler's some method
 */
class SafeStrings
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
	 * remove special characters from the string
	 *
	 * @param  string $string
	 * @return string
	 */
	public static function createAlias ($string, $charset = "UTF-8")
	{
    	return StringHandler::createAlias($string, $charset);
	}

	/**
	 * remove < and > characters from the given string
	 * @param  string $text
	 * @return string
	 */
	public static function removeHtml ($string)
	{
	    return StringHandler::removeHtml($string);
	}

	/**
	 * create safe filname without special chars etc.
	 *
	 * @param  string $fileName
	 * @return string
	 */
	public static function safeFileName ($fileName, $charset = "UTF-8")
	{
	    return StringHandler::safeFileName($fileName, $charset);
	}

	/**
	 * Checks if a string contains characters [0-9A-Za-z_-]
	 * @param  string
	 * @return int (1, 0)
	 */
	public static function isSafeString ($string)
	{
	    return Validate::isSafeString($string);
	}
}
?>