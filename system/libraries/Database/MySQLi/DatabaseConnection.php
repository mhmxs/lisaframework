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
 * MySQLiDatabaseConnection handling MySQLi database connection
 * @package Core
 * @subpackage Database
 * @author kovacsricsi
 */

namespace Core\Database\MySQLi;

class DatabaseConnection implements \Core\Database\IDatabaseConnection {

	/**
	 * Connections.
	 * @access protected
	 * @staticvar array
	 */
	protected static $_connections = array();
	/**
	 * Daatbase connection.
	 * @access protected
	 * @var mysqli
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
	public static function getConnection($name = "default") {
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
	protected function __construct($name) {
		if (!class_exists("mysqli")) {
			\Core\ErrorHandler\Basic::getInstance()->write_database_error_log("MySQLi", 0, "MySQLi not supportexd in PHP");
			throw new \Core\Database\DatabaseErrorException("MySQLi not supportexd in PHP");
		}
		$reader = \Util\Config\Cache::getConfig(DIR_CONFIG . "/MySQL.ini");

		$this->_connection = new \mysqli($reader->$name->db_host, $reader->$name->db_user, $reader->$name->db_password, $reader->$name->db_name, $reader->$name->db_port != null ? ":" . $reader->$name->db_port : null);

		if (is_null($this->_connection->get_server_info())) {
			\Core\ErrorHandler\Basic::getInstance()->write_database_error_log("MySQLi", 0, "Cannot connect to database : " . $name);
			throw new \Core\Database\DatabaseErrorException($name);
		} else {
			$this->_connection->set_charset($reader->$name->db_charset);
		}
	}

	/**
	 * Returns all row from database.
	 * @access public
	 * @param string $query
	 * @param array $prepare
	 * @return array
	 */
	public function getAll($query, &$prepare = null) {
		$all = array();

		if ($this->execute($query, $prepare)) {
			do {
				if ($result = $this->_connection->store_result()) {
					while ($row = $result->fetch_assoc()) {
						$all[] = $row;
					}
					$result->free();
				}
			} while ($this->_connection->next_result());
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
	public function getOne($query, &$prepare = null) {
		$one = null;

		if ($this->execute($query, $prepare)) {
			$result = $this->_connection->store_result();

			$one = $result->fetch_assoc();
			$result->free();
		}

		return $one;
	}

	/**
	 * Execute query.
	 * @access public
	 * @param string $query
	 * @param array $prepare
	 * @throws DatabaseErrorException
	 * @return resource
	 */
	public function execute($query, &$prepare = null) {
		if (!is_null($prepare)) {
			$this->_prepare($query, $prepare);
		}

		$resp = $this->_connection->multi_query($query);

		if ($this->_connection->errno != 0) {
			\Core\ErrorHandler\Basic::getInstance()->write_database_error_log("MySQLi", $this->_connection->errno, $this->_connection->error . " QUERY: " . $query);

			switch ($this->_connection->errno) {
				case 1216:
					throw new \Core\Database\ForeignKeyErrorException($this->_connection->error . " QUERY: " . $query);
					break;

				case 1062:
					throw new \Core\Database\DuplicateKeyErrorException($this->_connection->error . " QUERY: " . $query);
					break;

				default:
					throw new \Core\Database\DatabaseErrorException($this->_connection->error . " QUERY: " . $query);
					break;
			}
		}

		return $resp;
	}

	/**
	 * Protect database from sqlinjection.
	 * @access protected
	 * @param string $query
	 * @param array $prepare
	 * @return void
	 */
	protected function _prepare(&$query, &$prepare) {
		$keys = array();
		$values = array();

		$query = str_replace("\t", "", $query);

		foreach ($prepare as $index => $value) {
			$keys[] = "/(.*?):" . $index . "(?![a-zA-Z0-9_]+)/";
			if (is_null($value)) {
				$values[] = "\\1null\\2";
			} elseif (is_numeric($value)) {
				$values[] = "\\1'" . $value . "'\\2";
			} else {
				$values[] = "\\1'" . mysql_real_escape_string($value, $this->_connection) . "'\\2";
			}
		}

		$query = preg_replace($keys, $values, $query);
	}

	/**
	 * Constructor tart transaction.
	 * @access public
	 * @throws DatabaseErrorException
	 * return void
	 */
	public function start() {
		if ($this->_needRollback === true) {
			throw new \Core\Database\DatabaseErrorException("Transaction already started!");
		}

		$this->execute("START TRANSACTION;");

		$this->_needRollback = true;
	}

	/**
	 * Commit transaction.
	 * @access public
	 * @throws DatabaseErrorException
	 * return void
	 */
	public function commit() {
		if ($this->_needRollback === false) {
			throw new \Core\Database\DatabaseErrorException("Transaction not started!");
		}

		$this->execute("COMMIT;");

		$this->_needRollback = false;
	}

	/**
	 * Rollback transaction.
	 * @access public
	 * @throws DatabaseErrorException
	 * return void
	 */
	public function rollback() {
		if ($this->_needRollback === false) {
			throw new \Core\Database\DatabaseErrorException("Transaction not started!");
		}

		$this->execute("ROLLBACK;");

		$this->_needRollback = false;
	}

	/**
	 * Desctructor rollback transaction if it not committed.
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		if ($this->_needRollback == true) {
			$this->rollback();
		}

		if (!is_null($this->_connection->get_server_info())) {
			$this->_connection->close();
		}
	}

}

?>