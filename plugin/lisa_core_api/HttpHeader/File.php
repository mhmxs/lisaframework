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
 * File header
 * @package Core
 * @subpackage HTTP.Header
 * @author kovacsricsi
 */
namespace lisa_core_api\HttpHeader;

class File implements IHeader
{
	/**
	 * Array of headers lines.
	 * @access protected
	 * @var array
	 */
	protected $_headers;

	/**
	 * Constructor sets default variables.
	 * @access public
	 * @param \Util\FileHandler $file
	 * @return void
	 */
	public function __construct(\Util\FileHandler $file)
	{
		$this->_headers = array(
			"Pragma: public",
			"Expires: 0",
			"Cache-Control: must-revalidate, post-check=0, pre-check=0",
			"Cache-Control: private",
			"Content-Type: " . $file->getContentType(),
			"Content-Disposition: attachment; filename=\"" . $file->getBaseName() . "\";",
			"Content-Transfer-Encoding: binary",
			"Content-Length: " . $file->getSize()
		);
	}

	/**
	 * Returns with array of headers.
	 * @access public
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}
}