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
 * Data Access Object Query
 * @package Core
 * @subpackage DAO
 * @author kovacsricsi
 */

namespace Core\DAO;

class Query {

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
     * Factory method to create simple Query.
     * @access public
     * @static
     * @return Query
     */
    public static function getSimple() {
        $empty = array();
        return new self(array("*"), $empty, $empty, $empty, $empty, $empty, $empty);
    }

    /**
     * Constructor.
     * @access public
     * @param array $join
     * @param array $where
     * @param array $groupBy
     * @param array $orderBy
     * @param array $limit
     * @param array $prepare
     * @return void
     */
    public function __construct(array $select, array $join, array $where, array $groupBy, array $orderBy, array $limit, array $prepare) {
        $this->_select = $select;
        $this->_join = $join;
        $this->_where = $where;
        $this->_groupBy = $groupBy;
        $this->_orderBy = $orderBy;
        $this->_limit = $limit;
        $this->_prepare = $prepare;
    }

    public function getSelect() {
        return join(", ", $this->_select);
    }

    /**
     * Returns with JOIN part of query.
     * @access public
     * @return string
     */
    public function getJoin() {
        $sql = "";
        foreach ($this->_join as $join) {
            $sql .= " " . $join["type"] . " JOIN " . $join["table"] . " ON " . $join["reference"] . " = " . $join["connect"] . " ";
        }

        return $sql;
    }

    /**
     * Returns with WHERE part of query.
     * @access public
     * @return string
     */
    public function getWhere() {
        $sql = join(") AND (", $this->_where);

        return $sql != false ? " WHERE (" . $sql . ")" : "";
    }

    /**
     * Returns with GROUP BY part of query.
     * @access public
     * @return string
     */
    public function getGroupBy() {
        $sql = join(", ", $this->_groupBy);

        return $sql != false ? " GROUP BY " . $sql : "";
    }

    /**
     * Returns with ORDER BY part of query.
     * @access public
     * @return string
     */
    public function getOrderBy() {
        $sql = join(", ", $this->_orderBy);

        return $sql != false ? " ORDER BY " . $sql : "";
    }

    /**
     * Returns with LIMIT part of query.
     * @access public
     * @return string
     */
    public function getLimit() {
        $sql = join(",", $this->_limit);

        return $sql != false ? " LIMIT " . $sql : "";
    }

    /**
     * Returns with parameters to prepare.
     * @access public
     * @return array
     */
    public function getPrepare() {
        return $this->_prepare;
    }

}

?>
