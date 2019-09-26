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
 * Core class. Initialises the basic functions of the framework after the bootstrap procedure. Run Runner usually HTTPRunner.
 * @package Core
 * @category Core
 * @author nullstring
 */
namespace Core;

class Core
{
	/**
     * Session handler.
     * @access private
     * @var SessionHandler
     */
    private $_sessionHandler;

	/**
	 * Factory method whit LISA session handler
	 * @access public
	 * @static
	 * @return HTTPRunner
	 */
	public static function useSessionHandler()
	{
		return new self(true);
	}

	/**
	 * Factory method whitout LISA session handler
	 * @access public
	 * @static
	 * @return HTTPRunner
	 */
	public static function noSessionHandler()
	{
		return new self(false);
	}

	/**
	 * Constructor. MUST run the pre_core plugins.
	 * @access private
	 * @param boolean $useSesionHandler
	 * @throws \Exception
	 * @return void
	 */
	private function __construct($useSesionHandler)
	{
        if (!is_dir(DIR_ROOT . "/tmp")) {
            die503("Missing tmp directory, please create cache, logs, sessions in tmp directory, sample is in the package!");
        }

		$this->_sessionHandler = ($useSesionHandler == true) ? new \Core\Session\Handler() : null;

		Runner\Filter::runPreFilters()->run();

		try {
			$controller = \Core\Router\Router::getController();

			$controllerClass = "\\Controller\\" . $controller["controller"];
			new $controllerClass($controller["parameter"], $controller["function"]);
		} catch (\Exception $e) {
			die503("Controller failed to load");
		}
	}

	/**
	 * Destructor. MUST run the post_core plugins.
	 * @public
	 * @return void
	 */
	public function __destruct()
	{
		if ($this->_sessionHandler instanceof \Core\Session\Handler) {
			$this->_sessionHandler->writeSession();
		}

		Runner\Filter::runPostFilters()->run();
	}
}
?>