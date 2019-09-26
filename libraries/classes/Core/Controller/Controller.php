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
class Controller extends AController
{
	/**
	 * Header file
	 * @access protected
	 * @var string
	 */
	protected $_header;

	/**
	 * Constructor.
	 * @access public
	 * @param string $parameter
	 * @param string $function
	 * @return void
	 */
	public function __construct($parameter = null, $function = null)
	{
		if ($this->_view === null) {
			$this->_view = new View(DIR_TEMPLATES);
		}

		$this->_header    = new HeaderHtml();
		$this->_parameter = $parameter;

		if ($function) {
			$this->$function();
		}

		$this->_output($this->_header);
	}

	/**
	 * Send output.
	 * @access protected
	 * @param IHeader $header
	 * @return void
	 */
	protected function _output(IHeader $header)
	{
		HTTP::sendOutput($this->_view, $header);
	}
}
?>
