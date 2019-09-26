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
 * MySQL Entity class
 * @package Core
 * @subpackage Model.MySQL
 * @author kovacsricsi
 */
namespace Core\Model\MySQL;

class Entity extends \Core\Model\AEntity
{
	/**
	 * Load entity from database.
	 * @access protected
	 * @param int $entityPrimaryKey
	 * @return void
	 */
	protected function _load($entityPrimaryKey)
	{
		$sql = "SELECT * FROM " . $this->_tableName . "
				WHERE `" . $this->_primaryKey . "` = :entityPrimaryKey;";

		$params = array(
			"entityPrimaryKey" => $entityPrimaryKey
		);

	    $result = \Core\Database\MySQL\DatabaseConnection::getConnection($this->_connectionName)->getOne($sql, $params);

	    if ($result == true) {
			$this->_entity = $result;
	    } else {
			throw new \Core\Model\EntityException("Entity not found : " . $this->_tableName . " : " . $entityPrimaryKey);
	    }
	}

	/**
	 * Delete entity from database.
	 * @access public
	 * @throws EntityException
	 * @return void
	 */
	public function delete()
	{
		if ($this->_readOnly) {
			throw new \Core\Model\EntityException("Entity is read only!");
		}

		$query = "DELETE FROM " . $this->_tableName . "
		WHERE `" . $this->_primaryKey . "` = :entityPrimaryKey;";

		$params = array(
			"entityPrimaryKey" => $this->_entity[$this->_primaryKey]
		);

		\Core\Database\MySQL\DatabaseConnection::getConnection($this->_connectionName)->execute($query, $params);

		$this->_entity  = array();
		$this->_changed = array();
	}

	/**
	 * Store entity to database.
	 * @access public
	 * @throws EntityException
	 * @return AEntity
	 */
	public function commit()
	{
            if ($this->_readOnly) {
            throw new \Core\Model\EntityException("Entity is read only!");
        }

        if (!empty($this->_changed)) {
            $this->validate();

            $data = array();

            foreach ($this->_columns as $column) {
                if ((isset($this->_entity[$column["Field"]]) || is_null($this->_entity[$column["Field"]])) && in_array($column["Field"], $this->_changed)) {
                    $data[$column["Field"]] = $this->_entity[$column["Field"]];
                }
            }

            if ($data) {
                if (isset($this->_entity[$this->_primaryKey])) {
                    $this->_update($data);
                } else {
                    unset($data[$this->_primaryKey]);
                    $this->_insert($data);
                }
            }
        }

	    return $this;
	}

	/**
	 * Validate data to store entity.
	 * @access public
	 * @return void
	 */
	 public function validate()
	 {
		foreach($this->_columns as $column) {
			if (($column["Field"] !== $this->_primaryKey) && (strtoupper($column["Null"]) == "NO") && ($column["Default"] === null) && (!isset($this->_entity[$column["Field"]]) || is_null($this->_entity[$column["Field"]]))) {
				throw new \Core\Model\EntityException("Missed requested field : " . $column["Field"]);
			}
		}
	 }

	 /**
	  * Insert new Entity to database.
	  * @access protected
	  * @param array $data
	  * @return void
	  */
	protected function _insert(array $data)
	{
		$query = "INSERT INTO " . $this->_tableName . " SET ";

		$tmp = array();

		foreach($data as $k => $v) {
			$tmp[] = " `" . $k . "` =:" . (string)$k;
		}

		$query .= join(",", $tmp);

		\Core\Database\MySQL\DatabaseConnection::getConnection($this->_connectionName)->execute($query, $data);

		$sql = "SELECT * FROM " . $this->_tableName . " WHERE `" . $this->_primaryKey . "` = LAST_INSERT_ID();";
	    $this->_entity = \Core\Database\MySQL\DatabaseConnection::getConnection($this->_connectionName)->getOne($sql);
		$this->_changed = array();
	}

	 /**
	  * Update Entity in database.
	  * @access protected
	  * @param array $data
	  * @return void
	  */
	protected function _update(array $data)
	{
		$query = "UPDATE " . $this->_tableName . " SET ";

		$tmp = array();

		foreach($data as $k => $v) {
			$tmp[] = " `" . $k . "` =:" . (string)$k;
		}

		$query .= join(",", $tmp);

		$query .= " WHERE `" . $this->_primaryKey . "` = :entityPrimaryKey;";

		$data["entityPrimaryKey"] = $this->_entity[$this->_primaryKey];

		\Core\Database\MySQL\DatabaseConnection::getConnection($this->_connectionName)->execute($query, $data);

        $sql = "SELECT * FROM " . $this->_tableName . " WHERE `" . $this->_primaryKey . "` = '".$this->_entity[$this->_primaryKey]."';";
	    $this->_entity = \Core\Database\MySQL\DatabaseConnection::getConnection($this->_connectionName)->getOne($sql);
		$this->_changed = array();
	}

    /**
     * Returns true if column is an auto increment column
     * @param string $columnName
     * @return bool
     */
    public function isAutoIncrement($columnName) {
        $column = $this->_getColumnData($columnName);
        return (bool) strstr($column["Extra"], "auto_increment");
    }

    /**
     * Returns with column's enum data
     * @param string $columnName
     * @throws \Core\Model\EntityException
     * @return array
     */
    public function getEnums($columnName) {
        $type = $this->_getType($columnName);
        if (!isset($type["enum"])) {
            throw new \Core\Model\EntityException("Column " . $columnName . " is not an enum");
        }
        return $type["enum"];
    }

    /**
     * Returns with max length of a data in the column. Useable for HTML + JS vaidation
     * @param string $columnName
     * @return int
     */
    public function getMaxLength($columnName) {
        $type = $this->_getType($columnName);
        // TODO some validating if max length is non-sense, 4 example a bool, enum, date
        foreach ($type as $value) {
            return $value;
        }
    }


    /*
     * Returns true if column is string like type
     * @param string $columnName
     * retrun bool
     */
    public function isStringType($columnName){
        return in_array(key($this->_getType($columnName)), array("varchar","text","blob","tinyblob","mediumblob","longblob","char"));
    }

    /*
     * Returns true if column is string like type
     * @param string $columnName
     * retrun bool
     */
    public function isNumberType($columnName){
        return in_array(key($this->_getType($columnName)),  array("int","float","tinyint","year","smallint","mediumint","bigint","double","dec","decimal"));
    }
    
    /**
     * Returns with column's type adnd its parameters
     * @param string $columnName
     * @throws \Core\Model\EntityException
     * @return array
     */
    protected function _getType($columnName) {
        $column = $this->_getColumnData($columnName);
        $matches = array();

        if ( preg_match("/^([\w]*)(.*)/", $column["Type"], $matches) ) {
            $key = $matches[1];
            $values = trim($matches[2], "()");

            $return = array($key => array());
            if ($values != "") {
                foreach (explode(",", $values) as $value) {
                    $return[$key][] = trim($value, "'");
                }
            }
            return $return;
        } else {
            throw new \Core\Model\EntityException($columnName . " Column Type reading failed");
        }
    }

    /**
     * Returns with all strucutal data of a column
     * @param string $field
     * @throws \Core\Model\EntityException
     * @return array
     */
    protected function _getColumnData($field) {
        foreach ($this->_getColumns() as $column) {
            if ($column["Field"] == $field) {
                return $column;
            }
        }
        throw new \Core\Model\EntityException("Entity has no \"" . $field . "\" named column");
    }

    /**
     * Retruns with columns used by the entity
     * @return array
     */
    protected function _getColumns() {
        return $this->_columns;
    }

}

?>
