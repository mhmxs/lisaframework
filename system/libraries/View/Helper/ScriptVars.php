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
 * Helper for script vars.
 * @package Core
 * @subpackage View.Helper
 * @category Helper
 * @author Somlyai Dezs
 */
namespace Core\View\Helper;

class ScriptVars extends Helper
{

	protected $_vars = array();

    /**
     * Constructor.
     * @access public
     * @param array $data
     * @param array $attribs Additional attributes for the <script> tag.
     * @return void
     */
    public function __construct(array $data = array(), $attribs = null)
    {
        settype($attribs, 'array');

        if (empty($attribs["type"])) {
            $attribs["type"] = 'text/javascript';
        }

		$this->_attributes = $attribs;

		foreach($data as $name=>$value) {
			$this->_vars[] = ( is_array($value) ) ? 'var ' . $name . ' = ' . json_encode($value) . ';' : 'var ' . $name . ' = "' . $value .'";';
		}
    }

	/**
	 * Returns with helper string.
	 * @access public
	 * @return string
	 */
	public function getHelper()
	{
        return "<script" . $this->_createAttributes() . ">" . join("\r\n",$this->_vars) . "</script>";
	}
}
