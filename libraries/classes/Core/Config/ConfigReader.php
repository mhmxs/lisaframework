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
 * INI parser create array data from ini config structure
 *
 * @package    Osiris
 * @subpackage Libraries
 * @version    SVN: $Id$
 */
class ConfigReader extends INITag
{
	/**
	 * Parse INI file
	 *
	 * @access public
	 * @param  string  $filename File to process
	 * @throws ConfigNotFoundException When filename is not set
	 * @return void
	 */
	public function __construct($filename)
	{
		if (!file_exists($filename)) {
			throw new ConfigNotFoundException($filename);
		}

		parent::__construct(parse_ini_file($filename, true));
	}
}

?>
