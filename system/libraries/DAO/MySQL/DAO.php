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
 * Data Access Object MySQL implementation
 * @package Core
 * @subpackage DAO
 * @author kovacsricsi
 */

namespace Core\DAO\MySQL;

class DAO extends \Core\DAO\ADAO {

	/**
	 * Returns with entities whitch matced to Query.
	 * @access public
	 * @param \Core\DAO\Query $query
	 * @return array
	 */
	public function getAll($query = null) {
		$entites = array();

		foreach ($this->_select($query) as $data) {
			$entites[] = $this->_getEntity($data);
		}

		return $entites;
	}

	/**
	 * Returns with first entity whitch matched to Query and $oder pattern.
	 * @access pulic
	 * @param \Core\DAO\Query $query
	 * @return Entity
	 */
	public function getEntity($query = null) {
		$data = $this->_select($query, false);
		if (!$data) {
			return null;
		}

		return $this->_getEntity($data);
	}

	/**
	 * Returns the number of results whitch matched to Query.
	 * @access public
	 * @param \Core\DAO\Query $query
	 * @return integer
	 */
	public function count($query = null) {
		$sql = "SELECT 1 FROM " . $this->_tableName;

		if ($query instanceof \Core\DAO\Query) {
			$sql .= $query->getJoin();
			$sql .= $query->getWhere();
			$sql .= $query->getGroupBy();
			$sql .= $query->getOrderBy();
			$sql .= $query->getLimit();

			return $this->_connection->numRows($sql, $query->getPrepare());
		} else {
			return $this->_connection->numRows($sql);
		}


		return $count;
	}

	/**
	 * Validate Entity to commit.
	 * @access public
	 * @param \Core\DAO\Entity $entity
	 * @throws Exception
	 * @return void
	 */
	public function validate(\Core\DAO\Entity $entity) {
		foreach ($this->_columns as $column) {
			$field = $column["Field"];
			if (($field !== $this->_primaryKey) && (strtoupper($column["Null"]) == "NO") && ($column["Default"] === null) && ($entity->$field === null)) {
				throw new \Core\DAO\Exception("Missed requested field : " . $field);
			}
		}
	}

	/**
	 * Commit Entity.
	 * @access public
	 * @param \Core\DAO\Entity $entity
	 * @throws Exception
	 * @return \Core\DAO\Entity
	 */
	public function commit(\Core\DAO\Entity $entity) {
		$this->validate($entity);

		$data = array();

		foreach ($this->_columns as $column) {
			$field = $column["Field"];
			if ($entity->$field !== null) {
				$data[$field] = $entity->$field;
			}
		}

		if ($data) {
			$pk = $this->_primaryKey;
			if ($entity->$pk) {
				return $this->_update($data);
			} else {
				return $this->_insert($data);
			}
		}
	}

	/**
	 * Insert new Entity to database.
	 * @access protected
	 * @param array $data
	 * @return \Core\DAO\Entity
	 */
	protected function _insert(array $data) {
		$sql = "INSERT INTO " . $this->_tableName . " SET ";

		$tmp = array();

		foreach ($data as $k => $v) {
			$tmp[] = " " . $k . " =:" . (string) $k;
		}

		$sql .= join(",", $tmp);

		$this->_connection->execute($sql, $data);

		$sql = "SELECT * FROM " . $this->_tableName . " WHERE " . $this->_fullPrimaryKey . " = ";
		if ($this->isAutoIncrement($this->_primaryKey)) {
			$sql .= "LAST_INSERT_ID();";
		} else {
			$sql .= ":entityPrimaryKey;";
			$nai = true;
		}

		$prepare = isset($nai) ? array("entityPrimaryKey" => $data[$this->_primaryKey]) : null;
		return $this->_getEntity($this->_connection->getOne($sql, $prepare));
	}

	/**
	 * Update Entity in database.
	 * @access protected
	 * @param array $data
	 * @return \Core\DAO\Entity
	 */
	protected function _update(array $data) {
		$sql = "UPDATE " . $this->_tableName . " SET ";

		$tmp = array();

		foreach ($data as $k => $v) {
			$tmp[] = " " . $k . " =:" . (string) $k;
		}

		$sql .= join(",", $tmp);

		$sql .= " WHERE " . $this->_fullPrimaryKey . " = :entityPrimaryKey;";

		$data["entityPrimaryKey"] = $data[$this->_primaryKey];

		$this->_connection->execute($sql, $data);

		$sql = "SELECT * FROM " . $this->_tableName . " WHERE " . $this->_fullPrimaryKey . " = :entityPrimaryKey;";
		return $this->_getEntity($this->_connection->getOne($sql, $data));
	}

	/**
	 * Delete Entity.
	 * @access public
	 * @param \Core\DAO\Entity $entity
	 * @throws Exception
	 * @return void
	 */
	public function delete(\Core\DAO\Entity $entity) {
		$primaryKey = $this->_primaryKey;
		$prepare = array("pk" => $entity->$primaryKey);
		$this->_connection->execute("DELETE FROM " . $this->_tableName . " WHERE " . $this->_fullPrimaryKey . " = :pk", $prepare);
	}

	/**
     * Delete All records.
     * @access public
     * @abstract
     * @param Query $query
     * @return void
     */
    public function deleteAll(\Core\DAO\Query $query) {
		$sql = "DELETE FROM " . $this->_tableName;
		$sql .= $query->getJoin();
		$sql .= $query->getWhere();
		$sql .= $query->getGroupBy();
		$sql .= $query->getOrderBy();
		$sql .= $query->getLimit();

		$this->_connection->execute($sql, $query->getPrepare());
	}

	/**
	 * Returns with entities data.
	 * @access protected
	 * @param \Core\DAO\Query $query
	 * @param boolean $all
	 * @return array
	 */
	protected function _select($query, $all = true) {
		if ($query instanceof \Core\DAO\Query) {
			$sql = "SELECT " . $query->getSelect() . " FROM " . $this->_tableName;
			$sql .= $query->getJoin();
			$sql .= $query->getWhere();
			$sql .= $query->getGroupBy();
			$sql .= $query->getOrderBy();
			$sql .= $query->getLimit();

			return $all === true ? $this->_connection->getAll($sql, $query->getPrepare()) : $this->_connection->getOne($sql, $query->getPrepare());
		} else {
			$sql = "SELECT * FROM " . $this->_tableName;

			return $all === true ? $this->_connection->getAll($sql, $query->getPrepare()) : $this->_connection->getOne($sql, $query->getPrepare());
		}
	}

	/**
	 * Returns with columns of table.
	 * @access protected
	 * @return array
	 */
	public function getColumns() {
		return $this->_connection->getAll("SHOW COLUMNS FROM " . $this->_tableName . ";");
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
	 * @return array
	 */
	public function getEnums($columnName) {
		$type = $this->_getType($columnName);
		if (!isset($type["enum"])) {
			throw new \Core\Model\EntityException("Column " . $columnName . " is not an enum");
		}
		return $type["enum"];
	}

	/*
	 * Returns true if column is string like type
	 * @param string $columnName
	 * retrun bool
	 */

	public function isStringType($columnName) {
		return in_array(key($this->_getType($columnName)), array("varchar", "text", "blob", "tinyblob", "mediumblob", "longblob", "char"));
	}

	/*
	 * Returns true if column is string like type
	 * @param string $columnName
	 * retrun bool
	 */

	public function isNumberType($columnName) {
		return in_array(key($this->_getType($columnName)), array("int", "float", "tinyint", "year", "smallint", "mediumint", "bigint", "double", "dec", "decimal"));
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

	/**
	 * Returns with column's type adnd its parameters
	 * @param string $columnName
	 * @throws \Core\DAO\Exception
	 * @return array
	 */
	protected function _getType($columnName) {
		$column = $this->_getColumnData($columnName);
		$matches = array();

		if (preg_match("/^([\w]*)(.*)/", $column["Type"], $matches)) {
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
			throw new \Core\DAO\Exception($columnName . " Column Type reading failed");
		}
	}

	/**
	 * Returns with all strucutal data of a column
	 * @param string $field
	 * @throws \Core\DAO\Exception
	 * @return array
	 */
	protected function _getColumnData($field) {
		foreach ($this->getColumns() as $column) {
			if ($column["Field"] == $field) {
				return $column;
			}
		}
		throw new \Core\DAO\Exception("Entity has no \"" . $field . "\" named column");
	}

	/**
	 * Returns with columns of table.
	 * @access protected
	 * @abstract
	 * @return array
	 */
	protected function _getColumns() {
		return $this->_connection->getAll("SHOW COLUMNS FROM `" . $this->_tableName . "`;");
	}

	/**
	 * Set primary key for entities.
	 * @access protected
	 * @throws Exception
	 * @return void
	 */
	protected function _setPrimaryKey($pk) {
		if ($pk !== null) {
			foreach ($this->_columns as $column) {
				if ($column["Field"] == $pk && strtoupper($column["Key"]) == "UNI") {
					$this->_primaryKey = $pk;
				}
			}
			if ($this->_primaryKey === null) {
				throw new \Core\DAO\Exception("Primary key not unique!");
			}
		} else {
			foreach ($this->_columns as $column) {
				if (strtoupper($column["Key"]) == "PRI") {
					$this->_primaryKey = $column["Field"];
				}
			}
			if ($this->_primaryKey === null) {
				throw new \Core\DAO\Exception("Primary key not found!");
			}
		}
	}

	/**
	 * Set primary key for entities.
	 * @access protected
	 * @return void
	 */
	protected function _setConnection() {
		$this->_connection = \Core\Database\MySQL\DatabaseConnection::getConnection($this->_connectionName);
	}

}

?>
