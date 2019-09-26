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
 * simple implementation of View.
 * @package Core
 * @subpackage View
 * @author kovacsricsi
 */
namespace Core\View;

class Simple implements IView
{
	/**
	 * Output string.
	 * @access protcted
	 * @var string
	 */
	protected $_out;
	
	/**
	 * Constructor sets default variables.
	 * @access public
	 * @param string $out
	 * @return void
	 */
	public function __construct($out = "")															
	{
		$this->_out = $out;
	}
	
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
	 * Returns output.
	 * @access public
	 * @return string
	 */
	public function getOutput()
	{
		return $this->_out;
	}
}
?>