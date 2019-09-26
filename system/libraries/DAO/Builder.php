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
 * Data Access Object Builder
 * @package Core
 * @subpackage DAO
 * @author kovacsricsi
 */
namespace Core\DAO;

class Builder {
	/**
	 * Name of table.
	 * @access protected
	 * @var string
	 */
	protected $_tableName;

	/**
	 * Class of Entity.
	 * @access protected
	 * @var string
	 */
	protected $_entity;

	/**
	 * Type of database.
	 * @access protected
	 * @var string
	 */
	protected $_dbType;

	/**
	 * Name of database connection.
	 * @access protected
	 * @var string
	 */
	protected $_connectionName;

	/**
	 * Table prefix.
	 * @access protected
	 * @var string
	 */
	protected $_prefix;

	/**
	 * Entity primary key.
	 * @access protected
	 * @var string
	 */
	protected $_pk;

	/**
	 * DAO class.
	 * @access protected
	 * @var string
	 */
	protected $_dao;

	/**
	 * Builder factory.
	 * @access public
	 * @static
	 * @param string $tableName
	 * @param string $connectionName
	 * @return Builder
	 */
	public static function start($tableName, $connectionName = "default")
	{
		$class = get_called_class();
		return new $class($tableName, $connectionName);
	}

	/**
	 * Constructor sets default variables.
	 * @access protected
	 * @param string $tableName
	 * @return void
	 */
	protected function __construct($tableName, $connectionName)
	{
		$this->_tableName      = $tableName;
		$this->_entity         = "\Core\DAO\Entity";
		$this->_dbType         = null;
		$this->_connectionName = $connectionName;
		$this->_prefix         = "";
		$this->_pk             = null;
	}

	/**
	 * Set entity class.
	 * @access public
	 * @param string $entity
	 * @return Builder
	 */
	public function setEntity($entity)
	{
		$this->_entity = $entity;
		return $this;
	}

	/**
	 * Set database type.
	 * @access public
	 * @param string $dbType
	 * @return Builder
	 */
	public function setDbType($dbType)
	{
		$this->_dbType = $dbType;
		return $this;
	}

	/**
	 * Set entity primary key.
	 * @access public
	 * @param string $pk
	 * @return Builder
	 */
	public function setPrimaryKey($pk)
	{
		$this->_pk = $pk;
		return $this;
	}

	/**
	 * Set table prefix.
	 * @access public
	 * @param string $prefix
	 * @return Builder
	 */
	public function setPrefix($prefix)
	{
		$this->_prefix = $prefix;
		return $this;
	}

	/**
	 * Set DAO calss.
	 * @access public
	 * @var string $dao
	 * @return Builder
	 */
	public function setDAO($dao) {
		$this->_dao = (string) $dao;
		return $this;
	}
	/**
	 * Bulder.
	 * @access public
	 * @return ADAO
	 */
	public function build()
	{
		if ($this->_dbType == null) {
		    $reader        = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Config.ini");
		    $this->_dbType = $reader->DATABASE->type;
		}

		$dao = $this->_dao == false ? "\Core\DAO\\" . $this->_dbType . "\DAO" : $this->_dao;
		return $dao::getInstance($this->_prefix . $this->_tableName, $this->_entity, $this->_connectionName, $this->_pk);
	}
}
?>
