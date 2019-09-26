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
namespace Core\Router;

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
		throw new \Exception("Illegal operation, this class is only static class!");
	}

	/**
	 * Read controller and parameters from Router class.
	 * @access public
	 * @static
	 * @return array
	 */
	public static function getController()
	{
		$Reader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Config.ini");

		$uriHandlerClass = "\\Core\\Router\\URIHandler\\" . $Reader->URI->handler;
		$uriHandler      = new $uriHandlerClass();
		
		if (!($uriHandler instanceof \Core\Router\URIHandler\IURIHandler)) {
			throw new RouterException("Invalid URI handler class, it doesn't imlements IURIHandler!");
		}

		return $uriHandler->getController();
	}
}
?>