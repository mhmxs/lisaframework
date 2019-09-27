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
 * Helper for <title> tags.
 * @package Core
 * @subpackage View.Helper
 * @category Helper
 * @author kovacsricsi
 */
namespace lisa_core_api\HttpHelper;

class Title extends \lisa_core_api\AHTMLElement
{
	/**
	 * text of title
	 * @access proetcted
	 * @var string
	 */
	protected $_text;

    /**
     * Constructor.
     * @access public
     * @param string $text The title string.
     * @return void
     */
    public function __construct($text)
    {
		$this->_text = (string)$text;
    }

	/**
	 * Returns with helper string.
	 * @access public
	 * @return string
	 */
	public function getHelper()
	{
		return '<title>' . htmlspecialchars($this->_text) . '</title>';
	}
    
	/**
	 * Returns with title string.
	 * @access public
	 * @return string
	 */
	public function getTitle()
	{
		return $this->_text;
	}
}
