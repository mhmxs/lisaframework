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
 * IModel model interface.
 * @package Core
 * @subpackage Entity
 * @category Interface
 * @author kovacsricsi
 */

interface IModel
{
	/**
	 * Returns entitis id by parameters.
	 * @access public
	 * @param array $where
	 * @param array $order
	 * @param array $limit
	 * @return void
	 */
	public function getAll(array $where = array(), array $order = array(), array $limit = array());

	/**
	 * Returns with first entity whitch matched to $where and $oder pattern.
	 * @access pulic
	 * @param array $where
	 * @param array $order
	 * @param integer $limit
	 * @return AEntity
	 */
	public function getEntity(array $where = array(),array $order = array(), $limit = 0);

	/**
	 * Returns entity by primary key from database by parameters.
	 * @access public
	 * @param array $primaryKey
	 * @return AEntity
	 */
	public function getOne($primaryKey);

	/**
	 * Returns new empty entity.
	 * @access public
	 * @return AEntity
	 */
	public function getNew();

	/**
	 * Create new entity in database, and returns with new primary key.
	 * @access public
	 * @param array $data
	 * @throws ModelException
	 * @return mixed
	 */
	public function create(array $data);

	/**
	 * Modify entity in database.
	 * @access public
	 * @param mixed $primaryKey
	 * @param array $data
	 * @throws ModelException
	 * @return void
	 */
	public function modify($primaryKey, array $data);

	/**
	 * Delete entity from database.
	 * @access public
	 * @param mixed $primaryKey
	 * @throws ModelException
	 * @return void
	 */
	public function delete($primaryKey);

	/**
	 * Returns the max number of results.
	 * @access public
	 * @param array $where
	 * @return integer
	 */
	public function countMax(array $where = array());
}
?>