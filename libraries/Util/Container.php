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
 * Container class is an extended array.
 * @package Util
 * @author kovacsricsi
 */
namespace Util;

class Container
{
	/**
	 * container elements
	 * @access protected
     * @var array
	 */
	protected $_elements;

    /**
     * Max size of the Container
     * @access protected
     * @var integer
     */
	protected $_maxSize;

	/**
	 * container type
	 * @access public
     * @var string
	 */
	public $type;

	/**
	 * constructor
	 * set the default Container
	 * @access public
     * @return void
	 */
	public function __construct($type = "N/A", $maxSize = null)
	{
		$this->type      = $type;
		$this->_maxSize  = (int)$maxSize;
		$this->_elements = array();
	}

	/**
	 * Returns index of last element
	 * @access public
	 * @return int, or boolean
	 */
	public function lastIndex()
	{
		$indexes = array_keys($this->_elements);
		if ($indexes) {
			return array_pop($indexes);
		} else {
			return false;
		}
	}

	/**
	 * Returns value of last element
	 * @access public
	 * @return mixed
	 */
	public function lastElement() {
		return empty($this->_elements) ? false : $this->_elements[$this->lastIndex()];
	}

	/**
	 * return array of elements
	 * @access public
	 * @return array
	 */
	public function listElements()
	{
		return $this->_elements;
	}

	/**
	 * return an element of the container
	 * @access public
	 * @param int $index index of element
	 * @return mixed
	 */
	public function getElement($index = null)
	{
		if(!array_key_exists($index, $this->_elements)) return false;
		else return $this->_elements[$index];
	}

	/**
	 * put element to the first position
	 * @access public
	 * @param mixed $element
     * @throws Exception
	 * @return int id of inserted element
	 */
	public function addElementToFirst($element = null)
	{
		if ($this->_maxSize && (count($this->_elements) == $this->_maxSize)) {
			throw new \Exception("Konténer megtelt!");
		}

		$tmp = array_reverse($this->_elements);
		$tmp[] = $element;
		$this->_elements = array_reverse($tmp);

		return 0;
	}

	/**
	 * add an element to the end of the container
	 * @access public
	 * @param mixed $element
     * @throws Exception
	 * @return int id of inserted element
	 */
	public function addElement($element = null)
	{
		if ($this->_maxSize && (count($this->_elements) == $this->_maxSize)) {
			throw new \Exception("Konténer megtelt!");
		}

		$this->_elements[] = $element;
		return $this->lastIndex();
	}

    /**
     * Add element to specific position or key
     * @access public
     * @param mixed $index
     * @param mixed $element
     * @throws Exception
     * @return void
     */
	public function addElementByIndex($index = null, $element = null)
	{
		if ($this->_maxSize && (count($this->_elements) == $this->_maxSize)) {
			throw new \Exception("Konténer megtelt!");
		}

		if ($index === null) {
			$this->_elements[] = $element;
		} else {
			$this->_elements[$index] = $element;
		}
	}

	/**
	 * delete an element from the container
	 * @access public
	 * @param int $index
     * @return void
	 */
	public function delElement($index = null)
	{
		if(!array_key_exists($index, $this->_elements)) return false;
	}

	/**
	 * set directly an element
	 * @access public
	 * @param int $index the index of the element
	 * @param mixed $element the element
     * @return void
	 */
	public function setElement($index = null, $element = null)
	{
		if(!array_key_exists($index, $this->_elements)) return false;
		try {
			$this->_elements[$index] = $element;
		} catch (\Exception $e){
			return false;
		}
	}

	/**
	 * search a melement in the container
	 * @access public
	 * @param mixed $element
     * @return array
	 */
	public function searchElement($element = null)
	{
		$ok = array();
		$element = serialize($element);
		foreach($this->_elements as $i => $e) {
			if(serialize($e) == $element) $ok[] = $i;
		}
		return $ok;
	}

	/**
	 * return the size of the container
	 * @access public
	 * @return int
     * @return void
	 */
	public function getSize()
	{
		return count($this->_elements);
	}

    /**
     * Set max size of Container
     * @access public
     * @param int $maxSize
     * @throws Exception
     * @return void
     */
	public function setMaxSize($maxSize = null)
	{
        if ($this->getSize() > $maxSize) {
           throw new \Exception("The max size (" . $maxSize . ") is smaller than the Container's size (" . $this->getSize() . ")");
        }
		$this->_maxSize = (int)$maxSize;
	}
}
?>