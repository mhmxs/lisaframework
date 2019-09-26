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
 * AEntity abstarct entity classes
 * @package Core
 * @subpackage Model
 * @category Abstract
 * @author kovacsricsi
 */
namespace Core\Model;

abstract class AEntity {

    /**
     * Data of entity.
     * @access protected
     * @var array
     */
    protected $_entity;

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
     * Primary key of entity.
     * @access protected
     * @var string
     */
    protected $_primaryKey;

    /**
     * The entity data is changed or not.
     * @access protected
     * @var array
     */
    protected $_changed;

	/**
	 * Read only flag.
	 * @access protected
	 * @var boolean
	 */
	protected $_readOnly;

	/**
	 * Factory method for new entity.
	 * @access public
	 * @static
	 * @param string $tableName
	 * @param array $columns
	 * @param string $primaryKey
	 * @param string $connectionName
	 * @return AEntity
	 */
	public static function getNew($tableName, array $columns, $primaryKey, $connectionName = null)
	{
		$class = get_called_class();
		return new $class($tableName, $columns, $primaryKey, null, $connectionName);
	}

	/**
	 * Factory method for existied entity.
	 * @access public
	 * @static
	 * @param string $tableName
	 * @param array $columns
	 * @param string $primaryKey
	 * @param mixed $entityPrimaryKey
	 * @param string $connectionName
	 * @return AEntity
	 */
	public static function getExisted($tableName, array $columns, $primaryKey, $entityPrimaryKey, $connectionName = null)
	{
		$class = get_called_class();
		return new $class($tableName, $columns, $primaryKey, $entityPrimaryKey, $connectionName);
	}

	/**
	 * Factory method for readonly entity.
	 * @access public
	 * @static
	 * @param string $tableName
	 * @param string $primaryKey
	 * @param mixed $entityPrimaryKey
	 * @param string $connectionName
	 * @return AEntity
	 */
	public static function getExistedReadOnly($tableName, $primaryKey, $entityPrimaryKey, $connectionName = null)
	{
		$class = get_called_class();
		return new $class($tableName, array(), $primaryKey, $entityPrimaryKey, $connectionName);
	}

	/**
	 * Constructor.
	 * @access public
	 * @param string $tableName
	 * @param array $columns
	 * @param string $primaryKey
	 * @param mixed $entityPrimaryKey
	 * @param string $connectionName
	 * @return void
	 */
	public function __construct($tableName, array $columns, $primaryKey, $entityPrimaryKey = null, $connectionName = null)
	{
		$this->_entity			= array();
		$this->_tableName		= (string)$tableName;
		$this->_columns			= $columns;
		$this->_primaryKey		= (string)$primaryKey;
		$this->_connectionName	= (string)$connectionName;
		$this->_changed			= array();
		$this->_readOnly		= ($this->_columns == false) ? true : false;

		if ($entityPrimaryKey) {
			$this->_load($entityPrimaryKey);
		}
	}

	/**
	 * __get
	 * @access public
	 * @param string $key
	 * @return string
	 */
	public function __get($key)
	{
		if (isset($this->_entity[$key])) {
			return $this->_entity[$key];
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
		if ($this->_readOnly) {
			throw new EntityException("Entity is read only!");
		}

		if ( (!isset($this->_entity[$key]) || $value != $this->_entity[$key]) && ($key != $this->_primaryKey || !isset($this->_entity[$this->_primaryKey])) ) {
		    $this->_changed[]    = $key;
			$this->_entity[$key] = $value;
		}
	}

	/**
	 * Entity is changed or not.
	 * @access public
	 * @param string $filed
	 * @return boolean
	 */
	public function isChanged($field = null)
	{
		return $field == null ? (boolean)count($this->_changed) : in_array($field, $this->_changed);
	}

	/**
	 * Convert entity to array.
	 * @access public
	 * @return array
	 */
	public function toArray()
	{
		return $this->_entity;
	}

	/**
	 * Mass use of __set
	 * @access public
	 * @param array $data
	 * @throws EntityException
	 * @return void
	 */
	public function setData($data)
	{
		if ($this->_readOnly) {
			throw new EntityException("Entity is read only!");
		}

		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
	 * Set read only flag.
	 * @access public
	 * @param boolean $readOnly
	 * @return void
	 */
	public function setReadOnly($readOnly)
	{
		$this->_readOnly = $readOnly;
	}

	/**
	 * Returns with entity read only flag.
	 * @access public
	 * @return boolean
	 */
	public function getReadOnly()
	{
		return $this->_readOnly;
	}

	/**
	 * Returns with entity table name.
	 * @access public
	 * @return string
	 */
	public function getTableName()
	{
		return $this->_tableName;
	}

	/**
	 * Load entity from database.
	 * @access protected
	 * @param int $entityPrimaryKey
	 * @return void
	 */
	abstract protected function _load($entityPrimaryKey);

	/**
	 * Delete entity from database.
	 * @access public
	 * @throws EntityException
	 * @return void
	 */
	abstract public function delete();

	/**
	 * Store entity to database.
	 * @access public
	 * @throws EntityException
	 * @return AEntity
	 */
	abstract public function commit();

	/**
	 * Validate data to store entity.
	 * @access public
	 * @return void
	 */
	 abstract public function validate();
}

?>