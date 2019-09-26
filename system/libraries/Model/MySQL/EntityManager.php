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
 * MySQL EntityManager class.
 * @package Core
 * @subpackage Model.MySQL
 * @author kovacsricsi
 */
namespace Core\Model\MySQL;

class EntityManager implements \Core\Model\IEntityManager
{

	/**
	 * Instance of Model.
	 * @access protected
	 * @staticvar self
	 */
	protected static $_instance = null;

	/**
	 * Name of database table.
	 * @access protected
	 * @var string
	 */
	protected $_tableName;

	/**
	 * Name of database connection.
	 * @access protected
	 * @var string
	 */
	protected $_connectionName;

	/**
	 * List of fields name.
	 * @access protected
	 * @var array
	 */
	protected $_columns;

	/**
	 * Primary key of entities.
	 * @access protected
	 * @var string
	 */
	protected $_primaryKey = null;

	/**
	 * Name of Entity class, if developer want to use own entity
	 * @access protected
	 * @var string
	 */
	protected $_entity;

	/**
	 * Returns with instance of model.
	 * @access public
	 * @static
	 * @param string $tableName
	 * @param string $entity
	 * @param string $connectionName
	 * @return IEntityManager
	 */
	public static function getInstance($tableName, $entity, $connectionName)
				{
		if ( !isset(static::$_instance[$tableName][$entity][$connectionName]) ) {
			$class = get_called_class();
			static::$_instance[$tableName][$entity][$connectionName] = new $class($tableName, $entity, $connectionName);
		}

		return static::$_instance[$tableName][$entity][$connectionName];

	}

	/**
	 * Factory method for Model.
	 * @access public
	 * @static
	 * @param string $tableName
	 * @param string $entity
	 * @param string $connectionName
	 * @throws EntityManagerException
	 * @return Model
	 */
	public static function init($tableName, $entity, $connectionName )
				{
		$class = get_called_class();
		return new $class($tableName, $entity, $connectionName);
	}

	/**
	 * Constructor.
	 * @access protected
	 * @param string $tableName
	 * @param string $entity
	 * @param string $connectionName
	 * @throws EntityManagerException
	 * @return void
	 */
	protected function __construct($tableName, $entity, $connectionName)
	{
		$this->_tableName		= (string)$tableName;
		$this->_entity			= $entity;
		$this->_connectionName	= $connectionName;
		$this->_columns			= \Core\Database\MySQL\DatabaseConnection::getConnection($this->_connectionName)->getAll("SHOW COLUMNS FROM " . $this->_tableName . ";");

		if ($this->_columns == false) {
			throw new \Core\Model\EntityManagerException("Invalid table name : " . $this->_tableName);
		}

		foreach($this->_columns as $column) {
			if (strtoupper($column["Key"]) == "PRI") {
				$this->_primaryKey = $column["Field"];
			}
		}

		if ($this->_primaryKey === null) {
			throw new \Core\Model\EntityManagerException("Primary key not found!");
		}

		if (!(new $entity(null, array(), null) instanceof \Core\Model\AEntity)) {
			throw new \Core\Model\EntityManagerException("Entity not compatible with Model!");
		}
	}

	/**
	 * Returns with name of the table the modell works with.
	 * @access public
	 * @return string
	 */
	public function getTableName()
	{
		return $this->_tableName;
	}

	/**
	 * Returns with columns.
	 * @access public
	 * @return array
	 */
	public function getColumns()
	{
		return $this->_columns;
	}

	/**
	 * Returns with primary key.
	 * @access public
	 * @return string
	 */
	public function getPrimaryKey()
	{
		return $this->_primaryKey;
	}

	/**
	 * Returns with entities.
	 * @access public
	 * @param mixed $where
	 * @param mixed $order
	 * @param mixed $limit
	 * @param mixed $group
	 * @param mixed $join
	 * @return array
	 */
	public function getAll($where = array(), $order = array(), $limit = array(), $group = array(), $join = array())
	{
		settype($where, "array");
		settype($order, "array");
		settype($limit, "array");
		settype($group, "array");
		settype($join, "array");

		$entities = array();

		$where[] = "1";

		$entityData = $this->_selectAllEntities($this->_buildQuery($where, $order, $limit, $group, $join));

		foreach($entityData as $i => $data) {
			$entities[$i] = $this->getNew();
			foreach($data as $k => $v) {
				$entities[$i]->$k = $v;
			}
		}

		return $entities;
	}

	/**
	 * Returns with first entity whitch matched to $where and $oder pattern.
	 * @access pulic
	 * @param mixed $where
	 * @param mixed $order
	 * @param integer $limit
	 * @param mixed $join
	 * @return AEntity
	 */
	public function getEntity($where = array(), $order = array(), $limit = 0, $join = array())
	{
		settype($where, "array");
		settype($order, "array");
		settype($join, "array");

		$entity = null;

		$where[] = "1";

		$entityData = $this->_selectAllEntities($this->_buildQuery($where, $order, array(abs((int)$limit), 1), array(), $join));

		if (count($entityData) > 0) {
			$entity = $this->getNew();
			foreach($entityData[0] as $k => $v) {
				$entity->$k = $v;
			}
		}

		return $entity;
	}

	/**
	 * Returns entity by primary key from database by parameters.
	 * @access public
	 * @param array $primaryKey
	 * @return AEntity
	 */
	public function getOne($primaryKey)
	{
		$entity = $this->_entity;
		return new $entity($this->_tableName, $this->_columns, $this->_primaryKey, $primaryKey, $this->_connectionName);
	}

	/**
	 * Returns new empty entity.
	 * @access public
	 * @return AEntity
	 */
	public function getNew()
	{
		$entity = $this->_entity;
		return new $entity($this->_tableName, $this->_columns, $this->_primaryKey, null, $this->_connectionName);
	}

	/**
	 * Create new entity in database, and returns with new primary key.
	 * @access public
	 * @param array $data
	 * @throws EntityManagerException
	 * @return mixed
	 */
	public function create(array $data)
	{
		$entity = $this->getNew();

		foreach($data as $key => $value) {
			$entity->$key = $value;
		}

		return $entity->commit();
	}

	/**
	 * Modify entity in database.
	 * @access public
	 * @param mixed $primaryKey
	 * @param array $data
	 * @throws EntityManagerException
	 * @return void
	 */
	public function modify($primaryKey, array $data)
	{
		$entity = $this->getOne($primaryKey);

		foreach($data as $key => $value) {
			$entity->$key = $value;
		}

		return $entity->commit();
	}

	/**
	 * Modify entities in database.
	 * @access public
	 * @param array $data
	 * @param mixed $where
	 * @param mixed $order
	 * @param mixed $limit
	 * @param mixed $group
	 * @param mixed $join
	 * @throws EntityManagerException
	 * @return void
	 */
	public function modifyAll(array $data, $where = array(), $order = array(), $limit = array(), $group = array(), $join = array())
	{
		settype($where, "array");
		settype($order, "array");
		settype($limit, "array");
		settype($group, "array");
		settype($join, "array");

		$entities = array();

		try {
			foreach ($this->getAll($where, $order, $limit, $group, $join) as $entity) {
				foreach($data as $key => $value) {
					$entity->$key = $value;
				}
				$entities[] = $entity->commit();
			}

			return $entities;
		} catch (\Exception $e) {
			throw new \Core\Model\EntityManagerException($e->getMessage());
		}
	}

	/**
	 * Delete entity from database.
	 * @access public
	 * @param mixed $primaryKey
	 * @throws EntityManagerException
	 * @return void
	 */
	public function delete($primaryKey)
	{
		$entity = $this->getOne($primaryKey)->delete();
	}

	/**
	 * Delete entities from database.
	 * @access public
	 * @param mixed $where
	 * @param mixed $order
	 * @param mixed $limit
	 * @param mixed $group
	 * @param mixed $join
	 * @throws EntityManagerException
	 * @return integer
	 */
	public function deleteAll($where = array(), $order = array(), $limit = array(), $group = array(), $join = array())
	{
		settype($where, "array");
		settype($order, "array");
		settype($limit, "array");
		settype($group, "array");
		settype($join, "array");

		try {
			$entities = $this->getAll($where, $order, $limit, $group, $join);
			foreach ($entities as $entity) {
				$entity->delete();
			}

			return count($entities);
		} catch (\Exception $e) {
			throw new \Core\Model\EntityManagerException($e->getMessage());
		}
	}

	/**
	 * Returns the max number of results.
	 * @access public
	 * @deprecated use count()
	 * @param mixed $where
	 * @param mixed $order
	 * @param mixed $limit
	 * @param mixed $group
	 * @param mixed $join
	 * @return integer
	 */
	public function countMax($where = array(), $order = array(), $limit = array(), $group = array(), $join = array())
	{
		return $this->count($where, $order, $limit, $group, $join = array());
	}

	/**
	 * Returns the max number of results.
	 * @access public
	 * @param mixed $where
	 * @param mixed $order
	 * @param mixed $limit
	 * @param mixed $group
	 * @param mixed $join
	 * @return integer
	 */
	public function count($where = array(), $order = array(), $limit = array(), $group = array(), $join = array())
	{
		settype($where, "array");
		settype($order, "array");
		settype($limit, "array");
		settype($group, "array");
		settype($join, "array");
		$where[] = "1";

		$query = $this->_buildQuery($where, $order, $limit, $group, $join, "COUNT(1)");

		$result = \Core\Database\MySQL\DatabaseConnection::getConnection($this->_connectionName)->getOne($query);
		return $result["COUNT(1)"];
	}

	/**
	 * Returns with entities data.
	 * @access protected
	 * @param string $query
	 * @return array
	 */
	protected function _selectAllEntities($query)
	{
		return \Core\Database\MySQL\DatabaseConnection::getConnection($this->_connectionName)->getAll($query);
	}

	/**
	 * Returns Query string.
	 * @access protected
	 * @param array $where
	 * @param array $order
	 * @param array $limit
	 * @param array $group
	 * @param array $join
	 * @param string $select default: *
	 * @return string
	 */
	protected function _buildQuery($where = array(), $order = array(), $limit = array(), $group = array(), $join = array(), $select = "*") {
		$query = "SELECT " . ($select == "COUNT(1)" ? $select : $this->_tableName  . ".*") . " FROM " . $this->_tableName;

		if (!empty($join)) {
			foreach ($join as $aJoinTableName => $aJoinWhere) {
				if (!\Util\Validate::isEmpty($aJoinWhere)) {
					$aJoinWhere = (array) $aJoinWhere;
					$query .= " INNER JOIN `$aJoinTableName` ON ";
					$query .= "( (" . join(") AND (", $aJoinWhere) . ") )";
				}
			}
		}

		$query .= " WHERE (" . join(") AND (", $where) . ")";

		if ($group == true) {
			$query .= " GROUP BY " . join(",", $group);
		}

		if ($order == true) {
			$query .= " ORDER BY " . join(",", $order);
		}

		if ($limit == true) {
			$query .= " LIMIT " . join(",", $limit);
		}
		return $query;
	}
}

?>
