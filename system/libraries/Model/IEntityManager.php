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
 * IEntityManager model interface.
 * @package Core
 * @subpackage Model
 * @category Interface
 * @author kovacsricsi
 */
namespace Core\Model;

interface IEntityManager
{
	/**
	 * Returns entitis id by parameters.
	 * @access public
	 * @param mixed $where array or string
	 * @param mixed $order array or string
	 * @param mixed $limit array or string
	 * @param mixed $group array or string
	 * @return void
	 */
	public function getAll($where = array(), $order = array(), $limit = array(), $group = array());

	/**
	 * Returns with first entity whitch matched to $where and $oder pattern.
	 * @access pulic
	 * @param mixed $where array or string
	 * @param mixed $order array or string
	 * @param integer $limit
	 * @return AEntity
	 */
	public function getEntity($where = array(), $order = array(), $limit = 0);

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
	 * @throws EntityManagerException
	 * @return mixed
	 */
	public function create(array $data);

	/**
	 * Modify entity in database.
	 * @access public
	 * @param mixed $primaryKey
	 * @param array $data
	 * @throws EntityManagerException
	 * @return void
	 */
	public function modify($primaryKey, array $data);

	/**
	 * Modify entities in database.
	 * @access public
	 * @param array $data
	 * @param mixed $where array or string
	 * @param mixed $order array or string
	 * @param mixed $limit array or string
	 * @param mixed $group array or string
	 * @throws EntityManagerException
	 * @return void
	 */
	public function modifyAll(array $data, $where = array(), $order = array(), $limit = array(), $group = array());

	/**
	 * Delete entity from database.
	 * @access public
	 * @param mixed $primaryKey
	 * @throws EntityManagerException
	 * @return void
	 */
	public function delete($primaryKey);

	/**
	 * Delete entities from database.
	 * @access public
	 * @param mixed $where array or string
	 * @param mixed $order array or string
	 * @param mixed $limit array or string
	 * @param mixed $group array or string
	 * @throws EntityManagerException
	 * @return integer
	 */
	public function deleteAll($where = array(), $order = array(), $limit = array(), $group = array());

	/**
	 * Returns the max number of results.
	 * @access public
	 * @param mixed $where array or string
	 * @return integer
	 */
	public function countMax($where = array(), $order = array(), $limit = array(), $group = array());
}
?>