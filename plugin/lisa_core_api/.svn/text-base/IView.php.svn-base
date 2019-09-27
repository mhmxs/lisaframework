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
 * @subpackage API
 * @category Interface
 * @author kovacsricsi
 */
namespace lisa_core_api;

interface IView
{
	 /**
	  * Get template of view.
	  * @access public
	  * @return string
	  */
	public function getTemplate();
	
	/**
	 * Returns current content.
	 * @access public
	 * @return string
	 */
	public function getContent();
	
	/**
	 * Set new content to output.
	 * @access public
	 * @param string $out
	 * @return void
	 */
	public function setContent($out);
	
	/**
	 * Concat  content with output.
	 * @access public
	 * @param string $out
	 * @return void
	 */
	public function addContent($out);
	
	/**
	 * Clear content.
	 * @access public
	 * @return void
	 */
	public function clearContent();
}
?>