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
 * Pasing uri by static array from /config/urls.cfg.
 * @package Core
 * @subpackage Router.URIHandler
 * @author kovacsricsi
 */
namespace Core\Router\URIHandler;

class StaticArray implements IURIHandler
{	/**
	 * Read controller and parameters from Router class.
	 * @access public
	 * @return array
	 */
	public function getController()
	{
		$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

		if (strlen($uri) > 1) {
			$uri = rtrim($uri, "/");
		}

		$controller = array(
			'controller' => 'StaticController',
			'function'   => 'showPage',
			'parameter'  => null,
			'params'     => array(
				'template' => '404'
			)
		);

		foreach($this->_getUrls() as $url => $options) {
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
	 * @access protected
	 * @return array
	 */
	protected function _getUrls()
	{
		eval('$urls = array(' . file_get_contents(DIR_CONFIG . '/urls.cfg') . ');');

		return $urls;
	}
}
?>