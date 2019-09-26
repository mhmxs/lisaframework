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
 * Model initialize class
 * @package Core
 * @subpackage Model
 * @author kovacsricsi
 * @deprecated
 */
namespace Core\Model;

class Model
{
	/**
	 * Recreate Model class by static call.
	 * @access public
	 * @static
	 * @param string $name name of the function what you want to call public
	 * @param array $arguments array of the public functiion's arguments
	 * @return IEntityManager
	 */
	public static function __callStatic($name, $arguments)
	{
		$data = static::_getInstanceParameters();
		$class = static::getInstance($data["tablename"], $data["entity"], $data["dbType"], $data["connectionName"]);
		return call_user_func_array(array($class, $name), $arguments);
	}

	/**
	 * Returns with instance of model.
	 * @access public
	 * @static
	 * @deprecated
	 * @param string $tableName
	 * @param string $entity
	 * @param string $dbType
	 * @param string $connectionName
	 * @return IEntityManager
	 */
	public static function getInstance($tableName, $entity = null, $dbType = null, $connectionName = "default") {
		return EntityManager::Builder($tableName)->setEntity($entity)->setDbType($dbType)->setConnectionName($connectionName)->build();
	}

	/**
	 * Factory method from Model.
	 * @access public
	 * @static
	 * @deprecated
	 * @param string $tableName
	 * @param string $entity
	 * @param string $dbType
	 * @param string $connectionName
	 * @return IEntityManager
	 */
	public static function init($tableName, $entity = null, $dbType = null, $connectionName = "default")
	{
		return static::getInstance($tableName, $entity, $dbType, $connectionName);
	}
	
	/**
	 * Instace data initializator of static to public function conversion.
	 * @access protected
	 * @static
	 * @return array
	 */
	protected static function _getInstanceParameters()
	{
		$data = array();
		$data["tablename"]      = null;
		$data["entity"]         = null;
		$data["dbType"]         = null;
		$data["connectionName"] = "default";
		return $data;
	}
}

?>