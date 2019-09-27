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
 * Interface for ControllerFactory classes.
 * @package Core
 * @subpackage API
 * @category Interface
 * @author kovacsricsi
 */
namespace lisa_core_api;

interface IControllerFactory {
	
	/**
	 * Create a new Controller class.
	 * @access public
	 * @param string $parameter
	 * @param string $function
	 * @return \Common\AController
	 */
	public function initController($parameter = null, $function = null);
}
?>
