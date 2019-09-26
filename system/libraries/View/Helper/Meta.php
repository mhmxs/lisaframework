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
 * Helper for meta tags.
 * @package Core
 * @subpackage View.Helper
 * @category Helper
 * @author kovacsricsi
 */
namespace Core\View\Helper;

class Meta extends Helper
{
    /**
     * Constructor.
     * @access public
     * @param string $attribs The specification array, typically
     * with keys 'name' or 'http-equiv', and 'content'.
     * @return void
     */
    public function __construct($attribs)
    {
		settype($attribs, 'array');

		$this->_attributes = $attribs;
    }

	/**
	 * Returns with helper string.
	 * @access public
	 * @return string
	 */
	public function getHelper()
	{
		return '<meta' . $this->_createAttributes() . '/>';
	}

}
