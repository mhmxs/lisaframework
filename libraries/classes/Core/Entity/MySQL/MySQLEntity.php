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
 * @subpackage Entity
 * @author kovacsricsi
 */
class MySQLEntity extends AEntity
{
	/**
	 * Load entity from database.
	 * @access protected
	 * @param int $entityPrimaryKey
	 * @return void
	 */
	protected function _load($entityPrimaryKey)
	{
		$sql = "SELECT * FROM `" . $this->_tableName . "` WHERE `" . $this->_primaryKey . "` = :entityPrimaryKey";

		$params = array(
			"entityPrimaryKey" => $entityPrimaryKey
		);

	    $result = QueryHandler::getOne($sql, $params);

	    if ($result == true) {
			$this->_entity = $result;
	    } else {
			throw new EntityException("Entity not found : " .$this->_tableName . " : " . $entityPrimaryKey);
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
			throw new EntityException("Entity is read only!");
		}

		$query = "DELETE FROM " . $this->_tableName . " WHERE `" . $this->_primaryKey . "` = :entityPrimaryKey";

		$params = array(
			"entityPrimaryKey" => $this->_entity[$this->_primaryKey]
		);

		QueryHandler::execute($query, $params);

		$this->_entity  = array();
		$this->_changed = false;
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
			throw new EntityException("Entity is read only!");
		}

	    if ($this->_changed) {
			$this->validate();

			$data = array();

			foreach($this->_columns as $column) {
				if (isset($this->_entity[$column["Field"]])) {
					$data[$column["Field"]] = $this->_entity[$column["Field"]];
				}
			}

			if (isset($this->_entity[$this->_primaryKey])) {
				$this->_update($data);
			} else {
				unset($data[$this->_primaryKey]);
				$this->_entity[$this->_primaryKey] = $this->_insert($data);
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
				throw new EntityException("Missed requested field : " . $column["Field"]);
			}
		}
	 }

	 /**
	  * Insert new Entity to database.
	  * @access protected
	  * @param array $data
	  * @return int
	  */
	protected function _insert(array $data)
	{
		$query = "INSERT INTO `" . $this->_tableName . "` SET ";

		$tmp = array();

		foreach($data as $k => $v) {
			$tmp[] = " `" . $k . "` =:" . (string)$k;
		}

		$query .= join(",", $tmp);

		QueryHandler::execute($query, $data);

		$entityPrimaryKey = QueryHandler::getOne("SELECT  LAST_INSERT_ID() as entityPrimaryKey");
		return $entityPrimaryKey["entityPrimaryKey"];
	}

	 /**
	  * Update Entity in database.
	  * @access protected
	  * @param array $data
	  * @return void
	  */
	protected function _update(array $data)
	{
		$query = "UPDATE `" . $this->_tableName . "` SET ";

		$tmp = array();

		foreach($data as $k => $v) {
			$tmp[] = " `" . $k . "` =:" . (string)$k;
		}

		$query .= join(",", $tmp);

		$query .= " WHERE " . $this->_primaryKey . " = :entityPrimaryKey";

		$data["entityPrimaryKey"] = $this->_entity[$this->_primaryKey];

		QueryHandler::execute($query, $data);
	}
}

?>
