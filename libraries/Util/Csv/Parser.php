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
 * CSV Parse class.
 * @package Util
 * @subpackage Csv
 * @author Somlyai Dezső
 */
namespace Util\Csv;

class Parser
{

	/**
	 * The csv parse data.
	 * @access protected
	 * @var array
	 */
	protected $_data;

	/**
	 * Csv file content.
	 * @access protected
	 * @var array
	 */
	protected $_content;

	/**
	 * CSV row separator.
	 * @access protected
	 * @var string
	 */
	protected $_separator;

	/**
	 * Array of CSV rows
	 * @access protected
	 * @var array
	 */
	protected $_CSVRows;

	/**
	 * First Row of CSV as header.
	 * @access protected
	 * @var bool
	 */
	protected $_header;

	/**
	* Character encoding.
	 * @access protected
	 * @var bool
	 */
	protected $_charSet;

	/**
	 * Constructor.
	 * @access private
	 * @param string $file
	 * @param string $separator
	 * @param bool $header
	 * @return void
	 */
	private function __construct($file, $separator, $header, $charset) {
		$this->_data = array();
		$this->_separator = $separator;
		$this->_content = \Util\FileHandler::init($file)->getContents();
		$this->_charSet = $charset;
		if ($this->_charSet != "ISO-8859-2") {
			$this->_content = mb_convert_encoding($this->_content, $this->_charSet, "ISO-8859-2");
		}
		$this->_CSVRows = explode(chr(13), $this->_content);
		$this->_header = $header;
	}

	/**
	 * Factory method of CSVParser.
	 * @access public
	 * static
	 * @param string $file
	 * @param string $separator
	 * @param bool $header
	 * @return CSVParser
	 */
	public static function init($file, $separator = ";", $header = true, $charset = "UTF-8") {
		$class = get_called_class();
		return new $class($file, $separator, $header, $charset);
	}

	/**
	 * Read CSV and converting array.
	 * @access public
	 * @return array
	 */
	public function csvReader() {
		if ( $this->_header ) {
			$header = array_shift($this->_CSVRows);
			$header = $this->_CSVRowSplit($header);
		}
		$i = 0;
		foreach($this->_CSVRows as $row) {
			$csvRowElements = $this->_CSVRowSplit($row);

			if (! \Util\Validate::isEmptyArray($csvRowElements)) {
				foreach($csvRowElements as $key=>$element){
					if ( isset($header) ) {
						$this->_data[$i][trim($header[$key])] = trim($element);
					} else {
						$this->_data[$i][] = trim($element);
					}
				}
				$i++;
			}
		}
		return $this->_data;
	}

	/**
	 * Spliting CSV row.
	 * @access protected
	 * @param string $row
	 * @return array
	 */
	protected function _CSVRowSplit($row) {
		$expr = "/". $this->_separator. "(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/";
		$results = preg_split( $expr, trim($row) );
		return preg_replace("/^\"(.*)\"$/", "$1", $results);
	}

	/**
	 * Get csv row.
	 * @param int $num
	 * @return array
	 */
	public function getRow($num) {
		$num += 1;
		if (array_key_exists($num, $this->_CSVRows)) {
			return $this->_CSVRowSplit($this->_CSVRows[$num]);
		} else {
			return false;
		}
	}

}
?>
