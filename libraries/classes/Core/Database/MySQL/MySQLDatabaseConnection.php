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
 * MySQLDatabaseConnection handling MySQL database connection
 * @package Core
 * @subpackage Database
 * @author kovacsricsi
 */
class MySQLDatabaseConnection implements IDatabaseConnection
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
	 * @return MySQLDatabaseConnection
	 */
	public static function getConnection($name = "default")
	{
        if (!isset(self::$_connections[$name]) || !(self::$_connections[$name] instanceof MySQLDatabaseConnection)) {
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
		$this->_needRollback = false;
		
		$reader = new ConfigReader(DIR_CONFIG . "/MySQL.ini");

		$this->_connection = mysql_connect($reader->$name->db_host, $reader->$name->db_user, $reader->$name->db_password);

		if (!is_resource($this->_connection)) {
			BasicErrorHandler::getInstance()->write_database_error_log("MySQL", 0, "Cannot connect to database : " . $name);
			throw new DatabaseErrorException($name);
		} else {
			mysql_select_db($reader->$name->db_name, $this->_connection);

			$this->execute("SET NAMES " . $reader->$name->db_charset);
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

		$resp = $this->execute($query);

		if (is_resource($resp)) {
			while($row = mysql_fetch_assoc($resp)) {
				$all[] = $row;
			}
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

		$resp = $this->execute($query);

		if (is_resource($resp)) {
			$one = mysql_fetch_assoc($resp);
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
		
		$resp = mysql_query($query, $this->_connection);

		if (mysql_errno($this->_connection) != 0) {
			BasicErrorHandler::getInstance()->write_database_error_log("MySQL", mysql_errno($this->_connection), mysql_error(self::$_connections[$name]));
			throw new DatabaseErrorException(mysql_error($this->_connection));
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
            $values[] = "\\1'" . ((get_magic_quotes_gpc() == true) ? $value : mysql_real_escape_string($value, $this->_connection)) . "'\\2";
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

		if (is_resource($this->_connection)) {
			mysql_close($this->_connection);
		}
    }
}
?>