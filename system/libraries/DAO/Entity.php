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
 * Data Access Object default Entity
 * @package Core
 * @subpackage DAO
 * @author kovacsricsi
 */
namespace Core\DAO;

class Entity {
	/**
     * Data of entity.
     * @access protected
     * @var array
     */
	protected $_data;

	/**
	 * Constructor.
	 * @access public
	 * @param array $data
	 * @return void
	 */
	public function __construct(array $data = array()) {
		$this->_data = $data;
	}

	/**
	 * __get
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function __get($key)
	{
		if (isset($this->_data[$key])) {
			return $this->_data[$key];
		} else {
			return null;
		}
	}

	/**
	 * __set
	 * @access public
	 * @param string $key
	 * @param string $value
	 * @throws EntityException
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->_data[$key] = $value;
	}
}
?>
