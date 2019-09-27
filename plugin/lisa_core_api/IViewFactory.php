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
 * Interface for ViewFactory classes.
 * @package Core
 * @subpackage API
 * @category Interface
 * @author kovacsricsi
 */
namespace lisa_core_api;

interface IViewFactory {
	/**
	 * Inicialize View class.
	 * @access public
	 * @return \lisa_core_api\IView
	 */
    public function initView();
}
?>
