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
 * File header
 * @package Core
 * @subpackage HTTP.Header
 * @author kovacsricsi
 */
namespace Core\HTTP\Header;

class Json implements IHeader
{
	/**
	 * Array of headers lines.
	 * @access protected
	 * @var array
	 */
	protected $_headers;

	/**
	 * Constructor sets default variables.
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->_headers = array(
			"Pragma: no-cache",
			"Expires: Mon, 26 Jul 1997 05:00:00 GMT",
			"Cache-Control: no-cache, must-revalidate",
			"Content-Type: application/json"
		);
	}

	/**
	 * Returns with array of headers.
	 * @access public
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}
}