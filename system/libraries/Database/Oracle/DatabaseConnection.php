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
 * Oracle DatabaseConnection handling Oracle database connection
 * @package Core
 * @subpackage Database
 * @author kovacsricsi
 */
namespace Core\Database\Oracle;

class DatabaseConnection {
	/**
	 * Connections.
	 * @access protected
     * @staticvar array
	 */
	protected static $_connections = array();

	/**
	 * Daatbase connection.
	 * @access protected
	 * @var resource
	 */
	protected $_connection;

	/**
	 * CNeed rollback or not.
	 * @access protected
	 * @var boolean
	 */
	protected $_needRollback;

	/**
	 * Returns with database connection.
	 * @access public
	 * @static
	 * @param string $name
	 * @return DatabaseConnection
	 */
	public static function getConnection($name = "default")
	{
        if (!isset(static::$_connections[$name]) || !(static::$_connections[$name] instanceof DatabaseConnection)) {
			$class = get_called_class();
        	static::$_connections[$name] = new $class($name);
		}

		return static::$_connections[$name];
	}

	/**
	 * Constructor set connection.
	 * @access protected
	 * @param string $name
	 * @throws DatabaseErrorException
	 * @return void
	 */
	protected function __construct($name)
	{
		if (!function_exists("oci_connect")) {
			\Core\ErrorHandler\Basic::getInstance()->write_database_error_log("Oracle", 0, "Oracle not supportexd in PHP");
			throw new \Core\Database\DatabaseErrorException("Oracle not supportexd in PHP");
		}
		$this->_needRollback = false;

		$reader = \Util\Config\Cache::getConfig(DIR_CONFIG . "/Oracle.ini");

		$this->_connection = oci_connect($reader->$name->db_user, $reader->$name->db_password, $reader->$name->db_host . ($reader->$name->db_port != null ? ":" . $reader->$name->db_port : "") ."/" . $reader->$name->db_sid);

		if (!is_resource($this->_connection)) {
			\Core\ErrorHandler\Basic::getInstance()->write_database_error_log("Oracle", 0, "Cannot connect to database : " . $name);
			throw new \Core\Database\DatabaseErrorException("Cannot connect to database : " . $name);
		}
	}

	/**
	 * Returns all row from database.
	 * @access public
	 * @param string $query
	 * @param array $prepare
	 * @return array
	 */
	public function getAll($query, &$prepare = null)
	{
		$all = array();

		$resp = $this->execute($query, $prepare);

		if (is_resource($resp)) {
			while ($row = oci_fetch_assoc($resp)) {
				$all[] = $row;
			}

			oci_free_statement($resp);
		}

		return $all;
	}

	/**
	 * Returns one row from database.
	 * @access public
	 * @param string $query
	 * @param array $prepare
	 * @return object
	 */
	public function getOne($query, &$prepare = null)
	{
		$resp = $this->execute($query, $prepare);

		if (is_resource($resp)) {
			$data = oci_fetch_assoc($resp);
			oci_free_statement($resp);

			return $data;
		}

		return null;
	}

	/**
	 * Execute query.
	 * @access public
	 * @param string $query
	 * @param array $prepare (if any value is array, first item will be the value, and secound the type)
	 * @throws DatabaseErrorException
	 * @return resource
	 */
	public function execute($query, &$prepare = null)
	{
		$statement = oci_parse($this->_connection, $query);

		if ($prepare !== null) {
			foreach($prepare as $k => &$v) {
				if (is_array($v) && count($v) == 2) {
					$type = $v[1];
					$prepare[$k] = $v[0];
				} else {
					if (is_int($v)) {
						$type = SQLT_INT;
					} elseif (is_float($v)) {
						$type = SQLT_LNG;
					} elseif (is_string($v)) {
						$type = SQLT_CHR;
					} elseif (is_object($v)) {
						$type = SQLT_NTY;
					} else {
						$type = null;
					}
				}

				oci_bind_by_name($statement, $k, $v, $type);
			}
		}

		oci_execute($statement, $this->_needRollback == true ? OCI_NO_AUTO_COMMIT : OCI_COMMIT_ON_SUCCESS);

		if (oci_error($this->_connection) != false) {
			$error = oci_error($this->_connection);
			\Core\ErrorHandler\Basic::getInstance()->write_database_error_log("Oracle", $error["code"], $error["message"] . " QUERY: " . $query);
			throw new \Core\Database\DatabaseErrorException($error["message"] . " QUERY: " . $query);
		}

		return $statement;
	}

	/**
	 * Returns field's value from the fetched row.
	 * @access public
	 * @param resource $resource
	 * @param mixed $field
	 * @return mixed
	 */
	public function result($resource, $field)
	{
		return is_resource($resource) ? oci_result($resource, $field) : null;
	}

	/**
	 * Constructor tart transaction.
	 * @access public
	 * @throws DatabaseErrorException
	 * return void
	 */
    public function start()
    {
		if ($this->_needRollback === true) {
			throw new \Core\Database\DatabaseErrorException("Transaction already started!");
		}

		$this->_needRollback = true;
    }

	/**
	 * Commit transaction.
	 * @access public
	 * @throws DatabaseErrorException
	 * return void
	 */
    public function commit()
    {
		if ($this->_needRollback === false) {
			throw new \Core\Database\DatabaseErrorException("Transaction not started!");
		}

        oci_commit($this->_connection);

		$this->_needRollback = false;
    }

	/**
	 * Rollback transaction.
	 * @access public
	 * @throws DatabaseErrorException
	 * return void
	 */
    public function rollback()
    {
		if ($this->_needRollback === false) {
			throw new \Core\Database\DatabaseErrorException("Transaction not started!");
		}

        oci_rollback($this->_connection);

	    $this->_needRollback = false;
    }

	/**
	 * Desctructor rollback transaction if it not committed.
	 * @access public
	 * @return void
	 */
    public function __destruct()
    {
		if ($this->_needRollback == true) {
			$this->rollback();
		}

		if (is_resource($this->_connection)) {
			oci_close($this->_connection);
		}
    }
}
?>