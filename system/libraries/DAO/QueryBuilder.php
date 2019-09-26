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
 * Data Access Object Query Builder
 * @package Core
 * @subpackage DAO
 * @author kovacsricsi
 */
namespace Core\DAO;

class QueryBuilder {
	/**
	 * Select
	 * @access protected
	 * @var array
	 */
	protected $_select;
	
	/**
	 * Joins
	 * @access protected
	 * @var array
	 */
	protected $_join;

	/**
	 * Where
	 * @access protected
	 * @var array
	 */
    protected $_where;

	/**
	 * Group By
	 * @access protected
	 * @var array
	 */
	protected $_groupBy;

	/**
	 * Order By
	 * @access protected
	 * @var array
	 */
	protected $_orderBy;

	/**
	 * Limit
	 * @access protected
	 * @var array
	 */
	protected $_limit;

	/**
	 * Prepare statement array
	 * @access protected
	 * @var array
	 */
	protected $_prepare;

	/**
	 * Builder factory.
	 * @access public
	 * @static
	 * @return QueryBuilder
	 */
	public static function start()
	{
		$class = get_called_class();
		return new $class();
	}

	/**
	 * Constructor sets default variables.
	 * @access protected
	 * @return void
	 */
	protected function __construct()
	{
		$this->_select = array("*");
		$this->_join = array();
		$this->_where = array();
		$this->_groupBy = array();
		$this->_orderBy = array();
		$this->_limit = array();
		$this->_prepare = array();
	}

	/**
	 * Set SELECT parameters.
	 * @access public
	 * @param mixed $where
	 * @return QueryBuilder
	 */
	public function setSelect($select)
	{
		settype($select, "array");
		$this->_select = $select;
		return $this;
	}

	/**
	 * Add SELECT parameters.
	 * @access public
	 * @param mixed $select
	 * @return QueryBuilder
	 */
	public function addSelect($select)
	{
		if (is_array($select)) {
			$this->_select = array_merge($this->_select, $select);
		} else {
			$this->_select[] = $select;
		}
		return $this;
	}

	/**
	 * Add JOIN operation to list.
	 * @access public
	 * @param string $table
	 * @param string $reference
	 * @param string $connect
	 * @param string $type
	 * @return QueryBuilder
	 */
	public function addJoin($table, $reference, $connect, $type = "INNER")
	{
		$this->_join[] = array(
			"table" => $table,
			"reference" => $reference,
			"connect" => $connect,
			"type" => $type
		);
		return $this;
	}

	/**
	 * Set WHERE parameters.
	 * @access public
	 * @param mixed $where
	 * @return QueryBuilder
	 */
	public function setWhere($where)
	{
		settype($where, "array");
		$this->_where = $where;
		return $this;
	}

	/**
	 * Add WHERE parameters.
	 * @access public
	 * @param mixed $where
	 * @return QueryBuilder
	 */
	public function addWhere($where)
	{
		if (is_array($where)) {
			$this->_where = array_merge($this->_where, $where);
		} else {
			$this->_where[] = $where;
		}
		return $this;
	}

	/**
	 * Set GROUP BY parameters.
	 * @access public
	 * @param mixed $groupBy
	 * @return QueryBuilder
	 */
	public function setGroupBy($groupBy)
	{
		settype($groupBy, "array");
		$this->_groupBy = $groupBy;
		return $this;
	}

	/**
	 * Add GROUP BY parameters.
	 * @access public
	 * @param mixed $groupBy
	 * @return QueryBuilder
	 */
	public function addGroupBy($groupBy)
	{
		if (is_array($groupBy)) {
			$this->_groupBy = array_merge($this->_groupBy, $groupBy);
		} else {
			$this->_groupBy[] = $groupBy;
		}
		return $this;
	}

	/**
	 * Set ORDER BY parameters.
	 * @access public
	 * @param mixed $orderBy
	 * @return QueryBuilder
	 */
	public function setOrderBy($orderBy)
	{
		settype($orderBy, "array");
		$this->_orderBy = $orderBy;
		return $this;
	}

	/**
	 * Add ORDER BY parameters.
	 * @access public
	 * @param mixed $orderBy
	 * @return QueryBuilder
	 */
	public function addOrderBy($orderBy)
	{
		if (is_array($orderBy)) {
			$this->_orderBy = array_merge($this->_orderBy, $orderBy);
		} else {
			$this->_orderBy[] = $orderBy;
		}
		return $this;
	}

	/**
	 * Set LIMIT parameters.
	 * @access public
	 * @param int $start
	 * @param mixed $limit
	 * @return QueryBuilder
	 */
	public function setLimit($start, $limit = null)
	{
		$this->_limit = array($start);
		if ($limit !== null) {
			$this->_limit[] = $limit;
		}
		return $this;
	}

	/**
	 * Add Add parameters to prepare.
	 * @access public
	 * @param array $prepare
	 * @return QueryBuilder
	 */
	public function setPrepare(array $prepare)
	{
		$this->_prepare = $prepare;
		return $this;
	}

	/**
	 * Add Add parameters to prepare.
	 * @access public
	 * @param string $key
	 * @param mixed $data
	 * @return QueryBuilder
	 */
	public function addPrepare($key, $data)
	{
		$this->_prepare[$key] = $data;
		return $this;
	}

	/**
	 * Build Query object
	 * @access public
	 * @return Query
	 */
	public function build()
	{
		return new Query($this->_select, $this->_join, $this->_where, $this->_groupBy, $this->_orderBy, $this->_limit, $this->_prepare);
	}
}
?>
