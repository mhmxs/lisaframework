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
 * @subpackage Entity
 * @category Abstract
 * @author kovacsricsi
 */
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
     * The entity is changed or not
     * @access protected
     * @var boolean
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
	 * @return AEntity
	 */
	public static function getNew($tableName, array $columns, $primaryKey)
	{
		return new self($tableName, $columns, $primaryKey);
	}

	/**
	 * Factory method for existied entity.
	 * @access public
	 * @static
	 * @param string $tableName
	 * @param array $columns
	 * @param string $primaryKey
	 * @param mixed $entityPrimaryKey
	 * @return AEntity
	 */
	public static function getExisted($tableName, array $columns, $primaryKey, $entityPrimaryKey)
	{
		return new self($tableName, $columns, $primaryKey, $entityPrimaryKey);
	}

	/**
	 * Factory method for readonly entity.
	 * @access public
	 * @static
	 * @param string $tableName
	 * @param string $primaryKey
	 * @param mixed $entityPrimaryKey
	 * @return AEntity
	 */
	public static function getReadOnly($tableName, $primaryKey, $entityPrimaryKey)
	{
		return new self($tableName, array(), $primaryKey, $entityPrimaryKey);
	}

	/**
	 * Constructor.
	 * @access public
	 * @param string $tableName
	 * @param array $columns
	 * @param string $primaryKey
	 * @param mixed $entityPrimaryKey
	 * @return void
	 */
	public function __construct($tableName, array $columns, $primaryKey, $entityPrimaryKey = null)
	{
		$this->_entity     = array();
		$this->_tableName  = (string)$tableName;
		$this->_columns    = $columns;
		$this->_primaryKey = (string)$primaryKey;
		$this->_changed    = false;
		$this->_readOnly   = ($this->_columns == false) ? true : false;

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

		if ($key != $this->_primaryKey) {
		    $this->_changed      = true ;
			$this->_entity[$key] = $value;
		}
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