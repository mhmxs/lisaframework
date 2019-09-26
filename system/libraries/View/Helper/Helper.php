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
 * Abstract Helper for helpers
 * @package Core
 * @subpackage View.Helper
 * @category Abstract
 * @author kovacsricsi
 */
namespace Core\View\Helper;

abstract class Helper
{
	/**
	 * Attributes for heleper.
	 * @access protected
	 * @var array
	 */
	 protected $_attributes = array();

	/**
	 * Create attribute string for herlper.
	 * @access protected
	 * @return string
	 */
	protected function _createAttributes()
	{
		$attributes = array();

		foreach($this->_attributes as $name => $value) {
			$attributes[] = $name . '="' . $value . '"';
		}

		return " " . join(" ", $attributes) . " ";
	}

	/**
	 * Returns with helper string.
	 * @access public
	 * @return string
	 */
	abstract public function getHelper();
}

?>