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
 * Util to handle text internationalization, no positions suffixes, currency only plain texts
 * @package Util
 * @author pilou
 * @version 0.1
 */

namespace lisa_util;

class I18n {

	/**
	 * Instance of I18n.
	 * @access protected
	 * @staticvar array of I18n classes (by the text file)
	 */
	private static $_instance = array();
	/**
	 * Instances of Text with different languages than initialized language.
	 * @access protected
	 * @staticvar array of I18n classes
	 */
	protected static $_differentLanguageInstances = array();
	/**
	 * File full path to the text file
	 * @access protected
	 * @var string path to the text file
	 */
	protected $_file;
	/**
	 * Handles and stores data
	 * @access protected
	 * @var \Util\Config\INITag Parsed text config file
	 */
	protected $_iniHandler;
	/**
	 * Language for the instance
	 * @access protected
	 * @var string
	 */
	protected $_language;
	/**
	 * Changed flag
	 * If data has changed destruct will write the file (if not read only)
	 * @access protected
	 * @var bool is text config changed (should be written)
	 */
	protected $_changed;
	/**
	 * ReadOnly flag
	 * If read only destruct wont write the file
	 * @access public
	 * @var bool is text config file can be written
	 */
	public $readOnly = false;
	/**
	 * Stores the URL of a main text config
	 * @access protected 
	 * @var string URL
	 */
	protected $_remoteText;

	/**
	 * Initializes $_iniHandler with text dataa
	 * @param string $file path from DIR_ROOT
	 * @access private Do not allow explicit call for the constructor
	 * @final
	 * @return void
	 */
	final private function __construct($file = "/config/i18n.ini") {

		$this->_file = DIR_ROOT . $file;
		touch($this->_file);

		$this->_iniHandler = new \Util\Config\INITag(parse_ini_file($this->_file, true));
	}

	/**
	 * Do not allow the clone operation
	 */
	final private function __clone() {

	}

	/**
	 * Writes the file if readOnly set to false (default), and has been changed
	 * @access public
	 * @return void
	 */
	public function __destruct() {

		if ($this->_changed === true && $this->readOnly === false) {
			$content = "";
			foreach ($this->_iniHandler->toArray() as $key => $value) {
				$content .= "[" . $key . "]" . chr(10);
				if (count($value) > 0) {
					foreach ($value as $k => $v) {
						$content .= $k . ' = "' . $v . '"' . chr(10);
					}
				}
			}
			$handle = fopen($this->_file, "w");
			fwrite($handle, $content);
			fclose($handle);
		}
	}

	/**
	 * Getter, setter of language
	 * Getter returns with $_language string
	 * Setter returns with a clone instance with different language (parameter $language)
	 * @param string $language optional
	 * @return mixed
	 * @access public
	 * @throws Exception
	 */
	public function language($language = null) {
		if (!is_null($language) && strlen($language) == 0) {
			throw new \Exception("Language error of Text object");
		}
		if (!is_null($language)) {

			$language = strtoupper($language);
			if (is_null($this->_language) || $language != $this->_language) {
				$this->_language = $language;
				return $this;
			} else {
				if (!isset(static::$_differentLanguageInstances[$language])) {
					static::$_differentLanguageInstances[$language] = clone($this);
					static::$_differentLanguageInstances[$language]->_language = $language;
				}
				return static::$_differentLanguageInstances[$language];
			}
		}
		return $this->_language;
	}

	/**
	 * Factory method for Text class
	 * Sets the text config file, and the language
	 * @param string $language Language code case insensitive (will be capitalized)
	 * @param string $file Optional path to the text config file Default: "/config/i18n.ini"
	 * @access public
	 * @return I18n
	 */
	public static function init($language, $file = "/config/i18n.ini") {

		if (!isset(static::$_instance[$file]) || !(static::$_instance[$file] instanceof self)) {
			$class = get_called_class();
			static::$_instance[$file] = new $class($file);
		}
		static::$_instance[$file] = static::$_instance[$file]->language($language);

		return static::$_instance[$file];
	}

	/**
	 * Returns with text by the language. If it's not set, setter sets it
	 * Shortcut to getText($name)
	 * @param string $name text alias
	 * @access public
	 * @return string
	 */
	public function __get($name) {
		return $this->getText($name);
	}

	/**
	 * Sets the text data by the language
	 * @param string $name text alias
	 * @param string $value text value
	 * @access public
	 * @return string
	 */
	public function __set($name, $value) {

		$mainValue = $this->_getRemote($name);

		$value = (is_null($mainValue)) ? $value : $mainValue;

		if (is_null($this->_iniHandler->$name)) {
			$this->_iniHandler->$name = array($this->language() => $value);
		} else {
			$this->_iniHandler->$name->{$this->language()} = $value;
		}

		$this->_changed = true;
		return $value;
	}

	/**
	 * Returns with text by the language.
	 * If it's not set, setter sets it
	 * If remote has been set setter tries to get the remote text value
	 * @param string $name text alias
	 * @access public
	 * @return string
	 */
	public function getText($name) {
		$text = $this->_iniHandler->$name->{$this->_language};
		if (is_null($text)) {
			$text = $this->$name = $name;
		}
		return $text;
	}

	/**
	 * Set, or gets remote text URL, to get remote text content if its not present in local text
	 * @param string $url The url of a remote text config file
	 * @access public
	 * @return mixed I18n|string
	 */
	public function remote($url = null) {
		if (is_null($url)) {
			return $url;
		} else {
			$this->_remoteText = $url;
			return $this;
		}
	}

	/**
	 * Get remote content by $_remoteText
	 * @param string $name
	 * @access protected
	 * @return mixed string|null
	 */
	protected function _getRemote($name) {
		if (!is_null($this->_remoteText)) {
			try {
				$remoteContent = \Util\Curl::init($this->_remoteText)->getContent();
				$remoteContent = parse_ini_string($remoteContent, true);
				if ($remoteContent === false) {
					throw new \Exception();
				}

				$iniHandler = new \Util\Config\INITag($remoteContent);
				return $iniHandler->$name->{$this->_language};
			} catch (\Exception $exc) {
				return null;
			}
		}
		return null;
	}

}

?>