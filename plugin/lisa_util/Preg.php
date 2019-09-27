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
 * Preg class to store pregs
 * @package Util
 * @author kovacsricsi + Sitemakers Kft
 */
namespace lisa_util;

class Preg
{
	/**
	 * Constructor protect the classs, because it is static.
	 * @access private
	 * @throws Exception
	 * @return void
	 */
	private function __construct()
	{
		throw new \Exception("Illegal operation, this class is only static class!");
	}

	public static function matchLongerWordsThan($charaterCount, $string) {
		$matches = array();
		preg_match_all("/[\S]{".$charaterCount.",}/", $string, $matches);
		return $matches;
	}

}
?>
