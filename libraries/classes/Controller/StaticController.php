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
 * StaticController is controller for static pages
 * @package Controller
 * @category Controller
 * @author kovacsricsi
 */

class StaticController extends Controller
{
	/**
	 * Show static page.
	 * @access public
	 * @return void
	 */
	public function showPage()
	{
		$this->_view->setLayout("default.html");
		$this->_view->setTemplate(isset($_GET["template"]) ? "static/" . $_GET["template"] . ".html" : "404.html");
	}

	/**
	 * Show template preview form webroot/templates/static/preview directory.
	 * @access public
	 * @return void
	 */
	public function showTemplate()
	{
		$this->_view->setTemplate(isset($_GET["template"]) ? "static/preview/" . $_GET["template"] . ".html" : "404.html");
	}
}
?>