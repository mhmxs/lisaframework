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
class MySQLiDatabaseConnection implements IDatabaseConnection
{
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
	 * @return MySQLDatabaseConnection
	 */
	public static function getConnection($name = "default")
	{
        if (!isset(self::$_connections[$name]) || !(self::$_connections[$name] instanceof MySQLiDatabaseConnection)) {
			self::$_connections[$name] = new self($name);
		}

		return self::$_connections[$name];
	}

	/**
	 * Constructor set connection.
	 * @access protected
	 * @param string $name
	 * @throws Exception
	 * @return void
	 */
	protected function __construct($name)
	{
        $reader = new ConfigReader(DIR_CONFIG . "/MySQL.ini");

        $this->_connection = new mysqli($reader->$name->db_host, $reader->$name->db_user, $reader->$name->db_password, $reader->$name->db_name);

		if (is_null($this->_connection->get_server_info())) {
			BasicErrorHandler::getInstance()->write_database_error_log("MySQLi", 0, "Cannot connect to database : " . $name);
			throw new DatabaseErrorException($name);
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
	public function getAll($query, $prepare = null)
	{
		if (!is_null($prepare)) {
			$this->_prepare($query, $prepare);
		}

		$all = array();

		if ($this->execute($query)) {
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
	public function getOne($query, $prepare = null)
	{
		if (!is_null($prepare)) {
			$this->_prepare($query, $prepare);
		}

		$one = null;

		if ($this->execute($query)) {
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
	public function execute($query, $prepare = null)
	{
		if (!is_null($prepare)) {
			$this->_prepare($query, $prepare);
		}

		$resp = $this->_connection->multi_query($query);

		if ($this->_connection->errno != 0) {
			BasicErrorHandler::getInstance()->write_database_error_log("MySQLi", $this->_connection->errno, $this->_connection->error);
			throw new DatabaseErrorException($this->_connection->error);
		}

		return $resp;
	}

	/**
	 * Returns with name of tables in selected database.
	 * @access public
	 * @return array
	 */
	public function getTableNames()
	{
		return $this->getAll("SHOW FULL TABLES;");
	}

	/**
	 * Protect database from sqlinjection.
	 * @access protected
	 * @param string $query
	 * @param array $prepare
	 * @return void
	 */
	protected function _prepare(&$query, $prepare)
	{
		$keys   = array();
		$values = array();

   		foreach($prepare as $index => $value) {
   			$keys[]   = "/(.*?):" . $index . "(?![a-zA-Z0-9_]+)/";
            $values[] = "\\1'" . ((get_magic_quotes_gpc() == true) ? $value : $this->_connection->real_escape_string($value)) . "'\\2";
   		}

		$query = preg_replace($keys, $values, $query);
		$query = str_replace("\t", "", $query);

		do {
			$query = str_replace("  ", " ", $query);
		} while (ereg("  ", $query));
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
			throw new DatabaseErrorException("Transaction already started!");
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
    public function commit()
    {
		if ($this->_needRollback === false) {
			throw new DatabaseErrorException("Transaction not started!");
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
    public function rollback()
    {
		if ($this->_needRollback === false) {
			throw new DatabaseErrorException("Transaction not started!");
		}

        $this->execute("ROLLBACK;");

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

		if (!is_null($this->_connection->get_server_info())) {
			$this->_connection->close();
		}
    }
}
?>