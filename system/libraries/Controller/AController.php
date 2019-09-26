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
 * AController abstract controller classes
 * @package Core
 * @subpackage Controller
 * @category Abstract
 * @author kovacsricsi
 */
namespace Core\Controller;

abstract class AController
{
	/**
	 * Running parameter for the controller.
	 * @access protected
	 * @var string
	 */
	protected $_parameter;

	/**
	 * Constructor.
	 * @access public
	 * @param string $parameter
	 * @param string $function
	 * @return void
	 */
	abstract public function __construct($parameter = null, $function = null);

	/**
	 * Send output.
	 * @access protected
	 * @param IHeader $header
	 * @return void
	 */
	abstract protected function _output(\Core\HTTP\Header\IHeader $header);
}
?>