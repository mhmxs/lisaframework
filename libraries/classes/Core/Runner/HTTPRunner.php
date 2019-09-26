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
 * HTTPRunner run HTTP request.
 * @package Core
 * @subpackage Runner
 * @category Runner
 * @author kovacsricsi
 */

class HTTPRunner implements IRunner
{
	/**
     * Session handler.
     * @access protected
     * @var SessionHandler
     */
    protected $_sessionHandler;

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
	 * Contructor.
	 * @access protected
	 * @param boolean $useSesionHandler
	 * @return void
	 */
	protected function __construct($useSesionHandler)
	{
		if ($useSesionHandler == true) {
			$this->_sessionHandler = new SessionHandler();
		} else {
			$this->_sessionHandler = null;
		}
	}

	/**
	 * Run function - Loads the corresponnding controller and set X-System header.
	 * @access public
	 * @return void
	 */
	public function run()
	{
		try {
			$controller = Router::getController();

			$parameter = isset($controller["parameter"]) ? $controller["parameter"] : null;
			$function  = isset($controller["function"]) ? $controller["function"] : null;

			new $controller["controller"]($parameter, $function);
		} catch (Exception $e) {
			HTTP::sendHeader("HTTP/1.1 503 Server Unavailable", true, 503);
			HTTP::write("<h1>503 error!</h1>");
		}
	}

	/**
	 * Destructor
	 * @access public
	 * @return void
	 */
	public function __destruct()
	{
		if ($this->_sessionHandler instanceof SessionHandler) {
			$this->_sessionHandler->writeSession();
		}
	}
}
?>