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
 * Controller implements basic funcionality of a controller
 * @package Core
 * @subpackage Controller
 * @category Controller
 * @author kovacsricsi
 */
namespace Core\Controller;

use Util;

class Controller extends AController
{
	/**
	 * View for the controller.
	 * @access protected
	 * @var IView
	 */
	protected $_view = null;

	/**
	 * Model for the controller.
	 * @access protected
	 * @var IEntityManager
	 */
	protected $_model = null;

	/**
	 * Header file.
	 * @access protected
	 * @var string
	 */
	protected $_header;

	/**
	 * Send output or not. Default is send.
	 * @access protected
	 * @var boolean
	 */
	protected $_sendOutput = true;

	/**
	 * Post superglobal for the controller
	 * @access protected
	 * @var \Core\HTTP\Superglobal\Post
	 */
	protected $_post;

	/**
	 * Get superglobal for the controller
	 * @access protected
	 * @var \Core\HTTP\Superglobal\Get
	 */
	protected $_get;

	/**
	 * Session superglobal for the controller
	 * @access protected
	 * @var \Core\HTTP\Superglobal\Session
	 */
	protected $_session;

	/**
	 * Files superglobal for the controller
	 * @access protected
	 * @var \Core\HTTP\Superglobal\Files
	 */
	protected $_files;

	/**
	 * Cookie superglobal for the controller
	 * @access protected
	 * @var \Core\HTTP\Superglobal\Cookie
	 */
	protected $_cookie;

	/**
	 * server superglobal for the controller
	 * @access protected
	 * @var \Core\HTTP\Superglobal\Server
	 */
	protected $_server;

	/**
	 * Constructor.
	 * @access public
	 * @param string $parameter
	 * @param string $function
	 * @return void
	 */
	public function __construct($parameter = null, $function = null)
	{
		$this->_post    = new \Core\HTTP\Superglobal\Post();
		$this->_get     = new \Core\HTTP\Superglobal\Get();
		$this->_session = new \Core\HTTP\Superglobal\Session();
		$this->_files   = new \Core\HTTP\Superglobal\Files();
		$this->_cookie  = new \Core\HTTP\Superglobal\Cookie();
		$this->_server  = new \Core\HTTP\Superglobal\Server();

		if ($this->_view === null) {
			$reader = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Config.ini");
			$view = "\Core\View\\" . $reader->VIEW->template_engine;
			$this->_view = new $view(DIR_TEMPLATES);
		}


		$this->_header    = new \Core\HTTP\Header\Html();
		$this->_parameter = $parameter;

		if ($function) {
			$this->$function();
		}

		if ($this->_sendOutput === true) {
			$this->_output($this->_header);
		}
	}

	/**
	 * Forward request to another Controller.
	 * @access protected
	 * @param string $controller
	 * @param string $function
	 * @return void
	 */
	protected function _forward($controller, $function = null)
	{
		$this->_sendOutput = false;
		$controllerClass = "\\Controller\\" . $controller;
		new $controllerClass($this->_parameter, $function);
	}

	/**
	 * Send output.
	 * @access protected
	 * @param IHeader $header
	 * @return void
	 */
	protected function _output(\Core\HTTP\Header\IHeader $header)
	{
		\Core\HTTP\HTTP::sendOutput($this->_view, $header);
	}
}
?>
