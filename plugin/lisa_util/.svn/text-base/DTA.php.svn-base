<?php
namespace lisa_util;
class DTA {

	protected $_data;

	protected $_tmp = array();

	protected function __construct($data) {
		$this->_data = $data;
	}

	public function init($data = array()) {
		return new self($data);
	}

	public function walk($method, $arg = null) {
		if (is_null($arg)) {
			array_walk($this->_data, array($this, $method));
		} else {
			array_walk($this->_data, array($this, $method), $arg);
		}
		return $this->_tmp;
	}

	public function getValue($array, $key, $item) {
		$this->_tmp[] = $array->$item;
	}

}

?>
