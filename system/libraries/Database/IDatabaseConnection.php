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
 * IDatabaseConnection interface to database connection
 * @package Core
 * @subpackage Database
 * @category Interface
 * @author kovacsricsi
 */
namespace Core\Database;

interface IDatabaseConnection
{
	/**
	 * Returns all row from database.
	 * @access public
	 * @param string $query
	 * @param array $prepare
	 * @return array
	 */
	public function getAll($query, &$prepare = null);

	/**
	 * Returns one row from database.
	 * @access public
	 * @param string $query
	 * @param array $prepare
	 * @return array
	 */
	public function getOne($query, &$prepare = null);

	/**
	 * Execute query.
	 * @access public
	 * @param string $query
	 * @param array $prepare
	 * @return void
	 */
	public function execute($query, &$prepare = null);
}
?>
