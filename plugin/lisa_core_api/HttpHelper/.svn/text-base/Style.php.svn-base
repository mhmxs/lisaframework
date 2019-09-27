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
 * Helper for <link> tags.
 * @package Core
 * @subpackage View.Helper
 * @category Helper
 * @author kovacsricsi
 */
namespace lisa_core_api\HttpHelper;

class Style extends \lisa_core_api\AHTMLElement
{
    /**
     * Constructor.
     *
     * Adds "media" attribute if not specified, and always uses
     * "type" attribute of "text/css".
     * @access public
     * @param string $href The source href for the stylesheet.
     * @param array $attribs Additional attributes for the <style> tag.
     * @return void
     */
    public function __construct($href, $attribs = null)
    {
        settype($attribs, 'array');

		$attribs["href"] = $href;
        $attribs["type"] = 'text/css';
		$attribs["rel"]  = 'stylesheet';

        // default to media="screen"
        if (empty($attribs["media"])) {
            $attribs["media"] = 'screen';
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
        // build and return the tag rel="stylesheet" href="style.css" type="text/css"
        return '<link' . $this->_createAttributes() . '/>';
	}
}
