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
 * Curl implementation.
 * @package Util
 * @author dezsi
 */

namespace Util;

class CURL {

	/**
	 * URL
	 * @access protected
	 * @var string
	 */
	protected $_url;
	/**
	 * Timeout
	 * @access protected
	 * @var string
	 */
	protected $_timeOut;
	/**
	 * Curl init
	 * @access protected
	 * @var string
	 */
	protected $_ch;

	/**
	 * Factory method.
	 * @access public
	 * @static
	 * @param string $url
	 * @param integer $timeOut
	 * @return Curl
	 */
	public static function init($url, $timeOut = 30) {
		return new self($url, $timeOut);
	}

	/**
	 * Constructor.
	 * @access public
	 * @param string $url
	 * @param integer $timeOut
	 * throws \Exception
	 * @return void
	 */
	public function __construct($url, $timeOut = 30) {
		if (empty($url)) {
			throw new \Exception("URL not found.");
		}
		$this->_url = $url;
		$this->_timeOut = (int) $timeOut;
		$this->_ch = curl_init();
	}

	/**
	 * Returns with content of connection.
	 * @access public
	 * @param boolean $close
	 * @return string
	 */
	public function getContent($close = true) {
		if (!is_resource($this->_ch)) {
			throw new \Exception("Resource not exists!");
		}
		curl_setopt($this->_ch, CURLOPT_URL, $this->_url);
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, $this->_timeOut);
		curl_setopt($this->_ch, CURLOPT_FAILONERROR, 1);

		$file_contents = curl_exec($this->_ch);

		if (!curl_errno($this->_ch)) {
			return $file_contents;
		} else {
			throw new \Exception(curl_error($this->_ch));
		}
	}

	/**
	 * Returns with information of connection.
	 * @access public
	 * @return string
	 */
	public function getInfo() {
		if (!is_resource($this->_ch)) {
			throw new \Exception("Resource not exists!");
		}
		return curl_getinfo($this->_ch);
	}

	/**
	 * Close connection.
	 * @access public
	 * @return void
	 */
	public function close() {
		if (is_resource($this->_ch)) {
			curl_close($this->_ch);
		}
	}

	/**
	 * Destructor.
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		if (is_resource($this->_ch)) {
			curl_close($this->_ch);
		}
	}

}

?>
