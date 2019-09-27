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
 * Simple implementation of View.
 * @package Core
 * @subpackage View
 * @author kovacsricsi
 */
namespace lisa_core\View;

class Simple implements \lisa_core_api\IView
{
	/**
	 * Output string.
	 * @access protcted
	 * @var string
	 */
	protected $_out;

	/**
	 * Inicialize View class.
	 * @access public
	 * @static
	 * @return \lisa_core_api\IView
	 */
    public static function getInstance() {
		return new self();
	}

	/**
	 * Constructor sets default variables.
	 * @access private
	 * @return void
	 */
	private function __construct() {}
	
	/**
	 * Set new content to output.
	 * @access public
	 * @param string $out
	 * @return void
	 */
	public function setContent($out)
	{
		$this->_out = $out;
	}
	
	/**
	 * Concat  content with output.
	 * @access public
	 * @param string $out
	 * @return void
	 */
	public function addContent($out)
	{
		$this->_out .= $out;
	}
	
	/**
	 * Clear content.
	 * @access public
	 * @return void
	 */
	public function clearContent()
	{
		$this->_out = "";
	}
	
	 /**
	  * Get template of view.
	  * @access public
	  * @return string
	  */
	public function getTemplate()
	{
		return null;
	}
	
	/**
	 * Returns current content.
	 * @access public
	 * @return string
	 */
	public function getContent()
	{
		return $this->_out;
	}
}
?>