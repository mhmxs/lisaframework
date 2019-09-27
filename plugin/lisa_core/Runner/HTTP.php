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
 * HTTP runner process HTTP request.
 * @package Core
 * @subpackage Runner
 * @author kovacsricsi
 */

namespace lisa_core\Runner;

class HTTP implements IRunner {

	/**
	 * Staert HTTP request processing.
	 * @access public
	 * @return void
	 */
	public function run() {
		$Reader = \lisa_util\Config\Cache::getConfig(DIR_ROOT . "/config/Config.ini");

		$router = \Context::getService($Reader->ROUTER->service);

		if ($router == null || !($router instanceof \lisa_core_api\IRouter)) {
			throw new \lisa_core\RouterException("Invalid Router class, it doesn't imlements IRouter!");
		}

		$controllerData = $router->getController();
		$layout = DIR_ROOT . "/layout/" . $controllerData["layout"] . ".php";

		if (!isset($controllerData["layout"]) || !file_exists($layout)) {
			throw new \lisa_core\PagenotFoundException();
		} else {
			if (isset($controllerData["view"])) {
				\Context::setView(\Context::getService($controllerData["view"]));
			}
			
			include_once($layout);
			
			if (\Context::getView()) {//TODO klálniita hogy leseene header tbeállítani
				\Context::getService("Http")->sendOutput(\Context::getView());
			}
		}
	}

}

?>