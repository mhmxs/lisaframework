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
 * EntityManager builder class
 * @package Core
 * @subpackage Model
 * @author kovacsricsi
 */
namespace Core\Model;

class EntityManager
{
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
	 * Builder factory.
	 * @access public
	 * @static
	 * @param string $tableName
	 * @return Model
	 */
	public static function Builder($tableName)
	{
		$class = get_called_class();
		return new $class($tableName);
	}

	/**
	 * Constructor sets default variables.
	 * @access protected
	 * @param string $tableName
	 * @return void
	 */
	protected function __construct($tableName)
	{
		if ($tableName === null) {
			throw new EntityManagerException("Table name must not be null!");
		}
		$this->_tableName      = $tableName;
		$this->_entity         = null;
		$this->_dbType         = null;
		$this->_connectionName = "default";
	}

	/**
	 * Set entity class.
	 * @access public
	 * @param string $entity
	 * @return Model
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
	 * @return Model
	 */
	public function setDbType($dbType)
	{
		$this->_dbType = $dbType;
		return $this;
	}

	/**
	 * Set connection name.
	 * @access public
	 * @param string $name
	 * @return Model
	 */
	public function setConnectionName($connectionName)
	{
		$this->_connectionName = $connectionName;
		return $this;
	}

	/**
	 * Bulder.
	 * @access public
	 * @return IEntityManager
	 */
	public function build()
	{
		if ($this->_dbType == null) {
		    $reader        = \Util\Config\Cache::getConfig(DIR_ROOT . "/config/Config.ini");
		    $this->_dbType = $reader->DATABASE->type;
		}

		$model = "\\Core\\Model\\" . $this->_dbType . "\\EntityManager";
		return $model::getInstance($this->_tableName, $this->_entity == null ? "\\Core\\Model\\" . $this->_dbType . "\\Entity" : $this->_entity, $this->_connectionName);
	}
}

?>