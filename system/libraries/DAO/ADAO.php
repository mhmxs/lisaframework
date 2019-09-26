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
 * Data Access Object abstract
 * @package Core
 * @subpackage DAO
 * @author kovacsricsi
 */

namespace Core\DAO;

abstract class ADAO {

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
     * Primary key of entities with table name.
     * @access protected
     * @var string
     */
    protected $_fullPrimaryKey = null;
    /**
     * Name of Entity class, if developer want to use own entity
     * @access protected
     * @var string
     */
    protected $_entity;
    /**
     * Database connection.
     * @access protected
     * @var IDatabaseConnection
     */
    protected $_connection;

    /**
     * Returns with instance of model.
     * @access public
     * @static
     * @param string $tableName
     * @param string $entity
     * @param string $connectionName
     * @param string $pk
     * @return ADAO
     */
    public static function getInstance($tableName, $entity, $connectionName, $pk) {
        if (!isset(static::$_instance[$tableName][$entity][$connectionName])) {
            $class = get_called_class();
            static::$_instance[$tableName][$entity][$connectionName] = new $class($tableName, $entity, $connectionName, $pk);
        }

        return static::$_instance[$tableName][$entity][$connectionName];
    }

    /**
     * Constructor.
     * @access protected
     * @param string $tableName
     * @param string $entity
     * @param string $connectionName
     * @param string $pk
     * @throws Exception
     * @return void
     */
    protected function __construct($tableName, $entity, $connectionName, $pk) {
        $this->_tableName = (string) $tableName;
        $this->_entity = (string) $entity;
        $this->_connectionName = (string) $connectionName;

        $this->_setConnection();

        $this->_columns = $this->getColumns();

        if ($this->_columns == false) {
            throw new \Core\DAO\Exception("Invalid table name : " . $this->_tableName);
        }

        $this->_setPrimaryKey($pk);

        $this->_fullPrimaryKey = $this->_tableName . "." . $this->_primaryKey;
    }

    /**
     * Returns with name of the table the modell works with.
     * @access public
     * @return string
     */
    public function getTableName() {
        return $this->_tableName;
    }

    /**
     * Returns with columns.
     * @access public
     * @return array
     */
    public function getColumns() {
        if ($this->_columns == null) {
            $this->_columns = $this->_getColumns();
        }
        return $this->_columns;
    }

    /**
     * Returns with primary key.
     * @access public
     * @return string
     */
    public function getPrimaryKey() {
        return $this->_primaryKey;
    }

    /**
     * Returns new empty entity.
     * @access public
     * @param array $data
     * @return Entity
     */
    public function getNew(array $data = null) {
        $entity = $this->_entity;
        return $data === null ? new $entity() : new $entity($data);
    }

    /**
     * Returns entity by primary key from database by parameters.
     * @access public
     * @param mixed $primaryKey
     * @throw Exception
     * @return Entity
     */
    public function getOne($primaryKey) {
        $entity = $this->getEntity(\Core\DAO\QueryBuilder::start()->addWhere($this->_fullPrimaryKey . " = :pk")->addPrepare("pk", $primaryKey)->build());
        if (!$entity) {
            throw new \Core\DAO\Exception("Entity not exists");
        }

        return $entity;
    }

    /**
     * Returns entity object
     * @access protected
     * @param array $data
     * @return Entity
     */
    protected function _getEntity(array $data) {
        $entity = $this->_entity;
        return new $entity($data);
    }

    /**
     * Returns with entities whitch matced to Query.
     * @access public
     * @abstract
     * @param Query $query
     * @return array
     */
    abstract public function getAll($query = null);

    /**
     * Returns with first entity whitch matched to Query and $oder pattern.
     * @access pulic
     * @abstract
     * @param Query $query
     * @return Entity
     */
    abstract public function getEntity($query = null);

    /**
     * Returns the number of results whitch matched to Query.
     * @access public
     * @abstract
     * @param Query $query
     * @return integer
     */
    abstract public function count($query = null);

    /**
     * Validate Entity to commit.
     * @access public
     * @abstract
     * @param Entity $entity
     * @throws Exception
     * @return void
     */
    abstract public function validate(Entity $entity);

    /**
     * Commit Entity.
     * @access public
     * @abstract
     * @throws Exception
     * @param Entity $entity
     * @return Entity
     */
    abstract public function commit(Entity $entity);

    /**
     * Delete Entity.
     * @access public
     * @abstract
     * @param Entity $entity
     * @throws Exception
     * @return void
     */
    abstract public function delete(Entity $entity);

	/**
     * Delete All records.
     * @access public
     * @abstract
     * @param Query $query
     * @return void
     */
    abstract public function deleteAll(Query $query);

    /**
     * Returns with columns of table.
     * @access protected
     * @abstract
     * @return array
     */
    abstract protected function _getColumns();

    /**
     * Set primary key for entities.
     * @access protected
     * @abstract
     * @throws Exception
     * @return void
     */
    abstract protected function _setPrimaryKey($pk);

    /**
     * Set primary key for entities.
     * @access protected
     * @abstract
     * @return void
     */
    abstract protected function _setConnection();
}

?>
