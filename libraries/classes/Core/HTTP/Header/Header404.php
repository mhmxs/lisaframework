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
 * 404 header
 * @package Core
 * @subpackage HTTP
 * @author kovacsricsi
 */
class Header404 implements IHeader
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
	 * @param string $charset
	 * @return void
	 */
	public function __construct($charset = null)
	{
		if (is_null($charset)) {
			$charset = CHARSET;
		}

		$this->_headers = array(
			"Content-Type: text/html; charset=" . strtolower($charset),
			"Pragma: private",
			"Expires: Mon, 26 Jul 1997 05:00:00 GMT",
			"Cache-Control: no-store, no-cache, pre-check=0, post-check=0, max-age=0, must-revalidate",
			"Pragma: no-cache",
			"HTTP/1.1 404 Not Found"
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
?>