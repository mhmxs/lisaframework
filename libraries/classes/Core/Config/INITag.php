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
 * Lisa INI config
 *
 * @package    Core
 * @subpackage Config
 * @author kovacsricsi
 */
class INITag
{
   /**
     * Iteration index
     *
     * @access protected
     * @var    integer
     */
    protected $_index;

    /**
     * Number of elements in configuration data
     *
     * @access protected
     * @var    integer
     */
    protected $_count;

    /**
     * Contains array of configuration data
     *
     * @access protected
     * @var    array
     */
    protected $_data;

    /**
     * Osiris\Core\Parser provides a property based interface to
     * an array. The data are read-only unless $allowModifications
     * is set to true on construction.
     *
     * Osiris\Core\Parser also implements Countable and Iterator to
     * facilitate easy access to the data.
     *
     * @access public
     * @param  array   $array
     * @return void
     */
    public function __construct(array $array)
    {
        $this->_index              = 0;
        $this->_data               = array();

        foreach ($array as $key => $value) {
        	if (is_array($value)) {
        		$this->_data[$key] = new self($value, $this->_type);
            } else {
	        	$this->_data[$key] = $value;
            }
        }

        $this->_count = count($this->_data);
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @access public
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
    		return $this->_data[$name];
    	} else {
    		return null;
    	}
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @access public
     * @param  string $name
     * @param  mixed  $value
     * @throws Osiris_Core_Config_Exception
     * @return void
     */
    public function __set($name, $value)
    {
        if (is_array($value)) {
            $this->_data[$name] = new self($value, $this->_type);
        } else {
        	$this->_data[$name] = $value;
        }

        $this->_count = count($this->_data);
    }

    /**
     * Deep clone of this instance to ensure that nested Configs
     * are also cloned.
     *
     * @access public
     * @return void
     */
    public function __clone()
    {
      $array = array();

      foreach ($this->_data as $key => $value) {
          if ($value instanceof Osiris_Libraries_Parser_Parser) {
              $array[$key] = clone $value;
          } else {
              $array[$key] = $value;
          }
      }

      $this->_data = $array;
    }

    /**
     * Return an associative array of the stored data.
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        $array = array();

        foreach ($this->_data as $key => $value) {
            if ($value instanceof Osiris_Libraries_Parser_Parser) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * Support isset() overloading on PHP 5.1
     *
     * @access public
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * Support unset() overloading on PHP 5.1
     *
     * @access public
     * @param  string $name
     * @throws Osiris_Core_Config_Exception
     * @return void
     */
    public function __unset($name)
    {
        unset($this->_data[$name]);

        $this->_count = count($this->_data);
    }

    /**
     * Defined by Countable interface
     *
     * @access public
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Defined by Iterator interface
     *
     * @access public
     * @return mixed
     */
    public function current()
    {
        return current($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     * @access public
     * @return mixed
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     * @access public
     * @return void
     */
    public function next()
    {
        next($this->_data);
        $this->_index++;
    }

    /**
     * Defined by Iterator interface
     *
     * @access public
     * @return void
     */
    public function rewind()
    {
        reset($this->_data);
        $this->_index = 0;
    }

    /**
     * Defined by Iterator interface
     *
     * @access public
     * @return boolean
     */
    public function valid()
    {
        return $this->_index < $this->_count;
    }
}

?>
