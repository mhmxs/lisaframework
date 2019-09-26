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
 * Represent $_POST superglobal in Controller.
 * @package Core
 * @subpackage HTTP.Superglobals
 * @author kovacsricsi
 */
namespace Core\HTTP\Superglobal;

class Post
{
	/**
	 * Retrieve a value and return null if there is no element set.
	 * @access public
	 * @param  string $name
	 * @return mixed
	 */
	public function __get($key) {
		if ( array_key_exists($key, $_POST) ) {
			return $_POST[$key];
		} else {
			return null;
		}
	}

	/**
	 * Set a value
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$_POST[$key] = $value;
	}

	/**
	 * Get the POST data array
	 * @return array
	 */
	public function toArray()
	{
		return $_POST;
	}
}
?>