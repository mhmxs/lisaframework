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
 * Database connection factory create default database connection.
 * @package Core
 * @subpackage Database
 * @author kovacsricsi
 */
namespace Core\Database;

class ConnectionFactory
{
	/**
	 * Constructor.
	 * @access protected
	 * @throws DatabaseErrorException
	 * @return void
	 */
	protected function __construct()
	{
		throw new DatabaseErrorException("DatabaseConnectionFactory cannot create!");
	}
	
	/**
	 * Returns with default database connection.
	 * @access public
	 * @static
	 * @return IDatabaseConnection
	 */
	public static function getDefaultConnection()
	{
		$reader = \Util\Config\Cache::getConfig(DIR_CONFIG . "/Config.ini");
		
		$class = "\\Core\\Database\\" . $reader->DATABASE->type . "\\DatabaseConnection";
		return $class::getConnection();
	}
}
?>
