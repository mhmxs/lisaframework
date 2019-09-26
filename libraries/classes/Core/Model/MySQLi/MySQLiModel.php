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
 * MySQLi Model classe
 * @package Core
 * @subpackage Model
 * @author kovacsricsi
 */
class MySQLiModel extends MySQLModel
{
	/**
	 * Factory method for Model.
	 * @access public
	 * @static
     * @param string $tableName
     * @throws ModelException
	 * @return Model
	 */
	public static function init($tableName, $entity = "MySQLiEntity")
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
	public function __construct($tableName, $entity = "MySQLiEntity")
	{
		parent::__construct($tableName, $entity);
	}
}

?>
