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
 * Generate csv output.
 * @package Util
 * @subpackage Csv
 * @author nullstring
 */
namespace lisa_util\Csv;

class Output
{
	/**
	 * Character encoding.
	 *
	 * @access protected
	 * @var    string
	 */
	protected $_charSet;

	/**
	 * Headers of the file, it appear in first line.
	 *
	 * @access protected
	 * @var    array
	 */
	protected $_headers;

	/**
	 * CSV data.
	 *
	 * @access protected
	 * @var    array
	 */
	protected $_data;

	/**
	 * Contructor sets default variables.
	 *
	 * @access public
	 * @param  array $headers
	 * @param  array $data
	 * @return void
	 */
	public function __construct(array $headers, array $data, $charset = "UTF-8")
	{
		$this->_charSet = $charset;
		$this->_headers = $headers;

		$this->_setData($data);
	}

	/**
	 * Sets header
	 */

	/**
	 * Set data from array by headers.
	 *
	 * @access protected
	 * @param  array $data
	 * @return void
	 */
	protected function _setData(array $data)
	{
		$validData = array();

		foreach ($data as $row) {
			$validRow = array();
			foreach ($this->_headers as  $hkey => $header) {
				 if (array_key_exists($hkey, $row)) {
				 	$validRow[$hkey] = $row[$hkey];
				 } else {
				 	$validRow[$hkey] = "";
				 }
			}
			$validData[] = $validRow;
		}
		$this->_data = $validData;
	}

	/**
	 * Generate content.
	 * @access public
	 * @return string
	 */
	public function getContent()
	{
		$content = '"' . implode('","', $this->_headers) . '"' . "\r\n";

		foreach ($this->_data as $row) {
			$line = "";

			foreach ($row as $field) {
				if ($field) {
					$line .= '"' . str_replace('"', '""', $field) . '",';
				} else {
					$line .= '"",';
				}
			}
			$line    = substr($line, 0, strlen($line) - 1);
			$content .= $line . "\r\n";
		}

		if ($this->_charSet != "LATIN2") {
			$content = mb_convert_encoding($content, "LATIN2", $this->_charSet);
		}

		return $content;
	}

	/**
	 * Send output in file.
	 *
	 * @access public
	 * @param  string $filename
	 * @return void
	 */
	public function send($filename)
	{
		$output = $this->getContent();

		header("Content-Type: application/csv");
		header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
		header("Content-Length: " . strlen($output));

		echo $output;
		die;
	}
}

?>