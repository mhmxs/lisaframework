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
 * Router routing url request and compire uri to controller.
 * @package Core
 * @category Core
 * @author kovacsricsi
 */

class Router
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
	 * Read controller and parameters from Router class.
	 * @access public
	 * @static
	 * @return array
	 */
	public static function getController()
	{
		$uri = ($i = strpos($_SERVER["REQUEST_URI"],'?'))?substr($_SERVER["REQUEST_URI"], 0, $i):$_SERVER["REQUEST_URI"];
		if (strlen($uri) > 1) {
			$uri = rtrim($uri, "/");
		}
		
		$controller = array(
			'controller' => 'StaticController',
			'function'   => 'showPage',
			'params'     => array(
				'template' => '404'
			)
		);

		foreach(self::getUrls() as $url => $options) {
			if (preg_match('!^' . $url . '$!i', $uri, $matches)) {
				$controller["controller"] = (isset($options["controller"])) ? $options["controller"] : null;
				$controller["function"]   = (isset($options["function"])) ? $options["function"] : null;
				$controller["parameter"]  = (isset($options["parameter"])) ? $options["parameter"] : null;

				if(isset($options["params"])) {
				     foreach($options["params"] as $var => $value) {
				          $_GET[$var] = $value;
				     }
				}

				if(isset($options["urlParams"])) {
					foreach($options["urlParams"] as $i => $var) {
						$_GET[$var] = $matches[$i+1];
					}
				}

				break;
			}
		}

		return $controller;
	}

	/**
	 * Parse urls, and create array.
	 * @access public
	 * @static
	 * @return array
	 */
	public static function getUrls()
	{
		eval('$urls = array(' . file_get_contents(DIR_CONFIG . '/urls.cfg') . ');');

		return $urls;
	}
}
?>