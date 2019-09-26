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
 * @author nullstring
 */
class CSVOutput
{

	protected $_headers;
	protected $_data;

	public function __construct(array $headers, array $data)
	{
		$this->setHeaders($headers);
		$this->setData($data);
	}

	protected function setHeaders(array $headers)
	{
		$this->_headers = $headers;
	}

	protected function setData(array $data)
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
		$this->data = $validData;
	}

	public function send($filename)
	{
		$output = '"' . implode('","', $this->_headers) . '"' . "\r\n";
		foreach ($this->data as $row) {
			$line = "";
			foreach ($row as $field) {
				if ($field) {
					$line .= '"' . str_replace('"', '""', $field) . '",';
				} else {
					$line .= '"",';
				}
			}
			$line    = substr($line, 0, strlen($line) - 1);
			$output .= "$line\r\n";
		}
		$output = mb_convert_encoding($output, "LATIN2", "UTF-8");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Length: " . strlen($output));
		echo $output;
		die;
	}

}

?>