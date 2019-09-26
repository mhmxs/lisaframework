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
 * MySQL Model classe
 * @package Core
 * @subpackage Model
 * @author kovacsricsi
 */
class MySQLModel implements IModel
{
    /**
     * Name of database table.
     * @access protected
     * @var string
     */
    protected $_tableName;

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
	 * Factory method for Model.
	 * @access public
	 * @static
     * @param string $tableName
     * @throws ModelException
	 * @return Model
	 */
	public static function init($tableName, $entity = "MySQLEntity")
	{
		return new self($tableName, $entity);
	}

	/**
	 * Constructor.
	 * @access public
     * @param string $tableName
     * @param string $entity
     * @param string $dbType
     * @throws ModelException
	 * @return void
	 */
	public function __construct($tableName, $entity = "MySQLEntity")
	{
	    $this->_tableName = (string)$tableName;
		$this->_entity    = $entity;
	    $this->_columns   = QueryHandler::getAll("SHOW COLUMNS FROM " . $this->_tableName);

	    if ($this->_columns == false) {
	    	throw new ModelException("Invalid table name : " . $this->_tableName);
	    }

	    foreach($this->_columns as $column) {
			if (strtoupper($column["Key"]) == "PRI") {
				$this->_primaryKey = $column["Field"];
			}
	    }

	    if ($this->_primaryKey === null) {
	    	throw new ModelException("Primary key not found!");
	    }

	    $testEntity = new $entity(null, null, null);
	    if (!($testEntity instanceof AEntity)) {
	    	throw new ModelException("Entity not compatible with Model!");
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
	 * @param array $where
	 * @param array $order
	 * @param array $limit
	 * @return array
	 */
	public function getAll(array $where = array(), array $order = array(), array $limit = array())
	{
		$entities = array();

		$where[] = "1";

		$primaryKeys = $this->_selectAllEntityId($where, $order, $limit);

		foreach($primaryKeys as $primaryKey) {
			try {
				$entities[] = $this->getOne($primaryKey[$this->_primaryKey]);
			} catch (Exception $e) {
				BasicErrorHandler::trace($e->getMessage());
			}
		}

		return $entities;
	}

	/**
	 * Returns with first entity whitch matched to $where and $oder pattern.
	 * @access pulic
	 * @param array $where
	 * @param array $order
	 * @param integer $limit
	 * @return AEntity
	 */
	public function getEntity(array $where = array(),array $order = array(), $limit = 0)
	{
		$entity = null;

		$where[] = "1";

		$primaryKeys = $this->_selectAllEntityId($where, $order, array(abs((int)$limit), 1));

		if (count($primaryKeys) > 0) {
			try {
				$entity = $this->getOne($primaryKeys[0][$this->_primaryKey]);
			} catch (Exception $e) {
				BasicErrorHandler::trace($e->getMessage());
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
		return new $entity($this->_tableName, $this->_columns, $this->_primaryKey, $primaryKey);
	}

	/**
	 * Returns new empty entity.
	 * @access public
	 * @return AEntity
	 */
	public function getNew()
	{
		$entity = $this->_entity;
		return new $entity($this->_tableName, $this->_columns, $this->_primaryKey);
	}

	/**
	 * Create new entity in database, and returns with new primary key.
	 * @access public
	 * @param array $data
	 * @throws ModelException
	 * @return mixed
	 */
	public function create(array $data)
	{
		try {
			$entity = $this->getNew();

			foreach($data as $key => $value) {
				$entity->$key = $value;
			}

			return $entity->commit();
		} catch (Exception $e) {
			throw new ModelException($e->getMessage());
		}
	}

	/**
	 * Modify entity in database.
	 * @access public
	 * @param mixed $primaryKey
	 * @param array $data
	 * @throws ModelException
	 * @return void
	 */
	public function modify($primaryKey, array $data)
	{
		try {
			$entity = $this->getOne($primaryKey);

			foreach($data as $key => $value) {
				$entity->$key = $value;
			}

			return $entity->commit();
		} catch (Exception $e) {
			throw new ModelException($e->getMessage());
		}
	}

	/**
	 * Delete entity from database.
	 * @access public
	 * @param mixed $primaryKey
	 * @throws ModelException
	 * @return void
	 */
	public function delete($primaryKey)
	{
		try {
			$entity = $this->getOne($primaryKey)->delete();
		} catch (Exception $e) {
			throw new ModelException($e->getMessage());
		}
	}

	/**
	 * Returns the max number of results.
	 * @access public
	 * @param array $where
	 * @return integer
	 */
	public function countMax(array $where = array())
	{
		$where[] = "1";

		$result = $this->_selectAllEntityId($where);

		return count($result);
	}

	/**
	 * Returns with entities primary keys.
	 * @access protected
	 * @param array $where
	 * @param array $order
	 * @param array $limit
	 * @return array
	 */
	protected function _selectAllEntityId(array $where = array(), array $order = array(), array $limit = array())
	{
	   $query = "SELECT `" . $this->_primaryKey . "` FROM " . $this->_tableName;

		$query .= " WHERE (" . join(") AND (", $where) . ")" ;

		if ($order == true) {
			$query .= " ORDER BY " . join(",", $order);
		}

		if ($limit == true) {
			$query .= " LIMIT " . join(",", $limit);
		}

		return QueryHandler::getAll($query);
	}
}

?>
