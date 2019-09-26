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
 * QueryHandler is handling query and choose right DatabaseConnection
 * @package Core
 * @subpackage Database
 * @author kovacsricsi
 */
class QueryHandler {
	
    /**
     * Database type
     * @access protected
     * @static
     * @staticvar string
     */
    protected static $_dbType = DB_TYPE;

	/**
	 * Constructor protecte the classs, because it is stataic.
	 * @access private
	 * @throws Exception
	 * @return void
	 */
	private function __construct()
	{
		throw new Exception("Illegal operation, this class is only static class!");
	}

    /**
     * Set database type
     * @access public
     * @static
     * @param string $dbType
     * @return void
     */
    public static function setDbType($dbType)
    {
        self::$_dbType = (string)$dbType;
    }

	/**
	 * Returns all row from database.
	 * @access public
	 * @static
	 * @param string $query
	 * @param array $prepare
	 * @param string $name
     * @throws DatabaseErrorException
	 * @return array
	 */
	public static function getAll($query, $prepare = null, $name = "default")
    {
        if (self::$_dbType == "MySQL") {
            return MySQLDatabaseConnection::getConnection($name)->getAll($query, $prepare);
        } elseif (self::$_dbType == "MySQLi") {
            return MySQLiDatabaseConnection::getConnection($name)->getAll($query, $prepare);
        } else {
            throw new DatabaseErrorException("Database not set!");
        }
    }

	/**
	 * Returns one row from database.
	 * @access public
	 * @static
	 * @param string $query
	 * @param array $prepare
	 * @param string $name
     * @throws DatabaseErrorException
	 * @return array
	 */
	public static function getOne($query, $prepare = null, $name = "default")
    {
        if (self::$_dbType == "MySQL") {
            return MySQLDatabaseConnection::getConnection($name)->getOne($query, $prepare);
        } elseif (self::$_dbType == "MySQLi") {
            return MySQLiDatabaseConnection::getConnection($name)->getOne($query, $prepare);
        } else {
            throw new DatabaseErrorException("Database not set!");
        }
    }

	/**
	 * Execute query.
	 * @access public
	 * @static
	 * @param string $query
	 * @param array $prepare
	 * @param string $name
     * @throws DatabaseErrorException
	 * @return void
	 */
	public static function execute($query, $prepare = null, $name = "default")
    {
        if (self::$_dbType == "MySQL") {
            MySQLDatabaseConnection::getConnection($name)->execute($query, $prepare);
        } elseif (self::$_dbType == "MySQLi") {
            MySQLiDatabaseConnection::getConnection($name)->execute($query, $prepare);
        } else {
            throw new DatabaseErrorException("Database not set!");
        }
    }

	/**
	 * Returns with name of tables in selected database.
	 * @access public
	 * @static
	 * @param string $name
	 * @return array
	 */
	public static function getTableNames($name = "default")
	{
	    if (self::$_dbType == "MySQL") {
            MySQLDatabaseConnection::getConnection($name)->getTableNames();
        } elseif (self::$_dbType == "MySQLi") {
            MySQLiDatabaseConnection::getConnection($name)->getTableNames();
        } else {
            throw new DatabaseErrorException("Database not set!");
        }
	}

	/**
	 * Start transaction.
	 * @access public
	 * @static
	 * @param string $name
	 * @throws DatabaseErrorException
	 * return void
	 */
    public static function start($name = "default")
    {
	    if (self::$_dbType == "MySQL") {
            MySQLDatabaseConnection::getConnection($name)->start();
        } elseif (self::$_dbType == "MySQLi") {
            MySQLiDatabaseConnection::getConnection($name)->start();
        } else {
            throw new DatabaseErrorException("Database not set!");
        }
    }

	/**
	 * Commit transaction.
	 * @access public
	 * @static
	 * @param string $name
	 * @throws DatabaseErrorException
	 * return void
	 */
    public static function commit($name = "default")
    {
	    if (self::$_dbType == "MySQL") {
            MySQLDatabaseConnection::getConnection($name)->commit();
        } elseif (self::$_dbType == "MySQLi") {
            MySQLiDatabaseConnection::getConnection($name)->commit();
        } else {
            throw new DatabaseErrorException("Database not set!");
        }
    }

	/**
	 * Rollback transaction.
	 * @access public
	 * @static
	 * @param string $name
	 * @throws DatabaseErrorException
	 * return void
	 */
    public static function rollback($name = "default")
    {
	    if (self::$_dbType == "MySQL") {
            MySQLDatabaseConnection::getConnection($name)->rollback();
        } elseif (self::$_dbType == "MySQLi") {
            MySQLiDatabaseConnection::getConnection($name)->rollback();
        } else {
            throw new DatabaseErrorException("Database not set!");
        }
    }
}

?>
