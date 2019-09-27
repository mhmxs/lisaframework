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
 * HTTP headers interface
 * @package Core
 * @subpackage API
 * @category Interface
 * @author kovacsricsi
 */
namespace lisa_core_api\HttpHeader;

interface IHeader
{
	/**
	 * Returns with array of headers.
	 * @access public
	 * @return array
	 */
	public function getHeaders();
}
?>