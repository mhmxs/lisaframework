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
 * PgSQLDatabaseConnection handling PgSQL database connection
 * @package Core
 * @subpackage Database
 * @author kovacsricsi
 */

namespace Core\Database\PgSQL;

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
        if (!function_exists("pg_connect")) {
            \Core\ErrorHandler\Basic::getInstance()->write_database_error_log("PgSQL", 0, "PgSQL not supportexd in PHP");
            throw new \Core\Database\DatabaseErrorException("PgSQL not supportexd in PHP");
        }
        $this->_needRollback = false;

        $reader = \Util\Config\Cache::getConfig(DIR_CONFIG . "/PgSQL.ini");

        $this->_connection = pg_connect("host=" . $reader->$name->db_host . ($reader->$name->db_port != null ? ":" . $reader->$name->db_port : "") . " dbname=" . $reader->$name->db_name . " user=" . $reader->$name->db_user . " password=" . $reader->$name->db_password);

        if (!is_resource($this->_connection)) {
            \Core\ErrorHandler\Basic::getInstance()->write_database_error_log("PgSQL", 0, "Cannot connect to database : " . $name);
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
    public function getAll($query, &$prepare = null) {
        $all = array();

        $resp = $this->execute($query, $prepare);

        if (is_resource($resp)) {
            while ($row = pg_fetch_array($resp, null, PGSQL_ASSOC)) {
                $all[] = $row;
            }

            pg_free_result($resp);
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
        $resp = $this->execute($query, $prepare);

        if (is_resource($resp)) {
            $data = pg_fetch_array($resp, null, PGSQL_ASSOC);

            pg_free_result($resp);

            return $data;
        }

        return null;
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
        if ($prepare !== null) {
            $this->_prepare($query, $prepare);
        }

        $resp = pg_query($this->_connection, $query);

        if (pg_last_error($this->_connection) != false) {
            \Core\ErrorHandler\Basic::getInstance()->write_database_error_log("PgSQL", 0, pg_last_error($this->_connection) . " QUERY: " . $query);

            throw new \Core\Database\DatabaseErrorException(pg_last_error($this->_connection) . " QUERY: " . $query);
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
                $values[] = "\\1'" . pg_escape_string($this->_connection, $value) . "'\\2";
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

        if (is_resource($this->_connection)) {
            pg_close($this->_connection);
        }
    }

}

?>