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
 * URL manipulations.
 * @package Util
 * @author pilou
 */
class URLHandler
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
	 * Redirect browser to same domain dropping last parts of current path.
	 * @access public
	 * @static
	 * @param integer $howmany (how many part of the path you want to drop)
	 * @param string $suffix (to tag $appendage onto the end of changed path)
	 * @throws Exception
	 * @return void
	 */
	public static function urlBack($howmany = 1, $suffix = null)
	{
		if (!is_int($howmany) || ($howmany < 1)) {
			throw new Exception("First parameter howmany (optional) must be an integer greater than zero");
    	}

    	if (($suffix != null) && preg_match('((/[a-z0-9-_.%~]*)*)?', $suffix)){
      		throw new Exception("Second parameter suffix (optional) must be a valid URL path");
		}

    	$uri = explode("/", substr($_SERVER['REQUEST_URI'], 1));

    	if (count($uri) < $howmany){
      		$howmany = count($uri);
    	}

    	for($i=0; $i < $howmany; $i++){
      		unset($uri[count($uri) - 1]);
    	}

    	$uri = "/" . join("/", $uri);

    	HTTP::redirect($uri . $suffix);
	}
}
?>