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
 * Helper for <script> tags.
 * @package Core
 * @subpackage View
 * @category Helper
 * @author kovacsricsi
 */
class Script extends Helper
{
    /**
     * Constructor.
     * @access public
     * @param string $src The source href for the script.
     * @param array $attribs Additional attributes for the <script> tag.
     * @return void
     */
    public function __construct($src, $attribs = null)
    {
        settype($attribs, 'array');

        $attribs["src"] = $src;

        if (empty($attribs["type"])) {
            $attribs["type"] = 'text/javascript';
        }

		$this->_attributes = $attribs;
    }

	/**
	 * Returns with helper string.
	 * @access public
	 * @return string
	 */
	public function getHelper()
	{
        return "<script" . $this->_createAttributes() . "></script>";
	}
}
