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
 * Interface for View implementations
 * @package Core
 * @subpackage View
 * @category Interface
 * @author kovacsricsi
 */
namespace Core\View;

interface IView
{
	 /**
	  * Get template of view.
	  * @access public
	  * @return string
	  */
	public function getTemplate();
	
	/**
	 * Returns output.
	 * @access public
	 * @return string
	 */
	public function getOutput();
}
?>